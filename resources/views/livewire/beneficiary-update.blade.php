<div x-data="{ benModal: @entangle('benModal') }">
    <x-personal.button @click="benModal = true" @keydown.escape.window="benModal = false" variant="primary" size="sm"
        iconCenter="fa-solid fa-user-pen">
        Editar
    </x-personal.button>

    <div x-show="benModal" x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-100"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center bg-gray-900/50 p-4">

        {{-- Modal Container --}}
        <div class="bg-white overflow-hidden rounded-2xl shadow-xl sm:max-w-3xl w-full"
            @click.outside="benModal = false">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 tracking-tight">
                            Editar Perfil del Beneficiario
                        </h3>
                        <p class="text-sm text-gray-500 mt-0.5">Actualice la información del beneficiario</p>
                    </div>
                    <button @click="benModal = false"
                        class="rounded-full p-2 text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition-colors"
                        title="Cerrar">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-6 py-6 max-h-[calc(100vh-220px)] overflow-y-auto custom-scrollbar">
                <form wire:submit="update" class="space-y-6">

                    {{-- Sección: Información Personal --}}
                    <div class="space-y-4">
                        <h4
                            class="text-xs font-bold uppercase tracking-wider text-gray-400 pb-2 border-b border-gray-100">
                            Información Personal
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Nombre --}}
                            <div class="md:col-span-2">
                                <label for="nombre" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Nombre Completo
                                </label>
                                <input type="text" wire:model="nombre" id="nombre"
                                    class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                @error('nombre')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- CI --}}
                            <div>
                                <label for="ci" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Cédula de Identidad
                                </label>
                                <input type="text" wire:model="ci" id="ci"
                                    class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                @error('ci')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Complemento --}}
                            <div>
                                <label for="complemento" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Complemento
                                </label>
                                <input type="text" wire:model="complemento" id="complemento"
                                    class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                @error('complemento')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Expedido --}}
                            <div>
                                <label for="expedido" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Expedido en
                                </label>
                                <input type="text" wire:model="expedido" id="expedido"
                                    class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                @error('expedido')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Fecha de Nacimiento --}}
                            <div>
                                <label for="fecha_nacimiento" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Fecha de Nacimiento
                                </label>
                                <input type="date" wire:model="fecha_nacimiento" id="fecha_nacimiento"
                                    class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                @error('fecha_nacimiento')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Departamento --}}
                            <div>
                                <label for="departamento" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Departamento
                                </label>
                                <input type="text" wire:model="departamento" id="departamento"
                                    class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                @error('departamento')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Estado del Crédito --}}
                    <div class="space-y-4">
                        <h4
                            class="text-xs font-bold uppercase tracking-wider text-gray-400 pb-2 border-b border-gray-100">
                            Estado del Crédito
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Estado --}}
                            <div class="md:col-span-2">
                                <label for="estado" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Estado del Crédito
                                </label>
                                <input type="text" wire:model.live="estado" id="estado"
                                    class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                @error('estado')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror

                                @if ($estado === 'BLOQUEADO')
                                    <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                        <label for="cod_fondesif"
                                            class="block text-xs font-semibold text-amber-700 mb-1.5">
                                            Tipo de Bloqueo
                                        </label>
                                        <select wire:model="cod_fondesif" id="cod_fondesif"
                                            class="block w-full rounded-lg border-amber-200 bg-white px-4 py-2.5 text-gray-700 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500 transition-all">
                                            <option value="">Seleccione una opción</option>
                                            <option value="INACTIVO">INACTIVO</option>
                                            <option value="REVERTIDO">REVERTIDO</option>
                                            <option value="SINIESTRO REPORTADO">SINIESTRO REPORTADO</option>
                                        </select>
                                        @error('cod_fondesif')
                                            <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            {{-- Código Préstamo --}}
                            <div class="md:col-span-2">
                                <label for="idepro" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Código de Préstamo
                                </label>
                                @if ($idepro != $beneficiary->idepro)
                                    <div class="mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
                                        <p class="text-red-600 text-xs flex items-start gap-2">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>
                                                Se detectó una posible actualización para el código de préstamo.
                                                Considere que esto implica también la sincronía con criterios paralelos.
                                            </span>
                                        </p>
                                    </div>
                                @endif
                                <input type="text" wire:model.live="idepro" id="idepro"
                                    class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                @error('idepro')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Montos y Valores --}}
                    <div class="space-y-4">
                        <h4
                            class="text-xs font-bold uppercase tracking-wider text-gray-400 pb-2 border-b border-gray-100">
                            Montos y Valores
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Monto Crédito --}}
                            <div>
                                <label for="monto_credito" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Monto Crédito
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Bs.</span>
                                    <input type="number" wire:model="monto_credito" id="monto_credito"
                                        step="0.01"
                                        class="block w-full rounded-lg border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                </div>
                                @error('monto_credito')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Monto Activado --}}
                            <div>
                                <label for="monto_activado" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Monto Activado
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Bs.</span>
                                    <input type="number" wire:model="monto_activado" id="monto_activado"
                                        step="0.01"
                                        class="block w-full rounded-lg border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                </div>
                                @error('monto_activado')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Total Activado --}}
                            <div>
                                <label for="total_activado" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Total Activado
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Bs.</span>
                                    <input type="number" wire:model="total_activado" id="total_activado"
                                        step="0.01"
                                        class="block w-full rounded-lg border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                </div>
                                @error('total_activado')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Gastos Judiciales --}}
                            <div>
                                <label for="gastos_judiciales"
                                    class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Gastos Judiciales
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Bs.</span>
                                    <input type="number" wire:model="gastos_judiciales" id="gastos_judiciales"
                                        step="0.01"
                                        class="block w-full rounded-lg border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                </div>
                                @error('gastos_judiciales')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Saldo Crédito --}}
                            <div>
                                <label for="saldo_credito" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Saldo Crédito
                                </label>
                                @php
                                    $saldo_credito =
                                        $beneficiary->monto_activado -
                                        $beneficiary
                                            ->payments()
                                            ->where('prtdtdesc', 'LIKE', 'CAPI%')
                                            ->where('prtdtdesc', 'NOT LIKE', '%DIFER%')
                                            ->where(function ($query) {
                                                $query
                                                    ->whereNull('observacion')
                                                    ->orWhere('observacion', '')
                                                    ->orWhere('observacion', '!=', 'LEGACY 22/24');
                                            })
                                            ->sum('montopago');
                                @endphp
                                @if ($saldo_credito != $beneficiary->saldo_credito)
                                    <div class="mb-2 p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <p class="text-yellow-700 text-xs flex items-start gap-2">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>
                                                El sistema detecta un saldo aprox. de Bs.
                                                {{ number_format($saldo_credito, 3) }}
                                            </span>
                                        </p>
                                    </div>
                                @endif
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Bs.</span>
                                    <input type="number" wire:model="saldo_credito" id="saldo_credito"
                                        step="0.01"
                                        class="block w-full rounded-lg border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                </div>
                                @error('saldo_credito')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Monto Recuperado --}}
                            <div>
                                <label for="monto_recuperado"
                                    class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Monto Recuperado
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Bs.</span>
                                    <input type="number" wire:model="monto_recuperado" id="monto_recuperado"
                                        step="0.01"
                                        class="block w-full rounded-lg border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                </div>
                                @error('monto_recuperado')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Sección: Parámetros del Crédito --}}
                    <div class="space-y-4">
                        <h4
                            class="text-xs font-bold uppercase tracking-wider text-gray-400 pb-2 border-b border-gray-100">
                            Parámetros del Crédito
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Fecha de Activación --}}
                            <div>
                                <label for="fecha_activacion"
                                    class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Fecha de Activación
                                </label>
                                <input type="date" wire:model="fecha_activacion" id="fecha_activacion"
                                    class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                @error('fecha_activacion')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Plazo Crédito --}}
                            <div>
                                <label for="plazo_credito" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Plazo Crédito (meses)
                                </label>
                                <input type="number" wire:model="plazo_credito" id="plazo_credito"
                                    class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                @error('plazo_credito')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Tasa de Interés --}}
                            <div>
                                <label for="tasa_interes" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Tasa de Interés (%)
                                </label>
                                <div class="relative">
                                    <input type="number" wire:model="tasa_interes" id="tasa_interes" step="0.01"
                                        class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                                </div>
                                @error('tasa_interes')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Seguro Desgravamen --}}
                            <div>
                                <label for="seguro" class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Seguro Desgravamen
                                </label>
                                <input type="number" wire:model="seguro" id="seguro" min="0"
                                    max="0.076" step="0.001"
                                    class="block w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-700 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                                @error('seguro')
                                    <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div
                class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500 hidden sm:block">
                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    Los cambios son irreversibles una vez confirmados
                </p>
                <div class="flex gap-3 w-full sm:w-auto">
                    <x-personal.button variant="success" iconLeft="fa-solid fa-save" wire:click="update"
                        wire:confirm="¿Está seguro de que desea guardar los cambios?" class="flex-1 sm:flex-none">
                        Aplicar Cambios
                    </x-personal.button>
                    <x-personal.button variant="danger" iconLeft="fa-solid fa-trash" wire:click="delete"
                        wire:confirm.prompt="Desea eliminar el beneficiario?\n\nEscriba BORRAR para proceder|BORRAR"
                        class="flex-1 sm:flex-none">
                        Eliminar
                    </x-personal.button>
                </div>
            </div>
        </div>
    </div>
</div>
