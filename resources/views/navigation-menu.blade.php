<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-[90rem] mx-auto sm:px-2 lg:px-4">
        <div class="flex justify-between h-16">
            <!-- Left Navigation Links -->
            <div class="hidden sm:flex sm:items-center">
                <div class="flex space-x-8">
                    {{-- <x-nav-link href="{{ route('importaciones') }}" :active="request()->routeIs('importaciones')" class="transition duration-150 ease-in-out">
                        {{ ('Panel resumen') }}
                    </x-nav-link> --}}
                    <x-nav-link href="{{ route('proyecto.index') }}" :active="request()->routeIs('proyecto.*')"
                        class="transition duration-150 ease-in-out">
                        {{ 'Proyectos' }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('importaciones') }}" :active="request()->routeIs('importaciones')"
                        class="transition duration-150 ease-in-out">
                        {{ 'Carga de Datos' }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('beneficiario.index') }}" :active="request()->routeIs('beneficiario.*')"
                        class="transition duration-150 ease-in-out">
                        {{ 'Beneficiarios' }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('bi.index') }}" :active="request()->routeIs('bi.*')"
                        class="transition duration-150 ease-in-out">
                        {{ 'B.I.' }}
                    </x-nav-link>
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
                <button id="theme-toggle" type="button"
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
                </button>

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
                                {{ 'Manage Account' }}
                            </div>

                            @can('write beneficiaries')
                                <x-dropdown-link href="{{ route('users.index') }}">
                                    {{ 'Usuarios' }}
                                </x-dropdown-link>
                            @endcan

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ 'Profile' }}
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
                                    {{ 'Log Out' }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Notifications -->
                <div class="ml-3 flex items-center space-x-2">
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
            <x-responsive-nav-link href="{{ route('importaciones') }}" :active="request()->routeIs('importaciones')">
                {{ 'Carga de Datos' }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('beneficiario.index') }}" :active="request()->routeIs('beneficiario.*')">
                {{ 'Beneficiarios' }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('bi.index') }}" :active="request()->routeIs('bi.*')">
                {{ 'B.I.' }}
            </x-responsive-nav-link>

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
                @can('write beneficiaries')
                    <x-responsive-nav-link href="{{ route('users.index') }}">
                        {{ 'Usuarios' }}
                    </x-responsive-nav-link>
                @endcan

                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ 'Profile' }}
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
                        {{ 'Log Out' }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
