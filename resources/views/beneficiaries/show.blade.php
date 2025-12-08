<x-app-layout>
    @push('styles')
        <style>
            .glass-effect {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
            }

            .gradient-text {
                background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
        </style>
    @endpush

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Section -->
        <div id="beneficiary_header"
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
            </div>

            <div id="identity_info" class="flex items-center gap-4 z-10">
                <div
                    class="h-14 w-14 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="fas fa-user text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $beneficiary->nombre }}</h1>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                        <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-600 font-medium">CI:
                            {{ $beneficiary->ci }} {{ $beneficiary->complemento }} {{ $beneficiary->expedido }}</span>
                        <span class="text-gray-300">|</span>
                        <span>{{ $beneficiary->idepro }}</span>
                    </div>
                </div>
            </div>

            <div id="action_buttons" class="flex flex-wrap items-center gap-3 z-10">
                @can('write beneficiaries')
                    <livewire:beneficiary-update lazy :beneficiary="$beneficiary" />
                @endcan

                @if (auth()->user()->can('read settlements') || auth()->user()->can('write settlements'))
                    <livewire:settle-beneficiary lazy :beneficiary="$beneficiary" />
                @endif

                <div class="h-8 w-px bg-gray-200 mx-1"></div>

                <button onclick="startBeneficiaryTour()"
                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Guía Rápida">
                    <i class="fas fa-circle-question text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Column: Details & Stats -->
            <div class="space-y-6">

                <!-- Key Stats Grid -->
                <div id="status_indicators" class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Estado</div>
                        <div class="font-bold text-gray-800 flex items-center gap-2">
                            @if ($beneficiary->estado == 'CANCELADO')
                                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                            @elseif($beneficiary->estado == 'BLOQUEADO')
                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            @else
                                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                            @endif
                            {{ $beneficiary->estado }}
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                        <div class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Mora</div>
                        <div
                            class="{{ $showMora ? 'text-red-600' : 'text-green-600' }} font-bold text-lg {{ $showMora ? 'animate-pulse' : '' }}">
                            {{ $diasMora }} <span class="text-sm font-normal text-gray-500">días</span>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary Card -->
                <div id="financial_summary"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 pt-5 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-semibold text-gray-800">Resumen Financiero</h3>
                        <i class="fas fa-chart-pie text-gray-400"></i>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex justify-center items-center">
                            @php
                                $totalMonto = $beneficiary->monto_activado > 0 ? $beneficiary->monto_activado : 0.01; // Avoid division by zero
                                $percentCapital = round(($capitalCancelado / $totalMonto) * 100, 2);
                                $percentSaldo = round(($beneficiary->saldo_credito / $totalMonto) * 100, 2);
                            @endphp
                            <div id="financialPieChart" class="w-full h-auto"></div>
                        </div>
                        <div class="flex justify-evenly text-xs text-gray-600">
                            <div class="flex items-center gap-1">
                                <span class="block w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                Capital Cancelado ({{ $percentCapital }}%)
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="block w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                Saldo Crédito ({{ $percentSaldo }}%)
                            </div>
                        </div>

                        @push('scripts')
                            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                            <script>
                                document.addEventListener('livewire:navigated', () => {
                                    const options = {
                                        series: [{{ $percentCapital }}, {{ $percentSaldo }}],
                                        labels: ['Capital Cancelado', 'Saldo Crédito'],
                                        colors: ['#3B82F6', '#6366F1'], // blue-500, indigo-500 de Tailwind CSS
                                        chart: {
                                            type: 'donut',
                                            height: 160,
                                            fontFamily: 'Inter, sans-serif',
                                            sparkline: {
                                                enabled: true
                                            }
                                        },
                                        dataLabels: {
                                            enabled: false,
                                        },
                                        legend: {
                                            show: false,
                                        },
                                        tooltip: {
                                            y: {
                                                formatter: function(val) {
                                                    return val + "%"
                                                }
                                            },
                                            style: {
                                                fontSize: '12px',
                                                fontFamily: 'Inter, sans-serif',
                                            }
                                        },
                                        plotOptions: {
                                            pie: {
                                                donut: {
                                                    size: '75%',
                                                    labels: {
                                                        show: true,
                                                        total: {
                                                            show: true,
                                                            showAlways: false,
                                                            label: 'Total Activado',
                                                            fontSize: '13px',
                                                            fontWeight: '400',
                                                            color: '#6B7280',
                                                            formatter: function(w) {
                                                                return 'Bs ' + parseFloat({{ $beneficiary->monto_activado }})
                                                                    .toLocaleString('es-BO', {
                                                                        minimumFractionDigits: 2,
                                                                        maximumFractionDigits: 2
                                                                    });
                                                            }
                                                        },
                                                        value: {
                                                            offsetY: 0,
                                                            fontSize: '16px',
                                                            fontWeight: '700',
                                                            color: '#1F2937',
                                                            formatter: function(val) {
                                                                return val + '%'
                                                            }
                                                        }
                                                    }
                                                },
                                                expandOnClick: true,
                                                startAngle: -90,
                                                endAngle: 270,
                                                stroke: {
                                                    colors: ['#3B82F6', '#6366F1'], // Asegurar que los bordes coincidan con los colores de las series
                                                    width: 2
                                                }
                                            }
                                        }
                                    };

                                    const chart = new ApexCharts(document.querySelector("#financialPieChart"), options);
                                    chart.render();
                                });
                            </script>
                        @endpush

                        <div class="grid grid-cols-2 gap-4 border-t border-gray-50">
                            <div>
                                <span class="block text-xs text-gray-500">Capital Cancelado</span>
                                <span
                                    class="block font-semibold text-gray-800">{{ number_format($capitalCancelado, 2) }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-500">Monto Activado</span>
                                <span
                                    class="block font-semibold text-gray-800">{{ number_format($beneficiary->monto_activado, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info List -->
                <div id="details_list" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-3">
                    @foreach ([['icon' => 'fas fa-project-diagram', 'label' => 'Proyecto', 'value' => $beneficiary->proyecto], ['icon' => 'fas fa-map-marker-alt', 'label' => 'Departamento', 'value' => $beneficiary->departamento], ['icon' => 'fas fa-calendar-alt', 'label' => 'Fecha Activación', 'value' => $beneficiary->fecha_activacion], ['icon' => 'fas fa-money-bill-wave', 'label' => 'Gastos Adicionales', 'value' => number_format($gastosAdicionales, 2)]] as $item)
                        <div
                            class="flex items-center gap-3 p-3 rounded-xl bg-gray-50/50 hover:bg-gray-50 transition-colors">
                            <div
                                class="h-10 w-10 rounded-lg bg-white flex items-center justify-center text-blue-500 shadow-sm">
                                <i class="{{ $item['icon'] }}"></i>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">{{ $item['label'] }}</p>
                                <p class="text-sm font-bold text-gray-800">{{ $item['value'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Alerts -->
                @if (session('success'))
                    <div class="animate-fade-in-up">
                        <x-personal.alert type="success" message="{{ session('success') }}"
                            goto="{{ session('file') ?? null }}" />
                    </div>
                @endif
            </div>

            <!-- Right Column: Chart & Actions -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Chart Card -->
                <div id="chart_card" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-6">Evolución de Pagos</h3>
                    <div id="timelineChart" class="w-full h-80"></div>
                </div>

                <!-- Actions Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Plan Management -->
                    <div id="plan_management"
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <h3 class="font-bold text-gray-800">Plan de Pagos</h3>
                            </div>
                            <p class="text-sm text-gray-500 mb-4">Visualice o modifique el plan de pagos actual.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('beneficiario.pdf', $beneficiary->ci) }}" target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-file-pdf mr-2 text-red-500"></i> PDF
                            </a>
                            <livewire:plans.plan-mutator lazy :beneficiary="$beneficiary" />
                        </div>
                    </div>

                    <!-- Payments Management -->
                    <div id="payments_management"
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-green-50 rounded-lg text-green-600">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <h3 class="font-bold text-gray-800">Pagos</h3>
                            </div>
                            <p class="text-sm text-gray-500 mb-4">Registre nuevos pagos o vea el historial detallado.
                            </p>
                        </div>
                        <div class="flex flex-col gap-2">
                            @if ($hasPayments)
                                <livewire:payment-modal lazy :beneficiary="$beneficiary" title="Historial de Pagos" />
                            @else
                                <span class="text-xs text-gray-400 italic mb-2">Sin historial registrado</span>
                            @endif
                            <livewire:voucher-register :idepro="$beneficiary->idepro" title="Registrar Nuevo Pago" />
                        </div>
                    </div>
                </div>

                <!-- Generator Section (Collapsible) -->
                @if ($beneficiary->estado != 'CANCELADO')
                    <div id="plan_generator" x-data="{ open: false }"
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-orange-50 rounded-lg text-orange-600">
                                    <i class="fas fa-calculator"></i>
                                </div>
                                <div class="text-left">
                                    <h3 class="font-bold text-gray-800">Generador / Reajuste de Planes</h3>
                                    <p class="text-xs text-gray-500">Crear nuevas series de pagos</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"
                                :class="{ 'rotate-180': open }"></i>
                        </button>

                        <div x-show="open" x-collapse x-cloak class="border-t border-gray-100 p-6 bg-gray-50/50">
                            <form action="{{ route('plan.reajuste') }}" method="post">
                                @csrf
                                <input type="hidden" name="idepro" value="{{ $beneficiary->idepro }}" />
                                <input type="hidden" name="plazo_credito"
                                    value="{{ $beneficiary->plazo_credito }}" />
                                <input type="hidden" name="gastos_judiciales"
                                    value="{{ $beneficiary->gastos_judiciales }}" />

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <!-- Inputs simplified for cleaner UI -->
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-gray-600">Capital Inicial</label>
                                        <input type="number" step="0.01" name="capital_inicial"
                                            value="{{ $beneficiary->saldo_credito }}"
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                            required>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-gray-600">Meses Restantes</label>
                                        <input type="number" name="meses" value="{{ $mesesRestantes }}"
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                            required>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-gray-600">Interés (%)</label>
                                        <input type="number" step="0.01" name="taza_interes"
                                            value="{{ $beneficiary->tasa_interes ?? 0 }}"
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                            required>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold text-gray-600">Seguro (%)</label>
                                        <input type="number" step="0.001" name="seguro" value="0.04"
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                            required>
                                    </div>
                                </div>

                                <!-- Additional options toggle -->
                                <div x-data="{ advanced: false }" class="mt-4">
                                    <button type="button" @click="advanced = !advanced"
                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                                        Opciones Avanzadas <i class="fas fa-cog"></i>
                                    </button>

                                    <div x-show="advanced" x-collapse
                                        class="mt-4 p-4 bg-white rounded-lg border border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="text-xs font-semibold text-gray-600 block mb-1">Fecha
                                                Inicio</label>
                                            <input type="date" name="fecha_inicio" value="{{ date('Y-m-d') }}"
                                                class="w-full rounded-lg border-gray-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="flex items-center gap-2 mt-6 cursor-pointer">
                                                <input type="checkbox" name="correlativo"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span class="text-sm text-gray-700">Modo Reajuste</span>
                                            </label>
                                        </div>

                                        <!-- Additional Costs -->
                                        <div class="md:col-span-3 border-t pt-3 mt-2">
                                            <p class="text-xs font-semibold text-gray-600 mb-2">Diferimiento (Opcional)
                                            </p>
                                            <div class="grid grid-cols-3 gap-2">
                                                <input type="number" name="diff_cuotas" placeholder="N° Cuotas"
                                                    class="rounded-lg border-gray-300 text-sm">
                                                <input type="number" step="0.01" name="diff_capital"
                                                    placeholder="Cap. Diferido"
                                                    class="rounded-lg border-gray-300 text-sm">
                                                <input type="number" step="0.01" name="diff_interes"
                                                    placeholder="Int. Diferido"
                                                    class="rounded-lg border-gray-300 text-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <x-personal.button submit="true"
                                        class="bg-blue-600 hover:bg-blue-700 text-white"
                                        iconLeft="fas fa-calculator">
                                        Generar Previsualización
                                    </x-personal.button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chartData = @json($chartData);

                Highcharts.chart('timelineChart', {
                    chart: {
                        type: 'areaspline',
                        backgroundColor: 'transparent',
                        fontFamily: 'Inter, sans-serif'
                    },
                    title: {
                        text: ''
                    },
                    xAxis: {
                        categories: chartData.categories,
                        crosshair: true,
                        lineColor: '#e5e7eb',
                        tickColor: '#e5e7eb',
                        labels: {
                            style: {
                                color: '#6b7280',
                                fontSize: '11px'
                            }
                        }
                    },
                    yAxis: [{
                        title: {
                            text: 'Monto (Bs)',
                            style: {
                                color: '#6b7280'
                            }
                        },
                        gridLineColor: '#f3f4f6',
                        labels: {
                            style: {
                                color: '#6b7280'
                            },
                            formatter: function() {
                                return Highcharts.numberFormat(this.value, 0, ',', '.');
                            }
                        }
                    }, {
                        title: {
                            text: 'Cumplimiento',
                            style: {
                                color: '#6b7280'
                            }
                        },
                        opposite: true,
                        labels: {
                            format: '{value}%',
                            style: {
                                color: '#6b7280'
                            }
                        }
                    }],
                    tooltip: {
                        shared: true,
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        borderRadius: 8,
                        shadow: true,
                        borderWidth: 0,
                        headerFormat: '<span style="font-size: 10px; color: #9ca3af">{point.key}</span><br/>'
                    },
                    plotOptions: {
                        areaspline: {
                            fillOpacity: 0.1
                        },
                        series: {
                            marker: {
                                enabled: false,
                                symbol: 'circle'
                            }
                        }
                    },
                    series: [{
                        name: 'Planificado',
                        data: chartData.plans,
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
                                [1, 'rgba(59, 130, 246, 0.0)']
                            ]
                        }
                    }, {
                        name: 'Pagado',
                        data: chartData.payments,
                        color: '#10b981',
                        fillColor: {
                            linearGradient: {
                                x1: 0,
                                y1: 0,
                                x2: 0,
                                y2: 1
                            },
                            stops: [
                                [0, 'rgba(16, 185, 129, 0.2)'],
                                [1, 'rgba(16, 185, 129, 0.0)']
                            ]
                        }
                    }, {
                        name: '% Cumplimiento',
                        type: 'spline',
                        yAxis: 1,
                        data: chartData.compliance,
                        color: '#f59e0b',
                        dashStyle: 'ShortDot',
                        tooltip: {
                            valueSuffix: ' %'
                        }
                    }],
                    credits: {
                        enabled: false
                    },
                    legend: {
                        itemStyle: {
                            color: '#4b5563',
                            fontWeight: '500'
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
