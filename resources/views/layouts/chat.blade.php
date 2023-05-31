<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.partials.title-meta')

        @stack('styles')

        @include('layouts.partials.head-css')
    </head>
    <body>
        {{ $slot }}

        @include('layouts.partials.vendor-scripts')

        @stack('scripts')

        @vite('resources/assets/js/app.js')
    </body>
</html>
