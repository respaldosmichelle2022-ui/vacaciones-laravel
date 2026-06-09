<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Empleado;
use App\Models\SaldoVacacion;
use App\Models\MovimientoVacacion;
use App\Models\Incidencia;
use App\Models\DiaFestivo;
use Carbon\Carbon;

class PersonalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->esAdmin()) {
            return view('personal.index', [
                'esAdmin' => true
            ]);
        }

        $sitio = $user->sitio;

        if (!$sitio) {
            return view('personal.index', [
                'sitio' => null,
                'error' => 'Tu usuario no está asociado a ningún sitio de trabajo. Solicita asistencia al administrador.'
            ]);
        }

        // Cargar todos los empleados del sitio
        $empleados = Empleado::where('sitio', $sitio)
            ->orderByRaw('CAST(numero_empleado AS UNSIGNED)')
            ->get();

        // Cargar datos del empleado seleccionado (opcional)
        $selectedEmpleadoId = $request->input('empleado_id');
        $empleadoSeleccionado = null;
        $saldos = collect();
        $movimientos = collect();
        $incidencias = collect();

        if ($selectedEmpleadoId) {
            $empleadoSeleccionado = Empleado::where('sitio', $sitio)->find($selectedEmpleadoId);
            if ($empleadoSeleccionado) {
                // Sincronizar saldos de vacaciones
                SaldoVacacion::sincronizarSaldos($empleadoSeleccionado->id);

                $saldos = SaldoVacacion::where('empleado_id', $empleadoSeleccionado->id)->get();
                $movimientos = MovimientoVacacion::where('empleado_id', $empleadoSeleccionado->id)
                    ->orderBy('fecha_inicio', 'desc')->get();
                $incidencias = Incidencia::where('empleado_id', $empleadoSeleccionado->id)
                    ->orderBy('fecha', 'desc')->get();
            }
        }

        return view('personal.index', compact('sitio', 'empleados', 'empleadoSeleccionado', 'saldos', 'movimientos', 'incidencias'));
    }

    public function solicitarVacaciones(Request $request)
    {
        $user = Auth::user();
        $sitio = $user->sitio;

        if (!$sitio) {
            return back()->with('error', 'No tienes un sitio asociado.');
        }

        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'periodo' => 'required|integer',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
        ]);

        // Validar que el empleado pertenezca al sitio del usuario
        $empleado = Empleado::where('sitio', $sitio)->find($request->empleado_id);
        if (!$empleado) {
            return back()->with('error', 'El colaborador seleccionado no pertenece a tu sitio.');
        }

        $inicio = Carbon::parse($request->fecha_inicio);
        $fin = Carbon::parse($request->fecha_fin);

        if ($fin < $inicio) {
            return back()->with('error', 'La fecha fin no puede ser menor que la fecha inicio.');
        }

        if ($inicio->year != $fin->year) {
            return back()->with('error', 'Las vacaciones deben pertenecer al mismo año.');
        }

        // 1. Validar orden de periodos (agotar periodos anteriores primero)
        $previousPending = SaldoVacacion::where('empleado_id', $empleado->id)
            ->where('periodo', '<', $request->periodo)
            ->where('dias_restantes', '>', 0)
            ->orderBy('periodo', 'asc')
            ->first();
        
        if ($previousPending) {
            return back()->with('error', "No se puede asignar vacaciones para el periodo {$request->periodo} porque aún existen {$previousPending->dias_restantes} días pendientes en el periodo anterior {$previousPending->periodo}. Debe agotar los periodos vencidos primero.");
        }

        // 2. Calcular días laborables (excluyendo fines de semana y festivos)
        $dias = 0;
        $tempDate = $inicio->copy();
        while ($tempDate->lte($fin)) {
            $isWeekend = $tempDate->isWeekend();
            $isHoliday = DiaFestivo::whereDate('fecha', $tempDate)->exists();
            if (!$isWeekend && !$isHoliday) {
                $dias++;
            }
            $tempDate->addDay();
        }

        if ($dias === 0) {
            return back()->with('error', 'La solicitud de vacaciones no contiene días laborables (se excluyeron fines de semana y días festivos).');
        }

        $duplicado = MovimientoVacacion::where('empleado_id', $empleado->id)
            ->where('periodo', $request->periodo)
            ->where('fecha_inicio', $request->fecha_inicio)
            ->where('fecha_fin', $request->fecha_fin)
            ->exists();

        if ($duplicado) {
            return back()->with('error', 'Ya existe un movimiento con esas mismas fechas.');
        }

        $traslape = MovimientoVacacion::where('empleado_id', $empleado->id)
            ->where(function($q) use ($request) {
                $q->whereBetween('fecha_inicio', [$request->fecha_inicio, $request->fecha_fin])
                  ->orWhereBetween('fecha_fin', [$request->fecha_inicio, $request->fecha_fin])
                  ->orWhere(function($sub) use ($request) {
                      $sub->where('fecha_inicio', '<=', $request->fecha_inicio)
                          ->where('fecha_fin', '>=', $request->fecha_fin);
                  });
            })
            ->exists();

        if ($traslape) {
            return back()->with('error', 'Las fechas se traslapan con otras vacaciones.');
        }

        $saldo = SaldoVacacion::where('empleado_id', $empleado->id)
            ->where('periodo', $request->periodo)
            ->first();

        if (!$saldo) {
            return back()->with('error', 'No existe saldo para el periodo seleccionado.');
        }

        if ($saldo->dias_restantes < $dias) {
            return back()->with('error', "No tiene suficientes días. Días requeridos: $dias, días disponibles: {$saldo->dias_restantes}.");
        }

        // Registrar movimiento y descontar saldo
        \Illuminate\Support\Facades\DB::transaction(function() use ($empleado, $request, $dias, $saldo) {
            MovimientoVacacion::create([
                'empleado_id' => $empleado->id,
                'periodo' => $request->periodo,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'dias' => $dias
            ]);

            $saldo->dias_restantes -= $dias;
            $saldo->save();
        });

        return redirect('/mi-sitio?empleado_id=' . $empleado->id)->with('success', 'Solicitud de vacaciones procesada y registrada correctamente.');
    }
}
