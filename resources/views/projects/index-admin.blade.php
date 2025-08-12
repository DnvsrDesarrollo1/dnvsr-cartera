<x-app-layout>
    <div class="w-full bg-white h-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-full">
            {{-- Chart --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <div id="department-status-chart-container" class="h-full"></div>
            </div>

            {{-- Data Table --}}
            <div class="bg-gray-50 rounded-lg p-4 flex items-center">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg w-full">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th scope="col"
                                    class="py-2 px-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Departamento
                                </th>
                                @foreach ($allStatuses as $status)
                                    <th scope="col"
                                        class="py-2 px-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        {{ $status }}
                                    </th>
                                @endforeach
                                <th scope="col"
                                    class="py-2 px-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($tableData as $department => $data)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-2 px-3 text-sm font-medium text-gray-900">
                                        {{ $department }}
                                    </td>
                                    @foreach ($allStatuses as $status)
                                        <td class="py-2 px-3 text-sm text-gray-500 text-center">
                                            {{ $data['statuses'][$status] ?? 0 }}
                                        </td>
                                    @endforeach
                                    <td class="py-2 px-3 text-sm font-bold text-gray-700 text-right">
                                        {{ $data['total'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($allStatuses) + 2 }}"
                                        class="py-4 px-3 text-sm text-gray-500 text-center italic">
                                        No hay datos disponibles.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.chart('department-status-chart-container', {
                chart: {
                    type: 'column',
                    style: {
                        fontFamily: 'Inter, sans-serif'
                    }
                },
                title: {
                    text: null
                },
                subtitle: {
                    text: 'Total de {{ $totalBeneficiaries }} beneficiarios en {{ count($chartCategories) }} departamentos'
                },
                xAxis: {
                    categories: {!! json_encode($chartCategories) !!},
                    title: {
                        text: null
                    },
                    labels: {
                        rotation: -45,
                        style: {
                            fontSize: '10px'
                        }
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Número de Beneficiarios'
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            fontWeight: 'bold',
                            color: (Highcharts.defaultOptions.title.style && Highcharts.defaultOptions.title
                                .style.color) || 'gray',
                            textOutline: 'none'
                        }
                    }
                },
                legend: {
                    align: 'center',
                    verticalAlign: 'bottom',
                    y: 20,
                    itemStyle: {
                        fontWeight: 'normal'
                    }
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    pointFormat: '<span style="color:{series.color}">●</span> {series.name}: <b>{point.y}</b> ({point.percentage:.1f}%)<br/>',
                    shared: true
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                if (this.y > 0 && this.point.percentage > 5) {
                                    return this.y;
                                }
                            },
                            style: {
                                color: 'white',
                                textOutline: 'none',
                                fontWeight: 'bold'
                            }
                        }
                    }
                },
                series: {!! json_encode($chartSeries) !!},
                credits: {
                    enabled: false
                }
            });
        });
    </script>
</x-app-layout>
