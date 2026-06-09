@extends('layouts.app')

@section('contenido')

<a href="/vacaciones" class="boton-volver">

← Regresar

</a>


<h1>Editar saldo</h1>

<form action="/vacaciones/actualizar/{{ $saldo->id }}"
method="POST">

@csrf
@method('PUT')

<div class="grupo">

<label>Empleado</label>

<select name="empleado_id">

@foreach($empleados as $empleado)

<option value="{{ $empleado->id }}"
{{ $saldo->empleado_id == $empleado->id ? 'selected' : '' }}>

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

<input type="number"
name="periodo"
value="{{ $saldo->periodo }}">

</div>

<div class="grupo">

<label>Días corresponden</label>

<input type="number"
name="dias_corresponden"
value="{{ $saldo->dias_corresponden }}">

</div>

<div class="grupo">

<label>Días restantes</label>

<input type="number"
name="dias_restantes"
value="{{ $saldo->dias_restantes }}">

</div>

<button type="submit">

Actualizar saldo

</button>

</form>

</div>

@endsection
