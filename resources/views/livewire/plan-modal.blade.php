<div x-data="{ isOpen: @entangle('isOpen') }">
    <button @click="isOpen = true" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
        Ver {{ $title }}
    </button>

    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
        <div class="fixed inset-0 transition-opacity" @click="isOpen = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="bg-white overflow-hidden rounded-lg transform transition-all sm:max-w-4xl sm:w-full m-4 z-50">
            <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">
                        {{ $title }}
                    </h3>
                    <button @click="isOpen = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="px-4 py-4 sm:p-6 max-h-[calc(100vh-200px)] overflow-y-auto">
                <div class="p-6" id="profile_plans">
                    <div class="overflow-x-auto">
                        <table class="w-full overflow-hidden rounded-lg">
                            <thead>
                                <tr class="bg-gray-200 text-gray-700">
                                    <th class="px-4 py-3 font-semibold text-left">#</th>
                                    <th class="px-4 py-3 font-semibold text-left">Cuota</th>
                                    <th class="px-4 py-3 font-semibold text-left">
                                        <span class="block">Capital</span>
                                        <span class="block text-green-600 text-sm">
                                            ({{ number_format($beneficiary->plans()->sum('prppgcapi'), 2) }})
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 font-semibold text-left">
                                        <span class="block">Interés</span>
                                        <span class="block text-green-600 text-sm">
                                            ({{ number_format($beneficiary->plans()->sum('prppginte'), 2) }})
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 font-semibold text-left">
                                        <span class="block">Seguro</span>
                                        <span class="block text-green-600 text-sm">
                                            ({{ number_format($beneficiary->plans()->sum('prppgsegu'), 2) }})
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 font-semibold text-left">
                                        <span class="block">Total a Pagar</span>
                                        <span class="block text-green-600 text-sm">
                                            ({{ number_format($beneficiary->plans()->sum('prppgtota'), 2) }})
                                        </span>
                                    </th>
                                    <th class="px-4 py-3 font-semibold text-left">Vencimiento</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @php
                                    $plans = $beneficiary->readjustments()
                                        ->where('estado', 'ACTIVO')
                                        ->orderBy('fecha_ppg', 'asc')
                                        ->get();

                                    if ($plans->count() <= 0) {
                                        $plans = $beneficiary->plans()
                                        ->where('estado', 'ACTIVO')
                                        ->orderBy('fecha_ppg', 'asc')
                                        ->get();
                                    }
                                @endphp
                                @forelse ($plans as $p)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-700">{{ $loop->index + 1 }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $p->prppgnpag }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ number_format($p->prppgcapi, 2) }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ number_format($p->prppginte, 2) }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ number_format($p->prppgsegu, 2) }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ number_format($p->prppgtota, 2) }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $p->fecha_ppg }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-4 py-3 text-gray-700 text-center" colspan="7">
                                            No hay planes de pago registrados
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div
                class="bg-gray-100 px-4 py-3 items-center justify-between lg:flex sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                <button @click="isOpen = false" type="button"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-800 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Cerrar
                </button>
                <a target="_blank" href="{{ route('beneficiario.pdf', ['cedula' => $beneficiary->ci]) }}"
                    class="border mr-2 px-2 py-1 rounded-md relative cursor-pointer">
                    <svg width="32px" height="32px" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg"
                        fill="#000000">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
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
                                    d="M325,105H250a5,5,0,0,1-5-5V25a5,5,0,0,1,10,0V95h70a5,5,0,0,1,0,10Z"></path>
                                <path class="cls-1"
                                    d="M325,154.83a5,5,0,0,1-5-5V102.07L247.93,30H100A20,20,0,0,0,80,50v98.17a5,5,0,0,1-10,0V50a30,30,0,0,1,30-30H250a5,5,0,0,1,3.54,1.46l75,75A5,5,0,0,1,330,100v49.83A5,5,0,0,1,325,154.83Z">
                                </path>
                                <path class="cls-1"
                                    d="M300,380H100a30,30,0,0,1-30-30V275a5,5,0,0,1,10,0v75a20,20,0,0,0,20,20H300a20,20,0,0,0,20-20V275a5,5,0,0,1,10,0v75A30,30,0,0,1,300,380Z">
                                </path>
                                <path class="cls-1" d="M275,280H125a5,5,0,0,1,0-10H275a5,5,0,0,1,0,10Z"></path>
                                <path class="cls-1" d="M200,330H125a5,5,0,0,1,0-10h75a5,5,0,0,1,0,10Z"></path>
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
                                    d="M245.59,236H235.7V182.68h33.71v8.24H245.59v14.57h18.75v8H245.59Z"></path>
                            </g>
                        </g>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
