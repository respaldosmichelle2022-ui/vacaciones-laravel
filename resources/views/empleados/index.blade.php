@extends('layouts.app')

@section('contenido')

<style>
    .titulo {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .titulo h1 {
        font-size: 36px;
        font-weight: 700;
        color: #0f172a;
    }

    .boton-desactivar {
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        color: white;
        cursor: pointer;
        font-weight: bold;
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .activo {
        background: #dc2626;
    }

    .activo:hover {
        background: #b91c1c;
    }

    .inactivo {
        background: #16a34a;
    }

    .inactivo:hover {
        background: #15803d;
    }

    /* Actions Bar */
    .barra-acciones {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .seccion-eliminar-sitio {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-danger-outline {
        background: transparent;
        color: #ef4444;
        border: 1.5px solid #ef4444;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .btn-danger-outline:hover {
        background: #fee2e2;
    }

    .btn-danger-solid {
        background: #ef4444;
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25);
    }

    .btn-danger-solid:hover {
        background: #dc2626;
        transform: translateY(-1px);
    }

    .btn-danger-solid:disabled {
        background: #cbd5e1;
        box-shadow: none;
        cursor: not-allowed;
        color: #94a3b8;
    }

    /* Estilo del formulario de importar Excel */
    .import-container {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #f1f5f9;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        transition: all 0.2s ease;
    }
    .import-container:hover {
        border-color: #3b82f6;
        background: #e2e8f0;
    }
    .import-btn-label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 0;
    }
    .import-file-input {
        display: none;
    }
    .btn-import-submit {
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(59, 130, 246, 0.2);
    }
    .btn-import-submit:hover {
        background: #2563eb;
    }

    /* Animation: Warehouse (Almacen) */
    @keyframes boxLift {
        0%, 100% { transform: translateY(3px) rotate(0deg); }
        50% { transform: translateY(-14px) rotate(-4deg); }
    }
    @keyframes bodySway {
        0%, 100% { transform: rotate(0deg) scale(1); }
        50% { transform: rotate(3deg) scale(1.02); }
    }

    /* Animation: Office (Oficina) */
    @keyframes headBob {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-4px) rotate(4deg); }
    }
    @keyframes typingFast {
        0% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
        100% { transform: translateY(2px); }
    }
    @keyframes chartScroll {
        0% { stroke-dashoffset: 30; }
        100% { stroke-dashoffset: 0; }
    }
    @keyframes steamPremium {
        0% { transform: translateY(0) scale(0.7); opacity: 0; }
        40% { opacity: 0.8; }
        100% { transform: translateY(-18px) scale(1.4); opacity: 0; }
    }

    /* Animation: Retail (Comercio) */
    @keyframes womanWalk {
        0%, 100% {
            transform: translateY(0) rotate(0deg);
        }
        25% {
            transform: translateY(-5px) rotate(1.5deg);
        }
        50% {
            transform: translateY(0) rotate(0deg);
        }
        75% {
            transform: translateY(-5px) rotate(-1.5deg);
        }
    }

    .woman-container {
        width: 75px;
        height: 75px;
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
        display: flex;
        justify-content: center;
        align-items: flex-end;
        flex-shrink: 0;
    }

    .woman-img {
        height: 95%;
        object-fit: contain;
        animation: womanWalk 1.6s ease-in-out infinite;
        transform-origin: bottom center;
    }

    /* Operational Cards Layout */
    .operational-panel {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .operational-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.01);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .operational-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.04);
    }
    .operational-card-text h4 {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .operational-card-text p {
        font-size: 11.5px;
        color: #64748b;
        margin: 0;
        line-height: 1.4;
    }
</style>

@if(session('import_warnings'))
    <div style="background: #fffbeb; border: 1px solid #fef3c7; border-left: 5px solid #d97706; padding: 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(217, 119, 6, 0.05);">
        <h4 style="color: #b45309; font-weight: 700; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
            <span>⚠️</span> Detalle de Omisiones / Alertas en la Carga Excel
        </h4>
        <div style="max-height: 200px; overflow-y: auto; padding-right: 10px;">
            <ul style="margin: 0; padding-left: 20px; color: #78350f; font-size: 13px; line-height: 1.6; list-style-type: disc;">
                @foreach(session('import_warnings') as $warning)
                    <li style="margin-bottom: 6px;">{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="titulo">
    <h1>Colaboradores</h1>
    <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
        <!-- Formulario Importación Excel -->
        @if(Auth::user()->esAdmin())
        <form action="/empleados/importar" method="POST" enctype="multipart/form-data" style="margin: 0; display: inline-block;" id="formImportarExcel">
            @csrf
            <div class="import-container">
                <label class="import-btn-label">
                    <span>📁</span> 
                    <span id="excelFileName">Importar Excel</span>
                    <input type="file" name="archivo_excel" accept=".xlsx,.xls" class="import-file-input" onchange="displaySelectedFileName(this)">
                </label>
                <button type="submit" id="btnSubmitImport" class="btn-import-submit" style="display: none;">Subir</button>
            </div>
        </form>
        @endif

        <a href="/empleados/exportar/excel" class="boton" style="background: #16a34a; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);">
            <span>📥</span> Exportar Excel
        </a>
        <button onclick="window.print()" class="boton" style="background: #475569; box-shadow: 0 4px 12px rgba(71, 85, 105, 0.2);">
            <span>🖨️</span> Exportar PDF
        </button>
        @if(Auth::user()->esSoloLectura())
        <button class="boton" style="background: #94a3b8; cursor: not-allowed; box-shadow: none;" disabled>
            <span>+</span> Agregar Empleado
        </button>
        @else
        <a href="/empleados/crear" class="boton">
            <span>+</span> Agregar Empleado
        </a>
        @endif
    </div>
</div>

<!-- Panel de Áreas Operativas y Funciones -->
<div class="operational-panel no-print">
    <!-- Card 1: Almacén -->
    <div class="operational-card">
        <svg viewBox="0 0 100 100" style="width: 75px; height: 75px; flex-shrink: 0;">
            <!-- Background Warehouse Shelves -->
            <rect x="5" y="15" width="22" height="70" fill="#e2e8f0" rx="1" />
            <line x1="5" y1="35" x2="27" y2="35" stroke="#cbd5e1" stroke-width="2" />
            <line x1="5" y1="60" x2="27" y2="60" stroke="#cbd5e1" stroke-width="2" />
            <rect x="8" y="22" width="16" height="12" fill="#d97706" rx="1" opacity="0.7" />
            <rect x="8" y="47" width="16" height="12" fill="#f59e0b" rx="1" opacity="0.7" />
            
            <!-- Worker Character -->
            <g style="animation: bodySway 2.5s ease-in-out infinite;">
                <!-- Helmet & Head -->
                <circle cx="55" cy="36" r="10" fill="#f59e0b" />
                <rect x="48" y="24" width="14" height="5" fill="#eab308" rx="2" />
                <path d="M47 38 C47 38, 55 42, 63 38" fill="none" stroke="#475569" stroke-width="2" />
                <!-- Torso -->
                <path d="M40 50 C40 45, 70 45, 70 50 L68 85 H42 Z" fill="#2563eb" />
            </g>
            
            <!-- Lifting Box (Lifting up and down significantly) -->
            <g style="animation: boxLift 2s ease-in-out infinite; transform-origin: center;">
                <!-- Arms holding box -->
                <path d="M44 58 L32 68 L42 78" fill="none" stroke="#f59e0b" stroke-width="3.5" stroke-linecap="round" />
                <path d="M66 58 L78 68 L68 78" fill="none" stroke="#f59e0b" stroke-width="3.5" stroke-linecap="round" />
                <!-- Cardboard Box -->
                <rect x="34" y="65" width="32" height="22" fill="#b45309" rx="2" />
                <line x1="34" y1="76" x2="66" y2="76" stroke="#92400e" stroke-width="2" />
                <!-- Tape accent -->
                <rect x="46" y="65" width="8" height="22" fill="#ef4444" opacity="0.8" />
            </g>
        </svg>
        <div class="operational-card-text">
            <h4>Logística y Almacén</h4>
            <p>Control de inventario, recepción de insumos y preparación de paquetes.</p>
        </div>
    </div>

    <!-- Card 2: Oficina -->
    <div class="operational-card">
        <svg viewBox="0 0 100 100" style="width: 75px; height: 75px; flex-shrink: 0;">
            <!-- Desk and Lamp -->
            <rect x="10" y="70" width="80" height="6" fill="#475569" rx="1" />
            <line x1="20" y1="76" x2="20" y2="90" stroke="#475569" stroke-width="4" />
            <line x1="80" y1="76" x2="80" y2="90" stroke="#475569" stroke-width="4" />
            
            <!-- Desk Lamp (Yellow light cone casting down) -->
            <path d="M15 70 L5 45 H20 Z" fill="#fef08a" opacity="0.3" />
            <path d="M12 40 L18 45" stroke="#ef4444" stroke-width="3" />
            <path d="M15 45 L15 70" stroke="#64748b" stroke-width="2" />
            
            <!-- Office Worker -->
            <!-- Torso -->
            <path d="M38 64 C38 52, 68 52, 68 64 L65 70 H41 Z" fill="#0d9488" />
            <!-- Head bobbing -->
            <g style="animation: headBob 2s ease-in-out infinite;">
                <circle cx="53" cy="46" r="10" fill="#f59e0b" />
                <path d="M48 44 C50 40, 56 40, 58 44" fill="none" stroke="#1e293b" stroke-width="1.5" />
            </g>
            
            <!-- Laptop Screen and Keyboard -->
            <rect x="68" y="44" width="22" height="26" fill="#1e293b" rx="2" />
            <!-- Animated Screen Chart -->
            <path d="M72 62 L78 54 L82 58 L88 48" fill="none" stroke="#10b981" stroke-width="2" stroke-dasharray="30" stroke-dashoffset="30" style="animation: chartScroll 1.5s linear infinite;" />
            <rect x="65" y="68" width="26" height="3" fill="#cbd5e1" />
            
            <!-- Typing hands (moving fast) -->
            <circle cx="61" cy="65" r="3" fill="#f59e0b" style="animation: typingFast 0.3s ease-in-out infinite alternate;" />
            <circle cx="65" cy="64" r="3" fill="#f59e0b" style="animation: typingFast 0.3s ease-in-out infinite alternate-reverse;" />
            
            <!-- Coffee mug with steam -->
            <rect x="30" y="62" width="6" height="8" fill="#ef4444" rx="1" />
            <path d="M32 52 C30 48, 34 46, 32 42" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" style="animation: steamPremium 1.8s ease-in-out infinite;" />
        </svg>
        <div class="operational-card-text">
            <h4>Administración y Oficina</h4>
            <p>Gestión corporativa, atención al cliente y facturación del negocio.</p>
        </div>
    </div>

    <!-- Card 3: Comercial -->
    <div class="operational-card">
        <div class="woman-container">
            <img src="{{ asset('images/mujer_ventas.png') }}" class="woman-img" alt="Comercio y Ventas">
        </div>
        <div class="operational-card-text">
            <h4>Comercio y Ventas</h4>
            <p>Exhibición de accesorios y calzado, atención directa y ventas.</p>
        </div>
    </div>
</div>

<!-- Barra de Búsqueda y Filtros -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px; flex-wrap: wrap;">
    <div style="display: flex; gap: 10px; flex: 1; min-width: 250px;">
        <input type="text" id="buscar" placeholder="Buscar por número, nombre o puesto..." style="padding: 12px; width: 100%; max-width: 350px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
    </div>
</div>

@if(Auth::user()->esAdmin())
<!-- Panel de Acciones Masivas y Borrado por Sitio -->
<div class="barra-acciones">
    <!-- Deletión por Checkbox -->
    <div>
        <form id="formEliminarMasivo" action="/empleados/eliminar-masivo" method="POST" onsubmit="return confirmarEliminacionMasiva(event)">
            @csrf
            <div id="bulkIdsContainer"></div>
            <button type="submit" id="btnEliminarMasivo" class="btn-danger-solid" disabled>
                🗑 Eliminar Seleccionados (<span id="lblCantSeleccionados">0</span>)
            </button>
        </form>
    </div>

    <!-- Eliminación por Sitio -->
    @php
        $sitios = $empleados->pluck('sitio')->unique()->filter();
    @endphp
    @if($sitios->count() > 0)
        <div class="seccion-eliminar-sitio">
            <form action="/empleados/eliminar-por-sitio" method="POST" onsubmit="return confirmarEliminacionPorSitio(event)">
                @csrf
                <select name="sitio" id="selectSitioEliminar" required style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 500; outline: none; background: white;">
                    <option value="">-- Seleccionar Sitio --</option>
                    @foreach($sitios as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-danger-outline">
                    ⚠️ Eliminar por Sitio
                </button>
            </form>
        </div>
    @endif
</div>
@endif

<!-- Tabla de Empleados -->
<div style="overflow-x: auto;">
    <table>
        <thead>
            <tr>
                <th style="width: 40px; text-align: center;">
                    <input type="checkbox" id="checkAll" onchange="toggleAllCheckboxes(this)">
                </th>
                <th>Número</th>
                <th>Nombre Completo</th>
                <th>Sitio</th>
                <th>Sucursal</th>
                <th>Puesto</th>
                <th>Fecha Ingreso</th>
                <th>Estado</th>
                <th style="text-align: center;">Acción</th>
                <th style="text-align: center;">Editar</th>
                <th style="text-align: center;">Eliminar</th>
            </tr>
        </thead>
        <tbody id="tabla-empleados">
            @forelse($empleados as $empleado)
                <tr ondblclick="abrirModalDetalle({{ $empleado->id }})" style="cursor: pointer;" title="Doble clic para ver detalle de vacaciones">
                    <td style="text-align: center;">
                        <input type="checkbox" class="row-checkbox" value="{{ $empleado->id }}" onchange="updateSelectedCount()" @if(Auth::user()->esSoloLectura()) disabled @endif>
                    </td>
                    <td style="font-weight: 600;">{{ $empleado->numero_empleado }}</td>
                    <td style="font-weight: 500;">
                        {{ $empleado->nombre }} {{ $empleado->apellido_paterno }} {{ $empleado->apellido_materno }}
                    </td>
                    <td>{{ $empleado->sitio }}</td>
                    <td>{{ $empleado->sucursal }}</td>
                    <td>{{ $empleado->puesto }}</td>
                    <td>{{ \Carbon\Carbon::parse($empleado->fecha_ingreso)->format('d/m/Y') }}</td>
                    <td>
                        <span style="font-weight: 600; font-size: 13px; color: {{ $empleado->activo ? '#16a34a' : '#94a3b8' }};">
                            {{ $empleado->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        @if(Auth::user()->esSoloLectura())
                            <button type="button" class="boton-desactivar @if($empleado->activo) activo @else inactivo @endif" disabled style="opacity: 0.6; cursor: not-allowed;">
                                {{ $empleado->activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        @else
                            <form action="/empleados/cambiar-estado/{{ $empleado->id }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="boton-desactivar @if($empleado->activo) activo @else inactivo @endif">
                                    {{ $empleado->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if(Auth::user()->esSoloLectura())
                            <span style="color: #94a3b8; font-weight: 600; cursor: not-allowed; opacity: 0.6;">
                                ✏️ Editar
                            </span>
                        @else
                            <a href="/empleados/{{ $empleado->id }}/editar" style="color: #2563eb; font-weight: 600; text-decoration: none;">
                                ✏️ Editar
                            </a>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if(Auth::user()->esAdmin())
                        <form action="/empleados/eliminar/{{ $empleado->id }}" method="POST" onsubmit="return confirmarEliminacionIndividual(event, '{{ $empleado->nombre }} {{ $empleado->apellido_paterno }}')" style="margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: transparent; border: none; color: #ef4444; font-weight: 600; cursor: pointer;">
                                🗑 Borrar
                            </button>
                        </form>
                        @else
                        <button type="button" disabled style="background: transparent; border: none; color: #cbd5e1; font-weight: 600; cursor: not-allowed; opacity: 0.6;">
                            🗑 Borrar
                        </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; color: #94a3b8; padding: 30px;">
                        No hay empleados registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    // Search Filter
    document.getElementById('buscar').addEventListener('keyup', function() {
        let texto = this.value.toLowerCase();
        let filas = document.querySelectorAll('#tabla-empleados tr');
        
        filas.forEach(function(fila) {
            let contenido = fila.innerText.toLowerCase();
            if (contenido.includes(texto)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });

    // Checkboxes management
    function toggleAllCheckboxes(master) {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(function(cb) {
            cb.checked = master.checked;
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const count = checkboxes.length;
        const btn = document.getElementById('btnEliminarMasivo');
        const lbl = document.getElementById('lblCantSeleccionados');
        
        lbl.textContent = count;
        if (count > 0) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }

        // Keep master checkbox in sync
        const allCheckboxes = document.querySelectorAll('.row-checkbox');
        const master = document.getElementById('checkAll');
        if (checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0) {
            master.checked = true;
        } else {
            master.checked = false;
        }
    }

    // Safety Warnings
    function confirmarEliminacionIndividual(event, nombre) {
        const confirmacion = confirm(`⚠️ ¡ADVERTENCIA DE SEGURIDAD! ⚠️\n\n¿Está absolutamente seguro de que desea eliminar permanentemente al empleado "${nombre}"?\n\nEsta acción eliminará de forma irreversible al colaborador y todos sus saldos de vacaciones, solicitudes, cuentas de login e incidencias registradas en el sistema.\n\nEsta acción no se puede deshacer.`);
        return confirmacion;
    }

    function confirmarEliminacionMasiva(event) {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const count = checkboxes.length;
        
        if (count === 0) return false;

        const confirmacion = confirm(`⚠️ ¡ADVERTENCIA DE SEGURIDAD MÁXIMA! ⚠️\n\n¿Está seguro de que desea eliminar permanentemente a los ${count} empleados seleccionados?\n\nEsta acción borrará de manera irreversible todas sus cuentas de login, saldos de vacaciones, solicitudes e incidencias asociadas.\n\nEscriba "Aceptar" para continuar con la eliminación masiva.`);
        
        if (confirmacion) {
            // Populate the container with hidden fields before submitting
            const container = document.getElementById('bulkIdsContainer');
            container.innerHTML = '';
            checkboxes.forEach(function(cb) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
            return true;
        }
        return false;
    }

    function confirmarEliminacionPorSitio(event) {
        const select = document.getElementById('selectSitioEliminar');
        const sitio = select.value;
        
        if (!sitio) {
            alert('Por favor, seleccione un sitio.');
            return false;
        }

        const confirmacion = confirm(`⚠️ ¡ALERTA CRÍTICA DE SEGURIDAD! ⚠️\n\n¿Está seguro de que desea eliminar a TODOS los empleados que pertenecen al sitio "${sitio}"?\n\nEsta acción borrará de forma definitiva a todos los colaboradores del sitio, sus saldos de vacaciones, historial de solicitudes, cuentas y reportes de incidencias.\n\nPresione Aceptar para proceder con la purga del sitio.`);
        return confirmacion;
    }

    function displaySelectedFileName(input) {
        const lbl = document.getElementById('excelFileName');
        const btn = document.getElementById('btnSubmitImport');
        if (input.files && input.files.length > 0) {
            lbl.textContent = input.files[0].name;
            btn.style.display = 'inline-block';
        } else {
            lbl.textContent = 'Importar Excel';
            btn.style.display = 'none';
        }
    }

    // Modal Detalle
    function abrirModalDetalle(id) {
        const modal = document.getElementById('modalDetalleEmpleado');
        const loader = document.getElementById('modalLoader');
        const content = document.getElementById('modalContent');
        const tablaBody = document.getElementById('modalSaldosTabla');
        
        // Show modal & loader
        modal.style.display = 'flex';
        loader.style.display = 'block';
        content.style.display = 'none';
        tablaBody.innerHTML = '';

        fetch(`/empleados/${id}/detalle-modal`)
            .then(res => {
                if (!res.ok) throw new Error('Error al cargar la información.');
                return res.json();
            })
            .then(data => {
                document.getElementById('modalNombreEmpleado').textContent = data.nombre_completo;
                document.getElementById('modalFechaIngreso').textContent = data.fecha_ingreso;
                document.getElementById('modalFechaNacimiento').textContent = data.fecha_nacimiento || 'No registrada';
                document.getElementById('modalEdad').textContent = data.edad !== null ? `${data.edad} AÑOS` : 'No registrada';
                document.getElementById('modalAnosCumplidos').textContent = `${data.anos_cumplidos} AÑOS`;

                let html = '';
                if (data.saldos && data.saldos.length > 0) {
                    data.saldos.forEach(s => {
                        html += `
                            <tr style="border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #334155;">
                                <td style="padding: 12px 5px; font-weight: 600;">Periodo ${s.periodo}</td>
                                <td style="padding: 12px 5px; text-align: center; font-weight: 600; color: #2563eb;">${s.correspondientes}</td>
                                <td style="padding: 12px 5px; text-align: center; font-weight: 600; color: #ef4444;">${s.tomadas}</td>
                                <td style="padding: 12px 5px; text-align: center; font-weight: 600; color: #16a34a;">${s.pendientes}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = `
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8; padding: 20px;">
                                No se encontraron saldos registrados para este colaborador.
                            </td>
                        </tr>
                    `;
                }
                tablaBody.innerHTML = html;

                loader.style.display = 'none';
                content.style.display = 'block';
            })
            .catch(err => {
                tablaBody.innerHTML = `
                    <tr>
                        <td colspan="4" style="text-align: center; color: #ef4444; padding: 20px; font-weight: 600;">
                            ⚠️ ${err.message || 'No se pudo obtener el detalle.'}
                        </td>
                    </tr>
                `;
                loader.style.display = 'none';
                content.style.display = 'block';
            });
    }

    function cerrarModalDetalle() {
        document.getElementById('modalDetalleEmpleado').style.display = 'none';
    }

    // Cerrar modal al hacer clic fuera de la caja
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('modalDetalleEmpleado');
        if (event.target === modal) {
            cerrarModalDetalle();
        }
    });
</script>

<!-- Modal Detalle Empleado -->
<div id="modalDetalleEmpleado" class="modal" style="display: none; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); justify-content: center; align-items: center;">
    <div style="background-color: white; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid #e2e8f0; width: 90%; max-width: 600px; padding: 25px; position: relative;">
        
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px;">
            <h3 style="font-weight: 700; color: #0f172a; margin: 0; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                <span>🌴</span> Detalle Histórico Vacacional
            </h3>
            <button onclick="cerrarModalDetalle()" style="background: transparent; border: none; font-size: 22px; font-weight: 700; color: #94a3b8; cursor: pointer; transition: color 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">&times;</button>
        </div>
        
        <!-- Loader -->
        <div id="modalLoader" style="text-align: center; padding: 30px;">
            <div style="border: 4px solid #f3f3f3; border-top: 4px solid #3b82f6; border-radius: 50%; width: 35px; height: 35px; animation: spin 1s linear infinite; margin: 0 auto 10px auto;"></div>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
            <span style="color: #64748b; font-size: 14px; font-weight: 500;">Cargando información...</span>
        </div>

        <!-- Content -->
        <div id="modalContent" style="display: none;">
            <div style="background: #f8fafc; border-radius: 12px; padding: 18px; margin-bottom: 25px; border: 1px solid #e2e8f0;">
                <div style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 8px;" id="modalNombreEmpleado"></div>
                <div style="font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 6px; font-weight: 500;">
                    <span>📅</span> Fecha de Ingreso: <strong id="modalFechaIngreso" style="color: #1e293b; font-weight: 600;"></strong>
                </div>
                <div style="font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 6px; font-weight: 500; margin-top: 8px;">
                    <span>🎂</span> Fecha de Nacimiento: <strong id="modalFechaNacimiento" style="color: #1e293b; font-weight: 600;"></strong>
                </div>
                <div style="font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 6px; font-weight: 500; margin-top: 8px;">
                    <span>⏳</span> Edad: <strong id="modalEdad" style="color: #1e293b; font-weight: 600;"></strong>
                </div>
                <div style="font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 6px; font-weight: 500; margin-top: 8px;">
                    <span>🏅</span> Años cumplidos: <strong id="modalAnosCumplidos" style="color: #2563eb; font-weight: 700;"></strong>
                </div>
            </div>

            <h4 style="font-size: 14px; font-weight: 700; color: #475569; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                <span>🌴</span> Resumen de Vacaciones por Periodo
            </h4>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; font-size: 12px; color: #64748b; text-transform: uppercase;">
                            <th style="padding: 10px 5px; font-weight: 700;">Periodo</th>
                            <th style="padding: 10px 5px; text-align: center; font-weight: 700;">Corresponden</th>
                            <th style="padding: 10px 5px; text-align: center; font-weight: 700; color: #ef4444;">Tomadas</th>
                            <th style="padding: 10px 5px; text-align: center; font-weight: 700; color: #16a34a;">Pendientes</th>
                        </tr>
                    </thead>
                    <tbody id="modalSaldosTabla">
                        <!-- Dynamic rows -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 25px; border-top: 1px solid #f1f5f9; padding-top: 15px; text-align: right;">
            <button onclick="cerrarModalDetalle()" class="boton-volver" style="margin-bottom: 0; padding: 9px 20px; font-size: 13px; display: inline-flex; height: auto;">Cerrar</button>
        </div>
    </div>
</div>

@endsection