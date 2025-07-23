<x-app-layout>
    <x-slot name="header">
        @php
            $criterio = DB::table('beneficiaries')->distinct()->get('estado');
        @endphp
        <table class="table-auto border-collapse border border-gray-400 w-full text-center shadow-sm my-2">
            <tbody>
                <tr>
                    @foreach ($criterio as $c)
                        <td class="border border-gray-200 px-2 py-2">
                            {{ $c->estado }} :
                            <b>{{ DB::table('beneficiaries')->where('estado', $c->estado)->count() }}</b>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </x-slot>
    <div class="mx-auto px-4 w-full">
        <div class="flex items-center justify-center p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
            <svg class="animate-bounce h-5 w-5 mr-3" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 2.18l7 3.12v4.7c0 4.67-3.13 8.95-7 10.18-3.87-1.23-7-5.51-7-10.18V6.3l7-3.12zm-2 6.82h4v2h-4v-2zm0-4h4v2h-4V6z"/>
            </svg>
            <span class="font-semibold">Building, please stand by...</span>
        </div>
        <div class="bg-white overflow-x-auto shadow-lg overflow-y-auto p-2 rounded-lg border border-gray-300 mt-2 mb-2">
            @if (session('success'))
                <x-personal.alert type="success" message="{{ session('success') }}" goto="{{ session('link') }}" />
            @endif
            @if (session('error'))
                <x-personal.alert type="error" message="{{ session('error') }} : {{ session('data') }}" />
            @endif
            <div class="px-4">
                @livewire('components.beneficiary-table')
            </div>
        </div>
    </div>

</x-app-layout>
