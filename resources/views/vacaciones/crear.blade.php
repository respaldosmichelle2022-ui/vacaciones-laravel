@extends('layouts.app')

@section('contenido')

<style>
.boton-volver{
    display:inline-block;
    margin-bottom:20px;
    padding:10px 18px;
    background:#64748b;
    color:white !important;
    text-decoration:none;
    border-radius:6px;
}

.boton-volver:hover{
    background:#475569;
}
</style>

<a href="/vacaciones" class="boton-volver">

← Regresar

</a>

<h1 style="margin-bottom:30px;">

Agregar vacaciones

</h1>

<form action="/vacaciones" method="POST">

    @csrf

    <div class="grupo">

        <label>Empleado</label>

        <input type="text"
               id="buscarEmpleado"
               placeholder="Buscar empleado..."
               onkeyup="filtrarEmpleado()">

        <br><br>

        <select name="empleado_id"
                id="empleadoSelect"
                size="8"
                required>

            @foreach($empleados as $empleado)

                <option value="{{ $empleado->id }}">

                    {{ $empleado->numero_empleado }}
                    -
                    {{ $empleado->nombre }}
                    {{ $empleado->apellido_paterno }}

                </option>

            @endforeach

        </select>

    </div>

    <div class="grupo">

        <label>Periodo</label>

        <input type="number"S
               name="periodo"
               required>

    </div>

    <div class="grupo">

        <label>Días que corresponden</label>

        <input type="number"
               name="dias_corresponden"
               required>

    </div>

    <div class="grupo">

        <label>Días usados</label>

        <input type="number"
               name="dias_usados"
               value="0"
               required>

    </div>

    <div class="grupo">

        <label>Días restantes</label>

        <input type="number"
               name="dias_restantes"
               readonly>

    </div>

    <div class="grupo">

        <label>Fecha inicio periodo</label>

        <input type="date"
               name="fecha_inicio_periodo">

    </div>

    <div class="grupo">

        <label>Fecha fin periodo</label>

        <input type="date"
               name="fecha_fin_periodo">

    </div>

    <button type="submit">

        Guardar vacaciones

    </button>

</form>

<script>

    const diasCorresponden = document.querySelector('[name="dias_corresponden"]');

    const diasUsados = document.querySelector('[name="dias_usados"]');

    const diasRestantes = document.querySelector('[name="dias_restantes"]');

    function calcularRestantes()
    {
        let corresponden = parseInt(diasCorresponden.value) || 0;

        let usados = parseInt(diasUsados.value) || 0;

        diasRestantes.value = corresponden - usados;
    }

    diasCorresponden.addEventListener('input', calcularRestantes);

    diasUsados.addEventListener('input', calcularRestantes);

    function filtrarEmpleado()
    {
        let input = document.getElementById('buscarEmpleado').value.toLowerCase();

        let select = document.getElementById('empleadoSelect');

        let opciones = select.options;

        for(let i = 0; i < opciones.length; i++)
        {
            let texto = opciones[i].text.toLowerCase();

            if(texto.includes(input))
            {
                opciones[i].style.display = '';
            }
            else
            {
                opciones[i].style.display = 'none';
            }
        }
    }

</script>

@endsection