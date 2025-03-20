<div>
    <button wire:click="$set('showModal', true)" class="p-2 rounded-full bg-gray-100">
        <svg width="42px" height="42px" viewBox="-102.4 -102.4 1228.80 1228.80" xmlns="http://www.w3.org/2000/svg"
            fill="#1db512" stroke="#1db512">
            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
            <g id="SVGRepo_iconCarrier">
                <path fill="#1db512"
                    d="M832 384H576V128H192v768h640V384zm-26.496-64L640 154.496V320h165.504zM160 64h480l256 256v608a32 32 0 0 1-32 32H160a32 32 0 0 1-32-32V96a32 32 0 0 1 32-32zm320 512V448h64v128h128v64H544v128h-64V640H352v-64h128z">
                </path>
            </g>
        </svg>
    </button>

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
                                    Registrar/Crear Nuevo Pago:
                                </h3>
                                <div class="mt-2">
                                    <form wire:submit.prevent="save">
                                        <div class="mb-4">
                                            <label for="numpago"
                                                class="block text-gray-700 text-sm font-bold mb-2">Numero de
                                                Cuota:</label>
                                            <input type="number" max="300" min="1" wire:model="numpago" id="numpago"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('numpago')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="numtramite"
                                                class="block text-gray-700 text-sm font-bold mb-2">N° Comprobante /
                                                Numero de Tramite:</label>
                                            <input type="text" wire:model="numtramite" id="numtramite"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('numtramite')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="numprestamo"
                                                class="block text-gray-700 text-sm font-bold mb-2">Codigo
                                                Prestamo:</label>
                                            <input type="text" wire:model="numprestamo" id="numprestamo"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('numprestamo')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="fecha_pago"
                                                class="block text-gray-700 text-sm font-bold mb-2">Fecha de
                                                Pago:</label>
                                            <input type="date" wire:model="fecha_pago" id="fecha_pago"
                                                class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            @error('fecha_pago')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="hora_pago"
                                                class="block text-gray-700 text-sm font-bold mb-2">Hora de Pago:</label>
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
                                        <div class="border-l-4 border-green-600 pl-4">
                                            <div class="mb-4">
                                                <label for="capital"
                                                    class="block text-gray-700 text-sm font-bold mb-2">CAPITAL:</label>
                                                <input type="number" wire:model="capital" id="capital"
                                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                @error('capital')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="mb-4">
                                                <label for="interes"
                                                    class="block text-gray-700 text-sm font-bold mb-2">INTERES:</label>
                                                <input type="number" wire:model="interes" id="interes"
                                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                @error('interes')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="mb-4">
                                                <label for="interes_devg"
                                                    class="block text-gray-700 text-sm font-bold mb-2">INTERES DEVENGADO:</label>
                                                <input type="number" wire:model="interes_devg" id="interes_devg"
                                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                @error('interes_devg')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="mb-4">
                                                <label for="seguro"
                                                    class="block text-gray-700 text-sm font-bold mb-2">SEGURO:</label>
                                                <input type="number" wire:model="seguro" id="seguro"
                                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                @error('seguro')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="mb-4">
                                                <label for="seguro_devg"
                                                    class="block text-gray-700 text-sm font-bold mb-2">SEGURO DEVENGADO:</label>
                                                <input type="number" wire:model="seguro_devg" id="seguro_devg"
                                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                @error('seguro_devg')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="mb-4">
                                                <label for="otros"
                                                    class="block text-gray-700 text-sm font-bold mb-2">OTROS:</label>
                                                <input type="number" wire:model="otros" id="otros"
                                                    class="appearance-none border-0 border-b-2 border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                                @error('otros')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="mb-4">
                                            <label for="agencia_pago"
                                                class="block text-gray-700 text-sm font-bold mb-2">Agencia
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
                                        <div class="flex items-center justify-between">
                                            <button type="button" wire:click="$set('confirmingSave', true)"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline">
                                                Guardar Cambios
                                            </button>
                                            <button type="button" wire:click="$set('showModal', false)"
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline">
                                                Cancelar
                                            </button>
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
                                        Mensaje de Confirmación.
                                    </span>
                                </h3>
                                <div class="mt-2">
                                    <p class="text-red-700 font-bold text-justify mb-2">
                                        Advertencia: Usted está a punto de registrar un Voucher y sus glosas, la información registrada no puede ser modificada en un futuro.
                                    </p>
                                    <hr />
                                    <p class="text-gray-700 mt-2">
                                        ¿Desea proceder?
                                    </p>
                                    <div class="flex items-center justify-between mt-4">
                                        <button type="button" wire:click="save"
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
