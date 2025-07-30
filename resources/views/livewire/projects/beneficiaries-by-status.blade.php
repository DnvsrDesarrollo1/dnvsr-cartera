<div x-data="{ projectBeneficiaries: @entangle('projectBeneficiaries') }">
    <x-personal.button @click="projectBeneficiaries = true" @keydown.escape.window="projectBeneficiaries = false"
        variant="outline-secondary" size="xs" iconCenter="fa-solid fa-eye">
    </x-personal.button>

    <div x-show="projectBeneficiaries" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
        <div class="fixed inset-0 transition-opacity" @click="projectBeneficiaries = false">
            <div class="absolute inset-0 bg-gray-500 opacity-50"></div>
        </div>

        <div class="bg-white overflow-hidden rounded-lg transform transition-all sm:max-w-4xl sm:w-full m-4">
            <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">
                        {{ $project->nombre_proyecto }}
                    </h3>
                    <span class="text-xl font-semibold text-gray-900">
                        LISTA EN {{ $statusFilter }}
                    </span>
                    <button @click="projectBeneficiaries = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="px-2 py-2 sm:p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                <div class="px-4 border-l-4 border-gray-800" id="profile_plans">
                    <div class="overflow-x-auto">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nombre</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            CI</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            MORA A HOY</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($beneficiaries as $beneficiary)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-blue-600">
                                                <a href="{{ route('beneficiario.show', $beneficiary->ci) }}"
                                                    target="_blank">
                                                    {{ $beneficiary->nombre }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $beneficiary->ci }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $vencs = $beneficiary
                                                        ->getCurrentPlan('CANCELADO', '!=');
                                                    $total = 0;
                                                    if ($vencs->count() > 0) {
                                                        $fechaInicio = \Carbon\Carbon::parse(
                                                            $vencs->first()->fecha_ppg,
                                                        );
                                                        $fechaFin = now();
                                                        $diff = $fechaInicio->diffInDays($fechaFin);
                                                        $total = $diff;
                                                        echo "
                                                <span class=\"w-full bg-white rounded-md text-center p-1\">
                                                    <b>" .
                                                            number_format($total <= 0 ? 0 : $total, 0) .
                                                            "</b>
                                                </span>
                                            ";
                                                    }
                                                @endphp
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-gray-100 px-4 py-3 items-center justify-between lg:flex sm:px-6 sm:flex border-t border-gray-200">
                <x-personal.button @click="projectBeneficiaries = false" variant="danger" iconLeft="fa-solid fa-xmark">
                    Cerrar
                </x-personal.button>
            </div>
        </div>
    </div>
</div>
