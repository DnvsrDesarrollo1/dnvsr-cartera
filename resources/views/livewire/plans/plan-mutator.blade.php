<div>
    <!-- Modal -->
    <div x-data="{ mutatorModal: @entangle('mutatorModal') }">
        <!-- Trigger Button -->
        <x-personal.button variant="outline-primary" @click="mutatorModal = true" iconLeft="fa-solid fa-compass-drafting"
            size="sm">
            Mutador de PP
        </x-personal.button>

        <!-- Modal Background -->
        <div x-show="mutatorModal" @keyup.escape.window="mutatorModal = false"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
            <!-- Modal Content -->

            <div class="bg-white overflow-hidden rounded-lg transform transition-all sm:max-w-6xl sm:w-full m-4 z-50"
                @click.outside="mutatorModal = false">
                <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900">
                            Mutador de Planes de Pago
                        </h3>
                        <button @click="mutatorModal = false" class="text-gray-400 hover:text-gray-500"
                            title="Presione 'ESC' para cerrar.">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="px-2 sm:p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                    @if (session()->has('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form wire:submit.prevent="mutate" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="affectedQuotas" class="block text-sm font-medium text-gray-700">Cuotas a
                                    Afectar</label>
                                <input type="number" name="affectedQuotas" id="affectedQuotas"
                                    wire:model.live="affectedQuotas" wire:change="getNQuotas" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="amountToInsert" class="block text-sm font-medium text-gray-700">Monto a
                                    Insertar</label>
                                <input type="number" name="amountToInsert" id="amountToInsert"
                                    wire:model.live="amountToInsert" step="0.01"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="amountPerQuota" class="block text-sm font-medium text-gray-700">Monto
                                    Calculado</label>
                                <input type="number" name="amountPerQuota" id="amountPerQuota"
                                    wire:model.live="amountPerQuota" step="0.01" readonly
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="amountCriteria" class="block text-sm font-medium text-gray-700">Criterio del
                                    Monto</label>
                                <select name="amountCriteria" id="amountCriteria" wire:model.live="amountCriteria"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Seleccione un criterio</option>
                                    <option value="GASTOS ADMINISTRATIVOS">GASTOS ADMINISTRATIVOS</option>
                                    <option value="GASTOS JUDICIALES">GASTOS JUDICIALES</option>
                                    <option value="GASTOS NOTARIALES">GASTOS NOTARIALES</option>
                                    <option value="OTROS">OTROS</option>
                                </select>
                            </div>
                        </div>
                        <div class="relative">
                            <div wire:loading wire:target="getNQuotas"
                                class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10 rounded-lg">
                                <div class="flex flex-col items-center">
                                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-500"></div>
                                    <p class="text-sm text-gray-500">Preparando escenario, espere...</p>
                                </div>
                            </div>
                            <div class="overflow-x-auto shadow-sm sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                N° Cuota
                                            </th>
                                            <th scope="col"
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Fecha
                                            </th>
                                            <th scope="col"
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Otros (+ Extra)
                                            </th>
                                            <th scope="col"
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Total
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($plan as $quota)
                                            <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $quota->prppgnpag }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                                    {{ \Carbon\Carbon::parse($quota->fecha_ppg)->format('d/m/Y') }}</td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                                    <span class="text-gray-500 line-through mr-1">
                                                        {{ number_format($quota->prppgotro, 2) }}
                                                    </span>
                                                    <span class="text-green-600 font-semibold mr-1">+
                                                        {{ number_format($amountPerQuota, 2) }}</span>
                                                    <span class="font-bold text-blue-700"> =>
                                                        {{ number_format($quota->prppgotro + $amountPerQuota, 2) }}</span>
                                                </td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                                    <span
                                                        class="text-gray-500 line-through mr-1">{{ number_format($quota->prppgtota, 2) }}</span>
                                                    <span class="text-green-600 font-semibold mr-1">+
                                                        {{ number_format($amountPerQuota, 2) }}</span>
                                                    <span class="font-bold text-blue-700"> =>
                                                        {{ number_format($quota->prppgtota + $amountPerQuota, 2) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <x-personal.button variant="success" iconLeft="fa-solid fa-paper-plane" wire:click="mutate"
                                wire:confirm="¿Está seguro de mutar el plan de pagos?" wire:loading.attr="disabled"
                                wire:loading.class="opacity-75 cursor-not-allowed">
                                <span wire:loading.remove wire:target="mutate">Aplicar Cambios</span>
                                <span wire:loading wire:target="mutate">Aplicando Cambios....</span>
                            </x-personal.button>
                        </div>
                    </form>
                </div>
                <div class="flex items-center justify-between bg-gray-100 p-4 border-t border-gray-300">
                    <div class="flex items-center space-x-4 text-sm text-gray-700">
                        <span class="font-semibold">Simbología:</span>
                        <div class="flex items-center">
                            <span class="text-gray-500 mr-1">Monto actual</span>
                            <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-green-600 mr-1">Monto a incrementar</span>
                            <span class="w-3 h-3 bg-green-600 rounded-full"></span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-blue-700 mr-1">Nuevo Total</span>
                            <span class="w-3 h-3 bg-blue-700 rounded-full"></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
