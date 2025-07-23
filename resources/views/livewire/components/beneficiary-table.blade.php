<div>
    <div class="p-6">
        <div class="mb-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por nombre, CI, IDEPRO o código de proyecto"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-96">

                <select wire:model.live="perPage"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="25">25 por página</option>
                    <option value="50">50 por página</option>
                    <option value="100">100 por página</option>
                    <option value="500">500 por página</option>
                </select>
            </div>

            <button wire:click="toggleFilters"
                class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                {{ $showFilters ? 'Ocultar Filtros' : 'Mostrar Filtros' }}
            </button>
        </div>

        @if ($showFilters)
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select wire:model.live="filters.estado"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Todos</option>
                            @foreach ($filterOptions['estados'] as $estado)
                                <option value="{{ $estado }}">{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Entidad Financiera</label>
                        <select wire:model.live="filters.entidad_financiera"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Todas</option>
                            @foreach ($filterOptions['entidades'] as $entidad)
                                <option value="{{ $entidad }}">{{ $entidad }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                        <select wire:model.live="filters.departamento"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Todos</option>
                            @foreach ($filterOptions['departamentos'] as $departamento)
                                <option value="{{ $departamento }}">{{ $departamento }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Género</label>
                        <select wire:model.live="filters.genero"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Todos</option>
                            @foreach ($filterOptions['generos'] as $genero)
                                <option value="{{ $genero }}">{{ $genero }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Activación</label>
                        <div class="flex space-x-2">
                            <input type="date" wire:model.live="filters.fecha_activacion_desde"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Desde">
                            <input type="date" wire:model.live="filters.fecha_activacion_hasta"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Hasta">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto Crédito</label>
                        <div class="flex space-x-2">
                            <input type="number" wire:model.live.debounce.300ms="filters.monto_credito_min"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Mínimo">
                            <input type="number" wire:model.live.debounce.300ms="filters.monto_credito_max"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Máximo">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Saldo Crédito</label>
                        <div class="flex space-x-2">
                            <input type="number" wire:model.live.debounce.300ms="filters.saldo_credito_min"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Mínimo">
                            <input type="number" wire:model.live.debounce.300ms="filters.saldo_credito_max"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Máximo">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plazo Crédito (meses)</label>
                        <input type="number" wire:model.live.debounce.300ms="filters.plazo_credito"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            placeholder="Plazo en meses">
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button wire:click="resetFilters"
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        @endif

        <p>{{ print_r($selected) }}</p>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
        <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
            <thead>
                <tr class="text-left">
                    <th class="py-2 px-3 sticky top-0 border-b border-gray-200 bg-gray-100">
                        <input type="checkbox" wire:click="selectAll" wire:model.live="selected"
                            {{ count($selected) === $beneficiaries->total() ? 'checked' : '' }}>
                    </th>
                    <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-2 text-gray-600 font-bold tracking-wider uppercase text-xs cursor-pointer"
                        wire:click="sortBy('nombre')">
                        Nombre
                        @if ($sortField === 'nombre')
                            @if ($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-2 text-gray-600 font-bold tracking-wider uppercase text-xs cursor-pointer"
                        wire:click="sortBy('ci')">
                        CI
                        @if ($sortField === 'ci')
                            @if ($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-2 text-gray-600 font-bold tracking-wider uppercase text-xs cursor-pointer"
                        wire:click="sortBy('idepro')">
                        IDEPRO
                        @if ($sortField === 'idepro')
                            @if ($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-2 text-gray-600 font-bold tracking-wider uppercase text-xs cursor-pointer"
                        wire:click="sortBy('estado')">
                        Estado
                        @if ($sortField === 'estado')
                            @if ($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-2 text-gray-600 font-bold tracking-wider uppercase text-xs cursor-pointer"
                        wire:click="sortBy('monto_credito')">
                        Monto Crédito
                        @if ($sortField === 'monto_credito')
                            @if ($sortDirection === 'asc')
                                ↑
                            @else
                                ↓
                            @endif
                        @endif
                    </th>
                    <th
                        class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-2 text-gray-600 font-bold tracking-wider uppercase text-xs">
                        Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($beneficiaries as $beneficiary)
                    <tr class="hover:bg-gray-100">
                        <td class="border-dashed border-t border-gray-200 px-3">
                            <input type="checkbox" wire:model.live.debounce.200ms="selected" value="{{ $beneficiary->id }}" id="chkbox{{ $beneficiary->id }}" name="chkbox{{ $beneficiary->id }}"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </td>
                        <td class="border-dashed border-t border-gray-200 px-6 py-4">
                            <div class="flex items-center cursor-pointer"
                                wire:click="toggleRow({{ $beneficiary->id }})">
                                <span class="mr-2">{{ $beneficiary->nombre }}</span>
                                <span
                                    class="transform transition-transform duration-200 {{ in_array($beneficiary->id, $expandedRows) ? 'rotate-180' : '' }}">
                                    ▼
                                </span>
                            </div>
                        </td>
                        <td class="border-dashed border-t border-gray-200 px-6 py-4">{{ $beneficiary->ci }}</td>
                        <td class="border-dashed border-t border-gray-200 px-6 py-4">{{ $beneficiary->idepro }}</td>
                        <td class="border-dashed border-t border-gray-200 px-6 py-4">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $beneficiary->estado === 'ACTIVO' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $beneficiary->estado }}
                            </span>
                        </td>
                        <td class="border-dashed border-t border-gray-200 px-6 py-4">
                            {{ number_format($beneficiary->monto_credito, 2) }}</td>
                        <td class="border-dashed border-t border-gray-200 px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('beneficiario.show', $beneficiary->ci) }}" target="_blank"
                                    class="text-blue-600 hover:text-blue-900">Ver</a>
                                <a href="{{ route('beneficiario.pdf', $beneficiary->ci) }}" target="_blank"
                                    class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                <a href="{{ route('beneficiario.pdf-extract', $beneficiary->ci) }}" target="_blank"
                                    class="text-indigo-600 hover:text-indigo-900">Editar</a>
                            </div>
                        </td>
                    </tr>
                    @if (in_array($beneficiary->id, $expandedRows))
                        <tr class="bg-gray-50">
                            <td colspan="7" class="border-dashed border-t border-gray-200 px-6 py-4">
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Entidad Financiera</p>
                                        <p class="mt-1">{{ $beneficiary->entidad_financiera }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Departamento</p>
                                        <p class="mt-1">{{ $beneficiary->departamento }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Fecha Activación</p>
                                        <p class="mt-1">{{ $beneficiary->fecha_activacion }}</p>
                                    </div>
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
