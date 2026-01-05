<div x-data="{ voucherModal: @entangle('voucherModal') }">
    {{-- Botón de apertura --}}
    <x-personal.button @click="voucherModal = true" @keydown.escape.window="voucherModal = false" variant="primary"
        size="md" iconCenter="fa-solid fa-plus">
    </x-personal.button>

    {{-- Modal Principal - Optimizado --}}
    <div x-show="voucherModal" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-100"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak style="will-change: opacity;"
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center p-4 bg-black/40">

        {{-- Overlay --}}
        <div class="fixed inset-0" @click="voucherModal = false"></div>

        {{-- Contenedor del Modal --}}
        <div class="relative bg-white rounded-xl shadow-2xl w-full sm:max-w-4xl overflow-hidden">

            {{-- Header --}}
            <div class="bg-slate-800 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-slate-700 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-receipt text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Registro de Voucher</h3>
                            <p class="text-slate-300 text-sm">Módulo Cajero</p>
                        </div>
                    </div>
                    <button @click="voucherModal = false"
                        class="w-8 h-8 rounded-lg bg-slate-700 hover:bg-slate-600 text-white flex items-center justify-center">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Contenido --}}
            <div class="px-6 py-6 max-h-[calc(100vh-250px)] overflow-y-auto custom-scrollbar">
                <form wire:submit.prevent="save" class="space-y-5">

                    {{-- Información Básica --}}
                    <div class="bg-blue-50 rounded-lg p-5 border border-blue-200">
                        <h4 class="text-sm font-semibold text-slate-700 mb-4 flex items-center">
                            <i class="fa-solid fa-info-circle text-blue-500 mr-2"></i>
                            Información Básica
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- N° de Cuota --}}
                            <div>
                                <label for="numpago" class="block text-xs font-medium text-slate-600 mb-2">
                                    <i class="fa-solid fa-hashtag text-slate-400 mr-1"></i>
                                    N° de Cuota
                                </label>
                                <input type="number" max="300" min="1" wire:model="numpago" id="numpago"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700
                                           focus:border-blue-500 focus:outline-none placeholder-slate-400">
                                @error('numpago')
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- N° Comprobante --}}
                            <div>
                                <label for="numtramite" class="block text-xs font-medium text-slate-600 mb-2">
                                    <i class="fa-solid fa-file-invoice text-slate-400 mr-1"></i>
                                    N° Comprobante
                                </label>
                                <input type="text" wire:model.live.debounce.500ms="numtramite" id="numtramite"
                                    class="w-full px-4 py-2.5 bg-white border rounded-lg text-slate-700
                                           focus:outline-none placeholder-slate-400
                                           @if ($comprobanteDuplicado) border-red-400 focus:border-red-500
                                           @else border-slate-300 focus:border-blue-500 @endif">
                                @if ($comprobanteDuplicado)
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>Comprobante Duplicado
                                    </span>
                                @endif
                                @error('numtramite')
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Asignado a --}}
                            <div class="flex items-end">
                                <div class="w-full px-4 py-2.5 bg-slate-100 border border-slate-300 rounded-lg">
                                    <span class="text-xs text-slate-500 block mb-1">Asignado a:</span>
                                    <span class="text-sm font-semibold text-slate-700">{{ $numprestamo }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Fecha y Hora --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label for="fecha_pago" class="block text-xs font-medium text-slate-600 mb-2">
                                    <i class="fa-solid fa-calendar text-slate-400 mr-1"></i>
                                    Fecha de Pago
                                </label>
                                <input type="date" wire:model="fecha_pago" id="fecha_pago"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700
                                           focus:border-blue-500 focus:outline-none">
                                @error('fecha_pago')
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div>
                                <label for="hora_pago" class="block text-xs font-medium text-slate-600 mb-2">
                                    <i class="fa-solid fa-clock text-slate-400 mr-1"></i>
                                    Hora de Pago
                                </label>
                                <input type="time" wire:model="hora_pago" id="hora_pago"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700
                                           focus:border-blue-500 focus:outline-none">
                                @error('hora_pago')
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div>
                                <label for="descripcion" class="block text-xs font-medium text-slate-600 mb-2">
                                    <i class="fa-solid fa-align-left text-slate-400 mr-1"></i>
                                    Descripción
                                </label>
                                <input type="text" wire:model="descripcion" id="descripcion"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700
                                           focus:border-blue-500 focus:outline-none placeholder-slate-400"
                                    placeholder="Descripción del pago">
                                @error('descripcion')
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Alerta de Saldo --}}
                    <div class="bg-amber-50 border-l-4 border-amber-400 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-amber-800">Validación de Saldo</p>
                                <p class="text-xs text-amber-700 mt-1">
                                    Saldo actual del crédito: <span class="font-semibold">Bs.
                                        {{ number_format($beneficiario->saldo_credito, 2) }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Detalles del Pago --}}
                    <div class="bg-emerald-50 rounded-lg p-5 border border-emerald-200">
                        <h4 class="text-sm font-semibold text-slate-700 mb-4 flex items-center">
                            <i class="fa-solid fa-money-bill-wave text-emerald-600 mr-2"></i>
                            Detalles del Pago
                        </h4>

                        {{-- Total Pagado --}}
                        <div class="mb-4 p-4 bg-white rounded-lg border border-emerald-300">
                            <label for="totalpagado" class="block text-xs font-medium text-slate-600 mb-2">
                                <i class="fa-solid fa-calculator text-emerald-600 mr-1"></i>
                                Total Pagado (Actualización Automática)
                            </label>
                            <input type="number" wire:model.live.debounce.300ms="totalpagado" id="totalpagado"
                                step="0.01" min="0"
                                class="w-full px-4 py-3 bg-emerald-50 border border-emerald-300 rounded-lg text-lg font-semibold text-emerald-700
                                       focus:border-emerald-500 focus:outline-none placeholder-emerald-300"
                                placeholder="0.00">
                            <p class="text-xs text-slate-500 mt-2 italic flex items-center">
                                <i class="fa-solid fa-lightbulb text-amber-400 mr-1"></i>
                                Este campo actualiza automáticamente el capital según el total ingresado
                            </p>
                        </div>

                        {{-- Grid de Campos de Pago --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Capital --}}
                            <div>
                                <label for="capital" class="block text-xs font-medium text-slate-600 mb-2">
                                    <i class="fa-solid fa-coins text-slate-400 mr-1"></i>
                                    Capital
                                </label>
                                <input type="number" wire:model.live.debounce.300ms="capital" id="capital"
                                    step="0.01" min="0"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700
                                           focus:border-emerald-500 focus:outline-none placeholder-slate-400"
                                    placeholder="0.00">
                                @error('capital')
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Interés --}}
                            <div>
                                <label for="interes" class="block text-xs font-medium text-slate-600 mb-2">
                                    <i class="fa-solid fa-percent text-slate-400 mr-1"></i>
                                    Interés
                                </label>
                                <input type="number" wire:model.live.debounce.300ms="interes" id="interes"
                                    step="0.01" min="0"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700
                                           focus:border-emerald-500 focus:outline-none placeholder-slate-400"
                                    placeholder="0.00">
                                @error('interes')
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Interés Devengado --}}
                            <div>
                                <label for="interes_devg" class="block text-xs font-medium text-slate-600 mb-2">
                                    <i class="fa-solid fa-chart-line text-slate-400 mr-1"></i>
                                    Interés Devengado
                                </label>
                                <input type="number" wire:model.live.debounce.300ms="interes_devg" id="interes_devg"
                                    step="0.01" min="0"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700
                                           focus:border-emerald-500 focus:outline-none placeholder-slate-400"
                                    placeholder="0.00">
                                @error('interes_devg')
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Seguro --}}
                            <div>
                                <label for="seguro" class="block text-xs font-medium text-slate-600 mb-2">
                                    <i class="fa-solid fa-shield-halved text-slate-400 mr-1"></i>
                                    Seguro
                                </label>
                                <input type="number" wire:model.live.debounce.300ms="seguro" id="seguro"
                                    step="0.01" min="0"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700
                                           focus:border-emerald-500 focus:outline-none placeholder-slate-400"
                                    placeholder="0.00">
                                @error('seguro')
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Seguro Devengado --}}
                            <div>
                                <label for="seguro_devg" class="block text-xs font-medium text-slate-600 mb-2">
                                    <i class="fa-solid fa-shield text-slate-400 mr-1"></i>
                                    Seguro Devengado
                                </label>
                                <input type="number" wire:model.live.debounce.300ms="seguro_devg" id="seguro_devg"
                                    step="0.01" min="0"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700
                                           focus:border-emerald-500 focus:outline-none placeholder-slate-400"
                                    placeholder="0.00">
                                @error('seguro_devg')
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Otros --}}
                            <div>
                                <label for="otros" class="block text-xs font-medium text-slate-600 mb-2">
                                    <i class="fa-solid fa-ellipsis text-slate-400 mr-1"></i>
                                    Otros
                                </label>
                                <input type="number" wire:model.live.debounce.300ms="otros" id="otros"
                                    step="0.01" min="0"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700
                                           focus:border-emerald-500 focus:outline-none placeholder-slate-400"
                                    placeholder="0.00">
                                @error('otros')
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Campos Diferenciados --}}
                        <div x-data="{ enableDiffFields: @entangle('enableDiffFields') }" class="mt-4">
                            <label
                                class="flex items-center p-3 bg-white rounded-lg border border-slate-300 cursor-pointer hover:border-purple-400">
                                <input type="checkbox" id="enable_diff_fields" x-model="enableDiffFields"
                                    x-on:click="enableDiffFields = !enableDiffFields"
                                    class="w-5 h-5 text-purple-600 border-slate-300 rounded focus:ring-purple-500 focus:ring-2">
                                <span class="ml-3 text-sm font-medium text-slate-700">
                                    <i class="fa-solid fa-toggle-on text-purple-500 mr-1"></i>
                                    Activar Campos Diferenciados
                                </span>
                                <span class="ml-auto text-xs text-slate-500 italic">(Solo se registrará la cuota
                                    diferida)</span>
                            </label>

                            <div x-show="enableDiffFields" x-transition:enter="transition-opacity duration-150"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition-opacity duration-100"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
                                class="mt-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="cuota_diff"
                                            class="block text-xs font-medium text-purple-700 mb-2">
                                            <i class="fa-solid fa-hashtag text-purple-500 mr-1"></i>
                                            N° Cuota Diferida
                                        </label>
                                        <input type="number" wire:model.live.debounce.300ms="cuota_diff"
                                            id="cuota_diff" step="1" min="1"
                                            class="w-full px-4 py-2.5 bg-white border border-purple-300 rounded-lg text-slate-700
                                                   focus:border-purple-500 focus:outline-none">
                                        @error('cuota_diff')
                                            <span class="text-red-500 text-xs mt-1 flex items-center">
                                                <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="capital_diff"
                                            class="block text-xs font-medium text-purple-700 mb-2">
                                            <i class="fa-solid fa-coins text-purple-500 mr-1"></i>
                                            Capital Diferido
                                        </label>
                                        <input type="number" wire:model.live.debounce.300ms="capital_diff"
                                            id="capital_diff" step="0.01" min="0"
                                            class="w-full px-4 py-2.5 bg-white border border-purple-300 rounded-lg text-slate-700
                                                   focus:border-purple-500 focus:outline-none">
                                        @error('capital_diff')
                                            <span class="text-red-500 text-xs mt-1 flex items-center">
                                                <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="interes_diff"
                                            class="block text-xs font-medium text-purple-700 mb-2">
                                            <i class="fa-solid fa-percent text-purple-500 mr-1"></i>
                                            Interés Diferido
                                        </label>
                                        <input type="number" wire:model.live.debounce.300ms="interes_diff"
                                            id="interes_diff" step="0.01" min="0"
                                            class="w-full px-4 py-2.5 bg-white border border-purple-300 rounded-lg text-slate-700
                                                   focus:border-purple-500 focus:outline-none">
                                        @error('interes_diff')
                                            <span class="text-red-500 text-xs mt-1 flex items-center">
                                                <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Total a Pagar --}}
                        <div class="mt-4 p-4 bg-emerald-600 rounded-lg shadow-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-white text-sm font-medium flex items-center">
                                    <i class="fa-solid fa-calculator mr-2"></i>
                                    Total a Pagar
                                </span>
                                <span class="text-white text-2xl font-bold">
                                    Bs. {{ number_format($this->montopago(), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Información Adicional --}}
                    <div class="bg-slate-50 rounded-lg p-5 border border-slate-200">
                        <h4 class="text-sm font-semibold text-slate-700 mb-4 flex items-center">
                            <i class="fa-solid fa-building text-slate-500 mr-2"></i>
                            Información Adicional
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="agencia_pago" class="block text-xs font-medium text-slate-600 mb-2">
                                    <i class="fa-solid fa-store text-slate-400 mr-1"></i>
                                    Agencia de Pago
                                </label>
                                <input type="text" wire:model="agencia_pago" id="agencia_pago"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700
                                           focus:border-blue-500 focus:outline-none placeholder-slate-400"
                                    placeholder="Nombre de la agencia">
                                @error('agencia_pago')
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div>
                                <label for="depto_pago" class="block text-xs font-medium text-slate-600 mb-2">
                                    <i class="fa-solid fa-map-marker-alt text-slate-400 mr-1"></i>
                                    Departamento
                                </label>
                                <input type="text" wire:model="depto_pago" id="depto_pago"
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700
                                           focus:border-blue-500 focus:outline-none placeholder-slate-400"
                                    placeholder="Departamento">
                                @error('depto_pago')
                                    <span class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="obs_pago" class="block text-xs font-medium text-slate-600 mb-2">
                                <i class="fa-solid fa-comment-dots text-slate-400 mr-1"></i>
                                Observaciones
                            </label>
                            <textarea wire:model="obs_pago" id="obs_pago" rows="3"
                                class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700
                                       focus:border-blue-500 focus:outline-none placeholder-slate-400 resize-none"
                                placeholder="Observaciones adicionales..."></textarea>
                            @error('obs_pago')
                                <span class="text-red-500 text-xs mt-1 flex items-center">
                                    <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
                <div class="flex items-center justify-end space-x-3">
                    <button wire:click="save" wire:confirm="¿Está seguro de que desea registrar este Voucher?"
                        wire:loading.attr="disabled" wire:target="save" type="button"
                        class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg font-medium
                                   hover:bg-emerald-700 shadow-lg flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="save" class="flex items-center">
                            <i class="fa-solid fa-save mr-2"></i>
                            Registrar Voucher
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                            Guardando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
