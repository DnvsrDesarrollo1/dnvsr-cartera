<div x-data="{ voucherModal: @entangle('voucherModal') }">
    <x-personal.button @click="voucherModal = true" @keydown.escape.window="voucherModal = false" variant="primary"
        size="md" iconCenter="fa-solid fa-plus">
    </x-personal.button>

    <div x-show="voucherModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
        <div class="fixed inset-0 transition-opacity" @click="voucherModal = false">
            <div class="absolute inset-0 bg-gray-500 opacity-50"></div>
        </div>

        <div class="bg-white overflow-hidden rounded-lg transform transition-all sm:max-w-4xl sm:w-full m-4">
            <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Registro de Pagos:
                    </h3>
                    <button @click="voucherModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="px-2 py-2 sm:p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                <div class="px-4 border-l-4 border-gray-800" id="profile_plans">
                    <div class="overflow-x-auto">
                        <form wire:submit.prevent="save">
                            <div class="mb-4">
                                <label for="numpago" class="block text-gray-700 text-sm font-bold mb-2">Numero de
                                    Cuota:</label>
                                <input type="number" max="300" min="1" wire:model="numpago" id="numpago"
                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('numpago')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="numtramite" class="block text-gray-700 text-sm font-bold mb-2">N°
                                    Comprobante /
                                    Numero de Tramite:</label>
                                <input type="text" wire:model="numtramite" id="numtramite"
                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('numtramite')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="numprestamo" class="block text-gray-700 text-sm font-bold mb-2">Codigo
                                    Prestamo:</label>
                                <input type="text" wire:model="numprestamo" id="numprestamo"
                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('numprestamo')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="fecha_pago" class="block text-gray-700 text-sm font-bold mb-2">Fecha de
                                    Pago:</label>
                                <input type="date" wire:model="fecha_pago" id="fecha_pago"
                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('fecha_pago')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="hora_pago" class="block text-gray-700 text-sm font-bold mb-2">Hora de
                                    Pago:</label>
                                <input type="time" wire:model="hora_pago" id="hora_pago"
                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('hora_pago')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="descripcion"
                                    class="block text-gray-700 text-sm font-bold mb-2">Descripcion:</label>
                                <input type="text" wire:model="descripcion" id="descripcion"
                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('descripcion')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <x-personal.alert clossable='false' type='info' message='Recuerde validar el SALDO CREDITO antes de registrar cualquier pago.' />

                            <div class="border-l-4 border-green-600 pl-4">

                                <div class="mb-4">
                                    <label for="totalpagado" class="block text-gray-700 text-sm font-bold mb-2">Total
                                        Pagado:</label>
                                    <input type="number" wire:model.live.debounce.300ms="totalpagado" id="totalpagado"
                                        step="0.10"
                                        class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('totalpagado')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <span class="block text-gray-400 text-sm mb-2 italic text-end">
                                    Este valor determinará el monto real destinado a Capital (k), usar solo en caso de
                                    necesitar re-elaborar el plan vigente.
                                </span>
                                <hr class="mt-4 mb-4" />
                                <div class="mb-4">
                                    <label for="capital"
                                        class="block text-gray-700 text-sm font-bold mb-2">CAPITAL:</label>
                                    <input type="number" wire:model.live.debounce.300ms="capital" id="capital"
                                        step="0.10"
                                        class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('capital')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="interes"
                                        class="block text-gray-700 text-sm font-bold mb-2">INTERES:</label>
                                    <input type="number" wire:model.live.debounce.300ms="interes" id="interes"
                                        step="0.10"
                                        class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('interes')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="interes_devg"
                                        class="block text-gray-700 text-sm font-bold mb-2">INTERES
                                        DEVENGADO:</label>
                                    <input type="number" wire:model.live.debounce.300ms="interes_devg"
                                        id="interes_devg" step="0.10"
                                        class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('interes_devg')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="seguro"
                                        class="block text-gray-700 text-sm font-bold mb-2">SEGURO:</label>
                                    <input type="number" wire:model.live.debounce.300ms="seguro" id="seguro"
                                        step="0.10"
                                        class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('seguro')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="seguro_devg" class="block text-gray-700 text-sm font-bold mb-2">SEGURO
                                        DEVENGADO:</label>
                                    <input type="number" wire:model.live.debounce.300ms="seguro_devg"
                                        id="seguro_devg" step="0.10"
                                        class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('seguro_devg')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="otros"
                                        class="block text-gray-700 text-sm font-bold mb-2">OTROS:</label>
                                    <input type="number" wire:model.live.debounce.300ms="otros" id="otros"
                                        step="0.10"
                                        class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('otros')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div x-data="{ enableDiffFields: @entangle('enableDiffFields') }">
                                    <div class="mb-4">
                                        <label for="enable_diff_fields" class="inline-flex items-center">
                                            <input type="checkbox" id="enable_diff_fields" x-model="enableDiffFields"
                                                x-on:click="enableDiffFields = !enableDiffFields"
                                                class="form-checkbox h-5 w-5 text-gray-600">
                                            <span class="ml-2 text-gray-700 text-sm font-bold">Activar Campos
                                                Diferenciados (nota: solo se registrará la cuota diferida).</span>
                                        </label>
                                    </div>

                                    <div x-show="enableDiffFields"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                        x-cloak class="flex items-center w-full gap-2">
                                        <div class="mb-4 w-full">
                                            <label for="cuota_diff"
                                                class="block text-gray-700 text-sm font-bold mb-2">
                                                NRO° CUOTA DIFERIDA:
                                            </label>
                                            <input type="number" wire:model.live.debounce.300ms="cuota_diff"
                                                id="cuota_diff" step="1"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('cuota_diff')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4 w-full">
                                            <label for="capital_diff"
                                                class="block text-gray-700 text-sm font-bold mb-2">
                                                CAPITAL DIFERIDO:
                                            </label>
                                            <input type="number" wire:model.live.debounce.300ms="capital_diff"
                                                id="capital_diff" step="0.10"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('capital_diff')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4 w-full">
                                            <label for="interes_diff"
                                                class="block text-gray-700 text-sm font-bold mb-2">
                                                INTERES DIFERIDO:
                                            </label>
                                            <input type="number" wire:model.live.debounce.300ms="interes_diff"
                                                id="interes_diff" step="0.10"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('interes_diff')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-4 mb-4" />
                                <span class="block text-gray-500 text-sm font-bold mb-2 text-end">
                                    <i class="fa-solid fa-circle-info"></i> &nbsp; Total a Pagar: Bs.
                                    {{ number_format($montopago, 2) }}</span>
                            </div>
                            <div class="mb-4">
                                <label for="agencia_pago" class="block text-gray-700 text-sm font-bold mb-2">Agencia
                                    Pago:</label>
                                <input type="text" wire:model="agencia_pago" id="agencia_pago"
                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('agencia_pago')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="depto_pago"
                                    class="block text-gray-700 text-sm font-bold mb-2">Departamento:</label>
                                <input type="text" wire:model="depto_pago" id="depto_pago"
                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('depto_pago')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="obs_pago"
                                    class="block text-gray-700 text-sm font-bold mb-2">Observaciones:</label>
                                <input type="text" wire:model="obs_pago" id="obs_pago"
                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('obs_pago')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div
                class="bg-gray-100 px-4 py-3 items-center justify-between lg:flex sm:px-6 sm:flex border-t border-gray-200">
                <x-personal.button variant="success" iconLeft="fa-solid fa-save" wire:click="save"
                    wire:confirm="¿Está seguro de que desea registrar este Voucher?">
                    Registrar Voucher
                </x-personal.button>
                <x-personal.button @click="voucherModal = false" variant="danger" iconLeft="fa-solid fa-xmark">
                    Cerrar
                </x-personal.button>
            </div>
        </div>
    </div>
</div>
