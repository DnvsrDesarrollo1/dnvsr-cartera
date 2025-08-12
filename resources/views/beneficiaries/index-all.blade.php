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
    <div class="mx-auto px-4">
        <div class="bg-white overflow-x-auto shadow-lg overflow-y-auto p-2 rounded-lg border border-gray-300 mt-2 mb-2">
            @if (session('success'))
                <x-personal.alert type="success" message="{{ session('success') }}" goto="{{ session('link') }}" />
            @endif
            @if (session('error'))
                <x-personal.alert type="error" message="{{ session('error') }} : {{ session('data') }}" />
            @endif
            <div class="px-4">
                <livewire:beneficiary-table />
            </div>
        </div>
    </div>

</x-app-layout>
