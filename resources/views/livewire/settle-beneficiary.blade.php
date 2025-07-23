<div>
    <!-- Modal -->
    <div x-data="{ settleModal: @entangle('settleModal') }">
        <!-- Trigger Button -->
        <x-personal.button variant="success" @click="settleModal = true" iconCenter="fa-solid fa-handshake">
            @if ($settlement->id != null)
                Actualizar Liquidación
            @else
                Liquidar Crédito
            @endif
        </x-personal.button>

        <!-- Modal Background -->
        <div x-show="settleModal" @keyup.escape.window="settleModal = false"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
            <!-- Modal Content -->

            <div class="bg-white overflow-hidden rounded-lg transform transition-all sm:max-w-5xl sm:w-full m-4 z-50"
                @click.outside="settleModal = false">
                <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900">
                            Liquidación de Beneficiarios
                        </h3>
                        @if ($settlement->id != null && $settlement->estado != 'pendiente')
                            <div>
                                <h3
                                    class="bg-gray-500 text-white rounded-md p-2 space-x-2 flex items-center justify-between">
                                    <i class="fa-solid fa-qrcode"></i>
                                    <span>{{ base64_encode($settlement->id . '_' . $settlement->beneficiary_id . '_' . $settlement->user_id) }}</span>
                                    <x-personal.button variant="secondary"
                                        href="{{ route('liquidacion.pdf', $settlement) }}"
                                        iconLeft="fa-solid fa-file-pdf" to="_blank">
                                        PDF
                                    </x-personal.button>
                                </h3>
                                <span class="text-xs">
                                    Liquidación solicitada y aprobada para <b>{{ $settlement->user->name }}</b>.
                                </span>
                            </div>
                        @endif
                        <button @click="settleModal = false" class="text-gray-400 hover:text-gray-500"
                            title="Presione 'ESC' para cerrar.">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="px-2 sm:p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                    @if (session('message'))
                        <x-personal.alert type="info" message="{{ session('message') }}" />
                    @endif
                    @if (
                        $settlement->id != null &&
                            strtoupper($settlement->estado) == 'APROBADO' &&
                            $settlement->updated_at->diffInDays(now()) >= 5)
                        <x-personal.alert type="info" message="La ejecución de esta liquidación venció."
                            clossable="false" />
                    @endif
                    <form class="space-y-2" wire:submit.prevent="save">
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-2 bg-white rounded-lg shadow-md">

                            <!-- Capital a Liquidar -->
                            <div class="space-y-1">
                                <label for="capSettle" class="block text-sm font-medium text-gray-700">
                                    Capital:
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <input min="0" type="number" wire:model.blur="capSettle" id="capSettle"
                                        step="0.10"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border"
                                        placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    </div>
                                </div>
                                @error('capSettle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Capital Diferido a Liquidar -->
                            <div class="space-y-1">
                                <label for="capDifSettle" class="block text-sm font-medium text-gray-700">
                                    Capital Diferido:
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <input min="0" type="number" wire:model.blur="capDifSettle"
                                        id="capDifSettle" step="0.10"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border"
                                        placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    </div>
                                </div>
                                @error('capDifSettle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            {{-- 1111 --}}

                            <!-- Interés Devengado a Liquidar -->
                            <div class="space-y-1">
                                <label for="intDevSettle" class="block text-sm font-medium text-gray-700">
                                    Interés Devengado:
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <input min="0" type="number" wire:model.blur="intDevSettle"
                                        id="intDevSettle" step="0.10"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border"
                                        placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    </div>
                                </div>
                                @error('intDevSettle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Interés a Liquidar -->
                            <div class="space-y-1">
                                <label for="intSettle" class="block text-sm font-medium text-gray-700">
                                    Interés: (de una mora de {{ $diasMora }} días)
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <input min="0" type="number" wire:model.blur="intSettle" id="intSettle"
                                        step="0.10"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border"
                                        placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    </div>
                                </div>
                                @error('intSettle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Interés Diferido a Liquidar -->
                            <div class="space-y-1">
                                <label for="intDifSettle" class="block text-sm font-medium text-gray-700">
                                    Interés Diferido:
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <input min="0" type="number" wire:model.blur="intDifSettle"
                                        id="intDifSettle" step="0.10"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border"
                                        placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    </div>
                                </div>
                                @error('intDifSettle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Seguro Devengado a Liquidar -->
                            <div class="space-y-1">
                                <label for="segDevSettle" class="block text-sm font-medium text-gray-700">
                                    Seguro Devengado:
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <input min="0" type="number" wire:model.blur="segDevSettle"
                                        id="segDevSettle" step="0.10"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border"
                                        placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    </div>
                                </div>
                                @error('segDevSettle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Seguro a Liquidar -->
                            <div class="space-y-1">
                                <label for="segSettle" class="block text-sm font-medium text-gray-700">
                                    Seguro:
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <input min="0" type="number" wire:model.blur="segSettle" id="segSettle"
                                        step="0.10"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border"
                                        placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    </div>
                                </div>
                                @error('segSettle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Otros a Liquidars -->
                            <div class="space-y-1">
                                <div class="space-y-1">
                                    <label for="otrosSettle" class="block text-sm font-medium text-gray-700">
                                        Otros: (Gastos Adm, Jud, Not, etc.)
                                    </label>
                                    <div class="relative rounded-md shadow-sm">
                                        <input min="0" type="number" wire:model.blur="otrosSettle"
                                            id="otrosSettle" step="0.10"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border"
                                            placeholder="0.00">
                                        <div
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        </div>
                                    </div>
                                    @error('otrosSettle')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-1">
                                    <label for="descuento" class="block text-sm font-medium text-gray-700">
                                        Descuento al Gasto Administrativo:
                                    </label>
                                    <div class="relative rounded-md shadow-sm">
                                        <input min="0" type="number" wire:model.blur="descuento"
                                            id="descuento" step="0.10"
                                            class="block w-full rounded-md border-red-400 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border"
                                            placeholder="0.00">
                                        <div
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        </div>
                                    </div>
                                    @error('descuento')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Autocalculo a Liquidars -->
                            <div class="space-y-1">
                                <label for="totalSettle" class="block text-sm font-medium text-gray-700">
                                    Campo de autocálculo y autodistribución:
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="number" wire:model.blur="totalSettle" id="totalSettle"
                                        step="0.10"
                                        class="block w-full bg-gray-200 rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm py-2 px-3 border"
                                        placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    </div>
                                </div>
                                @error('totalSettle')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div class="space-y-1">
                                <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                                <select id="estado"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    wire:model.blur="estado">
                                    @foreach ($estados as $estadoOption)
                                        <option value="{{ $estadoOption }}" @selected($estado == $estadoOption)>
                                            {{ ucfirst($estadoOption) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('estado')
                                    <span class="text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Comentarios -->
                            <div class="space-y-1">
                                <label for="comentarios" class="block text-sm font-medium text-gray-700">Comentarios
                                    del Técnico:</label>
                                <textarea id="comentarios" name="comentarios" rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    wire:model.blur="comentarios"></textarea>
                                @error('comentarios')
                                    <span class="text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Observaciones -->
                            <div class="space-y-1">
                                <label for="observaciones"
                                    class="block text-sm font-medium text-green-700">Observaciones del
                                    Responsable:</label>
                                @can('write settlements')
                                    <textarea id="observaciones" name="observaciones" rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                                        wire:model.blur="observaciones"></textarea>
                                    @error('observaciones')
                                        <span class="text-sm text-red-600">{{ $message }}</span>
                                    @enderror
                                @else
                                    <p class="border rounded-md p-2 text-sm mt-1 bg-gray-100">
                                        {{ $observaciones ?? 'N/A' }}
                                    </p>
                                @endcan
                            </div>

                            <!-- Anexos -->
                            <div class="space-y-1 bg-gray-200 p-2 rounded-md col-span-2">
                                <div>
                                    <label for="anexos"
                                        class="block text-sm font-medium text-gray-700">Anexos</label>
                                    <div class="mt-1">
                                        <input id="anexos" type="file" multiple
                                            class="block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-md file:border-0
                                                file:text-sm file:font-semibold
                                            file:bg-indigo-50 file:text-indigo-700
                                            hover:file:bg-indigo-100"
                                            wire:model="anexos">

                                        @error('anexos.*')
                                            <span class="text-sm text-red-600">{{ $message }}</span>
                                        @enderror

                                        <div wire:loading wire:target="anexos" class="flex items-center space-x-2 text-sm text-gray-600 mt-2 bg-blue-50 p-2 rounded-md border border-blue-200">
                                            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>Subiendo archivos, por favor espere...</span>
                                        </div>

                                        @if (count($anexos) > 0)
                                            <div class="mt-2">
                                                <p class="text-sm font-medium text-gray-700">Archivos anexados:</p>
                                                <ul class="list-disc list-inside mt-1 text-sm text-gray-600">
                                                    @foreach ($anexos as $index => $anexo)
                                                        <li class="flex items-center">
                                                            @if (is_string($anexo))
                                                                <a href="{{ url('storage/' . ltrim($anexo, '/')) }}"
                                                                    target="_blank"
                                                                    class="text-indigo-600 hover:underline">
                                                                    {{ basename($anexo) }}
                                                                </a>
                                                            @else
                                                                {{ $anexo->getClientOriginalName() }}
                                                            @endif
                                                            <button type="button"
                                                                wire:click="removeAnexo({{ $index }})"
                                                                class="ml-2 text-red-500 hover:text-red-700">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-1 bg-gray-200 p-2 rounded-md col-span-1">
                                @if ($settlement->id != null && $settlement->estado == 'aprobado')
                                    <div class="mb-2">
                                        <label for="comprobante" class="block text-sm font-medium text-gray-700">
                                            Comprobante de Deposito:
                                        </label>
                                        <div class="relative rounded-md shadow-sm">
                                            <input type="text" wire:model.blur="comprobante" id="comprobante"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm py-2 px-3 border"
                                                placeholder="12345678900ABC">
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            </div>
                                        </div>
                                        @error('comprobante')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-2">
                                        <label for="fecha_comprobante"
                                            class="block text-sm font-medium text-gray-700">
                                            Comprobante de Deposito:
                                        </label>
                                        <div class="relative rounded-md shadow-sm">
                                            <input type="date" wire:model.blur="fecha_comprobante"
                                                id="fecha_comprobante"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm py-2 px-3 border">
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            </div>
                                        </div>
                                        @error('fecha_comprobante')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                <div class="flex items-center justify-between bg-gray-100 p-4 border-t border-gray-300">
                    <!-- Botones de acción -->
                    <div class="flex items-center space-x-4 w-full">
                        @can('read settlements')
                            @if (strtoupper($settlement->estado) == 'PENDIENTE' || $settlement->id == null)
                                <x-personal.button variant="success" iconLeft="fa-solid fa-paper-plane" wire:click="save"
                                    wire:confirm="¿Está seguro de emitir la solicitud?">
                                    @if ($settlement->id != null)
                                        Actualizar Solicitud
                                    @else
                                        Emitir Solicitud
                                    @endif
                                </x-personal.button>
                                @if ($settlement->id != null)
                                    <x-personal.button variant="danger" iconLeft="fa-solid fa-trash-can"
                                        wire:click="delete"
                                        wire:confirm="¿Está seguro de eliminar la solicitud?\nDeberá generar y revisar todos los campos/montos nuevamente.">
                                        Eliminar Solicitud
                                    </x-personal.button>
                                @endif
                            @elseif (strtoupper($settlement->estado) == 'APROBADO')
                                <div class="w-full">
                                    <x-personal.alert type="success"
                                        message="Liquidación aprobada, proceder con firma de contrato."
                                        clossable="false" />
                                    <div>
                                        <x-personal.button variant="success" iconLeft="fa-solid fa-paper-plane"
                                            wire:click="save" wire:confirm="¿Está seguro de actualizar la solicitud?">
                                            Actualizar Solicitud
                                        </x-personal.button>
                                    </div>
                                </div>
                            @endif
                        @endcan
                        <div class="w-full">
                            @if ($settlement->id != null && $settlement->estado != 'ejecutado')
                                <h2 class="text-right text-gray-700">
                                    Se liquidará un monto total de: <strong>Bs.
                                        {{ number_format(($capSettle ?: 0) + ($capDifSettle ?: 0) + ($intSettle ?: 0) + ($intDevSettle ?: 0) + ($intDifSettle ?: 0) + ($segSettle ?: 0) + ($segDevSettle ?: 0) + ($otrosSettle ?: 0), 2) }}</strong>
                                </h2>
                            @else
                                <h2 class="text-right text-gray-700">
                                    La presente liquidación fue ejecutada por un monto total de: <strong>Bs.
                                        {{ number_format(($capSettle ?: 0) + ($capDifSettle ?: 0) + ($intSettle ?: 0) + ($intDevSettle ?: 0) + ($intDifSettle ?: 0) + ($segSettle ?: 0) + ($segDevSettle ?: 0) + ($otrosSettle ?: 0), 2) }}</strong>
                                </h2>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
