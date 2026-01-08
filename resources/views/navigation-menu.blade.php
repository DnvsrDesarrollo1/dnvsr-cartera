<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-[90rem] mx-auto sm:px-2 lg:px-4">
        <div class="flex justify-between h-16">
            <!-- Left Navigation Links -->
            <div class="hidden sm:flex sm:items-center">
                <div class="flex space-x-8">
                    <x-nav-link href="{{ route('proyecto.index') }}" :active="request()->routeIs('proyecto.*')"
                        class="transition duration-150 ease-in-out">
                        {{ 'Proyectos' }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('beneficiario.index') }}" :active="request()->routeIs('beneficiario.*')"
                        class="transition duration-150 ease-in-out">
                        {{ 'Beneficiarios' }}
                    </x-nav-link>
                    {{-- <x-nav-link href="{{ route('bi.index') }}" :active="request()->routeIs('bi.*')"
                        class="transition duration-150 ease-in-out">
                        {{ 'B.I.' }}
                    </x-nav-link> --}}
                </div>
            </div>

            <!-- Logo (Centered) -->
            <div class="flex items-center justify-center flex-1">
                <div class="flex-shrink-0">
                    <a href="{{ route('importaciones') }}" class="flex items-center">
                        <x-application-mark class="block h-10 w-auto" />
                    </a>
                </div>
            </div>

            <!-- Right Elements (User, Theme, etc) -->
            <div class="hidden sm:flex sm:items-center">
                <!-- Theme Toggle Button -->

                <!-- Settings Dropdown -->
                <div class="relative ml-3">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button title="{{ Auth::user()->name }}"
                                    class="flex text-sm border-2 border-green-600 rounded-full focus:outline-none focus:border-green-300 transition">
                                    <img class="h-10 w-10 rounded-full object-cover"
                                        src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                        {{ Auth::user()->name }}

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ 'Opciones' }}
                            </div>

                            <x-dropdown-link href="{{ route('importaciones') }}">
                                {{ 'Carga de CSV' }}
                            </x-dropdown-link>

                            @can(abilities: 'write users')
                                <x-dropdown-link href="{{ route('users.index') }}">
                                    {{ 'Gestionar Usuarios' }}
                                </x-dropdown-link>
                            @endcan

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ 'Mi Perfil' }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ 'API Tokens' }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link @class(['font-bold']) href="{{ route('logout') }}"
                                    @click.prevent="$root.submit();">
                                    {{ 'Cerrar Sesión' }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Notifications -->
                <div class="ml-3 flex items-center space-x-4">
                    {{-- <button id="theme-toggle" type="button"
                        class="mr-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-sm p-2.5 transition duration-150 ease-in-out">
                        <svg id="theme-toggle-dark-icon" class="text-yellow-600 hidden w-5 h-5" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="text-blue-800 hidden w-5 h-5" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </button> --}}
                    <livewire:plans-notification lazy="on-load" />
                    <livewire:notifications-manager lazy="on-load" />
                </div>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <!-- Mobile Navigation Links -->
            <x-responsive-nav-link href="{{ route('proyecto.index') }}" :active="request()->routeIs('proyecto.*')">
                {{ 'Proyectos' }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('beneficiario.index') }}" :active="request()->routeIs('beneficiario.*')">
                {{ 'Beneficiarios' }}
            </x-responsive-nav-link>
            {{-- <x-responsive-nav-link href="{{ route('bi.index') }}" :active="request()->routeIs('bi.*')">
                {{ 'B.I.' }}
            </x-responsive-nav-link> --}}

            <!-- Mobile Theme Toggle -->
            <div class="px-4 py-2 flex">
                <button id="mobile-theme-toggle" type="button"
                    class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-sm p-2.5 transition duration-150 ease-in-out">
                    <svg id="mobile-theme-toggle-dark-icon" class="text-yellow-600 hidden w-5 h-5" fill="currentColor"
                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                            fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                    <svg id="mobile-theme-toggle-light-icon" class="text-blue-800 hidden w-5 h-5" fill="currentColor"
                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <span class="ml-2">Cambiar tema</span>
                </button>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 mr-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                            alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->

                <x-responsive-nav-link href="{{ route('importaciones') }}" :active="request()->routeIs('importaciones')">
                    {{ 'Carga de Datos' }}
                </x-responsive-nav-link>

                @can('write users')
                    <x-responsive-nav-link href="{{ route('users.index') }}">
                        {{ 'Gestionar Usuarios' }}
                    </x-responsive-nav-link>
                @endcan

                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ 'Mi Cuenta' }}
                </x-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ 'API Tokens' }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                        {{ 'Cerrar Sesión' }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>

    <!-- Decoracion navideña, retirar despues del 6 de enero LOL -->
    <div class="absolute top-[58px] left-0 w-full flex justify-between overflow-hidden pointer-events-none z-50"
        style="display:none; transform: translateY(6px);">
        @foreach (range(1, 80) as $i)
            <svg {{-- style="animation-delay: {{ $i * 10 }}ms;" --}}
                class="flex-shrink-0 transition-all {{ $i % 2 == 0 ? 'text-green-600 w-4 h-4 animate-duration-1000 animate-bounce' : 'text-red-600 w-5 h-5 animate-duration-1000 animate-pulse' }} drop-shadow-sm"
                fill="currentColor" viewBox="144 144 512 512" xmlns="http://www.w3.org/2000/svg">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                    <path
                        d="m400.01 303.43c20.645 0 40.43 3.6445 58.766 10.324v-6.1055c0-6.4844-2.6602-12.379-6.9375-16.656-3.6289-3.6289-8.418-6.0898-13.738-6.7578l-0.22656-0.03125-0.074219-0.015625c-0.86328-0.089844-1.7383-0.13672-2.6289-0.13672h-70.344c-0.89062 0-1.7695 0.046875-2.6289 0.13672l-0.074218 0.015625-0.22656 0.03125c-5.3203 0.66406-10.113 3.1289-13.738 6.7578-4.2773 4.2773-6.9375 10.188-6.9375 16.656v6.1055c18.332-6.6797 38.117-10.324 58.766-10.324zm-11.562-95.539v-43.062c0-6.3789 5.1836-11.562 11.562-11.562 6.3789 0 11.562 5.1836 11.562 11.562v43.062c9.3555 2.1914 17.746 6.9961 24.336 13.586 9.2031 9.2031 14.918 21.914 14.918 35.91v6.2422c6.5898 2.3594 12.5 6.1523 17.352 11.004 8.4648 8.4648 13.707 20.148 13.707 33.012v16.535c14.555 7.9219 27.855 17.879 39.48 29.504 31.059 31.059 50.27 73.969 50.27 121.37s-19.211 90.309-50.27 121.37c-31.059 31.059-73.969 50.27-121.37 50.27-47.398 0-90.309-19.211-121.37-50.27-31.059-31.059-50.27-73.969-50.27-121.37s19.211-90.309 50.27-121.37c11.621-11.621 24.91-21.582 39.48-29.504v-16.535c0-12.863 5.2617-24.547 13.707-33.012 4.8516-4.8516 10.762-8.6445 17.352-11.004v-6.2422c0-13.996 5.7148-26.707 14.918-35.91 6.5898-6.5898 14.977-11.395 24.336-13.586zm39.266 53.035v-3.5352c0-7.6016-3.1133-14.523-8.1484-19.559-5.0195-5.0195-11.957-8.1484-19.559-8.1484-7.6016 0-14.523 3.1289-19.559 8.1484-5.0312 5.0312-8.1484 11.957-8.1484 19.559v3.5352zm-171.58 251.16 28.113-19.285c4.1094-2.8125 9.418-2.6016 13.242 0.15234l47.898 32.859 48.125-33.012c4.1094-2.8125 9.4023-2.6016 13.242 0.15234l47.898 32.859 48.125-33.012c4.1094-2.8125 9.4023-2.6016 13.242 0.15234l27.887 19.121c3.0391-11.82 4.6406-24.227 4.6406-37 0-3.1289-0.10547-6.2422-0.28906-9.3242l-38.98-26.738-47.898 32.859c-3.8398 2.75-9.1289 2.9609-13.242 0.15234l-48.125-33.012-47.898 32.859c-3.8242 2.75-9.1289 2.9609-13.242 0.15234l-48.109-33.012-38.98 26.738c-0.19531 3.082-0.28906 6.1953-0.28906 9.3242 0 12.773 1.6172 25.18 4.6406 37zm279.93 22.641-26.812-18.395-47.898 32.859c-3.8398 2.7344-9.1289 2.9609-13.242 0.15234l-48.125-33.012-47.898 32.859c-3.8242 2.7344-9.1289 2.9609-13.242 0.15234l-48.109-33.012-26.812 18.395c7.4805 17.035 18.062 32.406 31.047 45.387 26.875 26.875 64.008 43.5 105.02 43.5 41.004 0 78.141-16.625 105.02-43.5 12.969-12.984 23.562-28.34 31.047-45.387zm-278.83-100.71 27.008-18.531c4.1094-2.8125 9.418-2.5859 13.242 0.15234l47.898 32.859 48.125-33.012c4.1094-2.8125 9.4023-2.5859 13.242 0.15234l47.898 32.859 48.125-33.012c4.1094-2.8125 9.4023-2.5859 13.242 0.15234l26.781 18.363c-7.0273-24.5-20.207-46.418-37.742-63.949-26.875-26.875-64.008-43.5-105.02-43.5-41.004 0-78.141 16.625-105.02 43.5-17.531 17.547-30.711 39.449-37.742 63.949z">
                    </path>
                </g>
            </svg>
        @endforeach
    </div>
</nav>
