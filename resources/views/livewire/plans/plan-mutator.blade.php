<div id="profile_plan_mutator">
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
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4 sm:p-6">

            <!-- Modal Content -->
            <div class="w-full max-w-7xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all"
                @click.outside="mutatorModal = false">

                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 tracking-tight">
                            Mutador de Planes de Pago
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Inserción masiva de montos.</p>
                    </div>
                    <button @click="mutatorModal = false"
                        class="rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
                        title="Cerrar">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body / Main Content -->
                <div class="flex flex-col lg:flex-row h-[70vh] lg:h-[600px]">

                    <!-- Left Panel: Controls -->
                    <div
                        class="w-full lg:w-1/3 bg-gray-50/50 border-r border-gray-100 p-6 flex flex-col gap-6 overflow-y-auto">
                        <form wire:submit.prevent="mutate" id="mutatorForm" class="space-y-6">

                            <!-- Control Groups -->
                            <div class="space-y-4">
                                <div>
                                    <label for="affectedQuotas"
                                        class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                                        Cuotas a Afectar
                                    </label>
                                    <div class="relative">
                                        <input type="number" name="affectedQuotas" id="affectedQuotas"
                                            wire:model.live="affectedQuotas" wire:change="getNQuotas" min="0"
                                            max="{{ $maxQuotas }}"
                                            class="block w-full rounded-lg border-gray-200 bg-white px-4 py-2.5 text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all focus:outline-none focus:ring-2">
                                        <div
                                            class="absolute inset-y-0 right-8 flex items-center pr-3 pointer-events-none">
                                            <span class="text-gray-400 text-xs">/ {{ $maxQuotas }}</span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">Número de cuotas desde la más próxima.</p>
                                </div>

                                <div>
                                    <label for="amountToInsert"
                                        class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                                        Monto Total a Insertar
                                    </label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-400 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" name="amountToInsert" id="amountToInsert"
                                            wire:model.live="amountToInsert" step="0.01"
                                            class="block w-full rounded-lg border-gray-200 pl-7 px-4 py-2.5 text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all focus:outline-none focus:ring-2"
                                            placeholder="0.00">
                                    </div>
                                </div>

                                <div>
                                    <label for="amountCriteria"
                                        class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                                        Criterio / Concepto
                                    </label>
                                    <select name="amountCriteria" id="amountCriteria" wire:model.live="amountCriteria"
                                        class="block w-full rounded-lg border-gray-200 bg-white px-4 py-2.5 text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all focus:outline-none focus:ring-2">
                                        <option value="">Seleccione un criterio...</option>
                                        <option value="GASTOS ADMINISTRATIVOS">GASTOS ADMINISTRATIVOS</option>
                                        <option value="GASTOS JUDICIALES">GASTOS JUDICIALES</option>
                                        <option value="GASTOS NOTARIALES">GASTOS NOTARIALES</option>
                                        <option value="OTROS">OTROS</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Summary Card -->
                            <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100 shadow-sm">
                                <label class="block text-xs font-bold uppercase tracking-wider text-indigo-400 mb-1">
                                    Incremento por Cuota
                                </label>
                                <div class="text-2xl font-bold text-indigo-700 tracking-tight">
                                    + {{ number_format((float) $amountPerQuota, 2) }}
                                </div>
                                <p class="text-xs text-indigo-400 mt-1">Este monto se añadirá a cada cuota seleccionada.
                                </p>
                            </div>

                            <!-- Feedback Alerts -->
                            @if (session()->has('success'))
                                <div class="rounded-lg bg-emerald-50 p-4 border border-emerald-100 flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-emerald-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                                    </div>
                                </div>
                            @endif

                        </form>
                    </div>

                    <!-- Right Panel: Preview Table -->
                    <div class="w-full lg:w-2/3 bg-white flex flex-col relative">
                        <!-- Toolbar / Legend -->
                        <div
                            class="px-6 py-3 border-b border-gray-50 flex items-center justify-between bg-white text-xs text-gray-500">
                            <div class="flex space-x-6">
                                <span class="flex items-center"><span
                                        class="w-2 h-2 rounded-full bg-gray-300 mr-2"></span> Actual</span>
                                <span class="flex items-center"><span
                                        class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> Incremento</span>
                                <span class="flex items-center"><span
                                        class="w-2 h-2 rounded-full bg-indigo-600 mr-2"></span> Nuevo Total</span>
                            </div>
                            <span class="font-medium text-gray-400">Vista Previa</span>
                        </div>

                        <!-- Table Area -->
                        <div class="flex-1 overflow-auto relative custom-scrollbar">
                            <!-- Loading Overlay -->
                            <div wire:loading.flex wire:target="getNQuotas"
                                class="absolute inset-0 bg-white/80 backdrop-blur-[1px] z-10 items-center justify-center">
                                <div class="flex flex-col items-center animate-pulse">
                                    <div class="h-8 w-8 text-indigo-500 mb-2">
                                        <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-indigo-600">Calculando y generando escenario...</span>
                                </div>
                            </div>

                            <table class="min-w-full divide-y divide-gray-50">
                                <thead class="bg-white sticky top-0 z-0 shadow-sm">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider bg-white">
                                            </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider bg-white">
                                            N° Cuota</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider bg-white">
                                            Fecha</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider bg-white">
                                            Otros Pagos</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider bg-white">
                                            Cuota Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-50 text-gray-600">
                                    @foreach ($plan as $quota)
                                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                                            <td class="px-6 py-3 text-sm font-medium w-16">
                                                <span
                                                    class="px-2 py-0.5 text-xs text-gray-500 font-mono">
                                                    {{ $loop->iteration }})
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 text-sm font-medium w-16">
                                                <span
                                                    class="bg-gray-200 px-2 py-0.5 rounded text-xs text-gray-700 font-mono">
                                                    °{{ $quota->prppgnpag }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 text-sm">
                                                {{ \Carbon\Carbon::parse($quota->fecha_ppg)->format('d M, Y') }}
                                            </td>

                                            <!-- Column: Otros -->
                                            <td class="px-6 py-3 text-right text-sm">
                                                <div class="flex flex-col items-end gap-0.5">
                                                    <div
                                                        class="flex items-center gap-1.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                                        <span
                                                            class="text-gray-400 line-through text-xs decoration-gray-300">{{ number_format($quota->prppgotro, 2) }}</span>
                                                        <span
                                                            class="text-emerald-600 font-bold bg-emerald-50 px-1.5 rounded text-[10px]">+{{ number_format($amountPerQuota, 2) }}</span>
                                                    </div>
                                                    <span
                                                        class="font-bold text-indigo-600">{{ number_format($quota->prppgotro + $amountPerQuota, 2) }}</span>
                                                </div>
                                            </td>

                                            <!-- Column: Total -->
                                            <td class="px-6 py-3 text-right text-sm">
                                                <div class="flex flex-col items-end gap-0.5">
                                                    <div
                                                        class="flex items-center gap-1.5 text-xs opacity-60 group-hover:opacity-100 transition-opacity">
                                                        <span
                                                            class="text-gray-400 line-through">{{ number_format($quota->prppgtota, 2) }}</span>
                                                        <span
                                                            class="text-emerald-500 opacity-80 text-[10px]">+{{ number_format($amountPerQuota, 2) }}</span>
                                                    </div>
                                                    <span
                                                        class="font-black text-gray-800 tracking-tight">{{ number_format($quota->prppgtota + $amountPerQuota, 2) }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                    <div class="text-xs text-gray-400 hidden sm:block">
                        * Los cambios son irreversibles una vez confirmados.
                    </div>
                    <div class="flex gap-3 w-full sm:w-auto justify-end">
                        <button @click="mutatorModal = false" type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            Cancelar
                        </button>
                        <x-personal.button variant="primary" iconLeft="fa-solid fa-bolt" wire:click="mutate"
                            wire:confirm="¿Está seguro de mutar el plan de pagos? Esta acción no se puede deshacer fácilmente."
                            wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-wait">
                            <span wire:loading.remove wire:target="mutate">Aplicar Mutación</span>
                            <span wire:loading wire:target="mutate">Procesando...</span>
                        </x-personal.button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
