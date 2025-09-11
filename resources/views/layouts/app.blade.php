<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Slip Gaji') }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div id="app">
        <!-- Navigation Bar -->
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <i class="fa fa-money"></i> {{ config('app.name', 'Slip Gaji') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        <li><a href="{{ url('/home') }}"><i class="fa fa-home"></i> Home</a></li>
                        <li><a href="{{ url('/choosedate') }}"><i class="fa fa-calendar"></i> Pilih Tanggal</a></li>
                        <li><a href="#"><i class="fa fa-users"></i> Employees</a></li>
                        <li><a href="#"><i class="fa fa-calculator"></i> Payroll</a></li>
                        <li><a href="#"><i class="fa fa-file-text"></i> Reports</a></li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if(session('user'))
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-user"></i> 
                                    {{ session('user')['name'] }}
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="#"><i class="fa fa-user"></i> Profile</a></li>
                                    <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="#" onclick="event.preventDefault(); handleLogout();">
                                            <i class="fa fa-sign-out"></i> Logout
                                        </a>
                                        <form id="logout-form" action="{{ url('/logout') }}" method="GET" style="display: none;">
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li><a href="{{ url('/login') }}"><i class="fa fa-sign-in"></i> Login</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <p>&copy; {{ date('Y') }} Slip Gaji System. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <p>Powered by Laravel & Vue.js</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <!-- jQuery harus dimuat SEBELUM Bootstrap -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- Vue.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <!-- Laravel App JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- Vue.js App -->
    <script src="{{ asset('js/vue-app.js') }}"></script>
    
    <!-- Script untuk memastikan dropdown bekerja -->
    <script>
        $(document).ready(function() {
            // Pastikan dropdown Bootstrap bekerja
            if (typeof $().dropdown === 'function') {
                $('.dropdown-toggle').dropdown();
            }
            
            // Manual toggle sebagai fallback
            $('.dropdown-toggle').off('click.manual').on('click.manual', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var $dropdown = $(this).closest('.dropdown');
                var $menu = $dropdown.find('.dropdown-menu');
                
                // Close all other dropdowns
                $('.dropdown').not($dropdown).removeClass('open');
                
                // Toggle current dropdown
                $dropdown.toggleClass('open');
                
                return false;
            });
            
            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown').removeClass('open');
                }
            });
            
            // Prevent dropdown from closing when clicking inside
            $('.dropdown-menu').on('click', function(e) {
                e.stopPropagation();
            });
            
            // Debug info
            console.log('jQuery version:', $.fn.jquery);
            console.log('Bootstrap dropdown available:', typeof $().dropdown !== 'undefined');
            console.log('Dropdown elements found:', $('.dropdown-toggle').length);
        });
        
        // Function untuk handle logout
        function handleLogout() {
            // Bersihkan localStorage
            localStorage.removeItem('selectedStartDate');
            localStorage.removeItem('selectedEndDate');
            localStorage.clear();
            
            // Redirect ke logout
            window.location.href = '{{ url("/logout") }}';
        }
    </script>
    
    <!-- CSS tambahan untuk dropdown -->
    <style>
        /* Dropdown base styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            display: none;
            float: left;
            min-width: 160px;
            padding: 5px 0;
            margin: 2px 0 0;
            font-size: 14px;
            text-align: left;
            list-style: none;
            background-color: #fff;
            border: 1px solid #ccc;
            border: 1px solid rgba(0,0,0,.15);
            border-radius: 4px;
            box-shadow: 0 6px 12px rgba(0,0,0,.175);
            background-clip: padding-box;
        }
        
        .dropdown.open .dropdown-menu {
            display: block;
        }
        
        .dropdown-menu > li > a {
            display: block;
            padding: 3px 20px;
            clear: both;
            font-weight: normal;
            line-height: 1.42857143;
            color: #333;
            white-space: nowrap;
            text-decoration: none;
        }
        
        .dropdown-menu > li > a:hover,
        .dropdown-menu > li > a:focus {
            color: #262626;
            text-decoration: none;
            background-color: #f5f5f5;
        }
        
        .dropdown-menu .divider {
            height: 1px;
            margin: 9px 0;
            overflow: hidden;
            background-color: #e5e5e5;
        }
        
        .navbar .dropdown-menu {
            margin-top: 0;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
        
        .navbar-right .dropdown-menu {
            right: 0;
            left: auto;
        }
        
        /* Hover effect untuk dropdown toggle */
        .dropdown-toggle:hover {
            background-color: #e7e7e7;
            text-decoration: none;
        }
        
        /* Caret styling */
        .caret {
            display: inline-block;
            width: 0;
            height: 0;
            margin-left: 2px;
            vertical-align: middle;
            border-top: 4px dashed;
            border-top: 4px solid \9;
            border-right: 4px solid transparent;
            border-left: 4px solid transparent;
        }
    </style>
</body>
</html>
