<div>
    @if ($selected)
        <div class="flex flex-col sm:flex-row justify-end items-center gap-2 mb-2">
            <div class="w-full sm:w-auto">
                <x-personal.button iconLeft="fa-solid fa-bolt" class="w-full sm:w-auto"
                    href="{{ route('plan.bulk-activation', ['data' => json_encode(collect($selected))]) }}">
                    <span class="hidden sm:inline">Activación</span>
                    <span class="sm:hidden">Activar Seleccionados</span>
                    ({{ count($selected) }})
                </x-personal.button>
            </div>
            <div class="w-full sm:w-auto">
                <x-personal.button variant="outline-secondary"
                    href="{{ route('beneficiario.bulk-pdf', ['data' => json_encode(collect($selected))]) }}"
                    iconLeft="fa-solid fa-file-pdf" class="w-full sm:w-auto">
                    <span class="hidden sm:inline">PDF Planes</span>
                    <span class="sm:hidden">Generar PDF</span>
                    ({{ count($selected) }})
                </x-personal.button>
            </div>
            <div class="w-full sm:w-auto">
                <x-personal.button variant="outline-secondary"
                    href="{{ route('beneficiario.bulk-pdf-extract', ['data' => json_encode(collect($selected))]) }}"
                    iconLeft="fa-solid fa-file-pdf" class="w-full sm:w-auto">
                    <span class="hidden sm:inline">PDF Extractos</span>
                    <span class="sm:hidden">Generar PDF</span>
                    ({{ count($selected) }})
                </x-personal.button>
            </div>
            <div class="w-full sm:w-auto">
                <x-personal.button variant="outline-secondary"
                    href="{{ route('plan.bulk-xlsx', ['data' => json_encode(collect($selected))]) }}"
                    iconLeft="fa-solid fa-file-pdf" class="w-full sm:w-auto">
                    <span class="hidden sm:inline">XLSX Planes</span>
                    <span class="sm:hidden">Generar XLSX</span>
                    ({{ count($selected) }})
                </x-personal.button>
            </div>
        </div>
    @endif
    <div class="py-2 relative">
        <div class="flex items-center gap-3 bg-white p-2 rounded-lg shadow-sm border border-gray-400">
            <div class="flex-1 flex items-center gap-2">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.500ms="search" wire:keyup.enter="search"
                        placeholder="Buscar por nombre, CI o código..."
                        class="w-full pl-10 pr-10 py-1.5 text-sm border border-gray-300 rounded-md bg-white focus:ring-blue-500 focus:border-blue-500 uppercase placeholder-gray-400 shadow-sm"
                        oninput="this.value = this.value.toUpperCase()">
                    @if ($search)
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <button wire:click="$set('search', '')"
                                class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
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
                class="flex items-center gap-1 px-3 py-1.5 text-sm bg-green-700 text-white hover:text-green-200 focus:outline-none rounded-md transition-all ease-in-out">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                Mas Filtros...
            </button>
        </div>

        {{-- BLOQUE FILTROS --}}
        <div x-data="{ showFilters: @entangle('showFilters').live }">
            <!-- Contenedor principal de filtros -->
            <div x-show="showFilters" x-transition:enter="transition ease-out duration-300"
                @click.away="showFilters = false" x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95" x-cloak
                class="bg-white shadow-sm rounded-lg absolute z-50 border border-gray-400 w-full px-4 py-2 max-h-[80vh] overflow-y-auto">

                <!-- Grid principal responsivo -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <!-- Columna 1 -->
                    <div class="space-y-3">
                        <!-- Estado -->
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Estado</label>
                            <select wire:model.live="filters.estado"
                                class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200 p-2">
                                <option value="">Todos</option>
                                @foreach ($filterOptions['estados'] as $estado)
                                    <option value="{{ $estado }}">{{ $estado }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Proyecto -->
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Proyecto</label>
                            <select wire:model.live="filters.proyecto"
                                class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200 p-2">
                                <option value="">Todos</option>
                                @foreach ($filterOptions['proyectos'] as $proyecto)
                                    <option value="{{ $proyecto }}">{{ $proyecto }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fecha Activación -->
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Fecha Activación</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="date" wire:model.live="filters.fecha_activacion_desde"
                                    class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200 p-1">
                                <input type="date" wire:model.live="filters.fecha_activacion_hasta"
                                    class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200 p-1">
                            </div>
                        </div>
                    </div>

                    <!-- Columna 2 -->
                    <div class="space-y-3">
                        <!-- Entidad Financiera -->
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Entidad Financiera</label>
                            <select wire:model.live="filters.entidad_financiera"
                                class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200 p-2">
                                <option value="">Todas</option>
                                @foreach ($filterOptions['entidades'] as $entidad)
                                    <option value="{{ $entidad }}">{{ $entidad }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Monto Crédito -->
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Monto Crédito</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" wire:model.live.debounce.300ms="filters.monto_credito_min"
                                    class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200 p-1"
                                    placeholder="Mínimo">
                                <input type="number" wire:model.live.debounce.300ms="filters.monto_credito_max"
                                    class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200 p-1"
                                    placeholder="Máximo">
                            </div>
                        </div>
                    </div>

                    <!-- Columna 3 -->
                    <div class="space-y-3">
                        <!-- Departamento -->
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Departamento</label>
                            <select wire:model.live="filters.departamento"
                                class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200 p-2">
                                <option value="">Todos</option>
                                @foreach ($filterOptions['departamentos'] as $departamento)
                                    <option value="{{ $departamento }}">{{ $departamento }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Saldo Crédito -->
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-600">Saldo Crédito</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" wire:model.live.debounce.300ms="filters.saldo_credito_min"
                                    class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200 p-1"
                                    placeholder="Mínimo">
                                <input type="number" wire:model.live.debounce.300ms="filters.saldo_credito_max"
                                    class="w-full text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200 p-1"
                                    placeholder="Máximo">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón de limpiar filtros -->
                <div class="flex justify-end mt-4">
                    <button wire:click="resetFilters"
                        class="text-xs font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 flex items-center gap-1 border border-gray-300 p-2 rounded transition duration-200">
                        <i class="fa-solid fa-broom"></i>
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto relative" wire:loading.class.delay="opacity-50"
        wire:target="search,selectAll,perPage,sortBy,filters,resetFilters">
        <table class="min-w-full rounded-lg overflow-hidden shadow-sm border border-gray-400">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="p-4 sticky top-0 text-left">
                        <input type="checkbox" wire:model.live="selectAll"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    </th>
                    @foreach (['nombre' => 'Nombre', 'ci' => 'CI/IDEPRO', 'estado' => 'Estado', 'monto_credito' => 'Monto Crédito (k)', 'saldo_credito' => 'Saldo Crédito (k)'] as $field => $label)
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider sticky top-0 cursor-pointer whitespace-nowrap border-b-2"
                            wire:click="sortBy('{{ $field }}')">
                            <div class="flex items-center justify-between space-x-1">
                                <span class="hidden sm:inline">{{ $label }}</span>
                                <span class="sm:hidden">
                                    @if ($field === 'nombre')
                                        Nombre
                                    @elseif($field === 'ci')
                                        CI
                                    @elseif($field === 'estado')
                                        Estado
                                    @elseif($field === 'monto_credito')
                                        Monto (k)
                                    @else
                                        Saldo (k)
                                    @endif
                                </span>
                                @if ($sortField === $field)
                                    <span class="text-blue-700 text-lg">
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    </span>
                                @endif
                            </div>
                        </th>
                    @endforeach
                    <th scope="col" title="Cuotas Vencidas / Cuotas Pendientes"
                        class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider sticky top-0 whitespace-nowrap border-b-2">
                        <span class="hidden sm:inline">CV/CP</span>
                        <span class="sm:hidden">C</span>
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider sticky top-0 whitespace-nowrap border-b-2">
                        <span class="sr-only">Acciones</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" x-data="{ expandedRows: [] }">
                @foreach ($beneficiaries as $beneficiary)
                    <tr wire:key="beneficiary-row-{{ $beneficiary->id }}"
                        class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="p-4">
                            <input type="checkbox" wire:model.live="selected" value="{{ $beneficiary->id }}"
                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        </td>
                        <td class="px-4 py-3">
                            <div x-data="{ editing: false, value: '{{ $beneficiary->nombre }}' }"
                                @click.away="if(editing) { editing = false; $wire.save('{{ $beneficiary->id }}', 'nombre', value) }">
                                <div class="flex items-center justify-between space-x-2" x-show="!editing">
                                    <span @click="editing = true; $nextTick(() => $refs.input.focus())"
                                        class="cursor-pointer text-sm text-gray-900 truncate max-w-[100px] sm:max-w-none hover:text-indigo-600"
                                        x-text="value" title="{{ $beneficiary->nombre }}">
                                    </span>
                                </div>
                                <div x-show="editing" x-cloak>
                                    <input type="text" x-ref="input" x-model="value"
                                        @keydown.enter="editing = false; $wire.save('{{ $beneficiary->id }}', 'nombre', value)"
                                        @keydown.escape="editing = false"
                                        class="w-full px-2 py-1 text-sm border border-indigo-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 uppercase"
                                        oninput="this.value = this.value.toUpperCase()"
                                        title="Presione Enter para guardar, o click fuera para guardar...">
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <div x-data="{ editing: false, value: '{{ $beneficiary->ci }}' }"
                                @click.away="if(editing) { editing = false; $wire.save('{{ $beneficiary->id }}', 'ci', value) }">
                                <div @click="editing = true; $nextTick(() => $refs.input.focus())"
                                    class="cursor-pointer hover:text-indigo-600" x-show="!editing">
                                    <div x-text="value" class="truncate max-w-[80px] sm:max-w-none"></div>
                                </div>
                                <div x-show="editing" x-cloak>
                                    <input type="text" x-ref="input" x-model="value"
                                        @keydown.enter="editing = false; $wire.save('{{ $beneficiary->id }}', 'ci', value)"
                                        @keydown.escape="editing = false"
                                        class="w-full px-2 py-1 text-sm border border-indigo-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 uppercase"
                                        title="Presione Enter para guardar, o click fuera para guardar...">
                                </div>
                            </div>
                            <div class="mt-1 text-xs text-gray-400 truncate max-w-[80px] sm:max-w-none">
                                {{ $beneficiary->idepro }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div x-data="{ editing: false, value: '{{ $beneficiary->estado }}' }"
                                @click.away="if(editing) { editing = false; $wire.save('{{ $beneficiary->id }}', 'estado', value) }">
                                <div @click="editing = true; $nextTick(() => $refs.select.focus())"
                                    class="cursor-pointer" x-show="!editing">
                                    <span @class([
                                        'px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap',
                                        'bg-red-100 text-red-800' => $beneficiary->estado === 'BLOQUEADO',
                                        'bg-blue-100 text-blue-800' => $beneficiary->estado === 'CANCELADO',
                                        'bg-green-100 text-green-800' => $beneficiary->estado === 'ACTIVO',
                                        'bg-yellow-100 text-yellow-800' => $beneficiary->estado === 'PENDIENTE',
                                    ]) x-text="value"></span>
                                </div>
                                <div x-show="editing" x-cloak>
                                    <select x-ref="select" x-model="value"
                                        @change="editing = false; $wire.save('{{ $beneficiary->id }}', 'estado', value)"
                                        @keydown.escape="editing = false"
                                        class="w-full px-2 py-1 text-sm border border-indigo-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                        title="Click fuera del campo para guardar cambios o presione ESC para cancelar...">
                                        @foreach ($statusOptions as $status)
                                            <option value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                            {{ number_format($beneficiary->monto_activado, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                            <span @class([
                                'px-2 py-1 font-semibold rounded-full',
                                'bg-blue-100 text-blue-800' => $beneficiary->saldo_credito <= 0,
                                'bg-green-100 text-green-800' => $beneficiary->saldo_credito > 0,
                            ])>
                                {{ number_format($beneficiary->saldo_credito, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                            {{ count($beneficiary->plans->filter(fn($p) => $p->estado == 'VENCIDO')) }}
                            /
                            {{ count($beneficiary->plans->filter(fn($p) => $p->estado != 'CANCELADO')) }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('beneficiario.show', $beneficiary->ci) }}" target="_blank"
                                    title="Administrar Perfil"
                                    class="text-indigo-600 hover:text-indigo-800 hover:scale-110 transition-all transform text-lg">
                                    <i class="fa-solid fa-user-gear"></i>
                                </a>
                                <a href="{{ route('beneficiario.pdf', $beneficiary->ci) }}" target="_blank"
                                    title="Ver Plan de Pagos Vigente"
                                    class="text-indigo-600 hover:text-indigo-800 hover:scale-110 transition-all transform text-lg">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </a>
                                <a href="{{ route('beneficiario.pdf-extract', $beneficiary->ci) }}" target="_blank"
                                    title="Ver Extracto de Pagos"
                                    class="text-indigo-600 hover:text-indigo-800 hover:scale-110 transition-all transform text-lg">
                                    <i class="fa-solid fa-book"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="p-4 mt-2 bg-white rounded-lg">
        {{ $beneficiaries->links() }}
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notify', (message) => {
                Swal.fire({
                    icon: 'success',
                    title: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            });
        });
    </script>
</div>
