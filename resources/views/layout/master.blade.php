<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset_path('stylesheets/app.css') }}">

    @stack('css')
</head>
    <body>
        <main class="content">
            @section('content')
            @show
        </main>

        <script type="text/javascript" src="{{ asset_path('runtime.js') }}"></script>
        <script type="text/javascript" src="{{ asset_path('javascript/script.js') }}"></script>

        @stack('js')
    </body>
</html>

