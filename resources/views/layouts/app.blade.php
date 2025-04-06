<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'تطبيق وكالات السفر') }}</title>

    <!-- الخطوط -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- بوتستراب RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- أيقونات Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- الستايلات المخصصة -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <!-- السكريبتات -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        @include('partials.header')
        
        <div class="container-fluid">
            <div class="row">
                @auth
                    <!-- القائمة الجانبية -->
                    <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                        @include('partials.sidebar')
                    </div>
                    
                    <!-- المحتوى الرئيسي -->
                    <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4">
                        @include('partials.breadcrumb')
                        @yield('content')
                    </div>
                @else
                    <div class="col-12">
                        @yield('content')
                    </div>
                @endauth
            </div>
        </div>
        
        @include('partials.footer')
    </div>
    
    <!-- سكريبت بوتستراب JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- سكريبتات مخصصة -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
