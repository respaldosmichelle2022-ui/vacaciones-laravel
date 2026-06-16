@extends('layouts.app')

@section('contenido')

@php
    $nombresMeses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];

    // Cálculos Globales Iniciales (Servidor)
    $totalFaltas = 0;
    $totalRetardos = 0;
    $totalPermisos = 0;
    $totalIncapacidades = 0;
    $totalGlobalConceptos = 0;
    
    foreach($reporte as $item) {
        $totalFaltas += $item['faltas'];
        $totalRetardos += $item['retardos'];
        $totalPermisos += $item['permisos'];
        $totalIncapacidades += $item['incapacidades'];
        $totalGlobalConceptos += $item['total_global'];
    }

    $promedioScore = $reporte->count() > 0 ? round($reporte->avg('desempeno'), 1) : 100;
    
    $tasaGlobalFaltas = $diasTotales > 0 ? round(($totalFaltas / $diasTotales) * 100, 2) : 0;
    $tasaGlobalRetardos = $diasTotales > 0 ? round(($totalRetardos / $diasTotales) * 100, 2) : 0;
    $tasaGlobalPermisos = $diasTotales > 0 ? round(($totalPermisos / $diasTotales) * 100, 2) : 0;
    $tasaGlobalIncapacidad = $diasTotales > 0 ? round(($totalIncapacidades / $diasTotales) * 100, 2) : 0;
    $tasaGlobalTotal = $diasTotales > 0 ? round(($totalGlobalConceptos / $diasTotales) * 100, 2) : 0;
@endphp

<h1 style="margin-bottom: 10px; font-weight: 700; color: #0f172a;">Reporte de Desempeño y Asistencia</h1>
<p style="color: #64748b; font-size: 14px; margin-bottom: 30px;">
    Análisis detallado de índices de asistencia, retardos, permisos, incapacidades y penalizaciones calculadas.
</p>

<!-- Formulario de Filtros Dinámicos -->
<div class="card no-print" style="margin-bottom: 30px; padding: 24px; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
    <form method="GET" action="/incidencias/reporte" id="formFiltrosReporte">
        <h4 style="margin-bottom: 15px; color: #334155; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <span>🔍</span> Filtros del Reporte
        </h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
            <!-- Filtro Dinámico por Sitio (JS) -->
            <div class="grupo" style="margin-bottom: 0;">
                <label style="font-size: 12px; font-weight: 600; color: #64748b;">Sitio</label>
                <select id="filtro-sitio" name="sitio" style="padding: 10px; font-size: 13px;">
                    <option value="">-- Todos los Sitios --</option>
                    @foreach($sitios as $s)
                        <option value="{{ $s }}" {{ request('sitio') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Búsqueda Dinámica por Empleado (JS Autocomplete) -->
            <div class="grupo" style="margin-bottom: 0; position: relative;">
                <label style="font-size: 12px; font-weight: 600; color: #64748b;">Buscar Empleado</label>
                <input type="text" id="buscar-empleado" name="buscar_empleado" value="{{ request('buscar_empleado') }}" placeholder="Escribe nombre o número..." autocomplete="off" style="padding: 10px; font-size: 13px; width: 100%; border: 1px solid #cbd5e1; border-radius: 10px; outline: none;">
                <div id="buscar-empleado-autocomplete" style="display: none; position: absolute; top: 100%; left: 0; width: 100%; background: white; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 999; max-height: 200px; overflow-y: auto; margin-top: 5px;"></div>
            </div>

            <!-- Filtro por Año (Backend) -->
            <div class="grupo" style="margin-bottom: 0;">
                <label style="font-size: 12px; font-weight: 600; color: #64748b;">Año</label>
                <select name="anio" onchange="document.getElementById('formFiltrosReporte').submit()" style="padding: 10px; font-size: 13px;">
                    @for($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- Filtro de Selección de Meses (Backend) -->
        <div class="grupo" style="margin-bottom: 15px;">
            <label style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px; display: block;">Meses del Reporte (Selecciona uno o varios)</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px; padding: 10px; background: #f8fafc; border-radius: 8px; border: 1px solid #cbd5e1;">
                @foreach($nombresMeses as $num => $nombre)
                    <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 500; color: #cbd5e1; cursor: pointer;">
                        <input type="checkbox" name="meses[]" value="{{ $num }}" 
                            {{ in_array($num, $selectedMonths) ? 'checked' : '' }}
                            onchange="document.getElementById('formFiltrosReporte').submit()"
                            style="width: auto; cursor: pointer;">
                        {{ $nombre }}
                    </label>
                @endforeach
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 15px;">
            <span style="font-size: 12px; color: #64748b; font-weight: 600;">
                📅 Periodo: <strong style="color: #0f172a;">{{ $diasTotales }} días totales</strong>
            </span>
            <div style="display: flex; gap: 10px;">
                <a href="/incidencias/reporte" class="boton-volver" style="margin: 0; padding: 8px 16px; font-size: 13px;">Limpiar Filtros</a>
                <button type="submit" class="boton" style="padding: 8px 16px; font-size: 13px;">Actualizar Periodo</button>
            </div>
        </div>
    </form>
</div>

<!-- Importar Chart.js para visualizaciones dinámicas -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Resumen Estadístico Cards -->
<h4 style="margin-bottom: 15px; font-weight: 600; color: #334155;">Resumen General del Periodo</h4>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <!-- Desempeño Promedio -->
    <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 5px solid #2563eb; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 15px rgba(0,0,0,0.05)';" onmouseleave="this.style.transform='none'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.02)';">
        <div>
            <span style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase;">Desempeño Promedio</span>
            <h2 id="promedio-desempeno-val" style="font-size: 28px; font-weight: 700; color: #2563eb; margin-top: 5px;">{{ $promedioScore }}%</h2>
        </div>
        <span style="font-size: 11px; color: #94a3b8; margin-top: 8px;">(Incidencias Totales)</span>
    </div>

    <!-- Tasa de Faltas -->
    <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 5px solid #ef4444; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 15px rgba(0,0,0,0.05)';" onmouseleave="this.style.transform='none'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.02)';">
        <div>
            <span style="font-size: 11px; color: #ef4444; font-weight: 700; text-transform: uppercase;">Promedio de Faltas</span>
            <h2 id="tasa-faltas-val" style="font-size: 28px; font-weight: 700; color: #ef4444; margin-top: 5px;">{{ $tasaGlobalFaltas }}%</h2>
        </div>
        <span style="font-size: 11px; color: #64748b; margin-top: 8px;">Total: <strong id="total-faltas-val">{{ $totalFaltas }}</strong> Faltas</span>
    </div>

    <!-- Tasa de Retardos -->
    <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 5px solid #f59e0b; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 15px rgba(0,0,0,0.05)';" onmouseleave="this.style.transform='none'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.02)';">
        <div>
            <span style="font-size: 11px; color: #f59e0b; font-weight: 700; text-transform: uppercase;">Promedio de Retardos</span>
            <h2 id="tasa-retardos-val" style="font-size: 28px; font-weight: 700; color: #f59e0b; margin-top: 5px;">{{ $tasaGlobalRetardos }}%</h2>
        </div>
        <span style="font-size: 11px; color: #64748b; margin-top: 8px;">Total: <strong id="total-retardos-val">{{ $totalRetardos }}</strong> Retardos</span>
    </div>

    <!-- Tasa de Permisos -->
    <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 5px solid #10b981; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 15px rgba(0,0,0,0.05)';" onmouseleave="this.style.transform='none'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.02)';">
        <div>
            <span style="font-size: 11px; color: #10b981; font-weight: 700; text-transform: uppercase;">Promedio de Permisos</span>
            <h2 id="tasa-permisos-val" style="font-size: 28px; font-weight: 700; color: #10b981; margin-top: 5px;">{{ $tasaGlobalPermisos }}%</h2>
        </div>
        <span style="font-size: 11px; color: #64748b; margin-top: 8px;">Total: <strong id="total-permisos-val">{{ $totalPermisos }}</strong> Permisos</span>
    </div>

    <!-- Tasa de Incapacidades -->
    <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 5px solid #6366f1; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 15px rgba(0,0,0,0.05)';" onmouseleave="this.style.transform='none'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.02)';">
        <div>
            <span style="font-size: 11px; color: #6366f1; font-weight: 700; text-transform: uppercase;">Promedio de Incapacidad</span>
            <h2 id="tasa-incapacidades-val" style="font-size: 28px; font-weight: 700; color: #6366f1; margin-top: 5px;">{{ $tasaGlobalIncapacidad }}%</h2>
        </div>
        <span style="font-size: 11px; color: #64748b; margin-top: 8px;">Total: <strong id="total-incapacidades-val">{{ $totalIncapacidades }}</strong> Días</span>
    </div>

    <!-- Tasa Global -->
    <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 5px solid #0f172a; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 15px rgba(0,0,0,0.05)';" onmouseleave="this.style.transform='none'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.02)';">
        <div>
            <span style="font-size: 11px; color: #0f172a; font-weight: 700; text-transform: uppercase;">Tasa Global de Incidencias</span>
            <h2 id="tasa-global-val" style="font-size: 28px; font-weight: 700; color: #0f172a; margin-top: 5px;">{{ $tasaGlobalTotal }}%</h2>
        </div>
        <span style="font-size: 11px; color: #64748b; margin-top: 8px;">Conceptos Totales: <strong id="total-global-val">{{ $totalGlobalConceptos }}</strong></span>
    </div>
</div>

<!-- Contenedor del Gráfico Dinámico -->
<div class="card no-print" style="margin-bottom: 35px; padding: 24px; background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
    <h4 style="margin-bottom: 20px; font-weight: 600; color: #334155; display: flex; align-items: center; gap: 8px;">
        <span>📊</span> Distribución de Incidencias por Empleado
    </h4>
    <div style="position: relative; height: 320px; width: 100%;">
        <canvas id="incidenciasChart"></canvas>
    </div>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h3 style="font-weight: 600; color: #0f172a; margin: 0;">Clasificación de Desempeño y Tasas del Periodo</h3>
    <button onclick="window.print()" class="boton no-print" style="background: #475569; padding: 10px 18px; font-size: 13px;">
        <span>🖨️</span> Imprimir Reporte
    </button>
</div>

<div style="overflow-x: auto;">
    <table>
        <thead>
            <tr>
                <th>Empleado / Puesto</th>
                <th style="text-align: center;">Faltas (Tasa)</th>
                <th style="text-align: center;">Retardos (Tasa)</th>
                <th style="text-align: center;">Permisos (Tasa)</th>
                <th style="text-align: center;">Incapacidad (Tasa)</th>
                <th style="text-align: center;">Tasa Global</th>
                <th style="text-align: center; width: 170px;">Índice Desempeño</th>
                <th style="text-align: right;">Estatus</th>
            </tr>
        </thead>
        <tbody id="tabla-reporte-cuerpo">
            @forelse($reporte as $item)
                @php
                    $emp = $item['empleado'];
                    $score = $item['desempeno'];
                    
                    // Lógica de Estatus Mejorada por Rangos:
                    // Excelente: >= 95
                    // Bueno: 85 - 94.9
                    // Regular: 70 - 84.9
                    // Deficiente: < 70
                    $color = '#10b981'; // verde para Excelente
                    $badgeText = 'Excelente';
                    $badgeStyle = 'background: #dcfce7; color: #166534;';
                    
                    if ($score < 70) {
                        $color = '#ef4444'; // rojo
                        $badgeText = 'Deficiente';
                        $badgeStyle = 'background: #fee2e2; color: #991b1b;';
                    } elseif ($score < 85) {
                        $color = '#f59e0b'; // naranja
                        $badgeText = 'Regular';
                        $badgeStyle = 'background: #fef3c7; color: #92400e;';
                    } elseif ($score < 95) {
                        $color = '#3b82f6'; // azul
                        $badgeText = 'Bueno';
                        $badgeStyle = 'background: #dbeafe; color: #1e40af;';
                    }
                @endphp
                <tr data-sitio="{{ $emp->sitio }}" 
                    data-nombre="{{ $emp->nombre }} {{ $emp->apellido_paterno }} {{ $emp->apellido_materno }} #{{ $emp->numero_empleado }}"
                    data-faltas="{{ $item['faltas'] }}"
                    data-retardos="{{ $item['retardos'] }}"
                    data-permisos="{{ $item['permisos'] }}"
                    data-incapacidades="{{ $item['incapacidades'] }}"
                    data-total-global="{{ $item['total_global'] }}"
                    data-desempeno="{{ $score }}">
                    <td style="font-weight: 600;">
                        <span style="color: #0f172a;">#{{ $emp->numero_empleado }} - {{ $emp->nombre }} {{ $emp->apellido_paterno }} {{ $emp->apellido_materno }}</span>
                        <div style="color: #94a3b8; font-size: 11px; font-weight: 400; margin-top: 3px;">
                            {{ $emp->puesto ?? 'Sin puesto' }} | <span style="font-weight: 500;">{{ $emp->sitio ?: 'N/A' }}</span>
                        </div>
                    </td>
                    <!-- Faltas -->
                    <td style="text-align: center;">
                        <strong style="color: {{ $item['faltas'] > 0 ? '#ef4444' : '#94a3b8' }};">{{ $item['faltas'] }}</strong>
                        <div style="font-size: 11px; color: #94a3b8;">{{ $item['promedio_faltas'] }}%</div>
                    </td>
                    <!-- Retardos -->
                    <td style="text-align: center;">
                        <strong style="color: {{ $item['retardos'] > 0 ? '#f59e0b' : '#94a3b8' }};">{{ $item['retardos'] }}</strong>
                        <div style="font-size: 11px; color: #94a3b8;">{{ $item['promedio_retardos'] }}%</div>
                    </td>
                    <!-- Permisos -->
                    <td style="text-align: center;">
                        <strong style="color: #94a3b8;">{{ $item['permisos'] }}</strong>
                        <div style="font-size: 11px; color: #94a3b8;">{{ $item['promedio_incapacidad'] }}%</div>
                    </td>
                    <!-- Incapacidades -->
                    <td style="text-align: center;">
                        <strong style="color: #94a3b8;">{{ $item['incapacidades'] }}</strong>
                        <div style="font-size: 11px; color: #94a3b8;">{{ $item['promedio_incapacidad'] }}%</div>
                    </td>
                    <!-- Tasa Global -->
                    <td style="text-align: center; font-weight: 700; color: #60a5fa;">
                        {{ $item['total_global'] }}
                        <div style="font-size: 11px; color: #94a3b8; font-weight: 500;">{{ $item['promedio_global'] }}%</div>
                    </td>
                    <!-- Desempeño -->
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="background: #334155; border-radius: 10px; height: 6px; flex-grow: 1; overflow: hidden;">
                                <div style="background: {{ $color }}; width: {{ $score }}%; height: 100%; border-radius: 10px;"></div>
                            </div>
                            <span style="font-weight: 700; font-size: 12px; color: #cbd5e1; width: 35px; text-align: right;">{{ $score }}%</span>
                        </div>
                    </td>
                    <td style="text-align: right;">
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; {{ $badgeStyle }}">
                            {{ $badgeText }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #94a3b8; padding: 30px;">
                        No se encontraron registros para los filtros seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('buscar-empleado');
        const autocompleteDiv = document.getElementById('buscar-empleado-autocomplete');
        const filterSitio = document.getElementById('filtro-sitio');
        const rows = document.querySelectorAll('#tabla-reporte-cuerpo tr');
        const diasTotales = {{ $diasTotales }};

        // Instancia del gráfico
        let chartInstance = null;

        function wrapLabel(name) {
            const words = name.split(' ');
            if (words.length <= 2) {
                return name;
            }
            const mid = Math.ceil(words.length / 2);
            return [
                words.slice(0, mid).join(' '),
                words.slice(mid).join(' ')
            ];
        }

        function updateChart(chartData) {
            const ctx = document.getElementById('incidenciasChart').getContext('2d');
            if (chartInstance) {
                chartInstance.destroy();
            }

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Faltas',
                            data: chartData.faltas,
                            backgroundColor: 'rgba(239, 68, 68, 0.7)',
                            borderColor: '#ef4444',
                            borderWidth: 1.5,
                            borderRadius: 4
                        },
                        {
                            label: 'Retardos',
                            data: chartData.retardos,
                            backgroundColor: 'rgba(245, 158, 11, 0.7)',
                            borderColor: '#f59e0b',
                            borderWidth: 1.5,
                            borderRadius: 4
                        },
                        {
                            label: 'Permisos',
                            data: chartData.permisos,
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            borderColor: '#10b981',
                            borderWidth: 1.5,
                            borderRadius: 4
                        },
                        {
                            label: 'Incapacidades',
                            data: chartData.incapacidades,
                            backgroundColor: 'rgba(99, 102, 241, 0.7)',
                            borderColor: '#6366f1',
                            borderWidth: 1.5,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                color: '#334155',
                                font: {
                                    family: 'Outfit',
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            titleColor: '#ffffff',
                            bodyColor: '#e2e8f0',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            padding: 10,
                            boxPadding: 4,
                            usePointStyle: true,
                            callbacks: {
                                title: function(context) {
                                    const label = context[0].label;
                                    if (Array.isArray(label)) {
                                        return label.join(' ');
                                    }
                                    return label;
                                },
                                label: function(context) {
                                    return ` ${context.dataset.label}: ${context.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#475569',
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 0,
                                font: {
                                    family: 'Outfit',
                                    size: 9,
                                    weight: '500'
                                }
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                color: '#475569',
                                font: {
                                    family: 'Outfit'
                                },
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // Recopilar nombres únicos de empleados de la tabla
        let uniqueEmployees = [];
        rows.forEach(function(row) {
            if (row.cells.length === 1 && row.cells[0].colSpan === 8) return;
            let nameText = row.getAttribute('data-nombre');
            if (nameText) {
                // Limpiar el formato para el autocomplete ("Nombre Apellido #Numero")
                let cleanName = nameText.split('#')[0].trim();
                if (cleanName && !uniqueEmployees.includes(cleanName)) {
                    uniqueEmployees.push(cleanName);
                }
            }
        });

        function applyFilters() {
            const selectedSitio = filterSitio.value.toLowerCase().trim();
            const textQuery = searchInput.value.toLowerCase().trim();

            let totalFaltas = 0;
            let totalRetardos = 0;
            let totalPermisos = 0;
            let totalIncapacidades = 0;
            let totalGlobal = 0;
            let sumDesempeno = 0;
            let visibleCount = 0;

            let chartData = {
                labels: [],
                faltas: [],
                retardos: [],
                permisos: [],
                incapacidades: []
            };

            rows.forEach(function(row) {
                if (row.cells.length === 1 && row.cells[0].colSpan === 8) return;

                const rowSitio = (row.getAttribute('data-sitio') || '').toLowerCase().trim();
                const rowNombre = (row.getAttribute('data-nombre') || '').toLowerCase().trim();
                const rowFaltas = parseInt(row.getAttribute('data-faltas')) || 0;
                const rowRetardos = parseInt(row.getAttribute('data-retardos')) || 0;
                const rowPermisos = parseInt(row.getAttribute('data-permisos')) || 0;
                const rowIncapacidades = parseInt(row.getAttribute('data-incapacidades')) || 0;
                const rowTotalGlobal = parseInt(row.getAttribute('data-total-global')) || 0;
                const rowDesempeno = parseFloat(row.getAttribute('data-desempeno')) || 0;

                const matchesSitio = selectedSitio === '' || rowSitio === selectedSitio;
                const matchesNombre = textQuery === '' || rowNombre.includes(textQuery);

                if (matchesSitio && matchesNombre) {
                    row.style.display = '';
                    totalFaltas += rowFaltas;
                    totalRetardos += rowRetardos;
                    totalPermisos += rowPermisos;
                    totalIncapacidades += rowIncapacidades;
                    totalGlobal += rowTotalGlobal;
                    sumDesempeno += rowDesempeno;
                    visibleCount++;

                    // Obtener nombre completo de los datos originales
                    const rawNombre = row.getAttribute('data-nombre') || '';
                    const fullName = rawNombre.split('#')[0].trim();
                    chartData.labels.push(wrapLabel(fullName));
                    chartData.faltas.push(rowFaltas);
                    chartData.retardos.push(rowRetardos);
                    chartData.permisos.push(rowPermisos);
                    chartData.incapacidades.push(rowIncapacidades);
                } else {
                    row.style.display = 'none';
                }
            });

            // Recalcular estadísticas globales del periodo visible
            const avgDesempeno = visibleCount > 0 ? (sumDesempeno / visibleCount).toFixed(1) : '100.0';
            const tasaFaltas = diasTotales > 0 ? ((totalFaltas / diasTotales) * 100).toFixed(2) : '0.00';
            const tasaRetardos = diasTotales > 0 ? ((totalRetardos / diasTotales) * 100).toFixed(2) : '0.00';
            const tasaPermisos = diasTotales > 0 ? ((totalPermisos / diasTotales) * 100).toFixed(2) : '0.00';
            const tasaIncapacidades = diasTotales > 0 ? ((totalIncapacidades / diasTotales) * 100).toFixed(2) : '0.00';
            const tasaGlobalVal = diasTotales > 0 ? ((totalGlobal / diasTotales) * 100).toFixed(2) : '0.00';

            // Actualizar tarjetas en tiempo real
            document.getElementById('promedio-desempeno-val').innerText = avgDesempeno + '%';
            document.getElementById('total-faltas-val').innerText = totalFaltas;
            document.getElementById('tasa-faltas-val').innerText = tasaFaltas + '%';
            document.getElementById('total-retardos-val').innerText = totalRetardos;
            document.getElementById('tasa-retardos-val').innerText = tasaRetardos + '%';
            document.getElementById('total-permisos-val').innerText = totalPermisos;
            document.getElementById('tasa-permisos-val').innerText = tasaPermisos + '%';
            document.getElementById('total-incapacidades-val').innerText = totalIncapacidades;
            document.getElementById('tasa-incapacidades-val').innerText = tasaIncapacidades + '%';
            document.getElementById('total-global-val').innerText = totalGlobal;
            document.getElementById('tasa-global-val').innerText = tasaGlobalVal + '%';

            // Actualizar gráfico dinámicamente
            updateChart(chartData);
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

        // Event Listeners
        searchInput.addEventListener('keyup', function() {
            applyFilters();
            showAutocomplete();
        });

        // Ocultar autocompletar al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (e.target !== searchInput && e.target !== autocompleteDiv) {
                autocompleteDiv.style.display = 'none';
            }
        });

        filterSitio.addEventListener('change', function() {
            applyFilters();
        });

        // Inicializar el gráfico al cargar por primera vez
        applyFilters();
    });
</script>

@endsection
