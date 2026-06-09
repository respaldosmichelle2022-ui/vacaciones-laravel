<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SaldoVacacion;
use App\Models\Empleado;

class SaldoVacacionController extends Controller
{
    public function index()
    {
        SaldoVacacion::sincronizarTodos();
        
        $sitio = auth()->user()->sitio;
        $query = SaldoVacacion::with('empleado');
        if ($sitio) {
            $query->whereHas('empleado', function($q) use ($sitio) {
                $q->where('sitio', $sitio);
            });
        }
        $saldos = $query->get();

        return view('saldos.index', compact('saldos'));
    }

    public function crear()
    {
        $sitio = auth()->user()->sitio;
        $query = Empleado::orderByRaw('CAST(numero_empleado AS UNSIGNED)');
        if ($sitio) {
            $query->where('sitio', $sitio);
        }
        $empleados = $query->get();

        return view('saldos.crear', compact('empleados'));
    }

    public function guardar(Request $request)
    {

        $existe = SaldoVacacion::where('empleado_id', $request->empleado_id)
    ->where('periodo', $request->periodo)
    ->exists();

if($existe)
{
    return back()
        ->with('error',
        'Ya existe un saldo para este empleado en ese periodo');
}

        SaldoVacacion::create([

            'empleado_id' => $request->empleado_id,

            'periodo' => $request->periodo,

            'dias_corresponden' => $request->dias_corresponden,

            'dias_restantes' => $request->dias_corresponden

        ]);

        return redirect('/vacaciones')
            ->with('success', 'Saldo guardado correctamente');
    }

    public function editar($id)
    {
        $saldo = SaldoVacacion::findOrFail($id);

        $sitio = auth()->user()->sitio;
        $query = Empleado::orderByRaw('CAST(numero_empleado AS UNSIGNED)');
        if ($sitio) {
            $query->where('sitio', $sitio);
        }
        $empleados = $query->get();

        return view('saldos.editar', compact('saldo', 'empleados'));
    }

public function actualizar(Request $request, $id)
{
    $saldo = SaldoVacacion::findOrFail($id);

    $saldo->update([

        'empleado_id' => $request->empleado_id,

        'periodo' => $request->periodo,

        'dias_corresponden' => $request->dias_corresponden,

        'dias_restantes' => $request->dias_restantes

    ]);

    return redirect('/vacaciones')
        ->with('success', 'Saldo actualizado correctamente');
}

public function eliminar($id)
{
    $saldo = SaldoVacacion::findOrFail($id);

    $saldo->delete();

    return redirect('/vacaciones')
        ->with('success', 'Saldo eliminado correctamente');
}

    public function exportarExcel()
    {
        $sitio = auth()->user()->sitio;
        $query = SaldoVacacion::with('empleado');
        if ($sitio) {
            $query->whereHas('empleado', function($q) use ($sitio) {
                $q->where('sitio', $sitio);
            });
        }
        $saldos = $query->get();

        $filename = "reporte_saldos_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Numero Empleado', 'Colaborador', 'Sitio', 'Periodo', 'Dias Corresponden', 'Dias Restantes', 'Estado'];

        $callback = function() use($saldos, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($saldos as $s) {
                fputcsv($file, [
                    $s->empleado->numero_empleado,
                    $s->empleado->nombre . ' ' . $s->empleado->apellido_paterno . ' ' . $s->empleado->apellido_materno,
                    $s->empleado->sitio ?: 'N/A',
                    $s->periodo,
                    $s->dias_corresponden,
                    $s->dias_restantes,
                    $s->empleado->activo ? 'Activo' : 'Inactivo'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}