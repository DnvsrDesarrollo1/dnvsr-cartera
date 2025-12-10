<x-app-layout>
    <div class="relative mx-auto px-4 py-2 w-full">

        <button type="button" onclick="startBeneficiaryTour()"
            class="absolute top-[5.5rem] right-4 p-1 rounded-full bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400"
            title="Guía rápida">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        <div class="space-y-2 mt-2">
            @php
                $criterio = DB::table('beneficiaries')->distinct()->get('estado');
                $total = DB::table('beneficiaries')->count();
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2" id="index_status_grid">
                @foreach ($criterio as $c)
                    @php
                        $count = DB::table('beneficiaries')->where('estado', $c->estado)->count();
                        $percentage = ($count / $total) * 100;
                        $color = match ($c->estado) {
                            'BLOQUEADO' => 'bg-red-500',
                            'CANCELADO' => 'bg-orange-500',
                            'VIGENTE' => 'bg-green-500',
                            'MOROSO' => 'bg-yellow-500',
                            default => 'bg-blue-500',
                        };
                    @endphp
                    <div
                        class="bg-white border border-gray-100 rounded px-2 sm:px-3 py-1 sm:py-2 hover:shadow-sm transition-shadow">
                        <div class="flex justify-between items-center">
                            <span class="text-xs sm:text-sm text-gray-600 truncate" title="{{ $c->estado }}">
                                {{ Str::limit($c->estado, 12) }}
                            </span>
                            <span class="text-xs text-gray-400">{{ number_format($percentage, 1) }}%</span>
                        </div>
                        <div class="mt-1 sm:mt-2 flex items-center gap-1 sm:gap-2">
                            <div class="flex-grow bg-gray-100 rounded-full h-1.5">
                                <div class="{{ $color }} h-1.5 rounded-full" style="width: {{ $percentage }}%">
                                </div>
                            </div>
                            <span
                                class="text-xs font-medium text-gray-700 whitespace-nowrap">{{ number_format($count) }}</span>
                        </div>
                    </div>
                @endforeach
                @php
                    $periods = collect();
                    for ($i = 0; $i < 4; $i++) {
                        $periods->push(now()->subMonths($i)->format('Y-m'));
                    }
                    for ($i = 1; $i <= 4; $i++) {
                        $periods->prepend(now()->addMonths($i)->format('Y-m'));
                    }
                @endphp
                <div class="bg-gray-50 rounded-xl p-3 shadow-sm hover:shadow transition" id="index_seguros_card">
                    <form action="{{ route('plan.xlsx-seguros', ['periodo' => '__periodo__']) }}" method="GET"
                        id="frmXlsxSeguros" class="flex items-center gap-2">
                        <label for="periodo" class="text-gray-700 text-xs font-medium">Planilla Seguros</label>
                        <select name="periodo" id="periodo" class="text-xs border bg-white rounded-lg p-2 w-full">
                            @foreach ($periods as $p)
                                <option value="{{ $p }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $p }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" id="frmXlsxSeguros_submit"
                            class="text-xs bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 active:bg-blue-800 transition">
                            Exportar
                        </button>
                    </form>
                </div>
                <div id="index_create_beneficiary">
                    <livewire:beneficiaries.create-beneficiary lazy="on-load" />
                </div>
                <script>
                    document.getElementById('frmXlsxSeguros').addEventListener('submit', function(e) {
                        const periodo = document.getElementById('periodo').value;
                        this.action = this.action.replace('__periodo__', periodo);

                    });
                </script>
            </div>
        </div>
        <div class="overflow-x-auto overflow-y-auto rounded-lg"
            id="index_table_card">
            <div>
                <div id="index_alerts">
                    @if (session('success'))
                        <x-personal.alert type="success" message="{{ session('success') }}" />
                    @endif
                    @if (session('error'))
                        <x-personal.alert type="error" message="{{ session('error') }} : {{ session('data') }}" />
                    @endif
                </div>
            </div>
            <div id="index_table">
                @livewire('components.beneficiary-table')
                {{-- <livewire:components.beneficiary-table lazy="on-load" /> --}}
            </div>
        </div>
    </div>

</x-app-layout>
