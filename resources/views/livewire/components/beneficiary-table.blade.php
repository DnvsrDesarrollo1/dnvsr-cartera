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
                        placeholder="Buscar por nombre, CI o código..."
                        class="w-full pl-3 pr-10 py-1.5 text-sm border-0 rounded bg-gray-50 focus:ring-1 focus:ring-indigo-200 uppercase"
                        oninput="this.value = this.value.toUpperCase()">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-600">
                        @if ($search)
                            <button wire:click="$set('search', '')" class="mr-2 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
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
                        <svg fill="#808080" height="24px" width="24px" version="1.1" id="Capa_1"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            viewBox="0 0 442 442" xml:space="preserve">
                            <g id="SVGRepo_bgCarrier" stroke-width="0" />
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" />
                            <g id="SVGRepo_iconCarrier">
                                <g>
                                    <path
                                        d="M120.586,248.59c-3.906-3.903-10.237-3.903-14.143,0l-54.972,54.972c-3.905,3.905-3.905,10.237,0,14.143 c1.953,1.952,4.512,2.929,7.071,2.929c2.559,0,5.119-0.977,7.071-2.929l54.972-54.972 C124.491,258.827,124.491,252.495,120.586,248.59z" />
                                    <path
                                        d="M158.054,308.206c-3.906-3.904-10.237-3.904-14.143,0l-43.898,43.898c-3.905,3.905-3.905,10.237,0,14.143 c1.953,1.952,4.512,2.929,7.071,2.929s5.119-0.977,7.071-2.929l43.898-43.898C161.959,318.443,161.959,312.111,158.054,308.206z" />
                                    <path
                                        d="M98.427,319.291c-3.905-3.904-10.237-3.904-14.143,0l-8.542,8.542c-3.905,3.905-3.905,10.237,0,14.143 c1.953,1.952,4.512,2.929,7.071,2.929s5.119-0.977,7.071-2.929l8.542-8.542C102.333,329.528,102.333,323.196,98.427,319.291z" />
                                    <path
                                        d="M78.17,242.464c-3.905-3.903-10.237-3.904-14.142,0L27.2,279.29c-3.905,3.905-3.905,10.237,0,14.143 c1.953,1.952,4.512,2.929,7.071,2.929c2.559,0,5.119-0.977,7.071-2.929l36.827-36.826C82.074,252.701,82.075,246.369,78.17,242.464 z" />
                                    <path
                                        d="M442,44.882c0-11.992-4.669-23.266-13.149-31.744c-17.503-17.502-45.984-17.502-63.488,0L254.158,124.344l-44.483-44.482 c-12.244-12.243-32.167-12.243-44.411,0l-13.405,13.404c-3.683,3.683-3.892,9.524-0.628,13.453l-5.849,5.848 c-3.929-3.264-9.77-3.054-13.453,0.629c-3.683,3.684-3.892,9.524-0.628,13.453L2.929,255.019c-3.905,3.905-3.905,10.237,0,14.143 c3.905,3.904,10.237,3.904,14.143,0L145.413,140.82l34.4,34.4l-52.156,52.155c-3.905,3.905-3.905,10.237,0,14.143 c3.906,3.904,10.238,3.905,14.142,0l52.156-52.155l58.671,58.671l-17.51,17.51c-3.905,3.905-3.905,10.237,0,14.143 c3.905,3.904,10.237,3.904,14.143,0l17.51-17.511l34.401,34.401L172.828,424.918c-3.906,3.905-3.906,10.237,0,14.143 c1.952,1.952,4.512,2.929,7.071,2.929s5.119-0.977,7.071-2.929l128.372-128.372c3.929,3.264,9.77,3.053,13.453-0.629 c3.683-3.684,3.892-9.524,0.629-13.452l5.848-5.849c1.786,1.48,4.042,2.301,6.381,2.301c2.652,0,5.196-1.054,7.071-2.929 l13.404-13.404c5.931-5.932,9.198-13.818,9.198-22.206c0-8.389-3.267-16.274-9.198-22.206l-44.483-44.482L428.852,76.626 C437.331,68.146,442,56.873,442,44.882z M315.312,282.434L201.067,168.19c-0.013-0.014-0.027-0.027-0.041-0.041 c-0.014-0.014-0.028-0.027-0.042-0.041l-41.43-41.43l5.787-5.787l155.756,155.756L315.312,282.434z M414.709,62.483 L296.432,180.761c-3.906,3.905-3.906,10.237,0,14.143l51.553,51.554c0,0,0,0,0,0c2.154,2.153,3.34,5.018,3.34,8.063 s-1.186,5.909-3.34,8.063l-6.333,6.333l-78.436-78.435l-90.145-90.146l6.333-6.333c4.447-4.446,11.682-4.446,16.127,0 l51.554,51.554c0.008,0.008,0.016,0.016,0.024,0.023l15.79,15.79c3.905,3.904,10.237,3.904,14.143,0 c3.905-3.905,3.905-10.237,0-14.143l-8.743-8.742L379.506,27.28c9.706-9.705,25.498-9.705,35.204,0 C419.411,31.981,422,38.232,422,44.882C422,51.53,419.411,57.782,414.709,62.483z" />
                                    <path
                                        d="M220.974,293.828c-3.904-3.904-10.237-3.904-14.142,0l-82.547,82.547c-3.905,3.905-3.905,10.237,0,14.143 c1.953,1.952,4.512,2.929,7.071,2.929s5.119-0.977,7.071-2.929l82.546-82.547C224.88,304.065,224.88,297.733,220.974,293.828z" />
                                    <path
                                        d="M387.504,35.28c-5.292,5.295-5.292,13.908,0.002,19.205c2.647,2.646,6.124,3.969,9.601,3.969s6.954-1.322,9.601-3.969 c0.001-0.001,0.001-0.002,0.002-0.003c5.292-5.294,5.292-13.907-0.002-19.204C401.414,29.985,392.801,29.987,387.504,35.28z M401.646,49.423c-2.503,2.502-6.575,2.5-9.08-0.002c-2.502-2.503-2.502-6.575,0-9.077l0.002-0.003 c2.502-2.503,6.574-2.502,9.08,0.003C404.151,42.846,404.151,46.918,401.646,49.423z" />
                                    <path
                                        d="M199.526,363.82c-3.905-3.903-10.236-3.904-14.143,0l-36.826,36.826c-3.905,3.905-3.905,10.237,0,14.143 c1.953,1.952,4.512,2.929,7.071,2.929c2.559,0,5.119-0.977,7.071-2.929l36.827-36.826 C203.43,374.057,203.431,367.725,199.526,363.82z" />
                                </g>
                            </g>
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

    </div>

    <div class="overflow-x-auto relative mt-2" wire:loading.class.delay="opacity-50"
        wire:target="search,perPage,sortBy,filters,resetFilters">
        <table class="min-w-full">
            <thead class="rounded-md overflow-hidden">
                <tr class="divide-x-2 divide-white">
                    <th scope="col" class="p-4 sticky top-0 border-r-2 border-gray-200 bg-gray-200">
                        <input type="checkbox" wire:model.live.debounce.300ms="selectAll"
                            class="rounded border-gray-300 text-indigo-500 focus:ring-indigo-200">
                    </th>
                    @foreach (['nombre' => 'Nombre', 'ci' => 'CI/IDEPRO', 'estado' => 'Estado', 'monto_credito' => 'Monto Crédito', 'saldo_credito' => 'Saldo K'] as $field => $label)
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-800 uppercase tracking-wider sticky top-0 bg-gray-200 cursor-pointer"
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
                    <th scope="col" title="Cuotas Vencidas / Cuotas Pendientes"
                        class="px-6 py-3 text-left text-xs font-bold text-gray-800 uppercase tracking-wider sticky top-0 bg-gray-200">
                        CV/CP</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider sticky top-0 bg-gray-200">
                    </th>
                </tr>
            </thead>
            <tbody class="bg-gray-200" x-data="{ expandedRows: [] }">
                @foreach ($beneficiaries as $beneficiary)
                    <tr class="bg-white hover:bg-gray-100 transition-colors duration-200 divide-x-2 divide-gray-200">
                        <td class="p-4">
                            <input type="checkbox" wire:model.live.debounce.300ms="selected"
                                value="{{ $beneficiary->id }}"
                                class="w-4 h-4 rounded border-gray-300 text-indigo-500 focus:ring-indigo-200">
                        </td>
                        <td class="px-6 py-4">
                            <div x-data="{ editing: false, value: '{{ $beneficiary->nombre }}' }"
                                @click.away="if(editing) { editing = false; $wire.save('{{ $beneficiary->id }}', 'nombre', value) }">
                                <div class="flex items-center space-x-2" x-show="!editing">
                                    <span @click="editing = true; $nextTick(() => $refs.input.focus())"
                                        class="cursor-pointer text-sm text-gray-900" x-text="value"></span>
                                    <button
                                        @click.prevent="
                                        const index = expandedRows.indexOf({{ $beneficiary->id }});
                                        if (index > -1) {
                                            expandedRows.splice(index, 1);
                                        } else {
                                            expandedRows.push({{ $beneficiary->id }});
                                        }
                                    ">
                                        <span class="transform transition-transform duration-200"
                                            :class="{ 'rotate-180': expandedRows.includes({{ $beneficiary->id }}) }">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                                <div x-show="editing" x-cloak>
                                    <input type="text" x-ref="input" x-model="value"
                                        @keydown.enter="editing = false; $wire.save('{{ $beneficiary->id }}', 'nombre', value)"
                                        @keydown.escape="editing = false"
                                        class="w-full px-1 py-0.5 text-sm border border-indigo-300 rounded focus:ring-1 focus:ring-indigo-200 uppercase"
                                        oninput="this.value = this.value.toUpperCase()"
                                        title="Presione Enter para guardar, o click fuera para guardar...">
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div x-data="{ editing: false, value: '{{ $beneficiary->ci }}' }"
                                @click.away="if(editing) { editing = false; $wire.save('{{ $beneficiary->id }}', 'ci', value) }">
                                <div @click="editing = true; $nextTick(() => $refs.input.focus())"
                                    class="cursor-pointer" x-show="!editing">
                                    <div x-text="value"></div>
                                </div>
                                <div x-show="editing" x-cloak>
                                    <input type="text" x-ref="input" x-model="value"
                                        @keydown.enter="editing = false; $wire.save('{{ $beneficiary->id }}', 'ci', value)"
                                        @keydown.escape="editing = false"
                                        class="w-full px-1 py-0.5 text-sm border border-indigo-300 rounded focus:ring-1 focus:ring-indigo-200 uppercase"
                                        title="Presione Enter para guardar, o click fuera para guardar...">
                                </div>
                            </div>
                            <div class="mt-1">
                                {{ $beneficiary->idepro }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div x-data="{ editing: false, value: '{{ $beneficiary->estado }}' }"
                                @click.away="if(editing) { editing = false; $wire.save('{{ $beneficiary->id }}', 'estado', value) }">
                                <div @click="editing = true; $nextTick(() => $refs.select.focus())"
                                    class="cursor-pointer" x-show="!editing">
                                    <span x-text="value" @class([
                                        'px-2 py-1 text-xs font-medium rounded-full',
                                        'bg-red-100 text-red-800' => $beneficiary->estado === 'BLOQUEADO',
                                        'bg-blue-100 text-blue-800' => $beneficiary->estado === 'CANCELADO',
                                    ])></span>
                                </div>
                                <div x-show="editing" x-cloak>
                                    <select x-ref="select" x-model="value"
                                        @change="editing = false; $wire.save('{{ $beneficiary->id }}', 'estado', value)"
                                        @keydown.escape="editing = false"
                                        class="w-full px-1 py-0.5 text-sm border border-indigo-300 rounded focus:ring-1 focus:ring-indigo-200"
                                        title="Click fuera del campo para guardar cambios o presione ESC para cancelar...">
                                        @foreach ($statusOptions as $status)
                                            <option value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ number_format($beneficiary->monto_activado, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <span @class([
                                'px-2 py-1 font-bold rounded-full',
                                'bg-blue-100 text-blue-800' => $beneficiary->saldo_credito <= 0,
                            ])>
                                {{ number_format($beneficiary->saldo_credito, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $beneficiary->getCurrentPlan('CANCELADO', '!=')->where('fecha_ppg', '<', now())->count() ?? 'N/A' }}
                            /
                            {{ $beneficiary->getCurrentPlan('CANCELADO', '!=')->count() ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 bf-gray-100">
                            <div class="flex items-center justify-evenly space-x-2">
                                <a href="{{ route('beneficiario.show', $beneficiary->ci) }}" target="_blank"
                                    title="Administrar Perfil"
                                    class="text-green-600 hover:scale-125 transition-all transform text-xl">
                                    <i class="fa-solid fa-user-gear"></i>
                                </a>
                                <a href="{{ route('beneficiario.pdf', $beneficiary->ci) }}" target="_blank"
                                    title="Ver Plan de Pagos Vigente"
                                    class="text-green-600 hover:scale-125 transition-all transform text-xl">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </a>
                                <a href="{{ route('beneficiario.pdf-extract', $beneficiary->ci) }}" target="_blank"
                                    title="Ver Extracto de Pagos"
                                    class="text-green-600 hover:scale-125 transition-all transform text-xl">
                                    <i class="fa-solid fa-book"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr class="bg-gray-50" x-show="expandedRows.includes({{ $beneficiary->id }})" x-cloak>
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
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
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
