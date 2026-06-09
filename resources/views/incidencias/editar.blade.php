@extends('layouts.app')

@section('contenido')

<div style="max-width: 600px; margin: 0 auto;">
    <a href="/incidencias" class="boton-volver">
        ← Volver a Incidencias
    </a>

    <h1 style="margin-bottom: 25px; font-weight: 700; color: #0f172a;">Editar Incidencia</h1>

    <form action="/incidencias/actualizar/{{ $incidencia->id }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grupo">
            <label for="empleado_id">Empleado</label>
            <select name="empleado_id" id="empleado_id" required>
                @foreach($empleados as $e)
                    <option value="{{ $e->id }}" {{ $incidencia->empleado_id == $e->id ? 'selected' : '' }}>
                        #{{ $e->numero_empleado }} - {{ $e->nombre }} {{ $e->apellido_paterno }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grupo">
            <label for="tipo">Tipo de Incidencia</label>
            <select name="tipo" id="tipo" required>
                <option value="falta" {{ $incidencia->tipo == 'falta' ? 'selected' : '' }}>Falta (Inasistencia)</option>
                <option value="permiso" {{ $incidencia->tipo == 'permiso' ? 'selected' : '' }}>Permiso (Justificado)</option>
                <option value="retardo" {{ $incidencia->tipo == 'retardo' ? 'selected' : '' }}>Retardo (Llegada tarde)</option>
                <option value="permiso_horas" {{ $incidencia->tipo == 'permiso_horas' ? 'selected' : '' }}>Permiso por horas</option>
                <option value="incapacidad" {{ $incidencia->tipo == 'incapacidad' ? 'selected' : '' }}>Incapacidad</option>
            </select>
        </div>

        <div class="grupo">
            <label for="fecha">Fecha del Evento</label>
            <input type="date" name="fecha" id="fecha" value="{{ $incidencia->fecha }}" required>
        </div>

        <div class="grupo">
            <label for="observaciones">Observaciones / Detalles</label>
            <textarea name="observaciones" id="observaciones" rows="4">{{ $incidencia->observaciones }}</textarea>
        </div>

        <button type="submit" class="boton" style="width: 100%; justify-content: center; margin-top: 15px;">
            Actualizar Incidencia
        </button>
    </form>
</div>

@endsection
