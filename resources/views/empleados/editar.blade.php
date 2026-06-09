@extends('layouts.app')

@section('contenido')

<style>

.contenedor{

    background:white;
    padding:30px;
    border-radius:10px;
}

.grupo{

    margin-bottom:20px;
}

label{

    display:block;
    margin-bottom:8px;
    font-weight:bold;
}

input{

    width:100%;
    padding:12px;
    border:1px solid #ccc;
    border-radius:6px;
}

.boton{

    background:#2563eb;
    color:white;
    padding:12px 20px;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

.boton-volver{

    background:#64748b;
    color:white;
    padding:10px 18px;
    border-radius:6px;
    text-decoration:none;
    display:inline-block;
    margin-bottom:20px;
    font-weight:bold;
}

</style>

<div class="contenedor">

<a href="/empleados"
class="boton-volver">

← Regresar

</a>

<h1>Editar empleado</h1>

<form action="/empleados/actualizar/{{ $empleado->id }}"
method="POST">

@csrf
@method('PUT')

<div class="grupo">

<label>Número empleado</label>

<input type="text"
name="numero_empleado"
value="{{ $empleado->numero_empleado }}">

</div>

<div class="grupo">

<label>Nombre</label>

<input type="text"
name="nombre"
value="{{ $empleado->nombre }}">

</div>

<div class="grupo">

<label>Apellido paterno</label>

<input type="text"
name="apellido_paterno"
value="{{ $empleado->apellido_paterno }}">

</div>

<div class="grupo">

<label>Apellido materno</label>

<input type="text"
name="apellido_materno"
value="{{ $empleado->apellido_materno }}">

</div>

<div class="grupo">

<label>Sitio</label>

<input type="text"
name="sitio"
value="{{ $empleado->sitio }}">

</div>

<div class="grupo">

<label>Sucursal</label>

<input type="text"
name="sucursal"
value="{{ $empleado->sucursal }}">

</div>

<div class="grupo">

<label>Puesto</label>

<input type="text"
name="puesto"
value="{{ $empleado->puesto }}">

</div>

<div class="grupo">

<label>Fecha ingreso</label>

<input type="date"
name="fecha_ingreso"
value="{{ $empleado->fecha_ingreso }}">

</div>

<div class="grupo">

<label>Fecha nacimiento</label>

<input type="date"
name="fecha_nacimiento"
value="{{ $empleado->fecha_nacimiento }}">

</div>

<button type="submit"
class="boton">

Actualizar empleado

</button>

</form>

</div>

@endsection