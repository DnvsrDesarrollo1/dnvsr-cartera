<div>
    <x-personal.button wire:click="$set('showModal', true)" variant="success" iconCenter="fa-solid fa-user-pen">
        Editar
    </x-personal.button>

    @if ($showModal)
        <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
            class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-4 pb-4 sm:p-6 sm:pb-4 border-t-8 border-gray-700">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full text-center sm:text-left">
                                <h3 class="text-xl leading-6 font-medium text-gray-900 border border-gray-400 rounded p-2"
                                    id="modal-title">
                                    Editar Perfil del Beneficiario
                                </h3>
                                <div class="mt-2">
                                    <form wire:submit.prevent="update">
                                        <div class="mb-4">
                                            <label for="nombre"
                                                class="block text-gray-700 text-sm font-bold">Nombre:</label>
                                            <input type="text" wire:model="nombre" id="nombre"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('nombre')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="ci"
                                                class="block text-gray-700 text-sm font-bold">CI:</label>
                                            <input type="text" wire:model="ci" id="ci"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('ci')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="complemento"
                                                class="block text-gray-700 text-sm font-bold">Complemento:</label>
                                            <input type="text" wire:model="complemento" id="complemento"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('complemento')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="expedido"
                                                class="block text-gray-700 text-sm font-bold">Expedido:</label>
                                            <input type="text" wire:model="expedido" id="expedido"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('expedido')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="estado"
                                                class="block text-gray-700 text-sm font-bold">Estado del
                                                Credito:</label>
                                            <input type="text" wire:model="estado" id="estado"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('estado')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="idepro"
                                                class="block text-gray-700 text-sm font-bold">Codigo
                                                Prestamo:</label>
                                            @if ($idepro != $beneficiary->idepro)
                                                <span class="text-red-500 text-sm">
                                                    <i>
                                                        Se detectó una posible actualización para el codigo de préstamo, considere que esto implica tambien la sincronia con criterios paralelos.
                                                    </i>
                                                </span>
                                            @endif
                                            <input type="text" wire:model.live="idepro" id="idepro"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('idepro')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="fecha_nacimiento"
                                                class="block text-gray-700 text-sm font-bold">Fecha de
                                                Nacimiento:</label>
                                            <input type="date" wire:model="fecha_nacimiento" id="fecha_nacimiento"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('fecha_nacimiento')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="total_activado"
                                                class="block text-gray-700 text-sm font-bold">Total
                                                Activado:</label>
                                            <input type="number" wire:model="total_activado" id="total_activado"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('total_activado')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="gastos_judiciales"
                                                class="block text-gray-700 text-sm font-bold">Gastos
                                                Judiciales:</label>
                                            <input type="number" wire:model="gastos_judiciales" id="gastos_judiciales"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('gastos_judiciales')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="saldo_credito"
                                                class="block text-gray-700 text-sm font-bold">
                                                Saldo Crédito:
                                            </label>
                                            @if (
                                                $beneficiary->total_activado - $beneficiary->payments()->where('prtdtdesc', 'LIKE', '%CAP%')->sum('montopago') !=
                                                    $beneficiary->saldo_credito)
                                                <span class="text-gray-500">
                                                    <i>
                                                        El sistema detecta un saldo aprox. de
                                                        {{ $beneficiary->total_activado - $beneficiary->payments()->where('prtdtdesc', 'LIKE', '%CAP%')->sum('montopago') }}
                                                    </i>
                                                </span>
                                            @endif
                                            <input type="number" wire:model="saldo_credito" id="saldo_credito"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('saldo_credito')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="monto_recuperado"
                                                class="block text-gray-700 text-sm font-bold">Monto
                                                Recuperado:</label>
                                            <input type="number" wire:model="monto_recuperado" id="monto_recuperado"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('monto_recuperado')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="fecha_activacion"
                                                class="block text-gray-700 text-sm font-bold">Fecha de
                                                Activación:</label>
                                            <input type="date" wire:model="fecha_activacion" id="fecha_activacion"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('fecha_activacion')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="plazo_credito"
                                                class="block text-gray-700 text-sm font-bold">Plazo
                                                Crédito:</label>
                                            <input type="number" wire:model="plazo_credito" id="plazo_credito"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('plazo_credito')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="tasa_interes"
                                                class="block text-gray-700 text-sm font-bold">Tasa de
                                                Interés:</label>
                                            <input type="number" wire:model="tasa_interes" id="tasa_interes"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('tasa_interes')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="departamento"
                                                class="block text-gray-700 text-sm font-bold">Departamento:</label>
                                            <input type="text" wire:model="departamento" id="departamento"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('departamento')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="seguro"
                                                class="block text-gray-700 text-sm font-bold">Seguro
                                                Desgravamen:</label>
                                            <input type="text" wire:model="seguro" id="seguro"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('seguro')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <x-personal.button wire:click="$set('confirmingSave', true)"
                                                variant="success" iconLeft="fa-solid fa-save">
                                                Guardar Cambios
                                            </x-personal.button>
                                            <x-personal.button wire:click="$set('showModal', false)" variant="danger"
                                                iconLeft="fa-solid fa-xmark">
                                                Cancelar
                                            </x-personal.button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($confirmingSave)
        <div class="fixed z-10 inset-0 overflow-y-auto" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-xl text-center leading-6 font-bold text-gray-900" id="modal-title">
                                    <span>
                                        <svg class="mx-auto" width="64px" height="64px"
                                            viewBox="-2.5 -2.5 30.00 30.00" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                stroke-linejoin="round"></g>
                                            <g id="SVGRepo_iconCarrier">
                                                <path
                                                    d="M10.8809 16.15C10.8809 16.0021 10.9101 15.8556 10.967 15.7191C11.024 15.5825 11.1073 15.4586 11.2124 15.3545C11.3175 15.2504 11.4422 15.1681 11.5792 15.1124C11.7163 15.0567 11.8629 15.0287 12.0109 15.03C12.2291 15.034 12.4413 15.1021 12.621 15.226C12.8006 15.3499 12.9399 15.5241 13.0211 15.7266C13.1024 15.9292 13.122 16.1512 13.0778 16.3649C13.0335 16.5786 12.9272 16.7745 12.7722 16.9282C12.6172 17.0818 12.4204 17.1863 12.2063 17.2287C11.9922 17.2711 11.7703 17.2494 11.5685 17.1663C11.3666 17.0833 11.1938 16.9426 11.0715 16.7618C10.9492 16.5811 10.8829 16.3683 10.8809 16.15ZM11.2408 13.42L11.1008 8.20001C11.0875 8.07453 11.1008 7.94766 11.1398 7.82764C11.1787 7.70761 11.2424 7.5971 11.3268 7.5033C11.4112 7.40949 11.5144 7.33449 11.6296 7.28314C11.7449 7.2318 11.8697 7.20526 11.9958 7.20526C12.122 7.20526 12.2468 7.2318 12.3621 7.28314C12.4773 7.33449 12.5805 7.40949 12.6649 7.5033C12.7493 7.5971 12.813 7.70761 12.8519 7.82764C12.8909 7.94766 12.9042 8.07453 12.8909 8.20001L12.7609 13.42C12.7609 13.6215 12.6809 13.8149 12.5383 13.9574C12.3958 14.0999 12.2024 14.18 12.0009 14.18C11.7993 14.18 11.606 14.0999 11.4635 13.9574C11.321 13.8149 11.2408 13.6215 11.2408 13.42Z"
                                                    fill="#920553"></path>
                                                <path
                                                    d="M12 21.5C17.1086 21.5 21.25 17.3586 21.25 12.25C21.25 7.14137 17.1086 3 12 3C6.89137 3 2.75 7.14137 2.75 12.25C2.75 17.3586 6.89137 21.5 12 21.5Z"
                                                    stroke="#920553" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                            </g>
                                        </svg>
                                    </span>
                                    <span>
                                        Información sensible a punto de ser modificada.
                                    </span>
                                </h3>
                                <div class="mt-2">
                                    <p class="text-red-700 font-bold text-justify">
                                        Advertencia: Al guardar los cambios, se actualizará la información del
                                        beneficiario en la base de datos, lo que puede involucrar a toda la
                                        información relacionada al mismo.
                                        Por favor, asegúrese de que todos los datos ingresados sean correctos,
                                        verificados y aprobados por un superior.
                                        Esta acción no se puede deshacer.
                                    </p>
                                    <hr />
                                    <p class="text-gray-700 mt-2">
                                        ¿Desea proceder?
                                    </p>
                                    <div class="flex items-center justify-between mt-4">
                                        <button type="button" wire:click="update"
                                            class="bg-green-500 hover:bg-green-800 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline">
                                            Sí, Guardar
                                        </button>
                                        <button type="button" wire:click="$set('confirmingSave', false)"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline">
                                            No, Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
