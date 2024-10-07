<x-app-layout>
    <div class="max-w-9xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white overflow-x-auto shadow-lg rounded-lg overflow-y-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50">
                    <tr class="text-lg text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800">
                        <th class="px-4 py-4 text-sm font-medium text-gray-700 whitespace-nowrap">#</th>
                        <th>Nombres</th>
                        <th>Cedula</th>
                        <th>Estado de Credito</th>
                        <th>Proyecto</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Recuperado</th>
                        <th>Departamento</th>
                        <th>Operaciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($beneficiaries as $b)
                        <tr class="border-b-2 px-3 py-4 text-sm p-2 h-auto">
                            <td class="px-4 py-4 text-sm font-medium text-gray-700 whitespace-nowrap">
                                {{ $loop->index + 1 }}</td>
                            <td>{{ $b->nombre }}</td>
                            <td>{{ "$b->ci ($b->complemento) $b->expedido" }}</td>
                            <td>{{ $b->estado }}</td>
                            <td>{{ $b->proyecto }}</td>
                            <td>{{ $b->fecha_activacion }}</td>
                            <td class="">{{ $b->monto_activado }}</td>
                            <td
                                class="{{ $b->monto_recuperado == $b->monto_activado ? 'text-green-500' : 'text-red-500' }}">
                                {{ $b->monto_recuperado }}
                            </td>
                            <td>{{ $b->departamento }}</td>
                            <td class="h-auto flex flex-row justify-center">
                                <a href="{{ route('beneficiary.show', $b) }}"
                                    class="px-4 py-2 text-white bg-indigo-600 rounded-full duration-150 hover:bg-indigo-500 active:bg-indigo-700">
                                    Revisar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-lg text-gray-500 bg-gray-100">
                        <th colspan="12">
                            {!! $beneficiaries->render() !!}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</x-app-layout>
