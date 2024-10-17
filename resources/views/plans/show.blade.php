<x-app-layout>
    {{--    {{$data}} --}}
    <div>
        @if ($errors->any())
            <div class="p-4 bg-red-500 text-white">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
    </div>
    <div class="max-w-9xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-lg mb-2 p-4 flex justify-between items-center">
            <h1 class="text-xl text-gray-800">Reajuste al plan de cuotas: <i>Cod. {{ $request->idepro }}</i></h1>
        </div>
        <div class="overflow-x-auto shadow-lg rounded-lg overflow-y-auto">
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
                        <tr @class([
                            'px-3 py-4 text-sm p-2 h-auto text-center',
                            'text-green-600 font-bold' => $loop->first,
                            'text-red-500 font-bold' => $loop->last,
                        ])>
                            <td class="px-4 py-4 text-sm font-medium text-gray-400 whitespace-nowrap">
                                {{ $loop->index + 1 }}</td>
                            <td>{{ $d->nro_cuota }}</td>
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
                    <tr class="text-lg text-gray-500 bg-white">
                        <th class="flex items-center">
                            <div class="m-4">
                                <form action="{{ route('excel.export-collection') }}" method="post">
                                    @csrf
                                    <input type="hidden" id="diff_cuotas" name="diff_cuotas" value="{{$request->input('diff_cuotas')}}" />
                                    <input type="hidden" id="diff_capital" name="diff_capital" value="{{$request->input('diff_capital')}}" />
                                    <input type="hidden" id="diff_interes" name="diff_interes" value="{{$request->input('diff_interes')}}" />
                                    <input type="hidden" id="plazo_credito" name="plazo_credito" value="{{$request->input('plazo_credito')}}" />
                                    <input type="hidden" id="idepro" name="idepro" value="{{$request->input('idepro')}}" />
                                    <input type="hidden" name="capital_inicial"
                                        value="{{ $request->input('capital_inicial') }}">
                                    <input type="hidden" name="meses" value="{{ $request->input('meses') }}">
                                    <input type="hidden" name="taza_interes"
                                        value="{{ $request->input('taza_interes') }}">
                                    <input type="hidden" name="correlativo"
                                        value="{{ $request->input('correlativo') }}">
                                    <input type="hidden" name="plazo_credito"
                                        value="{{ $request->input('plazo_credito') }}">
                                    <input type="hidden" name="fecha_inicio" value="<?php echo date('Y/m/d', strtotime($request->input('fecha_inicio'))); ?>">
                                    <x-button>
                                        guardar y exportar
                                    </x-button>
                                </form>
                            </div>
                        </th>
                        <th>
                            <x-danger-button onclick="history.back();">
                                Cancelar
                            </x-danger-button>
                        </th>
                    </tr>
                </tfoot>
            </table>
            @isset($diferimento)
                <div class="bg-white shadow-md rounded-lg mt-2 mb-2 p-4">
                    <h1 class="text-xl text-gray-800">Diferimentos:</i></h1>
                </div>
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50">
                        <tr class="text-lg text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 border-2">
                            <th class="px-4 py-4 text-sm font-medium text-gray-700 whitespace-nowrap">#</th>
                            <th>Indice Cuota</th>
                            <th>Capital</th>
                            <th>Interes</th>
                            <th>Vencimiento</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($diferimento as $d)
                            <tr @class([
                                'px-3 py-4 text-sm p-2 h-auto text-center',
                                'text-green-600 font-bold' => $loop->first,
                                'text-red-500 font-bold' => $loop->last,
                            ])>
                                <td class="px-4 py-4 text-sm font-medium text-gray-400 whitespace-nowrap">
                                    {{ $loop->index + 1 }}</td>
                                <td>{{ $d->nro_cuota }}</td>
                                <td>{{ number_format($d->capital, 2, '.', ',') }}</td>
                                <td>{{ number_format($d->interes, 2, '.', ',') }}</td>
                                <td>{{ $d->vencimiento }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endisset
        </div>
    </div>
</x-app-layout>
