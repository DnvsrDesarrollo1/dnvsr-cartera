<x-app-layout>
    <div class="w-full mx-auto min-h-screen p-4 overflow-y-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 h-full">
            @foreach ($tableData as $department => $data)
                <div class="bg-gray-50 rounded-lg shadow-lg p-2">
                    <div id="department-chart-{{ Str::slug($department) }}" class="h-[20rem]"></div>
                </div>
            @endforeach
        </div>

        {{-- Data Table --}}
        <div class="mt-6 bg-gray-50 rounded-lg p-4 flex items-center">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg w-full">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th scope="col" class="py-2 px-3 text-left text-xs font-semibold text-gray-600 uppercase">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($tableData as $department => $data)
                Highcharts.chart('department-chart-{{ Str::slug($department) }}', {
                    chart: {
                        type: 'pie',
                        style: {
                            fontFamily: 'Inter, sans-serif'
                        },
                        backgroundColor: {
                            linearGradient: {
                                x1: 0,
                                y1: 0,
                                x2: 1,
                                y2: 1
                            },
                            stops: [
                                [0, '#ffffff'],
                                [1, '#f8fafc']
                            ]
                        },
                        borderRadius: 8
                    },
                    title: {
                        text: '{{ $department }}',
                        style: {
                            fontSize: '18px',
                            fontWeight: 'bold',
                            color: '#1e293b'
                        }
                    },
                    subtitle: {
                        text: 'Total: {{ $data['total'] }} beneficiarios',
                        style: {
                            color: '#64748b'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        borderWidth: 0,
                        borderRadius: 8,
                        shadow: true,
                        style: {
                            fontSize: '14px'
                        },
                        pointFormat: '<span style="color:{point.color}">●</span> <b>{point.name}</b>: {point.y} ({point.percentage:.1f}%)'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            colors: ['#3b82f6', '#7dd3fc', '#ef4444', '#eab308', '#22c55e'],
                            borderRadius: 4,
                            borderWidth: 1,
                            borderColor: '#ffffff',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.percentage:.1f}%',
                                distance: -30,
                                filter: {
                                    property: 'percentage',
                                    operator: '>',
                                    value: 4
                                },
                                style: {
                                    fontSize: '12px',
                                    color: 'white',
                                    textOutline: '1px contrast',
                                    fontWeight: 'bold'
                                }
                            },
                            showInLegend: true
                        }
                    },
                    legend: {
                        align: 'center',
                        verticalAlign: 'bottom',
                        layout: 'horizontal',
                        itemStyle: {
                            fontSize: '8px',
                            fontWeight: 'normal',
                            color: '#475569'
                        },
                        itemHoverStyle: {
                            color: '#1e293b'
                        }
                    },
                    series: [{
                        name: 'Estado',
                        colorByPoint: true,
                        data: [
                            @foreach ($allStatuses as $status)
                                {
                                    name: '{{ $status }}',
                                    y: {{ $data['statuses'][$status] ?? 0 }}
                                },
                            @endforeach
                        ]
                    }],
                    credits: {
                        enabled: false
                    }
                });
            @endforeach
        });
    </script>
</x-app-layout>
