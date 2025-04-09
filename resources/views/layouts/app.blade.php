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
    
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }
        .dropdown-menu {
            text-align: right;
        }
        .main-content {
            min-height: calc(100vh - 160px);
        }
        .breadcrumb {
            background-color: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-left: 10px;
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.6rem;
        }
        .welcome-banner {
            background: linear-gradient(135deg, #4a90e2, #825ee4);
            color: white;
            padding: 3rem 0;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        .feature-card {
            transition: transform 0.3s;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #4a90e2;
        }
        .navbar-brand img {
            height: 40px;
        }
        .navbar-nav .nav-link.active {
            color: #4a90e2;
            font-weight: bold;
        }
        .sidebar {
            min-height: calc(100vh - 72px);
            background-color: #f8f9fa;
            position: sticky;
            top: 72px;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #e9ecef;
            color: #4a90e2;
        }
        @media (max-width: 767.98px) {
            .sidebar {
                position: static;
                margin-bottom: 20px;
            }
        }
        .icon-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'وكالات السفر') }}" onerror="this.src='https://via.placeholder.com/120x40?text=وكالات السفر'">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                                <i class="fas fa-home me-1"></i> الرئيسية
                            </a>
                        </li>
                        
                        @auth
                            @if(auth()->user()->isAgency())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('agency.dashboard') ? 'active' : '' }}" href="{{ route('agency.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                                    </a>
                                </li>
                            @elseif(auth()->user()->isSubagent())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('subagent.dashboard') ? 'active' : '' }}" href="{{ route('subagent.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                                    </a>
                                </li>
                            @elseif(auth()->user()->isCustomer())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                                    </a>
                                </li>
                            @endif
                        @endauth
                        
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-info-circle me-1"></i> عن الموقع
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-envelope me-1"></i> اتصل بنا
                            </a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="btn btn-outline-primary mx-1" href="{{ route('login') }}">
                                        <i class="fas fa-sign-in-alt me-1"></i> {{ __('تسجيل الدخول') }}
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="btn btn-primary mx-1" href="{{ route('register') }}">
                                        <i class="fas fa-user-plus me-1"></i> {{ __('التسجيل') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <div class="user-avatar">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <span class="ms-2">{{ Auth::user()->name }}</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <span class="dropdown-item-text text-muted">
                                        <small>
                                            @if(auth()->user()->isAgency())
                                                <i class="fas fa-building me-1"></i> وكيل رئيسي
                                            @elseif(auth()->user()->isSubagent())
                                                <i class="fas fa-user-tie me-1"></i> سبوكيل
                                            @elseif(auth()->user()->isCustomer())
                                                <i class="fas fa-user me-1"></i> عميل
                                            @endif
                                        </small>
                                    </span>
                                    <div class="dropdown-divider"></div>
                                    
                                    @if(auth()->user()->isAgency())
                                        <a class="dropdown-item" href="{{ route('agency.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                                        </a>
                                    @elseif(auth()->user()->isSubagent())
                                        <a class="dropdown-item" href="{{ route('subagent.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                                        </a>
                                    @elseif(auth()->user()->isCustomer())
                                        <a class="dropdown-item" href="{{ route('customer.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                                        </a>
                                    @endif
                                    
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user-edit me-1"></i> الملف الشخصي
                                    </a>
                                    
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-1"></i> {{ __('تسجيل الخروج') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            
                            <!-- Notifications Icon -->
                            <li class="nav-item dropdown">
                                <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bell"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge" style="{{ auth()->user()->notifications()->unread()->count() ? '' : 'display: none;' }}">
                                        {{ auth()->user()->notifications()->unread()->count() }}
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 300px; max-height: 400px; overflow-y: auto;">
                                    <li><span class="dropdown-item-text fw-bold">الإشعارات</span></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <div id="notifications-container">
                                        <!-- سيتم تحميل الإشعارات هنا -->
                                        <div class="text-center p-3">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                <span class="visually-hidden">جاري التحميل...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-center" href="{{ route('notifications.index') }}">عرض كل الإشعارات</a></li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @if(request()->routeIs('agency.*') || request()->routeIs('subagent.*') || request()->routeIs('customer.*'))
                <div class="container-fluid">
                    <div class="row">
                        <!-- Sidebar -->
                        <div class="col-md-3 col-lg-2 sidebar">
                            @include('partials.sidebar')
                        </div>
                        
                        <!-- Main content -->
                        <div class="col-md-9 col-lg-10 px-md-4">
                            <!-- Breadcrumb -->
                            <nav aria-label="breadcrumb" class="mb-4">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
                                    @yield('breadcrumb')
                                </ol>
                            </nav>
                            
                            <!-- Content -->
                            @yield('content')
                        </div>
                    </div>
                </div>
            @else
                <div class="container">
                    @yield('content')
                </div>
            @endif
        </main>
        
        <footer class="bg-dark text-white py-4 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <h5>وكالات السفر</h5>
                        <p>نظام متكامل لإدارة وكالات السفر والسبوكلاء والعملاء</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5>روابط سريعة</h5>
                        <ul class="list-unstyled">
                            <li><a href="{{ url('/') }}" class="text-white">الرئيسية</a></li>
                            <li><a href="#" class="text-white">عن الموقع</a></li>
                            <li><a href="#" class="text-white">اتصل بنا</a></li>
                            <li><a href="#" class="text-white">الشروط والأحكام</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5>تواصل معنا</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-envelope me-2"></i> info@travelagency.com</li>
                            <li><i class="fas fa-phone me-2"></i> +966 55 123 4567</li>
                            <li><i class="fas fa-map-marker-alt me-2"></i> الرياض، المملكة العربية السعودية</li>
                        </ul>
                        <div class="mt-3">
                            <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} وكالات السفر. جميع الحقوق محفوظة.</p>
                    <div class="version">
                        Version: {{ config('app.version') }}
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Scripts -->
    @stack('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
    
    <script>
        // حل مشكلة استمرار حالة التحميل في المتصفح
        document.addEventListener('DOMContentLoaded', function() {
            // التأكد من إيقاف مؤشر التحميل بعد فترة معينة
            setTimeout(function() {
                if (document.readyState !== 'complete') {
                    console.log('إيقاف حالة التحميل المستمرة');
                    window.stop();
                    document.dispatchEvent(new Event('readystatechange'));
        });         window.dispatchEvent(new Event('load'));
    </script>   }
</body>     }, 8000);
</html> });
    </script>
</body>
</html>
