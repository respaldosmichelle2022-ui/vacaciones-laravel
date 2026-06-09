@extends('layouts.app')

@section('contenido')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
    <h1 style="font-weight: 700; color: #0f172a; margin: 0;">Gestión de Usuarios y Roles</h1>
    <div style="display:flex; gap:10px;">
        <a href="/usuarios/exportar/excel" class="boton" style="background: #16a34a; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);">
            <span>📥</span> Exportar Excel
        </a>
        <button onclick="window.print()" class="boton" style="background: #475569; box-shadow: 0 4px 12px rgba(71, 85, 105, 0.2);">
            <span>🖨️</span> Exportar PDF
        </button>
        <a href="/usuarios/crear" class="boton">
            <span>+</span> Crear Nuevo Usuario
        </a>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Usuario / Email</th>
            <th>Rol</th>
            <th>Sitio Vinculado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $u)
            <tr>
                <td style="font-weight: 600;">{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>
                    @if($u->role === 'administrador')
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: #fee2e2; color: #991b1b;">
                            Administrador
                        </span>
                    @elseif($u->role === 'solo_lectura')
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: #fef08a; color: #854d0e;">
                            Solo Visualización
                        </span>
                    @else
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: #e0f2fe; color: #0369a1;">
                            Empleado
                        </span>
                    @endif
                </td>
                <td>
                    @if($u->sitio)
                        <span style="color: #475569; font-weight: 600;">
                            {{ $u->sitio }}
                        </span>
                    @else
                        <em style="color: #94a3b8;">Ninguno (Acceso Global)</em>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 15px;">
                        <a href="/usuarios/editar/{{ $u->id }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">
                            ✏️ Editar
                        </a>
                        <a href="/usuarios/eliminar/{{ $u->id }}" onclick="return confirm('¿Seguro que deseas eliminar este usuario?')" style="color: #ef4444; text-decoration: none; font-weight: 600;">
                            🗑 Eliminar
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection
