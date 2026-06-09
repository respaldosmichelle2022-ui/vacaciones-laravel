@extends('layouts.app')

@section('contenido')

<a href="/empleados" class="boton-volver">

← Regresar

</a>

    <h1>Nuevo empleado</h1>

    <form method="POST" action="/empleados">

        @csrf

        <label>Número empleado</label>
        <br>
        <input type="text" name="numero_empleado">
        <br><br>

        <label>Nombre</label>
        <br>
        <input type="text" name="nombre">
        <br><br>

        <label>Apellido paterno</label>
        <br>
        <input type="text" name="apellido_paterno">
        <br><br>

        <label>Apellido materno</label>
        <br>
        <input type="text" name="apellido_materno">
        <br><br>

        <label>Fecha ingreso</label>
        <br>
        <input type="date" name="fecha_ingreso">
        <br><br>

        <label>Fecha nacimiento</label>
        <br>
        <input type="date" name="fecha_nacimiento">
        <br><br>
        <label>Sitio</label>
        <br>
        <input type="text" name="sitio">
        <br><br>

        <label>Sucursal</label>
        <br>
        <input type="text" name="sucursal">
        <br><br>

        <label>Puesto</label>
        <br>
        <input type="text" name="puesto">
        <br><br>
        <button type="submit">
            Guardar
        </button>

    </form>

@endsection