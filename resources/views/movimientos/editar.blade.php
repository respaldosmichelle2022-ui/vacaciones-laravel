@extends('layouts.app')

@section('contenido')

<a href="/movimientos" class="boton-volver">
    ← Regresar
</a>

<h1>Editar movimiento de vacaciones</h1>

@if(session('error'))
    <div class="alerta-success" style="background:#f8d7da; color:#721c24; border-color:#f5c6cb;">
        {{ session('error') }}
    </div>
@endif

<form action="/movimientos/actualizar/{{ $movimiento->id }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grupo">
        <label>Empleado</label>
        <!-- Mostramos el empleado pero deshabilitado para evitar inconsistencias -->
        <input type="text" value="{{ $movimiento->empleado->numero_empleado }} - {{ $movimiento->empleado->nombre }} {{ $movimiento->empleado->apellido_paterno }}" disabled>
        <input type="hidden" name="empleado_id" id="empleadoId" value="{{ $movimiento->empleado_id }}">
    </div>

    <div class="grupo">
        <label>Periodo</label>
        <select name="periodo" id="periodoSelect" required>
            @foreach($periodosDisponibles as $periodo)
                <option value="{{ $periodo->periodo }}" {{ $movimiento->periodo == $periodo->periodo ? 'selected' : '' }}>
                    {{ $periodo->periodo }}
                </option>
            @endforeach
        </select>

        <div id="diasDisponiblesContainer" style="margin-top: 10px; font-weight: bold; color: #1e3a5f;">
            Días disponibles (incluyendo este movimiento): <span id="lblDiasDisponibles">0</span>
        </div>
    </div>

    <div class="grupo">
        <label>Fecha inicio</label>
        <input type="date"
               name="fecha_inicio"
               id="fechaInicioInput"
               value="{{ $movimiento->fecha_inicio }}"
               required>
    </div>

    <div class="grupo">
        <label>Fecha fin</label>
        <input type="date"
               name="fecha_fin"
               id="fechaFinInput"
               value="{{ $movimiento->fecha_fin }}"
               required>

        <div id="diasDescontarContainer" style="margin-top: 10px; font-weight: bold; color: #d9534f; display: none;">
            Días a descontar: <span id="lblDiasDescontar">0</span>
        </div>
    </div>

    <button type="submit">
        Actualizar movimiento
    </button>
</form>

<script>
// Guardamos los días restantes correspondientes a cada periodo para este empleado,
// sumándole temporalmente los días que ya tiene ocupados este movimiento para que el usuario
// pueda ver su saldo real disponible si reajusta las fechas del mismo movimiento.
let periodosSaldos = @json($saldosConDiasRevertidos);
let diasActualesMovimiento = {{ $movimiento->dias }};
let periodoOriginal = "{{ $movimiento->periodo }}";

document.getElementById('periodoSelect').addEventListener('change', actualizarDiasDisponibles);
document.getElementById('fechaInicioInput').addEventListener('input', calcularDiasDescontar);
document.getElementById('fechaFinInput').addEventListener('input', calcularDiasDescontar);

function actualizarDiasDisponibles()
{
    let periodoSelect = document.getElementById('periodoSelect');
    let periodo = periodoSelect.value;
    let container = document.getElementById('diasDisponiblesContainer');
    let label = document.getElementById('lblDiasDisponibles');

    if (periodo && periodosSaldos[periodo] !== undefined) {
        label.textContent = periodosSaldos[periodo];
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
        } else {
            container.style.display = 'none';
        }
    } else {
        container.style.display = 'none';
    }
}

// Inicializar vistas
actualizarDiasDisponibles();
calcularDiasDescontar();
</script>

@endsection
