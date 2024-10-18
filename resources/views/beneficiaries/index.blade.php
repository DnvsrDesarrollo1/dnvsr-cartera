<x-app-layout>
    <div class="mx-auto px-6 py-4 sm:px-4 lg:px-6">
        <div class="flex items-center justify-center shadow-lg bg-white rounded-lg">
            <div class="m-4">
                <span class="text-blue-500">Cancelados:</span>
                <span class="text-gray-600">{{ $cancelados }}</span>
            </div>
            <div class="m-4">
                <span class="text-green-600">Vigentes:</span>
                <span class="text-gray-600">{{ $vigentes }}</span>
            </div>
            <div class="m-4">
                <span class="text-yellow-600">Vencidos:</span>
                <span class="text-gray-600">{{ $vencidos }}</span>
            </div>
            <div class="m-4">
                <span class="text-red-600">Ejecucion:</span>
                <span class="text-gray-600">{{ $ejecuciones }}</span>
            </div>
        </div>
        <hr>
        <div class="bg-white overflow-x-auto shadow-lg rounded-lg overflow-y-auto mt-4 p-2">
            @if (session('success'))
                <div class="bg-green-200 border border-green-600 text-green-600 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong class="font-bold">Éxito!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-200 border border-red-500 text-red-500 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong class="font-bold">Oops...</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            <livewire:beneficiary-table />
        </div>
    </div>

</x-app-layout>
