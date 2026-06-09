@extends('layouts.app')

@section('contenido')

<h1 style="margin-bottom:30px; font-size:36px; font-weight:700; color:#0f172a;">
    Control de Vacaciones
</h1>

@if(session('success'))
<div class="alerta-success">
    {{ session('success') }}
</div>
@endif

<div style="display:flex; flex-direction:column; gap:15px; margin-bottom:25px; background:#f8fafc; border:1px solid #e2e8f0; padding:20px; border-radius:12px;">
        <div style="display:flex; gap:10px;">
            <a href="/vacaciones/exportar/excel" class="boton" style="background: #16a34a; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);">
                <span>📥</span> Exportar Excel
            </a>
            <button onclick="window.print()" class="boton" style="background: #475569; box-shadow: 0 4px 12px rgba(71, 85, 105, 0.2);">
                <span>🖨️</span> Exportar PDF
            </button>
            @if(Auth::user()->esSoloLectura())
            <button class="boton" style="background: #94a3b8; cursor: not-allowed; box-shadow: none;" disabled>
                <span>+</span> Agregar Saldo
            </button>
            @else
            <a href="/vacaciones/crear" class="boton">
                <span>+</span> Agregar Saldo
            </a>
            @endif
        </div>
        <div style="position:relative; display:inline-block;">
            <input type="text" id="buscar-saldos" placeholder="Buscar por número, nombre..." style="padding:10px 14px; width:280px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; outline:none; transition:all 0.2s ease;" autocomplete="off">
            <div id="buscar-saldos-autocomplete" style="position:absolute; top:100%; left:0; right:0; background:white; border:1px solid #cbd5e1; border-radius:8px; max-height:200px; overflow-y:auto; z-index:1000; display:none; box-shadow:0 4px 10px rgba(0,0,0,0.1); margin-top:5px;"></div>
        </div>
    </div>
    
    <!-- Filtros avanzados -->
    <div style="display:flex; gap:15px; flex-wrap:wrap; align-items:center;">
        @php
            $periodos = $saldos->pluck('periodo')->unique()->sort();
            $sitios = $saldos->pluck('empleado.sitio')->unique()->filter()->sort();
        @endphp
        
        <div style="display:flex; flex-direction:column; gap:5px;">
            <label style="font-size:12px; font-weight:600; color:#64748b;">Periodo</label>
            <select id="filtro-periodo" style="padding:10px; border:1px solid #cbd5e1; border-radius:8px; background:white; font-size:13px; font-weight:500; outline:none;">
                <option value="">-- Todos --</option>
                @foreach($periodos as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                @endforeach
            </select>
        </div>

        <div style="display:flex; flex-direction:column; gap:5px;">
            <label style="font-size:12px; font-weight:600; color:#64748b;">Estado Colaborador</label>
            <select id="filtro-estado" style="padding:10px; border:1px solid #cbd5e1; border-radius:8px; background:white; font-size:13px; font-weight:500; outline:none;">
                <option value="">-- Todos --</option>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </div>

        <div style="display:flex; flex-direction:column; gap:5px;">
            <label style="font-size:12px; font-weight:600; color:#64748b;">Sitio</label>
            <select id="filtro-sitio" style="padding:10px; border:1px solid #cbd5e1; border-radius:8px; background:white; font-size:13px; font-weight:500; outline:none;">
                <option value="">-- Todos --</option>
                @foreach($sitios as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div style="overflow-x:auto;">
    <table>
        <thead>
            <tr>
                <th>Empleado</th>
                <th>Sitio</th>
                <th style="text-align:center;">Periodo</th>
                <th style="text-align:center;">Días corresponden</th>
                <th style="text-align:center;">Días restantes</th>
                <th>Estado</th>
                <th style="text-align:center;">Editar</th>
                <th style="text-align:center;">Eliminar</th>
            </tr>
        </thead>
        <tbody id="tabla-saldos">
            @forelse($saldos as $saldo)
                <tr data-periodo="{{ $saldo->periodo }}" data-activo="{{ $saldo->empleado->activo ? '1' : '0' }}" data-sitio="{{ $saldo->empleado->sitio }}">
                    <td>
                        <span style="font-weight:600; color:#0f172a;">{{ $saldo->empleado->numero_empleado }}</span> - 
                        <span style="font-weight:500;">{{ $saldo->empleado->nombre }} {{ $saldo->empleado->apellido_paterno }} {{ $saldo->empleado->apellido_materno }}</span>
                    </td>
                    <td>
                        {{ $saldo->empleado->sitio ?: 'N/A' }}
                    </td>
                    <td style="text-align:center; font-weight:600;">
                        {{ $saldo->periodo }}
                    </td>
                    <td style="text-align:center; font-weight:600;">
                        <span style="color:#2563eb;">{{ $saldo->dias_corresponden }}</span>
                    </td>
                    <td style="text-align:center; font-weight:600;">
                        <span style="color:#16a34a;">{{ $saldo->dias_restantes }}</span>
                    </td>
                    <td>
                        <span class="badge-estado" style="padding:4px 8px; border-radius:6px; font-size:12px; font-weight:600; background:{{ $saldo->empleado->activo ? '#dcfce7' : '#f1f5f9' }}; color:{{ $saldo->empleado->activo ? '#166534' : '#64748b' }};">
                            {{ $saldo->empleado->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        @if(Auth::user()->esSoloLectura())
                            <span style="color: #94a3b8; font-weight: 600; cursor: not-allowed; opacity: 0.6;">
                                ✏️ Editar
                            </span>
                        @else
                            <a href="/vacaciones/editar/{{ $saldo->id }}" style="color: #2563eb; font-weight: 600; text-decoration: none;">
                                ✏️ Editar
                            </a>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if(Auth::user()->esSoloLectura())
                            <span style="color: #94a3b8; font-weight: 600; cursor: not-allowed; opacity: 0.6;">
                                🗑 Eliminar
                            </span>
                        @elseif(Auth::user()->esAdmin())
                            <a href="/vacaciones/eliminar/{{ $saldo->id }}" onclick="return confirm('¿Está seguro de que desea eliminar este saldo de vacaciones?')" style="color: #ef4444; font-weight: 600; text-decoration: none;">
                                🗑 Eliminar
                            </a>
                        @else
                            <span style="color: #cbd5e1; font-weight: 600; cursor: not-allowed; opacity: 0.6;">
                                🗑 Eliminar
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; color:#94a3b8; padding:30px;">
                        No hay saldos de vacaciones registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    const searchInput = document.getElementById('buscar-saldos');
    const autocompleteDiv = document.getElementById('buscar-saldos-autocomplete');
    const filterPeriodo = document.getElementById('filtro-periodo');
    const filterEstado = document.getElementById('filtro-estado');
    const filterSitio = document.getElementById('filtro-sitio');
    const rows = document.querySelectorAll('#tabla-saldos tr');

    // Recopilar nombres únicos de empleados en la tabla
    let uniqueEmployees = [];
    rows.forEach(function(row) {
        if (row.cells.length === 1 && row.cells[0].colSpan === 8) return;
        
        let nameText = row.cells[0].innerText.trim();
        if (nameText && !uniqueEmployees.includes(nameText)) {
            uniqueEmployees.push(nameText);
        }
    });

    function applyFilters() {
        const textQuery = searchInput.value.toLowerCase();
        const selectedPeriodo = filterPeriodo.value;
        const selectedEstado = filterEstado.value;
        const selectedSitio = filterSitio.value.toLowerCase();

        rows.forEach(function(row) {
            // Check if it's the empty row
            if (row.cells.length === 1 && row.cells[0].colSpan === 8) {
                return;
            }

            const rowText = row.innerText.toLowerCase();
            const rowPeriodo = row.getAttribute('data-periodo');
            const rowEstado = row.getAttribute('data-activo');
            const rowSitio = (row.getAttribute('data-sitio') || '').toLowerCase();

            const matchesText = rowText.includes(textQuery);
            const matchesPeriodo = selectedPeriodo === '' || rowPeriodo === selectedPeriodo;
            const matchesEstado = selectedEstado === '' || rowEstado === selectedEstado;
            const matchesSitio = selectedSitio === '' || rowSitio === selectedSitio;

            if (matchesText && matchesPeriodo && matchesEstado && matchesSitio) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function showAutocomplete() {
        const query = searchInput.value.toLowerCase().trim();
        autocompleteDiv.innerHTML = '';
        
        if (!query) {
            autocompleteDiv.style.display = 'none';
            return;
        }

        const matches = uniqueEmployees.filter(function(name) {
            return name.toLowerCase().includes(query);
        });

        if (matches.length > 0) {
            matches.forEach(function(name) {
                const item = document.createElement('div');
                item.style.padding = '10px 14px';
                item.style.cursor = 'pointer';
                item.style.fontSize = '13px';
                item.style.borderBottom = '1px solid #f1f5f9';
                item.style.color = '#334155';
                item.innerText = name;
                
                item.addEventListener('mouseenter', function() {
                    item.style.backgroundColor = '#f1f5f9';
                });
                item.addEventListener('mouseleave', function() {
                    item.style.backgroundColor = 'white';
                });
                
                item.addEventListener('click', function() {
                    searchInput.value = name;
                    autocompleteDiv.style.display = 'none';
                    applyFilters();
                });
                
                autocompleteDiv.appendChild(item);
            });
            autocompleteDiv.style.display = 'block';
        } else {
            autocompleteDiv.style.display = 'none';
        }
    }

    searchInput.addEventListener('keyup', function() {
        applyFilters();
        showAutocomplete();
    });
    
    // Ocultar al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (e.target !== searchInput && e.target !== autocompleteDiv) {
            autocompleteDiv.style.display = 'none';
        }
    });

    filterPeriodo.addEventListener('change', applyFilters);
    filterEstado.addEventListener('change', applyFilters);
    filterSitio.addEventListener('change', applyFilters);
</script>

@endsection