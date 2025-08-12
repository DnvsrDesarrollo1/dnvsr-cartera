<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Hola
                {{ substr(Auth::user()->name, 0, strpos(Auth::user()->name, ' ')) }}!</h1>
            <p class="text-gray-500">
                A continuación se muestra una lista de los proyectos en los que estás a cargo.
            </p>
            <p class="text-gray-500 text-sm">
                Puedes ver los beneficiarios de cada proyecto con el boton -> <i
                    class="fa-solid fa-eye p-2 rounded-lg border border-gray-500"></i>
                </variant=>
            </p>
        </div>
    </x-slot>
    <div class="overflow-x-auto bg-white rounded-lg shadow m-2 w-2/3">
        <div class="min-w-full bg-white rounded-lg shadow-sm">
            @php
                $projects = Auth::user()->hasrole('admin') ? \App\Models\Project::all() : Auth::user()->projects;
            @endphp
            @foreach ($projects as $project)
                @php
                    $project_beneficiaries = $project->beneficiaries;
                    $estados = $project_beneficiaries->unique('estado')->sortBy('estado');

                    // Prepare data for Highcharts
                    $chartData = [];
                    foreach ($estados as $estado) {
                        $count = $project_beneficiaries->where('estado', $estado->estado)->count();
                        $color = match (strtolower($estado->estado)) {
                            'vigente' => '#84cc16',
                            'vencido' => '#eab308',
                            'ejecucion' => '#ef4444',
                            'bloqueado' => '#3b82f6',
                            'cancelado' => '#6366f1',
                            default => '#3b82f6',
                        };
                        $chartData[] = [
                            'name' => $estado->estado,
                            'y' => $count,
                            'color' => $color,
                        ];
                    }
                @endphp

                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 bg-gray-200 px-4 rounded-md w-full">
                            {{ $project->nombre_proyecto }}
                        </h3>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div id="chart-{{ $project->id }}" class="h-96 col-span-2"></div>
                        <div class="space-y-3">
                            @foreach ($estados as $estado)
                                @php
                                    $count = $project_beneficiaries->where('estado', $estado->estado)->count();
                                    $totalBeneficiaries = $project_beneficiaries->count();
                                    $percentage = $totalBeneficiaries > 0 ? ($count / $totalBeneficiaries) * 100 : 0;

                                    $statusColor = match (strtolower($estado->estado)) {
                                        'vigente' => 'bg-green-100 text-green-800',
                                        'vencido' => 'bg-yellow-100 text-yellow-800',
                                        'ejecucion' => 'bg-red-100 text-red-800',
                                        'bloqueado' => 'bg-blue-100 text-blue-800',
                                        'cancelado' => 'bg-indigo-100 text-indigo-800',
                                        default => 'bg-blue-100 text-blue-800',
                                    };
                                @endphp

                                <div class="flex items-center justify-between">
                                    <div class="grid grid-cols-2 gap-2">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                            {{ $estado->estado }}
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            {{ $count }} -> {{ number_format($percentage, 1) }}%
                                        </span>
                                    </div>
                                    <div class="flex items-end">
                                        <livewire:projects.beneficiaries-by-status :project="$project" :status-filter="$estado->estado"
                                            lazy />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Highcharts.chart('chart-{{ $project->id }}', {
                            chart: {
                                type: 'pie',
                                style: {
                                    fontFamily: 'Inter, sans-serif'
                                }
                            },
                            title: {
                                text: null,
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: true,
                                        format: '{point.percentage:.1f}%'
                                    }
                                }
                            },
                            series: [{
                                name: 'Beneficiarios',
                                data: {!! json_encode($chartData) !!}
                            }],
                            credits: {
                                enabled: false
                            }
                        });
                    });
                </script>
            @endforeach
        </div>
    </div>
</x-app-layout>
