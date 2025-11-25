<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Beneficiario</title>
    <style>
        * {
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            padding: 3px;
            font-size: 11px;
            line-height: 1.2;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            padding: 10px 0;
            display: flex;
            align-items: center;
        }

        .header img {
            position: absolute;
            top: 5px;
            left: 5px;
            height: 72px;
            width: auto;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
        }

        .header h2 {
            margin: 2px 0;
            font-size: 14px;
        }

        .info-grid {
            margin: 5px 0;
        }

        .info-item {
            padding: 3px 5px;
            background: #f9f9f9;
            border-bottom: 1px solid #eee;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }

        .info-value {
            display: inline-block;
        }

        .signatures {
            margin-top: 30px;
            gap: 30px;
        }

        .signature-line {
            margin-top: 30px;
            padding-top: 5px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            font-size: 10px;
        }

        @media print {
            body {
                padding: 0;
            }
        }

        .description {
            width: 100%;
            border-collapse: collapse;
        }

        .description>thead>th {}

        .description>tbody>tr {
            border-bottom: 1px solid #eee;
        }

        .plans {
            margin-top: 5px;
        }

        .plans table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .plans th {
            background: #f5f5f5;
            color: #333;
            font-weight: normal;
            padding: 5px;
            text-align: right;
            border-bottom: 2px solid #ddd;
        }

        .plans td {
            padding: 4px 5px;
            text-align: right;
            border-bottom: 1px solid #eee;
        }

        .plans tr:nth-child(even) {
            background: #fafafa;
        }

        .plans td:first-child,
        .plans th:first-child {
            text-align: center;
        }

        .plans td:nth-child(2),
        .plans th:nth-child(2) {
            text-align: left;
        }

        .plans .total {
            font-weight: 600;
            color: #2d3748;
        }

        .plans .saldo {
            color: #718096;
        }

        #footer {
            position: fixed;
            right: 20px;
            bottom: 0;
            text-align: center;
        }

        #footer .page:after {
            content: counter(page);
        }
    </style>
</head>

@if ($beneficiary->estado == 'BLOQUEADOx' || $beneficiary->estado == 'CANCELADOx')
    <h1>Estado de credito inválido, informacion no disponible</h1>
@else

    <body>
        @php
            $saldo = $plans->sum('prppgcapi');
            $tasa = 0;
            if ($beneficiary->insurance()->exists()) {
                $tasa = $beneficiary->insurance->tasa_seguro;
            }
            if ($tasa == 0) {
                $tasa = $plans->isNotEmpty() ? ($plans->first()->prppgsegu / $plans->sum('prppgcapi')) * 100 : 0;
            }
            $tasa = number_format($tasa, 3);
        @endphp
        <div id="footer">
            <strong>
                <p class="page">Página: </p>
            </strong>
        </div>
        <div class="container">
            <header class="header">
                <img src="{{ public_path('assets/main_ico.png') }}" alt="Logo">
                <h1>AEVIVIENDA</h1>
                <h2>AGENCIA ESTATAL DE VIVIENDA</h2>
                <h2>PROGRAMA DE VIVIENDA SOCIAL Y SOLIDARIA - PVS</h2>
                <hr />
                <h1>PLAN DE PAGOS</h1>
                <hr />
                <p style="text-align: center; font-size: 8px; padding: 0; margin: 0;">
                    <b>CANALES HABILITADOS:</b> BANCO SOL <b>|</b> SINTESIS MIS-CUENTAS (APP MOVIL) <b>|</b> COOP. SAN
                    MARTIN DE PORRES <b>|</b> BANCO PYME ECOFUTURO <b>|</b> CRECER IFD <b>|</b> DIACONIA
                </p>
                <hr />
            </header>
            <div class="details">
                <table class="description">
                    <thead>
                        <th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Proyecto:</td>
                            <td>{{ $beneficiary->proyecto }}</td>
                            <td></td>
                            <td>{{-- Fecha Emision: --}}</td>
                            <td>{{-- {{ date('d/m/Y', strtotime(now())) }} --}}</td>
                        </tr>
                        <tr>
                            <td>C.I. / Codigo Prestamo:</td>
                            <td>{{ $beneficiary->ci }} / {{ $beneficiary->idepro }}</td>
                            <td></td>
                            <td>Monto Activado:</td>
                            <td>Bs. {{ number_format($beneficiary->monto_credito, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Nombres:</td>
                            <td>{{ $beneficiary->nombre }}</td>
                            <td></td>
                            <td>Fecha Activacion:</td>
                            <td>{{ date('d/m/Y', strtotime($beneficiary->fecha_activacion)) }}</td>
                        </tr>
                        <tr>
                            <td>Moneda:</td>
                            <td>BOLIVIANOS</td>
                            <td></td>
                            <td>Fecha Re-estructuracion:</td>
                            <td>
                                {{ $beneficiary->fecha_extendida ? date('m/Y', strtotime($beneficiary->fecha_extendida)) : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td>Seguro Desgravamen:</td>
                            <td>
                                {{ number_format($tasa, 2) }} %
                            </td>
                            <td></td>
                            <td>Fecha Vencimiento del Plan:</td>
                            <td>
                                {{ date('d/m/Y', strtotime($beneficiary->fecha_activacion . ' + 20 years')) }}

                            </td>
                        </tr>
                        <tr>
                            <td>Periodicidad de pago:</td>
                            <td>MENSUAL</td>
                            <td></td>
                            <td>Plazo de operacion:</td>
                            <td>{{ $plans->count() }}</td>
                        </tr>
                        <tr>
                            <td>
                                Interes/Seguro Devengados: <br>
                                Cuotas/Capital/Interes Diferidos:
                            </td>
                            <td>
                                Bs.
                                {{ number_format(
                                    \App\Models\Earn::where('idepro', $beneficiary->idepro)->where('estado', 'ACTIVO')->sum('interes') ?? 0,
                                    2,
                                ) }}
                                /
                                Bs.
                                {{ number_format(\App\Models\Earn::where('idepro', $beneficiary->idepro)->where('estado', 'ACTIVO')->sum('seguro') ?? 0, 2) }}
                                <br>
                                {{ $differs->count() ?? 0 }} / Bs.
                                {{ number_format($differs->sum('capital') ?? 0, 2) }} /
                                Bs. {{ number_format($differs->sum('interes') ?? 0, 2) }}
                            </td>
                            <td></td>
                            <td>Saldo "{{ $beneficiary->entidad_financiera }}": <br /> Gastos Jud - Adm: </td>
                            <td>Bs.
                                {{ number_format($beneficiary->total_activado, 2) }}
                                {{-- {{ number_format($beneficiary->saldo_credito, 2) }} --}}
                                <br />
                                Bs.
                                {{ number_format($beneficiary->spends()->where('estado', 'ACTIVO')->sum('monto') ?? 0, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Tasa de interes:</td>
                            <td>{{ $beneficiary->tasa_interes }} %</td>
                            <td></td>
                            <td>
                                <b>Saldo:</b>
                            </td>
                            <td>Bs.
                                {{ number_format($plans->sum('prppgcapi') + ($differs->sum('capital') ?? 0), 2) ?? 0 }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="plans">
                <table>
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Fecha Plazo</th>
                            <th>Capital</th>
                            <th>Interés</th>
                            <th>Seguro</th>
                            <th>Otros</th>
                            <th>Interes Devengado</th>
                            <th>Seguro Devengado</th>
                            <th>Total</th>
                            <th style="text-align: center;">
                                Saldo
                                <br>
                                <i>({{ number_format($plans->sum('prppgcapi'), 2) }})</i>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($plans as $plan)
                            @php
                                $saldo -= $plan->prppgcapi;
                            @endphp
                            <tr>
                                <td>
                                    @can('write plans')
                                        <a style="text-decoration: none; font-weight: 600; @if ($plan->estado == 'CANCELADO') color: green; @else color: black @endif"
                                            title="Click para cambiar estado a CANCELADO/ACTIVO"
                                            href="{{ route('beneficiario.pdf.switch-status', [$beneficiary->ci, $plan]) }}">
                                            {{ $plan->prppgnpag }}
                                        </a>
                                    @else
                                        <span
                                            style="font-weight: 600; @if ($plan->estado == 'CANCELADO') color: green; @else color: black @endif">
                                            {{ $plan->prppgnpag }}
                                        </span>
                                    @endcan
                                </td>
                                <td>{{ date('d/m/Y', strtotime($plan->fecha_ppg)) }}</td>
                                <td>{{ number_format($plan->prppgcapi, 2) }}</td>
                                <td>{{ number_format($plan->prppginte, 2) }}</td>
                                <td>{{ number_format($plan->prppgsegu, 2) }}</td>
                                <td>{{ number_format($plan->prppgotro, 2) }}</td>
                                <th>{{ number_format($plan->prppggral, 2) }}</th>
                                <th>{{ number_format($plan->prppgcarg, 2) }}</th>
                                <td style="font-weight: 600;">{{ number_format($plan->prppgtota, 2) }}</td>
                                <td>{{ number_format($saldo, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="text-align: center;">No hay planes de pagos asociados</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="padding: 1rem;">Totales:</th>
                            <th style="padding: 1rem;"></th>
                            <th style="font-weight: 800; padding: 0.5rem;">
                                Cap: {{ number_format($plans->sum('prppgcapi'), 2) }}
                            </th>
                            <th style="font-weight: 800; padding: 0.5rem;">
                                Int: {{ number_format($plans->sum('prppginte'), 2) }}
                            </th>
                            <th style="font-weight: 800; padding: 0.5rem;">
                                Seg: {{ number_format($plans->sum('prppgsegu'), 2) }}
                            </th>
                            <th style="font-weight: 800; padding: 0.5rem;">
                                Otr: {{ number_format($plans->sum('prppgotro'), 2) }}
                            </th>
                            <th style="font-weight: 800; padding: 0.5rem;">
                                Int. Dev: {{ number_format($plans->sum('prppggral'), 2) }}
                            </th>
                            <th style="font-weight: 800; padding: 0.5rem;">
                                Seg. Dev: {{ number_format($plans->sum('prppgcarg'), 2) }}
                            </th>
                            <th style="font-weight: 800; padding: 0.5rem;">
                                Total: {{ number_format($plans->sum('prppgtota'), 2) }}
                            </th>
                            <th style="font-weight: 800; padding: 0.5rem;">
                                Saldo: {{ number_format($saldo, 2) }}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="10">
                                @if ($plans->count() > 0)
                                    <span style="font-style: italic;">
                                        En caso que el dia de vencimiento sea fin de semana, se tomará en cuenta el
                                        siguiente dia
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
                            <th colspan="10">
                                <span style="font-style: italic;">
                                    La cuota puede variar en funcion a la tasa de la <b>prima de seguro</b>
                                </span>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @isset($differs)
                <hr style="margin-top: 5px;" />
                <h3 style="text-align: center;">CUOTAS DIFERIDAS</h3>
                <hr />
                <div class="plans">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                {{-- <th>N°</th> --}}
                                <th>Fecha</th>
                                <th>Capital</th>
                                <th>Interés</th>
                                <th>Seguro</th>
                                <th>Otros</th>
                                <th>Total</th>
                                <th style="text-align: center;">
                                    Saldo
                                    <br>
                                    ({{ number_format($differs->sum('capital') + $differs->sum('interes') + $differs->sum('seguro') + $differs->sum('otros'), 2) }})
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $dif = $differs->sum('capital') + $differs->sum('interes');
                            @endphp
                            @forelse ($differs as $differ)
                                @php
                                    $dif -= $differ->capital + $differ->interes;
                                @endphp
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    {{-- <td>{{ $differ->indice }}</td> --}}
                                    <td>{{ date('d/m/Y', strtotime($differ->vencimiento)) }}</td>
                                    <td>{{ number_format($differ->capital, 2) }}</td>
                                    <td>{{ number_format($differ->interes, 2) }}</td>
                                    <td>{{ number_format($differ->seguro, 2) }}</td>
                                    <td>{{ number_format($differ->otros, 2) }}</td>
                                    <td>{{ number_format($differ->capital + $differ->interes + $differ->seguro + $differ->otros, 2) }}
                                    </td>
                                    <td>{{ number_format($dif, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" style="text-align: center;">No hay cuotas diferidas asociadas</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th style="font-weight: 800;">Totales:</th>
                                <th style="font-weight: 800;">{{ number_format($differs->sum('capital'), 2) }}</th>
                                <th style="font-weight: 800;">{{ number_format($differs->sum('interes'), 2) }}</th>
                                <th style="font-weight: 800;">{{ number_format($differs->sum('seguro'), 2) }}</th>
                                <th style="font-weight: 800;">{{ number_format($differs->sum('otros'), 2) }}</th>
                                <th style="font-weight: 800;">
                                    {{ number_format($differs->sum('capital') + $differs->sum('interes') + $differs->sum('seguro') + $differs->sum('otros'), 2) }}
                                </th>
                                <th>{{ number_format($dif, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endisset

            <div style="margin-top: 5rem;" id="firmas">
                <table style="width: 50%; border: none;">
                    <tr>
                        <td style="margin: auto; text-align: center; vertical-align: bottom; border: none;">
                            <div style="border-top: 1px solid #000; padding-top: 5px; margin-top: 50px;">
                                <b>{{ $beneficiary->nombre }}</b><br>
                                C.I. {{ $beneficiary->ci }} {{ $beneficiary->complemento }}
                                {{ $beneficiary->expedido }}<br>
                                DEUDOR<br>
                                <span style="color: #cecece; font-size: 8px"></span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
@endif

</html>
