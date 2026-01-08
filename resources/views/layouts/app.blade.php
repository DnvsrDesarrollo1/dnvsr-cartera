<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('assets/main_ico.png') }}" type="image/x-icon">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/dumbbell.js"></script>
    <script src="https://code.highcharts.com/modules/lollipop.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --primary-blue: #2D5D7B;
            --accent-blue: #3A7CA5;
            --ice-blue: #D6E6F2;
            --silver: #C0CFD8;
            --white: #FFFFFF;
            --glow: rgba(255, 255, 255, 0.5);
        }

        .snowflakes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 99;
        }

        .snowflake {
            position: absolute;
            background: var(--white);
            border-radius: 75%;
            opacity: 1;
            box-shadow: 0 0 5px var(--glow);
        }
    </style>

    @livewireStyles
</head>

<body class="font-sans antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-300" style="display: grid; grid-template-rows: auto 1fr auto;"
        x-data="{ down: true }">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow-md mt-2 py-2 ml-4 mr-4 rounded-md h-fit" x-show="down"
                x-on:dblclick="down = !down" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
                <div class="max-w-7xl mx-auto py-2 sm:px-4 lg:px-8 border-l-4 border-gray-800">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="flex items-center justify-center">
            {{ $slot }}
        </main>

        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <div>
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 text-center">
                    <p class="text-sm text-gray-800 dark:text-gray-100 mt-2">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}
                    </p>
                    <p class="text-sm text-gray-400 dark:text-gray-100">
                        (release) | v.{{app()->version()}}
                    </p>
                    <p class="text-sm text-gray-800 font-bold dark:text-gray-100">
                        Agencia Estatal de Vivienda - Direccion Nacional de Vivienda Social Residual.
                    </p>
                    <p class="text-sm text-gray-800 dark:text-gray-100">
                        Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </footer>
        <div class="snowflakes" id="snowflakes"></div>
    </div>
    @stack('modals')
    @stack('scripts')

    <script-disabled>
        // Create snowflakes
        const snowflakesContainer = document.getElementById('snowflakes');
        const containerWidth = window.innerWidth;
        const containerHeight = window.innerHeight;
        const snowflakeCount = 500;
        let scrollSpeedFactor = 1;

        // Create initial snowflakes
        for (let i = 0; i < snowflakeCount; i++) {
            createSnowflake();
        }

        function createSnowflake() {
            const snowflake = document.createElement('div');
            snowflake.classList.add('snowflake');

            // Randomize snowflake properties
            const size = Math.random() * 5 + 2;
            const startPositionX = Math.random() * containerWidth;
            const startPositionY = Math.random() * containerHeight * -1;
            const duration = Math.random() * 10 + 8;
            const delay = Math.random() * 5;

            // Set snowflake styles
            snowflake.style.width = `${size}px`;
            snowflake.style.height = `${size}px`;
            snowflake.style.left = `${startPositionX}px`;
            snowflake.style.top = `${startPositionY}px`;
            snowflake.style.opacity = Math.random() * 0.7 + 0.3;

            // Store snowflake properties for animation
            snowflake.setAttribute('data-speed', Math.random() * 2 + 1);
            snowflake.setAttribute('data-amplitude', Math.random() * 30 + 5);
            snowflake.setAttribute('data-x', startPositionX);
            snowflake.setAttribute('data-y', startPositionY);

            snowflakesContainer.appendChild(snowflake);
            animateSnowflake(snowflake);
        }

        function animateSnowflake(snowflake) {
            const speed = parseFloat(snowflake.getAttribute('data-speed'));
            const amplitude = parseFloat(snowflake.getAttribute('data-amplitude'));
            let x = parseFloat(snowflake.getAttribute('data-x'));
            let y = parseFloat(snowflake.getAttribute('data-y'));

            function update() {
                // Update position with scroll speed factor
                y += speed * scrollSpeedFactor;
                x += Math.sin(y / amplitude) * 0.5;

                // Set new position
                snowflake.style.transform = `translate(${Math.sin(y / 50) * amplitude}px, ${y}px)`;

                // Reset if snowflake moves out of view
                if (y > containerHeight) {
                    y = -10;
                    x = Math.random() * containerWidth;
                    snowflake.setAttribute('data-x', x);
                    snowflake.setAttribute('data-y', y);
                } else {
                    snowflake.setAttribute('data-y', y);
                }

                requestAnimationFrame(update);
            }

            update();
        }

        // Adjust snowfall speed based on scroll
        const container = document.querySelector('.container');
        if (container) {
            container.addEventListener('scroll', () => {
                const scrollPosition = container.scrollTop;
                const maxScroll = container.scrollHeight - container.clientHeight;
                scrollSpeedFactor = 1 + (scrollPosition / maxScroll) * 1.5;
            });
        }
    </script-disabled>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireScripts
</body>

</html>
