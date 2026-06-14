@extends('layouts.app')

@section('contenido')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
    <h1 style="margin: 0; font-weight: 700; color: #0f172a;">Reporte General de Vacaciones</h1>
    <div style="display: flex; gap: 10px;" class="no-print">
        <a href="#" onclick="exportarExcel()" class="boton" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);">
            <span>📥</span> Exportar Excel
        </a>
        <button onclick="window.print()" class="boton" style="background: #475569; padding: 10px 18px; font-size: 13.5px; box-shadow: 0 4px 14px rgba(71, 85, 105, 0.3);">
            <span>🖨️</span> Imprimir
        </button>
    </div>
</div>
<p style="color: #64748b; font-size: 14px; margin-bottom: 30px;">
    Resumen consolidado de vacaciones por colaborador, días correspondientes, consumidos y remanentes.
</p>

<!-- Formulario de Filtros Dinámicos -->
<div class="card no-print" style="margin-bottom: 30px; padding: 24px; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
    <form method="GET" action="/reportes/vacaciones-general" id="formFiltrosReporte">
        <h4 style="margin-bottom: 15px; color: #334155; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <span>🔍</span> Filtros del Reporte
        </h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <!-- Filtro por Sitio (JS Client-Side) -->
            <div class="grupo" style="margin-bottom: 0;">
                <label style="font-size: 12px; font-weight: 600; color: #64748b;">Sitio</label>
                <select id="filtro-sitio" style="padding: 10px; font-size: 13px;">
                    <option value="">-- Todos los Sitios --</option>
                    @foreach($sitios as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Búsqueda por Empleado (JS Client-Side Autocomplete) -->
            <div class="grupo" style="margin-bottom: 0; position: relative;">
                <label style="font-size: 12px; font-weight: 600; color: #64748b;">Buscar Empleado</label>
                <input type="text" id="buscar-empleado" placeholder="Escribe nombre o número..." autocomplete="off" style="padding: 10px; font-size: 13px; width: 100%; border: 1px solid #cbd5e1; border-radius: 10px; outline: none;">
                <div id="buscar-empleado-autocomplete" style="display: none; position: absolute; top: 100%; left: 0; width: 100%; background: white; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 999; max-height: 200px; overflow-y: auto; margin-top: 5px;"></div>
            </div>

            <!-- Filtro de Periodo (Backend reload) -->
            <div class="grupo" style="margin-bottom: 0;">
                <label style="font-size: 12px; font-weight: 600; color: #64748b;">Periodo (Año)</label>
                <select name="periodo" onchange="document.getElementById('formFiltrosReporte').submit()" style="padding: 10px; font-size: 13px;">
                    @foreach($periodos as $p)
                        <option value="{{ $p }}" {{ $selectedPeriod == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
</div>

<!-- Tabla General -->
<div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 12px; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
    <table style="width: 100%; border-collapse: collapse; margin-top: 0; border: none; border-radius: 0;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <th style="padding: 14px 18px; text-align: left; font-weight: 600; color: #475569; font-size: 12.5px; text-transform: uppercase;">Número</th>
                <th style="padding: 14px 18px; text-align: left; font-weight: 600; color: #475569; font-size: 12.5px; text-transform: uppercase;">Colaborador</th>
                <th style="padding: 14px 18px; text-align: left; font-weight: 600; color: #475569; font-size: 12.5px; text-transform: uppercase;">Puesto</th>
                <th style="padding: 14px 18px; text-align: left; font-weight: 600; color: #475569; font-size: 12.5px; text-transform: uppercase;">Sitio</th>
                <th style="padding: 14px 18px; text-align: center; font-weight: 600; color: #475569; font-size: 12.5px; text-transform: uppercase;">Periodo</th>
                <th style="padding: 14px 18px; text-align: center; font-weight: 600; color: #475569; font-size: 12.5px; text-transform: uppercase; background-color: rgba(37, 99, 235, 0.03);">Días Corresponden</th>
                <th style="padding: 14px 18px; text-align: center; font-weight: 600; color: #475569; font-size: 12.5px; text-transform: uppercase; background-color: rgba(245, 158, 11, 0.03);">Días Tomados</th>
                <th style="padding: 14px 18px; text-align: center; font-weight: 600; color: #475569; font-size: 12.5px; text-transform: uppercase; background-color: rgba(16, 185, 129, 0.03);">Días Pendientes</th>
            </tr>
        </thead>
        <tbody id="tabla-general-cuerpo">
            @forelse($reporte as $col)
                <tr data-sitio="{{ $col['sitio'] }}" data-nombre="{{ $col['nombre'] }} #{{ $col['numero_empleado'] }}" style="border-bottom: 1px solid #e2e8f0; transition: background 0.15s ease;">
                    <td style="padding: 14px 18px; font-weight: 600; color: #64748b;">#{{ $col['numero_empleado'] }}</td>
                    <td style="padding: 14px 18px; font-weight: 600; color: #0f172a;">{{ $col['nombre'] }}</td>
                    <td style="padding: 14px 18px; color: #475569;">{{ $col['puesto'] }}</td>
                    <td style="padding: 14px 18px; color: #475569;"><span style="background: #e2e8f0; color: #334155; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;">{{ $col['sitio'] }}</span></td>
                    <td style="padding: 14px 18px; text-align: center; font-weight: 600; color: #475569;">{{ $col['periodo'] }}</td>
                    <td style="padding: 14px 18px; text-align: center; font-weight: 700; color: #2563eb; background-color: rgba(37, 99, 235, 0.01);">{{ $col['dias_corresponden'] }}</td>
                    <td style="padding: 14px 18px; text-align: center; font-weight: 700; color: #d97706; background-color: rgba(245, 158, 11, 0.01);">{{ $col['dias_tomados'] }}</td>
                    <td style="padding: 14px 18px; text-align: center; font-weight: 700; color: #15803d; background-color: rgba(16, 185, 129, 0.01);">{{ $col['dias_pendientes'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding: 30px; text-align: center; color: #94a3b8;">
                        No existen saldos cargados para el periodo {{ $selectedPeriod }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    function exportarExcel() {
        const queryParams = new URLSearchParams(window.location.search);
        queryParams.set('periodo', '{{ $selectedPeriod }}');
        
        const sitio = document.getElementById('filtro-sitio').value;
        if (sitio) queryParams.set('sitio', sitio);
        
        const empleado = document.getElementById('buscar-empleado').value;
        if (empleado) {
            // Si el autocomplete tiene un filtro, pasarlo
            queryParams.set('empleado_id', document.getElementById('buscar-empleado').getAttribute('data-id') || '');
        }

        window.location.href = '/reportes/vacaciones-general/exportar?' + queryParams.toString();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('buscar-empleado');
        const autocompleteDiv = document.getElementById('buscar-empleado-autocomplete');
        const filterSitio = document.getElementById('filtro-sitio');
        const rows = document.querySelectorAll('#tabla-general-cuerpo tr');

        // Colección de empleados de la tabla
        let uniqueEmployees = [];
        rows.forEach(function(row) {
            if (row.cells.length === 1) return;
            let nameAttr = row.getAttribute('data-nombre');
            if (nameAttr) {
                let cleanName = nameAttr.split('#')[0].trim();
                let number = nameAttr.split('#')[1] || '';
                if (!uniqueEmployees.some(e => e.name === cleanName)) {
                    uniqueEmployees.push({ name: cleanName, number: number });
                }
            }
        });

        function applyFilters() {
            const selectedSitio = filterSitio.value.toLowerCase().trim();
            const textQuery = searchInput.value.toLowerCase().trim();

            rows.forEach(function(row) {
                if (row.cells.length === 1) return;

                const rowSitio = (row.getAttribute('data-sitio') || '').toLowerCase().trim();
                const rowNombre = (row.getAttribute('data-nombre') || '').toLowerCase().trim();

                const matchesSitio = selectedSitio === '' || rowSitio === selectedSitio;
                const matchesNombre = textQuery === '' || rowNombre.includes(textQuery);

                if (matchesSitio && matchesNombre) {
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

            const matches = uniqueEmployees.filter(function(e) {
                return e.name.toLowerCase().includes(query) || e.number.includes(query);
            });

            if (matches.length > 0) {
                matches.forEach(function(e) {
                    const item = document.createElement('div');
                    item.style.padding = '10px 14px';
                    item.style.cursor = 'pointer';
                    item.style.fontSize = '13px';
                    item.style.borderBottom = '1px solid #f1f5f9';
                    item.style.color = '#334155';
                    item.innerText = e.name + ' (#' + e.number + ')';
                    
                    item.addEventListener('mouseenter', function() {
                        item.style.backgroundColor = '#f1f5f9';
                    });
                    item.addEventListener('mouseleave', function() {
                        item.style.backgroundColor = 'white';
                    });
                    
                    item.addEventListener('click', function() {
                        searchInput.value = e.name;
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

        document.addEventListener('click', function(evt) {
            if (evt.target !== searchInput && evt.target !== autocompleteDiv) {
                autocompleteDiv.style.display = 'none';
            }
        });

        filterSitio.addEventListener('change', function() {
            applyFilters();
        });
    });
</script>

@endsection
