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
<div style="display: flex; gap: 15px; margin-bottom: 25px; align-items: flex-end; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 200px;">
        <label for="buscar" style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Buscar Empleado</label>
        <input type="text" id="buscar" placeholder="Buscar por número o nombre completo..." value="{{ $buscar }}" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
    </div>

    @if(count($sitios) > 1 || !auth()->user()->sitio)
    <div style="width: 180px;">
        <label for="sitio" style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Filtrar Sitio</label>
        <select id="sitio" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
            <option value="">Todos los sitios</option>
            @foreach($sitios as $s)
                <option value="{{ $s }}" {{ $sitioFiltro == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
    </div>
    @endif
    
    <div style="width: 180px;">
        <label for="tipo" style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Filtrar Tipo</label>
        <select id="tipo" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
            <option value="">Todos los tipos</option>
            <option value="falta" {{ $tipo == 'falta' ? 'selected' : '' }}>Falta</option>
            <option value="permiso" {{ $tipo == 'permiso' ? 'selected' : '' }}>Permiso</option>
            <option value="retardo" {{ $tipo == 'retardo' ? 'selected' : '' }}>Retardo</option>
            <option value="permiso_horas" {{ $tipo == 'permiso_horas' ? 'selected' : '' }}>Permiso por horas</option>
            <option value="incapacidad" {{ $tipo == 'incapacidad' ? 'selected' : '' }}>Incapacidad</option>
        </select>
    </div>

    <div>
        <a href="/incidencias" class="boton-volver" style="margin-bottom: 0; padding: 11px 20px; display: inline-flex; height: auto;">
            Limpiar Filtros
        </a>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Empleado</th>
            <th>Sitio</th>
            <th>Tipo</th>
            <th>Fecha</th>
            <th>Observaciones</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($incidencias as $inc)
            <tr class="incidencia-row"
                data-numero-empleado="{{ $inc->empleado->numero_empleado }}"
                data-nombre-empleado="{{ $inc->empleado->nombre }} {{ $inc->empleado->apellido_paterno }} {{ $inc->empleado->apellido_materno }}"
                data-sitio="{{ $inc->empleado->sitio }}"
                data-tipo="{{ $inc->tipo }}">
                <td style="font-weight: 600;">
                    #{{ $inc->empleado->numero_empleado }} - {{ $inc->empleado->nombre }} {{ $inc->empleado->apellido_paterno }} {{ $inc->empleado->apellido_materno }}
                </td>
                <td style="font-weight: 500; color: #475569;">{{ $inc->empleado->sitio }}</td>
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
            <tr id="empty-state-row">
                <td colspan="6" style="text-align: center; color: #94a3b8; padding: 30px;">
                    No se encontraron incidencias registradas.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputBuscar = document.getElementById('buscar');
        const selectTipo = document.getElementById('tipo');
        const selectSitio = document.getElementById('sitio');

        function filtrarTabla() {
            const buscarVal = inputBuscar.value.toLowerCase().trim();
            const tipoVal = selectTipo.value;
            const sitioVal = selectSitio ? selectSitio.value : '';

            const rows = document.querySelectorAll('.incidencia-row');
            let visibleRowsCount = 0;

            rows.forEach(row => {
                const numEmp = row.getAttribute('data-numero-empleado') || '';
                const nomEmp = row.getAttribute('data-nombre-empleado').toLowerCase() || '';
                const tipoEmp = row.getAttribute('data-tipo') || '';
                const sitioEmp = row.getAttribute('data-sitio') || '';

                const matchesBuscar = numEmp.includes(buscarVal) || nomEmp.includes(buscarVal);
                const matchesTipo = tipoVal === '' || tipoEmp === tipoVal;
                const matchesSitio = sitioVal === '' || sitioEmp === sitioVal;

                if (matchesBuscar && matchesTipo && matchesSitio) {
                    row.style.display = '';
                    visibleRowsCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Control de fila vacía
            let emptyRow = document.getElementById('no-results-row');
            const defaultEmptyRow = document.getElementById('empty-state-row');

            if (visibleRowsCount === 0) {
                if (defaultEmptyRow) {
                    defaultEmptyRow.style.display = '';
                } else {
                    if (!emptyRow) {
                        const tbody = document.querySelector('table tbody');
                        emptyRow = document.createElement('tr');
                        emptyRow.id = 'no-results-row';
                        emptyRow.innerHTML = `
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 30px;">
                                No se encontraron incidencias registradas con los filtros seleccionados.
                            </td>
                        `;
                        tbody.appendChild(emptyRow);
                    } else {
                        emptyRow.style.display = '';
                    }
                }
            } else {
                if (defaultEmptyRow) {
                    defaultEmptyRow.style.display = 'none';
                }
                if (emptyRow) {
                    emptyRow.style.display = 'none';
                }
            }
        }

        if (inputBuscar) inputBuscar.addEventListener('input', filtrarTabla);
        if (selectTipo) selectTipo.addEventListener('change', filtrarTabla);
        if (selectSitio) selectSitio.addEventListener('change', filtrarTabla);

        // Ejecutar inicialmente para aplicar filtros precargados
        filtrarTabla();
    });
</script>
@endsection
