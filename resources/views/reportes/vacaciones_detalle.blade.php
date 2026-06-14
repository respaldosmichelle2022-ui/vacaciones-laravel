@extends('layouts.app')

@section('contenido')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
    <h1 style="margin: 0; font-weight: 700; color: #0f172a;">Reporte Detallado de Cálculo</h1>
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
    Desglose cronológico paso a paso (fecha a fecha) de consumos de vacaciones y saldo acumulado.
</p>

<!-- Formulario de Filtros Dinámicos -->
<div class="card no-print" style="margin-bottom: 30px; padding: 24px; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
    <form method="GET" action="/reportes/vacaciones-detalle" id="formFiltrosReporte">
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

<!-- Listado de Tarjetas Detalladas -->
<div id="listado-tarjetas">
    @forelse($reporte as $col)
        <div class="card tarjeta-desglose" data-sitio="{{ $col['sitio'] }}" data-nombre="{{ $col['nombre'] }} #{{ $col['numero_empleado'] }}" style="margin-bottom: 25px; padding: 24px; border: 1px solid #e2e8f0; border-radius: 16px; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.02); page-break-inside: avoid;">
            <!-- Encabezado de la Tarjeta -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 15px;">
                <div>
                    <h3 style="margin: 0 0 5px 0; font-size: 17px; font-weight: 700; color: #0f172a;">
                        #{{ $col['numero_empleado'] }} - {{ $col['nombre'] }}
                    </h3>
                    <div style="color: #64748b; font-size: 12px; font-weight: 500;">
                        Puesto: <strong style="color: #334155;">{{ $col['puesto'] }}</strong> | Sitio: <span style="background: #e2e8f0; color: #334155; padding: 2px 6px; border-radius: 4px; font-weight: 600;">{{ $col['sitio'] }}</span>
                    </div>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; display: block;">Periodo</span>
                    <strong style="font-size: 20px; color: #2563eb; font-weight: 800;">{{ $col['periodo'] }}</strong>
                </div>
            </div>

            <!-- Tabla de cálculo paso a paso -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; border: none; border-radius: 8px; overflow: hidden; margin-top: 5px;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <th style="padding: 10px 14px; text-align: left; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Operación</th>
                            <th style="padding: 10px 14px; text-align: left; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Fechas / Detalle</th>
                            <th style="padding: 10px 14px; text-align: center; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Días</th>
                            <th style="padding: 10px 14px; text-align: right; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Saldo Acumulado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($col['pasos'] as $paso)
                            <tr style="border-bottom: 1px solid #f1f5f9; background: white;">
                                <td style="padding: 10px 14px; font-size: 13.5px; font-weight: 600;">
                                    @if($paso['tipo'] === 'inicial')
                                        <span style="color: #2563eb; background: #eff6ff; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;">Asignación Inicial</span>
                                    @else
                                        <span style="color: #b45309; background: #fffbeb; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;">Deducción</span>
                                    @endif
                                </td>
                                <td style="padding: 10px 14px; font-size: 13.5px; color: #475569;">
                                    {{ $paso['detalle'] }}
                                    @if($paso['fecha'])
                                        <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">{{ $paso['fecha'] }}</div>
                                    @endif
                                </td>
                                <td style="padding: 10px 14px; font-size: 14px; text-align: center; font-weight: 700; color: {{ $paso['tipo'] === 'inicial' ? '#2563eb' : '#b45309' }}">
                                    {{ $paso['cambio'] }}
                                </td>
                                <td style="padding: 10px 14px; font-size: 14px; text-align: right; font-weight: 700; color: #1e293b;">
                                    {{ $paso['acumulado'] }} días
                                </td>
                            </tr>
                        @endforeach
                        <!-- Fila de Saldo Final -->
                        <tr style="background: #f8fafc; border-top: 1.5px solid #cbd5e1;">
                            <td colspan="2" style="padding: 12px 14px; font-size: 14px; font-weight: 700; color: #0f172a; text-transform: uppercase;">Saldo Final de Vacaciones</td>
                            <td style="padding: 12px 14px; text-align: center;"></td>
                            <td style="padding: 12px 14px; text-align: right; font-size: 15px; font-weight: 800; color: #166534; background: #f0fdf4;">
                                {{ $col['saldo_final'] }} días
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card" style="padding: 30px; text-align: center; color: #94a3b8;">
            No existen registros cargados para el periodo {{ $selectedPeriod }}.
        </div>
    @endforelse
</div>

<script>
    function exportarExcel() {
        const queryParams = new URLSearchParams(window.location.search);
        queryParams.set('periodo', '{{ $selectedPeriod }}');
        
        const sitio = document.getElementById('filtro-sitio').value;
        if (sitio) queryParams.set('sitio', sitio);
        
        const empleado = document.getElementById('buscar-empleado').value;
        if (empleado) {
            queryParams.set('empleado_id', document.getElementById('buscar-empleado').getAttribute('data-id') || '');
        }

        window.location.href = '/reportes/vacaciones-detalle/exportar?' + queryParams.toString();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('buscar-empleado');
        const autocompleteDiv = document.getElementById('buscar-empleado-autocomplete');
        const filterSitio = document.getElementById('filtro-sitio');
        const cards = document.querySelectorAll('#listado-tarjetas .tarjeta-desglose');

        // Colección de empleados
        let uniqueEmployees = [];
        cards.forEach(function(card) {
            let nameAttr = card.getAttribute('data-nombre');
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

            cards.forEach(function(card) {
                const cardSitio = (card.getAttribute('data-sitio') || '').toLowerCase().trim();
                const cardNombre = (card.getAttribute('data-nombre') || '').toLowerCase().trim();

                const matchesSitio = selectedSitio === '' || cardSitio === selectedSitio;
                const matchesNombre = textQuery === '' || cardNombre.includes(textQuery);

                if (matchesSitio && matchesNombre) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
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
