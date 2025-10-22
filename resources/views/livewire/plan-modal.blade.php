<div x-data="{ planModal: @entangle('planModal') }">
    <x-personal.button @click="planModal = true" @keydown.escape.window="planModal = false" variant="outline-primary"
        size="md" iconLeft="fa-solid fa-book">
        Ver {{ $title }}
    </x-personal.button>

    <div x-show="planModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
        <div class="fixed inset-0 transition-opacity" @click="planModal = false">
            <div class="absolute inset-0 bg-gray-500 opacity-25"></div>
        </div>

        <div class="bg-white overflow-hidden rounded-lg transform transition-all sm:max-w-7xl sm:w-full z-50">
            <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">
                        {{ $title }}
                    </h3>
                    <button @click="planModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="px-2 py-2 sm:p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                <div class="p-2" id="profile_plans">
                    <div class="overflow-x-auto">
                        <table class="w-full overflow-hidden rounded-lg">
                            <thead>
                                <tr class="text-gray-800 dark:text-gray-400 bg-gray-200 dark:bg-gray-800">
                                    <th class="px-2 py-4 text-sm font-medium text-gray-500 whitespace-nowrap"></th>
                                    <th class="px-2 py-4 text-sm font-medium text-gray-500 whitespace-nowrap">Cuota</th>
                                    <th class="px-2 py-4 text-sm font-medium text-gray-500 whitespace-nowrap">
                                        Vencimiento</th>
                                    <th class="px-2 py-4 text-sm font-medium text-gray-500 whitespace-nowrap">Capital
                                    </th>
                                    <th class="px-2 py-4 text-sm font-medium text-gray-500 whitespace-nowrap">Interés
                                    </th>
                                    <th class="px-2 py-4 text-sm font-medium text-gray-500 whitespace-nowrap">Interés
                                        Devengado</th>
                                    <th class="px-2 py-4 text-sm font-medium text-gray-500 whitespace-nowrap">Seguro
                                    </th>
                                    <th class="px-2 py-4 text-sm font-medium text-gray-500 whitespace-nowrap">Seguro
                                        Devengado</th>
                                    <th class="px-2 py-4 text-sm font-medium text-gray-500 whitespace-nowrap">Seguro
                                        Gastos/Otros</th>
                                    <th class="px-2 py-4 text-sm font-medium text-gray-500 whitespace-nowrap">Total a
                                        Pagar</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @php
                                    $plans = $beneficiary->getCurrentPlan('INACTIVO', '!=');
                                @endphp
                                @forelse ($plans as $p)
                                    @php
                                        //determine how many days are left for the payment and if it is overdue or not
                                        $today = new DateTime(date('Y-m-d'));
                                        $dueDate = new DateTime($p->fecha_ppg);
                                        $diff = $today->diff($dueDate);
                                        $days = $diff->format('%R%a');
                                    @endphp
                                    <tr class="border-b-2 px-3 py-4 p-2 h-auto divide-x-2 divide-gray-100">
                                        <td class="px-2 py-2 text-xs text-gray-500 text-center whitespace-nowrap">
                                            {{ $loop->index + 1 }}°
                                        </td>
                                        <td class="px-2 py-2 text-xs text-gray-700 text-center">
                                            <div class="flex items-center justify-center space-x-1">
                                                @if ($p->estado == 'CANCELADO')
                                                    <svg width="16px" height="16px" viewBox="-1.6 -1.6 19.20 19.20"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <circle cx="8" cy="8" r="8" fill="#a7c957">
                                                            </circle>
                                                        </g>
                                                    </svg>
                                                @else
                                                    @switch(true)
                                                        @case($days < -60)
                                                            <svg width="16px" height="16px" viewBox="-1.6 -1.6 19.20 19.20"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                    stroke-linejoin="round"></g>
                                                                <g id="SVGRepo_iconCarrier">
                                                                    <circle cx="8" cy="8" r="8" fill="#d00000">
                                                                    </circle>
                                                                </g>
                                                            </svg>
                                                        @break

                                                        @case($days < -30 && $days >= -60)
                                                            <svg width="16px" height="16px" viewBox="-1.6 -1.6 19.20 19.20"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                    stroke-linejoin="round"></g>
                                                                <g id="SVGRepo_iconCarrier">
                                                                    <circle cx="8" cy="8" r="8" fill="#ffba08">
                                                                    </circle>
                                                                </g>
                                                            </svg>
                                                        @break

                                                        @case($days < 0 && $days >= -30)
                                                            <svg width="16px" height="16px" viewBox="-1.6 -1.6 19.20 19.20"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                    stroke-linejoin="round"></g>
                                                                <g id="SVGRepo_iconCarrier">
                                                                    <circle cx="8" cy="8" r="8"
                                                                        fill="#ffba08">
                                                                    </circle>
                                                                </g>
                                                            </svg>
                                                        @break

                                                        @default
                                                            <svg width="16px" height="16px"
                                                                viewBox="-1.6 -1.6 19.20 19.20" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                                    stroke-linejoin="round"></g>
                                                                <g id="SVGRepo_iconCarrier">
                                                                    <circle cx="8" cy="8" r="8"
                                                                        fill="#a7c957">
                                                                    </circle>
                                                                </g>
                                                            </svg>
                                                    @endswitch
                                                @endif
                                                <span>
                                                    {{ $p->prppgnpag }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-2 py-2 text-xs text-gray-700 text-center whitespace-nowrap">
                                            {{ strftime('%d/%m/%Y', strtotime($p->fecha_ppg)) }}</td>
                                        <td class="px-2 py-2 text-xs text-gray-700 text-center whitespace-nowrap">
                                            {{ number_format($p->prppgcapi, 2) }}</td>
                                        <td class="px-2 py-2 text-xs text-gray-700 text-center whitespace-nowrap">
                                            {{ number_format($p->prppginte, 2) }}</td>
                                        <td class="px-2 py-2 text-xs text-gray-700 text-center whitespace-nowrap">
                                            {{ number_format($p->prppggral, 2) }}</td>
                                        <td class="px-2 py-2 text-xs text-gray-700 text-center whitespace-nowrap">
                                            {{ number_format($p->prppgsegu, 2) }}</td>
                                        <td class="px-2 py-2 text-xs text-gray-700 text-center whitespace-nowrap">
                                            {{ number_format($p->prppgcarg, 2) }}</td>
                                        <td class="px-2 py-2 text-xs text-gray-700 text-center whitespace-nowrap">
                                            {{ number_format($p->prppgotro, 2) }}</td>
                                        <td class="px-2 py-2 text-xs text-gray-700 text-center whitespace-nowrap">
                                            {{ number_format($p->prppgtota, 2) }}</td>
                                        <td class="px-2 py-2 text-gray-700 text-center text-sm">
                                            @if ($p->estado == 'CANCELADO')
                                                Pagada el: <br /> {{ $p->updated_at }}
                                            @else
                                                {{ $days < 0 ? 'Vencido ' . abs($days) . ' días' : ($days == 1 ? 'Cuota vence hoy.' : $days . ' días restantes') }}
                                            @endif
                                            <br />
                                            <span class="text-xs italic">
                                                ({{ $p->estado }})
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td class="px-4 py-3 text-gray-700 text-center" colspan="7">
                                                No hay planes de pago registrados
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-100 px-4 py-3 items-center justify-between lg:flex sm:px-6 sm:flex border-t border-gray-200">
                    <div class="flex items-center space-x-1 text-sm text-gray-700">
                        <span class="font-medium p-2 border-2 border-gray-700 rounded-md">Capital: <span
                                class="font-semibold text-gray-900">{{ number_format($plans->sum('prppgcapi'), 2) }}</span>
                            @if ($beneficiary->helpers()->exists())
                                <span class="text-gray-500">(+
                                    {{ number_format($beneficiary->helpers()->sum('capital'), 2) }})</span>
                            @endif
                        </span>
                        <span class="font-medium p-2 border-2 border-gray-700 rounded-md">Intereses: <span
                                class="font-semibold text-gray-900">{{ number_format($plans->sum('prppginte'), 2) }}</span>
                            @if ($beneficiary->helpers()->exists())
                                <span class="text-gray-500">(+
                                    {{ number_format($beneficiary->helpers()->sum('interes'), 2) }})</span>
                            @endif
                        </span>
                        <span class="font-medium p-2 border-2 border-gray-700 rounded-md">Interés Devengado: <span
                                class="font-semibold text-gray-900">{{ number_format($plans->sum('prppggral'), 2) }}</span></span>
                        <span class="font-medium p-2 border-2 border-gray-700 rounded-md">Seguros: <span
                                class="font-semibold text-gray-900">{{ number_format($plans->sum('prppgsegu'), 2) }}</span></span>
                        <span class="font-medium p-2 border-2 border-gray-700 rounded-md">Seguro Devengado: <span
                                class="font-semibold text-gray-900">{{ number_format($plans->sum('prppgcarg'), 2) }}</span></span>
                                <span class="font-medium p-2 border-2 border-gray-700 rounded-md">Gastos / Otros: <span
                                class="font-semibold text-gray-900">{{ number_format($plans->sum('prppgotro'), 2) }}</span></span>
                        <span class="font-medium p-2 border-2 border-gray-700 rounded-md">Total: <span
                                class="font-semibold text-gray-900">{{ number_format($plans->sum('prppgtota'), 2) }}</span></span>
                    </div>
                    <a target="_blank" href="{{ route('beneficiario.pdf', ['cedula' => $beneficiary->ci]) }}"
                        class="border mr-2 px-2 py-1 rounded-md relative cursor-pointer text-green-600">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span class="text-xs">
                            Ver PDF
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
