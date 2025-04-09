<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ session('textDirection', 'rtl') }}" class="{{ session('theme', 'light') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RTLA') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/v2/app.css') }}">
    @stack('styles')

    <!-- Scripts -->
    <script src="{{ asset('js/v2/app.js') }}" defer></script>
    <script>
        window.darkModeSettings = @json(config('v2_features.dark_mode'));
        window.userId = "{{ auth()->id() }}";
    </script>
    @stack('scripts')
</head>
<body class="font-sans antialiased min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div id="app">
        <!-- Navigation Header -->
        @include('layouts.v2.navigation')

        <!-- Language Selector -->
        @if(config('v2_features.multilingual.enabled'))
            @include('layouts.v2.language-selector')
        @endif

        <!-- Page Content -->
        <main class="container mx-auto py-4 px-4 sm:px-6 lg:px-8">
            @include('layouts.v2.flash-messages')
            @yield('content')
        </main>

        <!-- Footer -->
        @include('layouts.v2.footer')
    </div>

    <!-- Dark Mode Toggle -->
    @if(config('v2_features.dark_mode.enabled'))
        <button 
            id="dark-mode-toggle" 
            type="button" 
            class="fixed bottom-4 left-4 p-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 shadow-lg"
            aria-label="{{ __('v2.toggle_dark_mode') }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    @endif
</body>
</html>
