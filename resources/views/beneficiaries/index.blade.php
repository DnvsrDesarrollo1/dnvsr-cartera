<x-app-layout>
    <div class="mx-auto px-4 py-2 w-full">
        <div class="space-y-2 my-2">
            @php
                $criterio = DB::table('beneficiaries')->distinct()->get('estado');
                $total = DB::table('beneficiaries')->count();
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
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
            </div>
        </div>
        {{-- <div
            class="flex items-center justify-center p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded mb-2">
            <svg class="animate-bounce h-5 w-5 mr-3" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 2.18l7 3.12v4.7c0 4.67-3.13 8.95-7 10.18-3.87-1.23-7-5.51-7-10.18V6.3l7-3.12zm-2 6.82h4v2h-4v-2zm0-4h4v2h-4V6z" />
            </svg>
            <span class="font-semibold">En desarrollo, espere el lanzamiento oficial...</span>
        </div> --}}
        <div class="bg-white overflow-x-auto shadow-lg overflow-y-auto p-2 rounded-lg border border-gray-300 mt-2 mb-2">
            <div class="px-4">
                @if (session('success'))
                    <x-personal.alert type="success" message="{{ session('success') }}" />
                @endif
                @if (session('error'))
                    <x-personal.alert type="error" message="{{ session('error') }} : {{ session('data') }}" />
                @endif
            </div>
            <div class="px-4">
                @livewire('components.beneficiary-table')
                {{-- <livewire:components.beneficiary-table lazy="on-load" /> --}}
            </div>
        </div>
    </div>

</x-app-layout>
