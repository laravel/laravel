<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
        <!-- Sweet Alert -->
        <link type="text/css" href="/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
        @livewireStyles 
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/scss/app.scss', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">

    <!-- Page Content -->
    <!-- SideNav -->
    @include('layouts.partials.sidenav')

    <main class="content">
        <livewire:layout.navigation />
        <!-- Page Heading -->
        @if (isset($header))
            <header class="card card-body border-0 mb-2 p-5">
                <div class="text-secondary">
                    {{ $header }}
                </div>
            </header>
        @endif

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible custom-alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <h5><i class="icon fa fa-check"></i> Success!</h5>
                {{ $message }}
            </div>
        @endif
        @if ($message = Session::get('danger'))
            <div class="alert alert-danger alert-dismissible custom-alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <h5><i class="icon fa fa-check"></i> Errors!</h5>
                {{ $message }}
            </div>
        @endif

        {{ $slot }}

        {{-- Footer --}}
        @include('layouts.partials.footer')
    </main>     
    @livewireScripts
    @stack('scripts')
    </body>
</html>
