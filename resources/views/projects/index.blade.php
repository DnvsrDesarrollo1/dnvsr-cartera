<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Hola {{ substr(Auth::user()->name, 0, strpos(Auth::user()->name, ' ')) }}!</h1>
            <p class="text-gray-500">
                A continuación se muestra una lista de los proyectos en los que estás a cargo.
            </p>
            <p class="text-gray-500">
                Puedes ver los beneficiarios de cada proyecto con el boton ->  <i class="fa-solid fa-eye p-2 rounded-lg border border-gray-500"></i>
                </variant=>
            </p>
        </div>
    </x-slot>
    <div class="overflow-x-auto bg-white rounded-lg shadow m-2 w-1/2">
        <table class="min-w-full divide-y divide-gray-200 rounded-md overflow-hidden border-2 border-white">
            <thead class="bg-gray-500">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                        Proyecto
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                        Estados
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach (Auth::user()->projects as $project)
                    @php
                        $project_beneficiaries = $project->beneficiaries;
                        $estados = $project_beneficiaries->unique('estado')->sortBy('estado');
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 max-w-fit">
                            {{ $project->nombre_proyecto }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="space-y-2">
                                @foreach ($estados as $estado)
                                    @php
                                        $statusColor = match (strtolower($estado->estado)) {
                                            'vigente' => 'bg-green-100 text-green-800',
                                            'vencido' => 'bg-yellow-100 text-yellow-800',
                                            'ejecucion' => 'bg-red-100 text-red-800',
                                            'bloqueado' => 'bg-blue-100 text-blue-800',
                                            'cancelado' => 'bg-indigo-100 text-indigo-800',
                                            default => 'bg-blue-100 text-blue-800',
                                        };

                                        $count = $project_beneficiaries->where('estado', $estado->estado)->count();
                                        $totalBeneficiaries = $project_beneficiaries->count();
                                        $percentage =
                                            $totalBeneficiaries > 0 ? ($count / $totalBeneficiaries) * 100 : 0;
                                    @endphp

                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="flex-1 col-span-2">
                                            <div class="flex items-center justify-between mb-1">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                                    {{ $estado->estado }}
                                                </span>
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ $count }} ({{ number_format($percentage, 1) }}%)
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full {{ str_replace('bg-', 'bg-', str_replace('100', '600', $statusColor)) }}"
                                                    style="width: {{ $percentage }}%">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-center">
                                            <livewire:projects.beneficiaries-by-status :project="$project"
                                                :status-filter="$estado->estado" lazy />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
