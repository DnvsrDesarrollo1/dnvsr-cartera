<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ ('Administración de Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-6" id="user_administration">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-6">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <table class="min-w-full divide-y divide-gray-200 rounded-md overflow-hidden">
                    <thead>
                        <tr class="bg-gray-800 divide-x divide-gray-600">
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-300 uppercase tracking-wider">
                                Nombre
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-300 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-300 uppercase tracking-wider">
                                Permisos
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-300 uppercase tracking-wider">
                                Rol
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-300 uppercase tracking-wider">

                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">
                                        {{ $user->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <form action="{{ route('users.update.permissions', $user) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex flex-col gap-2">
                                            @foreach ($permissions as $permission)
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                        {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                    <span class="ml-2 text-sm text-gray-600">{{ $permission->name }}</span>
                                                </label>
                                            @endforeach
                                            <x-personal.button variant="success" :submit="true" iconLeft="fa-solid fa-arrows-rotate">
                                                Sync Permisos
                                            </x-personal.button>
                                        </div>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <form action="{{ route('users.update.role', $user) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex gap-2">
                                            <select name="role"
                                                class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}"
                                                        {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                        {{ strtoupper($role->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-personal.button variant="success" :submit="true" iconLeft="fa-solid fa-floppy-disk">
                                                Aplicar Rol
                                            </x-personal.button>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <x-personal.button variant="primary" href="{{ route('users.edit', $user) }}" iconLeft="fa-solid fa-pencil">
                                            Editar
                                        </x-personal.button>

                                        <form action="{{ route('users.destroy', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-personal.button variant="danger" :submit="true" iconLeft="fa-solid fa-trash-can">
                                                Eliminar
                                            </x-personal.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-app-layout>
