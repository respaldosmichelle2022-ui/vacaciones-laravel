<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;
use App\Models\Empleado;

class IncidenciaController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $tipo = $request->tipo;
        $sitioFiltro = $request->sitio;
        $mesFiltro = $request->mes;
        $userSitio = auth()->user()->sitio;

        // Obtener la lista de sitios únicos para el filtro
        $sitiosQuery = Empleado::select('sitio')->distinct();
        if ($userSitio) {
            $sitiosQuery->where('sitio', $userSitio);
        }
        $sitios = $sitiosQuery->pluck('sitio')->filter()->values();

        $query = Incidencia::with('empleado');

        if ($userSitio) {
            $query->whereHas('empleado', function($q) use ($userSitio) {
                $q->where('sitio', $userSitio);
            });
        } elseif ($sitioFiltro) {
            $query->whereHas('empleado', function($q) use ($sitioFiltro) {
                $q->where('sitio', $sitioFiltro);
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

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        if ($mesFiltro) {
            $query->whereMonth('fecha', $mesFiltro);
        }

        $incidencias = $query->orderBy('fecha', 'desc')->get();

        return view('incidencias.index', compact('incidencias', 'buscar', 'tipo', 'sitios', 'sitioFiltro', 'mesFiltro'));
    }

    public function crear()
    {
        $sitio = auth()->user()->sitio;
        $query = Empleado::orderByRaw('CAST(numero_empleado AS DECIMAL)');
        if ($sitio) {
            $query->where('sitio', $sitio);
        }
        $empleados = $query->get();
        return view('incidencias.crear', compact('empleados'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'tipo' => 'required|in:falta,permiso,retardo,permiso_horas,incapacidad',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        Incidencia::create($request->all());

        return redirect('/incidencias')->with('success', 'Incidencia registrada correctamente.');
    }

    public function editar($id)
    {
        $incidencia = Incidencia::findOrFail($id);
        $sitio = auth()->user()->sitio;
        $query = Empleado::orderByRaw('CAST(numero_empleado AS DECIMAL)');
        if ($sitio) {
            $query->where('sitio', $sitio);
        }
        $empleados = $query->get();
        return view('incidencias.editar', compact('incidencia', 'empleados'));
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'tipo' => 'required|in:falta,permiso,retardo,permiso_horas,incapacidad',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $incidencia = Incidencia::findOrFail($id);
        $incidencia->update($request->all());

        return redirect('/incidencias')->with('success', 'Incidencia actualizada correctamente.');
    }

    public function eliminar($id)
    {
        $incidencia = Incidencia::findOrFail($id);
        $incidencia->delete();

        return redirect('/incidencias')->with('success', 'Incidencia eliminada correctamente.');
    }

    public function reporte(Request $request)
    {
        $userSitio = auth()->user()->sitio;

        // 1. Obtener opciones de filtros para los dropdowns
        $sitiosQuery = Empleado::select('sitio')->distinct();
        if ($userSitio) {
            $sitiosQuery->where('sitio', $userSitio);
        }
        $sitios = $sitiosQuery->pluck('sitio')->filter()->values();

        $empleadosDropdownQuery = Empleado::query();
        if ($userSitio) {
            $empleadosDropdownQuery->where('sitio', $userSitio);
        }
        if ($request->filled('sitio')) {
            $empleadosDropdownQuery->where('sitio', $request->input('sitio'));
        }
        $empleadosDropdown = $empleadosDropdownQuery->orderBy('nombre')->get();

        // 2. Determinar periodo seleccionado (Meses y Año)
        $selectedYear = $request->input('anio', date('Y'));
        $selectedMonths = $request->input('meses');
        if (is_null($selectedMonths)) {
            // Por defecto el mes actual
            $selectedMonths = [intval(date('n'))];
        } else {
            if (!is_array($selectedMonths)) {
                $selectedMonths = [$selectedMonths];
            }
            $selectedMonths = array_map('intval', $selectedMonths);
        }

        // Calcular total de días del periodo seleccionado
        $diasTotales = 0;
        foreach ($selectedMonths as $m) {
            $diasTotales += \Carbon\Carbon::createFromDate($selectedYear, $m, 1)->daysInMonth;
        }

        // 3. Filtrar empleados y sus incidencias
        $query = Empleado::query();
        if ($userSitio) {
            $query->where('sitio', $userSitio);
        } elseif ($request->filled('sitio')) {
            $query->where('sitio', $request->input('sitio'));
        }

        if ($request->filled('empleado_id')) {
            $query->where('id', $request->input('empleado_id'));
        }

        $empleados = $query->with(['incidencias' => function($q) use ($selectedYear, $selectedMonths) {
            $q->whereYear('fecha', $selectedYear)
              ->where(function($sub) use ($selectedMonths) {
                  foreach ($selectedMonths as $index => $month) {
                      if ($index === 0) {
                          $sub->whereMonth('fecha', $month);
                      } else {
                          $sub->orWhereMonth('fecha', $month);
                      }
                  }
              });
        }])->get();

        // 4. Mapear estadísticas por colaborador
        $reporte = $empleados->map(function($empleado) use ($diasTotales) {
            $faltas = $empleado->incidencias->where('tipo', 'falta')->count();
            $retardos = $empleado->incidencias->where('tipo', 'retardo')->count();
            $permisos = $empleado->incidencias->where('tipo', 'permiso')->count();
            $permisosHoras = $empleado->incidencias->where('tipo', 'permiso_horas')->count();
            $incapacidades = $empleado->incidencias->where('tipo', 'incapacidad')->count();

            $totalPermisos = $permisos + $permisosHoras;
            $totalGlobal = $faltas + $retardos + $totalPermisos + $incapacidades;

            // Score de desempeño mejorado
            $desempeno = 100 - ($faltas * 10) - ($retardos * 2) - ($totalPermisos * 1) - ($incapacidades * 0.5);
            $desempeno = max(0, $desempeno);

            // Tasas/Promedios
            $promedioFaltas = $diasTotales > 0 ? ($faltas / $diasTotales) * 100 : 0;
            $promedioRetardos = $diasTotales > 0 ? ($retardos / $diasTotales) * 100 : 0;
            $promedioPermisos = $diasTotales > 0 ? ($totalPermisos / $diasTotales) * 100 : 0;
            $promedioIncapacidad = $diasTotales > 0 ? ($incapacidades / $diasTotales) * 100 : 0;
            $promedioGlobal = $diasTotales > 0 ? ($totalGlobal / $diasTotales) * 100 : 0;

            return [
                'empleado' => $empleado,
                'faltas' => $faltas,
                'retardos' => $retardos,
                'permisos' => $totalPermisos,
                'incapacidades' => $incapacidades,
                'total_global' => $totalGlobal,
                'desempeno' => $desempeno,
                'promedio_faltas' => round($promedioFaltas, 2),
                'promedio_retardos' => round($promedioRetardos, 2),
                'promedio_permisos' => round($promedioPermisos, 2),
                'promedio_incapacidad' => round($promedioIncapacidad, 2),
                'promedio_global' => round($promedioGlobal, 2)
            ];
        })->sortByDesc('desempeno');

        return view('incidencias.reporte', compact(
            'reporte',
            'sitios',
            'empleadosDropdown',
            'selectedYear',
            'selectedMonths',
            'diasTotales'
        ));
    }

    public function exportarExcel()
    {
        $sitio = auth()->user()->sitio;
        $query = Incidencia::with('empleado');
        if ($sitio) {
            $query->whereHas('empleado', function($q) use ($sitio) {
                $q->where('sitio', $sitio);
            });
        }
        $incidencias = $query->orderBy('fecha', 'desc')->get();

        $filename = "reporte_incidencias_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Numero Empleado', 'Colaborador', 'Sitio', 'Tipo', 'Fecha', 'Observaciones'];

        $callback = function() use($incidencias, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($incidencias as $inc) {
                $tipoLabel = $inc->tipo;
                if ($inc->tipo == 'falta') $tipoLabel = 'Falta';
                elseif ($inc->tipo == 'retardo') $tipoLabel = 'Retardo';
                elseif ($inc->tipo == 'permiso') $tipoLabel = 'Permiso';
                elseif ($inc->tipo == 'permiso_horas') $tipoLabel = 'Permiso por horas';
                elseif ($inc->tipo == 'incapacidad') $tipoLabel = 'Incapacidad';

                fputcsv($file, [
                    $inc->empleado->numero_empleado,
                    $inc->empleado->nombre . ' ' . $inc->empleado->apellido_paterno,
                    $inc->empleado->sitio,
                    $tipoLabel,
                    $inc->fecha,
                    $inc->observaciones
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
