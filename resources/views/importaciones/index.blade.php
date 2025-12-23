<x-app-layout>
    {{-- Notification Container --}}
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-3 max-w-md w-full px-4">
        {{-- Success Notifications --}}
        @if (session('success'))
            <div class="notification-toast success-toast" data-type="success">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center">
                            <i class="fa-solid fa-check text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">¡Éxito!</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-0.5">{{ session('success') }}</p>
                    </div>
                    <button onclick="closeNotification(this)"
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="progress-bar bg-emerald-500"></div>
            </div>
        @endif

        @if (session('successD'))
            <div class="notification-toast success-toast" data-type="success">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center">
                            <i class="fa-solid fa-check text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">¡Éxito!</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-0.5">{{ session('successD') }}</p>
                    </div>
                    <button onclick="closeNotification(this)"
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="progress-bar bg-emerald-500"></div>
            </div>
        @endif

        @if (session('successS'))
            <div class="notification-toast success-toast" data-type="success">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center">
                            <i class="fa-solid fa-check text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">¡Éxito!</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-0.5">{{ session('successS') }}</p>
                    </div>
                    <button onclick="closeNotification(this)"
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="progress-bar bg-emerald-500"></div>
            </div>
        @endif

        {{-- Error Notifications --}}
        @if (session('error'))
            <div class="notification-toast error-toast" data-type="error">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center">
                            <i class="fa-solid fa-exclamation text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Error</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-0.5">{{ session('error') }}</p>
                    </div>
                    <button onclick="closeNotification(this)"
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="progress-bar bg-red-500"></div>
            </div>
        @endif

        @if (session('errorD'))
            <div class="notification-toast error-toast" data-type="error">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center">
                            <i class="fa-solid fa-exclamation text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Error</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-0.5">{{ session('errorD') }}</p>
                    </div>
                    <button onclick="closeNotification(this)"
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="progress-bar bg-red-500"></div>
            </div>
        @endif

        @if (session('errorS'))
            <div class="notification-toast error-toast" data-type="error">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center">
                            <i class="fa-solid fa-exclamation text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Error</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-0.5">{{ session('errorS') }}</p>
                    </div>
                    <button onclick="closeNotification(this)"
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="progress-bar bg-red-500"></div>
            </div>
        @endif
    </div>

    {{-- Main Content --}}
    <div class="py-8 w-full min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Importación de Datos</h1>
                <p class="text-gray-600 dark:text-gray-400">Carga masiva de información desde archivos CSV</p>
            </div>

            {{-- Import Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Beneficiarios Import Card --}}
                <div class="import-card group">
                    <div class="card-header">
                        <div class="flex items-center gap-3">
                            <div class="icon-wrapper bg-gradient-to-br from-blue-500 to-blue-600">
                                <i class="fa-solid fa-users text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="card-title">Beneficiarios</h3>
                                <p class="card-subtitle">Importar listado de beneficiarios</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('excel.import-model') }}" method="post" enctype="multipart/form-data"
                        class="card-body">
                        @csrf
                        <x-validation-errors />

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-file-csv text-gray-400 mr-2"></i>
                                Archivo CSV
                            </label>
                            <input type="file" name="file" accept=".csv" required
                                class="form-input file-input">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-database text-gray-400 mr-2"></i>
                                Modelo
                            </label>
                            <select name="model" class="form-input">
                                <option value="beneficiary">Beneficiarios</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-grip-lines-vertical text-gray-400 mr-2"></i>
                                Separador
                            </label>
                            <input type="text" name="separator" placeholder="," maxlength="1" value=";"
                                class="form-input">
                        </div>

                        <x-personal.button submit="true" variant="success" iconLeft="fa-solid fa-file-arrow-up"
                            class="w-full">
                            Importar Beneficiarios
                        </x-personal.button>
                    </form>
                </div>

                {{-- Diferimientos Import Card --}}
                <div class="import-card group">
                    <div class="card-header">
                        <div class="flex items-center gap-3">
                            <div class="icon-wrapper bg-gradient-to-br from-amber-500 to-amber-600">
                                <i class="fa-solid fa-calendar-days text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="card-title">Diferimientos</h3>
                                <p class="card-subtitle">Importar diferimientos de pago</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('excel.import-differiments') }}" method="post"
                        enctype="multipart/form-data" class="card-body">
                        @csrf
                        <x-validation-errors />

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-file-csv text-gray-400 mr-2"></i>
                                Archivo CSV
                            </label>
                            <input type="file" name="file-differiments" accept=".csv" required
                                class="form-input file-input">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-grip-lines-vertical text-gray-400 mr-2"></i>
                                Separador
                            </label>
                            <input type="text" name="separator-differiments" placeholder="," maxlength="1"
                                value=";" class="form-input">
                        </div>

                        <x-personal.button submit="true" variant="success" iconLeft="fa-solid fa-file-arrow-up"
                            class="w-full">
                            Importar Diferimientos
                        </x-personal.button>
                    </form>
                </div>

                {{-- Gastos Import Card --}}
                <div class="import-card group">
                    <div class="card-header">
                        <div class="flex items-center gap-3">
                            <div class="icon-wrapper bg-gradient-to-br from-purple-500 to-purple-600">
                                <i class="fa-solid fa-receipt text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="card-title">Gastos</h3>
                                <p class="card-subtitle">Importar registro de gastos</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('excel.import-spends') }}" method="post" enctype="multipart/form-data"
                        class="card-body">
                        @csrf
                        <x-validation-errors />

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-file-csv text-gray-400 mr-2"></i>
                                Archivo CSV
                            </label>
                            <input type="file" name="file-spends" accept=".csv" required
                                class="form-input file-input">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-grip-lines-vertical text-gray-400 mr-2"></i>
                                Separador
                            </label>
                            <input type="text" name="separator-spends" placeholder="" maxlength="1"
                                value=";" class="form-input">
                        </div>

                        <x-personal.button submit="true" variant="success" iconLeft="fa-solid fa-file-arrow-up"
                            class="w-full">
                            Importar Gastos
                        </x-personal.button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Styles --}}
    <style>
        /* Notification Toast Styles */
        .notification-toast {
            position: relative;
            background: white;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
            animation: slideInRight 0.3s ease-out;
            backdrop-filter: blur(10px);
        }

        .dark .notification-toast {
            background: rgba(31, 41, 55, 0.95);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .notification-toast.hiding {
            animation: slideOutRight 0.3s ease-in forwards;
        }

        .progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            animation: progressBar 5s linear forwards;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        @keyframes progressBar {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        /* Import Card Styles */
        .import-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .import-card {
            background: rgba(31, 41, 55, 0.5);
            border-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .import-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .card-header {
            padding: 24px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dark .card-header {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        .icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .group:hover .icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
        }

        .dark .card-title {
            color: white;
        }

        .card-subtitle {
            font-size: 13px;
            color: #6B7280;
            margin-top: 2px;
        }

        .dark .card-subtitle {
            color: #9CA3AF;
        }

        .card-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Form Styles */
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            display: flex;
            align-items: center;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        .dark .form-label {
            color: #D1D5DB;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #E5E7EB;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s ease;
            background: white;
            color: #111827;
        }

        .dark .form-input {
            background: rgba(17, 24, 39, 0.5);
            border-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .dark .form-input:focus {
            border-color: #60A5FA;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
        }

        .file-input {
            padding: 8px;
            cursor: pointer;
        }

        .file-input::file-selector-button {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            background: #F3F4F6;
            color: #374151;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-right: 12px;
        }

        .dark .file-input::file-selector-button {
            background: rgba(55, 65, 81, 0.8);
            color: #D1D5DB;
        }

        .file-input::file-selector-button:hover {
            background: #E5E7EB;
        }

        .dark .file-input::file-selector-button:hover {
            background: rgba(75, 85, 99, 0.8);
        }
    </style>

    {{-- Scripts --}}
    <script>
        // Auto-dismiss notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification-toast');

            notifications.forEach(notification => {
                setTimeout(() => {
                    closeNotification(notification.querySelector('button'));
                }, 5000);
            });
        });

        function closeNotification(button) {
            const notification = button.closest('.notification-toast');
            notification.classList.add('hiding');

            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    </script>
</x-app-layout>
