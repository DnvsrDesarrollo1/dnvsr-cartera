<div x-data="{ benModal: @entangle('benModal') }">
    <x-personal.button @click="benModal = true" @keydown.escape.window="benModal = false" variant="primary" size="sm"
        iconCenter="fa-solid fa-user-pen">
        Editar
    </x-personal.button>

    <div x-show="benModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
        <div class="fixed inset-0 transition-opacity" @click="benModal = false">
            <div class="absolute inset-0 bg-gray-500 opacity-25"></div>
        </div>

        <div class="bg-white overflow-hidden rounded-lg transform transition-all sm:max-w-2xl sm:w-full m-4">
            <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Editar Perfil del Beneficiario
                    </h3>
                    <button @click="benModal = false" class="text-gray-400 hover:text-gray-500">
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
                        <form wire:submit="update">
                            <div class="mb-4">
                                <label for="nombre" class="block text-gray-700 text-sm font-bold">Nombre:</label>
                                <input type="text" wire:model="nombre" id="nombre"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('nombre')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="ci" class="block text-gray-700 text-sm font-bold">CI:</label>
                                <input type="text" wire:model="ci" id="ci"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('ci')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="complemento"
                                    class="block text-gray-700 text-sm font-bold">Complemento:</label>
                                <input type="text" wire:model="complemento" id="complemento"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('complemento')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="expedido" class="block text-gray-700 text-sm font-bold">Expedido:</label>
                                <input type="text" wire:model="expedido" id="expedido"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('expedido')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="estado" class="block text-gray-700 text-sm font-bold">Estado del
                                    Credito:</label>
                                <input type="text" wire:model.live="estado" id="estado"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('estado')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror

                                @if ($estado === 'BLOQUEADO')
                                    <div class="mt-2">
                                        <label for="cod_fondesif" class="block text-gray-700 text-sm font-bold">Tipo de
                                            Bloqueo:</label>
                                        <select wire:model="cod_fondesif" id="cod_fondesif"
                                            class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            <option value="">Seleccione una opción</option>
                                            <option value="INACTIVO">INACTIVO</option>
                                            <option value="REVERTIDO">REVERTIDO</option>
                                            <option value="SINIESTRO REPORTADO">SINIESTRO REPORTADO</option>
                                        </select>
                                        @error('cod_fondesif')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                            <div class="mb-4">
                                <label for="idepro" class="block text-gray-700 text-sm font-bold">Codigo
                                    Prestamo:</label>
                                @if ($idepro != $beneficiary->idepro)
                                    <span class="text-red-500 text-sm">
                                        <i>
                                            Se detectó una posible actualización para el codigo de préstamo, considere
                                            que esto implica tambien la sincronia con criterios paralelos.
                                        </i>
                                    </span>
                                @endif
                                <input type="text" wire:model.live="idepro" id="idepro"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('idepro')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="fecha_nacimiento" class="block text-gray-700 text-sm font-bold">Fecha de
                                    Nacimiento:</label>
                                <input type="date" wire:model="fecha_nacimiento" id="fecha_nacimiento"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('fecha_nacimiento')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label for="monto_credito" class="block text-gray-700 text-sm font-bold">Monto
                                        Crédito:</label>
                                    <input type="number" wire:model="monto_credito" id="monto_credito"
                                        class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('monto_credito')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="monto_activado" class="block text-gray-700 text-sm font-bold">Monto
                                        Activado:</label>
                                    <input type="number" wire:model="monto_activado" id="monto_activado"
                                        class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('monto_activado')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="total_activado" class="block text-gray-700 text-sm font-bold">Total
                                        Activado:</label>
                                    <input type="number" wire:model="total_activado" id="total_activado"
                                        class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @error('total_activado')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="gastos_judiciales" class="block text-gray-700 text-sm font-bold">Gastos
                                    Judiciales:</label>
                                <input type="number" wire:model="gastos_judiciales" id="gastos_judiciales"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('gastos_judiciales')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="saldo_credito" class="block text-gray-700 text-sm font-bold">
                                    Saldo Crédito:
                                </label>
                                @php
                                    $saldo_credito =
                                        $beneficiary->monto_activado -
                                        $beneficiary
                                            ->payments()
                                            ->where('prtdtdesc', 'LIKE', 'CAPI%')
                                            ->where('prtdtdesc', 'NOT LIKE', '%DIFER%')
                                            ->where(function ($query) {
                                                $query
                                                    ->whereNull('observacion')
                                                    ->orWhere('observacion', '')
                                                    ->orWhere('observacion', '!=', 'LEGACY 22/24');
                                            })
                                            ->sum('montopago');
                                @endphp
                                @if ($saldo_credito != $beneficiary->saldo_credito)
                                    <span class="text-yellow-800 text-xs">
                                        <i>
                                            El sistema detecta un saldo aprox. de Bs.
                                            {{ number_format($saldo_credito, 3) }}
                                        </i>
                                    </span>
                                @endif
                                <input type="number" wire:model="saldo_credito" id="saldo_credito"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('saldo_credito')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="monto_recuperado" class="block text-gray-700 text-sm font-bold">Monto
                                    Recuperado:</label>
                                <input type="number" wire:model="monto_recuperado" id="monto_recuperado"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('monto_recuperado')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="fecha_activacion" class="block text-gray-700 text-sm font-bold">Fecha de
                                    Activación:</label>
                                <input type="date" wire:model="fecha_activacion" id="fecha_activacion"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('fecha_activacion')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="plazo_credito" class="block text-gray-700 text-sm font-bold">Plazo
                                    Crédito:</label>
                                <input type="number" wire:model="plazo_credito" id="plazo_credito"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('plazo_credito')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="tasa_interes" class="block text-gray-700 text-sm font-bold">Tasa de
                                    Interés:</label>
                                <input type="number" wire:model="tasa_interes" id="tasa_interes"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('tasa_interes')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="departamento"
                                    class="block text-gray-700 text-sm font-bold">Departamento:</label>
                                <input type="text" wire:model="departamento" id="departamento"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('departamento')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="seguro" class="block text-gray-700 text-sm font-bold">Seguro
                                    Desgravamen:</label>
                                <input type="text" wire:model="seguro" id="seguro"
                                    class="appearance-none border-0 border-b-2 border-gray-300 w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('seguro')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div
                class="bg-gray-100 px-4 py-3 items-center justify-between lg:flex sm:px-6 sm:flex border-t border-gray-200">
                <x-personal.button variant="success" iconLeft="fa-solid fa-save" wire:click="update"
                    wire:confirm="¿Está seguro de que desea guardar los cambios?">
                    Aplicar Cambios
                </x-personal.button>
                <x-personal.button @click="benModal = false" variant="danger" iconLeft="fa-solid fa-xmark">
                    Cerrar
                </x-personal.button>
            </div>
        </div>
    </div>
</div>
