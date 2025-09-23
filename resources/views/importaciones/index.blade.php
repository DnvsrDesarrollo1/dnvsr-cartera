<x-app-layout>
    <div class="py-4 w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Beneficiarios Import Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6">
                        <x-validation-errors />
                        @if (session('error'))
                            <div class="mb-4 p-4 rounded-lg bg-red-50 border-l-4 border-red-500 dark:bg-red-900/50">
                                <p class="text-sm text-red-700 dark:text-red-200">{{ session('error') }}</p>
                            </div>
                        @endif

                        @if (session('success'))
                            <div
                                class="mb-4 p-4 rounded-lg bg-green-50 border-l-4 border-green-500 dark:bg-green-900/50">
                                <p class="text-sm text-green-700 dark:text-green-200">{{ session('success') }}</p>
                            </div>
                        @endif

                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Beneficiarios
                        </h3>
                        <form action="{{ route('excel.import-model') }}" method="post" enctype="multipart/form-data"
                            class="mt-6 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Archivo
                                    CSV</label>
                                <input type="file" name="file" accept=".csv"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Modelo</label>
                                <select name="model"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm">
                                    <option value="beneficiary">Beneficiarios</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Separador</label>
                                <input type="text" name="separator" placeholder="," maxlength="1"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm">
                            </div>
                            <x-personal.button submit="true" variant="success" iconLeft="fa-solid fa-file-arrow-up">
                                Importar
                            </x-personal.button>
                        </form>
                    </div>
                </div>

                <!-- Diferimientos Import Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6">
                        <x-validation-errors />
                        @if (session('errorD'))
                            <div class="mb-4 p-4 rounded-lg bg-red-50 border-l-4 border-red-500 dark:bg-red-900/50">
                                <p class="text-sm text-red-700 dark:text-red-200">{{ session('errorD') }}</p>
                            </div>
                        @endif

                        @if (session('successD'))
                            <div
                                class="mb-4 p-4 rounded-lg bg-green-50 border-l-4 border-green-500 dark:bg-green-900/50">
                                <p class="text-sm text-green-700 dark:text-green-200">{{ session('successD') }}</p>
                            </div>
                        @endif

                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Diferimientos
                        </h3>
                        <form action="{{ route('excel.import-differiments') }}" method="post"
                            enctype="multipart/form-data" class="mt-6 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Archivo
                                    CSV</label>
                                <input type="file" name="file-differiments" accept=".csv"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Separador</label>
                                <input type="text" name="separator-differiments" placeholder="," maxlength="1"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm">
                            </div>
                            <x-personal.button submit="true" variant="success" iconLeft="fa-solid fa-file-arrow-up">
                                Importar
                            </x-personal.button>
                        </form>
                    </div>
                </div>

                <!-- Gastos Import Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6">
                        <x-validation-errors />
                        @if (session('errorS'))
                            <div class="mb-4 p-4 rounded-lg bg-red-50 border-l-4 border-red-500 dark:bg-red-900/50">
                                <p class="text-sm text-red-700 dark:text-red-200">{{ session('errorS') }}</p>
                            </div>
                        @endif

                        @if (session('successS'))
                            <div
                                class="mb-4 p-4 rounded-lg bg-green-50 border-l-4 border-green-500 dark:bg-green-900/50">
                                <p class="text-sm text-green-700 dark:text-green-200">{{ session('successS') }}</p>
                            </div>
                        @endif

                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Gastos
                        </h3>
                        <form action="{{ route('excel.import-spends') }}" method="post" enctype="multipart/form-data"
                            class="mt-6 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Archivo
                                    CSV</label>
                                <input type="file" name="file-spends" accept=".csv"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm">
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Separador</label>
                                <input type="text" name="separator-spends" placeholder="," maxlength="1"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm">
                            </div>
                            <x-personal.button submit="true" variant="success" iconLeft="fa-solid fa-file-arrow-up">
                                Importar
                            </x-personal.button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
