<div x-data="{ isOpen: @entangle('isOpen') }">
    <x-personal.button @click="isOpen = true" @keydown.escape.window="isOpen = false" variant="primary" size="md" iconLeft="fa-solid fa-receipt">
        Ver {{ $title }}
    </x-personal.button>

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
                    <div>
                        @if (session()->has('success'))
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4"
                                role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
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
                                <th></th>
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
                                    <td>
                                        <button
                                            title="Borrar voucher"
                                            type="submit"
                                            wire:click="delete('{{ $p->numtramite }}')"
                                            wire:confirm.prompt="¿Está seguro(a)?\nEsta información nunca podrá ser recuperada.\n\nEscriba BORRAR para proseguir:|BORRAR"
                                            class="group relative flex h-14 w-8 flex-col items-center justify-center overflow-hidden rounded-lg border-red-800 bg-red-400 hover:bg-red-500 transition ease-in-out">
                                            <svg viewBox="0 0 1.625 1.625"
                                                class="absolute -top-7 fill-white delay-100 group-hover:top-6 group-hover:animate-[spin_1.4s] group-hover:duration-1000"
                                                height="15" width="15">
                                                <path
                                                    d="M.471 1.024v-.52a.1.1 0 0 0-.098.098v.618c0 .054.044.098.098.098h.487a.1.1 0 0 0 .098-.099h-.39c-.107 0-.195 0-.195-.195">
                                                </path>
                                                <path
                                                    d="M1.219.601h-.163A.1.1 0 0 1 .959.504V.341A.033.033 0 0 0 .926.309h-.26a.1.1 0 0 0-.098.098v.618c0 .054.044.098.098.098h.487a.1.1 0 0 0 .098-.099v-.39a.033.033 0 0 0-.032-.033">
                                                </path>
                                                <path
                                                    d="m1.245.465-.15-.15a.02.02 0 0 0-.016-.006.023.023 0 0 0-.023.022v.108c0 .036.029.065.065.065h.107a.023.023 0 0 0 .023-.023.02.02 0 0 0-.007-.016">
                                                </path>
                                            </svg>
                                            <svg width="16" fill="none" viewBox="0 0 39 7"
                                                class="origin-right duration-500 group-hover:rotate-90">
                                                <line stroke-width="4" stroke="white" y2="5" x2="39"
                                                    y1="5"></line>
                                                <line stroke-width="3" stroke="white" y2="1.5" x2="26.0357"
                                                    y1="1.5" x1="12"></line>
                                            </svg>
                                            <svg width="16" fill="none" viewBox="0 0 33 39" class="">
                                                <mask fill="white" id="path-1-inside-1_8_19">
                                                    <path
                                                        d="M0 0H33V35C33 37.2091 31.2091 39 29 39H4C1.79086 39 0 37.2091 0 35V0Z">
                                                    </path>
                                                </mask>
                                                <path mask="url(#path-1-inside-1_8_19)" fill="white"
                                                    d="M0 0H33H0ZM37 35C37 39.4183 33.4183 43 29 43H4C-0.418278 43 -4 39.4183 -4 35H4H29H37ZM4 43C-0.418278 43 -4 39.4183 -4 35V0H4V35V43ZM37 0V35C37 39.4183 33.4183 43 29 43V35V0H37Z">
                                                </path>
                                                <path stroke-width="4" stroke="white" d="M12 6L12 29"></path>
                                                <path stroke-width="4" stroke="white" d="M21 6V29"></path>
                                            </svg>
                                        </button>
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

            <div
                class="bg-gray-100 px-4 py-3 items-center justify-between lg:flex sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                <button @click="isOpen = false" type="button"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-800 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Cerrar
                </button>
                <a target="_blank" href="{{ route('beneficiario.pdf-extract', ['cedula' => $beneficiary->ci]) }}"
                    class="border mr-2 px-2 py-1 rounded-md relative cursor-pointer">
                    <svg width="32px" height="32px" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"
                        stroke-width="2.56" stroke="#34d539" fill="none">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path
                                d="M45.77,51a.09.09,0,0,1,.11,0l3.4,3.31s.12,0,.12-.07V9.7H14.6V54.16a.08.08,0,0,0,.13.07l4.38-3.29a.08.08,0,0,1,.1,0l4.53,3.33a.08.08,0,0,0,.11,0l4-3.33s.07,0,.11,0l4.52,3.32a.08.08,0,0,0,.11,0L36.79,51a.09.09,0,0,1,.12,0l4.51,3.31a.09.09,0,0,0,.12,0l4-3.3"
                                stroke-linecap="round"></path>
                            <line x1="18.72" y1="31.11" x2="32.02" y2="31.11"
                                stroke-linecap="round">
                            </line>
                            <line x1="18.72" y1="25.77" x2="34.69" y2="25.77"
                                stroke-linecap="round">
                            </line>
                            <line x1="18.72" y1="15" x2="36.05" y2="15"
                                stroke-linecap="round">
                            </line>
                            <line x1="18.72" y1="20.11" x2="33.15" y2="20.11"
                                stroke-linecap="round">
                            </line>
                            <line x1="41.01" y1="30.95" x2="45.28" y2="30.95"
                                stroke-linecap="round"></line>
                            <line x1="18.72" y1="36.81" x2="34.69" y2="36.81"
                                stroke-linecap="round"></line>
                            <line x1="41.86" y1="36.66" x2="45.28" y2="36.66"
                                stroke-linecap="round"></line>
                            <line x1="41.01" y1="42.56" x2="45.28" y2="42.56"
                                stroke-linecap="round"></line>
                            <line x1="41.01" y1="25.61" x2="45.28" y2="25.61"
                                stroke-linecap="round"></line>
                            <line x1="41.86" y1="20.11" x2="45.28" y2="20.11"
                                stroke-linecap="round"></line>
                            <line x1="41.01" y1="15" x2="45.28" y2="15"
                                stroke-linecap="round"></line>
                        </g>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
