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
            padding: 5px;
            font-size: 11px;
            line-height: 1.2;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            padding: 5px 0;
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

        .description>tbody>tr {
            border-bottom: 1px solid #eee;
        }

        .plans {
            margin-top: 15px;
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
            background: #bebebe;
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

        .description {
            border-collapse: collapse;
            width: 100%;
        }

        .description th {
            font-size: 11px;
            font-weight: bold;
            text-align: left;
        }

        .description td {
            font-size: 10px;
        }

        .description tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .description tfoot {
            border-top: 2px solid #ddd;
            font-weight: bold;
        }

        .description tfoot td {
            font-size: 11px;
        }
    </style>
</head>

<body>
    @php
        $saldo = $beneficiary->monto_activado;
    @endphp
    <div class="container">
        <header class="header">
            <img src="{{ public_path('assets/main_ico.png') }}" alt="Logo">
            <h1>AEVIVIENDA</h1>
            <h2>AGENCIA ESTATAL DE VIVIENDA</h2>
            <h2>PROGRAMA DE VIVIENDA SOCIAL Y SOLIDARIA - PVS</h2>
            <hr />
            <h1>EXTRACTO DE PAGOS</h1>
            <hr />
        </header>
        <div class="details">
            <table class="description">
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Total Activado</td>
                        <td style="font-weight: 800;">Bs.
                            {{-- {{ number_format($beneficiary->saldo_credito + $beneficiary->monto_recuperado, 2) }}</td> --}}
                            {{ number_format($beneficiary->monto_credito, 2) }}
                    </tr>
                    <tr>
                        <td>Proyecto:</td>
                        <td>{{ $beneficiary->proyecto }}</td>
                        <td></td>
                        <td>Fecha Emision:</td>
                        <td>{{ date('Y-m-d', strtotime(now())) }}</td>
                    </tr>
                    <tr>
                        <td>Codigo Prestamo:</td>
                        <td>{{ $beneficiary->idepro }}</td>
                        <td></td>
                        <td>Monto Migrado:</td>
                        <td>Bs. {{ number_format($beneficiary->total_activado, 2) }}</td>
                    </tr>
                    <tr>
                        <td>CI:</td>
                        <td>{{ $beneficiary->ci }}</td>
                        <td></td>
                        <td>Monto Restructuración</td>
                        <td>Bs. {{ number_format($beneficiary->monto_activado, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Nombres:</td>
                        <td>{{ $beneficiary->nombre }}</td>
                        <td></td>
                        <td>Saldo Credito</td>
                        <td>Bs.
                            {{ number_format($beneficiary->saldo_credito > 0 ? $beneficiary->saldo_credito : 0, 2) }}
                        </td>
                    </tr>
                    @if ($beneficiary->saldo_credito < 0)
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Fondos a Devolver:</td>
                            <td>Bs.
                                {{ number_format($beneficiary->saldo_credito * -1, 2) }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td>Moneda:</td>
                        <td>BOLIVIANOS</td>
                        <td></td>
                        <td>Total Recuperado</td>
                        <td>Bs.
                            {{ number_format($beneficiary->saldo_credito <= 0 ? $beneficiary->monto_credito : $beneficiary->monto_recuperado, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Interes/Seguro Devengados: <br>
                            Cuotas/Capital/Interes Diferidos:
                        </td>
                        <td>
                            Bs.
                            {{ number_format($devengadoInt ?? 0, 2) }}
                            /
                            Bs.
                            {{ number_format($devengadoSeg ?? 0, 2) }}
                            <br>
                            {{ $beneficiary->helpers()->count() ?? 0 }} / Bs.
                            {{ number_format($diferidoCap ?? 0, 2) }} /
                            Bs. {{ number_format($diferidoInt ?? 0, 2) }}
                        </td>
                        <td></td>
                        <td>Gastos Jud - Adm: </td>
                        <td>Bs.
                            {{ number_format($gastos ?? 0, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Cuotas Pendientes por Cancelar:</td>
                        <td>{{ $beneficiary->getCurrentPlan('CANCELADO', '!=')->count() }} <b>de</b>
                            {{ $beneficiary->getCurrentPlan('INACTIVO', '!=')->count() }}</td>
                        <td></td>
                        <td>Estado:</td>
                        <td>{{ $beneficiary->estado }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-responsive">
            <table class="description" style="width: 100%;">
                <thead>
                    <tr>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            #
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Fecha
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            N° Cuota
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Comprobante
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Capital
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Intereses
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Seguros
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Otros Cargos
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Monto Total
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Saldo
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Observaciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($noLegacy as $v)
                        <tr>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #a5a4a47a;">
                                {{ $loop->iteration }}</td>
                            <td
                                style="text-align: center; padding: 4px; border-bottom: 1px solid #a5a4a47a; font-size: 9px;">
                                {{ \Carbon\Carbon::parse($v->fecha_pago)->format('d/m/Y') }} <br />
                                {{ \Carbon\Carbon::parse($v->hora_pago)->format('H:i') }}
                            </td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #a5a4a47a;">
                                {{ $v->numpago }}</td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #a5a4a47a;">
                                {{ $v->numtramite }}</td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #a5a4a47a;">
                                @php
                                    $cd = $v
                                        ->payments()
                                        ->where('prtdtnpag', $v->numpago)
                                        ->where('prtdtdesc', 'LIKE', '%DIFER%')
                                        ->sum('montopago');
                                @endphp
                                {{ number_format(
                                    $v->payments()->where('prtdtnpag', $v->numpago)->where('prtdtdesc', 'LIKE', 'CAPI%')->where('prtdtdesc', 'NOT LIKE', '%DIFER%')->sum('montopago') +
                                        $v->payments()->where('prtdtnpag', $v->numpago)->where('prtdtdesc', 'LIKE', 'AMR%')->sum('montopago'),
                                    2,
                                ) . ($cd > 0 ? ' + ' . $cd : '') }}
                            </td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #a5a4a47a;">
                                {{ number_format($v->payments()->where('prtdtnpag', $v->numpago)->where('prtdtdesc', 'LIKE', 'INTE%')->sum('montopago'), 2) }}
                            </td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #a5a4a47a;">
                                {{ number_format($v->payments()->where('prtdtnpag', $v->numpago)->where('prtdtdesc', 'LIKE', 'SEGU%')->sum('montopago'), 2) }}
                            </td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #a5a4a47a;">
                                {{ number_format($v->payments()->where('prtdtnpag', $v->numpago)->where('prtdtdesc', 'LIKE', 'OTR%')->sum('montopago'), 2) }}
                            </td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #a5a4a47a;">
                                {{ number_format($v->montopago, 2) }}
                            </td>
                            @php
                                $saldo < 0
                                    ? ($saldo -= $v
                                            ->payments()
                                            ->where('prtdtdesc', 'NOT LIKE', '%DIF%')
                                            ->where('prtdtdesc', 'LIKE', 'CAPI%')
                                            ->where('prtdtnpag', $v->numpago)
                                            ->sum('montopago')) * -1
                                    : ($saldo -= $v
                                        ->payments()
                                        ->where('prtdtdesc', 'NOT LIKE', '%DIF%')
                                        ->where('prtdtdesc', 'LIKE', 'CAPI%')
                                        ->where('prtdtnpag', $v->numpago)
                                        ->sum('montopago'));
                            @endphp
                            <td
                                style="text-align: center; font-weight: 800;padding: 4px; border-bottom: 1px solid #a5a4a49d;">
                                {{ number_format($saldo, 2) }}
                            </td>
                            <td
                                style="text-align: center; padding: 4px; border-bottom: 1px solid #a5a4a47a; font-size: 8px;">
                                {{ $v->obs_pago }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 10px;">No hay pagos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"
                            style="text-align: right; padding: 8px; font-weight: bold; background-color: #cbcbcb;">
                            Capital Pagado:
                        </td>
                        <td style="text-align: right; padding: 8px; font-weight: bold;">
                            {{ number_format(
                                $beneficiary->payments()->where('prtdtdesc', 'LIKE', 'CAPI%')->where(function ($query) {
                                        $query->whereNull('observacion')->orWhere('observacion', '')->orWhere('observacion', '!=', 'LEGACY 22/24');
                                    })->sum('montopago'),
                                2,
                            ) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10" style="text-align: right; padding: 8px; font-weight: bold;">Saldo Credito:
                        </td>
                        <td style="text-align: right; padding: 8px; font-weight: bold;">
                            {{ number_format(($beneficiary->saldo_credito == 0 ? 0 : $saldo) + $beneficiary->helpers->where('estado', 'ACTIVO')->sum('capital') ?? 0, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10" style="text-align: right; padding: 8px; font-weight: bold;">Monto en
                            Diferimientos:
                        </td>
                        <td style="text-align: right; padding: 8px; font-weight: bold;">
                            {{ number_format($beneficiary->helpers->where('estado', 'ACTIVO')->sum('capital') ?? 0, 2) }}
                        </td>
                    </tr>
                    <tr style="border-top: 3px black solid">
                        <td colspan="10" style="text-align: right; padding: 8px; font-weight: bold;">Saldo SIN
                            Diferimientos:
                        </td>
                        <td style="text-align: right; padding: 8px; font-weight: bold;">
                            {{ number_format($beneficiary->saldo_credito, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <hr />

        <h1>Historial 2022 / 2024:</h1>

        <div class="table-responsive">
            <table class="description" style="width: 100%;">
                <thead>
                    <tr>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            #
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Fecha
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            N° Cuota
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Comprobante
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Capital
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Intereses
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Seguros
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Otros Cargos
                        </th>
                        <th
                            style="text-align: right; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Monto Total
                        </th>
                        <th
                            style="text-align: center; padding: 8px; background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            Observaciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($legacy as $l)
                        <tr>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #eee;">
                                {{ $loop->iteration }}</td>
                            <td
                                style="text-align: center; padding: 4px; border-bottom: 1px solid #eee; font-size: 9px;">
                                {{ \Carbon\Carbon::parse($l->fecha_pago)->format('d/m/Y') }} <br />
                                {{ \Carbon\Carbon::parse($l->hora_pago)->format('H:i') }}
                            </td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #eee;">
                                {{ $l->numpago }}</td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #eee;">
                                {{ $l->numtramite }}</td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #eee;">
                                {{ number_format(
                                    $l->payments()->where('prtdtnpag', $l->numpago)->where('prtdtdesc', 'LIKE', 'CAPI%')->sum('montopago') +
                                        $l->payments()->where('prtdtnpag', $l->numpago)->where('prtdtdesc', 'LIKE', 'AMR%')->sum('montopago'),
                                    2,
                                ) }}
                            </td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #eee;">
                                {{ number_format($l->payments()->where('prtdtnpag', $l->numpago)->where('prtdtdesc', 'LIKE', 'INTE%')->sum('montopago'), 2) }}
                            </td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #eee;">
                                {{ number_format($l->payments()->where('prtdtnpag', $l->numpago)->where('prtdtdesc', 'LIKE', 'SEGU%')->sum('montopago'), 2) }}
                            </td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #eee;">
                                {{ number_format($l->payments()->where('prtdtnpag', $l->numpago)->where('prtdtdesc', 'LIKE', 'OTR%')->sum('montopago'), 2) }}
                            </td>
                            <td style="text-align: center; padding: 4px; border-bottom: 1px solid #eee;">
                                {{ number_format($l->montopago, 2) }}
                            </td>
                            <td
                                style="text-align: center; padding: 4px; border-bottom: 1px solid #eee; font-size: 8px;">
                                {{ $l->obs_pago }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 10px;">No hay pagos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"
                            style="text-align: right; padding: 8px; font-weight: bold; background-color: #cbcbcb;">
                            Capital Pagado:
                        </td>
                        <td style="text-align: right; padding: 8px; font-weight: bold;">
                            {{ number_format($beneficiary->payments()->where('observacion', 'LIKE', '%LEGACY%')->where('prtdtdesc', 'LIKE', 'CAPI%')->sum('montopago'), 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- ... existing footer code ... -->

        <div class="signatures">
            <div class="signature-line">
                <span>__________________________</span>
                <p>Firma del Beneficiario</p>
            </div>
        </div>

        <div class="footer">
            <p>Este es un documento generado automáticamente y no requiere firma. <span
                    style="color: #cecece; font-size: 8px;"></span></p>
        </div>
    </div>
</body>

</html>
