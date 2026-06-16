@extends('layouts.app')

@section('contenido')
<style>
    /* Letrero Vacaciones con efecto aparecer/desaparecer (fade in / out con escala) */
    @keyframes pulseVacaciones {
        0%, 100% {
            opacity: 0.1;
            transform: scale(0.9);
            filter: drop-shadow(0 0 2px rgba(255,255,255,0.2));
        }
        50% {
            opacity: 1;
            transform: scale(1.08);
            filter: drop-shadow(0 0 15px rgba(255,255,255,0.8));
        }
    }

    /* Vuelo de gaviotas de derecha a izquierda cruzando el banner */
    @keyframes seagullFlight {
        0% {
            transform: translate(150px, 40px) scale(0.4);
            opacity: 0;
        }
        10% {
            opacity: 0.8;
        }
        90% {
            opacity: 0.8;
        }
        100% {
            transform: translate(-380px, -20px) scale(0.9);
            opacity: 0;
        }
    }

    /* Aleteo de las alas de la gaviota en 3D */
    @keyframes wingFlap {
        0%, 100% {
            transform: rotateX(0deg) scaleY(1);
        }
        50% {
            transform: rotateX(70deg) scaleY(0.3);
        }
    }

    /* Caída de confeti de fiesta */
    @keyframes confettiRain {
        0% {
            transform: translateY(-20px) rotate(0deg);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateY(180px) rotate(720deg);
            opacity: 0;
        }
    }

    .movimientos-banner {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 40%, #f59e0b 100%);
        border-radius: 16px;
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 30px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 25px rgba(3, 105, 161, 0.15);
        color: white;
        height: 150px;
    }

    .banner-left {
        z-index: 2;
        position: relative;
        flex: 1;
    }

    .letrero-vacaciones {
        font-size: 38px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 4px;
        color: #ffffff;
        margin: 0;
        display: inline-block;
        animation: pulseVacaciones 3s ease-in-out infinite;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .banner-right {
        z-index: 2;
        position: relative;
        width: 140px;
        height: 140px;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-shrink: 0;
    }

    .banner-img-frame {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid rgba(255, 255, 255, 0.9);
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        position: relative;
        background: #e2e8f0;
    }

    .banner-img-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Confeti contenedor y piezas individuales */
    .confetti-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 70%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    }

    .confetti-piece {
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 2px;
        opacity: 0;
        animation: confettiRain 4s linear infinite;
    }

    /* Gaviotas volando sobre el banner */
    .seagull-container {
        position: absolute;
        right: 0;
        top: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 3;
        overflow: hidden;
    }

    .seagull {
        position: absolute;
        width: 35px;
        height: 20px;
        animation: seagullFlight 8s linear infinite;
    }

    .seagull-wing-left, .seagull-wing-right {
        fill: #ffffff;
        animation: wingFlap 0.6s ease-in-out infinite;
        transform-origin: center bottom;
    }
</style>

<h1 style="margin-bottom:15px;">
    Movimientos de vacaciones
</h1>

<div class="movimientos-banner no-print">
    <!-- Confeti de fiesta flotando -->
    <div class="confetti-container">
        <div class="confetti-piece" style="left: 10%; background: #f43f5e; animation-delay: 0s; animation-duration: 3.5s;"></div>
        <div class="confetti-piece" style="left: 25%; background: #3b82f6; animation-delay: 1.2s; animation-duration: 4.2s;"></div>
        <div class="confetti-piece" style="left: 40%; background: #10b981; animation-delay: 0.5s; animation-duration: 3.8s;"></div>
        <div class="confetti-piece" style="left: 55%; background: #eab308; animation-delay: 2.1s; animation-duration: 4.5s;"></div>
        <div class="confetti-piece" style="left: 70%; background: #a855f7; animation-delay: 1.6s; animation-duration: 3.9s;"></div>
        <div class="confetti-piece" style="left: 15%; background: #f97316; animation-delay: 2.7s; animation-duration: 4s;"></div>
        <div class="confetti-piece" style="left: 35%; background: #06b6d4; animation-delay: 0.9s; animation-duration: 3.6s;"></div>
        <div class="confetti-piece" style="left: 60%; background: #ec4899; animation-delay: 2.4s; animation-duration: 4.1s;"></div>
    </div>

    <!-- Letrero de Vacaciones -->
    <div class="banner-left">
        <h2 class="letrero-vacaciones">¡Vacaciones!</h2>
    </div>

    <!-- Gaviotas volando sobre el banner -->
    <div class="seagull-container">
        <!-- Gaviota 1 -->
        <svg class="seagull" style="animation-delay: 0s; animation-duration: 9s; top: 20px;" viewBox="0 0 100 60">
            <path class="seagull-wing-left" d="M10,30 C30,20 45,5 50,30 C45,35 25,35 10,30 Z" />
            <path class="seagull-wing-right" d="M90,30 C70,20 55,5 50,30 C55,35 75,35 90,30 Z" />
        </svg>
        <!-- Gaviota 2 -->
        <svg class="seagull" style="animation-delay: 3s; animation-duration: 11s; top: 60px;" viewBox="0 0 100 60">
            <path class="seagull-wing-left" d="M10,30 C30,20 45,5 50,30 C45,35 25,35 10,30 Z" />
            <path class="seagull-wing-right" d="M90,30 C70,20 55,5 50,30 C55,35 75,35 90,30 Z" />
        </svg>
        <!-- Gaviota 3 -->
        <svg class="seagull" style="animation-delay: 5.5s; animation-duration: 8s; top: 40px;" viewBox="0 0 100 60">
            <path class="seagull-wing-left" d="M10,30 C30,20 45,5 50,30 C45,35 25,35 10,30 Z" />
            <path class="seagull-wing-right" d="M90,30 C70,20 55,5 50,30 C55,35 75,35 90,30 Z" />
        </svg>
    </div>

    <!-- Lado derecho con foto de París -->
    <div class="banner-right">
        <div class="banner-img-frame">
            <img src="{{ asset('images/paris_vacaciones.jpg') }}" alt="París Vacaciones">
        </div>
    </div>
</div>

@if(session('success'))

<div class="alerta-success" style="display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap;">

    <span>{{ session('success') }}</span>
    @if(session('nuevo_id'))
        <a href="/movimientos/imprimir/{{ session('nuevo_id') }}" target="_blank" class="boton" style="background: #2563eb; padding: 6px 12px; font-size: 12px; margin: 0; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2); text-decoration: none; color: white !important;">
            🖨️ Imprimir Reporte Autorización
        </a>
    @endif

</div>

@endif

<div style="display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;">

    <div style="display:flex; gap:10px;">
        <a href="/movimientos/exportar/excel" class="boton" style="background: #16a34a; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);">
            <span>📥</span> Exportar Excel
        </a>
        <button onclick="window.print()" class="boton" style="background: #475569; box-shadow: 0 4px 12px rgba(71, 85, 105, 0.2);">
            <span>🖨️</span> Exportar PDF
        </button>
        @if(Auth::user()->esSoloLectura())
        <button class="boton" style="background: #94a3b8; cursor: not-allowed; box-shadow: none;" disabled>
            + Asignar vacaciones
        </button>
        @else
        <a href="/movimientos/crear" class="boton">
            + Asignar vacaciones
        </a>
        @endif
    </div>

    <input type="text"
    id="buscar-movimientos"
    placeholder="Buscar movimientos..."
    style="padding:10px;
    width:250px;
    border:1px solid #ccc;
    border-radius:6px;">

</div>

<table>

<thead>

<tr>

<th>Empleado</th>
<th>Periodo</th>
<th>Inicio</th>
<th>Fin</th>
<th>Días</th>
<th>Imprimir</th>
<th>Editar</th>
<th>Eliminar</th>

</tr>

</thead>

<tbody>

@foreach($movimientos as $movimiento)

<tr>

<td>

{{ $movimiento->empleado->numero_empleado }}
-
{{ $movimiento->empleado->nombre }}
{{ $movimiento->empleado->apellido_paterno }}

</td>

<td>

{{ $movimiento->periodo }}

</td>

<td>

{{ $movimiento->fecha_inicio }}

</td>

<td>

{{ $movimiento->fecha_fin }}

</td>

<td>
    {{ $movimiento->dias }}
</td>

<td>
    <a href="/movimientos/imprimir/{{ $movimiento->id }}" target="_blank" style="color: #2563eb; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
        🖨️ Imprimir
    </a>
</td>

<td>
    @if(Auth::user()->esSoloLectura())
        <span style="color: #94a3b8; font-weight: 600; cursor: not-allowed; opacity: 0.6;">
            ✏️ Editar
        </span>
    @else
        <a href="/movimientos/editar/{{ $movimiento->id }}">
            ✏️ Editar
        </a>
    @endif
</td>

<td>
    @if(Auth::user()->esSoloLectura())
        <span style="color: #94a3b8; font-weight: 600; cursor: not-allowed; opacity: 0.6;">
            🗑 Eliminar
        </span>
    @elseif(Auth::user()->esAdmin())
        <a href="/movimientos/eliminar/{{ $movimiento->id }}"
           onclick="return confirm('¿Eliminar este movimiento?')">
            🗑 Eliminar
        </a>
    @else
        <span style="color: #cbd5e1; font-weight: 600; cursor: not-allowed; opacity: 0.6;">
            🗑 Eliminar
        </span>
    @endif
</td>

</tr>

@endforeach

</tbody>

</table>

<script>


document.getElementById('buscar-movimientos')
.addEventListener('keyup', function(){

    let texto = this.value.toLowerCase().trim();

    let filas = document.querySelectorAll('tbody tr');

    filas.forEach(function(fila){

        let columnas = fila.querySelectorAll('td');

        let encontrado = false;

        columnas.forEach(function(columna){

            let contenido = columna.textContent.toLowerCase().trim();

            if(contenido.includes(texto)){

                encontrado = true;
            }

        });

        if(encontrado){

            fila.style.display = '';

        }else{

            fila.style.display = 'none';
        }

    });

});

</script>

@endsection