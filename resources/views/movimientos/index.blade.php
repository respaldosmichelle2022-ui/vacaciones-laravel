@extends('layouts.app')

@section('contenido')

<h1 style="margin-bottom:30px;">

    Movimientos de vacaciones

</h1>

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