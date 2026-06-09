@extends('layouts.app')

@section('contenido')

<div style="max-width: 700px; margin: 0 auto;">
    <h1 style="margin-bottom: 25px; font-weight: 700; color: #0f172a;">Configuración de Identidad Corporativa</h1>

    <!-- Preview Box -->
    <div style="background: #f1f5f9; padding: 25px; border-radius: 12px; margin-bottom: 30px; text-align: center; border: 1px dashed #cbd5e1;">
        <span style="display: block; font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 15px;">Vista Previa del Logo Actual</span>
        <img src="{{ $logoPath }}" alt="Logo Corporativo" style="max-width: 250px; max-height: 120px; object-fit: contain; background: white; padding: 10px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
    </div>

    <!-- Upload Form -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
        <h3 style="margin-bottom: 15px; font-weight: 600; color: #1e293b;">1. Subir Logo de la Empresa</h3>
        <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">Sube una imagen corporativa (se recomiendan formatos PNG o SVG con fondo transparente).</p>
        
        <form action="/configuracion/logo" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grupo">
                <input type="file" name="logo" required style="border: 1px solid #cbd5e1; padding: 10px; border-radius: 8px; width: 100%;">
            </div>
            <button type="submit" class="boton">
                <span>📤</span> Subir Logo
            </button>
        </form>
    </div>

    <!-- Position Form -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
        <h3 style="margin-bottom: 15px; font-weight: 600; color: #1e293b;">2. Posición y Comportamiento del Logo</h3>
        <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">Selecciona la ubicación donde deseas mostrar el logo corporativo dentro del portal.</p>

        <form action="/configuracion/posicion" method="POST">
            @csrf
            <div class="grupo">
                <label for="logo_position">Ubicación del Logo</label>
                <select name="logo_position" id="logo_position" required>
                    <option value="sidebar_top" {{ $logoPosition == 'sidebar_top' ? 'selected' : '' }}>Fijo en la parte superior del Menú Lateral (Sidebar)</option>
                    <option value="topbar_left" {{ $logoPosition == 'topbar_left' ? 'selected' : '' }}>Fijo en la Barra Superior (Topbar) - Izquierda</option>
                    <option value="topbar_right" {{ $logoPosition == 'topbar_right' ? 'selected' : '' }}>Fijo en la Barra Superior (Topbar) - Derecha</option>
                    <option value="draggable" {{ $logoPosition == 'draggable' ? 'selected' : '' }}>Flotante, Libre y Movible (Arrastrable por el usuario)</option>
                </select>
            </div>

            @if($logoPosition === 'draggable')
                <div style="background: #e0f2fe; color: #0369a1; padding: 12px 15px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; font-weight: 500;">
                    💡 <strong>Modo Arrastrable Activo:</strong> Puedes hacer clic y arrastrar el logo flotante a cualquier parte de tu pantalla. Su ubicación se guardará automáticamente al soltarlo.
                </div>
            @endif

            <button type="submit" class="boton">
                <span>💾</span> Guardar Configuración de Ubicación
            </button>
        </form>
    </div>

    <!-- Title Form -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
        <h3 style="margin-bottom: 15px; font-weight: 600; color: #1e293b;">3. Título del Sistema</h3>
        <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">Establece el nombre o título que identificará a la plataforma en todas las pantallas y pestañas del navegador.</p>
        
        <form action="/configuracion/titulo" method="POST">
            @csrf
            <div class="grupo">
                <label for="system_title">Nombre del Sistema</label>
                <input type="text" name="system_title" id="system_title" required value="{{ $systemTitle }}" placeholder="Ej. Plataforma Corporativa RH" style="border: 1px solid #cbd5e1; padding: 10px; border-radius: 8px; width: 100%;">
            </div>
            <button type="submit" class="boton">
                <span>💾</span> Guardar Título
            </button>
        </form>
    </div>

    <!-- Login Image Form -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px;">
        <h3 style="margin-bottom: 15px; font-weight: 600; color: #1e293b;">4. Imagen de Inicio de Sesión</h3>
        <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">Sube una imagen corporativa que servirá como banner o portada en la tarjeta de inicio de sesión de los usuarios (formatos JPG, PNG).</p>
        
        @if($loginImagePath)
            <div style="background: #f1f5f9; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; border: 1px solid #cbd5e1; position: relative;">
                <span style="display: block; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 10px;">Imagen de Login Actual</span>
                <img src="{{ $loginImagePath }}" alt="Banner Login" style="max-width: 100%; max-height: 150px; object-fit: contain; border-radius: 6px;">
                
                <form action="/configuracion/login-image/delete" method="POST" style="margin-top: 15px;">
                    @csrf
                    <button type="submit" class="boton" style="background: #ef4444; margin: 0 auto; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);" onclick="return confirm('¿Seguro que deseas eliminar la imagen actual de inicio de sesión?')">
                        <span>🗑</span> Eliminar Imagen Actual
                    </button>
                </form>
            </div>
        @endif

        <form action="/configuracion/login-image" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grupo">
                <input type="file" name="login_image" required accept="image/jpeg,image/png,image/jpg" style="border: 1px solid #cbd5e1; padding: 10px; border-radius: 8px; width: 100%;">
            </div>
            <button type="submit" class="boton">
                <span>📤</span> Subir Imagen de Login
            </button>
        </form>
    </div>

    <!-- Backup and Restore Section -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-top: 30px;">
        <h3 style="margin-bottom: 15px; font-weight: 600; color: #1e293b;">5. Respaldos y Restauración de Base de Datos</h3>
        <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">Gestiona copias de seguridad de toda la información del sistema (colaboradores, vacaciones, incidencias, usuarios, etc.). La sesión actual del administrador se mantendrá activa.</p>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-top: 15px;">
            <!-- Generation Column -->
            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 20px;">
                <h4 style="margin-bottom: 10px; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <span>📥</span> Generar Respaldo
                </h4>
                <p style="color: #64748b; font-size: 12px; margin-bottom: 15px;">Descarga una copia completa del estado actual de la base de datos (excluye datos temporales de sesión).</p>
                
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <form action="/configuracion/backup" method="POST" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="format" value="sql">
                        <button type="submit" class="boton" style="background: #10b981; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.2);">
                            Descargar .SQL
                        </button>
                    </form>
                    <form action="/configuracion/backup" method="POST" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="format" value="zip">
                        <button type="submit" class="boton" style="background: #059669; box-shadow: 0 4px 14px rgba(5, 150, 105, 0.2);">
                            Descargar .ZIP
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Restoration Column -->
            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 20px;">
                <h4 style="margin-bottom: 10px; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <span>📤</span> Restaurar Respaldo
                </h4>
                <p style="color: #64748b; font-size: 12px; margin-bottom: 15px;">⚠️ <strong>Advertencia:</strong> Esto reemplazará toda la información del sistema. Los datos se sincronizarán y recalcularán automáticamente con el Dashboard al finalizar.</p>
                
                <form action="/configuracion/restore" method="POST" enctype="multipart/form-data" id="restoreForm" style="margin: 0;" onsubmit="return confirmarRestauracion()">
                    @csrf
                    <div class="grupo" style="margin-bottom: 15px;">
                        <input type="file" name="backup_file" required accept=".sql,.zip" style="border: 1px solid #cbd5e1; padding: 8px; border-radius: 8px; width: 100%; font-size: 12px;">
                    </div>
                    <button type="submit" class="boton" style="background: #ef4444; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.2); width: 100%; justify-content: center;">
                        Subir y Restaurar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Birthday Alert Form -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-top: 30px;">
        <h3 style="margin-bottom: 15px; font-weight: 600; color: #1e293b;">6. Configuración de Alertas de Cumpleaños</h3>
        <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">Configura cuántos días antes de la fecha de nacimiento de un empleado se debe mostrar la notificación de cumpleaños en el Dashboard.</p>
        
        <form action="/configuracion/cumpleanos" method="POST">
            @csrf
            <div class="grupo">
                <label for="birthday_alert_days">Días de Anticipación para la Alerta</label>
                <input type="number" name="birthday_alert_days" id="birthday_alert_days" required min="0" max="365" value="{{ \App\Models\Setting::getVal('birthday_alert_days', 7) }}" style="border: 1px solid #cbd5e1; padding: 10px; border-radius: 8px; width: 100%;">
            </div>
            <button type="submit" class="boton">
                <span>💾</span> Guardar Configuración de Alertas
            </button>
        </form>
    </div>

    <!-- Minimum Wage Form -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-top: 30px;">
        <h3 style="margin-bottom: 15px; font-weight: 600; color: #1e293b;">7. Configuración de Salario Mínimo Vigente</h3>
        <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">Establece el salario mínimo general que se usará como valor por defecto al asignar y calcular la prima vacacional de los empleados.</p>
        
        <form action="/configuracion/salario" method="POST">
            @csrf
            <div class="grupo">
                <label for="salario_minimo">Salario Mínimo General ($ MXN)</label>
                <input type="number" step="0.01" name="salario_minimo" id="salario_minimo" required min="0" value="{{ \App\Models\Setting::getVal('salario_minimo', 315.04) }}" style="border: 1px solid #cbd5e1; padding: 10px; border-radius: 8px; width: 100%;">
            </div>
            <button type="submit" class="boton">
                <span>💾</span> Guardar Salario Mínimo
            </button>
        </form>
    </div>
</div>

<script>
    function confirmarRestauracion() {
        const doubleCheck = confirm("⚠️ ATENCIÓN: Estás a punto de restaurar la base de datos.\n\nEsta acción eliminará todas las tablas actuales y las reemplazará con el contenido del respaldo subido.\n\n¿Estás completamente seguro de que deseas continuar?");
        if (doubleCheck) {
            return confirm("Por favor, confirma una vez más que deseas REEMPLAZAR todos los datos actuales con la copia de seguridad.");
        }
        return false;
    }
</script>

@endsection
