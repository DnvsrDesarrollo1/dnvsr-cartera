<div x-data="{ openNewBeneficiary: @entangle('openNewBeneficiary') }">
    <div class="relative inline-block">
        <button @click="openNewBeneficiary = true" @keydown.escape.window="openNewBeneficiary = false"
            class="text-xs bg-green-600 text-white p-2 rounded-lg hover:bg-green-700 active:bg-green-800 transition flex items-center gap-2">
            <i class="fa-solid fa-user-plus"></i><span>Nuevo Beneficiario</span>
        </button>
    </div>

    <div x-show="openNewBeneficiary" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
        <div class="fixed inset-0 transition-opacity" @click="openNewBeneficiary = false">
            <div class="absolute inset-0 bg-gray-500 opacity-25"></div>
        </div>
        <div
            class="bg-white rounded-lg shadow-md transform transition-all sm:max-w-4xl sm:w-full m-4 z-50 overflow-hidden border-2 border-gray-400">
            <div class="bg-gray-100 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Nuevo Beneficiario</h3>
                    <span class="text-xs text-gray-400">
                        Este beneficiario será creado bajo la <b>responsabilidad</b> de
                        <b>{{ Auth::user()->name }}</b></span>
                </div>
                <div>
                    <button @click="openNewBeneficiary = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="saveBeneficiary" class="p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                {{-- Sección: Información Personal --}}
                <div class="mb-6 pb-4 border-l-2 border-green-800 px-4">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 bg-gray-200 p-2 rounded">
                        Información Personal
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nombre Completo --}}
                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre
                                Completo</label>
                            <input type="text" id="nombre" wire:model.live="nombre"
                                @input="$event.target.value = $event.target.value.toUpperCase()"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('nombre')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- CI --}}
                        <div>
                            <label for="ci" class="block text-sm font-medium text-gray-700">C.I.</label>
                            <input type="text" id="ci" wire:model.live="ci"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('ci')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Complemento --}}
                        <div>
                            <label for="complemento" class="block text-sm font-medium text-gray-700">Complemento</label>
                            <input type="text" id="complemento" wire:model="complemento"
                                @input="$event.target.value = $event.target.value.toUpperCase()"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('complemento')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- IDEPRO (Autogenerado) --}}
                        <div>
                            <label for="idepro" class="block text-sm font-medium text-gray-700">IDEPRO</label>
                            <input type="text" id="idepro" wire:model.live="idepro" readonly
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('idepro')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Expedido --}}
                        <div>
                            <label for="expedido" class="block text-sm font-medium text-gray-700">Expedido en</label>
                            <select id="expedido" wire:model.live="expedido"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Seleccione</option>
                                <option value="LA PAZ">La Paz</option>
                                <option value="COCHABAMBA">Cochabamba</option>
                                <option value="SANTA CRUZ">Santa Cruz</option>
                                <option value="ORURO">Oruro</option>
                                <option value="POTOSI">Potosí</option>
                                <option value="CHUQUISACA">Chuquisaca</option>
                                <option value="TARIJA">Tarija</option>
                                <option value="BENI">Beni</option>
                                <option value="PANDO">Pando</option>
                            </select>
                            @error('expedido')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Departamento --}}
                        <div>
                            <label for="departamento"
                                class="block text-sm font-medium text-gray-700">Departamento</label>
                            <select id="departamento" wire:model.live="departamento"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Seleccione</option>
                                <option value="LA PAZ">La Paz</option>
                                <option value="COCHABAMBA">Cochabamba</option>
                                <option value="SANTA CRUZ">Santa Cruz</option>
                                <option value="ORURO">Oruro</option>
                                <option value="POTOSI">Potosí</option>
                                <option value="CHUQUISACA">Chuquisaca</option>
                                <option value="TARIJA">Tarija</option>
                                <option value="BENI">Beni</option>
                                <option value="PANDO">Pando</option>
                            </select>
                            @error('departamento')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Género --}}
                        <div>
                            <label for="genero" class="block text-sm font-medium text-gray-700">Género</label>
                            <select id="genero" wire:model="genero"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Seleccione</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                            @error('genero')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select id="estado" wire:model="estado"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Seleccione</option>
                                <option value="VIGENTE">VIGENTE</option>
                                <option value="VENCIDO">VENCIDO</option>
                                <option value="EJECUCION">EJECUCION</option>
                                <option value="CANCELADO">CANCELADO</option>
                                <option value="BLOQUEADO">BLOQUEADO</option>
                            </select>
                            @error('estado')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Fecha de Nacimiento --}}
                        <div>
                            <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700">Fecha de
                                Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" wire:model="fecha_nacimiento"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('fecha_nacimiento')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Proyecto --}}
                        <div>
                            <label for="proyecto" class="block text-sm font-medium text-gray-700">Proyecto</label>
                            <input type="text" id="proyecto" wire:model="proyecto"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('proyecto')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Entidad Financiera --}}
                        <div>
                            <label for="entidad_financiera" class="block text-sm font-medium text-gray-700">Entidad
                                Financiera</label>
                            <input type="text" id="entidad_financiera" wire:model="entidad_financiera"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('entidad_financiera')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Sección: Información del Crédito --}}
                <div class="mb-6 pb-4 border-l-2 border-green-800 px-4">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 bg-gray-200 p-2 rounded">
                        Información del Crédito
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Monto Crédito --}}
                        <div>
                            <label for="monto_credito" class="block text-sm font-medium text-gray-700"
                                title="Valor total de entrega de la vivienda">
                                Monto Crédito
                            </label>
                            <input type="number" step="0.01" id="monto_credito" wire:model="monto_credito"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('monto_credito')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Monto Activado --}}
                        <div>
                            <label for="monto_activado" class="block text-sm font-medium text-gray-700"
                                title="Valor total para generar plan de pagos">
                                Monto Activado
                            </label>
                            <input type="number" step="0.01" id="monto_activado" wire:model="monto_activado"
                                class="mt-1 block w-full rounded-md border-green-600 shadow-sm focus:border-green-600 focus:ring focus:ring-green-600 focus:ring-opacity-50">
                            @error('monto_activado')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Total Activado --}}
                        <div>
                            <label for="total_activado" class="block text-sm font-medium text-gray-700"
                                title="Valor relacionado a migracion IEF">
                                Total Activado
                            </label>
                            <input type="number" step="0.01" id="total_activado" wire:model="total_activado"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('total_activado')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Saldo Crédito --}}
                        <div>
                            <label for="saldo_credito" class="block text-sm font-medium text-gray-700"
                                title="Valor restante por pagar">
                                Saldo Crédito
                            </label>
                            <input type="number" step="0.01" id="saldo_credito" wire:model="saldo_credito"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('saldo_credito')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Monto Recuperado --}}
                        <div>
                            <label for="monto_recuperado" class="block text-sm font-medium text-gray-700"
                                title="Valor recuperado por el banco">
                                Monto Recuperado
                            </label>
                            <input type="number" step="0.01" id="monto_recuperado" wire:model="monto_recuperado"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('monto_recuperado')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Fecha Activación --}}
                        <div>
                            <label for="fecha_activacion" class="block text-sm font-medium text-gray-700"
                                title="Fecha en que se activo el crédito">
                                Fecha de Activación
                            </label>
                            <input type="date" id="fecha_activacion" wire:model="fecha_activacion"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('fecha_activacion')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Plazo Crédito --}}
                        <div>
                            <label for="plazo_credito" class="block text-sm font-medium text-gray-700"
                                title="Número de meses para pagar el crédito">
                                Plazo Crédito
                            </label>
                            <input type="number" id="plazo_credito" wire:model="plazo_credito"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('plazo_credito')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Tasa Interés --}}
                        <div>
                            <label for="tasa_interes" class="block text-sm font-medium text-gray-700"
                                title="Porcentaje de interés por el crédito">
                                Tasa de Interés
                            </label>
                            <input type="number" step="0.01" id="tasa_interes" wire:model="tasa_interes"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('tasa_interes')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- User ID --}}
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700"
                                title="ID del usuario que registro el beneficiario">Cuenta Responsable</label>
                            <input type="number" id="user_id" wire:model="user_id" readonly
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-gray-200">
                            @error('user_id')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Gastos Administrativos --}}
                        <div>
                            <label for="gastos_administrativos" class="block text-sm font-medium text-gray-700"
                                title="Gastos administrativos">Gastos Administrativos</label>
                            <input type="number" id="gastos_administrativos" wire:model="gastos_administrativos"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('gastos_administrativos')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Gastos Judiciales --}}
                        <div>
                            <label for="gastos_judiciales" class="block text-sm font-medium text-gray-700"
                                title="Gastos judiciales">Gastos Judiciales</label>
                            <input type="number" id="gastos_judiciales" wire:model="gastos_judiciales"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('gastos_judiciales')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- Gastos Notariales --}}
                        <div>
                            <label for="gastos_notariales" class="block text-sm font-medium text-gray-700"
                                title="Gastos notariales">Gastos Notariales</label>
                            <input type="number" id="gastos_notariales" wire:model="gastos_notariales"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('gastos_notariales')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </form>
            <div class="bg-gray-200 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                <button wire:click="saveBeneficiary" wire:loading.attr="disabled"
                    wire:confirm="¿Estas seguro(a) de crear el perfil?"
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="saveBeneficiary">Crear Perfil</span>
                    <span wire:loading wire:target="saveBeneficiary">Creando...</span>
                </button>
            </div>
        </div>
    </div>
</div>
