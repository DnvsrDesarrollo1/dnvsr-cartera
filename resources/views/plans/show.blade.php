<x-app-layout>
    {{--    {{$data}} --}}
    <div class="max-w-9xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white overflow-x-auto shadow-lg rounded-lg overflow-y-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50">
                    <tr class="text-lg text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 border-2">
                        <th class="px-4 py-4 text-sm font-medium text-gray-700 whitespace-nowrap">#</th>
                        <th>Indice Cuota</th>
                        <th>Saldo Inicial</th>
                        <th>Amortizacion</th>
                        <th>Abono Capital</th>
                        <th>Interes</th>
                        <th>Seguro</th>
                        <th>Total a Pagar</th>
                        <th>Saldo Final</th>
                        <th>Fecha Vencimiento</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($data as $d)
                        <tr @class(['px-3 py-4 text-sm p-2 h-auto text-center', 'text-green-600 font-bold' => $loop->first, 'text-red-500 font-bold' => $loop->last])>
                            <td class="px-4 py-4 text-sm font-medium text-gray-400 whitespace-nowrap">
                                {{ $loop->index + 1 }}</td>
                            <td>{{ $d->no }}</td>
                            <td>{{ number_format($d->saldo_inicial, 2, '.', ',') }}</td>
                            <td>{{ number_format($d->amortizacion, 2, '.', ',') }}</td>
                            <td>{{ number_format($d->abono_capital, 2, '.', ',') }}</td>
                            <td>{{ number_format($d->interes, 2, '.', ',') }}</td>
                            <td>{{ number_format($d->seguro, 2, '.', ',') }}</td>
                            <td>{{ number_format($d->total_cuota, 2, '.', ',') }} </td>
                            <td>{{ number_format($d->saldo_final, 2, '.', ',') }}</td>
                            <td>{{ $d->vencimiento }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-lg text-gray-500 bg-gray-100">
                        <th colspan="12">
                            <x-button>
                                Confirmar nuevo plan y guardar
                            </x-button>
                            <x-danger-button onclick="history.back();">
                                Cancelar
                            </x-danger-button>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
