<!DOCTYPE html>
<html lang="es">
<?php 
$template = "Gp";
?>

<head>
    @include("templates.{$template}.head")

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS Includes -->
    @yield('cssincludes')

    <!-- MANDATORY Here -->
    <!-- Tabler style -->
    <link href="{{ asset('zentro/extra/tablericons/tabler-icons.min.css') }}" rel="stylesheet">
    <!-- Zentro Utilities -->
    <script src="{{ asset('zentro/zentro.js') }}"></script>
    <!-- Zentro Web3Modal -->
    <script src="{{ asset('zentro/walletconnect.js') }}" type="module"></script>
</head>

<body id="@yield('bodyid')" class="@yield('bodycss')">

    @yield('content')

    @yield('jsincludes')

    <!-- Toastr & Swal Message-->
    @include("include.script.alerts")
    @include("templates.{$template}.footer")

</body>

</html>