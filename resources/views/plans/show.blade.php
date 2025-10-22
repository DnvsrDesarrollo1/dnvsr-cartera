<x-app-layout>
    <div class="max-w-fit mx-auto my-2 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-md mb-2 rounded-md px-4 py-2 border border-gray-300">
            <h1 class="text-xl font-bold text-gray-800">
                @if ($request->correlativo == 'on')
                    Reajuste
                @else
                    Activacion
                @endif de plan de pagos: <span class="text-blue-600">{{ $request->idepro }}</span>
            </h1>
        </div>

        <div class="bg-white shadow-md rounded-md border border-gray-300 max-h-[calc(100vh-200px)] overflow-y-auto">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">#</th>
                            <th class="py-3 px-6 text-left">Indice Cuota</th>
                            <th class="py-3 px-6 text-right">Saldo Inicial</th>
                            <th class="py-3 px-6 text-right">Amortización</th>
                            <th class="py-3 px-6 text-right">Abono Capital</th>
                            <th class="py-3 px-6 text-right">Interés</th>
                            <th class="py-3 px-6 text-right">Seguro</th>
                            <th class="py-3 px-6 text-right">Otros</th>
                            <th class="py-3 px-6 text-right">Interes Devengado</th>
                            <th class="py-3 px-6 text-right">Seguro Devengado</th>
                            <th class="py-3 px-6 text-right">Total a Pagar</th>
                            <th class="py-3 px-6 text-right">Saldo Final</th>
                            <th class="py-3 px-6 text-center">Fecha Vencimiento</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light text-center">
                        @foreach ($data as $d)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left whitespace-nowrap italic">
                                    {{ $loop->index + 1 }})</td>
                                <td class="py-3 px-6 text-left">{{ $d->nro_cuota }}</td>
                                <td class="py-3 px-6 text-right">{{ number_format($d->saldo_inicial, 2) }}
                                </td>
                                <td class="py-3 px-6 text-right">{{ number_format($d->amortizacion, 2) }}</td>
                                <td class="py-3 px-6 text-right">{{ number_format($d->abono_capital, 2) }}
                                </td>
                                <td class="py-3 px-6 text-right">{{ number_format($d->interes, 2) }}</td>
                                <td class="py-3 px-6 text-right">{{ number_format($d->seguro, 2) }}</td>
                                <td class="py-3 px-6 text-right">{{ number_format($d->gastos_judiciales ?? 0, 2) }}</td>
                                <td class="py-3 px-6 text-right">{{ number_format($d->interes_devengado ?? 0, 2) }}</td>
                                <td class="py-3 px-6 text-right">{{ number_format($d->seguro_devengado ?? 0, 2) }}</td>
                                <td class="py-3 px-6 text-right font-medium">
                                    {{ number_format($d->total_cuota, 2) }}</td>
                                <td class="py-3 px-6 text-right">{{ number_format($d->saldo_final, 2) }}</td>
                                <td class="py-3 px-6 text-center">{{ $d->vencimiento }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4 grid grid-flow-col-2 auto-cols-auto gap-1 bg-white p-4 rounded-lg shadow-md">
            <div class="">
                <table>
                    <tbody class="text-center">
                        <tr class="bg-gray-200 text-gray-700 font-bold">
                            <td colspan="3" class="py-3 px-6 text-right bg-white">Totales:</td>
                            <td class="py-3 px-6 text-right">
                                <h3 class="text-sm">Total del Credito:</h3>
                                <p class="text-green-500 text-sm">
                                    {{ number_format($data->sum('amortizacion'), 2) }}
                                </p>
                            </td>
                            <td class="py-3 px-6 text-right">
                                <h3 class="text-sm">Pago a Capital:</h3>
                                <p class="text-green-500 text-sm">
                                    {{ number_format($data->sum('abono_capital'), 2) }}
                                </p>
                            </td>
                            <td class="py-3 px-6 text-right">
                                <h3 class="text-sm">Pago a Interes:</h3>
                                <p class="text-green-500 text-sm">
                                    {{ number_format($data->sum('interes'), 2) }}
                                </p>
                            </td>
                            <td class="py-3 px-6 text-right">
                                <h3 class="text-sm">Pago al Seguro:</h3>
                                <p class="text-green-500 text-sm">
                                    {{ number_format($data->sum('seguro'), 2) }}
                                </p>
                            </td>
                            <td class="py-3 px-6 text-right">
                                <h3 class="text-sm">Gastos Judiciales:</h3>
                                <p class="text-green-500 text-sm">
                                    {{ number_format($data->sum('gastos_judiciales'), 2) }}
                                </p>
                            </td>
                            <td class="py-3 px-6 text-right">
                                <h3 class="text-sm">Interes Devengado:</h3>
                                <p class="text-green-500 text-sm">
                                    {{ number_format($data->sum('interes_devengado'), 2) }}
                                </p>
                            </td>
                            <td class="py-3 px-6 text-right">
                                <h3 class="text-sm">Seguro Devengado:</h3>
                                <p class="text-green-500 text-sm">
                                    {{ number_format($data->sum('seguro_devengado'), 2) }}
                                </p>
                            </td>
                            <td class="py-3 px-6 text-right">
                                <h3 class="text-sm">Pago en Cuotas:</h3>
                                <p class="text-green-500 text-sm">
                                    {{ number_format($data->sum('total_cuota'), 2) }}
                                </p>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-end space-x-2">
                @can('write plans')
                    <form action="{{ route('plan.store') }}" method="post" class="flex space-x-4">
                        @csrf
                        <input type="hidden" name="diff_cuotas" value="{{ $request->input('diff_cuotas') }}" />
                        <input type="hidden" name="diff_capital" value="{{ $request->input('diff_capital') }}" />
                        <input type="hidden" name="diff_interes" value="{{ $request->input('diff_interes') }}" />
                        <input type="hidden" name="plazo_credito" value="{{ $request->input('plazo_credito') }}" />
                        <input type="hidden" name="idepro" value="{{ $request->input('idepro') }}" />
                        <input type="hidden" name="capital_inicial" value="{{ $request->input('capital_inicial') }}" />
                        <input type="hidden" name="meses" value="{{ $request->input('meses') }}" />
                        <input type="hidden" name="taza_interes" value="{{ $request->input('taza_interes') }}" />
                        <input type="hidden" name="seguro" id="seguro" value="{{ $request->input('seguro') }}" />
                        <input type="hidden" name="correlativo" value="{{ $request->input('correlativo') }}" />
                        <input type="hidden" name="fecha_inicio"
                            value="{{ date('Y/m/d', strtotime($request->input('fecha_inicio'))) }}">
                        <input type="hidden" name="gastos_judiciales"
                            value="{{ $request->input('gastos_judiciales') }}" />
                        <x-personal.button :submit="true" variant="success" iconLeft="fa-solid fa-floppy-disk">
                            Confirmar y Guardar
                        </x-personal.button>
                    </form>
                @else
                    <span class="text-gray-500">
                        Usted solo puede realizar simulaciónes, los datos presentados no serán guardados.
                    </span>
                @endcan
                <x-personal.button onclick="history.back();" variant="danger" iconLeft="fa-solid fa-xmark">
                    Cancelar
                </x-personal.button>
            </div>
        </div>

        @isset($diferimento)
            <div class="mt-10 bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Diferimentos</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">#</th>
                                <th class="py-3 px-6 text-left">Indice Cuota</th>
                                <th class="py-3 px-6 text-right">Capital</th>
                                <th class="py-3 px-6 text-right">Interés</th>
                                <th class="py-3 px-6 text-center">Vencimiento</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach ($diferimento as $d)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $loop->index + 1 }}</td>
                                    <td class="py-3 px-6 text-left">{{ $d->nro_cuota }}</td>
                                    <td class="py-3 px-6 text-right">{{ number_format($d->capital, 2) }}</td>
                                    <td class="py-3 px-6 text-right">{{ number_format($d->interes, 2) }}</td>
                                    <td class="py-3 px-6 text-center">{{ $d->vencimiento }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endisset
    </div>
</x-app-layout>
