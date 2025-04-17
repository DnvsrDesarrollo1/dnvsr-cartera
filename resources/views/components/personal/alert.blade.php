@props([
    'type' => 'info',
    'message' => '',
    'goto' => null,
])

@php
    $alertClasses = [
        'success' =>
            'bg-green-100 dark:bg-green-800 border-l-4 border-green-500 dark:border-green-700 text-green-900 dark:text-green-100 hover:bg-green-200 dark:hover:bg-green-800',
        'info' =>
            'bg-blue-100 dark:bg-blue-800 border-l-4 border-blue-500 dark:border-blue-700 text-blue-900 dark:text-blue-100 hover:bg-blue-200 dark:hover:bg-blue-800',
        'warning' =>
            'bg-yellow-100 dark:bg-yellow-800 border-l-4 border-yellow-500 dark:border-yellow-700 text-yellow-900 dark:text-yellow-100 hover:bg-yellow-200 dark:hover:bg-yellow-800',
        'error' =>
            'bg-red-100 dark:bg-red-800 border-l-4 border-red-500 dark:border-red-700 text-red-900 dark:text-red-100 hover:bg-red-200 dark:hover:bg-red-800',
    ];

    $iconColors = [
        'success' => 'text-green-600',
        'info' => 'text-blue-600',
        'warning' => 'text-yellow-600',
        'error' => 'text-red-600',
    ];
@endphp

<div class="space-y-2 my-2">
    <div role="alert"
        class="{{ $alertClasses[$type] ?? $alertClasses['info'] }} p-2 rounded-md flex items-center justify-between transition duration-300 ease-in-out transform scale-y-105"
        x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90">
        <div class="flex items-center">
            <svg stroke="currentColor" viewBox="0 0 24 24" fill="none"
                class="h-5 w-5 flex-shrink-0 mr-2 {{ $iconColors[$type] ?? $iconColors['info'] }}"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M13 16h-1v-4h1m0-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"
                    stroke-linejoin="round" stroke-linecap="round"></path>
            </svg>
            <p class="text-xs font-semibold">
                {{ $message }}
            </p>
            @isset($goto)
                <a href="{{ $goto }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 ml-2">
                    <svg fill="#000000" height="24px" width="24px" version="1.1" id="Layer_1"
                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        viewBox="-51.2 -51.2 614.40 614.40" xml:space="preserve">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <g>
                                <g>
                                    <path
                                        d="M346.927,149.639l-21.234-21.235L149.157,304.935c-21.844,21.843-21.844,57.385,0,79.23 c10.569,10.571,24.638,16.391,39.616,16.391c14.978,0,29.047-5.821,39.617-16.391l237.089-237.094 c33.599-33.602,33.599-88.272,0-121.872c-33.6-33.6-88.271-33.6-121.871,0L55.34,313.467c-45.357,45.357-45.357,119.158,0,164.516 C78.019,500.661,107.809,512,137.599,512s59.58-11.338,82.257-34.017l227.708-227.708L426.33,229.04L198.622,456.748 c-33.649,33.647-88.4,33.647-122.047,0c-33.648-33.649-33.648-88.399,0-122.047L364.841,46.435 c21.891-21.89,57.512-21.89,79.403,0c21.891,21.891,21.89,57.511,0,79.403L207.153,362.932 c-4.898,4.898-11.426,7.596-18.381,7.596s-13.484-2.698-18.38-7.596c-10.135-10.136-10.135-26.628,0-36.763L346.927,149.639z">
                                    </path>
                                </g>
                            </g>
                        </g>
                    </svg>
                </a>
            @endisset
        </div>
        <button @click="show = false" class="text-gray-500 dark:text-gray-200 hover:text-gray-700 dark:hover:text-gray-100 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
