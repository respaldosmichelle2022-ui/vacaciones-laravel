<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\SaldoVacacion;
use App\Models\MovimientoVacacion;

class ReporteVacacionController extends Controller
{
    public function general(Request $request)
    {
        SaldoVacacion::sincronizarTodos();

        $userSitio = auth()->user()->sitio;

        // 1. Obtener filtros para dropdowns
        $sitiosQuery = Empleado::select('sitio')->distinct();
        if ($userSitio) {
            $sitiosQuery->where('sitio', $userSitio);
        }
        $sitios = $sitiosQuery->pluck('sitio')->filter()->values();

        $periodos = SaldoVacacion::select('periodo')->distinct()->orderBy('periodo', 'desc')->pluck('periodo');
        $selectedPeriod = $request->input('periodo', date('Y'));

        $empleadosDropdownQuery = Empleado::query();
        if ($userSitio) {
            $empleadosDropdownQuery->where('sitio', $userSitio);
        }
        if ($request->filled('sitio')) {
            $empleadosDropdownQuery->where('sitio', $request->input('sitio'));
        }
        $empleadosDropdown = $empleadosDropdownQuery->orderBy('nombre')->get();

        // 2. Cargar saldos de acuerdo a filtros
        $query = SaldoVacacion::with('empleado');

        if ($userSitio) {
            $query->whereHas('empleado', function($q) use ($userSitio) {
                $q->where('sitio', $userSitio);
            });
        } elseif ($request->filled('sitio')) {
            $query->whereHas('empleado', function($q) use ($request) {
                $q->where('sitio', $request->input('sitio'));
            });
        }

        if ($request->filled('empleado_id')) {
            $query->where('empleado_id', $request->input('empleado_id'));
        }

        $query->where('periodo', $selectedPeriod);

        $saldos = $query->get();

        // 3. Mapear datos con días tomados
        $reporte = $saldos->map(function($saldo) {
            $diasTomados = MovimientoVacacion::where('empleado_id', $saldo->empleado_id)
                ->where('periodo', $saldo->periodo)
                ->sum('dias');

            return [
                'numero_empleado' => $saldo->empleado->numero_empleado,
                'nombre' => $saldo->empleado->nombre . ' ' . $saldo->empleado->apellido_paterno . ' ' . $saldo->empleado->apellido_materno,
                'puesto' => $saldo->empleado->puesto ?? 'Sin puesto',
                'sitio' => $saldo->empleado->sitio ?: 'N/A',
                'periodo' => $saldo->periodo,
                'dias_corresponden' => $saldo->dias_corresponden,
                'dias_tomados' => $diasTomados,
                'dias_pendientes' => $saldo->dias_restantes
            ];
        });

        return view('reportes.vacaciones_general', compact(
            'reporte', 'sitios', 'periodos', 'empleadosDropdown', 'selectedPeriod'
        ));
    }

    public function generalExportar(Request $request)
    {
        $userSitio = auth()->user()->sitio;
        $selectedPeriod = $request->input('periodo', date('Y'));

        $query = SaldoVacacion::with('empleado');

        if ($userSitio) {
            $query->whereHas('empleado', function($q) use ($userSitio) {
                $q->where('sitio', $userSitio);
            });
        } elseif ($request->filled('sitio')) {
            $query->whereHas('empleado', function($q) use ($request) {
                $q->where('sitio', $request->input('sitio'));
            });
        }

        if ($request->filled('empleado_id')) {
            $query->where('empleado_id', $request->input('empleado_id'));
        }

        $query->where('periodo', $selectedPeriod);

        $saldos = $query->get();

        $filename = "reporte_general_vacaciones_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Número Empleado', 'Colaborador', 'Puesto', 'Sitio', 'Periodo', 'Días Correspondientes', 'Días Tomados', 'Días Pendientes'];

        $callback = function() use($saldos, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($file, $columns);

            foreach ($saldos as $saldo) {
                $diasTomados = MovimientoVacacion::where('empleado_id', $saldo->empleado_id)
                    ->where('periodo', $saldo->periodo)
                    ->sum('dias');

                fputcsv($file, [
                    $saldo->empleado->numero_empleado,
                    $saldo->empleado->nombre . ' ' . $saldo->empleado->apellido_paterno . ' ' . $saldo->empleado->apellido_materno,
                    $saldo->empleado->puesto ?? 'Sin puesto',
                    $saldo->empleado->sitio ?: 'N/A',
                    $saldo->periodo,
                    $saldo->dias_corresponden,
                    $diasTomados,
                    $saldo->dias_restantes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function detalle(Request $request)
    {
        SaldoVacacion::sincronizarTodos();

        $userSitio = auth()->user()->sitio;

        // 1. Obtener filtros para dropdowns
        $sitiosQuery = Empleado::select('sitio')->distinct();
        if ($userSitio) {
            $sitiosQuery->where('sitio', $userSitio);
        }
        $sitios = $sitiosQuery->pluck('sitio')->filter()->values();

        // Obtener todos los periodos en orden cronológico (menor a mayor / antiguo a actual)
        $periodos = SaldoVacacion::select('periodo')->distinct()->orderBy('periodo', 'asc')->pluck('periodo')->toArray();

        // Procesar selección de periodos (checkboxes o todos)
        $requestPeriodos = $request->input('periodos');
        if (is_null($requestPeriodos)) {
            $requestPeriodos = ['todos'];
        }

        if (in_array('todos', $requestPeriodos)) {
            $selectedPeriods = $periodos;
        } else {
            if (!is_array($requestPeriodos)) {
                $requestPeriodos = [$requestPeriodos];
            }
            $selectedPeriods = array_map('intval', $requestPeriodos);
        }

        $empleadosDropdownQuery = Empleado::query();
        if ($userSitio) {
            $empleadosDropdownQuery->where('sitio', $userSitio);
        }
        if ($request->filled('sitio')) {
            $empleadosDropdownQuery->where('sitio', $request->input('sitio'));
        }
        $empleadosDropdown = $empleadosDropdownQuery->orderBy('nombre')->get();

        // 2. Obtener colaboradores filtrados
        $empleadosQuery = Empleado::query();
        if ($userSitio) {
            $empleadosQuery->where('sitio', $userSitio);
        } elseif ($request->filled('sitio')) {
            $empleadosQuery->where('sitio', $request->input('sitio'));
        }

        if ($request->filled('empleado_id')) {
            $empleadosQuery->where('id', $request->input('empleado_id'));
        }

        $empleados = $empleadosQuery->get();

        // 3. Mapear desglose paso a paso de movimientos agrupados por colaborador y cronológico por periodo
        $reporte = $empleados->map(function($empleado) use ($selectedPeriods) {
            $saldos = SaldoVacacion::where('empleado_id', $empleado->id)
                ->whereIn('periodo', $selectedPeriods)
                ->orderBy('periodo', 'asc')
                ->get();

            if ($saldos->isEmpty()) {
                return null;
            }

            $saldosDesglose = $saldos->map(function($saldo) {
                $movimientos = MovimientoVacacion::where('empleado_id', $saldo->empleado_id)
                    ->where('periodo', $saldo->periodo)
                    ->orderBy('fecha_inicio', 'asc')
                    ->get();

                $pasos = [];
                $saldoAcumulado = $saldo->dias_corresponden;

                // Primer paso: Carga inicial
                $pasos[] = [
                    'tipo' => 'inicial',
                    'detalle' => 'Asignación de días del periodo',
                    'fecha' => null,
                    'cambio' => '+' . $saldo->dias_corresponden,
                    'acumulado' => $saldoAcumulado
                ];

                foreach ($movimientos as $mov) {
                    $saldoAcumulado -= $mov->dias;
                    $pasos[] = [
                        'tipo' => 'movimiento',
                        'detalle' => 'Vacaciones tomadas',
                        'fecha' => \Carbon\Carbon::parse($mov->fecha_inicio)->format('d/m/Y') . ' al ' . \Carbon\Carbon::parse($mov->fecha_fin)->format('d/m/Y'),
                        'cambio' => '-' . $mov->dias,
                        'acumulado' => $saldoAcumulado
                    ];
                }

                return [
                    'periodo' => $saldo->periodo,
                    'dias_corresponden' => $saldo->dias_corresponden,
                    'pasos' => $pasos,
                    'saldo_final' => $saldoAcumulado
                ];
            });

            return [
                'id' => $empleado->id,
                'numero_empleado' => $empleado->numero_empleado,
                'nombre' => $empleado->nombre . ' ' . $empleado->apellido_paterno . ' ' . $empleado->apellido_materno,
                'puesto' => $empleado->puesto ?? 'Sin puesto',
                'sitio' => $empleado->sitio ?: 'N/A',
                'saldos' => $saldosDesglose
            ];
        })->filter()->values();

        return view('reportes.vacaciones_detalle', compact(
            'reporte', 'sitios', 'periodos', 'empleadosDropdown', 'requestPeriodos', 'selectedPeriods'
        ));
    }

    public function detalleExportar(Request $request)
    {
        $userSitio = auth()->user()->sitio;

        // Obtener todos los periodos ordenados cronológicamente
        $periodos = SaldoVacacion::select('periodo')->distinct()->orderBy('periodo', 'asc')->pluck('periodo')->toArray();

        // Procesar selección de periodos (checkboxes o todos)
        $requestPeriodos = $request->input('periodos');
        if (is_null($requestPeriodos)) {
            $requestPeriodos = ['todos'];
        }

        if (in_array('todos', $requestPeriodos)) {
            $selectedPeriods = $periodos;
        } else {
            if (!is_array($requestPeriodos)) {
                $requestPeriodos = [$requestPeriodos];
            }
            $selectedPeriods = array_map('intval', $requestPeriodos);
        }

        $empleadosQuery = Empleado::query();
        if ($userSitio) {
            $empleadosQuery->where('sitio', $userSitio);
        } elseif ($request->filled('sitio')) {
            $empleadosQuery->where('sitio', $request->input('sitio'));
        }

        if ($request->filled('empleado_id')) {
            $empleadosQuery->where('id', $request->input('empleado_id'));
        }

        $empleados = $empleadosQuery->get();

        $filename = "reporte_desglose_vacaciones_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Número Empleado', 'Colaborador', 'Puesto', 'Sitio', 'Periodo', 'Operación', 'Fechas / Detalle', 'Días', 'Saldo Acumulado'];

        $callback = function() use($empleados, $selectedPeriods, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($file, $columns);

            foreach ($empleados as $empleado) {
                $saldos = SaldoVacacion::where('empleado_id', $empleado->id)
                    ->whereIn('periodo', $selectedPeriods)
                    ->orderBy('periodo', 'asc')
                    ->get();

                foreach ($saldos as $saldo) {
                    $movimientos = MovimientoVacacion::where('empleado_id', $saldo->empleado_id)
                        ->where('periodo', $saldo->periodo)
                        ->orderBy('fecha_inicio', 'asc')
                        ->get();

                    $saldoAcumulado = $saldo->dias_corresponden;

                    // Asignación inicial
                    fputcsv($file, [
                        $saldo->empleado->numero_empleado,
                        $saldo->empleado->nombre . ' ' . $saldo->empleado->apellido_paterno . ' ' . $saldo->empleado->apellido_materno,
                        $saldo->empleado->puesto ?? 'Sin puesto',
                        $saldo->empleado->sitio ?: 'N/A',
                        $saldo->periodo,
                        'Asignación Inicial',
                        'Días del Periodo',
                        '+' . $saldo->dias_corresponden,
                        $saldoAcumulado
                    ]);

                    foreach ($movimientos as $mov) {
                        $saldoAcumulado -= $mov->dias;
                        fputcsv($file, [
                            $saldo->empleado->numero_empleado,
                            $saldo->empleado->nombre . ' ' . $saldo->empleado->apellido_paterno . ' ' . $saldo->empleado->apellido_materno,
                            $saldo->empleado->puesto ?? 'Sin puesto',
                            $saldo->empleado->sitio ?: 'N/A',
                            $saldo->periodo,
                            'Deducción',
                            \Carbon\Carbon::parse($mov->fecha_inicio)->format('d/m/Y') . ' al ' . \Carbon\Carbon::parse($mov->fecha_fin)->format('d/m/Y'),
                            '-' . $mov->dias,
                            $saldoAcumulado
                        ]);
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
