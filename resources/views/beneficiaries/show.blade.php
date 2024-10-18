<x-app-layout>
    <div class="mt-4 overflow-x-scroll">
        <div class="grid sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-2 w-full px-4">
            <div class="bg-white shadow-lg rounded-lg p-6 mb-4 h-fit" id="profile_preview">
                <div class="flex items-center">
                    <svg width="105px" height="105px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path
                                d="M14 19.2857L15.8 21L20 17M16.5 14.4018C16.2052 14.2315 15.8784 14.1098 15.5303 14.0472C15.4548 14.0337 15.3748 14.024 15.2842 14.0171C15.059 14 14.9464 13.9915 14.7961 14.0027C14.6399 14.0143 14.5527 14.0297 14.4019 14.0723C14.2569 14.1132 13.9957 14.2315 13.4732 14.4682C12.7191 14.8098 11.8817 15 11 15C10.1183 15 9.28093 14.8098 8.52682 14.4682C8.00429 14.2315 7.74302 14.1131 7.59797 14.0722C7.4472 14.0297 7.35983 14.0143 7.20361 14.0026C7.05331 13.9914 6.94079 14 6.71575 14.0172C6.6237 14.0242 6.5425 14.0341 6.46558 14.048C5.23442 14.2709 4.27087 15.2344 4.04798 16.4656C4 16.7306 4 17.0485 4 17.6841V19.4C4 19.9601 4 20.2401 4.10899 20.454C4.20487 20.6422 4.35785 20.7951 4.54601 20.891C4.75992 21 5.03995 21 5.6 21H10.5M15 7C15 9.20914 13.2091 11 11 11C8.79086 11 7 9.20914 7 7C7 4.79086 8.79086 3 11 3C13.2091 3 15 4.79086 15 7Z"
                                stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </g>
                    </svg>
                    <div class="ml-4">
                        <p class="text-lg font-semibold text-gray-800">{{ $beneficiary->nombre }} -
                            {{ $beneficiary->ci . " ($beneficiary->complemento) " . $beneficiary->expedido }}</p>
                        <p class="text-gray-600">
                            <i>
                                COD.CREDITO: {{ $beneficiary->idepro }}
                            </i>
                        </p>
                    </div>
                </div>
                <hr>
                <div class="flex justify-between mt-4">
                    <div>
                        <p class="text-gray-600">Estado de Credito</p>
                        <p class="text-gray-800">{{ $beneficiary->estado }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Proyecto</p>
                        <p class="text-gray-800">{{ $beneficiary->proyecto }}</p>
                    </div>
                </div>
                <div class="flex justify-between mt-4">
                    <div>
                        <p class="text-gray-600">Total Activado</p>
                        <p class="text-gray-800">{{ 'Bs. ' . number_format($beneficiary->total_activado, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Fecha de Activacion</p>
                        <p class="text-gray-800">{{ $beneficiary->fecha_activacion }}</p>
                    </div>
                </div>
                <div class="flex justify-between mt-4">
                    <div>
                        <p class="text-gray-600">Monto Recuperado (Segun Cartera)</p>
                        <p class="text-gray-800">{{ 'Bs. ' . number_format($beneficiary->monto_recuperado, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Departamento</p>
                        <p class="text-gray-800">{{ $beneficiary->departamento }}</p>
                    </div>
                </div>
                <div class="flex justify-between mt-4">
                    <div>
                        <p class="text-gray-600">Total en Pagos</p>
                        <p class="text-gray-800">
                            {{ 'Bs. ' . number_format($totalVouchers * -1, 2) }}
                        </p>
                    </div>
                </div>
                <hr class="mt-4">
                <div class="flex justify-between mt-4">
                    <p class=@if ($beneficiary->estado == 'CANCELADO') "text-gray-400" @else "text-gray-600" @endif>
                        Generador de Planes:</p>
                    <p class=@if ($beneficiary->estado == 'CANCELADO') "text-gray-400" @else "text-gray-800" @endif>
                        Permite realizar una nueva serie (plan) de pagos en base a los <b>meses especificados</b> y
                        el <b>saldo a capital.</b>
                    </p>
                </div>
                <div x-data="{ show: false }">
                    @if ($beneficiary->estado != 'CANCELADO')
                        <button @click="show = !show">
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
                    <div class="bg-white rounded-lg p-6" x-show="show" x-transition>
                        <form action="{{ route('plan.reajuste') }}" method="post">
                            @csrf
                            <input type="hidden" name="idepro" value="{{ $beneficiary->idepro }}" />
                            <input type="hidden" name="plazo_credito" value="{{ $beneficiary->plazo_credito }}" />
                            <div class="mb-4">
                                <label for="capital_inicial" class="block text-gray-700 font-bold mb-2">
                                    Capital Inicial:
                                </label>
                                <input type="text" inputmode="decimal" id="capital_inicial" name="capital_inicial"
                                    placeholder="Ej: 25000.75" pattern="[0-9]*[.,]?[0-9]*"
                                    class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                    required value="{{ $beneficiary->total_activado + $totalVouchers }}"
                                    title="Saldo restante de (Monto Activado menos Monto en Cuotas).">
                            </div>
                            <div class="mb-4">
                                <label for="meses" class="block text-gray-700 font-bold mb-2">
                                    Meses restantes:
                                </label>
                                <input type="text" id="meses" name="meses" placeholder="Ej: 10"
                                    class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                    required value="{{ $mesesRestantes }}"
                                    title="Meses restantes desde hoy, a la fecha de activacion (+ 20 años).">
                            </div>
                            <div class="mb-4">
                                <label for="taza_interes" class="block text-gray-700 font-bold mb-2">
                                    Interes:
                                </label>
                                <input type="number" id="taza_interes" name="taza_interes"
                                    placeholder="Ej: 13 (no es necesario agregar simbolo %)"
                                    class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                    required value="3" title="Taza por defecto 3%.">
                                <span>
                                </span>
                            </div>
                            <div class="mb-4">
                                <div class="block mt-4">
                                    <label for="correlativo" class="flex items-center text-gray-700 font-bold mb-2"
                                        title="Desactivado: el n# de cuota empezará desde el 1 para adelante, de lo contrario, desde la ultima cuota correspondiente a los meses restantes.">
                                        <x-checkbox checked id="correlativo" name="correlativo" />
                                        <span class="ms-2 text-gray-600 dark:text-gray-400">Mantener
                                            correlatividad</span>
                                    </label>
                                    <label for="fecha_inicio" class="block text-gray-700 font-bold mb-2">
                                        Fecha de inicio:
                                    </label>
                                    <input type="date" id="fecha_inicio" name="fecha_inicio"
                                        class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                        required value="<?php echo date('Y-m-d'); ?>" title="Fecha actual por defecto." />
                                </div>
                            </div>
                            <hr class="mt-4 mb-4">
                            <div class="mb-4">
                                <label for="taza_interes" class="block text-gray-700 font-bold mb-2">
                                    Diferimiento de cobro (de no existir, dejar todos los campos vacios):
                                </label>
                                <input type="number" id="diff_cuotas" name="diff_cuotas" placeholder="Ej: 10"
                                    class="mt-2 appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                    title="Cantidad de cuotas adicionales." />
                                <input type="text" id="diff_capital" name="diff_capital"
                                    placeholder="Ej: 3500.75" pattern="[0-9]*[.,]?[0-9]*" inputmode="decimal"
                                    class="mt-2 appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                    title="Monto del diferimiento." />
                                <input type="text" id="diff_interes" name="diff_interes"
                                    placeholder="Ej: 1200.50" pattern="[0-9]*[.,]?[0-9]*" inputmode="decimal"
                                    class="mt-2 appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                    title="Interes del diferimiento." />
                            </div>
                            <div class="flex justify-end mt-4">
                                <button type="submit" class="mt-4">
                                    <span>
                                        <svg fill="#000000" height="75px" width="75px" version="1.1"
                                            id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 290 290"
                                            xml:space="preserve">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                stroke-linejoin="round"></g>
                                            <g id="SVGRepo_iconCarrier">
                                                <g>
                                                    <path id="circle34669"
                                                        d="M165.862,46.682c-0.688-0.009-1.376-0.009-2.064,0.002c-13.223,0.207-26.518,4.065-38.291,11.84 c-5.703,3.645-0.079,12.159,5.512,8.344c27.088-17.889,63.28-11.773,82.986,14.023c19.706,25.797,16.09,62.324-8.293,83.754 c-24.383,21.43-61.073,20.33-84.127-2.525c-4.735-4.798-11.88,2.408-7.041,7.102c23.295,23.094,58.803,27.024,86.25,11.193 l53.785,61.211c1.906,2.169,5.248,2.278,7.291,0.236l26.666-26.666c2.016-2.016,1.941-5.306-0.164-7.229l-60.518-55.236 c13.22-24.202,11.725-54.835-5.902-77.91C208.123,56.718,187.193,46.961,165.862,46.682z M162.426,75.754 c-2.76,0.041-4.965,2.31-4.926,5.07v47.5h-7.5v-6.666c0.04-2.818-2.256-5.112-5.074-5.07c-2.76,0.041-4.965,2.31-4.926,5.07v6.666 h-7.5V92.492c0.04-2.818-2.256-5.112-5.074-5.07c-2.76,0.041-4.965,2.31-4.926,5.07v35.832h-7.5v-18.332 c0.04-2.818-2.256-5.112-5.074-5.07c-2.76,0.041-4.965,2.31-4.926,5.07v18.332h-7.5v-0.832c0.04-2.818-2.256-5.112-5.074-5.07 c-2.76,0.041-4.965,2.31-4.926,5.07v0.832h-25v-24.166c0.04-2.818-2.256-5.112-5.074-5.07c-2.76,0.041-4.965,2.31-4.926,5.07 v24.166h-25V86.658c0.04-2.818-2.256-5.112-5.074-5.07c-2.76,0.041-4.965,2.31-4.926,5.07v41.666h-7.5v-30 c0.04-2.818-2.256-5.112-5.074-5.07c-2.76,0.041-4.965,2.31-4.926,5.07v35c0,2.761,2.239,5,5,5h30v12.5 c-0.096,6.762,10.096,6.762,10,0v-12.5h25v12.5c-0.096,6.762,10.096,6.762,10,0v-12.5h100c2.761,0,5-2.239,5-5v-17.5 c0.096-6.762-10.096-6.762-10,0v12.5h-7.5v-47.5C167.54,78.006,165.244,75.712,162.426,75.754z M215.223,115.789 c-1.379,0.021-2.48,1.156-2.461,2.535c0,26.407-21.355,47.762-47.762,47.762c-3.381-0.048-3.381,5.048,0,5 c29.109,0,52.762-23.652,52.762-52.762C217.782,116.915,216.633,115.767,215.223,115.789z M222.368,161.264l43.764,39.943 l-18.584,17.27l-38.402-43.707c1.073-0.84,2.133-1.704,3.168-2.613C216.105,168.824,219.449,165.162,222.368,161.264z M269.838,204.59l7.926,7.234l-19.195,19.195l-7.719-8.785L269.838,204.59z">
                                                    </path>
                                                </g>
                                            </g>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow-lg rounded-lg p-4" id="profile_payments">
                <table class="w-full overflow-hidden rounded-lg dark:divide-gray-700">
                    <thead>
                        <tr class="text-gray-800 dark:text-gray-400 bg-gray-200 dark:bg-gray-800"">
                            <th class="px-4 py-4 font-medium text-gray-500 whitespace-nowrap">
                                N° Cuota
                            </th>
                            <th>Comprobante BUSA</th>
                            <th>Codigo Prestamo</th>
                            <th>Fecha Pago</th>
                            <th>Hora Pago</th>
                            <th>Descripcion</th>
                            <th>Monto</th>
                            <th>Operaciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($vouchers as $v)
                            <tr class="border-b-2 px-3 py-4 text-sm p-2 h-auto">
                                <td class="px-4 py-4 text-sm font-medium text-gray-700 whitespace-nowrap">
                                    {{ $v->numpago }}
                                </td>
                                <td>{{ $v->numtramite }}</td>
                                <td>{{ $v->numprestamo }}</td>
                                <td>{{ $v->fecha_pago }}</td>
                                <td>{{ $v->hora_pago }}</td>
                                <td>{{ $v->descripcion }}</td>
                                <td>{{ 'Bs. ' . number_format($v->montopago, 2) }}</td>
                                <td class="h-auto flex flex-row justify-center py-2">
                                    <x-dropdown align="right" width="40">
                                        <x-slot name="trigger">
                                            <span class="inline-flex rounded-md">
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-full text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                                    <svg width="24px" height="24px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <rect width="24" height="24" fill="transparent">
                                                            </rect>
                                                            <circle cx="12" cy="7" r="0.5"
                                                                transform="rotate(90 12 7)" stroke="#000000"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                            </circle>
                                                            <circle cx="12" cy="12" r="0.5"
                                                                transform="rotate(90 12 12)" stroke="#000000"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                            </circle>
                                                            <circle cx="12" cy="17" r="0.5"
                                                                transform="rotate(90 12 17)" stroke="#000000"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                            </circle>
                                                        </g>
                                                    </svg>
                                                </button>
                                            </span>
                                        </x-slot>
                                        <x-slot name="content">
                                            @foreach ($v->payments as $p)
                                                <x-dropdown-link>
                                                    <span class="cursor-pointer text-xs">
                                                        {{ $p->prtdtdesc }}
                                                    </span>
                                                    <span class="cursor-pointer text-xs font-bold">
                                                        {{ number_format($p->montopago * -1, 2) }}
                                                    </span>
                                                </x-dropdown-link>
                                            @endforeach
                                        </x-slot>
                                    </x-dropdown>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-lg text-gray-500 bg-gray-100">
                            <th colspan="12">
                                {!! $vouchers->render() !!}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="bg-white shadow-lg rounded-lg p-6 m-4" id="profile_plans">
            <table class="w-full overflow-hidden rounded-lg dark:divide-gray-700">
                <thead>
                    <tr class="text-gray-800 dark:text-gray-400 bg-gray-200 dark:bg-gray-800">
                        <th class="px-4 py-4 font-medium text-gray-500 whitespace-nowrap">
                            #
                        </th>
                        <th>Cuota</th>
                        <th>
                            <span class="block">
                                Capital
                            </span>
                            {{-- <span class="block text-green-600">
                                ({{ number_format($plans->sum('prppgcapi'), 2) }})
                            </span> --}}
                        </th>
                        <th>
                            <span class="block">
                                Interés
                            </span>
                            {{-- <hr>
                            <span class="block text-green-600">
                                ({{ number_format($plans->sum('prppginte'), 2) }})
                            </span> --}}
                        </th>
                        <th>
                            <span class="block">
                                Seguro Desgravamen
                            </span>
                            {{-- <hr>
                            <span class="block text-green-600">
                                ({{ number_format($plans->sum('prppgsegu'), 2) }})
                            </span> --}}
                        </th>
                        <th>
                            <span class="block">
                                Total a Pagar
                            </span>
                            {{-- <hr>
                            <span class="block text-green-600">
                                ({{ number_format($plans->sum('prppgtota'), 2) }})
                            </span> --}}
                        </th>
                        <th>Vencimiento</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($plans as $p)
                        <tr class="border-b-2 px-3 py-4 text-center text-sm p-2 h-auto">
                            <td class="px-4 py-4 text-sm font-medium text-gray-700 whitespace-nowrap">
                                {{ $loop->index + 1 }}
                            </td>
                            <td>{{ $p->prppgnpag }}</td>
                            <td>{{ number_format($p->prppgcapi, 2) }}</td>
                            <td>{{ number_format($p->prppginte, 2) }}</td>
                            <td>{{ number_format($p->prppgsegu, 2) }}</td>
                            <td>{{ number_format($p->prppgtota, 2) }}</td>
                            <td>{{ $p->fecha_ppg }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-lg text-gray-500 bg-gray-100">
                        <th colspan="12">
                            {!! $plans->render() !!}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
