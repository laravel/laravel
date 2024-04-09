<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="/admin_panel/assets/" data-template="vertical-menu-template">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title')</title>
  <meta name="description" content="" />
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="/admin_panel/assets/img/favicon/favicon.ico" />
  <!-- Fonts -->
  <link rel="stylesheet" href="/admin_panel/assets/vendor/fonts/sans.css" />
  <!-- Icons -->
  <link rel="stylesheet" href="/admin_panel/assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="/admin_panel/assets/vendor/fonts/fontawesome.css" />
  <link rel="stylesheet" href="/admin_panel/assets/vendor/fonts/flag-icons.css" />
  <!-- Core CSS -->
  <link rel="stylesheet" href="/admin_panel/assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="/admin_panel/assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="/admin_panel/assets/css/demo.css" />
  <!-- Vendors CSS -->
  <link rel="stylesheet" href="/admin_panel/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="/admin_panel/assets/vendor/libs/typeahead-js/typeahead.css" />
  <link rel="stylesheet" href="/admin_panel/assets/vendor/libs/apex-charts/apex-charts.css" />

  <link rel="stylesheet" href="/admin_panel/assets/vendor/libs/animate-css/animate.css" />
  <link rel="stylesheet" href="/admin_panel/assets/vendor/libs/sweetalert2/sweetalert2.css" />
  <!-- Page CSS -->
  @yield('css')
  <!-- Helpers -->
  <script src="/admin_panel/assets/vendor/js/helpers.js"></script>
  <!-- Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
  <script src="/admin_panel/assets/vendor/js/template-customizer.js"></script>
  <script src="/admin_panel/assets/js/config.js"></script>
  <link rel="stylesheet" href="{{asset('/js/toast.css')}}">
  <link rel="stylesheet" href="/js/admin_custom.css" />
  <link rel="stylesheet" href="{{asset('/js/admin_custom.css')}}">
  <link rel="stylesheet" href="{{asset('/js/toast.css')}}">
</head>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->
      @include('admin.sidebar')
      <!-- / Menu -->
      <!-- Layout container -->
      <div class="layout-page">
        <!-- Navbar -->
        @include('admin.navbar')
        <!-- / Navbar -->
        <!-- Content -->
        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">
            @yield('content')
          </div>
        </div>
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
      </div>
    </div>
  </div>
  <!-- / Layout wrapper -->
  <!-- Core JS -->
  <script type="text/javascript" src="{{asset('js/jquery.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/ajax.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/toast.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/validate.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/select.js')}}"></script>
@include('scripts.admin.script_sidebar')
  
  <script src="/admin_panel/assets/vendor/libs/jquery/jquery.js"></script>
  <script src="/admin_panel/assets/vendor/libs/popper/popper.js"></script>
  <script src="/admin_panel/assets/vendor/js/bootstrap.js"></script>
  <script src="/admin_panel/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="/admin_panel/assets/vendor/libs/hammer/hammer.js"></script>
  <script src="/admin_panel/assets/vendor/libs/i18n/i18n.js"></script>
  <script src="/admin_panel/assets/vendor/libs/typeahead-js/typeahead.js"></script>
  <script src="/admin_panel/assets/vendor/js/menu.js"></script>
  <!-- Vendors JS -->
  <script src="/admin_panel/assets/vendor/libs/apex-charts/apexcharts.js"></script>
  <!-- Main JS -->
  <script src="/admin_panel/assets/js/main.js"></script>
  <!-- Page JS -->
  <script src="/admin_panel/assets/js/dashboards-analytics.js"></script>
  <script src="/admin_panel/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
  <script src="/admin_panel/assets/vendor/libs/moment/moment.js"></script>
  <script src="/admin_panel/assets/vendor/libs/flatpickr/flatpickr.js"></script>
  <script src="/admin_panel/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
  <script src="/admin_panel/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
  <script src="/admin_panel/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>

  <script src="/admin_panel/assets/vendor/libs/sweetalert2/sweetalert2.js" />

  <!-- 
    <script type="text/javascript" src="{{asset('js/jquery.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/ajax.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/toast.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/validate.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/select.js')}}"></script>
   -->

  <script type="text/javascript" src="{{asset('js/validate.js')}}"></script>
  <script src="/admin_panel/assets/js/tables-datatables-basic.js"></script>

  @yield('scripts')

</body>

</html>