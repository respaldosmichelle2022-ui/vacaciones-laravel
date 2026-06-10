<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\SaldoVacacion;

class EmpleadoController extends Controller
{

    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $sitio = auth()->user()->sitio;

        $query = Empleado::query();

        if ($sitio) {
            $query->where('sitio', $sitio);
        }

        if ($buscar) {
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%$buscar%")
                  ->orWhere('apellido_paterno', 'like', "%$buscar%")
                  ->orWhere('apellido_materno', 'like', "%$buscar%")
                  ->orWhere('numero_empleado', 'like', "%$buscar%");
            });
        }

        $empleados = $query->orderByRaw('CAST(numero_empleado AS DECIMAL)')->get();

        return view('empleados.index', compact('empleados'));
    }

    public function crear()
    {

        return view('empleados.crear');

    }

    public function guardar(Request $request)
    {

        Empleado::create([

            'numero_empleado' => $request->numero_empleado,
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'fecha_ingreso' => $request->fecha_ingreso,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sitio' => $request->sitio,
            'sucursal' => $request->sucursal,
            'puesto' => $request->puesto,

        ]);

        return redirect('/empleados');

    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|file'
        ]);

        $file = $request->file('archivo_excel');
        $filePath = $file->getRealPath();

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return back()->with('error', 'No se pudo abrir el archivo Excel.');
        }

        // Leer strings compartidos
        $sharedStrings = [];
        $stringsData = $zip->getFromName('xl/sharedStrings.xml');
        if ($stringsData) {
            $xml = simplexml_load_string($stringsData);
            if ($xml) {
                foreach ($xml->si as $si) {
                    $rStr = '';
                    if (isset($si->t)) {
                        $rStr = (string)$si->t;
                    } elseif (isset($si->r)) {
                        foreach ($si->r as $r) {
                            $rStr .= (string)$r->t;
                        }
                    }
                    $sharedStrings[] = $rStr;
                }
            }
        }

        // Leer hoja 1
        $sheetData = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetData) {
            $zip->close();
            return back()->with('error', 'No se encontró la hoja de datos en el archivo Excel.');
        }

        $xml = simplexml_load_string($sheetData);
        $zip->close();

        if (!$xml) {
            return back()->with('error', 'Error al procesar la estructura de la hoja de cálculo.');
        }

        $empleadosImportados = 0;
        $empleadosOmitidos = 0;
        $advertencias = [];

        // Dynamic header mapping
        $colMapping = [
            'numero_empleado' => 'A',
            'nombre' => 'B',
            'apellido_paterno' => 'C',
            'apellido_materno' => 'D',
            'fecha_ingreso' => 'E',
            'fecha_nacimiento' => null,
            'sitio' => 'F',
            'sucursal' => 'G',
            'puesto' => 'H',
        ];

        foreach ($xml->sheetData->row as $row) {
            $rowIndex = (int)$row['r'];
            if ($rowIndex === 1) {
                // Fila 1 es la cabecera
                foreach ($row->c as $cell) {
                    $colName = preg_replace('/[0-9]/', '', (string)$cell['r']);
                    $type = (string)$cell['t'];
                    $val = (string)$cell->v;
                    if ($type === 's' && isset($sharedStrings[(int)$val])) {
                        $val = $sharedStrings[(int)$val];
                    }
                    $valClean = mb_strtolower(trim($val));
                    
                    if (str_contains($valClean, 'nacimiento') || str_contains($valClean, 'nac')) {
                        $colMapping['fecha_nacimiento'] = $colName;
                    } elseif (str_contains($valClean, 'sitio')) {
                        $colMapping['sitio'] = $colName;
                    } elseif (str_contains($valClean, 'sucursal')) {
                        $colMapping['sucursal'] = $colName;
                    } elseif (str_contains($valClean, 'puesto')) {
                        $colMapping['puesto'] = $colName;
                    } elseif (str_contains($valClean, 'alta') || str_contains($valClean, 'ingreso')) {
                        $colMapping['fecha_ingreso'] = $colName;
                    } elseif (str_contains($valClean, 'num') || str_contains($valClean, 'numb') || str_contains($valClean, 'id')) {
                        $colMapping['numero_empleado'] = $colName;
                    } elseif (str_contains($valClean, 'nombre')) {
                        $colMapping['nombre'] = $colName;
                    } elseif (str_contains($valClean, 'paterno')) {
                        $colMapping['apellido_paterno'] = $colName;
                    } elseif (str_contains($valClean, 'materno')) {
                        $colMapping['apellido_materno'] = $colName;
                    }
                }
                continue;
            }

            $rowData = [];
            foreach ($row->c as $cell) {
                $colName = preg_replace('/[0-9]/', '', (string)$cell['r']);
                $type = (string)$cell['t'];
                $val = (string)$cell->v;

                if ($type === 's' && isset($sharedStrings[(int)$val])) {
                    $val = $sharedStrings[(int)$val];
                }
                $rowData[$colName] = trim($val);
            }

            $numeroEmpleado = isset($rowData[$colMapping['numero_empleado']]) ? $rowData[$colMapping['numero_empleado']] : null;
            $nombre = isset($rowData[$colMapping['nombre']]) ? $rowData[$colMapping['nombre']] : null;
            $apellidoPaterno = isset($rowData[$colMapping['apellido_paterno']]) ? $rowData[$colMapping['apellido_paterno']] : null;
            $apellidoMaterno = isset($rowData[$colMapping['apellido_materno']]) ? $rowData[$colMapping['apellido_materno']] : '';
            $fechaAltaVal = isset($rowData[$colMapping['fecha_ingreso']]) ? $rowData[$colMapping['fecha_ingreso']] : null;
            $sitio = isset($rowData[$colMapping['sitio']]) ? $rowData[$colMapping['sitio']] : '';
            $sucursal = isset($rowData[$colMapping['sucursal']]) ? $rowData[$colMapping['sucursal']] : '';
            $puesto = isset($rowData[$colMapping['puesto']]) ? $rowData[$colMapping['puesto']] : '';

            // Si todos los campos principales están vacíos, es una fila vacía y se ignora
            if (empty($numeroEmpleado) && empty($nombre) && empty($apellidoPaterno) && empty($fechaAltaVal)) {
                continue;
            }

            // Validar campos obligatorios
            $missingFields = [];
            if (empty($numeroEmpleado)) $missingFields[] = 'Número de Empleado';
            if (empty($nombre)) $missingFields[] = 'Nombre';
            if (empty($apellidoPaterno)) $missingFields[] = 'Apellido Paterno';
            if (empty($fechaAltaVal)) $missingFields[] = 'Fecha de Ingreso';

            if (count($missingFields) > 0) {
                $nomCompleto = trim("$nombre $apellidoPaterno $apellidoMaterno") ?: 'Colaborador sin nombre';
                $advertencias[] = "Fila {$rowIndex} [{$nomCompleto}]: Omitido por falta de campos obligatorios (" . implode(', ', $missingFields) . ").";
                $empleadosOmitidos++;
                continue;
            }

            // Control de duplicidad: Verificar coincidencia por número, nombre y apellido paterno
            $existe = Empleado::where('numero_empleado', $numeroEmpleado)
                ->where('nombre', $nombre)
                ->where('apellido_paterno', $apellidoPaterno)
                ->exists();

            if ($existe) {
                $advertencias[] = "Fila {$rowIndex} [{$nombre} {$apellidoPaterno}]: Omitido porque ya existe en el sistema un colaborador con número {$numeroEmpleado} y el mismo nombre.";
                $empleadosOmitidos++;
                continue;
            }

            // Convertir fecha de ingreso
            $fechaIngreso = null;
            if (is_numeric($fechaAltaVal)) {
                $unixTimestamp = ($fechaAltaVal - 25569) * 86400;
                $fechaIngreso = date('Y-m-d', $unixTimestamp);
            } else {
                $clean = str_replace('/', '-', $fechaAltaVal);
                $time = strtotime($clean);
                if ($time) {
                    $fechaIngreso = date('Y-m-d', $time);
                }
            }

            if (!$fechaIngreso) {
                $advertencias[] = "Fila {$rowIndex} [{$nombre} {$apellidoPaterno}]: Omitido por formato de fecha inválido ('{$fechaAltaVal}').";
                $empleadosOmitidos++;
                continue;
            }

            // Convertir fecha de nacimiento
            $fechaNacimiento = null;
            if ($colMapping['fecha_nacimiento'] && isset($rowData[$colMapping['fecha_nacimiento']])) {
                $fechaNacVal = $rowData[$colMapping['fecha_nacimiento']];
                if (!empty($fechaNacVal)) {
                    if (is_numeric($fechaNacVal)) {
                        $unixTimestamp = ($fechaNacVal - 25569) * 86400;
                        $fechaNacimiento = date('Y-m-d', $unixTimestamp);
                    } else {
                        $clean = str_replace('/', '-', $fechaNacVal);
                        $time = strtotime($clean);
                        if ($time) {
                            $fechaNacimiento = date('Y-m-d', $time);
                        }
                    }
                }
            }

            // Crear el registro de Empleado
            $empleado = Empleado::create([
                'numero_empleado' => $numeroEmpleado,
                'nombre' => $nombre,
                'apellido_paterno' => $apellidoPaterno,
                'apellido_materno' => $apellidoMaterno,
                'fecha_ingreso' => $fechaIngreso,
                'fecha_nacimiento' => $fechaNacimiento,
                'sitio' => $sitio,
                'sucursal' => $sucursal,
                'puesto' => $puesto,
                'activo' => 1
            ]);

            // Sincronizar saldos de vacaciones inmediatamente para este empleado
            SaldoVacacion::sincronizarSaldos($empleado->id);

            $empleadosImportados++;
        }

        if (count($advertencias) > 0) {
            session()->flash('import_warnings', $advertencias);
        }

        return redirect('/empleados')->with('success', "Importación completada. Empleados nuevos importados: $empleadosImportados. Omitidos/Existentes: $empleadosOmitidos.");
    }

    public function editar($id)
    {

        $empleado = Empleado::findOrFail($id);

        return view('empleados.editar',
        compact('empleado'));

    }

    public function actualizar(Request $request, $id)
{
    $empleado = Empleado::findOrFail($id);

    $empleado->update([

        'numero_empleado' => $request->numero_empleado,
        'nombre' => $request->nombre,
        'apellido_paterno' => $request->apellido_paterno,
        'apellido_materno' => $request->apellido_materno,
        'sitio' => $request->sitio,
        'sucursal' => $request->sucursal,
        'puesto' => $request->puesto,
        'fecha_ingreso' => $request->fecha_ingreso,
        'fecha_nacimiento' => $request->fecha_nacimiento,

    ]);

    return redirect('/empleados');
}

    public function cambiarEstado($id)
    {

        $empleado = Empleado::findOrFail($id);

        $empleado->activo = !$empleado->activo;

        $empleado->save();

        return redirect('/empleados');

    }

    public function eliminar($id)
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->delete();

        return redirect('/empleados')->with('success', 'Empleado eliminado correctamente.');
    }

    public function eliminarMasivo(Request $request)
    {
        $ids = $request->input('ids', []);
        if (count($ids) > 0) {
            Empleado::whereIn('id', $ids)->get()->each(function($empleado) {
                $empleado->delete();
            });
            return redirect('/empleados')->with('success', 'Empleados seleccionados eliminados correctamente.');
        }

        return redirect('/empleados')->with('error', 'No se seleccionó ningún empleado.');
    }

    public function eliminarPorSitio(Request $request)
    {
        $sitio = $request->input('sitio');
        if ($sitio) {
            Empleado::where('sitio', $sitio)->get()->each(function($empleado) {
                $empleado->delete();
            });
            return redirect('/empleados')->with('success', "Todos los empleados del sitio '$sitio' fueron eliminados correctamente.");
        }

        return redirect('/empleados')->with('error', 'No se especificó ningún sitio.');
    }

    public function exportarExcel()
    {
        $sitio = auth()->user()->sitio;
        $query = Empleado::query();
        if ($sitio) {
            $query->where('sitio', $sitio);
        }
        $empleados = $query->orderByRaw('CAST(numero_empleado AS DECIMAL)')->get();

        $filename = "reporte_empleados_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Numero Empleado', 'Nombre', 'Apellido Paterno', 'Apellido Materno', 'Fecha Nacimiento', 'Sitio', 'Sucursal', 'Puesto', 'Fecha Ingreso', 'Estado'];

        $callback = function() use($empleados, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($empleados as $e) {
                fputcsv($file, [
                    $e->numero_empleado,
                    $e->nombre,
                    $e->apellido_paterno,
                    $e->apellido_materno,
                    $e->fecha_nacimiento,
                    $e->sitio,
                    $e->sucursal,
                    $e->puesto,
                    $e->fecha_ingreso,
                    $e->activo ? 'Activo' : 'Inactivo'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function detalleModal($id)
    {
        \App\Models\SaldoVacacion::sincronizarSaldos($id);

        $empleado = Empleado::with(['saldosVacaciones', 'movimientosVacaciones'])->findOrFail($id);
        
        $sitio = auth()->user()->sitio;
        if ($sitio && $empleado->sitio !== $sitio) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        return response()->json([
            'nombre_completo' => "{$empleado->nombre} {$empleado->apellido_paterno} {$empleado->apellido_materno}",
            'fecha_ingreso' => \Carbon\Carbon::parse($empleado->fecha_ingreso)->format('d/m/Y'),
            'fecha_nacimiento' => $empleado->fecha_nacimiento ? \Carbon\Carbon::parse($empleado->fecha_nacimiento)->format('d/m/Y') : 'No registrada',
            'anos_cumplidos' => \Carbon\Carbon::parse($empleado->fecha_ingreso)->age,
            'saldos' => $empleado->saldosVacaciones->map(function($saldo) use ($empleado) {
                $tomadas = $empleado->movimientosVacaciones->where('periodo', $saldo->periodo)->sum('dias');
                return [
                    'periodo' => $saldo->periodo,
                    'correspondientes' => $saldo->dias_corresponden,
                    'tomadas' => $tomadas,
                    'pendientes' => $saldo->dias_restantes
                ];
            })
        ]);
    }
}