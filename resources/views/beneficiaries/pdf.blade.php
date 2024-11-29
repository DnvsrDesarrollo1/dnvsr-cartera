<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Beneficiario</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.25;
            color: #333;
            margin: 0;
            padding: 5px;

        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #4a5568;
            padding-bottom: 5px;
            position: relative;
            width: 100%;
            height: fit-content;
            background-image: radial-gradient(circle, #ff0000 1px, transparent 1px);
            background-size: 20px 20px;
        }

        h1 {
            color: #2d3748;
            font-size: 20px;
            font-weight: 700;
        }

        h2 {
            color: #4a5568;
            font-size: 16px;
            font-weight: 600;
            margin-top: 5px;
        }

        .details {
            background-color: #f7fafccc;
            border-radius: 8px;
            padding: 0.5rem;
            margin-bottom: 15px;
            overflow: hidden;
            /* Asegura que la marca de agua no se salga del div */
        }

        .info-grid {
            display: flex;
            gap: 5px;
        }

        .info-item {
            margin-bottom: 5px;
            font-size: 10px;
        }

        .info-label {
            font-weight: 600;
            color: #4a5568;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #e2e8f0;
            padding: 3px;
            text-align: left;
        }

        th {
            background-color: #edf2f7;
            font-weight: 600;
            color: #4a5568;
        }

        tr:nth-child(even) {
            background-color: #f7fafc;
        }

        .rotingtxt {
            -webkit-transform: rotate(310deg);
            -moz-transform: rotate(310deg);
            -o-transform: rotate(310deg);
            transform: rotate(310deg);
            position: absolute;
            font-size: 1em;
            color: rgba(206, 206, 206, 0.25);
            top: 16%;
            right: 37%;
            width: fit-content;
            padding: 0.3rem;
            border-radius: 5%;
            border: rgba(206, 206, 206, 0.100) solid 3px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>| PLAN DE PAGOS |</h1>
            <H2>PROGRAMA DE VIVIENDA SOCIAL Y SOLIDARIA (PVS)</H2>
            <div class="rotingtxt">
                <p>
                    AEVIVIENDA - DNVSR
                </p>
            </div>
        </div>
        <div class="details">
            <h2>Proyecto "{{ $beneficiary->proyecto }}"</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Nombre:</span> {{ $beneficiary->nombre }}
                </div>
                <div class="info-item">
                    <span class="info-label">CI:</span> {{ $beneficiary->ci }} {{ $beneficiary->complemento }}
                    {{ $beneficiary->expedido }}
                </div>
                <div class="info-item">
                    <span class="info-label">Codigo Credito:</span> {{ $beneficiary->idepro }}
                </div>
                <div class="info-item">
                    <span class="info-label">Total Activado:</span> {{ number_format($beneficiary->total_activado, 2) }}
                </div>
                <div class="info-item">
                    <span class="info-label">Otros Gastos:</span>
                    {{ number_format($beneficiary->gastos_judiciales, 2) }}
                </div>
                <div class="info-item">
                    <span class="info-label">% Interés:</span>
                    {{ number_format($beneficiary->tasa_interes) }}%
                </div>
                <div class="info-item">
                    <span class="info-label">% Seg. Desgrv:</span>
                    {{ number_format($plans->first()->prppgsegu / $beneficiary->total_activado, 5) * 100 }}%
                </div>
                <div class="info-item">
                    <span class="info-label">Fecha Activación:</span>
                    {{ date('d/m/Y', strtotime($beneficiary->fecha_activacion)) }}
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Fecha Plazo</th>
                    <th>Capital</th>
                    <th>Interés</th>
                    <th>Seguro</th>
                    <th>Otros</th>
                    <th>Total</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $saldo = $beneficiary->total_activado;
                @endphp
                @foreach ($plans as $plan)
                    @php
                        $saldo -= $plan->prppgcapi;
                    @endphp
                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td>{{ date('d/m/Y', strtotime($plan->fecha_ppg)) }}</td>
                        <td>{{ number_format($plan->prppgcapi, 2) }}</td>
                        <td>{{ number_format($plan->prppginte, 2) }}</td>
                        <td>{{ number_format($plan->prppgsegu, 2) }}</td>
                        <td>{{ $plan->prppgotro }}</td>
                        <td style="font-weight: 600;">{{ number_format($plan->prppgtota, 2) }}</td>
                        <td>{{ number_format($saldo, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Subtotales:</th>
                    <th></th>
                    <th>Cap: {{ number_format($plans->sum('prppgcapi'), 2) }}</th>
                    <th>Int: {{ number_format($plans->sum('prppginte'), 2) }}</th>
                    <th>Seg: {{ number_format($plans->sum('prppgsegu'), 2) }}</th>
                    <th>Otr: {{ number_format($plans->sum('prppgotro'), 2) }}</th>
                    <th>Total: {{ number_format($plans->sum('prppgtota'), 2) }}</th>
                    <th></th>
                </tr>
                <tr>
                    <th colspan="8">
                        @if ($plans->count() > 0)
                            <span style="font-style: italic;">
                                En caso que el dia de vencimiento sea domingo, se tomará en cuenta el siguiente dia
                                hábil.
                            </span>
                        @else
                            <span style="font-style: italic;">
                                Este beneficiario no tiene planes de pagos asociados.
                            </span>
                        @endif
                    </th>
                </tr>
                <tr>
                    <th colspan="8">
                        <span style="font-style: italic;">
                            La cuota puede variar en funcion a la tasa de la <b>prima de seguro</b>
                        </span>
                    </th>
                </tr>
            </tfoot>
        </table>
        <div style="margin-top: 5rem;" id="firmas">
            <table style="width: 50%; border: none;">
                <tr>
                    <td style="margin: auto; text-align: center; vertical-align: bottom; border: none;">
                        <div style="border-top: 1px solid #000; padding-top: 5px; margin-top: 50px;">
                            <b>{{ $beneficiary->nombre }}</b><br>
                            C.I. {{ $beneficiary->ci }} {{ $beneficiary->complemento }}
                            {{ $beneficiary->expedido }}<br>
                            DEUDOR
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
