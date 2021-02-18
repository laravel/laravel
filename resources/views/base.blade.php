<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
	<div class="base flex flex-col min-h-screen justify-between">
		@include('layouts.header')
		@yield('content')
		@include('layouts.footer')
	</div>
    @include('cookieConsent::index')
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('js')
</body>

</html>
