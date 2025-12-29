<div x-data="{ paymentModal: @entangle('paymentModal') }">
    {{-- Botón de apertura con estilo mejorado --}}
    <x-personal.button @click="paymentModal = true" @keydown.escape.window="paymentModal = false" variant="outline-primary"
        size="md" iconLeft="fa-solid fa-receipt">
        Ver {{ $title }}
    </x-personal.button>

    {{-- Modal con overlay mejorado --}}
    <div x-show="paymentModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center p-4">

        {{-- Overlay con gradiente suave --}}
        <div class="fixed inset-0 bg-gray-900/75 transition-opacity" @click="paymentModal = false">
        </div>

        {{-- Contenedor del modal con diseño moderno --}}
        <div class="relative bg-white rounded-2xl shadow-2xl transform transition-all sm:max-w-[90rem] w-full overflow-hidden"
            style="max-height: 90vh;">

            {{-- Header con gradiente sutil --}}
            <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-4 border-b border-gray-200/50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $title }}
                    </h3>
                    <button @click="paymentModal = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
                        <svg class="h-6 w-6 transition-transform duration-200" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Contenido principal con scroll suave --}}
            <div class="px-6 py-6 overflow-y-auto" style="max-height: calc(90vh - 180px);">

                {{-- Cards de totales con diseño moderno --}}
                <div class="grid grid-cols-7 md:grid-cols-7 lg:grid-cols-7 gap-2 mb-4">
                    {{-- Capital --}}
                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                        <p class="text-xs font-medium text-blue-600 mb-1">Capital</p>
                        <p class="text-lg font-bold text-blue-900">
                            {{ number_format($paymentTotals['capital'] ?? 0, 2) }}
                        </p>
                    </div>

                    {{-- Capital Diferido --}}
                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                        <p class="text-xs font-medium text-blue-600 mb-1">Cap. Diferido</p>
                        <p class="text-lg font-bold text-blue-900">
                            {{ number_format($paymentTotals['capital_diferido'] ?? 0, 2) }}
                        </p>
                    </div>

                    {{-- Amortización --}}
                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                        <p class="text-xs font-medium text-blue-600 mb-1">Amortización</p>
                        <p class="text-lg font-bold text-blue-900">
                            {{ number_format($paymentTotals['amortizacion'] ?? 0, 2) }}
                        </p>
                    </div>

                    {{-- Interés Diferido --}}
                    <div class="bg-emerald-50 rounded-lg p-3 border border-emerald-100">
                        <p class="text-xs font-medium text-emerald-600 mb-1">Int. Diferido</p>
                        <p class="text-lg font-bold text-emerald-900">
                            {{ number_format($paymentTotals['interes_diferido'] ?? 0, 2) }}
                        </p>
                    </div>

                    {{-- Interés --}}
                    <div class="bg-emerald-50 rounded-lg p-3 border border-emerald-100">
                        <p class="text-xs font-medium text-emerald-600 mb-1">Interés</p>
                        <p class="text-lg font-bold text-emerald-900">
                            {{ number_format($paymentTotals['interes'] ?? 0, 2) }}
                        </p>
                    </div>

                    {{-- Seguros --}}
                    <div class="bg-amber-50 rounded-lg p-3 border border-amber-100">
                        <p class="text-xs font-medium text-amber-600 mb-1">Seguros</p>
                        <p class="text-lg font-bold text-amber-900">
                            {{ number_format($paymentTotals['seguros'] ?? 0, 2) }}
                        </p>
                    </div>

                    {{-- Total --}}
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                        <p class="text-xs font-medium text-gray-600 mb-1">Total</p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ number_format($paymentTotals['total'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>

                {{-- Notificaciones con animación --}}
                @if (session()->has('success'))
                    <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 p-3 rounded-r-lg"
                        role="alert">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-r-lg" role="alert">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                {{-- Tabla moderna con diseño limpio --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        N° Cuota</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Comprobante EIF</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Código Préstamo</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Fecha Pago</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Agencia</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Descripción</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Monto</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Glosas</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($vouchers as $v)
                                    <tr class="hover:bg-gray-50 transition-colors duration-100">
                                        <td class="px-4 py-3 text-xs text-gray-700 font-medium">{{ $v->numpago }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600">{{ $v->numtramite }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-600">{{ $v->numprestamo }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-600">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $v->fecha_pago }}</span>
                                                <span class="text-xs text-gray-500">{{ $v->hora_pago }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600" title="{{ $v->agencia_pago }}">
                                            {{ Str::limit($v->agencia_pago, 25) }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-600" title="{{ $v->descripcion }}">
                                            {{ Str::limit($v->descripcion, 25) }}
                                        </td>
                                        <td class="px-4 py-3 text-xs font-bold text-emerald-600">
                                            Bs. {{ number_format($v->montopago, 2) }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-col gap-1">
                                                @forelse ($v->payments as $p)
                                                    <div
                                                        class="flex justify-between items-center bg-gray-50 px-2 py-1 rounded text-xs border border-gray-100">
                                                        <span class="text-gray-700 font-medium"
                                                            title="{{ $p->prtdtdesc }}">
                                                            {{ Str::limit(mb_convert_case($p->prtdtdesc, MB_CASE_TITLE, 'UTF-8'), 20) }}
                                                        </span>
                                                        <span class="text-gray-900 font-semibold ml-2">
                                                            {{ number_format($p->montopago, 2) }}
                                                        </span>
                                                    </div>
                                                @empty
                                                    <div class="text-xs text-gray-400 italic">Sin glosas</div>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button title="Borrar voucher" type="button"
                                                wire:click="delete('{{ $v->numtramite }}')"
                                                wire:confirm.prompt="¿Está seguro(a)?\nEsta información nunca podrá ser recuperada.\n\nEscriba BORRAR para proseguir:|BORRAR"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-500 hover:bg-red-600 transition-colors duration-150">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-8 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-400">
                                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                </svg>
                                                <p class="text-sm font-medium">No hay pagos registrados</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Footer con botón de PDF mejorado --}}
            <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-4 border-t border-gray-200/50">
                <div class="flex items-center justify-end">
                    <a target="_blank" href="{{ route('beneficiario.pdf-extract', ['cedula' => $beneficiary->ci]) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-150">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                        </svg>
                        <span class="text-sm font-medium">Ver PDF</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
</div>
