@extends('layouts.app')

@section('contenido')

@php
    $firstDayOfMonth = \Carbon\Carbon::create($year, $month, 1);
    $daysInMonth = $firstDayOfMonth->daysInMonth;
    $startDayOfWeek = $firstDayOfMonth->dayOfWeekIso; // 1 (Lunes) a 7 (Domingo)
    
    $monthNames = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    
    // Agrupar festivos de este mes por día
    $monthHolidays = [];
    foreach ($festivos as $f) {
        $fDate = \Carbon\Carbon::parse($f->fecha);
        if ($fDate->year == $year && $fDate->month == $month) {
            $monthHolidays[$fDate->day] = $f;
        }
    }
    
    // Navegación
    $prevMonth = $month - 1;
    $prevYear = $year;
    if ($prevMonth < 1) {
        $prevMonth = 12;
        $prevYear--;
    }
    
    $nextMonth = $month + 1;
    $nextYear = $year;
    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear++;
    }
@endphp

<div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 30px; align-items: start;">
    
    <!-- Columna Izquierda: Formulario e Historial -->
    <div>
        <h1 style="margin-bottom: 25px; font-weight: 700; color: #0f172a;">Gestión de Días Festivos</h1>
        
        <!-- Formulario de Registro -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <h3 style="margin-bottom: 15px; font-weight: 600; color: #1e293b;">Registrar Día Inhábil</h3>
            
            <form action="/configuracion/festivos" method="POST">
                @csrf
                <div class="grupo">
                    <label for="fecha">Fecha del Inhábil</label>
                    <input type="date" name="fecha" id="fecha" required style="border: 1px solid #cbd5e1; padding: 10px; border-radius: 8px; width: 100%;">
                </div>
                
                <div class="grupo">
                    <label for="nombre">Nombre / Descripción</label>
                    <input type="text" name="nombre" id="nombre" required placeholder="Ej. Navidad, Aniversario Luctuoso" style="border: 1px solid #cbd5e1; padding: 10px; border-radius: 8px; width: 100%;">
                </div>
                
                <div class="grupo">
                    <label for="tipo">Categoría de Festivo</label>
                    <select name="tipo" id="tipo" required>
                        <option value="ley">Día Festivo de Ley (Obligatorio)</option>
                        <option value="tradicion">Festivo por Tradición / Concedido por la Empresa</option>
                    </select>
                </div>
                
                <button type="submit" class="boton" style="width: 100%; justify-content: center;">
                    <span>➕</span> Agregar Día Festivo
                </button>
            </form>
        </div>
        
        <!-- Historial / Listado de Días -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
            <h3 style="margin-bottom: 15px; font-weight: 600; color: #1e293b;">Días Inhábiles Registrados</h3>
            
            <div style="max-height: 350px; overflow-y: auto; padding-right: 5px;">
                @if($festivos->count() > 0)
                    <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; font-size: 11px; color: #64748b; text-transform: uppercase;">
                                <th style="padding: 10px 5px; font-weight: 700; background: #f8fafc; border: none; border-bottom: 2px solid #e2e8f0;">Fecha</th>
                                <th style="padding: 10px 5px; font-weight: 700; background: #f8fafc; border: none; border-bottom: 2px solid #e2e8f0;">Nombre</th>
                                <th style="padding: 10px 5px; font-weight: 700; background: #f8fafc; border: none; border-bottom: 2px solid #e2e8f0;">Tipo</th>
                                <th style="padding: 10px 5px; text-align: center; font-weight: 700; background: #f8fafc; border: none; border-bottom: 2px solid #e2e8f0;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($festivos as $f)
                                <tr style="border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #334155;">
                                    <td style="padding: 12px 5px; background: white; border: none; border-bottom: 1px solid #f1f5f9; font-weight: 600;">{{ \Carbon\Carbon::parse($f->fecha)->format('d/m/Y') }}</td>
                                    <td style="padding: 12px 5px; background: white; border: none; border-bottom: 1px solid #f1f5f9;">{{ $f->nombre }}</td>
                                    <td style="padding: 12px 5px; background: white; border: none; border-bottom: 1px solid #f1f5f9;">
                                        @if($f->tipo === 'ley')
                                            <span style="background: #d1fae5; color: #065f46; font-size: 10px; font-weight: 600; padding: 2px 6px; border-radius: 9999px; text-transform: uppercase;">De Ley</span>
                                        @else
                                            <span style="background: #ffedd5; color: #9a3412; font-size: 10px; font-weight: 600; padding: 2px 6px; border-radius: 9999px; text-transform: uppercase;">Tradición</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 5px; text-align: center; background: white; border: none; border-bottom: 1px solid #f1f5f9;">
                                        <form action="/configuracion/festivos/{{ $f->id }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este día festivo inhábil?')" style="margin: 0; display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: transparent; border: none; color: #ef4444; font-weight: 600; cursor: pointer;">
                                                🗑 Borrar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="color: #64748b; font-size: 13px; text-align: center; padding: 30px;">No hay días festivos registrados en el sistema.</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Columna Derecha: Calendario Visual -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); height: 100%;">
        
        <!-- Header del Calendario -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <a href="?year={{ $prevYear }}&month={{ $prevMonth }}" class="boton-volver" style="margin-bottom: 0; padding: 8px 14px; font-size: 12.5px;">
                ◀ {{ $monthNames[$prevMonth] }}
            </a>
            
            <h2 style="font-weight: 700; color: #0f172a; font-size: 20px; text-align: center;">
                {{ $monthNames[$month] }} {{ $year }}
            </h2>
            
            <a href="?year={{ $nextYear }}&month={{ $nextMonth }}" class="boton-volver" style="margin-bottom: 0; padding: 8px 14px; font-size: 12.5px;">
                {{ $monthNames[$nextMonth] }} ▶
            </a>
        </div>
        
        <!-- Leyendas del Calendario -->
        <div style="display: flex; gap: 20px; font-size: 12px; justify-content: center; margin-bottom: 25px; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 12px; height: 12px; background: #d1fae5; border: 1px solid #a7f3d0; border-radius: 3px;"></div>
                <span style="color: #334155; font-weight: 500;">Festivo Ley</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 12px; height: 12px; background: #ffedd5; border: 1px solid #fed7aa; border-radius: 3px;"></div>
                <span style="color: #334155; font-weight: 500;">Festivo Empresa</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 12px; height: 12px; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 3px;"></div>
                <span style="color: #334155; font-weight: 500;">Fin de Semana</span>
            </div>
        </div>
        
        <!-- Grid de Días de Semana -->
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; text-align: center; font-weight: 600; font-size: 11px; color: #64748b; text-transform: uppercase; margin-bottom: 10px;">
            <div>Lun</div>
            <div>Mar</div>
            <div>Mié</div>
            <div>Jue</div>
            <div>Vie</div>
            <div style="color: #ef4444;">Sáb</div>
            <div style="color: #ef4444;">Dom</div>
        </div>
        
        <!-- Grid de Días del Mes -->
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px;">
            
            <!-- Celdas vacías previas -->
            @for($i = 1; $i < $startDayOfWeek; $i++)
                <div style="aspect-ratio: 1; border-radius: 8px; background: #f8fafc; border: 1px solid #f1f5f9; opacity: 0.3;"></div>
            @endfor
            
            <!-- Celdas de Días -->
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $currentDayDate = $firstDayOfMonth->copy()->day($day);
                    $isWeekend = $currentDayDate->isWeekend();
                    $hasHoliday = isset($monthHolidays[$day]);
                    $holiday = $hasHoliday ? $monthHolidays[$day] : null;
                    
                    // Colores de Celda
                    $bgColor = '#ffffff';
                    $borderColor = '#cbd5e1';
                    $textColor = '#1e293b';
                    $tooltip = '';
                    
                    if ($isWeekend) {
                        $bgColor = '#fee2e2';
                        $borderColor = '#fca5a5';
                        $textColor = '#b91c1c';
                    }
                    
                    if ($hasHoliday) {
                        if ($holiday->tipo === 'ley') {
                            $bgColor = '#d1fae5';
                            $borderColor = '#86efac';
                            $textColor = '#065f46';
                        } else {
                            $bgColor = '#ffedd5';
                            $borderColor = '#fdba74';
                            $textColor = '#9a3412';
                        }
                        $tooltip = $holiday->nombre;
                    }
                @endphp
                
                <div title="{{ $tooltip ?: ($isWeekend ? 'Fin de Semana' : 'Día Laboral') }}" style="aspect-ratio: 1; border-radius: 8px; background: {{ $bgColor }}; border: 1px solid {{ $borderColor }}; color: {{ $textColor }}; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; transition: all 0.2s ease; cursor: default; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <span style="font-size: 14px; font-weight: 700;">{{ $day }}</span>
                    @if($hasHoliday)
                        <span style="font-size: 8px; font-weight: 600; text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 90%; margin-top: 2px;">
                            {{ $holiday->nombre }}
                        </span>
                    @endif
                </div>
            @endfor
        </div>
        
    </div>
</div>

@endsection
