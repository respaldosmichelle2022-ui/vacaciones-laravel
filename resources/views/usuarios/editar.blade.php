@extends('layouts.app')

@section('contenido')

<div style="max-width: 600px; margin: 0 auto;">
    <a href="/usuarios" class="boton-volver">
        ← Volver a Usuarios
    </a>

    <h1 style="margin-bottom: 25px; font-weight: 700; color: #0f172a;">Editar Usuario</h1>

    <form action="/usuarios/actualizar/{{ $user->id }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grupo">
            <label for="name">Nombre Completo</label>
            <input type="text" name="name" id="name" required placeholder="Nombre de la persona" value="{{ old('name', $user->name) }}">
        </div>

        <div class="grupo">
            <label for="email">Usuario o Correo Electrónico (Login)</label>
            <input type="text" name="email" id="email" required placeholder="usuario, usuario123 o correo@empresa.com" value="{{ old('email', $user->email) }}">
        </div>

        <div class="grupo">
            <label for="password">Contraseña (Dejar vacío para mantener actual)</label>
            <input type="password" name="password" id="password" placeholder="Mínimo 6 caracteres (opcional)">
        </div>

        <div class="grupo">
            <label for="role">Rol en el Sistema</label>
            <select name="role" id="roleSelect" required onchange="toggleSitioSelect()">
                <option value="empleado" {{ old('role', $user->role) === 'empleado' ? 'selected' : '' }}>Empleado (Acceso limitado a su sitio propio)</option>
                <option value="solo_lectura" {{ old('role', $user->role) === 'solo_lectura' ? 'selected' : '' }}>Solo Visualización (Consulta de información en su sitio asignado)</option>
                <option value="administrador" {{ old('role', $user->role) === 'administrador' ? 'selected' : '' }}>Administrador (Control total del sistema)</option>
            </select>
            <div id="infoSoloLectura" style="display: none; background-color: #fef08a; color: #854d0e; padding: 10px; border-radius: 6px; margin-top: 8px; font-size: 13px; font-weight: 500; border: 1px solid #fde047;">
                ℹ️ <strong>Solo Visualización:</strong> Este tipo de usuario solo puede consultar datos del sitio asignado. No podrá realizar modificaciones, eliminaciones ni importar información.
            </div>
        </div>

        <div class="grupo" id="sitioGroup">
            <label for="sitio">Asociar a Sitio</label>
            <select name="sitio" id="sitio">
                <option value="">-- Seleccionar Sitio (Global / Sin Restricción) --</option>
                @foreach($sitios as $s)
                    <option value="{{ $s }}" {{ old('sitio', $user->sitio) == $s ? 'selected' : '' }}>
                        {{ $s }}
                    </option>
                @endforeach
            </select>
            <p style="color: #64748b; font-size: 11px; margin-top: 5px;">Los roles asignados se aplicarán sobre el sitio asociado. Si es empleado o solo visualización, la asociación a un sitio es requerida.</p>
        </div>

        <button type="submit" class="boton" style="width: 100%; justify-content: center; margin-top: 15px;">
            Actualizar Usuario
        </button>
    </form>
</div>

<script>
    function toggleSitioSelect() {
        const role = document.getElementById('roleSelect').value;
        const sitioSelect = document.getElementById('sitio');
        const infoSoloLectura = document.getElementById('infoSoloLectura');
        
        if (role === 'administrador') {
            sitioSelect.required = false;
        } else {
            sitioSelect.required = true;
        }

        if (role === 'solo_lectura') {
            infoSoloLectura.style.display = 'block';
        } else {
            infoSoloLectura.style.display = 'none';
        }
    }
    
    // Run on startup
    document.getElementById('roleSelect').addEventListener('change', toggleSitioSelect);
    toggleSitioSelect();
</script>

@endsection
