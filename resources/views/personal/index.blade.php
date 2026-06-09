@extends('layouts.app')

@section('contenido')

@if(isset($esAdmin) && $esAdmin)
    <div style="text-align: center; padding: 60px 40px; background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
        <div style="font-size: 56px; margin-bottom: 20px;">🛡️</div>
        <h2 style="color: #0f172a; margin-bottom: 12px; font-weight: 700;">Acceso Global Administrativo</h2>
        <p style="color: #64748b; font-size: 15px; max-width: 500px; margin: 0 auto 25px auto;">
            Como administrador, tienes acceso global y completo a todos los sitios del sistema. El módulo de <strong>Mi Sitio Personal</strong> está diseñado para delegados o usuarios estándar con un sitio asignado.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="/" class="boton">Ir al Dashboard</a>
            <a href="/empleados" class="boton-volver" style="margin: 0; padding: 11px 22px;">Ver todos los Empleados</a>
        </div>
    </div>
@elseif(isset($error))
    <div style="text-align: center; padding: 40px;">
        <h2 style="color: #ef4444; margin-bottom: 15px;">⚠️ Acceso Limitado</h2>
        <p style="color: #64748b; font-size: 15px;">{{ $error }}</p>
    </div>
@else
    <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 25px; margin-bottom: 30px;">
        <span style="font-size: 12px; color: #3b82f6; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Panel de Colaboradores del Sitio</span>
        <h1 style="font-weight: 700; color: #0f172a; margin-top: 5px;">Sitio: {{ $sitio }}</h1>
        <p style="color: #64748b; font-size: 14px;">Aquí puedes visualizar todo el personal relacionado con tu sitio y gestionar sus vacaciones e incidencias.</p>
    </div>

    <div style="display: grid; grid-template-columns: 320px 1fr; gap: 30px; align-items: start;">
        
        <!-- Columna Izquierda: Listado del Personal -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
            <h3 style="margin-bottom: 15px; font-weight: 700; color: #1e293b; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                <span>👥</span> Personal del Sitio ({{ $empleados->count() }})
            </h3>
            
            <input type="text" id="buscarColaborador" placeholder="Buscar colaborador..." 
                   style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; margin-bottom: 15px; outline: none; background: #f8fafc;"
                   onkeyup="filtrarListaColaboradores()">

            <div id="listaColaboradores" style="max-height: 550px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; padding-right: 4px;">
                @forelse($empleados as $emp)
                    <a href="/mi-sitio?empleado_id={{ $emp->id }}" 
                       class="colaborador-item"
                       style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 10px; text-decoration: none; border: 1px solid {{ (isset($empleadoSeleccionado) && $empleadoSeleccionado->id == $emp->id) ? '#3b82f6' : '#f1f5f9' }}; 
                              background: {{ (isset($empleadoSeleccionado) && $empleadoSeleccionado->id == $emp->id) ? '#eff6ff' : '#f8fafc' }};
                              transition: all 0.2s ease;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: {{ (isset($empleadoSeleccionado) && $empleadoSeleccionado->id == $emp->id) ? '#3b82f6' : '#cbd5e1' }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                            {{ substr($emp->nombre, 0, 1) }}{{ substr($emp->apellido_paterno, 0, 1) }}
                        </div>
                        <div style="flex-grow: 1; min-width: 0;">
                            <div style="font-weight: 600; font-size: 13px; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $emp->nombre }} {{ $emp->apellido_paterno }}
                            </div>
                            <div style="font-size: 11px; color: #64748b;">
                                #{{ $emp->numero_empleado }} • {{ $emp->puesto ?? 'Puesto N/D' }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 13px;">No hay colaboradores en este sitio.</div>
                @endforelse
            </div>
        </div>

        <!-- Columna Derecha: Detalle y Operaciones del Colaborador Seleccionado -->
        <div>
            @if(isset($empleadoSeleccionado))
                <!-- Ficha del Colaborador -->
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); margin-bottom: 25px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 20px;">
                        <div>
                            <span style="font-size: 11px; text-transform: uppercase; color: #3b82f6; font-weight: 700; letter-spacing: 0.5px;">Colaborador Seleccionado</span>
                            <h2 style="font-weight: 700; color: #0f172a; margin-top: 3px;">{{ $empleadoSeleccionado->nombre }} {{ $empleadoSeleccionado->apellido_paterno }} {{ $empleadoSeleccionado->apellido_materno }}</h2>
                        </div>
                        <span style="padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; 
                            {{ $empleadoSeleccionado->activo ? 'background: #dcfce7; color: #166534;' : 'background: #fee2e2; color: #991b1b;' }}">
                            {{ $empleadoSeleccionado->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        <div>
                            <span style="font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: 600;">Número de Colaborador</span>
                            <div style="font-weight: 600; color: #1e293b; margin-top: 3px;">#{{ $empleadoSeleccionado->numero_empleado }}</div>
                        </div>
                        <div>
                            <span style="font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: 600;">Fecha de Ingreso</span>
                            <div style="font-weight: 600; color: #1e293b; margin-top: 3px;">{{ \Carbon\Carbon::parse($empleadoSeleccionado->fecha_ingreso)->format('d/m/Y') }}</div>
                        </div>
                        <div>
                            <span style="font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: 600;">Puesto</span>
                            <div style="font-weight: 600; color: #1e293b; margin-top: 3px;">{{ $empleadoSeleccionado->puesto ?? 'N/D' }}</div>
                        </div>
                        <div>
                            <span style="font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: 600;">Sucursal</span>
                            <div style="font-weight: 600; color: #1e293b; margin-top: 3px;">{{ $empleadoSeleccionado->sucursal ?? 'N/D' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Detalle de Saldos e Incidencias en Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px; align-items: start;">
                    
                    <!-- Saldos y Nueva Solicitud -->
                    <div style="display: flex; flex-direction: column; gap: 25px;">
                        <!-- Saldo de Vacaciones -->
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                            <h3 style="margin-bottom: 15px; font-weight: 700; color: #1e293b; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                                <span>🌴</span> Saldo de Vacaciones
                            </h3>
                            @forelse($saldos as $s)
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                                    <div>
                                        <span style="font-weight: 600; color: #1e293b; font-size: 13px;">Periodo {{ $s->periodo }}</span>
                                        <div style="font-size: 11px; color: #64748b;">Días correspondientes: {{ $s->dias_corresponden }}</div>
                                    </div>
                                    <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #e0f2fe; color: #0369a1;">
                                        {{ $s->dias_restantes }} disponibles
                                    </span>
                                </div>
                            @empty
                                <div style="color: #94a3b8; font-size: 13px; padding: 10px 0;">No se registran saldos para este colaborador.</div>
                            @endforelse
                        </div>

                        <!-- Solicitar Vacaciones -->
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                            <h3 style="margin-bottom: 15px; font-weight: 700; color: #1e293b; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                                <span>📝</span> Registrar Vacaciones
                            </h3>
                            
                            @if(Auth::user()->esSoloLectura())
                                <div style="background: #f8fafc; color: #64748b; padding: 12px; border-radius: 8px; font-size: 13px; font-weight: 500; margin-bottom: 20px; border: 1px solid #e2e8f0;">
                                    ℹ️ <strong>Solo Visualización:</strong> Tu usuario no tiene permisos para registrar o solicitar vacaciones.
                                </div>
                                <form onsubmit="return false;">
                                    <div class="grupo">
                                        <label for="periodoSelect">Periodo Vacacional</label>
                                        <select name="periodo" id="periodoSelect" disabled>
                                            <option value="">-- Seleccionar periodo --</option>
                                        </select>
                                    </div>
                                    <div class="grupo">
                                        <label for="fecha_inicio">Fecha de Inicio</label>
                                        <input type="date" name="fecha_inicio" id="fecha_inicio" disabled>
                                    </div>
                                    <div class="grupo">
                                        <label for="fecha_fin">Fecha de Fin</label>
                                        <input type="date" name="fecha_fin" id="fecha_fin" disabled>
                                    </div>
                                    <button type="button" class="boton" style="width: 100%; justify-content: center; background: #94a3b8; cursor: not-allowed; box-shadow: none;" disabled>
                                        Registrar Solicitud
                                    </button>
                                </form>
                            @else
                                @php
                                    $periodosConSaldo = $saldos->filter(function($s) {
                                        return $s->dias_restantes > 0;
                                    });
                                @endphp

                                @if($periodosConSaldo->isEmpty())
                                    <div style="background: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 15px; border-radius: 10px; font-size: 13px; font-weight: 500; margin-bottom: 5px; line-height: 1.5;">
                                        ⚠️ El colaborador no cuenta con periodos vacacionales con días disponibles para registrar nuevas solicitudes.
                                    </div>
                                @else
                                    <form action="/mi-sitio/solicitar-vacaciones" method="POST">
                                        @csrf
                                        <input type="hidden" name="empleado_id" value="{{ $empleadoSeleccionado->id }}">
                                        
                                        <div class="grupo">
                                            <label for="periodoSelect">Periodo Vacacional</label>
                                            <select name="periodo" id="periodoSelect" required onchange="actualizarDiasDisponibles()">
                                                <option value="">-- Seleccionar periodo --</option>
                                                @foreach($periodosConSaldo as $s)
                                                    <option value="{{ $s->periodo }}" data-disponibles="{{ $s->dias_restantes }}">
                                                        Periodo {{ $s->periodo }} ({{ $s->dias_restantes }} días disponibles)
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="lblDisponibles" style="margin-top: 6px; font-size: 12px; font-weight: 600; color: #2563eb; display: none;"></div>
                                        </div>

                                        <div class="grupo">
                                            <label for="fecha_inicio">Fecha de Inicio</label>
                                            <input type="date" name="fecha_inicio" id="fecha_inicio" required onchange="calcularDiasDescontar()">
                                        </div>

                                        <div class="grupo">
                                            <label for="fecha_fin">Fecha de Fin</label>
                                            <input type="date" name="fecha_fin" id="fecha_fin" required onchange="calcularDiasDescontar()">
                                        </div>

                                        <div id="lblDescontar" style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; font-size: 13px; font-weight: 600; margin-bottom: 20px; display: none;"></div>

                                        <button type="submit" class="boton" style="width: 100%; justify-content: center;">
                                            Registrar Solicitud
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Historiales de Movimientos e Incidencias -->
                    <div style="display: flex; flex-direction: column; gap: 25px;">
                        <!-- Historial de Vacaciones tomadas -->
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                            <h3 style="margin-bottom: 15px; font-weight: 700; color: #1e293b; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                                <span>📅</span> Historial de Solicitudes
                            </h3>
                            <div style="max-height: 250px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
                                @forelse($movimientos as $m)
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
                                        <div>
                                            <span style="font-weight: 600; color: #334155; font-size: 12px;">
                                                Del {{ \Carbon\Carbon::parse($m->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($m->fecha_fin)->format('d/m/Y') }}
                                            </span>
                                            <div style="font-size: 10px; color: #64748b;">Periodo {{ $m->periodo }}</div>
                                        </div>
                                        <span style="font-weight: 700; color: #ef4444; font-size: 13px;">
                                            -{{ $m->dias }} días
                                        </span>
                                    </div>
                                @empty
                                    <div style="color: #94a3b8; font-size: 12px; padding: 10px 0;">No se registran solicitudes anteriores.</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Incidencias -->
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                            <h3 style="margin-bottom: 15px; font-weight: 700; color: #1e293b; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                                <span>⚠️</span> Incidencias Registradas
                            </h3>
                            <div style="max-height: 250px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
                                @forelse($incidencias as $i)
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
                                        <div>
                                            <span style="padding: 3px 8px; border-radius: 12px; font-size: 9px; font-weight: 700; text-transform: uppercase;
                                                @if($i->tipo == 'falta') background: #fee2e2; color: #991b1b;
                                                @elseif($i->tipo == 'retardo') background: #fef3c7; color: #92400e;
                                                @else background: #dcfce7; color: #166534; @endif">
                                                @if($i->tipo == 'falta')
                                                    Falta
                                                @elseif($i->tipo == 'retardo')
                                                    Retardo
                                                @elseif($i->tipo == 'permiso')
                                                    Permiso
                                                @elseif($i->tipo == 'permiso_horas')
                                                    Permiso por horas
                                                @elseif($i->tipo == 'incapacidad')
                                                    Incapacidad
                                                @endif
                                            </span>
                                            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">{{ $i->observaciones ?? 'Sin detalles' }}</div>
                                        </div>
                                        <span style="font-size: 11px; color: #475569; font-weight: 500;">
                                            {{ \Carbon\Carbon::parse($i->fecha)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                @empty
                                    <div style="color: #10b981; font-size: 12px; padding: 10px 0; font-weight: 500;">✨ Sin incidencias registradas.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Estado Vacío -->
                <div style="background: white; border: 1px dashed #cbd5e1; border-radius: 16px; padding: 60px 40px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.01);">
                    <div style="font-size: 48px; margin-bottom: 15px;">👈</div>
                    <h3 style="font-weight: 700; color: #1e293b; margin-bottom: 8px;">Ningún colaborador seleccionado</h3>
                    <p style="color: #64748b; font-size: 14px; max-width: 400px; margin: 0 auto;">Por favor, selecciona un colaborador del listado de la izquierda para ver su expediente de saldos, solicitudes e incidencias.</p>
                </div>
            @endif
        </div>

    </div>

    <script>
        // Filtrar lista de colaboradores en tiempo real
        function filtrarListaColaboradores() {
            const input = document.getElementById('buscarColaborador').value.toLowerCase().trim();
            const items = document.querySelectorAll('#listaColaboradores .colaborador-item');
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(input)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Script de cálculo de vacaciones (solo si hay empleado seleccionado)
        @if(isset($empleadoSeleccionado))
        function actualizarDiasDisponibles() {
            const select = document.getElementById('periodoSelect');
            const selectedOpt = select.options[select.selectedIndex];
            const lbl = document.getElementById('lblDisponibles');
            
            if (selectedOpt && selectedOpt.value !== "") {
                const disp = selectedOpt.getAttribute('data-disponibles');
                lbl.textContent = `Días disponibles en este periodo: ${disp}`;
                lbl.style.display = 'block';
            } else {
                lbl.style.display = 'none';
            }
            calcularDiasDescontar();
        }

        let holidays = @json(\App\Models\DiaFestivo::pluck('fecha')->toArray());

        function calcularDiasDescontar() {
            const inicioInput = document.getElementById('fecha_inicio').value;
            const finInput = document.getElementById('fecha_fin').value;
            const lbl = document.getElementById('lblDescontar');
            
            if (inicioInput && finInput) {
                const inicio = new Date(inicioInput + 'T00:00:00');
                const fin = new Date(finInput + 'T00:00:00');
                
                if (fin >= inicio) {
                    let workingDays = 0;
                    let current = new Date(inicio.getTime());
                    while (current <= fin) {
                        let dayOfWeek = current.getDay(); // 0 = Sunday, 6 = Saturday
                        let year = current.getFullYear();
                        let month = String(current.getMonth() + 1).padStart(2, '0');
                        let day = String(current.getDate()).padStart(2, '0');
                        let dateStr = `${year}-${month}-${day}`;

                        if (dayOfWeek !== 0 && dayOfWeek !== 6 && !holidays.includes(dateStr)) {
                            workingDays++;
                        }
                        current.setDate(current.getDate() + 1);
                    }
                    
                    lbl.textContent = `Días a descontar: ${workingDays} día(s)`;
                    lbl.style.display = 'block';
                } else {
                    lbl.style.display = 'none';
                }
            } else {
                lbl.style.display = 'none';
            }
        }
        @endif
    </script>
@endif

@endsection
