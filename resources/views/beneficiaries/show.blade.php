<x-app-layout>
    <div class="w-full mt-4">
        <div class="px-4 grid sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-2">
            <div class="bg-white shadow-lg rounded-lg p-6 mb-4 h-fit border border-gray-300" id=$"profile_preview">
                <div class="flex items-center justify-between border-b pb-4">
                    <div class="flex items-center space-x-4">
                        <h1 class="text-xl font-medium text-gray-900">
                            {{ $beneficiary->nombre }}
                        </h1>
                    </div>

                    <div class="flex items-center gap-3">
                        @if ($beneficiary->estado != 'BLOQUEADO' && $beneficiary->estado != 'CANCELADO')
                            @can('write beneficiaries')
                                <form action="{{ route('beneficiario.update', $beneficiary) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                        class="p-1.5 rounded-full hover:bg-gray-100 text-gray-500 hover:text-red-600 transition-colors"
                                        onclick="return confirm('¿Está seguro de bloquear este beneficiario?')"
                                        title="Bloquear beneficiario">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </button>
                                </form>
                            @endcan
                        @endif

                        @can('write beneficiaries')
                            @livewire('beneficiary-update', ['beneficiary' => $beneficiary])
                        @endcan

                        @if (auth()->user()->can('read settlements') || auth()->user()->can('write settlements'))
                            @livewire('settle-beneficiary', ['beneficiary' => $beneficiary])
                        @endif
                    </div>
                </div>

                <div class="flex items-center my-2">
                    <span class="text-sm text-gray-600">
                        CI: {{ $beneficiary->ci }} {{ $beneficiary->complemento }} {{ $beneficiary->expedido }} | COD.CREDITO: <span class="font-medium">{{ $beneficiary->idepro }}</span>
                    </span>
                </div>
                <hr>
                @if (session('success'))
                    <x-personal.alert type="success" message="{{ session('success') }}"
                        goto="{{ session('file') ?? null }}" />
                @endif

                <div class="mt-2 grid grid-cols-2 gap-4">
                    @php
                        $infoCards = [
                            [
                                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                'label' => 'Estado del Credito',
                                'value' => $beneficiary->estado,
                                'isStatus' => true,
                            ],
                            [
                                'icon' =>
                                    'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                                'label' => 'Proyecto',
                                'value' => $beneficiary->proyecto,
                            ],
                            [
                                'icon' =>
                                    'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z',
                                'label' => 'Departamento',
                                'value' => $beneficiary->departamento,
                            ],
                            [
                                'icon' =>
                                    'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                                'label' => 'Fecha Activacion',
                                'value' => $beneficiary->fecha_activacion,
                            ],
                            [
                                'icon' =>
                                    'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                'label' => 'Monto Activado',
                                'value' => number_format($beneficiary->monto_activado, 2),
                            ],
                            [
                                'icon' =>
                                    'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3',
                                'label' => 'Saldo Credito',
                                'value' => number_format($beneficiary->saldo_credito, 2),
                            ],
                            [
                                'icon' =>
                                    'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                                'label' => 'Capital Cancelado',
                                'value' => number_format(
                                    $beneficiary->payments()->where('prtdtdesc', 'like', '%CAPI%')->sum('montopago'),
                                    2,
                                ),
                            ],
                        ];
                    @endphp

                    @foreach ($infoCards as $card)
                        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100 flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="{{ $card['icon'] }}" />
                                    </svg>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-gray-500 font-medium">{{ $card['label'] }}</p>
                                <p
                                    class="text-sm font-semibold {{ isset($card['isStatus']) && ($beneficiary->estado == 'CANCELADO' || $beneficiary->estado == 'BLOQUEADO') ? 'text-red-500' : 'text-gray-700' }}">
                                    {{ $card['value'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach

                    <div class="col-span-2 grid grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                            @php
                                $plans = $beneficiary->getCurrentPlan();
                            @endphp
                            <div class="flex flex-col items-center gap-2">
                                @if ($plans->count() > 0)
                                    <livewire:plan-modal lazy :beneficiary="$beneficiary" title="Plan de Pagos" />
                                    @php
                                        $vencs = $beneficiary
                                            ->plans()
                                            ->where('estado', '!=', 'CANCELADO')
                                            ->orderBy('fecha_ppg')
                                            ->get();
                                        if ($vencs->count() > 0) {
                                            $fechaInicio = \Carbon\Carbon::parse($vencs->first()->fecha_ppg);
                                            $diff = $fechaInicio->diffInDays(now());
                                            echo "<p class=\"text-sm text-red-500\">Dias de Mora: <b>" .
                                                number_format(max(0, $diff), 0) .
                                                '</b></p>';
                                        }
                                    @endphp
                                @else
                                    <p class="text-sm text-gray-500 italic">Sin plan de pagos registrado</p>
                                @endif
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                            <div class="flex flex-col items-center gap-2">
                                @if ($beneficiary->payments()->count() > 0)
                                    <livewire:payment-modal lazy :beneficiary="$beneficiary" title="Historial de Pagos" />
                                @else
                                    <p class="text-sm text-gray-500 italic">Sin historial de pagos registrado</p>
                                @endif
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

                                // Ajustar fechas para incluir trimestre completo
                                startDate.setMonth(startDate.getMonth() - 1);
                                endDate.setMonth(endDate.getMonth() + 1);

                                let months = [];
                                let currentDate = new Date(startDate);

                                // Generar array de meses con formato más legible
                                const monthNames = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
                                while (currentDate <= endDate) {
                                    months.push(`${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`);
                                    currentDate.setMonth(currentDate.getMonth() + 1);
                                }

                                // Inicializar arrays de datos
                                let plansData = new Array(months.length).fill(null);
                                let paymentsData = new Array(months.length).fill(null);
                                let complianceData = new Array(months.length).fill(null);

                                // Procesar datos de planes
                                plans.forEach(plan => {
                                    let planDate = new Date(plan.fecha_ppg);
                                    let planMonth = `${monthNames[planDate.getMonth()]} ${planDate.getFullYear()}`;
                                    let index = months.indexOf(planMonth);
                                    if (index !== -1) {
                                        plansData[index] = Math.abs(parseFloat(plan.prppgcapi));
                                    }
                                });

                                // Procesar datos de pagos
                                payments.forEach(payment => {
                                    let paymentDate = new Date(payment.fecha_pago);
                                    let paymentMonth = `${monthNames[paymentDate.getMonth()]} ${paymentDate.getFullYear()}`;
                                    let index = months.indexOf(paymentMonth);
                                    if (index !== -1) {
                                        paymentsData[index] = (paymentsData[index] || 0) + Math.abs(parseFloat(payment
                                            .montopago));

                                        // Calcular porcentaje de cumplimiento si existe plan para ese mes
                                        if (plansData[index]) {
                                            complianceData[index] = (paymentsData[index] / plansData[index]) * 100;
                                        }
                                    }
                                });

                                // Modern chart configuration
                                Highcharts.chart('timelineChart', {
                                    chart: {
                                        type: 'line',
                                        style: {
                                            fontFamily: 'Inter, sans-serif'
                                        },
                                        backgroundColor: 'transparent',
                                        borderRadius: 12,
                                        spacing: [20, 20, 15, 20]
                                    },
                                    title: {
                                        text: null,//'Evolución Mensual de Cumplimiento de Pagos',
                                        align: 'left',
                                        /* style: {
                                            fontSize: '18px',
                                            fontWeight: '600',
                                            color: '#1e293b'
                                        },
                                        margin: 25 */
                                    },
                                    /* subtitle: {
                                        text: 'Comparación entre capital planificado y pagado',
                                        align: 'left',
                                        style: {
                                            color: '#64748b',
                                            fontSize: '13px'
                                        }
                                    }, */
                                    xAxis: {
                                        categories: months,
                                        labels: {
                                            style: {
                                                color: '#64748b',
                                                fontSize: '11px'
                                            },
                                            rotation: -45
                                        },
                                        lineColor: '#e2e8f0',
                                        tickColor: '#e2e8f0',
                                        crosshair: {
                                            width: 1,
                                            color: '#94a3b8',
                                            dashStyle: 'dot'
                                        }
                                    },
                                    yAxis: [{
                                        title: {
                                            text: 'Monto (Bs)',
                                            style: {
                                                color: '#64748b'
                                            }
                                        },
                                        labels: {
                                            style: {
                                                color: '#64748b'
                                            },
                                            formatter: function() {
                                                return Highcharts.numberFormat(this.value, 0, ',', '.');
                                            }
                                        },
                                        gridLineColor: '#f1f5f9',
                                        opposite: false
                                    }, {
                                        title: {
                                            text: '% Cumplimiento',
                                            style: {
                                                color: '#64748b'
                                            }
                                        },
                                        labels: {
                                            style: {
                                                color: '#64748b'
                                            },
                                            formatter: function() {
                                                return this.value + '%';
                                            }
                                        },
                                        min: 0,
                                        max: 100,
                                        gridLineWidth: 0,
                                        opposite: true
                                    }],
                                    tooltip: {
                                        backgroundColor: 'rgba(255, 255, 255, 0.97)',
                                        borderWidth: 0,
                                        borderRadius: 12,
                                        shadow: {
                                            color: 'rgba(0,0,0,0.08)',
                                            width: 10,
                                            offsetX: 0,
                                            offsetY: 4
                                        },
                                        style: {
                                            fontSize: '13px',
                                            fontWeight: '400'
                                        },
                                        valueDecimals: 2,
                                        valueSuffix: ' Bs',
                                        shared: true,
                                        useHTML: true,
                                        headerFormat: '<small style="color: #64748b; font-weight: 600">{point.key}</small><br/><table>',
                                        pointFormat: '<tr><td><span style="color:{point.color}">●</span> {series.name}: </td>' +
                                            '<td style="text-align: right"><b>{point.y:,.2f} Bs</b></td></tr>',
                                        footerFormat: '</table>'
                                    },
                                    legend: {
                                        align: 'center',
                                        verticalAlign: 'top',
                                        itemStyle: {
                                            color: '#475569',
                                            fontWeight: '500',
                                            fontSize: '12px'
                                        },
                                        itemHoverStyle: {
                                            color: '#1e293b'
                                        },
                                        itemMarginBottom: 8,
                                        symbolRadius: 0,
                                        padding: 10
                                    },
                                    plotOptions: {
                                        series: {
                                            marker: {
                                                radius: 5,
                                                lineWidth: 2,
                                                lineColor: '#ffffff',
                                                symbol: 'circle',
                                                fillColor: null
                                            },
                                            states: {
                                                hover: {
                                                    halo: {
                                                        size: 8,
                                                        opacity: 0.2
                                                    }
                                                }
                                            }
                                        },
                                        line: {
                                            lineWidth: 3,
                                            fillOpacity: 0
                                        },
                                        area: {
                                            fillOpacity: 0.15,
                                            marker: {
                                                enabled: true
                                            }
                                        }
                                    },
                                    series: [{
                                        name: 'Capital Planificado',
                                        data: plansData,
                                        type: 'area',
                                        color: '#3b82f6',
                                        fillColor: {
                                            linearGradient: {
                                                x1: 0,
                                                y1: 0,
                                                x2: 0,
                                                y2: 1
                                            },
                                            stops: [
                                                [0, 'rgba(59, 130, 246, 0.2)'],
                                                [1, 'rgba(59, 130, 246, 0.05)']
                                            ]
                                        },
                                        zIndex: 1,
                                        yAxis: 0
                                    }, {
                                        name: 'Capital Pagado',
                                        data: paymentsData,
                                        type: 'area',
                                        color: '#22c55e',
                                        fillColor: {
                                            linearGradient: {
                                                x1: 0,
                                                y1: 0,
                                                x2: 0,
                                                y2: 1
                                            },
                                            stops: [
                                                [0, 'rgba(34, 197, 94, 0.2)'],
                                                [1, 'rgba(34, 197, 94, 0.05)']
                                            ]
                                        },
                                        zIndex: 2,
                                        yAxis: 0
                                    }, {
                                        name: '% Cumplimiento',
                                        data: complianceData,
                                        type: 'line',
                                        color: '#f59e0b',
                                        dashStyle: 'Dash',
                                        marker: {
                                            symbol: 'diamond'
                                        },
                                        yAxis: 1,
                                        tooltip: {
                                            valueSuffix: '%',
                                            valueDecimals: 1
                                        },
                                        zIndex: 3
                                    }],
                                    credits: {
                                        enabled: false
                                    },
                                    responsive: {
                                        rules: [{
                                            condition: {
                                                maxWidth: 600
                                            },
                                            chartOptions: {
                                                legend: {
                                                    layout: 'horizontal',
                                                    align: 'center',
                                                    verticalAlign: 'bottom'
                                                },
                                                xAxis: {
                                                    labels: {
                                                        rotation: -30
                                                    }
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
                    <div class="bg-white rounded-lg shadow-lg p-8 border border-gray-200" x-show="show" x-transition
                        x-cloak>
                        <form action="{{ route('plan.reajuste') }}" method="post">
                            @csrf
                            <input type="hidden" name="idepro" value="{{ $beneficiary->idepro }}" />
                            <input type="hidden" name="plazo_credito" value="{{ $beneficiary->plazo_credito }}" />
                            <input type="hidden" name="gastos_judiciales"
                                value="{{ $beneficiary->gastos_judiciales }}" />

                            <div class="space-y-6">
                                <!-- Main Credit Information -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <label for="capital_inicial"
                                            class="text-gray-700 font-semibold mb-2 flex items-center">
                                            <span
                                                class="bg-blue-100 text-blue-800 font-bold rounded-full w-6 h-6 flex items-center justify-center mr-2">1</span>
                                            Capital Inicial
                                        </label>
                                        <input type="text" inputmode="decimal" id="capital_inicial"
                                            name="capital_inicial" placeholder="Ej: 25000.75"
                                            pattern="[0-9]*[.,]?[0-9]*"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            required value="{{ $beneficiary->saldo_credito }}"
                                            title="Saldo restante de (Monto Activado menos Monto en Cuotas)">
                                    </div>

                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <label for="meses"
                                            class="text-gray-700 font-semibold mb-2 flex items-center">
                                            <span
                                                class="bg-blue-100 text-blue-800 font-bold rounded-full w-6 h-6 flex items-center justify-center mr-2">2</span>
                                            Meses restantes
                                        </label>
                                        <input type="text" id="meses" name="meses" placeholder="Ej: 10"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            required value="{{ $mesesRestantes }}"
                                            title="Meses restantes desde hoy, a la fecha de activacion (+ 20 años)">
                                    </div>
                                </div>

                                <!-- Interest and Insurance -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <label for="taza_interes"
                                            class="text-gray-700 font-semibold mb-2 flex items-center">
                                            <span
                                                class="bg-blue-100 text-blue-800 font-bold rounded-full w-6 h-6 flex items-center justify-center mr-2">3</span>
                                            Interés
                                        </label>
                                        <div class="relative">
                                            <input type="text" inputmode="decimal" name="taza_interes"
                                                placeholder="Ej: 13" pattern="[0-9]*[.,]?[0-9]*"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                                required value="{{ $beneficiary->tasa_interes ?? 0 }}"
                                                title="Taza por defecto 3%">
                                            <span class="absolute right-3 top-2 text-gray-500">%</span>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <label for="seguro"
                                            class="text-gray-700 font-semibold mb-2 flex items-center">
                                            <span
                                                class="bg-blue-100 text-blue-800 font-bold rounded-full w-6 h-6 flex items-center justify-center mr-2">4</span>
                                            Seguro
                                        </label>
                                        <div class="relative">
                                            <input type="text" inputmode="decimal" name="seguro"
                                                placeholder="Ej: 0.04" pattern="[0-9]*[.,]?[0-9]*"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                                required value="0.04" title="Seguro por defecto 0.04%">
                                            <span class="absolute right-3 top-2 text-gray-500">%</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Options -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="space-y-4">
                                        <label for="correlativo"
                                            class="hidden items-center p-4 bg-white rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:bg-gray-50 transition"
                                            title="Desactivado: el n# de cuota empezará desde el 1 para adelante, de lo contrario, desde la ultima cuota correspondiente a los meses restantes">
                                            <span
                                                class="bg-blue-100 text-blue-800 font-bold rounded-full w-6 h-6 flex items-center justify-center mr-2">5</span>
                                            <x-checkbox id="correlativo" name="correlativo" />
                                            <span class="ml-2 text-gray-700">Reajuste (marcado) / Activacion
                                                (desmarcado)</span>
                                        </label>

                                        <div class="mt-4">
                                            <label for="fecha_inicio"
                                                class="text-gray-700 font-semibold mb-2 flex items-center">
                                                <span
                                                    class="bg-blue-100 text-blue-800 font-bold rounded-full w-6 h-6 flex items-center justify-center mr-2">6</span>
                                                Fecha de inicio
                                            </label>
                                            <input type="date" id="fecha_inicio" name="fecha_inicio"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                                required value="<?php echo date('Y-m-d'); ?>" title="Fecha actual por defecto">
                                        </div>
                                    </div>
                                </div>

                                <!-- Optional Deferral Section -->
                                <div class="bg-gray-50 p-6 rounded-lg space-y-4">
                                    <h3 class="font-semibold text-gray-800 mb-4">Diferimiento de cobro (Opcional)</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <input type="number" id="diff_cuotas" name="diff_cuotas"
                                            placeholder="Número de cuotas"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            title="Cantidad de cuotas adicionales">

                                        <input type="text" id="diff_capital" name="diff_capital"
                                            placeholder="Monto diferido" pattern="[0-9]*[.,]?[0-9]*"
                                            inputmode="decimal"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            title="Monto del diferimiento">

                                        <input type="text" id="diff_interes" name="diff_interes"
                                            placeholder="Interés diferido" pattern="[0-9]*[.,]?[0-9]*"
                                            inputmode="decimal"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                            title="Interes del diferimiento">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <x-personal.button submit="true" iconCenter="fa-solid fa-calculator text-xl"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
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
