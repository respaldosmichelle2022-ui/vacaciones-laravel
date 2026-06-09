<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Editar vacaciones</title>

</head>

<body>

    <h1>Editar vacaciones</h1>

    <form method="POST" action="/vacaciones/{{ $vacacion->id }}/actualizar">

        @csrf

        <label>Empleado</label>

        <select name="empleado_id">

            @foreach($empleados as $empleado)

                <option
                    value="{{ $empleado->id }}"
                    {{ $vacacion->empleado_id == $empleado->id ? 'selected' : '' }}>

                    {{ $empleado->numero_empleado }}
                    -
                    {{ $empleado->nombre }}
                    {{ $empleado->apellido_paterno }}

                </option>

            @endforeach

        </select>

        <br><br>

        <label>Periodo</label>

        <input type="number"
               name="periodo"
               value="{{ $vacacion->periodo }}">

        <br><br>

        <label>Días corresponden</label>

        <input type="number"
               name="dias_corresponden"
               value="{{ $vacacion->dias_corresponden }}">

        <br><br>

        <label>Días usados</label>

        <input type="number"
               name="dias_usados"
               value="{{ $vacacion->dias_usados }}">

        <br><br>

        <label>Días restantes</label>

        <input type="number"
               name="dias_restantes"
               value="{{ $vacacion->dias_restantes }}">

        <br><br>

        <label>Fecha inicio</label>

        <input type="date"
               name="fecha_inicio_periodo"
               value="{{ $vacacion->fecha_inicio_periodo }}">

        <br><br>

        <label>Fecha fin</label>

        <input type="date"
               name="fecha_fin_periodo"
               value="{{ $vacacion->fecha_fin_periodo }}">

        <br><br>

        <button>
            Actualizar
        </button>

    </form>

</body>

</html>