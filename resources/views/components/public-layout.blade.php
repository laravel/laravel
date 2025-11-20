<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'inClouding') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700|noto-kufi-arabic:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-900 text-white overflow-x-hidden selection:bg-blue-500 selection:text-white">
    
    <!-- 3D Background Container -->
    <div id="canvas-container" class="fixed inset-0 -z-10 opacity-40 pointer-events-none"></div>

    <!-- Navigation -->
    <nav x-data="{ mobileMenuOpen: false, scrolled: false }" 
         @scroll.window="scrolled = (window.pageYOffset > 20)"
         :class="{ 'bg-slate-900/80 backdrop-blur-md border-b border-white/10': scrolled, 'bg-transparent': !scrolled }"
         class="fixed w-full z-50 transition-all duration-300 top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-2">
                        <img class="h-10 w-auto" src="{{ asset('images/logo.png') }}" alt="inClouding">
                        <span class="font-bold text-xl tracking-tight hidden sm:block">inClouding</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8 rtl:space-x-reverse">
                    <a href="#services" class="text-slate-300 hover:text-white transition-colors">{{ __('Services') }}</a>
                    <a href="#pricing" class="text-slate-300 hover:text-white transition-colors">{{ __('Pricing') }}</a>
                    <a href="#about" class="text-slate-300 hover:text-white transition-colors">{{ __('About Us') }}</a>
                    <a href="#contact" class="text-slate-300 hover:text-white transition-colors">{{ __('Contact') }}</a>
                </div>

                <!-- Right Side (Lang + Auth) -->
                <div class="hidden md:flex items-center space-x-4 rtl:space-x-reverse">
                    <!-- Language Switcher -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center text-slate-300 hover:text-white">
                            <span class="uppercase">{{ app()->getLocale() }}</span>
                            <svg class="w-4 h-4 ml-1 rtl:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 rtl:left-0 mt-2 w-24 bg-slate-800 rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 focus:outline-none">
                            <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white">English</a>
                            <a href="{{ route('lang.switch', 'ar') }}" class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white">العربية</a>
                        </div>
                    </div>

                    <!-- Theme Switcher -->
                    <div x-data="initThemeSwitcher()" x-init="init()">
                        <button @click="toggle()" class="p-2 rounded-lg hover:bg-slate-800 dark:hover:bg-slate-700 transition-colors" title="Toggle theme">
                            <!-- Sun icon (visible in dark mode) -->
                            <svg x-show="theme === 'dark'" class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <!-- Moon icon (visible in light mode) -->
                            <svg x-show="theme === 'light'" class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Auth Buttons -->
                    @if (Route::has('login'))
                        <div class="flex items-center space-x-4 rtl:space-x-reverse">
                            @auth
                                <a href="{{ url('/admin') }}" class="text-sm font-semibold text-slate-300 hover:text-white">{{ __('Dashboard') }}</a>
                            @else
                                <a href="{{ url('/admin/login') }}" class="text-sm font-semibold text-slate-300 hover:text-white">{{ __('Login') }}</a>
                            @endauth
                        </div>
                    @endif
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-300 hover:text-white p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden bg-slate-900 border-b border-white/10">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#services" class="block px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800">{{ __('Services') }}</a>
                <a href="#pricing" class="block px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800">{{ __('Pricing') }}</a>
                <a href="#about" class="block px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800">{{ __('About Us') }}</a>
                <a href="#contact" class="block px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800">{{ __('Contact') }}</a>
                
                <div class="border-t border-slate-700 my-2"></div>
                
                <div class="px-3 py-2">
                    <p class="text-xs text-slate-500 uppercase font-semibold mb-2">Language</p>
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <a href="{{ route('lang.switch', 'en') }}" class="text-slate-300 hover:text-white {{ app()->getLocale() == 'en' ? 'font-bold text-white' : '' }}">English</a>
                        <a href="{{ route('lang.switch', 'ar') }}" class="text-slate-300 hover:text-white {{ app()->getLocale() == 'ar' ? 'font-bold text-white' : '' }}">العربية</a>
                    </div>
                </div>

                @if (Route::has('login'))
                    <div class="border-t border-slate-700 my-2"></div>
                    @auth
                        <a href="{{ url('/admin') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800">{{ __('Dashboard') }}</a>
                    @else
                        <a href="{{ url('/admin/login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800">{{ __('Login') }}</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    <footer class="bg-slate-950 py-12 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <img class="h-8 w-auto" src="{{ asset('images/logo.png') }}" alt="inClouding">
                        <span class="font-bold text-xl text-white">inClouding</span>
                    </div>
                    <p class="text-slate-400 max-w-sm">
                        {{ __('Your premium partner for managed cloud services. We handle the tech, you focus on growth.') }}
                    </p>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-4">{{ __('Services') }}</h3>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="#" class="hover:text-blue-400">Managed Hosting</a></li>
                        <li><a href="#" class="hover:text-blue-400">Web Development</a></li>
                        <li><a href="#" class="hover:text-blue-400">SEO & Marketing</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-4">{{ __('Company') }}</h3>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="#" class="hover:text-blue-400">{{ __('About Us') }}</a></li>
                        <li><a href="#" class="hover:text-blue-400">{{ __('Contact') }}</a></li>
                        <li><a href="#" class="hover:text-blue-400">{{ __('Privacy Policy') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-white/5 text-center text-slate-500 text-sm">
                &copy; {{ date('Y') }} inClouding.net. {{ __('All rights reserved.') }}
            </div>
        </div>
    </footer>

</body>
</html>
