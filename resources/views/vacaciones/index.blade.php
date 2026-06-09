<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vacaciones</title>

    <style>

        body{
            font-family: Arial;
            background: #f4f4f4;
            margin: 0;
            padding: 40px;
        }

        .contenedor{
            width: 95%;
            margin: auto;
        }

        .titulo{
            font-size: 50px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .boton{
            background: #2563eb;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 8px;
            float: right;
        }

        .tabla{
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 30px;
        }

        .tabla th{
            background: #1f2937;
            color: white;
            padding: 15px;
            text-align: left;
        }

        .tabla td{
            padding: 12px;
            border: 1px solid #ddd;
        }

    </style>

</head>
<body>

<div class="contenedor">

    <div class="titulo">
        Control de Vacaciones
    </div>

    <a href="/vacaciones/crear" class="boton">
        + Agregar vacaciones
    </a>

    <table class="tabla">

        <thead>
            <tr>
                <th>Empleado</th>
                <th>Periodo</th>
                <th>Días</th>
                <th>Usados</th>
                <th>Restantes</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>

        <tbody>

            @foreach($vacaciones as $vacacion)

            <tr>

                <td>
                    {{ $vacacion->empleado->numero_empleado }}
                    -
                    {{ $vacacion->empleado->nombre }}
                    {{ $vacacion->empleado->apellido_paterno }}
                </td>

                <td>{{ $vacacion->periodo }}</td>

                <td>{{ $vacacion->dias_corresponden }}</td>

                <td>{{ $vacacion->dias_usados }}</td>

                <td>{{ $vacacion->dias_restantes }}</td>

                <td>{{ $vacacion->fecha_inicio_periodo }}</td>

                <td>{{ $vacacion->fecha_fin_periodo }}</td>
                <td>

    <a href="/vacaciones/{{ $vacacion->id }}/editar">

        ✏️ Editar

    </a>

</td>

<td>

    <a href="/vacaciones/{{ $vacacion->id }}/eliminar">

        🗑️ Eliminar

    </a>

</td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

</body>
</html>