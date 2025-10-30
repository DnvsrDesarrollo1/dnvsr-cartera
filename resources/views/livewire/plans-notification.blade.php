<div x-data="{ openMora: @entangle('openMora') }">
    <div class="relative inline-block">
        <span
            class="absolute -top-2 inline-flex items-center justify-center h-5 text-xs font-bold text-white bg-green-600 rounded-full shadow-lg w-auto px-1"
            style="right: -1rem;">
            {{ $nVencidos }}
        </span>
        <button @click="openMora = true" @keydown.escape.window="openMora = false"
            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full focus:outline-none transition duration-200 ease-in-out">
            <i class="fa-solid fa-flag-checkered text-xl"></i>
        </button>
    </div>

    <div x-show="openMora" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
        <div class="fixed inset-0 transition-opacity" @click="openMora = false">
            <div class="absolute inset-0 bg-gray-500 opacity-50"></div>
        </div>
        <div class="bg-white overflow-hidden rounded-lg transform transition-all sm:max-w-4xl sm:w-full m-4 z-50">
            <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Notificaciones
                    </h3>
                    <button @click="openMora = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="px-2 py-2 sm:p-2 max-h-[calc(100vh-200px)] overflow-y-auto">
                <div id="profile_plans">
                    <div class="overflow-x-auto">
                        <article class="w-full shadow p-4 space-y-2 rounded-md duration-300">
                            <div class="w-full space-y-2" x-data="{ mora: false }">
                                <div class="bg-gray-200 rounded-md flex items-center justify-center"
                                    x-on:click="mora = !mora">
                                    <i class="fa-regular fa-calendar-xmark text-xl"></i>
                                    <h1 class="text-gray-700 font-bold text-center p-2 rounded-lg">
                                        Tabla de Mora por Proyecto:
                                    </h1>
                                    <a href="{{ route('plan.mora-pdf') }}" target="_blank"
                                        class='py-2 px-4 rounded-lg border'>
                                        <i class="fa-solid fa-file-pdf text-xl"></i>
                                    </a>
                                </div>
                                <table class="w-full overflow-hidden rounded-lg" x-show="mora"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
                                    <thead>
                                        <tr class="bg-gray-200 text-gray-700">
                                            <th class="px-4 py-3 font-semibold text-left">#</th>
                                            <th class="px-4 py-3 font-semibold text-left">Proyecto</th>
                                            <th class="px-4 py-3 font-semibold text-left">Cantidad de Beneficiarios
                                            </th>
                                            <th class="px-4 py-3 font-semibold text-left">Morosos</th>
                                            <th class="px-4 py-3 font-semibold text-left">Porcentaje de Mora</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody x-data="{ open: null }">
                                        @forelse ($lProyectos as $proyecto => $data)
                                            <!-- Fila principal del proyecto (siempre visible) -->
                                            <tr class="border-b hover:bg-gray-50 transition">
                                                <td class="px-4 py-2 text-gray-700">{{ $loop->iteration }}</td>
                                                <td class="px-4 py-2 text-gray-700">
                                                    <button
                                                        @click="open === {{ $loop->index }} ? open = null : open = {{ $loop->index }}"
                                                        class="text-left font-medium text-gray-800 hover:text-gray-600">
                                                        {{ $proyecto }}
                                                    </button>
                                                </td>
                                                <td class="px-4 py-2 text-gray-700">{{ $data['total'] }}</td>
                                                <td class="px-4 py-2 text-gray-700">{{ $data['morosos'] }}</td>
                                                <td class="px-4 py-2 text-gray-700">
                                                    {{ number_format($data['porcentajeMora'], 2) }}%
                                                </td>
                                                <td>
                                                    <a href="{{ route('plan.mora-pdf-proyecto', $proyecto) }}" target="_blank"
                                                        class='py-2 px-4 rounded-lg border'>
                                                        <i class="fa-solid fa-file-excel text-xl"></i>
                                                    </a>
                                                </td>
                                            </tr>

                                            <!-- Fila de detalles del acordeón (se muestra/oculta) -->
                                            <tr x-show="open === {{ $loop->index }}"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 scale-90"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-200"
                                                x-transition:leave-start="opacity-100 scale-100"
                                                x-transition:leave-end="opacity-0 scale-90" class="bg-gray-50">
                                                <td colspan="5" class="pl-4">
                                                    <div class="px-8 border-l-8 border-gray-800 bg-gray-200">
                                                        <!-- Aquí puedes poner la información detallada del proyecto -->
                                                        <div class="grid grid-cols-1 gap-2">
                                                            @if (isset($data['listaBeneficiarios']) && count($data['listaBeneficiarios']) > 0)
                                                                <div class="mt-2">
                                                                    <h5 class="font-bold text-gray-700 mb-2">
                                                                        Listado de morosos:
                                                                    </h5>
                                                                    <table
                                                                        class="min-w-full divide-y divide-gray-200 text-sm">
                                                                        <thead class="bg-gray-50 rounded-md">
                                                                            <tr>
                                                                                <th
                                                                                    class="px-3 py-2 text-left text-gray-800">
                                                                                    Nombre
                                                                                </th>
                                                                                <th
                                                                                    class="px-3 py-2 text-left text-gray-800">
                                                                                    CI
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($data['listaBeneficiarios'] as $moroso)
                                                                                <tr class="border-b-2 border-white">
                                                                                    <td class="px-3 py-2">
                                                                                        <a href="{{ route('beneficiario.show', [$moroso['ci']]) }}"
                                                                                            class="text-gray-800 hover:text-gray-600 font-bold"
                                                                                            target="_blank">
                                                                                            {{ $moroso['nombre'] }}
                                                                                        </a>
                                                                                    </td>
                                                                                    <td class="px-3 py-2">
                                                                                        {{ $moroso['ci'] }}
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-2 text-gray-400 text-center">No hay
                                                    proyectos disponibles.</td>
                                            </tr>
                                        @endforelse

                                        @if (count($lProyectos) > 0)
                                            <tr class="font-bold bg-gray-100">
                                                <td class="px-4 py-2 text-gray-700" colspan="2">Total de Mora:</td>
                                                <td class="px-4 py-2 text-gray-700">
                                                    {{ array_sum(array_column($lProyectos->toArray(), 'total')) }}
                                                </td>
                                                <td class="px-4 py-2 text-gray-700">
                                                    {{ array_sum(array_column($lProyectos->toArray(), 'morosos')) }}
                                                </td>
                                                <td class="px-4 py-2 text-gray-700">
                                                    {{ number_format((array_sum(array_column($lProyectos->toArray(), 'morosos')) / max(array_sum(array_column($lProyectos->toArray(), 'total')), 1)) * 100, 2) }}%
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="w-full space-y-2" x-data="{ settles: false }">
                                <div class="bg-gray-200 rounded-md flex items-center justify-center"
                                    x-on:click="settles = !settles">
                                    <i class="fa-regular fa-calendar-xmark text-xl"></i>
                                    <h1 class="text-gray-700 font-bold text-center p-2 rounded-lg">
                                        Tabla de Liquidaciones por Revisar:
                                    </h1>
                                </div>
                                <table class="w-full overflow-hidden rounded-lg" x-show="settles"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
                                    <thead>
                                        <tr class="bg-gray-200 text-gray-700">
                                            <th class="px-4 py-3 font-semibold text-left">#</th>
                                            <th class="px-4 py-3 font-semibold text-left">Nombre</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($lSettlements as $s)
                                            <tr class="border-b hover:bg-gray-50 transition">
                                                <td class="px-4 py-2 text-gray-700">{{ $loop->iteration }}</td>
                                                <td class="px-4 py-2 text-gray-700">
                                                    <a href="{{ route('beneficiario.show', [$s->beneficiary->ci]) }}"
                                                        class="text-gray-800 hover:text-gray-600 font-bold"
                                                        target="_blank">
                                                        {{ $s->beneficiary->nombre }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="px-4 py-2 text-gray-400 text-center">No hay
                                                    liquidaciones pendientes.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </article>
                    </div>
                </div>
            </div>

            <div
                class="bg-gray-100 px-4 py-3 items-center justify-between lg:flex sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
            </div>
        </div>
    </div>
</div>
