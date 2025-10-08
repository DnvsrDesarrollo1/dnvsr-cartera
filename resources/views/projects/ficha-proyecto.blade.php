<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Proyecto</title>
</head>

<body>
@php
    // Agrupar beneficiarios por estado
    $estadisticasPorEstado = collect($projectStatistics)->groupBy('estado')->map->count();
    $totalBeneficiarios = count($projectStatistics);
    $sumaMontosCredito = collect($projectStatistics)->sum('monto_credito');
    $sumaMontosActivado = collect($projectStatistics)->sum('total_activado');
    $sumaGastosJudiciales = collect($projectStatistics)->sum('gastos_judiciales');
@endphp

<div class="report-container">
    <h2>Ficha: {{ $projectStatistics[0]['proyecto'] ?? '' }}</h2>

    <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">
        <tr>
            <td><strong>DEPARTAMENTO</strong></td>
            <td>{{ $projectStatistics[0]['departamento'] ?? '' }}</td>
        </tr>
        <tr>
            <td><strong>TOTAL BENEFICIARIOS</strong></td>
            <td>{{ $totalBeneficiarios }}</td>
        </tr>
        <tr>
            <td><strong>MONTO TOTAL DEL CRÉDITO Bs.</strong></td>
            <td>{{ number_format($sumaMontosCredito, 2) }}</td>
        </tr>
        <tr>
            <td><strong>MONTO TOTAL ACTIVADO Bs.</strong></td>
            <td>{{ number_format($sumaMontosActivado, 2) }}</td>
        </tr>
        <tr>
            <td><strong>GASTOS JUDICIALES PAGADOS Bs.</strong></td>
            <td>{{ number_format($sumaGastosJudiciales, 2) }}</td>
        </tr>
        @foreach($estadisticasPorEstado as $estado => $cantidad)
        <tr>
            <td><strong>BENEFICIARIOS {{ strtoupper($estado) }}</strong></td>
            <td>{{ $cantidad }}</td>
        </tr>
        @endforeach
    </table>
</div>
</body>

</html>
