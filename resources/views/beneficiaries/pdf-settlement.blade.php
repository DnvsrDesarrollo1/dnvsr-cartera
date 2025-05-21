<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquidación</title>
    <style>
        * {
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            padding: 10px;
            font-size: 11px;
            line-height: 1.2;
        }

        .container {
            display: grid;
            grid-template-rows: auto 1fr auto;
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

        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header class="header">
            <img src="{{ public_path('assets/main_ico.png') }}" alt="Logo">
            <h1>AEVIVIENDA</h1>
            <h2>AGENCIA ESTATAL DE VIVIENDA</h2>
            <h2>PROGRAMA DE VIVIENDA SOCIAL Y SOLIDARIA - PVS</h2>
            <hr />
            <h1>FORMULARIO DE LIQUIDACION</h1>
            <hr />
        </header>

        <main>

            <h2>LIQUIDACION DE CREDITO</h2>
            <table border="1" cellpadding="5" cellspacing="0"
                style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td><strong>Nombre del Beneficiario:</strong></td>
                    <td>{{ $settlement->beneficiary->nombre }}</td>
                </tr>
                <tr>
                    <td><strong>C.I. del Beneficiario:</strong></td>
                    <td>{{ $settlement->beneficiary->ci }}</td>
                </tr>
                <tr>
                    <td><strong>Entidad Financiera del Crédito:</strong></td>
                    <td>{{ $settlement->beneficiary->entidad_financiera }}</td>
                </tr>
                <tr>
                    <td><strong>Total del Crédito:</strong></td>
                    <td>{{ $settlement->beneficiary->monto_credito }}</td>
                </tr>
                <tr>
                    <td><strong>Fecha de Liquidación:</strong></td>
                    <td>{{ $settlement->created_at }}</td>
                </tr>
                <tr>
                    <td><strong>Fecha de Aprobación:</strong></td>
                    <td>{{ $settlement->updated_at }}</td>
                </tr>
                <tr>
                    <td><strong>Fecha de Vencimiento:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($settlement->updated_at)->addDays(5) }}</td>
                </tr>
                <tr>
                    <td><strong>Estado de la Liquidación:</strong></td>
                    <td>{{ strtoupper($settlement->estado) }}</td>
                </tr>
                @if ($settlement->anexos != null)
                    <tr>
                        <td colspan="2"><strong>La presente liquidación cuenta con documentos anexos al
                                informe.</strong></td>
                    </tr>
                @endif
            </table>

            <h2>DETALLE DE LIQUIDACION</h2>
            <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            Saldo a Capital
                        </td>
                        <td>
                            Bs. {{ number_format($settlement->capital_final, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Capital Diferido
                        </td>
                        <td>
                            Bs. {{ number_format($settlement->capital_diferido, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Interes
                        </td>
                        <td>
                            Bs. {{ number_format($settlement->interes, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Interes Devengado
                        </td>
                        <td>
                            Bs. {{ number_format($settlement->interes_devengado, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Interes Diferido</td>
                        <td>Bs. {{ number_format($settlement->interes_diferido, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Seguro</td>
                        <td>Bs. {{ number_format($settlement->seguro, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Seguro Devengado</td>
                        <td>Bs. {{ number_format($settlement->seguro_devengado, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Gastos Judiciales</td>
                        <td>Bs. {{ number_format($settlement->gastos_judiciales, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Gastos Administrativos</td>
                        <td>Bs. {{ number_format($settlement->gastos_administrativos, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Otros</td>
                        <td>Bs. {{ number_format($settlement->otros, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <h2>TOTAL A LIQUIDAR</h2>
            @php
                $total =
                    $settlement->capital_final +
                    $settlement->capital_diferido +
                    $settlement->interes +
                    $settlement->interes_devengado +
                    $settlement->interes_diferido +
                    $settlement->seguro +
                    $settlement->seguro_devengado +
                    $settlement->gastos_judiciales +
                    $settlement->gastos_administrativos +
                    $settlement->otros;
            @endphp

            <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td style="background-color: rgba(0,0,0,0.15);"></td>
                        <td style="font-size: 1rem;">Bs. {{ number_format($total, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            @php
                $renderer = new BaconQrCode\Renderer\ImageRenderer(
                    new BaconQrCode\Renderer\RendererStyle\RendererStyle(100),
                    new BaconQrCode\Renderer\Image\SvgImageBackEnd(),
                );
                $writer = new BaconQrCode\Writer($renderer);
                $qrUrl = base64_encode(
                    $settlement->id . '_' . $settlement->beneficiary_id . '_' . $settlement->user_id,
                );
                $qrCode = $writer->writeString($qrUrl);
            @endphp

            <!-- Agregar antes del footer -->
            <div style="text-align: center; margin: 20px 0;">
                <img src="data:image/svg+xml;base64,{{ base64_encode($qrCode) }}" style="width: 100px;">
                <p style="font-size: 9px; margin-top: 5px;">Código Unico de Liquidación</p>
            </div>

        </main>

        <footer>
            <p>Este es un documento generado automáticamente y no requiere firma.</p>
        </footer>
    </div>
</body>

</html>
