<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<title>Agregar saldo</title>

<style>

body{
    font-family:Arial;
    background:#f2f2f2;
}

.contenedor{

    width:700px;
    margin:40px auto;
    background:white;
    padding:40px;
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

input, select{

    width:100%;
    padding:12px;
    border:1px solid #ccc;
    border-radius:6px;
}

button{
    background:#2563eb;
    color:white;
    border:none;
    padding:12px 25px;
    border-radius:6px;
}

.boton-volver{
    display:inline-block;
    margin-bottom:20px;
    padding:10px 18px;
    background:#64748b;
    color:white;
    text-decoration:none;
    border-radius:6px;
}

.boton-volver:hover{
    background:#475569;
}

</style>

</head>
<body>

<div class="contenedor">

<a href="/vacaciones" class="boton-volver">
    ← Regresar
</a>

<h1>Agregar saldo</h1>

@if(session('error'))

<div style="
background:#fee2e2;
color:#991b1b;
padding:12px;
margin-bottom:20px;
border-radius:6px;
">

{{ session('error') }}

</div>

@endif

<form action="/vacaciones"
method="POST">

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

<input type="number"
name="periodo">

</div>

<div class="grupo">

<label>Días corresponden</label>

<input type="number"
name="dias_corresponden">

</div>

<button type="submit">

Guardar saldo

</button>

</form>

</div>

</body>

<script>

// Guardar copia de los empleados iniciales para filtrar
let todosLosEmpleados = [];
document.addEventListener("DOMContentLoaded", function() {
    let select = document.getElementById('empleadoSelect');
    for (let i = 0; i < select.options.length; i++) {
        let opt = select.options[i];
        todosLosEmpleados.push({
            value: opt.value,
            text: opt.text
        });
    }
    
    // Al seleccionar un empleado, dejar únicamente ese elemento en la lista y actualizar el buscador
    select.addEventListener('change', function() {
        let currentValue = select.value;
        if (currentValue) {
            let selectedEmp = todosLosEmpleados.find(e => e.value == currentValue);
            if (selectedEmp) {
                select.innerHTML = '';
                let opt = document.createElement('option');
                opt.value = selectedEmp.value;
                opt.text = selectedEmp.text;
                opt.selected = true;
                select.appendChild(opt);
                
                document.getElementById('buscarEmpleado').value = selectedEmp.text;
            }
        }
    });
});

function filtrarEmpleado()
{
    let input = document.getElementById('buscarEmpleado')
        .value
        .toLowerCase()
        .trim();

    let select = document.getElementById('empleadoSelect');
    let currentValue = select.value;
    select.innerHTML = '';

    todosLosEmpleados.forEach(function(emp) {
        if (emp.text.toLowerCase().includes(input)) {
            let opt = document.createElement('option');
            opt.value = emp.value;
            opt.text = emp.text;
            if (emp.value === currentValue) {
                opt.selected = true;
            }
            select.appendChild(opt);
        }
    });
}

</script>

</html>