<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Importación de Archivos
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
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
                                <button type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cargar archivo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- -------------------------------------------------------------------------- -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mt-4">
                <div class="p-6">
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
                                <button type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cargar archivo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        /*let dropArea = document.getElementById('drop-area');
            let fileInput = document.getElementById('file');
            let fileIcon = document.getElementById('file-icon');
            let fileName = document.getElementById('file-name');
            *
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropArea.classList.add('bg-indigo-100');
        }

        function unhighlight() {
            dropArea.classList.remove('bg-indigo-100');
        }

        dropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            updateIcon();
        }

        fileInput.addEventListener('change', updateIcon);

        function updateIcon() {
            if (fileInput.files && fileInput.files[0]) {
                // Change to CSV icon
                fileIcon.innerHTML = `
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                `;
                fileIcon.setAttribute('viewBox', '0 0 24 24');
                fileIcon.classList.remove('text-gray-400');
                fileIcon.classList.add('text-green-500');

                // Display file name
                fileName.textContent = fileInput.files[0].name + " - ha sido seleccionado.";
            } else {
                // Revert to original icon
                fileIcon.innerHTML = `
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                `;
                fileIcon.setAttribute('viewBox', '0 0 48 48');
                fileIcon.classList.remove('text-green-500');
                fileIcon.classList.add('text-gray-400');

                // Reset file name display
                fileName.textContent = 'CSV hasta 10MB';
            }
        }

        let dropAreaD = document.getElementById('drop-area-differiments');
        let fileInputD = document.getElementById('file-differiments');
        let fileIconD = document.getElementById('file-icon-differiments');
        let fileNameD = document.getElementById('file-name-differiments');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropAreaD.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropAreaD.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropAreaD.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropAreaD.classList.add('bg-indigo-100');
        }

        function unhighlight() {
            dropAreaD.classList.remove('bg-indigo-100');
        }

        dropAreaD.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInputD.files = files;
            updateIcon();
        }

        fileInputD.addEventListener('change', updateIcon);

        function updateIcon() {
            if (fileInputD.files && fileInputD.files[0]) {
                // Change to CSV icon
                fileIconD.innerHTML = `
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                `;
                fileIconD.setAttribute('viewBox', '0 0 24 24');
                fileIconD.classList.remove('text-gray-400');
                fileIconD.classList.add('text-green-500');

                // Display file name
                fileNameD.textContent = fileInputD.files[0].name + " - ha sido seleccionado.";
            } else {
                // Revert to original icon
                fileIconD.innerHTML = `
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                `;
                fileIconD.setAttribute('viewBox', '0 0 48 48');
                fileIconD.classList.remove('text-green-500');
                fileIconD.classList.add('text-gray-400');

                // Reset file name display
                fileNameD.textContent = 'CSV hasta 10MB';
            }
        }
        */
    </script>
</x-app-layout>
