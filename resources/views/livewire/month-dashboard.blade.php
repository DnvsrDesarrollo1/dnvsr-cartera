<div class="p-4">
    <div class="bg-white p-4 border dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="p-4 sm:px-6 border rounded-md">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                Dashboard de Recaudaciones
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                Seleccione un proyecto y rango de fechas para ver las recaudaciones.
            </p>

        </div>
        <div class="mt-2">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 divide-x divide-dashed">
                    <div class="p-2" x-data="{ open: false }">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4"
                            for="default-search">
                            <span class="border p-2 rounded-full dark:border-gray-100 mr-2">1</span>
                            <span>Buscar proyecto</span>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input @click="open = true" @click.outside="open = false"
                                wire:model.live.debounce.500="search" type="search" id="default-search"
                                autocomplete="off"
                                class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-3 pr-12 sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Mostrar por proyecto..." />
                        </div>
                        @if ($projects != '')
                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-90"
                                class="absolute z-10 mt-1 w-fit bg-white dark:bg-gray-700 dark:text-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @foreach ($projects as $p)
                                    <div wire:key="'{{ $p->proyecto }}'"
                                        @click="$wire.selectProject('{{ $p->proyecto }}')"
                                        class="cursor-pointer relative py-2 pl-3 pr-9 hover:font-bold">
                                        {{ $p->proyecto }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @if ($selected)
                            <div class="mt-2">
                                <!-- Add the reset button here -->
                                <button wire:click="resetSearch"
                                    class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Reset
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="p-2">
                        <label for="start-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            <span class="border p-2 rounded-full dark:border-gray-100 mr-2">2</span><span>Fecha de
                                inicio</span>
                        </label>
                        <input type="date" id="start-date" wire:model.live="fechaInicio" min="{{ $fechaInicio }}"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="p-2">
                        <label for="end-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            <span class="border p-2 rounded-full dark:border-gray-100 mr-2">3</span><span>Fecha de
                                fin</span>
                        </label>
                        <input type="date" id="end-date" wire:model.live="fechaFin" max="{{ $fechaFin }}"
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div wire:loading.flex wire:target="selectProject"
        class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl">
            <svg class="animate-spin h-10 w-10 text-indigo-500 mx-auto" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <p class="mt-4 text-center text-gray-700">Cargando datos del proyecto . . .</p>
        </div>
    </div>

    @if ($selected)
        <div class="mt-8 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex flex-col items-center">
                <h3
                    class="text-xl leading-6 font-medium text-gray-900 dark:text-white border border-gray-500 p-4 rounded-md">
                    Proyecto: "{{ $selected }}"
                </h3>
                <p class="mt-2 font-bold text-lg max-w-2xl text-gray-500 dark:text-gray-400">
                    Recaudaciones del {{ $fechaInicio }} al {{ $fechaFin }}
                </p>
            </div>
            <dl
                class="grid max-w-screen-xl grid-cols-2 gap-4 p-4 mx-auto text-gray-900 sm:grid-cols-3 xl:grid-cols-3 border dark:text-white sm:p-8 rounded-md">
                <div
                    class="flex flex-col p-1 items-center justify-center border rounded-md border-green-500 cursor-pointer hover:shadow-lg shadow-gray-500 dark:hover:shadow-gray-500">
                    <dt class="text-xl font-extrabold">Bs. {{ number_format($total_capital, 2) }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400">CAPITAL</dd>
                </div>
                <div
                    class="flex flex-col p-1 items-center justify-center border rounded-md border-green-500 cursor-pointer hover:shadow-lg shadow-gray-500 dark:hover:shadow-gray-500">
                    <dt class="text-xl font-extrabold">Bs. {{ number_format($total_interes, 2) }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400">INTERES</dd>
                </div>
                <div
                    class="flex flex-col p-1 items-center justify-center border rounded-md border-green-500 cursor-pointer hover:shadow-lg shadow-gray-500 dark:hover:shadow-gray-500">
                    <dt class="text-xl font-extrabold">Bs. {{ number_format($total_seguro, 2) }}</dt>
                    <dd class="text-gray-500 dark:text-gray-400">SEGURO</dd>
                </div>
            </dl>
            <hr />
            <dl
                class="grid max-w-screen-xl grid-cols-2 gap-4 p-4 mx-auto text-gray-900 sm:grid-cols-3 xl:grid-cols-6 border dark:text-white sm:p-8 rounded-md">
                @foreach ($payments as $payment)
                    <div
                        class="flex flex-col p-1 items-center justify-center border rounded-md border-gray-500 cursor-pointer hover:shadow-lg shadow-gray-500 dark:hover:shadow-gray-500">
                        <dt class="text-xl font-extrabold">Bs. {{ number_format($payment->total_monto, 2) }}</dt>
                        <dd class="text-gray-500 dark:text-gray-400">{{ $payment->prtdtdesc }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-4 flex justify-center">
                <button onclick="location.replace(location.href);"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Actualizar Gráficas
                </button>
            </div>

            <div class="mt-8 flex justify-center items-center">
                <div id="paymentsChart" class="w-11/12 rounded-md" style="height: 750px;"></div>
            </div>

            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Highcharts.chart('paymentsChart', {
                            chart: {
                                type: 'lollipop'
                            },
                            title: {
                                text: 'Resumen de Depósitos por Tipo'
                            },
                            xAxis: {
                                categories: {!! json_encode($payments->pluck('prtdtdesc')) !!},
                                title: {
                                    text: 'Tipo de Depósito'
                                }
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: 'Monto (Bs)'
                                },
                                labels: {
                                    formatter: function() {
                                        return Highcharts.numberFormat(this.value, 2);
                                    }
                                }
                            },
                            tooltip: {
                                valueDecimals: 2,
                                valueSuffix: ' Bs'
                            },
                            plotOptions: {
                                column: {
                                    dataLabels: {
                                        enabled: true,
                                        formatter: function() {
                                            return Highcharts.numberFormat(this.y, 2);
                                        }
                                    },
                                    series: {
                                        allowPointSelect: true
                                    }
                                }
                            },
                            series: [{
                                name: 'Recaudación por Glosa',
                                data: {!! json_encode(
                                    $payments->pluck('total_monto')->map(function ($value) {
                                        return round($value, 2);
                                    }),
                                ) !!}
                            }]
                        });
                    });
                </script>
            @endpush

            <!-- Highcharts Timeline Chart -->
            <div class="mt-8 flex justify-center items-center">
                <div id="timelineChart" class="w-11/12 rounded-md" style="height: 400px;"></div>
            </div>

            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Crear array de meses desde fecha de inicio hasta fecha de fin
                        let startDate = new Date("{{ $fechaInicio }}");
                        let endDate = new Date("{{ $fechaFin }}");
                        let months = [];
                        let currentDate = new Date(startDate);

                        while (currentDate <= endDate) {
                            months.push(currentDate.toISOString().slice(0, 7));
                            currentDate.setMonth(currentDate.getMonth() + 1);
                        }

                        // Inicializar arrays de datos con ceros
                        let budgetData = new Array(months.length).fill(null);
                        let timelineData = new Array(months.length).fill(null);

                        // Rellenar datos del presupuesto
                        let paymentsBudget = {!! json_encode($paymentsBudget) !!};
                        paymentsBudget.forEach(item => {
                            let index = months.indexOf(item.mes);
                            if (index !== -1) {
                                budgetData[index] = parseFloat(item.total_monto);
                            }
                        });

                        // Rellenar datos de la línea de tiempo
                        let paymentsTimeline = {!! json_encode($paymentsTimeline) !!};
                        paymentsTimeline.forEach(item => {
                            let index = months.indexOf(item.mes);
                            if (index !== -1) {
                                timelineData[index] = parseFloat(item.total_monto);
                            }
                        });

                        Highcharts.chart('timelineChart', {
                            chart: {
                                type: 'area'
                            },
                            title: {
                                text: 'Capital Planificado vs Recaudado por Mes'
                            },
                            xAxis: {
                                categories: months,
                                title: {
                                    text: 'Mes'
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'Monto (Bs)'
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
                                line: {
                                    dataLabels: {
                                        enabled: false,
                                        formatter: function() {
                                            return Highcharts.numberFormat(this.y, 2) + ' Bs';
                                        }
                                    },
                                    enableMouseTracking: true
                                }
                            },
                            series: [{
                                name: 'Capital Planificado',
                                data: budgetData,
                                color: 'rgb(255, 99, 132)',
                                fillColor: {
                                    pattern: {
                                        color: 'rgba(150, 99, 132)',
                                    }
                                }
                            }, {
                                name: 'Capital Recaudado',
                                data: timelineData,
                                color: 'rgb(75, 192, 192)',
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

            <div class="mt-8 flex justify-center items-center p-4">
                @php
                    $pb = $paymentsBudget->sum('total_monto');
                    $pb = $pb == 0 ? 1 : $pb;
                    $ejec = round(($paymentsTimeline->sum('total_monto') / $pb) * 100, 2);
                @endphp
                <div id="executionGauge" class="w-fit rounded-md" style="height: 400px;"></div>
            </div>

            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Highcharts.chart('executionGauge', {
                            chart: {
                                type: 'gauge',
                            },
                            title: {
                                text: 'Ejecución de Recaudaciones'
                            },
                            pane: {
                                startAngle: -150,
                                endAngle: 150,
                                background: {
                                    backgroundColor: 'transparent',
                                    borderWidth: 0
                                }
                            },
                            // The value axis
                            yAxis: {
                                min: 0,
                                max: 120,

                                minorTickInterval: 0,
                                tickColor: '#ffffff',
                                tickLength: 40,
                                tickPixelInterval: 40,
                                tickWidth: 2,
                                lineWidth: 0,
                                title: {
                                    text: 'RECAUDACIÓN DE CAPITAL'
                                },
                                labels: {
                                    distance: 20,
                                    style: {
                                        color: '#999999'
                                    },
                                },
                                // Plot bands with rounded corners. To avoid the bands having rounded
                                // corners in the transitions between them, we apply a trick. For the
                                // first and the last band we apply rounded corners, but let them extend
                                // behind the middle one. The middle one is not rounded, but has a
                                // higher zIndex to be above the other two.
                                plotBands: [{
                                    from: 1,
                                    to: 59,
                                    color: '#f94144',
                                    innerRadius: '90%',
                                    borderRadius: '35%'
                                }, {
                                    from: 60,
                                    to: 89,
                                    color: '#ffea00',
                                    innerRadius: '90%',
                                    borderRadius: '35%',
                                    zIndex: 1
                                }, {
                                    from: 90,
                                    to: 119,
                                    color: '#43aa8b',
                                    innerRadius: '90%',
                                    borderRadius: '35%'
                                }]
                            },
                            series: [{
                                name: 'Ejecución',
                                data: [{{ $ejec }}],
                                tooltip: {
                                    valueSuffix: ' %'
                                },
                                dataLabels: {
                                    format: '{y} %',
                                    borderWidth: 0,
                                    color: '#333',
                                    style: {
                                        fontSize: '2em'
                                    }
                                }
                            }]
                        });
                    });
                </script>
            @endpush

        </div>
    @endif



</div>
