<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">

    <title>Asignar vacaciones</title>

    <style>

        body{
            font-family: Arial;
            background:#f2f2f2;
        }

        .contenedor{

            width:700px;
            margin:40px auto;
            background:white;
            padding:40px;
            border-radius:10px;
        }

        h1{
            margin-bottom:30px;
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

            background:#1e3a5f;
            color:white;
            border:none;
            padding:12px 25px;
            border-radius:6px;
            cursor:pointer;
        }

        .boton-volver{
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 18px;
            background: #64748b;
            color: white !important;
            text-decoration: none !important;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.2s ease;
        }

        .boton-volver:hover{
            background: #475569;
        }

        .error{

            background:#ffdddd;
            color:#990000;
            padding:10px;
            margin-bottom:20px;
            border-radius:5px;
        }

    </style>

</head>
<body>

     <div class="contenedor">

     <a href="/movimientos" class="boton-volver">

     ← Regresar

     </a>

     <h1>Asignar vacaciones</h1>

    @if(session('error'))

        <div class="error">

            {{ session('error') }}

        </div>

    @endif

    <form action="/movimientos" method="POST">

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

<option
    value="{{ $empleado->id }}"
    data-periodos='@json(
        \App\Models\SaldoVacacion::where(
            "empleado_id",
            $empleado->id
        )->get()->mapWithKeys(function($item) {
            return [$item->periodo => $item->dias_restantes];
        })
    )'
>
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

    <select name="periodo" id="periodoSelect" required>

        <option value="">
            Seleccionar periodo
        </option>

    </select>

    <div id="noPeriodosAlert" style="margin-top: 10px; padding: 12px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 6px; display: none; font-weight: 500;">
        ⚠️ Este empleado no cuenta con vacaciones disponibles en ningún periodo.
    </div>

    <div id="diasDisponiblesContainer" style="margin-top: 10px; font-weight: bold; color: #1e3a5f; display: none;">
        Días disponibles: <span id="lblDiasDisponibles">0</span>
    </div>

        </div>

        <div class="grupo">

            <label>Fecha inicio</label>

            <input type="date"
                   name="fecha_inicio"
                   id="fechaInicioInput"
                   required>

        </div>

        <div class="grupo">

            <label>Fecha fin</label>

            <input type="date"
                   name="fecha_fin"
                   id="fechaFinInput"
                   required>

            <div id="diasDescontarContainer" style="margin-top: 10px; font-weight: bold; color: #d9534f; display: none;">
                Días a descontar: <span id="lblDiasDescontar">0</span>
            </div>

        </div>

        <div class="grupo">
            <label>Salario diario ($ MXN)</label>
            <input type="number"
                   step="0.01"
                   name="salario_diario"
                   id="salarioDiarioInput"
                   value="{{ \App\Models\Setting::getVal('salario_minimo', 315.04) }}"
                   required
                   oninput="calcularPrimaVacacional()">
        </div>

        <div class="grupo">
            <div id="primaVacacionalContainer" style="margin-top: 10px; font-weight: bold; color: #2e7d32; display: none;">
                Prima vacacional estimada (25%): $<span id="lblPrimaVacacional">0.00</span>
            </div>
        </div>

        <button type="submit">

            Guardar vacaciones

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
            text: opt.text,
            periodos: opt.getAttribute('data-periodos')
        });
    }
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
            if (emp.periodos) {
                opt.setAttribute('data-periodos', emp.periodos);
            }
            if (emp.value === currentValue) {
                opt.selected = true;
            }
            select.appendChild(opt);
        }
    });
}

document
.getElementById('empleadoSelect')
.addEventListener('change', function() {
    let select = this;
    let currentValue = select.value;
    if (currentValue) {
        let selectedEmp = todosLosEmpleados.find(e => e.value == currentValue);
        if (selectedEmp) {
            select.innerHTML = '';
            let opt = document.createElement('option');
            opt.value = selectedEmp.value;
            opt.text = selectedEmp.text;
            if (selectedEmp.periodos) {
                opt.setAttribute('data-periodos', selectedEmp.periodos);
            }
            opt.selected = true;
            select.appendChild(opt);
            
            document.getElementById('buscarEmpleado').value = selectedEmp.text;
        }
    }
    cargarPeriodos();
    actualizarDiasDisponibles();
});

document
.getElementById('periodoSelect')
.addEventListener('change', actualizarDiasDisponibles);

document.getElementById('fechaInicioInput').addEventListener('input', calcularDiasDescontar);
document.getElementById('fechaFinInput').addEventListener('input', calcularDiasDescontar);

// Guardamos la información de periodos del empleado seleccionado
let periodosActuales = {};

function cargarPeriodos()
{
    let empleado =
        document.getElementById('empleadoSelect');

    if (empleado.selectedIndex === -1) {
        return;
    }

    let opcion =
        empleado.options[empleado.selectedIndex];

    periodosActuales =
        JSON.parse(
            opcion.getAttribute('data-periodos')
            || '{}'
        );

    let periodoSelect =
        document.getElementById('periodoSelect');

    periodoSelect.innerHTML =
        '<option value="">Seleccionar periodo</option>';

    let alertDiv = document.getElementById('noPeriodosAlert');
    let hasAvailable = false;

    Object.keys(periodosActuales).forEach(function(periodo)
    {
        if (parseInt(periodosActuales[periodo]) > 0) {
            hasAvailable = true;
            periodoSelect.innerHTML +=
            `
            <option value="${periodo}">
                ${periodo}
            </option>
            `;
        }
    });

    if (!hasAvailable) {
        alertDiv.style.display = 'block';
    } else {
        alertDiv.style.display = 'none';
    }
}

function actualizarDiasDisponibles()
{
    let periodoSelect = document.getElementById('periodoSelect');
    let periodo = periodoSelect.value;
    let container = document.getElementById('diasDisponiblesContainer');
    let label = document.getElementById('lblDiasDisponibles');

    if (periodo && periodosActuales[periodo] !== undefined) {
        label.textContent = periodosActuales[periodo];
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}

let holidays = @json(\App\Models\DiaFestivo::pluck('fecha')->toArray());

function calcularDiasDescontar()
{
    let inicioVal = document.getElementById('fechaInicioInput').value;
    let finVal = document.getElementById('fechaFinInput').value;
    let container = document.getElementById('diasDescontarContainer');
    let label = document.getElementById('lblDiasDescontar');

    if (inicioVal && finVal) {
        let inicio = new Date(inicioVal + 'T00:00:00');
        let fin = new Date(finVal + 'T00:00:00');

        if (fin >= inicio) {
            let workingDays = 0;
            let current = new Date(inicio.getTime());
            while (current <= fin) {
                let dayOfWeek = current.getDay(); // 0 = Sunday, 6 = Saturday
                let year = current.getFullYear();
                let month = String(current.getMonth() + 1).padStart(2, '0');
                let day = String(current.getDate()).padStart(2, '0');
                let dateStr = `${year}-${month}-${day}`;

                if (dayOfWeek !== 0 && dayOfWeek !== 6 && !holidays.includes(dateStr)) {
                    workingDays++;
                }
                current.setDate(current.getDate() + 1);
            }
            label.textContent = workingDays;
            container.style.display = 'block';
            calcularPrimaVacacional();
        } else {
            container.style.display = 'none';
            document.getElementById('primaVacacionalContainer').style.display = 'none';
        }
    } else {
        container.style.display = 'none';
        document.getElementById('primaVacacionalContainer').style.display = 'none';
    }
}

function calcularPrimaVacacional() {
    let salario = parseFloat(document.getElementById('salarioDiarioInput').value) || 0;
    let container = document.getElementById('primaVacacionalContainer');
    let label = document.getElementById('lblPrimaVacacional');

    if (salario > 0) {
        let prima = salario * 0.25;
        label.textContent = prima.toFixed(2);
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}

cargarPeriodos();

</script>
</html>