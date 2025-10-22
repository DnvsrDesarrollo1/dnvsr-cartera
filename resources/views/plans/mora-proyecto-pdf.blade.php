<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de Mora por Proyecto</title>
</head>

<style>
    * {
        font-family: Arial, sans-serif;
    }

    body {
        margin: 0;
        padding: 0;
        background-color: #f9f9f9;
    }

    .w-full {
        width: 100%;
    }

    .overflow-hidden {
        overflow: hidden;
    }

    .rounded-lg {
        border-radius: 0.5rem;
    }

    .bg-gray-200 {
        background-color: #e5e7eb;
    }

    .bg-gray-300 {
        background-color: #bfc0c2;
    }

    .bg-gray-100 {
        background-color: #f3f4f6;
    }

    .text-gray-700 {
        color: #374151;
    }

    .text-gray-400 {
        color: #9ca3af;
    }

    .px-4 {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .py-3 {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }

    .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .font-semibold {
        font-weight: 600;
    }

    .text-left {
        text-align: left;
    }

    .text-center {
        text-align: center;
    }

    .border-b {
        border-bottom: 1px solid #d1d5db;
    }

    .hover\:bg-gray-50:hover {
        background-color: #f9fafb;
    }

    .transition {
        transition: background-color 0.2s ease-in-out;
    }

    .font-bold {
        font-weight: 700;
    }

    .text-sm {
        font-size: 0.875rem;
    }

</style>

<body>
    <h1>Reporte de Mora a Hoy ({{ now()->format('d/m/Y') }})</h1>
    <table class="w-full overflow-hidden rounded-lg text-sm">
        <thead>
            <tr class="bg-gray-200 text-gray-700">
                <th class="px-2 py-2 font-semibold text-left">#</th>
                <th class="px-2 py-2 font-semibold text-left">Departamento</th>
                <th class="px-2 py-2 font-semibold text-left">Proyecto</th>
                <th class="px-2 py-2 font-semibold text-center">Beneficiarios</th>
                <th class="px-2 py-2 font-semibold text-center">Morosos</th>
                <th class="px-2 py-2 font-semibold text-center">Mora (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lProyectos as $proyecto => $data)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-2 py-1 text-gray-700 text-sm">{{ $loop->iteration }}</td>
                    <td class="px-2 py-1 text-gray-700 text-sm">{{ $data['departamento'] }}</td>
                    <td class="px-2 py-1 text-gray-700 text-sm">{{ $proyecto }}</td>
                    <td class="px-2 py-1 text-left text-gray-700 text-sm">{{ $data['total'] }}</td>
                    <td class="px-2 py-1 text-left text-gray-700 text-sm">{{ $data['morosos'] }}</td>
                    <td class="px-2 py-1 text-right text-gray-700 text-sm">
                        {{ number_format($data['porcentajeMora'], 2) }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-2 py-2 text-gray-400 text-center">No hay proyectos disponibles.</td>
                </tr>
            @endforelse
            @if (count($lProyectos) > 0)
                <tr class="font-bold bg-gray-300">
                    <td class="px-2 py-1 text-gray-700" colspan="3">Total de Mora:</td>
                    <td class="px-2 py-1 text-center text-gray-700">
                        {{ array_sum(array_column($lProyectos->toArray(), 'total')) }}
                    </td>
                    <td class="px-2 py-1 text-center text-gray-700">
                        {{ array_sum(array_column($lProyectos->toArray(), 'morosos')) }}
                    </td>
                    <td class="px-2 py-1 text-center text-gray-700">
                        {{ number_format((array_sum(array_column($lProyectos->toArray(), 'morosos')) / max(array_sum(array_column($lProyectos->toArray(), 'total')), 1)) * 100, 2) }}%
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</body>

</html>
