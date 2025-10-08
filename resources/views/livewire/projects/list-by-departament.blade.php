<div x-data="{ listOpen: @entangle('listOpen') }">
    <x-personal.button @click="listOpen = true" @keydown.escape.window="listOpen = false" variant="primary" size="xs"
        iconCenter="fa-regular fa-file-lines">
    </x-personal.button>

    <div x-show="listOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
        <div class="fixed inset-0 transition-opacity" @click="listOpen = false">
            <div class="absolute inset-0 bg-gray-500 opacity-25"></div>
        </div>

        <div class="bg-white overflow-hidden rounded-lg transform transition-all sm:max-w-2xl sm:w-full m-4">
            <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">
                        {{ $departament }}
                    </h3>
                    <button @click="listOpen = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="px-2 py-2 sm:p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                <div class="px-4 border-l-4 border-gray-800" id="profile_plans">
                    <div class="overflow-x-auto">
                        <ul class="divide-y divide-gray-200">
                            @forelse ($projectsFromDepartaments as $project)
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <a href="{{ route('proyecto.ficha', $project->proyecto) }}"
                                            class="text-sm font-medium text-gray-900">{{ $project->proyecto }}</a>
                                    </div>
                                </li>
                            @empty
                                <li class="py-3 text-sm text-gray-500">No hay proyectos en este departamento.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
