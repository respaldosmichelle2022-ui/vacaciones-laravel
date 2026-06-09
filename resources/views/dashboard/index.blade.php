@extends('layouts.app')

@section('contenido')

<style>

.titulo{

    margin-bottom:30px;
}

.cards{

    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
}

.card-dashboard{

    background:#f8fafc;
    padding:30px;
    border-radius:12px;
    box-shadow:0 4px 20px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
}

.card-dashboard h2{

    font-size:40px;
    margin-bottom:10px;
    color:#2563eb !important;
}

.card-dashboard p{

    font-size:18px;
    color:#475569;
}

</style>

<div class="titulo">

    <h1>Dashboard RH</h1>

</div>

<div class="cards">

    <div class="card-dashboard">

        <h2>

            {{ $totalEmpleados }}

        </h2>

        <p>

            Empleados registrados

        </p>

    </div>

    <div class="card-dashboard">

        <h2>

            {{ $totalSaldos }}

        </h2>

        <p>

            Saldos de vacaciones

        </p>

    </div>

    <div class="card-dashboard">

        <h2>

            {{ $totalMovimientos }}

        </h2>

        <p>

            Movimientos registrados

        </p>

    </div>

</div>

@if(isset($cumpleanosProximos) && count($cumpleanosProximos) > 0)
    <!-- Modal de Cumpleaños -->
    <div id="modalCumpleanos" class="modal" style="display: flex; position: fixed; z-index: 1050; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); justify-content: center; align-items: center;">
        <div style="background-color: white; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid #e2e8f0; width: 90%; max-width: 600px; padding: 25px; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px;">
                <h3 style="font-weight: 700; color: #0f172a; margin: 0; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                    <span>🎉</span> Próximos Cumpleaños
                </h3>
                <button onclick="document.getElementById('modalCumpleanos').style.display='none'" style="background: transparent; border: none; font-size: 22px; font-weight: 700; color: #94a3b8; cursor: pointer; transition: color 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">&times;</button>
            </div>
            
            <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">
                Los siguientes colaboradores cumplirán años en los próximos {{ \App\Models\Setting::getVal('birthday_alert_days', 7) }} días:
            </p>

            <div style="max-height: 300px; overflow-y: auto; padding-right: 5px;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; font-size: 12px; color: #64748b; text-transform: uppercase;">
                            <th style="padding: 10px 5px; font-weight: 700; background: #f8fafc; color: #64748b; border: none; border-bottom: 2px solid #e2e8f0;">Colaborador</th>
                            <th style="padding: 10px 5px; text-align: center; font-weight: 700; background: #f8fafc; color: #64748b; border: none; border-bottom: 2px solid #e2e8f0;">Fecha Nac.</th>
                            <th style="padding: 10px 5px; text-align: center; font-weight: 700; background: #f8fafc; color: #64748b; border: none; border-bottom: 2px solid #e2e8f0;">Edad</th>
                            <th style="padding: 10px 5px; text-align: center; font-weight: 700; background: #f8fafc; color: #16a34a; border: none; border-bottom: 2px solid #e2e8f0;">Días Faltantes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cumpleanosProximos as $cumple)
                            <tr style="border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #334155;">
                                <td style="padding: 12px 5px; background: white; border: none; border-bottom: 1px solid #f1f5f9;">
                                    <strong style="color: #0f172a; display: block;">{{ $cumple['nombre_completo'] }}</strong>
                                    <small style="color: #64748b;">{{ $cumple['puesto'] }} ({{ $cumple['sitio'] }})</small>
                                </td>
                                <td style="padding: 12px 5px; text-align: center; background: white; border: none; border-bottom: 1px solid #f1f5f9;">{{ $cumple['fecha_nacimiento'] }}</td>
                                <td style="padding: 12px 5px; text-align: center; font-weight: 600; color: #2563eb; background: white; border: none; border-bottom: 1px solid #f1f5f9;">{{ $cumple['edad_nueva'] }} años</td>
                                <td style="padding: 12px 5px; text-align: center; font-weight: 600; color: {{ $cumple['dias_restantes'] == 0 ? '#ef4444' : '#16a34a' }}; background: white; border: none; border-bottom: 1px solid #f1f5f9;">
                                    {{ $cumple['dias_restantes'] == 0 ? '¡Hoy! 🎂' : "En {$cumple['dias_restantes']} día(s)" }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 25px; border-top: 1px solid #f1f5f9; padding-top: 15px; text-align: right;">
                <button onclick="document.getElementById('modalCumpleanos').style.display='none'" class="boton" style="padding: 9px 20px; font-size: 13px; display: inline-flex; height: auto;">Entendido</button>
            </div>
        </div>
    </div>
@endif

@endsection