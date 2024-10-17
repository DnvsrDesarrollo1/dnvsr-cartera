<x-app-layout>
    <div class="mx-auto px-6 py-4 sm:px-4 lg:px-6">
        <div class="flex items-center justify-center shadow-lg bg-white rounded-lg">
            <div class="m-4">
                <span class="text-blue-500">Cancelados:</span>
                <span class="text-gray-600">{{$cancelados}}</span>
            </div>
            <div class="m-4">
                <span class="text-green-600">Vigentes:</span>
                <span class="text-gray-600">{{$vigentes}}</span>
            </div>
            <div class="m-4">
                <span class="text-yellow-600">Vencidos:</span>
                <span class="text-gray-600">{{$vencidos}}</span>
            </div>
            <div class="m-4">
                <span class="text-red-600">Ejecucion:</span>
                <span class="text-gray-600">{{$ejecuciones}}</span>
            </div>
        </div>
        <hr>
        <div class="bg-white overflow-x-auto shadow-lg rounded-lg overflow-y-auto mt-4">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="text-lg text-gray-800 dark:text-gray-400 bg-gray-200 dark:bg-gray-800">
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
                            <td @class([
                                'text-blue-500' => $b->estado == 'CANCELADO',
                                'text-green-600' => $b->estado == 'VIGENTE',
                                'text-yellow-600' => $b->estado == 'VENCIDO',
                                'text-red-600' => $b->estado == 'EJECUCION',
                            ])>{{ $b->estado }}</td>
                            <td>{{ $b->proyecto }}</td>
                            <td>{{ $b->fecha_activacion }}</td>
                            <td class="">{{ $b->total_activado }}</td>
                            <td @class([
                                'text-green-600' => $b->monto_recuperado >= $b->total_activado,
                                'text-red-600' => $b->monto_recuperado < $b->total_activado,
                            ])>
                                {{ $b->monto_recuperado }}
                            </td>
                            <td>{{ $b->departamento }}</td>
                            <td class="h-auto flex flex-row justify-center">
                                <a href="{{ route('beneficiario.show', $b->ci) }}"
                                    class="px-4 py-2 text-white bg-indigo-600 rounded-full duration-150 hover:bg-indigo-500 active:bg-indigo-700">
                                    Revisar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-lg text-gray-500 bg-gray-100">
                        <th class="py-2" colspan="12">
                            {!! $beneficiaries->render() !!}
                        </th>
                    </tr>
                    <tr class="flex justify-end py-2">
                        <th class="h-full py-2">
                            <a href="{{ route('excel.export-model', $beneficiaries[0]->getTable()) }}"
                                class="px-4 py-2 text-white bg-gray-800 rounded-full">
                                Exportar Beneficiarios
                            </a>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</x-app-layout>
