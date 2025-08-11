<div>
    @if ($selected)
        <div class="flex justify-end items-center gap-2 mb-2">
            <x-personal.button wire:click="bulkActivation" iconLeft="fa-solid fa-bolt">
                x ({{ count($selected) }})
            </x-personal.button>
            <x-personal.button variant="outline-secondary"
                href="{{ route('beneficiario.bulk-pdf', ['data' => json_encode(collect($selected))]) }}"
                iconLeft="fa-solid fa-file-pdf">
                x ({{ count($selected) }})
            </x-personal.button>
        </div>
    @endif
    <div class="py-2 relative">
        <div class="flex items-center gap-3 bg-white p-2 rounded-lg shadow-sm border border-gray-400">
            <div class="flex-1 flex items-center gap-2">
                <div class="relative flex-1">
                    <input type="text" wire:model.live.debounce.500ms="search"
                        placeholder="Buscar por nombre, CI o IDEPRO..."
                        class="w-full pl-3 pr-10 py-1.5 text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200 uppercase"
                        oninput="this.value = this.value.toUpperCase()">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <select wire:model.live.debounce.300ms="perPage"
                    class="text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                </select>
            </div>

            <button wire:click="$toggle('showFilters')"
                class="flex items-center gap-1 px-3 py-1.5 text-sm text-indigo-600 hover:text-indigo-800 focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                Mas Filtros...
            </button>
        </div>

        <div x-data="{ showFilters: @entangle('showFilters').live }">
            <div x-show="showFilters" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95" x-cloak
                class="bg-white shadow-sm rounded-lg absolute z-50 border border-gray-400 w-full px-4 py-2">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
                    <div class="space-y-1">
                        <label class="text-xs text-gray-600">Estado</label>
                        <select wire:model.live="filters.estado"
                            class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200">
                            <option value="">Todos</option>
                            @foreach ($filterOptions['estados'] as $estado)
                                <option value="{{ $estado }}">{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs text-gray-600">Entidad Financiera</label>
                        <select wire:model.live="filters.entidad_financiera"
                            class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200">
                            <option value="">Todas</option>
                            @foreach ($filterOptions['entidades'] as $entidad)
                                <option value="{{ $entidad }}">{{ $entidad }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs text-gray-600">Departamento</label>
                        <select wire:model.live="filters.departamento"
                            class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200">
                            <option value="">Todos</option>
                            @foreach ($filterOptions['departamentos'] as $departamento)
                                <option value="{{ $departamento }}">{{ $departamento }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs text-gray-600">Fecha Activación</label>
                        <div class="flex gap-1">
                            <input type="date" wire:model.live="filters.fecha_activacion_desde"
                                class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200">
                            <input type="date" wire:model.live="filters.fecha_activacion_hasta"
                                class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs text-gray-600">Monto Crédito</label>
                        <div class="flex gap-1">
                            <input type="number" wire:model.live.debounce.300ms="filters.monto_credito_min"
                                class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200"
                                placeholder="Min">
                            <input type="number" wire:model.live.debounce.300ms="filters.monto_credito_max"
                                class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200"
                                placeholder="Max">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs text-gray-600">Saldo Crédito</label>
                        <div class="flex gap-1">
                            <input type="number" wire:model.live.debounce.300ms="filters.saldo_credito_min"
                                class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200"
                                placeholder="Min">
                            <input type="number" wire:model.live.debounce.300ms="filters.saldo_credito_max"
                                class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200"
                                placeholder="Max">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-3">
                    <button wire:click="resetFilters"
                        class="text-xs text-gray-500 hover:text-gray-700 hover:bg-gray-100 flex items-center gap-1 border p-2 rounded transition duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Limpiar
                    </button>
                </div>
            </div>
        </div>

    </div>

    <div class="overflow-x-auto relative" wire:loading.class.delay="opacity-50"
        wire:target="search,perPage,sortBy,filters,resetFilters">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="p-4 sticky top-0 bg-gray-50">
                        <input type="checkbox" wire:model.live.debounce.300ms="selectAll"
                            class="rounded border-gray-300 text-indigo-500 focus:ring-indigo-200">
                    </th>
                    @foreach (['nombre' => 'Nombre', 'ci' => 'CI/IDEPRO', 'estado' => 'Estado', 'monto_credito' => 'Monto Crédito'] as $field => $label)
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-800 uppercase tracking-wider sticky top-0 bg-gray-50 cursor-pointer"
                            wire:click="sortBy('{{ $field }}')">
                            <div class="flex items-center space-x-1">
                                <span>{{ $label }}</span>
                                @if ($sortField === $field)
                                    <span class="text-indigo-500">
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    </span>
                                @endif
                            </div>
                        </th>
                    @endforeach
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider sticky top-0 bg-gray-50">
                        CV/CP</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider sticky top-0 bg-gray-50">
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach ($beneficiaries as $beneficiary)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="p-4">
                            <input type="checkbox" wire:model.live.debounce.300ms="selected"
                                value="{{ $beneficiary->id }}"
                                class="w-4 h-4 rounded border-gray-300 text-indigo-500 focus:ring-indigo-200">
                        </td>
                        <td class="px-6 py-4">
                            <div wire:click="edit({{ $beneficiary->id }}, 'nombre')" class="cursor-pointer">
                                @if ($editingId === $beneficiary->id && $editingField === 'nombre')
                                    <input type="text" wire:model.live="editingValue" wire:keydown.enter="save" @click.away="$wire.save()"
                                        class="w-full px-1 py-0.5 text-sm border border-indigo-300 rounded focus:ring-1 focus:ring-indigo-200 uppercase"
                                        oninput="this.value = this.value.toUpperCase()">
                                @else
                                    <button class="flex items-center space-x-2 text-sm text-gray-900 ml-4"
                                        wire:click.stop="toggleRow({{ $beneficiary->id }})">
                                        <span>{{ $beneficiary->nombre }}</span>
                                        <span
                                            class="transform transition-transform duration-200 {{ in_array($beneficiary->id, $expandedRows) ? 'rotate-180' : '' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </span>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div wire:click="edit({{ $beneficiary->id }}, 'ci')" class="cursor-pointer">
                                @if ($editingId === $beneficiary->id && $editingField === 'ci')
                                    <input type="text" wire:model.live="editingValue" wire:keydown.enter="save" @click.away="$wire.save()"
                                        class="w-full px-1 py-0.5 text-sm border border-indigo-300 rounded focus:ring-1 focus:ring-indigo-200 uppercase">
                                @else
                                    <div>{{ $beneficiary->ci }}</div>
                                @endif
                            </div>
                            <div class="mt-1">
                                {{ $beneficiary->idepro }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div wire:click="edit({{ $beneficiary->id }}, 'estado')" class="cursor-pointer">
                                @if ($editingId === $beneficiary->id && $editingField === 'estado')
                                    <select wire:model.live="editingValue" wire:change="save" wire:keydown @click.away="$wire.save()"
                                        class="w-full px-1 py-0.5 text-sm border border-indigo-300 rounded focus:ring-1 focus:ring-indigo-200">
                                        @foreach($statusOptions as $status)
                                            <option value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <span @class([
                                        'px-2 py-1 text-xs font-medium rounded-full',
                                        'bg-red-100 text-red-800' => $beneficiary->estado === 'BLOQUEADO',
                                        'bg-blue-100 text-blue-800' => $beneficiary->estado === 'CANCELADO'
                                    ])>
                                        {{ $beneficiary->estado }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ number_format($beneficiary->monto_activado, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $beneficiary->getCurrentPlan('CANCELADO', '!=')->where('fecha_ppg', '<', now())->count() ?? 'N/A' }}
                            /
                            {{ $beneficiary->getCurrentPlan('CANCELADO', '!=')->count() ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('beneficiario.show', $beneficiary->ci) }}" target="_blank"
                                    class="text-gray-400 hover:text-blue-600 transition-colors">
                                    <i class="fa-solid fa-user-gear"></i>
                                </a>
                                <a href="{{ route('beneficiario.pdf', $beneficiary->ci) }}" target="_blank"
                                    class="text-gray-400 hover:text-green-600 transition-colors">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </a>
                                <a href="{{ route('beneficiario.pdf-extract', $beneficiary->ci) }}" target="_blank"
                                    class="text-gray-400 hover:text-purple-600 transition-colors">
                                    <i class="fa-solid fa-book"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @if (in_array($beneficiary->id, $expandedRows))
                        <tr class="bg-gray-50">
                            <td colspan="7" class="px-6 py-4">
                                <div class="grid grid-cols-3 gap-6">
                                    @foreach (['Entidad Financiera' => $beneficiary->entidad_financiera, 'Departamento' => $beneficiary->departamento, 'Fecha Activación' => $beneficiary->fecha_activacion] as $label => $value)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ $label }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $value }}</dd>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $beneficiaries->links() }}
    </div>
</div>
