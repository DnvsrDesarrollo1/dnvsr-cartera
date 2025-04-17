<x-app-layout>
    <div class="py-4 w-full">
        <div class="grid grid-cols-1 grid-rows-4 lg:grid-cols-4 gap-2 mx-auto px-2">
            <div class="md:col-span-2 row-span-4 bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg border border-gray-500">
                <div class="p-2">
                    <x-validation-errors />
                    @if (session('error'))
                        <div class="bg-red-200 border border-red-500 text-red-500 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Error! : </strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="bg-green-200 border border-green-600 text-green-600 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Éxito! : </strong>
                            <span class="block sm:inline"> : {{ session('success') }}</span>
                        </div>
                    @endif
                    <h3 class="text-lg mt-2 font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Beneficiarios / Planes / Pagos / Vouchers
                    </h3>
                    <form id="form" action="{{ route('excel.import-model') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label for="file"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Seleccionar archivo CSV
                                </label>
                                <input id="file" name="file" type="file" accept=".csv"
                                    class="mt-1 block w-full text-sm text-gray-800 dark:text-gray-300 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            </div>
                            <div>
                                <label for="model"
                                    class="block text-sm font-medium text-gray-800 dark:text-gray-300">
                                    Seleccionar modelo
                                </label>
                                <select id="model" name="model"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base text-gray-800 dark:text-gray-300 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    {{-- <option value="plan" class="dark:text-gray-800">Planes</option>
                                    <option value="payment" class="dark:text-gray-800">Pagos</option> --}}
                                    <option value="beneficiary" class="dark:text-gray-800">Beneficiarios</option>
                                    {{-- <option value="voucher" class="dark:text-gray-800">Vouchers</option> --}}
                                </select>
                            </div>

                            <div>
                                <label for="separator"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Símbolo separador de columnas
                                </label>
                                <input type="text" name="separator" id="separator" placeholder="Ej: ," maxlength="1"
                                    autocomplete="off"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div>
                                <x-personal.button submit="true" variant="success"
                                    iconLeft="fa-solid fa-file-circle-plus">
                                    Cargar archivo
                                </x-personal.button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- -------------------------------------------------------------------------- -->
            <div class="md:col-span-2 row-span-4 bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg border border-gray-500">
                <div class="p-2">
                    <x-validation-errors />
                    @if (session('errorD'))
                        <div class="bg-red-200 border border-red-500 text-red-500 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Error! : </strong>
                            <span class="block sm:inline">{{ session('errorD') }}</span>
                        </div>
                    @endif

                    @if (session('successD'))
                        <div class="bg-green-200 border border-green-600 text-green-600 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Éxito! : </strong>
                            <span class="block sm:inline">{{ session('successD') }}</span>
                        </div>
                    @endif
                    <h3 class="text-lg mt-2 font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Diferimientos
                    </h3>
                    <form id="form" action="{{ route('excel.import-differiments') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label for="file-differiments"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Seleccionar archivo CSV
                                </label>
                                <input id="file-differiments" name="file-differiments" type="file" accept=".csv"
                                    class="mt-1 block w-full text-sm text-gray-800 dark:text-gray-300 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            </div>
                            <div>
                                <label for="separator-differiments"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Símbolo separador de columnas
                                </label>
                                <input type="text" name="separator-differiments" id="separator-differiments"
                                    placeholder="Ej: ," maxlength="1" autocomplete="off"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <x-personal.button submit="true" variant="success"
                                    iconLeft="fa-solid fa-file-circle-plus">
                                    Cargar archivo
                                </x-personal.button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
