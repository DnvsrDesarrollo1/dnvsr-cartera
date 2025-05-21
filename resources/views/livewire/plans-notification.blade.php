<div x-data="{ isOpen: @entangle('isOpen') }">
    <div class="relative">
        @if ($nVencidos > 0)
            <span
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                {{ $nVencidos }}
            </span>
        @endif
        <button @click="isOpen = true" @keydown.escape.window="isOpen = false"
            title="{{ $nVencidos }} Beneficiarios en mora" @class(['py-2', 'px-4', 'rounded-lg'])>
            <svg width="30px" height="30px" viewBox="-2.4 -2.4 28.80 28.80" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                    <path
                        d="M12.02 2.90991C8.70997 2.90991 6.01997 5.59991 6.01997 8.90991V11.7999C6.01997 12.4099 5.75997 13.3399 5.44997 13.8599L4.29997 15.7699C3.58997 16.9499 4.07997 18.2599 5.37997 18.6999C9.68997 20.1399 14.34 20.1399 18.65 18.6999C19.86 18.2999 20.39 16.8699 19.73 15.7699L18.58 13.8599C18.28 13.3399 18.02 12.4099 18.02 11.7999V8.90991C18.02 5.60991 15.32 2.90991 12.02 2.90991Z"
                        stroke="#A1A1AA" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"></path>
                    <path
                        d="M13.87 3.19994C13.56 3.10994 13.24 3.03994 12.91 2.99994C11.95 2.87994 11.03 2.94994 10.17 3.19994C10.46 2.45994 11.18 1.93994 12.02 1.93994C12.86 1.93994 13.58 2.45994 13.87 3.19994Z"
                        stroke="#A1A1AA" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    <path
                        d="M15.02 19.0601C15.02 20.7101 13.67 22.0601 12.02 22.0601C11.2 22.0601 10.44 21.7201 9.90002 21.1801C9.36002 20.6401 9.02002 19.8801 9.02002 19.0601"
                        stroke="#A1A1AA" stroke-width="1.5" stroke-miterlimit="10"></path>
                </g>
            </svg>
        </button>
    </div>

    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
        <div class="fixed inset-0 transition-opacity" @click="isOpen = false">
            <div class="absolute inset-0 bg-gray-500 opacity-50"></div>
        </div>
        <div class="bg-white overflow-hidden rounded-lg transform transition-all sm:max-w-4xl sm:w-full m-4 z-50">
            <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Notificaciones
                    </h3>
                    <button @click="isOpen = false" class="text-gray-400 hover:text-gray-500">
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
                                        <svg width="32px" height="32px" viewBox="0 0 400 400"
                                            xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                stroke-linejoin="round"></g>
                                            <g id="SVGRepo_iconCarrier">
                                                <defs>
                                                    <style>
                                                        .cls-1 {
                                                            fill: #ff402f;
                                                        }
                                                    </style>
                                                </defs>
                                                <title></title>
                                                <g id="xxx-word">
                                                    <path class="cls-1"
                                                        d="M325,105H250a5,5,0,0,1-5-5V25a5,5,0,0,1,10,0V95h70a5,5,0,0,1,0,10Z">
                                                    </path>
                                                    <path class="cls-1"
                                                        d="M325,154.83a5,5,0,0,1-5-5V102.07L247.93,30H100A20,20,0,0,0,80,50v98.17a5,5,0,0,1-10,0V50a30,30,0,0,1,30-30H250a5,5,0,0,1,3.54,1.46l75,75A5,5,0,0,1,330,100v49.83A5,5,0,0,1,325,154.83Z">
                                                    </path>
                                                    <path class="cls-1"
                                                        d="M300,380H100a30,30,0,0,1-30-30V275a5,5,0,0,1,10,0v75a20,20,0,0,0,20,20H300a20,20,0,0,0,20-20V275a5,5,0,0,1,10,0v75A30,30,0,0,1,300,380Z">
                                                    </path>
                                                    <path class="cls-1"
                                                        d="M275,280H125a5,5,0,0,1,0-10H275a5,5,0,0,1,0,10Z"></path>
                                                    <path class="cls-1"
                                                        d="M200,330H125a5,5,0,0,1,0-10h75a5,5,0,0,1,0,10Z"></path>
                                                    <path class="cls-1"
                                                        d="M325,280H75a30,30,0,0,1-30-30V173.17a30,30,0,0,1,30-30h.2l250,1.66a30.09,30.09,0,0,1,29.81,30V250A30,30,0,0,1,325,280ZM75,153.17a20,20,0,0,0-20,20V250a20,20,0,0,0,20,20H325a20,20,0,0,0,20-20V174.83a20.06,20.06,0,0,0-19.88-20l-250-1.66Z">
                                                    </path>
                                                    <path class="cls-1"
                                                        d="M145,236h-9.61V182.68h21.84q9.34,0,13.85,4.71a16.37,16.37,0,0,1-.37,22.95,17.49,17.49,0,0,1-12.38,4.53H145Zm0-29.37h11.37q4.45,0,6.8-2.19a7.58,7.58,0,0,0,2.34-5.82,8,8,0,0,0-2.17-5.62q-2.17-2.34-7.83-2.34H145Z">
                                                    </path>
                                                    <path class="cls-1"
                                                        d="M183,236V182.68H202.7q10.9,0,17.5,7.71t6.6,19q0,11.33-6.8,18.95T200.55,236Zm9.88-7.85h8a14.36,14.36,0,0,0,10.94-4.84q4.49-4.84,4.49-14.41a21.91,21.91,0,0,0-3.93-13.22,12.22,12.22,0,0,0-10.37-5.41h-9.14Z">
                                                    </path>
                                                    <path class="cls-1"
                                                        d="M245.59,236H235.7V182.68h33.71v8.24H245.59v14.57h18.75v8H245.59Z">
                                                    </path>
                                                </g>
                                            </g>
                                        </svg>
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
