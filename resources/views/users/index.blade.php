<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ 'Administración de Usuarios' }}
            </h2>
            <div class="flex justify-end">
                <form action="{{ route('users.logout.all') }}" method="GET" class="ml-auto">
                    @csrf
                    <x-personal.button variant="danger" :submit="true" iconLeft="fa-solid fa-right-from-bracket"
                        class="text-sm" onclick="return confirm('¿Estás seguro de cerrar todas las sesiones activas?')">
                        Cerrar todas las sesiones ({{ $activeUsers }})
                    </x-personal.button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6" id="user_administration">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-6">
            <x-personal.alert clossable="false" type="info"
                message="{{ $activeUsers }} usuarios activos en este momento." />
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Permisos</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-sm text-gray-500">{{ $user->email }}</span>
                                </td>
                                <td class="px-4 py-2">
                                    <form action="{{ route('users.update.permissions', $user) }}" method="POST"
                                        class="space-y-2">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-2 gap-1">
                                            @foreach ($permissions as $permission)
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="permissions[]"
                                                        value="{{ $permission->name }}"
                                                        class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                        {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                    <span
                                                        class="text-xs text-gray-600">
                                                        {{ str_replace(['read', 'write', 'plans', 'beneficiaries', 'vouchers', 'payments', 'settlements', 'users', ' '],
                                                        ['R/', 'W/', 'PLN', 'BNF', 'VCH', 'PYM', 'STL', 'USR', ''], strtolower($permission->name)) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <x-personal.button variant="success" :submit="true"
                                            iconLeft="fa-solid fa-arrows-rotate" class="!px-2 !py-1 text-xs">
                                            Sync
                                        </x-personal.button>
                                    </form>
                                </td>
                                <td class="px-4 py-2">
                                    <form action="{{ route('users.update.role', $user) }}" method="POST"
                                        class="flex items-center space-x-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="role"
                                            class="text-sm rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}"
                                                    {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                    {{ strtoupper($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-personal.button variant="success" :submit="true"
                                            iconLeft="fa-solid fa-check" class="!px-2 !py-1 text-xs">
                                            OK
                                        </x-personal.button>
                                    </form>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center space-x-1">
                                        <x-personal.button variant="primary" href="{{ route('users.edit', $user) }}"
                                            iconLeft="fa-solid fa-pencil" class="!px-2 !py-1 text-xs">
                                            Editar
                                        </x-personal.button>

                                        <form action="{{ route('users.destroy', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-personal.button variant="danger" :submit="true"
                                                iconLeft="fa-solid fa-trash-can" class="!px-2 !py-1 text-xs">
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
