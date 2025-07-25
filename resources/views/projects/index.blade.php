<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-center">
            <div class="m-4">
                <span class="text-gray-500">EN PROCESO LEGAL:</span>
                <span class="text-gray-900 font-bold">{{ $procLegal }}</span>
            </div>
            <div class="m-4">
                <span class="text-gray-500">CONCLUIDO PARA CIERRE:</span>
                <span class="text-gray-900 font-bold">{{ $paraCierre }}</span>
            </div>
            <div class="m-4">
                <span class="text-gray-500">CONCLUIDO CON CIERRE:</span>
                <span class="text-gray-900 font-bold">{{ $conCierre }}</span>
            </div>
            <div class="m-4">
                <span class="text-gray-500">EJECUCION:</span>
                <span class="text-gray-900 font-bold">{{ $ejecucion }}</span>
            </div>
        </div>
    </x-slot>
    <div class="mx-auto px-6 py-4 sm:px-4 lg:px-6">
        <div class="bg-white overflow-x-auto shadow-lg rounded-lg overflow-y-auto mt-4 p-2">
            @if (session('success'))
                <div class="bg-green-200 border border-green-600 text-green-600 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong class="font-bold">Éxito!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                    @if (session('link'))
                        <a href="{{ session('link') }}" target="_blank" class="ml-2 mr-2 cursor-pointer font-bold">
                            Descargar ZIP
                        </a>
                    @endif
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-200 border border-red-500 text-red-500 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong class="font-bold">Oops...</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            <div class="px-4">
                <livewire:project-table />
            </div>
        </div>
    </div>

</x-app-layout>
