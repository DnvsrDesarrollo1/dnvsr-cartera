<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500">
                        </div>

                        <div>
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Contraseña:</label>
                            <input type="password" name="password" id="password"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500">
                            <p class="text-gray-400 text-sm mt-2 italic">Dejar en blanco si no desea cambiar la
                                contraseña.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Asignar Proyectos</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 p-2">
                        @foreach ($proyectos as $p)
                            <div
                                class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200 shadow-sm border border-gray-100">
                                <input type="checkbox" name="proyectos[]" value="{{ $p->id }}"
                                    {{ $user->projects->contains($p) ? 'checked' : '' }}
                                    class="form-checkbox h-5 w-5 text-blue-600 focus:ring-blue-500 rounded border-gray-300">
                                <label
                                    class="ml-2 text-sm font-medium text-gray-700 cursor-pointer hover:text-gray-900">
                                    {{ $p->nombre_proyecto }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <x-personal.button variant="success" :submit="true" iconLeft="fa-solid fa-save">
                        Guardar Cambios
                    </x-personal.button>
                    <x-personal.button variant="danger" href="{{ route('users.index') }}" iconLeft="fa-solid fa-xmark">
                        Cancelar
                    </x-personal.button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
