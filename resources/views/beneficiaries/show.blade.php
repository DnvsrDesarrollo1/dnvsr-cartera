<x-app-layout>
    <div class="mt-4">
        <div class="w-full px-4 grid sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-2">
            <div class="bg-white shadow-lg rounded-lg p-6 mb-4 h-fit" id="profile_preview">
                <div class="flex items-center justify-center mb-2">
                    @if ($beneficiary->genero == 'MA')
                        <svg fill="#000000" width="64px" height="64px" viewBox="-3.2 -3.2 38.40 38.40" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="0.00032">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"
                                stroke="#CCCCCC" stroke-width="3.008"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path
                                    d="M15.992 2c3.396 0 6.998 2.86 6.998 4.995v4.997c0 1.924-0.8 5.604-2.945 7.293-0.547 0.43-0.831 1.115-0.749 1.807 0.082 0.692 0.518 1.291 1.151 1.582l8.703 4.127c0.068 0.031 0.834 0.16 0.834 1.23l0.001 1.952-27.984 0.002v-2.029c0-0.795 0.596-1.045 0.835-1.154l8.782-4.145c0.63-0.289 1.065-0.885 1.149-1.573s-0.193-1.37-0.733-1.803c-2.078-1.668-3.046-5.335-3.046-7.287v-4.997c0.001-2.089 3.638-4.995 7.004-4.995zM15.992-0c-4.416 0-9.004 3.686-9.004 6.996v4.997c0 2.184 0.997 6.601 3.793 8.847l-8.783 4.145s-1.998 0.89-1.998 1.999v3.001c0 1.105 0.895 1.999 1.998 1.999h27.986c1.105 0 1.999-0.895 1.999-1.999v-3.001c0-1.175-1.999-1.999-1.999-1.999l-8.703-4.127c2.77-2.18 3.708-6.464 3.708-8.865v-4.997c0-3.31-4.582-6.995-8.998-6.995v0z">
                                </path>
                            </g>
                        </svg>
                    @else
                        <svg fill="#000000" width="64px" height="64px" viewBox="-3.2 -3.2 38.40 38.40" version="1.1"
                            xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path
                                    d="M30.001 25.084l-8.703-4.127c1.161-0.582 5.695-0.767 6.070-1.79 0 0-1.792-2.75-2.229-6.323-0.17-1.386-0.461-3.206-0.75-5.769-0.469-4.157-3.965-7.075-8.381-7.075h-0.016c-4.416 0-7.912 2.919-8.38 7.075-0.289 2.563-0.58 4.382-0.75 5.769-0.438 3.573-2.229 6.323-2.229 6.323 0.375 1.023 4.909 1.208 6.071 1.79l-8.704 4.128s-1.999 0.702-1.999 2.358v2.642c0 1.105 0.894 1.916 1.999 1.916h28.002c1.105 0 1.999-0.811 1.999-1.916v-2.642c0-1.657-1.999-2.358-1.999-2.358zM2 30v-2.558c0-0.107 0.378-0.363 0.685-0.48 0.067-0.023 0.107-0.042 0.17-0.072l8.703-4.127c0.691-0.327 1.135-1.021 1.144-1.786s-0.42-1.468-1.104-1.81c-0.678-0.34-1.573-0.508-2.976-0.751-0.333-0.058-0.788-0.14-1.229-0.229 0.572-1.285 1.205-3.081 1.454-5.114 0.062-0.506 0.14-1.075 0.229-1.706 0.152-1.073 0.339-2.434 0.524-4.069 0.349-3.090 2.977-5.299 6.393-5.299h0.016c3.416 0 6.045 2.209 6.393 5.299 0.184 1.635 0.372 2.997 0.523 4.069 0.088 0.63 0.167 1.2 0.229 1.706 0.249 2.032 0.882 3.829 1.454 5.114-0.442 0.088-0.896 0.17-1.23 0.229-1.404 0.243-2.299 0.411-2.977 0.751-0.683 0.343-1.111 1.046-1.104 1.811 0.009 0.764 0.452 1.459 1.143 1.786l8.703 4.127c0.063 0.030 0.104 0.049 0.17 0.072 0.308 0.117 0.64 0.373 0.686 0.48l0.001 2.557h-28.001z">
                                </path>
                            </g>
                        </svg>
                    @endif
                    <div class="ml-4">
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $beneficiary->nombre }} - {{ $beneficiary->ci }} {{$beneficiary->complemento}} {{ $beneficiary->expedido }}
                        </p>
                        <p class="text-gray-600">
                            <i>
                                COD.CREDITO: {{ $beneficiary->idepro }}
                            </i>
                        </p>
                    </div>
                </div>
                <hr>
                <div class="mt-6 grid grid-cols-2 gap-2">
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Estado de Crédito</h3>
                        <p class="font-bold {{ $beneficiary->estado == 'CANCELADO' ? 'text-green-500' : '' }}">
                            {{ $beneficiary->estado }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Proyecto</h3>
                        <p class="font-bold">{{ $beneficiary->proyecto }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Departamento</h3>
                        <p class="font-bold">{{ $beneficiary->departamento }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Fecha de Activación</h3>
                        <p class="font-bold">{{ $beneficiary->fecha_activacion }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Total Activado</h3>
                        <p class="font-bold text-sky-800">Bs.
                            {{ number_format($beneficiary->total_activado, 2) }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Monto Recuperado (Según Cartera)</h3>
                        <p class="font-bold text-sky-800">Bs.
                            {{ number_format($beneficiary->monto_recuperado, 2) }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 shadow">
                        <h3 class="font-semibold text-gray-700 mb-2">Total en Pagos</h3>
                        <p class="font-bold text-sky-800">Bs.
                            {{ number_format($beneficiary->payments()->where('prtdtdesc', 'like', '%CAPI%')->sum('montopago'), 2) }}
                        </p>
                    </div>
                    <div class="bg-gray-100 border rounded-lg shadow-md p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                @if ($beneficiary->plans()->count() > 0)
                                    <livewire:plan-modal lazy :beneficiary="$beneficiary" title="Plan de pagos vigente" />
                                @else
                                    <p class="text-gray-500 italic">Sin plan de pagos registrado</p>
                                @endif
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                @if ($beneficiary->payments()->count() > 0)
                                    <livewire:payment-modal lazy :beneficiary="$beneficiary"
                                        title="Historial de pagos y glosas" />
                                @else
                                    <p class="text-gray-500 italic">Sin historial de pagos registrado</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-100 p-4 rounded-lg shadow" id="profile_management">
                <div class="bg-white p-4 rounded-lg flex justify-between mt-4">
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
                        <button @click="show = !show" class="rounded-full mt-2 overflow-hidden">
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
                            <input type="hidden" name="gastos_judiciales"
                                value="{{ $beneficiary->gastos_judiciales }}" />
                            <div class="mb-4">
                                <label for="capital_inicial" class="block text-gray-700 font-bold mb-2">
                                    Capital Inicial:
                                </label>
                                <input type="text" inputmode="decimal" id="capital_inicial"
                                    name="capital_inicial" placeholder="Ej: 25000.75" pattern="[0-9]*[.,]?[0-9]*"
                                    class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                    required
                                    value="{{ $beneficiary->total_activado - $beneficiary->payments()->where('prtdtdesc', 'like', '%CAPI%')->sum('montopago') }}"
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
                            <div class="mb-4 grid grid-cols-2 gap-2">
                                <div>
                                    <label for="taza_interes" class="block text-gray-700 font-bold mb-2">
                                        Interes:
                                    </label>
                                    <input type="text" inputmode="decimal" name="taza_interes"
                                        placeholder="Ej: 13 (no es necesario agregar simbolo %)"
                                        pattern="[0-9]*[.,]?[0-9]*"
                                        class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                        required value="3" title="Taza por defecto 3%.">
                                </div>
                                <div>
                                    <label for="seguro" class="block text-gray-700 font-bold mb-2">
                                        Seguro:
                                    </label>
                                    <input type="text" inputmode="decimal" name="seguro"
                                        placeholder="Ej: 13 (no es necesario agregar simbolo %)"
                                        pattern="[0-9]*[.,]?[0-9]*"
                                        class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:bg-white"
                                        required value="3" title="Seguro por defecto 0.04%.">
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="block mt-4">
                                    <label for="correlativo"
                                        class="bg-gray-100 flex items-center text-gray-700 font-bold mb-2 p-4 border rounded-md cursor-pointer"
                                        title="Desactivado: el n# de cuota empezará desde el 1 para adelante, de lo contrario, desde la ultima cuota correspondiente a los meses restantes.">
                                        <x-checkbox checked id="correlativo" name="correlativo" />
                                        <span class="ms-2 text-gray-600 dark:text-gray-400">
                                            Reajuste (marcado) / Activacion (desmarcado)
                                        </span>
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
                                <button type="submit"
                                    class="border mt-2 px-2 py-1 rounded-md cursor-pointer shadow-md"
                                    title="Calcular plan de pagos">
                                    <span>
                                        <svg fill="#0c272a" version="1.1" id="Layer_1"
                                            xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="64px" height="64px"
                                            viewBox="-7 -7 84.00 84.00" enable-background="new 0 0 70 70"
                                            xml:space="preserve" stroke="#000000" stroke-width="0.0007"
                                            transform="rotate(0)matrix(1, 0, 0, 1, 0, 0)">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                stroke-linejoin="round" stroke="#CCCCCC" stroke-width="1.26"></g>
                                            <g id="SVGRepo_iconCarrier">
                                                <g>
                                                    <path
                                                        d="M59.427,2.583h-48c-2.209,0-4.844,2.305-4.844,4.514v56c0,2.209,2.635,3.486,4.844,3.486h48 c2.209,0,3.156-1.277,3.156-3.486v-56C62.583,4.888,61.636,2.583,59.427,2.583z M58.583,62.583h-48v-56h48V62.583z">
                                                    </path>
                                                    <path
                                                        d="M54.583,11.583c0-0.552-0.447-1-1-1h-39c-0.552,0-1,0.448-1,1v12c0,0.552,0.448,1,1,1h39c0.553,0,1-0.448,1-1V11.583z M15.583,12.583h37v10h-37V12.583z">
                                                    </path>
                                                    <path
                                                        d="M21.583,28.583c0-0.552-0.448-1-1-1h-6c-0.552,0-1,0.448-1,1v4c0,0.552,0.448,1,1,1h6c0.552,0,1-0.448,1-1V28.583z M15.583,29.583h4v2h-4V29.583z">
                                                    </path>
                                                    <path
                                                        d="M32.583,28.583c0-0.552-0.448-1-1-1h-6c-0.552,0-1,0.448-1,1v4c0,0.552,0.448,1,1,1h6c0.552,0,1-0.448,1-1V28.583z M30.583,31.583h-4v-2h4V31.583z">
                                                    </path>
                                                    <path
                                                        d="M43.583,28.583c0-0.552-0.447-1-1-1h-6c-0.552,0-1,0.448-1,1v4c0,0.552,0.448,1,1,1h6c0.553,0,1-0.448,1-1V28.583z M41.583,31.583h-4v-2h4V31.583z">
                                                    </path>
                                                    <path
                                                        d="M54.583,28.583c0-0.552-0.447-1-1-1h-6c-0.553,0-1,0.448-1,1v4c0,0.552,0.447,1,1,1h6c0.553,0,1-0.448,1-1V28.583z M52.583,31.583h-4v-2h4V31.583z">
                                                    </path>
                                                    <path
                                                        d="M21.583,36.583c0-0.552-0.448-1-1-1h-6c-0.552,0-1,0.448-1,1v4c0,0.553,0.448,1,1,1h6c0.552,0,1-0.447,1-1V36.583z M15.583,37.583h4v2h-4V37.583z">
                                                    </path>
                                                    <path
                                                        d="M32.583,36.583c0-0.552-0.448-1-1-1h-6c-0.552,0-1,0.448-1,1v4c0,0.553,0.448,1,1,1h6c0.552,0,1-0.447,1-1V36.583z M30.583,39.583h-4v-2h4V39.583z">
                                                    </path>
                                                    <path
                                                        d="M43.583,36.583c0-0.552-0.447-1-1-1h-6c-0.552,0-1,0.448-1,1v4c0,0.553,0.448,1,1,1h6c0.553,0,1-0.447,1-1V36.583z M41.583,39.583h-4v-2h4V39.583z">
                                                    </path>
                                                    <path
                                                        d="M54.583,36.583c0-0.552-0.447-1-1-1h-6c-0.553,0-1,0.448-1,1v4c0,0.553,0.447,1,1,1h6c0.553,0,1-0.447,1-1V36.583z M52.583,39.583h-4v-2h4V39.583z">
                                                    </path>
                                                    <path
                                                        d="M21.583,44.583c0-0.553-0.448-1-1-1h-6c-0.552,0-1,0.447-1,1v4c0,0.553,0.448,1,1,1h6c0.552,0,1-0.447,1-1V44.583z M15.583,45.583h4v2h-4V45.583z">
                                                    </path>
                                                    <path
                                                        d="M32.583,44.583c0-0.553-0.448-1-1-1h-6c-0.552,0-1,0.447-1,1v4c0,0.553,0.448,1,1,1h6c0.552,0,1-0.447,1-1V44.583z M30.583,47.583h-4v-2h4V47.583z">
                                                    </path>
                                                    <path
                                                        d="M43.583,44.583c0-0.553-0.447-1-1-1h-6c-0.552,0-1,0.447-1,1v4c0,0.553,0.448,1,1,1h6c0.553,0,1-0.447,1-1V44.583z M41.583,47.583h-4v-2h4V47.583z">
                                                    </path>
                                                    <path
                                                        d="M54.583,44.583c0-0.553-0.447-1-1-1h-6c-0.553,0-1,0.447-1,1v12c0,0.553,0.447,1,1,1h6c0.553,0,1-0.447,1-1V44.583z M52.583,55.583h-4v-10h4V55.583z">
                                                    </path>
                                                    <path
                                                        d="M21.583,52.583c0-0.553-0.448-1-1-1h-6c-0.552,0-1,0.447-1,1v4c0,0.553,0.448,1,1,1h6c0.552,0,1-0.447,1-1V52.583z M15.583,53.583h4v2h-4V53.583z">
                                                    </path>
                                                    <path
                                                        d="M32.583,52.583c0-0.553-0.448-1-1-1h-6c-0.552,0-1,0.447-1,1v4c0,0.553,0.448,1,1,1h6c0.552,0,1-0.447,1-1V52.583z M30.583,55.583h-4v-2h4V55.583z">
                                                    </path>
                                                    <path
                                                        d="M43.583,52.583c0-0.553-0.447-1-1-1h-6c-0.552,0-1,0.447-1,1v4c0,0.553,0.448,1,1,1h6c0.553,0,1-0.447,1-1V52.583z M41.583,55.583h-4v-2h4V55.583z">
                                                    </path>
                                                    <path
                                                        d="M23,13.583h-5c-0.553,0-1.417,1.281-1.417,1.834v3c0,0.553,0.447,1,1,1s1-0.447,1-1v-2.834H23c0.553,0,1-0.447,1-1 S23.553,13.583,23,13.583z">
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
        </div>
    </div>
</x-app-layout>
