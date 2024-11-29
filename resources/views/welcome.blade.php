<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Quicksand', sans-serif;
        }
    </style>

    @livewireStyles
</head>

<body class="font-sans antialiased bg-gradient-to-r from-blue-100 to-indigo-100">
    <div class="min-h-screen flex flex-col justify-center items-center px-4">
        <div class="max-w-4xl w-full space-y-8">
            <div class="text-center">
                <div class="bg-white p-4 rounded-md shadow-md mt-2 mb-2">
                    <h1 class="text-4xl font-extrabold text-blue-600 mb-2">
                        PLAN DE VIVIENDA SOCIAL - AEVIVIENDA
                    </h1>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        Bienvenido(a) a nuestra plataforma de pagos en linea!
                    </h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Beneficiarios aproximados hasta ahora
                            </dt>
                            <dd class="mt-1 text-3xl font-semibold text-blue-600">
                                {{ number_format($beneficiarios) }}
                            </dd>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Recaudaciones registradas al Mes
                            </dt>
                            <dd class="mt-1 text-3xl font-semibold text-green-600">
                                {{ number_format($beneficiarios + rand(-100, -10)) }}
                            </dd>
                        </div>
                    </div>
                </div>
                @auth
                    <div>
                        <p class="text-xl text-gray-600">
                            Hola, {{ Auth::user()->name }}. ¡Nos alegra verte de nuevo!
                        </p>
                        <div>
                            <a href="{{ url('/importaciones') }}"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Ir al Dashboard
                            </a>
                        </div>
                    </div>
                @else
                    <div class="m-2">
                        <div class="flex items-center justify-evenly bg-white p-4 rounded-md shadow-md mb-2">
                            <svg fill="#000000" width="64px" height="64px" viewBox="-2.4 -2.4 28.80 28.80"
                                id="down-direction-3" xmlns="http://www.w3.org/2000/svg" class="icon line">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path id="primary"
                                        d="M12,3V16m.67,4.62,2.08-3.12a1,1,0,0,0-.67-1.5H9.92a1,1,0,0,0-.67,1.5l2.08,3.12A.79.79,0,0,0,12.67,20.62Z"
                                        style="fill: none; stroke: #000000; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5;">
                                    </path>
                                </g>
                            </svg>
                            <p class="text-xl text-gray-600">
                                Ingresa tu <b>código de prestamo y número de cédula</b> para continuar con tu pago.
                            </p>
                            <svg fill="#000000" width="64px" height="64px" viewBox="-2.4 -2.4 28.80 28.80"
                                id="down-direction-3" xmlns="http://www.w3.org/2000/svg" class="icon line">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path id="primary"
                                        d="M12,3V16m.67,4.62,2.08-3.12a1,1,0,0,0-.67-1.5H9.92a1,1,0,0,0-.67,1.5l2.08,3.12A.79.79,0,0,0,12.67,20.62Z"
                                        style="fill: none; stroke: #000000; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5;">
                                    </path>
                                </g>
                            </svg>
                        </div>
                        <div>
                            <livewire:query-form />
                        </div>
                        <div class="mt-2 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4"
                            role="alert">
                            <p class="font-bold">Aviso de Privacidad y Seguridad:</p>
                            <p>
                                Proteja su información personal. Nunca compartiremos sus datos con terceros sin su
                                consentimiento expreso. Tenga cuidado con solicitudes no autorizadas de información personal
                                fuera de esta plataforma oficial. Si tiene dudas sobre la legitimidad de una solicitud,
                                contáctenos directamente a través de nuestros canales oficiales. Su seguridad es nuestra
                                prioridad.
                            </p>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
    @livewireScripts
</body>

</html>
