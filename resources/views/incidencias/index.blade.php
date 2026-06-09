@extends('layouts.app')

@section('contenido')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
    <h1 style="font-weight: 700; color: #0f172a; margin: 0;">Registro de Incidencias</h1>
    <div style="display:flex; gap:10px;">
        <a href="/incidencias/exportar/excel" class="boton" style="background: #16a34a; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);">
            <span>📥</span> Exportar Excel
        </a>
        <button onclick="window.print()" class="boton" style="background: #475569; box-shadow: 0 4px 12px rgba(71, 85, 105, 0.2);">
            <span>🖨️</span> Exportar PDF
        </button>
        @if(Auth::user()->esSoloLectura())
        <button class="boton" style="background: #94a3b8; cursor: not-allowed; box-shadow: none;" disabled>
            <span>+</span> Registrar Incidencia
        </button>
        @else
        <a href="/incidencias/crear" class="boton">
            <span>+</span> Registrar Incidencia
        </a>
        @endif
    </div>
</div>

<!-- Filtros y Búsqueda -->
<form action="/incidencias" method="GET" style="display: flex; gap: 15px; margin-bottom: 25px; align-items: flex-end; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 200px;">
        <label for="buscar" style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Buscar Empleado</label>
        <input type="text" name="buscar" id="buscar" placeholder="Número, nombre o apellido..." value="{{ $buscar }}" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
    </div>
    
    <div style="width: 180px;">
        <label for="tipo" style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Filtrar Tipo</label>
        <select name="tipo" id="tipo" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
            <option value="">Todos los tipos</option>
            <option value="falta" {{ $tipo == 'falta' ? 'selected' : '' }}>Falta</option>
            <option value="permiso" {{ $tipo == 'permiso' ? 'selected' : '' }}>Permiso</option>
            <option value="retardo" {{ $tipo == 'retardo' ? 'selected' : '' }}>Retardo</option>
            <option value="permiso_horas" {{ $tipo == 'permiso_horas' ? 'selected' : '' }}>Permiso por horas</option>
            <option value="incapacidad" {{ $tipo == 'incapacidad' ? 'selected' : '' }}>Incapacidad</option>
        </select>
    </div>

    <div>
        <button type="submit" class="boton" style="padding: 11px 20px;">
            Filtrar
        </button>
        <a href="/incidencias" class="boton-volver" style="margin-bottom: 0; padding: 11px 20px; display: inline-flex; height: auto;">
            Limpiar
        </a>
    </div>
</form>

<table>
    <thead>
        <tr>
            <th>Empleado</th>
            <th>Tipo</th>
            <th>Fecha</th>
            <th>Observaciones</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($incidencias as $inc)
            <tr>
                <td style="font-weight: 600;">
                    #{{ $inc->empleado->numero_empleado }} - {{ $inc->empleado->nombre }} {{ $inc->empleado->apellido_paterno }}
                </td>
                <td>
                    <span class="badge-estado" style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;
                        @if($inc->tipo == 'falta') background: #fee2e2; color: #991b1b;
                        @elseif($inc->tipo == 'retardo') background: #fef3c7; color: #92400e;
                        @else background: #dcfce7; color: #166534; @endif">
                        @if($inc->tipo == 'falta')
                            Falta
                        @elseif($inc->tipo == 'retardo')
                            Retardo
                        @elseif($inc->tipo == 'permiso')
                            Permiso
                        @elseif($inc->tipo == 'permiso_horas')
                            Permiso por horas
                        @elseif($inc->tipo == 'incapacidad')
                            Incapacidad
                        @endif
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($inc->fecha)->format('d/m/Y') }}</td>
                <td>{{ $inc->observaciones ?? 'Sin observaciones' }}</td>
                <td>
                    <div style="display: flex; gap: 15px;">
                        @if(Auth::user()->esSoloLectura())
                            <span style="color: #94a3b8; font-weight: 600; cursor: not-allowed; opacity: 0.6;">
                                ✏️ Editar
                            </span>
                            <span style="color: #94a3b8; font-weight: 600; cursor: not-allowed; opacity: 0.6;">
                                🗑 Eliminar
                            </span>
                        @else
                            <a href="/incidencias/editar/{{ $inc->id }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">
                                ✏️ Editar
                            </a>
                            <a href="/incidencias/eliminar/{{ $inc->id }}" onclick="return confirm('¿Seguro que deseas eliminar esta incidencia?')" style="color: #ef4444; text-decoration: none; font-weight: 600;">
                                🗑 Eliminar
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #94a3b8; padding: 30px;">
                    No se encontraron incidencias registradas.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
