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

        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-5xl sm:w-full z-50">
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
                <div class="bg-white h-fit shadow-lg rounded-lg" id="profile_payments">
                    <div class="flex items-center justify-center gap-2 p-2">
                        <div class="flex justify-between items-center w-fit bg-gray-100 p-2 rounded-lg shadow-sm">
                            <span class="text-lg text-gray-700 mr-2">
                                Capital:
                            </span>
                            <span class="text-lg font-bold text-gray-900">
                                {{ number_format($beneficiary->payments()->where('prtdtdesc', 'LIKE', '%CAPI%')->sum('montopago'), 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center w-fit bg-gray-100 p-2 rounded-lg shadow-sm">
                            <span class="text-lg text-gray-700 mr-2">
                                Interes:
                            </span>
                            <span class="text-lg font-bold text-gray-900">
                                {{ number_format($beneficiary->payments()->where('prtdtdesc', 'LIKE', '%INTE%')->sum('montopago'), 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center w-fit bg-gray-100 p-2 rounded-lg shadow-sm">
                            <span class="text-lg text-gray-700 mr-2">
                                Seguros:
                            </span>
                            <span class="text-lg font-bold text-gray-900">
                                {{ number_format($beneficiary->payments()->where('prtdtdesc', 'LIKE', '%SEG%')->sum('montopago'), 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center w-fit bg-gray-300 p-2 rounded-lg shadow-sm">
                            <span class="text-lg text-gray-700 mr-2">
                                Total:
                            </span>
                            <span class="text-lg font-bold text-gray-900">
                                {{ number_format($beneficiary->payments()->sum('montopago'), 2) }}
                            </span>
                        </div>
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
                                <th>Glosas</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($beneficiary->vouchers()->orderBy('fecha_pago')->get() as $v)
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
                                    <td class="h-auto p-2">
                                        <div class="flex flex-col space-y-2">
                                            @forelse ($v->payments as $p)
                                                <div
                                                    class="flex justify-between items-center bg-gray-100 p-2 rounded-lg shadow-sm">
                                                    <span class="text-xs text-gray-700">
                                                        {{ $p->prtdtdesc }}
                                                    </span>
                                                    <span class="text-xs font-bold text-gray-900">
                                                        {{ number_format($p->montopago, 2) }}
                                                    </span>
                                                </div>
                                            @empty
                                                <div
                                                    class="flex justify-between items-center bg-gray-100 p-2 rounded-lg shadow-sm">
                                                    <span class="text-xs text-gray-700">
                                                        No hay glosas
                                                    </span>
                                                </div>
                                            @endforelse
                                        </div>
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
