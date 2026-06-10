<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\DiaFestivo;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $action = $request->route()->getActionMethod();
            
            $adminOnlyMethods = [
                'guardarLogo',
                'guardarPosicion',
                'guardarTitulo',
                'guardarImagenLogin',
                'eliminarImagenLogin',
                'diaFestivosIndex',
                'diaFestivosStore',
                'diaFestivosDelete'
            ];
            
            if (in_array($action, $adminOnlyMethods) && !$user->esAdmin()) {
                abort(403, 'Acceso denegado. Solo administradores pueden realizar esta acción.');
            }
            
            return $next($request);
        });
    }

    public function index()
    {
        $logoPath = Setting::getVal('logo_path', '/logo-placeholder.png');
        $logoPosition = Setting::getVal('logo_position', 'sidebar_top');
        $logoX = Setting::getVal('logo_x', '20px');
        $logoY = Setting::getVal('logo_y', '20px');
        $systemTitle = Setting::getVal('system_title', 'Plataforma Corporativa RH');
        $loginImagePath = Setting::getVal('login_image_path');

        return view('settings.index', compact('logoPath', 'logoPosition', 'logoX', 'logoY', 'systemTitle', 'loginImagePath'));
    }

    public function guardarLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $name = 'logo_' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/logos');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $image->move($destinationPath, $name);
            
            $path = '/uploads/logos/' . $name;
            Setting::setVal('logo_path', $path);

            return back()->with('success', 'Logo corporativo subido correctamente.');
        }

        return back()->with('error', 'Error al subir el logo.');
    }

    public function guardarPosicion(Request $request)
    {
        $request->validate([
            'logo_position' => 'required|string',
        ]);

        Setting::setVal('logo_position', $request->logo_position);

        if ($request->has('logo_x')) {
            Setting::setVal('logo_x', $request->logo_x);
        }
        if ($request->has('logo_y')) {
            Setting::setVal('logo_y', $request->logo_y);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Posición del logo guardada correctamente.');
    }

    public function guardarTitulo(Request $request)
    {
        $request->validate([
            'system_title' => 'required|string|max:100',
        ]);

        Setting::setVal('system_title', $request->system_title);

        return back()->with('success', 'Título del sistema actualizado correctamente.');
    }

    public function guardarImagenLogin(Request $request)
    {
        $request->validate([
            'login_image' => 'required|image|mimes:jpeg,png,jpg',
        ]);

        if ($request->hasFile('login_image')) {
            $image = $request->file('login_image');
            $name = 'login_bg_' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/login');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $image->move($destinationPath, $name);
            
            $path = '/uploads/login/' . $name;
            
            // Delete old image if exists
            $oldPath = Setting::getVal('login_image_path');
            if ($oldPath && file_exists(public_path($oldPath))) {
                @unlink(public_path($oldPath));
            }

            Setting::setVal('login_image_path', $path);

            return back()->with('success', 'Imagen de inicio de sesión subida correctamente.');
        }

        return back()->with('error', 'Error al subir la imagen.');
    }

    public function eliminarImagenLogin()
    {
        $oldPath = Setting::getVal('login_image_path');
        if ($oldPath && file_exists(public_path($oldPath))) {
            @unlink(public_path($oldPath));
        }

        Setting::setVal('login_image_path', null);

        return back()->with('success', 'Imagen de inicio de sesión eliminada correctamente.');
    }

    public function generateBackup(Request $request)
    {
        $format = $request->input('format', 'sql');
        $driver = DB::connection()->getDriverName();
        
        try {
            $tables = [];
            if ($driver === 'pgsql') {
                $results = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE'");
                foreach ($results as $result) {
                    $tableName = $result->table_name;
                    // Excluir tablas de sesión y caché para evitar que se cierren las sesiones actuales
                    if (in_array($tableName, ['sessions', 'cache'])) {
                        continue;
                    }
                    $tables[] = $tableName;
                }
            } else {
                $results = DB::select("SHOW TABLES");
                foreach ($results as $result) {
                    $vars = get_object_vars($result);
                    $tableName = reset($vars);
                    // Excluir tablas de sesión y caché para evitar que se cierren las sesiones actuales
                    if (in_array($tableName, ['sessions', 'cache'])) {
                        continue;
                    }
                    $tables[] = $tableName;
                }
            }

            $sqlContent = "-- Vacaciones e Incidencias Database Backup\n";
            $sqlContent .= "-- Generated at: " . date('Y-m-d H:i:s') . "\n";
            $sqlContent .= "-- Driver: " . $driver . "\n\n";

            if ($driver === 'pgsql') {
                $sqlContent .= "SET session_replication_role = 'replica';\n\n";
            } else {
                $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
            }

            foreach ($tables as $table) {
                if ($driver === 'pgsql') {
                    $sqlContent .= "DROP TABLE IF EXISTS \"$table\" CASCADE;\n";
                    
                    // Build CREATE TABLE dynamically for pgsql
                    $createSql = "CREATE TABLE \"$table\" (\n";
                    $columns = DB::select("
                        SELECT column_name, data_type, character_maximum_length, is_nullable, column_default 
                        FROM information_schema.columns 
                        WHERE table_schema = 'public' AND table_name = :table
                        ORDER BY ordinal_position
                    ", ['table' => $table]);
                    
                    $colDefs = [];
                    foreach ($columns as $col) {
                        $type = $col->data_type;
                        $default = $col->column_default;
                        
                        // Si el valor por defecto usa una secuencia nextval, lo convertimos a tipo serial/bigserial/smallserial
                        if ($default !== null && preg_match('/nextval\(/i', $default)) {
                            if (stripos($type, 'bigint') !== false) {
                                $type = 'bigserial';
                            } elseif (stripos($type, 'smallint') !== false) {
                                $type = 'smallserial';
                            } else {
                                $type = 'serial';
                            }
                            $default = null; // No necesita DEFAULT nextval(...)
                        }
                        
                        $def = '  "' . $col->column_name . '" ' . $type;
                        if ($col->character_maximum_length && !preg_match('/serial/i', $type)) {
                            $def .= '(' . $col->character_maximum_length . ')';
                        }
                        if ($col->is_nullable === 'NO' && !preg_match('/serial/i', $type)) {
                            $def .= ' NOT NULL';
                        }
                        if ($default !== null) {
                            $def .= ' DEFAULT ' . $default;
                        }
                        $colDefs[] = $def;
                    }
                    
                    $pkQuery = DB::select("
                        SELECT DISTINCT kcu.column_name
                        FROM information_schema.table_constraints tc
                        JOIN information_schema.key_column_usage kcu
                          ON tc.constraint_name = kcu.constraint_name
                          AND tc.table_schema = kcu.table_schema
                          AND tc.table_name = kcu.table_name
                        WHERE tc.constraint_type = 'PRIMARY KEY' 
                          AND tc.table_name = :table
                    ", ['table' => $table]);
                    
                    if (!empty($pkQuery)) {
                        $pkCols = [];
                        foreach ($pkQuery as $pk) {
                            $pkCols[] = '"' . $pk->column_name . '"';
                        }
                        $colDefs[] = '  PRIMARY KEY (' . implode(', ', $pkCols) . ')';
                    }
                    
                    $createSql .= implode(",\n", $colDefs) . "\n);\n\n";
                    $sqlContent .= $createSql;
                } else {
                    $sqlContent .= "DROP TABLE IF EXISTS `$table`;\n";
                    
                    $createTableObj = DB::select("SHOW CREATE TABLE `$table`")[0];
                    $vars = get_object_vars($createTableObj);
                    $createSql = $vars['Create Table'] ?? reset($vars);
                    $sqlContent .= $createSql . ";\n\n";
                }
                
                $rows = DB::table($table)->get();
                foreach ($rows as $row) {
                    $rowArray = (array)$row;
                    $fields = array_keys($rowArray);
                    
                    if ($driver === 'pgsql') {
                        $escapedFields = array_map(function($field) {
                            return "\"$field\"";
                        }, $fields);
                    } else {
                        $escapedFields = array_map(function($field) {
                            return "`$field`";
                        }, $fields);
                    }
                    
                    $escapedValues = array_map(function($val) {
                        if (is_null($val)) {
                            return 'NULL';
                        }
                        return "'" . addslashes($val) . "'";
                    }, $rowArray);
                    
                    if ($driver === 'pgsql') {
                        $sqlContent .= "INSERT INTO \"$table\" (" . implode(', ', $escapedFields) . ") VALUES (" . implode(', ', $escapedValues) . ");\n";
                    } else {
                        $sqlContent .= "INSERT INTO `$table` (" . implode(', ', $escapedFields) . ") VALUES (" . implode(', ', $escapedValues) . ");\n";
                    }
                }
                
                $sqlContent .= "\n";
            }

            if ($driver === 'pgsql') {
                $sqlContent .= "SET session_replication_role = 'origin';\n";
            } else {
                $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";
            }

            $filename = 'respaldo_' . date('Ymd_His');

            if ($format === 'zip') {
                if (!class_exists('ZipArchive')) {
                    return back()->with('error', 'La extensión ZipArchive de PHP no está habilitada.');
                }
                
                $zip = new \ZipArchive();
                $zipFile = tempnam(sys_get_temp_dir(), 'backup') . '.zip';
                if ($zip->open($zipFile, \ZipArchive::CREATE) === TRUE) {
                    $zip->addFromString($filename . '.sql', $sqlContent);
                    $zip->close();
                    
                    return response()->download($zipFile, $filename . '.zip')->deleteFileAfterSend(true);
                }
                
                return back()->with('error', 'No se pudo crear el archivo ZIP.');
            }

            return response($sqlContent)
                ->header('Content-Type', 'application/sql')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '.sql"');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el respaldo: ' . $e->getMessage());
        }
    }

    public function restoreBackup(Request $request)
    {
        if (!$request->hasFile('backup_file')) {
            return back()->with('error', 'Por favor selecciona un archivo de respaldo.');
        }

        $file = $request->file('backup_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['sql', 'zip'])) {
            return back()->with('error', 'El formato del archivo debe ser .sql o .zip.');
        }

        try {
            $sqlContent = '';
            if ($extension === 'zip') {
                if (!class_exists('ZipArchive')) {
                    return back()->with('error', 'La extensión ZipArchive de PHP no está habilitada.');
                }

                $zip = new \ZipArchive();
                if ($zip->open($file->getRealPath()) === TRUE) {
                    $sqlFilename = null;
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'sql') {
                            $sqlFilename = $filename;
                            break;
                        }
                    }

                    if (!$sqlFilename) {
                        return back()->with('error', 'No se encontró ningún archivo .sql dentro del archivo ZIP.');
                    }

                    $sqlContent = $zip->getFromName($sqlFilename);
                    $zip->close();
                } else {
                    return back()->with('error', 'No se pudo abrir el archivo ZIP.');
                }
            } else {
                $sqlContent = file_get_contents($file->getRealPath());
            }

            if (empty($sqlContent)) {
                return back()->with('error', 'El archivo de respaldo está vacío.');
            }

            // Excluir operaciones sobre las tablas sessions y cache para mantener al administrador autenticado
            $sqlContent = preg_replace('/DROP TABLE IF EXISTS [`"]?(sessions|cache)[`"]?( CASCADE)?;/i', '-- Excluded drop', $sqlContent);
            $sqlContent = preg_replace('/CREATE TABLE [`"]?(sessions|cache)[`"]?.*?;/is', '-- Excluded create', $sqlContent);
            $sqlContent = preg_replace('/INSERT INTO [`"]?(sessions|cache)[`"]?.*?;/i', '-- Excluded insert', $sqlContent);

            // Convertir definiciones de nextval/secuencias a serial/bigserial para evitar errores de relación no existente
            $sqlContent = preg_replace_callback(
                '/"([^"]+)"\s+(integer|bigint|smallint)\s+NOT\s+NULL\s+DEFAULT\s+nextval\(\'([^\']+)\'::regclass\)/i',
                function($matches) {
                    $colName = $matches[1];
                    $type = strtolower($matches[2]);
                    if ($type === 'bigint') {
                        $newType = 'bigserial';
                    } elseif ($type === 'smallint') {
                        $newType = 'smallserial';
                    } else {
                        $newType = 'serial';
                    }
                    return "\"$colName\" $newType";
                },
                $sqlContent
            );

            // Corregir PRIMARY KEY duplicados como ("id", "id") a ("id")
            $sqlContent = preg_replace_callback(
                '/PRIMARY KEY\s*\(([^)]+)\)/i',
                function($matches) {
                    $cols = explode(',', $matches[1]);
                    $cols = array_map(function($c) {
                        return trim($c, " \t\n\r\0\x0B`\"");
                    }, $cols);
                    $uniqueCols = array_unique($cols);
                    $escapedCols = array_map(function($c) {
                        return "\"$c\"";
                    }, $uniqueCols);
                    return 'PRIMARY KEY (' . implode(', ', $escapedCols) . ')';
                },
                $sqlContent
            );

            // Validar que corresponda al sistema con tablas clave
            $requiredTables = ['users', 'settings', 'empleados', 'vacaciones', 'saldo_vacaciones', 'movimientos_vacaciones', 'incidencias'];
            $matchCount = 0;
            foreach ($requiredTables as $table) {
                if (stripos($sqlContent, "CREATE TABLE `$table`") !== false 
                    || stripos($sqlContent, "CREATE TABLE \"$table\"") !== false
                    || stripos($sqlContent, "INSERT INTO `$table`") !== false 
                    || stripos($sqlContent, "INSERT INTO \"$table\"") !== false
                    || stripos($sqlContent, "DROP TABLE IF EXISTS `$table`") !== false
                    || stripos($sqlContent, "DROP TABLE IF EXISTS \"$table\"") !== false
                ) {
                    $matchCount++;
                }
            }

            if ($matchCount < 3) {
                return back()->with('error', 'El archivo no parece ser un respaldo válido de este sistema de vacaciones.');
            }

            // Ejecutar la restauración
            $driver = DB::connection()->getDriverName();
            if ($driver === 'pgsql') {
                DB::unprepared("SET session_replication_role = 'replica';");
                DB::unprepared($sqlContent);
                DB::unprepared("SET session_replication_role = 'origin';");
                
                // Sincronizar secuencias para Postgres
                foreach ($requiredTables as $table) {
                    try {
                        $hasId = DB::select("
                            SELECT column_name 
                            FROM information_schema.columns 
                            WHERE table_schema = 'public' 
                              AND table_name = :table 
                              AND column_name = 'id'
                        ", ['table' => $table]);
                        if (!empty($hasId)) {
                            DB::select("SELECT setval(pg_get_serial_sequence('$table', 'id'), coalesce(max(id), 1)) FROM \"$table\"");
                        }
                    } catch (\Exception $e) {
                        // Ignorar errores de secuencias no autoincrementales
                    }
                }
            } else {
                DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');
                DB::unprepared($sqlContent);
                DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');
            }

            // Sincronizar todos los saldos de vacaciones para garantizar coherencia en el Dashboard
            if (class_exists('App\Models\SaldoVacacion')) {
                \App\Models\SaldoVacacion::sincronizarTodos();
            }

            // Borrar la caché de la aplicación
            \Illuminate\Support\Facades\Artisan::call('cache:clear');

            return back()->with('success', 'Base de datos restaurada correctamente. Los datos de colaboradores, saldos, movimientos e incidencias se han sincronizado con el Dashboard.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al restaurar la base de datos: ' . $e->getMessage());
        }
    }

    public function guardarBirthdayAlert(Request $request)
    {
        $request->validate([
            'birthday_alert_days' => 'required|integer|min:0|max:365',
        ]);

        Setting::setVal('birthday_alert_days', $request->birthday_alert_days);

        return back()->with('success', 'Configuración de alertas de cumpleaños actualizada correctamente.');
    }

    public function diaFestivosIndex(Request $request)
    {
        $festivos = DiaFestivo::orderBy('fecha', 'asc')->get();
        
        // Determinar año y mes actual para el calendario
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('n'));
        
        return view('settings.dia_festivos', compact('festivos', 'year', 'month'));
    }

    public function diaFestivosStore(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|in:ley,tradicion',
        ]);

        // Evitar duplicados
        $existe = DiaFestivo::whereDate('fecha', $request->fecha)->exists();
        if ($existe) {
            return back()->with('error', 'Ya se registró un día festivo en esta fecha.');
        }

        DiaFestivo::create([
            'fecha' => $request->fecha,
            'nombre' => $request->nombre,
            'tipo' => $request->tipo,
        ]);

        return back()->with('success', 'Día festivo registrado correctamente.');
    }

    public function diaFestivosDelete($id)
    {
        $festivo = DiaFestivo::findOrFail($id);
        $festivo->delete();

        return back()->with('success', 'Día festivo eliminado correctamente.');
    }

    public function guardarSalarioMinimo(Request $request)
    {
        $request->validate([
            'salario_minimo' => 'required|numeric|min:0',
        ]);

        Setting::setVal('salario_minimo', $request->salario_minimo);

        return back()->with('success', 'Salario mínimo vigente actualizado correctamente.');
    }
}
