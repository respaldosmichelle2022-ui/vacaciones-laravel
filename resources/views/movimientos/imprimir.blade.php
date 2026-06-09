<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Autorización de Vacaciones - {{ $empleado->nombre }} {{ $empleado->apellido_paterno }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            color: #000000;
            margin: 0;
            padding: 3px;
            font-size: 9.5px;
            line-height: 1.15;
        }

        .form-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 6px 12px;
            box-sizing: border-box;
            background: #fff;
        }

        .grid-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 4px;
        }

        .grid-table td {
            border: 1px solid #000;
            padding: 2.2px 5px;
            vertical-align: middle;
            box-sizing: border-box;
            word-wrap: break-word;
        }

        .title-main {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            background-color: #f2f2f2;
            height: 16px;
        }

        .title-sub {
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            height: 14px;
        }

        .title-action {
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            background-color: #f2f2f2;
            height: 14px;
        }

        .label {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .label-header {
            font-weight: bold;
            background-color: #f2f2f2;
            text-align: center;
        }

        .label-sub {
            font-weight: bold;
            background-color: #e2e8f0;
            text-align: center;
            font-size: 7.5px;
        }

        .value {
            background-color: #fff;
        }

        .value-center {
            text-align: center;
        }

        .section-title {
            font-weight: bold;
            background-color: #e2e8f0;
            text-align: center;
            padding: 2px;
            text-transform: uppercase;
            font-size: 8.5px;
        }

        .conformity-text {
            font-size: 8px;
            text-align: justify;
            padding: 3px 5px;
            background: #fafafa;
            line-height: 1.15;
        }

        .logo-cell {
            text-align: center;
            background: #fff;
        }

        .logo-img {
            max-width: 95px;
            max-height: 35px;
            object-fit: contain;
        }

        .divider {
            border-top: 1.5px dashed #000;
            margin: 8px 0;
            position: relative;
        }

        .divider::after {
            content: "✂ CORTE AQUÍ";
            position: absolute;
            top: -6px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 0 6px;
            font-size: 7.5px;
            font-weight: bold;
            color: #555;
        }

        /* Responsive */
        @media print {
            @page {
                size: letter;
                margin: 0.15in 0.2in;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
            .divider {
                border-top: 1.5px dashed #000;
                margin: 6px 0;
            }
        }

        .print-btn-container {
            text-align: center;
            margin-bottom: 8px;
            padding: 5px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .print-btn {
            background: #1e3a5f;
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .print-btn:hover {
            background: #112540;
        }
    </style>
</head>
<body>

    <div class="print-btn-container no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Imprimir Formato de Vacaciones</button>
    </div>

    @for ($copy = 1; $copy <= 2; $copy++)
        <div class="form-container">
            <table class="grid-table">
                <colgroup>
                    <col style="width: 6.66%;" span="15">
                </colgroup>
                <!-- Row 1 -->
                <tr>
                    <td class="logo-cell" colspan="3" rowspan="3">
                        <img src="{{ $logoPath }}" alt="Logo" class="logo-img">
                    </td>
                    <td colspan="12" class="title-main">DEPARTAMENTO DE RECURSOS HUMANOS</td>
                </tr>
                <!-- Row 2 -->
                <tr>
                    <td colspan="12" class="title-sub">FORMATO</td>
                </tr>
                <!-- Row 3 -->
                <tr>
                    <td colspan="12" class="title-action">AUTORIZACIÓN DE VACACIONES</td>
                </tr>
                <!-- Row 5 -->
                <tr>
                    <td colspan="3" class="label">NOMBRE DEL SOLICITANTE</td>
                    <td colspan="12" class="value" style="font-weight: bold; font-size: 10.5px;">{{ $empleado->nombre }} {{ $empleado->apellido_paterno }} {{ $empleado->apellido_materno }}</td>
                </tr>
                <!-- Row 7 -->
                <tr>
                    <td colspan="2" class="label">SUCURSAL</td>
                    <td colspan="6" class="value">{{ $empleado->sucursal ?? 'COMERCIALIZADORA MICHELLE SA DE CV' }}</td>
                    <td colspan="2" class="label">PUESTO</td>
                    <td colspan="5" class="value">{{ $empleado->puesto ?? 'N/D' }}</td>
                </tr>
                <!-- Row 9 -->
                <tr>
                    <td colspan="3" class="label">FECHA DE INGRESO</td>
                    <td colspan="4" class="value">{{ \Carbon\Carbon::parse($empleado->fecha_ingreso)->format('d/m/Y') }}</td>
                    <td colspan="3" class="label">ANTIGÜEDAD</td>
                    <td colspan="5" class="value">{{ $antiguedad }}</td>
                </tr>
                <!-- Row 10 -->
                <tr>
                    <td colspan="3" class="label">DÍAS QUE CORRESPONDEN</td>
                    <td class="value value-center" style="font-weight: bold;">{{ $diasCorresponden }}</td>
                    <td colspan="2" class="label">DÍAS DISFRUTADOS</td>
                    <td colspan="2" class="value value-center">{{ $diasDisfrutados }}</td>
                    <td colspan="2" class="label" style="color: #c2410c;">DÍAS A DISFRUTAR</td>
                    <td class="value value-center" style="font-weight: bold; color: #c2410c; background-color: #fff7ed;">{{ $movimiento->dias }}</td>
                    <td colspan="2" class="label">DÍAS PENDIENTES</td>
                    <td colspan="2" class="value value-center" style="font-weight: bold;">{{ $diasPendientes }}</td>
                </tr>
                <!-- Row 13 -->
                <tr>
                    <td colspan="3" class="label">PERIODO A DISFRUTAR (AÑO)</td>
                    <td colspan="2" class="value value-center" style="font-weight: bold;">{{ $movimiento->periodo }}</td>
                    <td colspan="2" class="label">SALARIO DIARIO</td>
                    <td colspan="3" class="value value-center" style="font-weight: bold;">$ {{ number_format($salario_diario, 2) }}</td>
                    <td colspan="2" class="label">PRIMA VACACIONAL (25%)</td>
                    <td colspan="3" class="value value-center" style="font-weight: bold; color: #15803d; background-color: #f0fdf4;">$ {{ number_format($prima_vacacional, 2) }}</td>
                </tr>
                <!-- Row 15 -->
                <tr>
                    <td colspan="15" class="section-title">Tiempo en el cual disfrutará de las vacaciones</td>
                </tr>
                <!-- Row 17 -->
                <tr>
                    <td colspan="7" class="label-header">DESDE</td>
                    <td rowspan="3" style="border: 1px solid #000; background: #f2f2f2; font-weight: bold; text-align: center; vertical-align: middle;">AL</td>
                    <td colspan="7" class="label-header">HASTA</td>
                </tr>
                <!-- Row 18 -->
                <tr>
                    <td colspan="2" class="label-sub">DÍA</td>
                    <td colspan="3" class="label-sub">MES</td>
                    <td colspan="2" class="label-sub">AÑO</td>
                    <td colspan="2" class="label-sub">DÍA</td>
                    <td colspan="3" class="label-sub">MES</td>
                    <td colspan="2" class="label-sub">AÑO</td>
                </tr>
                <!-- Row 20 -->
                <tr>
                    <td colspan="2" class="value-center" style="font-weight: bold;">{{ $inicioDia }}</td>
                    <td colspan="3" class="value-center" style="font-weight: bold;">{{ $inicioMes }}</td>
                    <td colspan="2" class="value-center" style="font-weight: bold;">{{ $inicioAnio }}</td>
                    <td colspan="2" class="value-center" style="font-weight: bold;">{{ $finDia }}</td>
                    <td colspan="3" class="value-center" style="font-weight: bold;">{{ $finMes }}</td>
                    <td colspan="2" class="value-center" style="font-weight: bold;">{{ $finAnio }}</td>
                </tr>
                <!-- Row 22 -->
                <tr>
                    <td colspan="3" class="label">OBSERVACIONES</td>
                    <td colspan="12" class="value">{{ $movimiento->observaciones ?? '' }}</td>
                </tr>
                <!-- Row 24 -->
                <tr>
                    <td colspan="15" class="conformity-text">
                        POR EL PRESENTE EXPRESO MI CONFORMIDAD DE SOLICITAR Y GOZAR MIS VACACIONES DE ACUERDO A LO QUE ESTABLECE EL ART. 76 DE LA LEY FEDERAL DEL TRABAJO, CONSIDERANDO LOS DATOS ANTES DETALLADOS.
                    </td>
                </tr>
                <!-- Signatures -->
                <tr>
                    <!-- Solicita block -->
                    <td colspan="7" rowspan="4" style="vertical-align: top; border: 1px solid #000; padding: 5px;">
                        <div style="font-weight: bold; text-align: left; margin-bottom: 12px;">SOLICITADO POR:</div>
                        <div style="border-top: 1px solid #000; text-align: center; margin-top: 16px; font-size: 8px; padding-top: 2px; font-weight: bold;">FIRMA DEL COLABORADOR</div>
                    </td>
                    <!-- Auth / VoBo header row -->
                    <td colspan="8" style="border: 1px solid #000; background: #f2f2f2; text-align: center; font-weight: bold; font-size: 7.5px; height: 12px;">COORDINADOR / GERENTE / DIRECTOR</td>
                </tr>
                <tr>
                    <!-- Auth block signature space -->
                    <td colspan="8" style="border: 1px solid #000; height: 22px; text-align: center; vertical-align: bottom;">
                        <div style="border-top: 1px dashed #777; margin: 0 10px; font-size: 7px; padding-top: 1px;">FIRMA DE AUTORIZACIÓN</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" style="border: 1px solid #000; background: #f2f2f2; text-align: center; font-weight: bold; font-size: 7.5px; height: 12px;">VoBo RECURSOS HUMANOS</td>
                </tr>
                <tr>
                    <!-- VoBo block signature space - NAME IS REMOVED -->
                    <td colspan="8" style="border: 1px solid #000; height: 22px; text-align: center; vertical-align: bottom;">
                        <div style="border-top: 1px dashed #777; margin: 0 10px; font-size: 7px; padding-top: 1px;">FIRMA</div>
                    </td>
                </tr>
                <!-- Row 34 (Names / lines) -->
                <tr>
                    <td colspan="2" class="label">NOMBRE:</td>
                    <td colspan="5" class="value" style="font-weight: bold;">{{ $empleado->nombre }} {{ $empleado->apellido_paterno }} {{ $empleado->apellido_materno }}</td>
                    <td colspan="8" style="border: 1px solid #000; font-size: 7.5px; text-align: center; font-weight: bold; background: #f2f2f2; height: 13px;">NOMBRE: _________________________________________</td>
                </tr>
                <!-- Row 42 (footer) -->
                <tr>
                    <td colspan="11" style="border: none; font-size: 7px; color: #555; text-align: left; padding-top: 4px;">
                        NOTA: ES REQUISITO ENVIAR ESTE FORMATO AL ÁREA DE RECURSOS HUMANOS
                    </td>
                    <td colspan="4" style="border: none; font-size: 7px; font-weight: bold; text-align: right; padding-top: 4px;">
                        FO-REH-33 (01)
                    </td>
                </tr>
            </table>
        </div>

        @if ($copy == 1)
            <div class="divider"></div>
        @endif
    @endfor

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Auto trigger print in a short delay
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
