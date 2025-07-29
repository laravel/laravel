@extends('layouts.html')

@section('content')

    @yield('topbar')

    @yield('sidebar')

    <main id="main" class="main" style="@yield('mainstyle')">

        @yield('maincontent')

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer" style="@yield('mainstyle')">
        <div class="copyright">
            &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
            Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
        </div>
    </footer><!-- End Footer -->

@endsection

@section('cssincludes')
    @include("templates.NiceAdmin.head")
    @yield('layoutcssincludes')
@endsection

@section('jsincludes')
    @include("templates.NiceAdmin.footer")
    @yield('layoutjsincludes')
@endsection