<x-app-layout>
    <div class="relative mx-auto px-4 py-2 w-full">
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
