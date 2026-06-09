@extends('layouts.app')

@section('contenido')

<div style="max-width: 600px; margin: 0 auto;">
    <a href="/incidencias" class="boton-volver">
        ← Volver a Incidencias
    </a>

    <h1 style="margin-bottom: 25px; font-weight: 700; color: #0f172a;">Registrar Incidencia</h1>

    <form action="/incidencias" method="POST">
        @csrf

        <div class="grupo">
            <label for="buscarEmpleado">Filtrar / Buscar Empleado</label>
            <input type="text" id="buscarEmpleado" placeholder="Escribe para buscar..." onkeyup="filtrarEmpleado()" style="margin-bottom: 10px;">
            <select name="empleado_id" id="empleadoSelect" size="6" required>
                @foreach($empleados as $e)
                    <option value="{{ $e->id }}">
                        #{{ $e->numero_empleado }} - {{ $e->nombre }} {{ $e->apellido_paterno }} {{ $e->apellido_materno }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grupo">
            <label for="tipo">Tipo de Incidencia</label>
            <select name="tipo" id="tipo" required>
                <option value="falta">Falta (Inasistencia)</option>
                <option value="permiso">Permiso (Justificado)</option>
                <option value="retardo">Retardo (Llegada tarde)</option>
                <option value="permiso_horas">Permiso por horas</option>
                <option value="incapacidad">Incapacidad</option>
            </select>
        </div>

        <div class="grupo">
            <label for="fecha">Fecha del Evento</label>
            <input type="date" name="fecha" id="fecha" value="{{ date('Y-m-d') }}" required>
        </div>

        <div class="grupo">
            <label for="observaciones">Observaciones / Detalles</label>
            <textarea name="observaciones" id="observaciones" rows="4" placeholder="Describa el motivo, justificante, etc."></textarea>
        </div>

        <button type="submit" class="boton" style="width: 100%; justify-content: center; margin-top: 15px;">
            Registrar Incidencia
        </button>
    </form>
</div>

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

    function filtrarEmpleado() {
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

@endsection
