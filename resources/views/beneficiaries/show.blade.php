<x-app-layout>
    <div class="w-full mt-4">
        <div class="px-4 grid sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-2 relative">
            <button type="button" onclick="startBeneficiaryTour()"
                class="absolute top-2 left-[1.5rem] p-1 rounded-full bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400"
                title="Guía rápida">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <div class="bg-white shadow-lg rounded-lg p-6 mb-4 h-fit border border-gray-300" id="profile_preview">
                <div class="flex items-center justify-between border-b pb-2" id="profile_header">
                    <div class="flex items-center space-x-4 border-l-4 border-gray-500 pl-2">
                        <h1 class="text-xl font-medium text-gray-900">
                            {{ $beneficiary->nombre }}
                        </h1>
                    </div>
                    <div class="flex items-center gap-3 p-2 bg-gray-200 rounded-md" id="profile_actions">
                        @can('write beneficiaries')
                            <div id="update-beneficiary">
                                <livewire:beneficiary-update lazy :beneficiary="$beneficiary" />
                            </div>
                        @endcan

                        @if (auth()->user()->can('read settlements') || auth()->user()->can('write settlements'))
                            <div id="settle-beneficiary">
                                <livewire:settle-beneficiary lazy :beneficiary="$beneficiary" />
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-center my-2" id="profile_identifiers">
                    <span class="text-xs text-gray-800 bg-gray-200 p-2 rounded-md">
                        CI: <span class="font-medium">{{ $beneficiary->ci }} {{ $beneficiary->complemento }}
                            {{ $beneficiary->expedido }}</span> |
                        COD.CREDITO: <span class="font-medium">{{ $beneficiary->idepro }}
                            ({{ $beneficiary->entidad_financiera }})</span>
                    </span>
                </div>
                <hr>
                @if (session('success'))
                    <x-personal.alert type="success" message="{{ session('success') }}"
                        goto="{{ session('file') ?? null }}" />
                @endif

                <div class="mt-2 grid grid-cols-2 gap-4" id="profile_infocards">
                    @php
                        $infoCards = [
                            [
                                'icon' => 'fas fa-shield',
                                'label' => 'Estado del Credito',
                                'value' => $beneficiary->estado,
                                'isStatus' => true,
                            ],
                            [
                                'icon' => 'fas fa-project-diagram',
                                'label' => 'Proyecto',
                                'value' => $beneficiary->proyecto,
                            ],
                            [
                                'icon' => 'fas fa-map-marked-alt',
                                'label' => 'Departamento',
                                'value' => $beneficiary->departamento,
                            ],
                            [
                                'icon' => 'fas fa-calendar-check',
                                'label' => 'Fecha Activacion',
                                'value' => $beneficiary->fecha_activacion,
                            ],
                            [
                                'icon' => 'fas fa-dollar-sign',
                                'label' => 'Monto Activado',
                                'value' => number_format($beneficiary->monto_activado, 2),
                            ],
                            [
                                'icon' => 'fas fa-money-bill-wave',
                                'label' => 'Saldo Credito',
                                'value' => number_format($beneficiary->saldo_credito, 2),
                            ],
                            [
                                'icon' => 'fas fa-piggy-bank',
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
                                    <i class="{{ $card['icon'] }} text-blue-500"></i>
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
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100" id="profile_plan">
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
                                            $fechaInicio = \Carbon\Carbon::parse(
                                                $vencs->first()->fecha_ppg,
                                            )->startOfDay();
                                            $diff = $fechaInicio->diffInDays(now()->startOfDay());
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

                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100" id="profile_payments">
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
                                        text: null, //'Evolución Mensual de Cumplimiento de Pagos',
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
                <div class="bg-gray-100 p-4 rounded-lg flex justify-between border-l-8 border-gray-800"
                    id="profile_mgmt_header">
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
                <div x-data="{ show: false }" id="profile_mgmt_section">
                    @if ($beneficiary->estado != 'CANCELADO')
                        <button @click="show = !show" id="profile_mgmt_toggle"
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
                        <form action="{{ route('plan.reajuste') }}" method="post" id="profile_mgmt_form">
                            @csrf
                            <input type="hidden" name="idepro" value="{{ $beneficiary->idepro }}" />
                            <input type="hidden" name="plazo_credito" value="{{ $beneficiary->plazo_credito }}" />
                            <input type="hidden" name="gastos_judiciales"
                                value="{{ $beneficiary->gastos_judiciales }}" />

                            <div class="space-y-6">
                                <!-- Main Credit Information -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-gray-50 p-4 rounded-lg" id="profile_mgmt_capital">
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

                                    <div class="bg-gray-50 p-4 rounded-lg" id="profile_mgmt_months">
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
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="profile_mgmt_rates">
                                    <div class="bg-gray-50 p-4 rounded-lg" id="profile_mgmt_interest">
                                        <label for="taza_interes"
                                            class="text-gray-700 font-semibold mb-2 flex items-center">
                                            <span
                                                class="bg-blue-100 text-blue-800 font-bold rounded-full w-6 h-6 flex items-center justify-center mr-2">3</span>
                                            Interés
                                        </label>
                                        <div class="relative">
                                            <input type="text" inputmode="decimal" name="taza_interes"
                                                id="taza_interes" placeholder="Ej: 13" pattern="[0-9]*[.,]?[0-9]*"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                                required value="{{ $beneficiary->tasa_interes ?? 0 }}"
                                                title="Taza por defecto 3%">
                                            <span class="absolute right-3 top-2 text-gray-500">%</span>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 p-4 rounded-lg" id="seguro">
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
                                <div class="bg-gray-50 p-4 rounded-lg" id="profile_mgmt_options">
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
                                        <div class="mt-4">
                                            <span>Gastos adicionales: {{ $beneficiary->spends()->sum('monto') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Optional Deferral Section -->
                                <div class="bg-gray-50 p-6 rounded-lg space-y-4" id="profile_mgmt_deferral">
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

                            <div class="flex justify-end mt-6" id="profile_mgmt_submit">
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
