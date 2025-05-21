<x-app-layout>
    <div class="mt-4">
        <div class="w-full px-4 grid sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-2">
            <div class="bg-white shadow-lg rounded-lg p-6 mb-4 h-fit border border-gray-300" id=$"profile_preview">
                <div class="flex items-center justify-between mb-2">
                    <div class="border bg-gray-100 p-2 rounded-md shadow-md">
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $beneficiary->nombre }} - {{ $beneficiary->ci }} {{ $beneficiary->complemento }}
                            {{ $beneficiary->expedido }}
                        </p>
                        <hr class="my-2" />
                        <p class="text-gray-600">
                            <i>
                                COD.CREDITO: {{ $beneficiary->idepro }}
                            </i>
                        </p>
                    </div>
                    <div class="ml-auto flex justify-end gap-2">
                        @can('write beneficiaries')
                            @livewire('beneficiary-update', ['beneficiary' => $beneficiary])
                        @endcan
                        @if (auth()->user()->can('read settlements') || auth()->user()->can('write settlements'))
                            @livewire('settle-beneficiary', ['beneficiary' => $beneficiary])
                        @endif
                    </div>
                </div>
                <hr>
                @if (session('success'))
                    <x-personal.alert type="success" message="{{ session('success') }}"
                        goto="{{ session('file') ?? null }}" />
                @endif

                <div class="mt-2 grid grid-cols-2 gap-2">
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Estado de Crédito</h3>
                        <p
                            class="font-bold {{ $beneficiary->estado == 'CANCELADO' || $beneficiary->estado == 'BLOQUEADO' ? 'text-red-500' : '' }}">
                            {{ $beneficiary->estado }}
                        </p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Proyecto</h3>
                        <p class="font-bold">{{ $beneficiary->proyecto }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Departamento</h3>
                        <p class="font-bold">{{ $beneficiary->departamento }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Fecha de Activación</h3>
                        <p class="font-bold">{{ $beneficiary->fecha_activacion }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Total Activado</h3>
                        <p class="font-bold text-sky-800">Bs.
                            {{ number_format($beneficiary->total_activado, 2) }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Saldo Credito</h3>
                        <p class="font-bold text-sky-800">Bs.
                            {{ number_format($beneficiary->saldo_credito, 2) }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Total en Pagos</h3>
                        <p class="font-bold text-sky-800">Bs.
                            {{ number_format($beneficiary->payments()->where('prtdtdesc', 'like', '%CAPI%')->sum('montopago'), 2) }}
                        </p>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                class="bg-gray-100 rounded-lg p-4 shadow-md flex flex-col justify-between items-center">
                                @php
                                    $plans = $beneficiary->getCurrentPlan();
                                @endphp
                                @if ($plans->count() > 0)
                                    <livewire:plan-modal lazy :beneficiary="$beneficiary" title="Plan de Pagos" />
                                @else
                                    <p class="text-gray-500 italic">Sin plan de pagos registrado</p>
                                @endif
                                <hr class="my-2" />
                                @if ($plans->count() > 0)
                                    @php
                                        $vencs = $beneficiary
                                            ->plans()
                                            ->where('estado', 'VENCIDO')
                                            ->orderBy('fecha_ppg')
                                            ->get();
                                        $total = 0;
                                        if ($vencs->count() > 0) {
                                            $fechaInicio = \Carbon\Carbon::parse($vencs->first()->fecha_ppg);
                                            $fechaFin = now();
                                            $diff = $fechaInicio->diffInDays($fechaFin);
                                            $total = $diff;
                                            echo "
                                                <span class=\"w-full bg-white rounded-md text-center text-red-500 p-1\">
                                                    Dias de Mora: <b>" .
                                                number_format($total, 0) .
                                                "</b>
                                                </span>
                                            ";
                                        }
                                    @endphp
                                @endif
                            </div>
                            <div
                                class="bg-gray-100 rounded-lg p-4 shadow-md flex flex-col justify-between items-center">
                                @if ($beneficiary->payments()->count() > 0)
                                    <livewire:payment-modal lazy :beneficiary="$beneficiary" title="Historial de Pagos" />
                                @else
                                    <p class="text-gray-500 italic">Sin historial de pagos registrado</p>
                                @endif

                                <hr class="my-2" />

                                <livewire:voucher-register :idepro="$beneficiary->idepro" title="Registrar Pago" />
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mt-4 mb-4" />
                <div class="bg-gray-100 rounded-lg p-2 shadow">
                    <!-- Highcharts Timeline Chart -->
                    <div class="mt-2 flex justify-center items-center">
                        <div id="timelineChart" class="w-full rounded-md" style="height: 600px;"></div>
                    </div>

                    @push('scripts')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Convertir los arrays de PHP a JavaScript
                                let plans = {!! json_encode($plansArray) !!};
                                let payments = {!! json_encode($paymentsArray) !!};

                                // Obtener fechas límite
                                let startDate = new Date(Math.min(
                                    ...plans.map(plan => new Date(plan.fecha_ppg)),
                                    ...payments.map(payment => new Date(payment.fecha_pago))
                                ));
                                let endDate = new Date(Math.max(
                                    ...plans.map(plan => new Date(plan.fecha_ppg)),
                                    ...payments.map(payment => new Date(payment.fecha_pago))
                                ));
                                let months = [];
                                let currentDate = new Date(startDate);

                                // Generar array de meses
                                while (currentDate <= endDate) {
                                    months.push(currentDate.toISOString().slice(0, 7));
                                    currentDate.setMonth(currentDate.getMonth() + 1);
                                }

                                // Inicializar arrays de datos
                                let plansData = new Array(months.length).fill(null);
                                let paymentsData = new Array(months.length).fill(null);

                                // Procesar datos de planes
                                plans.forEach(plan => {
                                    let planMonth = new Date(plan.fecha_ppg).toISOString().slice(0, 7);
                                    let index = months.indexOf(planMonth);
                                    if (index !== -1) {
                                        plansData[index] = Math.abs(parseFloat(plan.prppgcapi));
                                    }
                                });

                                // Procesar datos de pagos
                                payments.forEach(payment => {
                                    let paymentMonth = new Date(payment.fecha_pago).toISOString().slice(0, 7);
                                    let index = months.indexOf(paymentMonth);
                                    if (index !== -1) {
                                        paymentsData[index] = (paymentsData[index] || 0) + Math.abs(parseFloat(payment
                                            .montopago));
                                    }
                                });

                                Highcharts.chart('timelineChart', {
                                    chart: {
                                        type: 'line'
                                    },
                                    title: {
                                        text: 'Evolución mensual de cumplimiento'
                                    },
                                    xAxis: {
                                        categories: months,
                                        title: {
                                            text: null
                                        }
                                    },
                                    yAxis: {
                                        title: {
                                            text: null
                                        },
                                        labels: {
                                            formatter: function() {
                                                return Highcharts.numberFormat(this.value, 2);
                                            }
                                        }
                                    },
                                    tooltip: {
                                        valueDecimals: 2,
                                        valueSuffix: ' Bs',
                                        shared: true,
                                        crosshairs: true
                                    },
                                    plotOptions: {
                                        area: {
                                            fillOpacity: 0.5
                                        }
                                    },
                                    series: [{
                                        name: 'Capital Planificado',
                                        data: plansData,
                                        color: '#FF6384',
                                        fillColor: {
                                            linearGradient: {
                                                x1: 0,
                                                y1: 0,
                                                x2: 0,
                                                y2: 1
                                            },
                                            stops: [
                                                [0, 'rgba(255,99,132,0.3)'],
                                                [1, 'rgba(255,99,132,0)']
                                            ]
                                        }
                                    }, {
                                        name: 'Capital Pagado',
                                        data: paymentsData,
                                        color: '#4BC0C0',
                                        fillColor: {
                                            linearGradient: {
                                                x1: 0,
                                                y1: 0,
                                                x2: 0,
                                                y2: 1
                                            },
                                            stops: [
                                                [0, 'rgba(75,192,192,0.3)'],
                                                [1, 'rgba(75,192,192,0)']
                                            ]
                                        }
                                    }],
                                    responsive: {
                                        rules: [{
                                            condition: {
                                                maxWidth: 500
                                            },
                                            chartOptions: {
                                                legend: {
                                                    layout: 'horizontal',
                                                    align: 'center',
                                                    verticalAlign: 'bottom'
                                                }
                                            }
                                        }]
                                    }
                                });
                            });
                        </script>
                    @endpush
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow h-fit border border-gray-300" id="profile_management">
                <div class="bg-gray-100 p-4 rounded-lg flex justify-between border-l-8 border-gray-800">
                    <h3 class="font-bold">
                        Generador de Planes de Pago:
                    </h3>
                    <p>
                        @if ($beneficiary->estado != 'CANCELADO')
                            Permite realizar una nueva serie (plan) de pagos en base a los meses especificados y el
                            saldo a capital.
                        @else
                            No disponible para este beneficiario.
                        @endif
                    </p>
                </div>
                <div x-data="{ show: false }">
                    @if ($beneficiary->estado != 'CANCELADO')
                        <button @click="show = !show"
                            class="rounded-full m-2 overflow-hidden border border-gray-300 block mx-auto transition">
                            <svg x-show="!show" width="64px" height="64px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <rect width="24" height="24" fill="white"></rect>
                                    <path d="M17 9.5L12 14.5L7 9.5" stroke="#000000" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </g>
                            </svg>
                            <svg x-show="show" width="64px" height="64px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <rect width="24" height="24" fill="white"></rect>
                                    <path d="M7 14.5L12 9.5L17 14.5" stroke="#000000" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </g>
                            </svg>
                        </button>
                    @endif
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-300" x-show="show" x-transition x-cloak>
                        <form action="{{ route('plan.reajuste') }}" method="post">
                            @csrf
                            <input type="hidden" name="idepro" value="{{ $beneficiary->idepro }}" />
                            <input type="hidden" name="plazo_credito" value="{{ $beneficiary->plazo_credito }}" />
                            <input type="hidden" name="gastos_judiciales"
                                value="{{ $beneficiary->gastos_judiciales }}" />
                            <div class="mb-4">
                                <label for="capital_inicial" class="block text-gray-700 font-bold mb-2">
                                    1) Capital Inicial:
                                </label>
                                <input type="text" inputmode="decimal" id="capital_inicial"
                                    name="capital_inicial" placeholder="Ej: 25000.75" pattern="[0-9]*[.,]?[0-9]*"
                                    class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                    required value="{{ $beneficiary->saldo_credito }}"
                                    title="Saldo restante de (Monto Activado menos Monto en Cuotas).">
                            </div>
                            <div class="mb-4">
                                <label for="meses" class="block text-gray-700 font-bold mb-2">
                                    2) Meses restantes:
                                </label>
                                <input type="text" id="meses" name="meses" placeholder="Ej: 10"
                                    class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                    required value="{{ $mesesRestantes }}"
                                    title="Meses restantes desde hoy, a la fecha de activacion (+ 20 años).">
                            </div>
                            <div class="mb-4 grid grid-cols-2 gap-2">
                                <div>
                                    <label for="taza_interes" class="block text-gray-700 font-bold mb-2">
                                        3) Interes:
                                    </label>
                                    <input type="text" inputmode="decimal" name="taza_interes"
                                        placeholder="Ej: 13 (no es necesario agregar simbolo %)"
                                        pattern="[0-9]*[.,]?[0-9]*"
                                        class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                        required value="3" title="Taza por defecto 3%.">
                                </div>
                                <div>
                                    <label for="seguro" class="block text-gray-700 font-bold mb-2">
                                        4) Seguro:
                                    </label>
                                    <input type="text" inputmode="decimal" name="seguro"
                                        placeholder="Ej: 13 (no es necesario agregar simbolo %)"
                                        pattern="[0-9]*[.,]?[0-9]*"
                                        class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                        required value="0.04" title="Seguro por defecto 0.04%.">
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="block mt-4">
                                    <label for="correlativo"
                                        class="bg-gray-100 flex items-center text-gray-700 font-bold mb-2 p-4 border rounded-md cursor-pointer"
                                        title="Desactivado: el n# de cuota empezará desde el 1 para adelante, de lo contrario, desde la ultima cuota correspondiente a los meses restantes.">
                                        5) &nbsp;
                                        <x-checkbox checked id="correlativo" name="correlativo" />
                                        <span class="ms-2 text-gray-600 dark:text-gray-400">
                                            Reajuste (marcado) / Activacion (desmarcado)
                                        </span>
                                    </label>
                                    <label for="fecha_inicio" class="block text-gray-700 font-bold mb-2">
                                        6) Fecha de inicio:
                                    </label>
                                    <input type="date" id="fecha_inicio" name="fecha_inicio"
                                        class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                        required value="<?php echo date('Y-m-d'); ?>" title="Fecha actual por defecto." />
                                </div>
                            </div>
                            <hr class="mt-4 mb-4">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-bold mb-2">
                                    (Opcional) Diferimiento de cobro (de no existir, dejar todos los campos vacios):
                                </label>
                                <input type="number" id="diff_cuotas" name="diff_cuotas" placeholder="Ej: 10"
                                    class="mt-2 appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                    title="Cantidad de cuotas adicionales." />
                                <input type="text" id="diff_capital" name="diff_capital"
                                    placeholder="Ej: 3500.75" pattern="[0-9]*[.,]?[0-9]*" inputmode="decimal"
                                    class="mt-2 appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                    title="Monto del diferimiento." />
                                <input type="text" id="diff_interes" name="diff_interes"
                                    placeholder="Ej: 1200.50" pattern="[0-9]*[.,]?[0-9]*" inputmode="decimal"
                                    class="mt-2 appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                    title="Interes del diferimiento." />
                            </div>
                            <div class="flex justify-end mt-4">
                                <x-personal.button submit="true" iconCenter="fa-solid fa-calculator text-xl">
                                    Vista Previa del Plan
                                </x-personal.button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
