<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Empleado;
use App\Models\SaldoVacacion;
use App\Models\MovimientoVacacion;
use App\Models\DiaFestivo;

use Carbon\Carbon;

class MovimientoVacacionController extends Controller
{
public function index(Request $request)
{
    $buscar = $request->buscar;
    $sitio = auth()->user()->sitio;

    $query = MovimientoVacacion::with('empleado');

    if ($sitio) {
        $query->whereHas('empleado', function($q) use ($sitio) {
            $q->where('sitio', $sitio);
        });
    }

    if ($buscar) {
        $query->whereHas('empleado', function($q) use ($buscar) {
            $q->where(function($sub) use ($buscar) {
                $sub->where('nombre', 'like', "%$buscar%")
                    ->orWhere('apellido_paterno', 'like', "%$buscar%")
                    ->orWhere('apellido_materno', 'like', "%$buscar%")
                    ->orWhere('numero_empleado', 'like', "%$buscar%");
            });
        });
    }

    $movimientos = $query->orderBy('fecha_inicio', 'desc')->get();

    return view('movimientos.index', compact('movimientos'));
}

public function crear()
{
    $sitio = auth()->user()->sitio;
    $query = Empleado::orderByRaw('CAST(numero_empleado AS UNSIGNED)');
    if ($sitio) {
        $query->where('sitio', $sitio);
    }
    $empleados = $query->get();

    $saldos = SaldoVacacion::select('periodo')
        ->distinct()
        ->orderBy('periodo')
        ->get();

    return view(
        'movimientos.crear',
        compact('empleados', 'saldos')
    );
}

    public function guardar(Request $request)
    {
        $inicio = Carbon::parse($request->fecha_inicio);
        $fin = Carbon::parse($request->fecha_fin);

        if($fin < $inicio)
        {
            return back()->with(
                'error',
                'La fecha fin no puede ser menor que la fecha inicio'
            );
        }

        if($inicio->year != $fin->year)
        {
            return back()->with(
                'error',
                'Las vacaciones deben pertenecer al mismo año'
            );
        }

        // 1. Validar orden de periodos (agotar periodos anteriores primero)
        $previousPending = SaldoVacacion::where('empleado_id', $request->empleado_id)
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
            return back()->with('error', 'La asignación de vacaciones no contiene días laborables (se excluyeron fines de semana y días festivos).');
        }

        $duplicado = MovimientoVacacion::where('empleado_id', $request->empleado_id)
            ->where('periodo', $request->periodo)
            ->where('fecha_inicio', $request->fecha_inicio)
            ->where('fecha_fin', $request->fecha_fin)
            ->exists();

        if($duplicado)
        {
            return back()->with(
                'error',
                'Ya existe un movimiento con esas mismas fechas'
            );
        }

        $traslape = MovimientoVacacion::where(
            'empleado_id',
            $request->empleado_id
        )
        ->where(function($q) use ($request)
        {
            $q->whereBetween(
                    'fecha_inicio',
                    [$request->fecha_inicio, $request->fecha_fin]
                )
              ->orWhereBetween(
                    'fecha_fin',
                    [$request->fecha_inicio, $request->fecha_fin]
                )
              ->orWhere(function($sub) use ($request)
                {
                    $sub->where(
                            'fecha_inicio',
                            '<=',
                            $request->fecha_inicio
                        )
                        ->where(
                            'fecha_fin',
                            '>=',
                            $request->fecha_fin
                        );
                });
        })
        ->exists();

        if($traslape)
        {
            return back()->with(
                'error',
                'Las fechas se traslapan con otras vacaciones'
            );
        }

        $saldo = SaldoVacacion::where('empleado_id', $request->empleado_id)
            ->where('periodo', $request->periodo)
            ->first();

        if (!$saldo) {
            return back()->with('error', 'No existe saldo para este periodo');
        }

        if ($saldo->dias_restantes < $dias) {
            return back()->with('error', "No tiene suficientes días. Días requeridos: $dias, días disponibles: {$saldo->dias_restantes}.");
        }

        $salario_diario = $request->salario_diario ?? \App\Models\Setting::getVal('salario_minimo', 315.04);
        $prima_vacacional = $salario_diario * 0.25;

        $movimiento = MovimientoVacacion::create([
            'empleado_id' => $request->empleado_id,
            'periodo' => $request->periodo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'dias' => $dias,
            'salario_diario' => $salario_diario,
            'prima_vacacional' => $prima_vacacional
        ]);

        $saldo->dias_restantes = $saldo->dias_restantes - $dias;
        $saldo->save();

        return redirect('/movimientos')->with([
            'success' => 'Movimiento registrado correctamente.',
            'nuevo_id' => $movimiento->id
        ]);
    }

    public function editar($id)
    {
        $movimiento = MovimientoVacacion::with('empleado')->findOrFail($id);
        
        $periodosDisponibles = SaldoVacacion::where('empleado_id', $movimiento->empleado_id)
            ->select('periodo')
            ->distinct()
            ->get();

        $saldos = SaldoVacacion::where('empleado_id', $movimiento->empleado_id)->get();
        $saldosConDiasRevertidos = [];
        foreach ($saldos as $saldo) {
            $diasRestantes = $saldo->dias_restantes;
            if ($saldo->periodo == $movimiento->periodo) {
                $diasRestantes += $movimiento->dias;
            }
            $saldosConDiasRevertidos[$saldo->periodo] = $diasRestantes;
        }

        return view('movimientos.editar', compact('movimiento', 'periodosDisponibles', 'saldosConDiasRevertidos'));
    }

    public function actualizar(Request $request, $id)
    {
        $inicio = Carbon::parse($request->fecha_inicio);
        $fin = Carbon::parse($request->fecha_fin);

        if($fin < $inicio)
        {
            return back()->with(
                'error',
                'La fecha fin no puede ser menor que la fecha inicio'
            );
        }

        if($inicio->year != $fin->year)
        {
            return back()->with(
                'error',
                'Las vacaciones deben pertenecer al mismo año'
            );
        }

        $movimiento = MovimientoVacacion::findOrFail($id);

        // 1. Validar orden de periodos (agotar periodos anteriores primero)
        $previousPending = SaldoVacacion::where('empleado_id', $request->empleado_id)
            ->where('periodo', '<', $request->periodo)
            ->get();
        
        foreach ($previousPending as $prev) {
            $prevDays = $prev->dias_restantes;
            if ($movimiento->empleado_id == $request->empleado_id && $movimiento->periodo == $prev->periodo) {
                $prevDays += $movimiento->dias;
            }
            if ($prevDays > 0) {
                return back()->with('error', "No se puede asignar vacaciones para el periodo {$request->periodo} porque aún existen {$prevDays} días pendientes en el periodo anterior {$prev->periodo}. Debe agotar los periodos vencidos primero.");
            }
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
            return back()->with('error', 'La asignación de vacaciones no contiene días laborables (se excluyeron fines de semana y días festivos).');
        }

        $duplicado = MovimientoVacacion::where('id', '!=', $id)
            ->where('empleado_id', $request->empleado_id)
            ->where('periodo', $request->periodo)
            ->where('fecha_inicio', $request->fecha_inicio)
            ->where('fecha_fin', $request->fecha_fin)
            ->exists();

        if($duplicado)
        {
            return back()->with(
                'error',
                'Ya existe otro movimiento con esas mismas fechas'
            );
        }

        $traslape = MovimientoVacacion::where('id', '!=', $id)
            ->where('empleado_id', $request->empleado_id)
            ->where(function($q) use ($request)
            {
                $q->whereBetween(
                        'fecha_inicio',
                        [$request->fecha_inicio, $request->fecha_fin]
                    )
                  ->orWhereBetween(
                        'fecha_fin',
                        [$request->fecha_inicio, $request->fecha_fin]
                    )
                  ->orWhere(function($sub) use ($request)
                    {
                        $sub->where(
                                'fecha_inicio',
                                '<=',
                                $request->fecha_inicio
                            )
                            ->where(
                                'fecha_fin',
                                '>=',
                                $request->fecha_fin
                            );
                    });
            })
            ->exists();

        if($traslape)
        {
            return back()->with(
                'error',
                'Las fechas se traslapan con otras vacaciones'
            );
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function() use ($id, $request, $dias) {
                $movimiento = MovimientoVacacion::findOrFail($id);

                // Revertir saldo anterior
                $saldoOriginal = SaldoVacacion::where('empleado_id', $movimiento->empleado_id)
                    ->where('periodo', $movimiento->periodo)
                    ->first();

                if($saldoOriginal)
                {
                    $saldoOriginal->dias_restantes += $movimiento->dias;
                    $saldoOriginal->save();
                }

                // Cargar y validar nuevo saldo
                $saldoNuevo = SaldoVacacion::where('empleado_id', $request->empleado_id)
                    ->where('periodo', $request->periodo)
                    ->first();

                if (!$saldoNuevo) {
                    throw new \Exception('No existe saldo para el periodo seleccionado');
                }

                if ($saldoNuevo->dias_restantes < $dias) {
                    throw new \Exception("No tiene suficientes días. Días requeridos: $dias, días disponibles: {$saldoNuevo->dias_restantes}.");
                }

                // Aplicar descuento al nuevo saldo
                $saldoNuevo->dias_restantes -= $dias;
                $saldoNuevo->save();

                $salario_diario = $movimiento->salario_diario ?? $request->salario_diario ?? \App\Models\Setting::getVal('salario_minimo', 315.04);
                $prima_vacacional = $salario_diario * 0.25;

                // Actualizar movimiento
                $movimiento->update([
                    'periodo' => $request->periodo,
                    'fecha_inicio' => $request->fecha_inicio,
                    'fecha_fin' => $request->fecha_fin,
                    'dias' => $dias,
                    'salario_diario' => $salario_diario,
                    'prima_vacacional' => $prima_vacacional
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect('/movimientos')->with('success', 'Movimiento actualizado correctamente');
    }

    public function eliminar($id)
    {
        $movimiento = MovimientoVacacion::findOrFail($id);

        $saldo = SaldoVacacion::where('empleado_id', $movimiento->empleado_id)
            ->where('periodo', $movimiento->periodo)
            ->first();

        if($saldo)
        {
            $saldo->dias_restantes =
                $saldo->dias_restantes + $movimiento->dias;

            $saldo->save();
        }

        $movimiento->delete();

        return redirect('/movimientos')
            ->with('success','Movimiento eliminado correctamente');
    }

    public function exportarExcel()
    {
        $sitio = auth()->user()->sitio;
        $query = MovimientoVacacion::with('empleado');
        if ($sitio) {
            $query->whereHas('empleado', function($q) use ($sitio) {
                $q->where('sitio', $sitio);
            });
        }
        $movimientos = $query->orderBy('fecha_inicio', 'desc')->get();

        $filename = "reporte_movimientos_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Numero Empleado', 'Colaborador', 'Sitio', 'Periodo', 'Fecha Inicio', 'Fecha Fin', 'Dias'];

        $callback = function() use($movimientos, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($movimientos as $m) {
                fputcsv($file, [
                    $m->empleado->numero_empleado,
                    $m->empleado->nombre . ' ' . $m->empleado->apellido_paterno,
                    $m->empleado->sitio,
                    $m->periodo,
                    $m->fecha_inicio,
                    $m->fecha_fin,
                    $m->dias
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function imprimir($id)
    {
        $movimiento = MovimientoVacacion::with('empleado')->findOrFail($id);
        $empleado = $movimiento->empleado;

        $ingreso = \Carbon\Carbon::parse($empleado->fecha_ingreso);
        $fechaInicio = \Carbon\Carbon::parse($movimiento->fecha_inicio);
        $fechaFin = \Carbon\Carbon::parse($movimiento->fecha_fin);
        $anos = (int)$ingreso->diffInYears($fechaInicio);
        $antiguedad = $anos . ' ' . ($anos == 1 ? 'AÑO' : 'AÑOS');

        $saldo = SaldoVacacion::where('empleado_id', $empleado->id)
            ->where('periodo', $movimiento->periodo)
            ->first();
        $diasCorresponden = $saldo ? $saldo->dias_corresponden : 0;

        $diasDisfrutados = MovimientoVacacion::where('empleado_id', $empleado->id)
            ->where('periodo', $movimiento->periodo)
            ->where('id', '<', $movimiento->id)
            ->sum('dias');

        $diasPendientes = $diasCorresponden - $diasDisfrutados - $movimiento->dias;
        if ($diasPendientes < 0) {
            $diasPendientes = 0;
        }

        $salario_diario = $movimiento->salario_diario ?? \App\Models\Setting::getVal('salario_minimo', 315.04);
        $prima_vacacional = $movimiento->prima_vacacional ?? ($salario_diario * 0.25);

        $logoPath = \App\Models\Setting::getVal('logo_path', '/logo-placeholder.png');

        // Formatear fechas
        $inicioDia = $fechaInicio->format('d');
        // Mes en español
        $meses = [
            1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO',
            7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
        ];
        $inicioMes = $meses[$fechaInicio->month];
        $inicioAnio = $fechaInicio->format('Y');

        $finDia = $fechaFin->format('d');
        $finMes = $meses[$fechaFin->month];
        $finAnio = $fechaFin->format('Y');

        return view('movimientos.imprimir', compact(
            'movimiento', 'empleado', 'antiguedad', 'diasCorresponden',
            'diasDisfrutados', 'diasPendientes', 'salario_diario', 'prima_vacacional',
            'logoPath', 'inicioDia', 'inicioMes', 'inicioAnio', 'finDia', 'finMes', 'finAnio'
        ));
    }
}