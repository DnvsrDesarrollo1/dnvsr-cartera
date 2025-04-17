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
                            <div class="w-full space-y-2">
                                <div class="bg-gray-200 rounded-md flex items-center justify-center">
                                    <svg width="72px" height="72px" viewBox="-4.8 -4.8 57.60 57.60"
                                        xmlns="http://www.w3.org/2000/svg" fill="#DC2626">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                        </g>
                                        <g id="SVGRepo_iconCarrier">
                                            <title>expire-solid</title>
                                            <g id="Layer_2" data-name="Layer 2">
                                                <g id="icons_Q2" data-name="icons Q2">
                                                    <g>
                                                        <rect width="48" height="48" fill="none"></rect>
                                                        <g>
                                                            <path
                                                                d="M14.2,31.9h0a2,2,0,0,0-.9-2.9A11.8,11.8,0,0,1,6.1,16.8,12,12,0,0,1,16.9,6a12.1,12.1,0,0,1,11.2,5.6,2.3,2.3,0,0,0,2.3.9h0a2,2,0,0,0,1.1-3,15.8,15.8,0,0,0-15-7.4,16,16,0,0,0-4.8,30.6A2,2,0,0,0,14.2,31.9Z">
                                                            </path>
                                                            <path d="M16.5,11.5v5h-5a2,2,0,0,0,0,4h9v-9a2,2,0,0,0-4,0Z">
                                                            </path>
                                                            <path
                                                                d="M45.7,43l-15-26a2,2,0,0,0-3.4,0l-15,26A2,2,0,0,0,14,46H44A2,2,0,0,0,45.7,43ZM29,42a2,2,0,1,1,2-2A2,2,0,0,1,29,42Zm2-8a2,2,0,0,1-4,0V26a2,2,0,0,1,4,0Z">
                                                            </path>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
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
                                <table class="w-full overflow-hidden rounded-lg">
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
                                                    <button
                                                        @click="open === {{ $loop->index }} ? open = null : open = {{ $loop->index }}"
                                                        class="ml-2 text-gray-800 hover:text-gray-600 text-sm">
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Fila de detalles del acordeón (se muestra/oculta) -->
                                            <tr x-show="open === {{ $loop->index }}" x-transition x-collapse
                                                class="bg-gray-50">
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
                                                                                <th class="px-3 py-2 text-left text-gray-800">
                                                                                    Nombre
                                                                                </th>
                                                                                <th class="px-3 py-2 text-left text-gray-800">
                                                                                    CI
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($data['listaBeneficiarios'] as $moroso)
                                                                                <tr class="border-b-2 border-white">
                                                                                    <td class="px-3 py-2">
                                                                                        <a href="{{ route('beneficiario.show', [$moroso['ci']]) }}" class="text-gray-800 hover:text-gray-600 font-bold" target="_blank">
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
{{-- <div class="bg-gray-200 rounded-md flex items-center justify-center">
                                    <svg width="72px" height="72px" viewBox="-4.8 -4.8 57.60 57.60"
                                        xmlns="http://www.w3.org/2000/svg" fill="#DC2626">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                        </g>
                                        <g id="SVGRepo_iconCarrier">
                                            <title>expire-solid</title>
                                            <g id="Layer_2" data-name="Layer 2">
                                                <g id="icons_Q2" data-name="icons Q2">
                                                    <g>
                                                        <rect width="48" height="48" fill="none"></rect>
                                                        <g>
                                                            <path
                                                                d="M14.2,31.9h0a2,2,0,0,0-.9-2.9A11.8,11.8,0,0,1,6.1,16.8,12,12,0,0,1,16.9,6a12.1,12.1,0,0,1,11.2,5.6,2.3,2.3,0,0,0,2.3.9h0a2,2,0,0,0,1.1-3,15.8,15.8,0,0,0-15-7.4,16,16,0,0,0-4.8,30.6A2,2,0,0,0,14.2,31.9Z">
                                                            </path>
                                                            <path d="M16.5,11.5v5h-5a2,2,0,0,0,0,4h9v-9a2,2,0,0,0-4,0Z">
                                                            </path>
                                                            <path
                                                                d="M45.7,43l-15-26a2,2,0,0,0-3.4,0l-15,26A2,2,0,0,0,14,46H44A2,2,0,0,0,45.7,43ZM29,42a2,2,0,1,1,2-2A2,2,0,0,1,29,42Zm2-8a2,2,0,0,1-4,0V26a2,2,0,0,1,4,0Z">
                                                            </path>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    <h1 class="text-gray-700 font-bold text-center p-2 rounded-lg">
                                        Tabla de Mora por Beneficiario:
                                    </h1>
                                </div>
                                <table class="w-full overflow-hidden rounded-lg">
                                    <thead>
                                        <tr class="bg-gray-200 text-gray-700">
                                            <th class="px-4 py-3 font-semibold text-left">
                                                #
                                            </th>
                                            <th class="px-4 py-3 font-semibold text-left">
                                                Nombres
                                            </th>
                                            <th class="px-4 py-3 font-semibold text-left">
                                                CI
                                            </th>
                                            <th class="px-4 py-3 font-semibold text-left">
                                                Proyecto
                                            </th>
                                            <th class="px-4 py-3 font-semibold text-left">

                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($lBeneficiarios as $b)
                                            <tr class="border-b hover:bg-gray-50 transition">
                                                <td class="px-4 py-2 text-gray-700">
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td class="px-4 py-2 text-gray-700">
                                                    {{ $b->nombre }}
                                                </td>
                                                <td class="px-4 py-2 text-gray-700">
                                                    {{ $b->ci }}
                                                </td>
                                                <td class="px-4 py-2 text-gray-700">
                                                    {{ $b->proyecto }}
                                                </td>
                                                <td class="px-4 py-2 text-gray-700">
                                                    <x-personal.button variant="link"
                                                        href="{{ route('beneficiario.show', ['cedula' => $b->ci ?? 0]) }}"
                                                        to="_blank">
                                                        <svg fill="#000000" width="32px" height="32px"
                                                            version="1.1" viewBox="144 144 512 512"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                stroke-linejoin="round"></g>
                                                            <g id="SVGRepo_iconCarrier">
                                                                <path
                                                                    d="m300.14 250.55h-60.668v-13.84h60.617v13.84zm57.754 7.7461h-119v13.84h119zm0 21.531h-119v13.84h119zm233.08 340.62c0 3.5547-1.3789 6.8398-3.8711 9.332l-10.766 10.766c-2.4922 2.4922-5.7812 3.8711-9.332 3.8711-3.5547 0-6.8398-1.3789-9.332-3.8711l-33.145-33.145c-5.1445-5.1445-5.1445-13.523 0-18.668l0.47266-0.47266-16.227-16.227c-16.758 14.32-37.492 21.586-58.281 21.586-19.941 0-39.828-6.5742-56.16-19.727l-164.35-0.007813v-20.949h-20.949v-351.61l193.09 0.003906 20.949 20.949h82.57v210.96c2.8633 2.2266 5.6758 4.6133 8.2734 7.2656 33.359 33.359 34.844 86.551 4.668 121.76l16.227 16.227 0.47656-0.47656c5.1445-5.1445 13.523-5.1445 18.668 0l33.145 33.199c2.4922 2.3828 3.8711 5.6719 3.8711 9.2227zm-133.8-364.07 27.523 27.523v137.04c1.6445 0.6875 3.2344 1.4844 4.8789 2.2812 0.74219 0.37109 1.5391 0.63672 2.2266 1.0625v-188.16h-54.887zm-51.02 23.441h54.891l-54.891-54.891zm-176.17 259.27h137.94c-0.16016-0.42578-0.31641-0.84766-0.47656-1.2188-0.054688-0.10547-0.10547-0.26562-0.16016-0.37109-1.0078-2.5469-1.9102-5.1445-2.7031-7.7422-0.054688-0.10547-0.054688-0.21094-0.10547-0.37109-4.9336-16.652-4.8242-34.207-0.054688-50.594 0.10547-0.31641 0.21094-0.58203 0.26563-0.90234 0.6875-2.2812 1.4844-4.5625 2.3867-6.7891 0.16016-0.47656 0.31641-0.90234 0.53125-1.3789 2.0664-4.9844 4.5625-9.7578 7.5312-14.32 0.42578-0.6875 0.90234-1.3242 1.3242-1.9609 1.1133-1.6445 2.2812-3.2344 3.5-4.7734 0.53125-0.63672 1.0078-1.3242 1.5391-1.9609 1.6953-2.0664 3.5-4.082 5.4102-5.9922l0.10547-0.10547c0.054688-0.054688 0.054688-0.054688 0.10547-0.054688 1.8047-1.8047 3.6602-3.4453 5.5703-5.0391 0.63672-0.58203 1.3242-1.1133 2.0156-1.6445 1.3789-1.1133 2.8125-2.1758 4.2422-3.1836 1.5898-1.1133 3.2344-2.1758 4.8789-3.1836 0.63672-0.37109 1.2188-0.74219 1.8555-1.1133 9.5469-5.5156 19.992-9.1758 30.973-10.871 0.21094-0.054688 0.42578-0.054688 0.63672-0.10547 2.8125-0.42578 5.6758-0.6875 8.5391-0.84766 0.95312-0.054687 1.8555-0.10547 2.8125-0.16016 0.58203 0 1.2188-0.10547 1.8047-0.10547 0.6875 0 1.3789 0.10547 2.0664 0.10547 1.5391 0.054687 3.0234 0.10547 4.5625 0.21094 1.3789 0.10547 2.7578 0.21094 4.1367 0.37109 1.75 0.21094 3.5 0.47656 5.25 0.79688 1.0625 0.21094 2.0664 0.37109 3.0742 0.58203 0.42578 0.10547 0.79688 0.16016 1.2188 0.26562l-0.007812-122.98h-78.434v-78.488h-169.44v323.92zm150.51 20.949c-1.2188-1.4844-2.2812-3.0742-3.3945-4.668-0.53125-0.74219-1.1133-1.4336-1.5898-2.1758-0.054688-0.10547-0.16016-0.16016-0.21094-0.26562l-131.47 0.003906v7.1055zm123.67-2.4375c29.594-29.594 29.594-77.746 0-107.34-3.2891-3.2891-6.8945-6.2031-10.605-8.8047-0.6875-0.47656-1.3789-0.95312-2.0664-1.3789-1.2188-0.79688-2.4922-1.5391-3.7109-2.2266-0.6875-0.37109-1.3789-0.79688-2.0664-1.168-1.75-0.95312-3.6055-1.8047-5.4102-2.5469-0.79688-0.37109-1.6445-0.63672-2.4922-0.95313-1.2188-0.47656-2.4414-0.90234-3.7109-1.3242-0.90234-0.26562-1.8047-0.58203-2.7031-0.84766-1.3789-0.37109-2.7578-0.74219-4.1367-1.0078-1.0078-0.21094-2.0664-0.47656-3.0742-0.63672-1.6445-0.31641-3.2891-0.53125-4.9336-0.74219-1.0625-0.10547-2.1211-0.21094-3.1836-0.26562-1.0625-0.054688-2.0664-0.10547-3.1289-0.16016-4.7188-0.16016-9.4922 0.16016-14.16 0.84766-11.508 1.8047-22.594 6.3125-32.191 13.098-0.95312 0.6875-1.9102 1.3789-2.8633 2.1211-0.6875 0.53125-1.3789 1.0625-2.0156 1.6445-1.6445 1.3789-3.2891 2.8125-4.8242 4.4023-1.6953 1.6953-3.2891 3.5-4.8242 5.3047-0.47656 0.58203-0.95313 1.2188-1.4336 1.8555-1.0078 1.2734-1.9609 2.5469-2.8125 3.8711-0.47656 0.6875-0.95312 1.4336-1.3789 2.1211-0.84766 1.3242-1.6445 2.6523-2.3867 3.9766-0.37109 0.6875-0.74219 1.3789-1.1133 2.0664-0.79688 1.5898-1.5898 3.2344-2.2812 4.9336-0.16016 0.42578-0.37109 0.84766-0.58203 1.2734-2.7031 6.7344-4.4023 13.789-5.0898 20.949v0.10547c-1.1133 11.984 0.58203 24.184 5.0898 35.531 0.10547 0.31641 0.26562 0.63672 0.42578 0.95312 0.74219 1.8047 1.5391 3.5547 2.4414 5.25 0.31641 0.58203 0.63672 1.2188 0.95312 1.8047 0.6875 1.2188 1.3789 2.3867 2.1211 3.6055 1.0625 1.6445 2.1758 3.2891 3.2891 4.8789 0.53125 0.74219 1.0625 1.5391 1.6445 2.2812 1.8047 2.2812 3.7109 4.5078 5.832 6.5742 29.637 29.543 77.789 29.543 107.38-0.046875zm63.32 73.184m9.3867-10.344-32.242-32.242-9.8633 9.8633 32.242 32.242zm-129.61-303.45h-208.36v13.84h208.31v-13.84zm0 25.082h-208.36v13.84h208.31v-13.84zm0 25.137h-208.36v13.84h208.31v-13.84zm0 25.086h-208.36v13.84h208.31v-13.84zm-208.36 64.117h98.535v-13.84l-98.535-0.003907zm0 26.727h98.535v-13.84h-98.535zm0 26.676h98.535v-13.84h-98.535z">
                                                                </path>
                                                            </g>
                                                        </svg>
                                                    </x-personal.button>
                                                </td>
                                            </tr>
                                        @empty
                                            <p class="text-gray-400">No hay beneficiarios con mora.</p>
                                        @endforelse
                                    </tbody>
                                </table> --}}
