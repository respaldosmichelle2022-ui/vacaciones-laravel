@extends('layouts.app')

@section('contenido')

<div style="max-width: 600px; margin: 0 auto;">
    <h1 style="margin-bottom: 25px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 10px;">
        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="width: 28px; height: 28px; color: #6366f1;">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
        Mi Cuenta
    </h1>

    <form action="/mi-cuenta/actualizar" method="POST">
        @csrf
        @method('PUT')

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
            <h3 style="margin-bottom: 15px; font-size: 15px; font-weight: 600; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                Información de la Cuenta (Solo Lectura)
            </h3>
            
            <div class="grupo">
                <label>Nombre Completo</label>
                <input type="text" value="{{ $user->name }}" disabled style="background-color: #f1f5f9; color: #64748b; cursor: not-allowed; border-color: #e2e8f0;">
            </div>

            <div class="grupo">
                <label>Usuario o Correo Electrónico (Login)</label>
                <input type="text" value="{{ $user->email }}" disabled style="background-color: #f1f5f9; color: #64748b; cursor: not-allowed; border-color: #e2e8f0;">
            </div>

            <div class="grupo">
                <label>Rol Asignado</label>
                <input type="text" value="@if($user->role === 'administrador')Administrador (Control total)@elseif($user->role === 'supervisor')Supervisor (Acceso total)@elseif($user->role === 'solo_lectura')Solo Visualización@elseEmpleado (Mi Sitio)@endif" disabled style="background-color: #f1f5f9; color: #64748b; cursor: not-allowed; border-color: #e2e8f0;">
            </div>

            <div class="grupo" style="margin-bottom: 0;">
                <label>Sitio Vinculado</label>
                <input type="text" value="{{ $user->sitio ?? 'Ninguno (Acceso Global)' }}" disabled style="background-color: #f1f5f9; color: #64748b; cursor: not-allowed; border-color: #e2e8f0;">
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 20px; border-radius: 12px; margin-bottom: 25px;">
            <h3 style="margin-bottom: 15px; font-size: 15px; font-weight: 600; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                Modificar Contraseña de Acceso
            </h3>
            
            <p style="color: #64748b; font-size: 12.5px; margin-bottom: 15px;">
                Por seguridad, ingrese su contraseña actual para poder registrar la nueva contraseña.
            </p>

            <div class="grupo">
                <label for="current_password">Contraseña Actual</label>
                <input type="password" name="current_password" id="current_password" required placeholder="Ingrese su contraseña actual">
            </div>

            <div class="grupo">
                <label for="password">Nueva Contraseña</label>
                <input type="password" name="password" id="password" required placeholder="Mínimo 6 caracteres">
            </div>

            <div class="grupo" style="margin-bottom: 0;">
                <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Repita la nueva contraseña">
            </div>
        </div>

        <button type="submit" class="boton" style="width: 100%; justify-content: center; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);">
            Actualizar Contraseña
        </button>
    </form>
</div>

@endsection
