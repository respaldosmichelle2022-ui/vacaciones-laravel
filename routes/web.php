<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\MovimientoVacacionController;
use App\Http\Controllers\SaldoVacacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\SettingController;

// Autenticación pública
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Portal Protegido
// Portal Protegido
Route::middleware(['auth'])->group(function () {
    
    // Módulos Generales (Accesibles por Empleados y Administradores)
    Route::middleware(['restringir.solo_lectura'])->group(function () {
        // Portal del Empleado (Autoservicio)
        Route::get('/mi-sitio', [PersonalController::class, 'index']);
        Route::post('/mi-sitio/solicitar-vacaciones', [PersonalController::class, 'solicitarVacaciones']);

        // Dashboard
        Route::get('/', [DashboardController::class, 'index']);

        // Empleados
        Route::get('/empleados', [EmpleadoController::class, 'index']);
        Route::get('/empleados/crear', [EmpleadoController::class, 'crear']);
        Route::post('/empleados', [EmpleadoController::class, 'guardar']);
        Route::get('/empleados/{id}/editar', [EmpleadoController::class, 'editar']);
        Route::put('/empleados/actualizar/{id}', [EmpleadoController::class, 'actualizar']);
        Route::post('/empleados/cambiar-estado/{id}', [EmpleadoController::class, 'cambiarEstado']);
        Route::get('/empleados/exportar/excel', [EmpleadoController::class, 'exportarExcel']);
        Route::get('/empleados/{id}/detalle-modal', [EmpleadoController::class, 'detalleModal']);

        // Saldos vacaciones
        Route::get('/vacaciones', [SaldoVacacionController::class, 'index']);
        Route::get('/vacaciones/crear', [SaldoVacacionController::class, 'crear']);
        Route::post('/vacaciones', [SaldoVacacionController::class, 'guardar']);
        Route::get('/vacaciones/editar/{id}', [SaldoVacacionController::class, 'editar']);
        Route::put('/vacaciones/actualizar/{id}', [SaldoVacacionController::class, 'actualizar']);
        Route::get('/vacaciones/exportar/excel', [SaldoVacacionController::class, 'exportarExcel']);

        // Movimientos vacaciones
        Route::get('/movimientos', [MovimientoVacacionController::class, 'index']);
        Route::get('/movimientos/crear', [MovimientoVacacionController::class, 'crear']);
        Route::post('/movimientos', [MovimientoVacacionController::class, 'guardar']);
        Route::get('/movimientos/editar/{id}', [MovimientoVacacionController::class, 'editar']);
        Route::put('/movimientos/actualizar/{id}', [MovimientoVacacionController::class, 'actualizar']);
        Route::get('/movimientos/exportar/excel', [MovimientoVacacionController::class, 'exportarExcel']);
        Route::get('/movimientos/imprimir/{id}', [MovimientoVacacionController::class, 'imprimir']);

        // Incidencias
        Route::get('/incidencias', [IncidenciaController::class, 'index']);
        Route::get('/incidencias/crear', [IncidenciaController::class, 'crear']);
        Route::post('/incidencias', [IncidenciaController::class, 'guardar']);
        Route::get('/incidencias/editar/{id}', [IncidenciaController::class, 'editar']);
        Route::put('/incidencias/actualizar/{id}', [IncidenciaController::class, 'actualizar']);
        Route::get('/incidencias/reporte', [IncidenciaController::class, 'reporte']);
        Route::get('/incidencias/exportar/excel', [IncidenciaController::class, 'exportarExcel']);
    });

    // Módulos y Acciones Administrativas (Sólo Administradores)
    Route::middleware(['admin'])->group(function () {
        
        // Empleados - Acciones administrativas críticas
        Route::post('/empleados/importar', [EmpleadoController::class, 'importar']);
        Route::delete('/empleados/eliminar/{id}', [EmpleadoController::class, 'eliminar']);
        Route::post('/empleados/eliminar-masivo', [EmpleadoController::class, 'eliminarMasivo']);
        Route::post('/empleados/eliminar-por-sitio', [EmpleadoController::class, 'eliminarPorSitio']);

        // Saldos vacaciones - Acciones de eliminación
        Route::get('/vacaciones/eliminar/{id}', [SaldoVacacionController::class, 'eliminar']);

        // Movimientos vacaciones - Acciones de eliminación
        Route::get('/movimientos/eliminar/{id}', [MovimientoVacacionController::class, 'eliminar']);

        // Incidencias - Acciones de eliminación
        Route::get('/incidencias/eliminar/{id}', [IncidenciaController::class, 'eliminar']);

        // Usuarios y Roles
        Route::get('/usuarios', [UsuarioController::class, 'index']);
        Route::get('/usuarios/crear', [UsuarioController::class, 'crear']);
        Route::post('/usuarios', [UsuarioController::class, 'guardar']);
        Route::get('/usuarios/editar/{id}', [UsuarioController::class, 'editar']);
        Route::put('/usuarios/actualizar/{id}', [UsuarioController::class, 'actualizar']);
        Route::get('/usuarios/eliminar/{id}', [UsuarioController::class, 'eliminar']);
        Route::get('/usuarios/exportar/excel', [UsuarioController::class, 'exportarExcel']);

        // Configuraciones y Logo
        Route::get('/configuracion', [SettingController::class, 'index']);
        Route::post('/configuracion/logo', [SettingController::class, 'guardarLogo']);
        Route::post('/configuracion/posicion', [SettingController::class, 'guardarPosicion']);
        Route::post('/configuracion/titulo', [SettingController::class, 'guardarTitulo']);
        Route::post('/configuracion/login-image', [SettingController::class, 'guardarImagenLogin']);
        Route::post('/configuracion/login-image/delete', [SettingController::class, 'eliminarImagenLogin']);
        Route::post('/configuracion/backup', [SettingController::class, 'generateBackup']);
        Route::post('/configuracion/restore', [SettingController::class, 'restoreBackup']);
        Route::post('/configuracion/cumpleanos', [SettingController::class, 'guardarBirthdayAlert']);
        Route::post('/configuracion/salario', [SettingController::class, 'guardarSalarioMinimo']);
        
        Route::get('/configuracion/festivos', [SettingController::class, 'diaFestivosIndex']);
        Route::post('/configuracion/festivos', [SettingController::class, 'diaFestivosStore']);
        Route::delete('/configuracion/festivos/{id}', [SettingController::class, 'diaFestivosDelete']);
    });

});