<div x-data="{ isOpen: @entangle('isOpen') }">
    <button @click="isOpen = true" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
        Ver {{ $title }}
    </button>

    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
        <div class="fixed inset-0 transition-opacity" @click="isOpen = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-4xl sm:w-full m-4 z-50">
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
                <div class="bg-white h-fit shadow-lg rounded-lg p-4" id="profile_payments">
                    <div class="mb-4">
                        <h2 class="text-2xl font-bold text-gray-800">Historial de Pagos</h2>
                        <p class="text-gray-600">Detalle de los pagos realizados por el beneficiario</p>
                    </div>
                    <table class="w-full overflow-hidden rounded-lg dark:divide-gray-700">
                        <thead>
                            <tr class="text-gray-800 dark:text-gray-400 bg-gray-200 dark:bg-gray-800"">
                                <th class="px-4 py-4 font-medium text-gray-500 whitespace-nowrap">
                                    N° Cuota
                                </th>
                                <th>Comprobante EIF</th>
                                <th>Codigo Prestamo</th>
                                <th>Fecha Pago</th>
                                <th>Hora Pago</th>
                                <th>Descripcion</th>
                                <th>Monto</th>
                                <th>Operaciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($beneficiary->vouchers()->get() as $v)
                                <tr class="border-b-2 px-3 py-4 text-sm p-2 h-auto">
                                    <td class="px-4 py-4 text-sm font-medium text-gray-700 whitespace-nowrap">
                                        {{ $v->numpago }}
                                    </td>
                                    <td>{{ $v->numtramite }}</td>
                                    <td>{{ $v->numprestamo }}</td>
                                    <td>{{ $v->fecha_pago }}</td>
                                    <td>{{ $v->hora_pago }}</td>
                                    <td>{{ $v->descripcion }}</td>
                                    <td>{{ 'Bs. ' . number_format($v->montopago, 2) }}</td>
                                    <td class="h-auto flex flex-row justify-center py-2">
                                        <x-dropdown align="right" width="40">
                                            <x-slot name="trigger">
                                                <span class="inline-flex rounded-md">
                                                    <button type="button"
                                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-full text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                                        <svg width="24px" height="24px" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                stroke-linejoin="round"></g>
                                                            <g id="SVGRepo_iconCarrier">
                                                                <rect width="24" height="24" fill="transparent">
                                                                </rect>
                                                                <circle cx="12" cy="7" r="0.5"
                                                                    transform="rotate(90 12 7)" stroke="#000000"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                </circle>
                                                                <circle cx="12" cy="12" r="0.5"
                                                                    transform="rotate(90 12 12)" stroke="#000000"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                </circle>
                                                                <circle cx="12" cy="17" r="0.5"
                                                                    transform="rotate(90 12 17)" stroke="#000000"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                </circle>
                                                            </g>
                                                        </svg>
                                                    </button>
                                                </span>
                                            </x-slot>
                                            <x-slot name="content">
                                                @foreach ($v->payments()->where('prt') as $p)
                                                    <x-dropdown-link>
                                                        <span class="cursor-pointer text-xs">
                                                            {{ $p->prtdtdesc }}
                                                        </span>
                                                        <span class="cursor-pointer text-xs font-bold">
                                                            {{ number_format($p->montopago, 2) }}
                                                        </span>
                                                    </x-dropdown-link>
                                                @endforeach
                                            </x-slot>
                                        </x-dropdown>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-b-2 px-3 py-4 text-sm p-2 h-auto">
                                    <td class="px-4 py-4 text-sm font-medium text-gray-700 whitespace-nowrap"
                                        colspan="12">
                                        No hay pagos registrados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-gray-100 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                <button @click="isOpen = false" type="button"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-800 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
