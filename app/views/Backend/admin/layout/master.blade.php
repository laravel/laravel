<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->

    <head>
        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Bootstrap Stylesheet -->
    <link rel="stylesheet" href="{{ URL::to('Backend/bootstrap/css/bootstrap.min.css') }}" media="all">

        <!-- jquery-ui Stylesheets -->
        <link rel="stylesheet" href="{{ URL::to('Backend/assets/jui/css/jquery-ui.css') }}" media="screen">
            <link rel="stylesheet" href="{{ URL::to('Backend/assets/jui/jquery-ui.custom.css') }}" media="screen">
                <link rel="stylesheet" href="{{ URL::to('Backend/assets/jui/timepicker/jquery-ui-timepicker.css') }}" media="screen">

                    <!-- Uniform Stylesheet -->
                    <link rel="stylesheet" href="{{ URL::to('Backend/plugins/uniform/css/uniform.default.css') }}" media="screen">
                        <!-- Pnotify -->
                        {{HTML::style('Backend/plugins/pnotify/jquery.pnotify.css')}}

                        <!-- End Plugin Stylesheets -->

                        <!-- Main Layout Stylesheet -->
                        <link rel="stylesheet" href="{{ URL::to('Backend/assets/css/fonts/icomoon/style.css') }}" media="screen">
                            <link rel="stylesheet" href="{{ URL::to('Backend/assets/css/main-style.css') }}" media="screen">

                                <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
                                <!--[if lt IE 9]>
                                <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
                                <![endif]-->
                                @yield('css')
                                <title>@if(isset($title))Laravel Eğitim Serisi V - {{$title}} @else Laravel Eğitim Serisi V @endif</title>

                                </head>

                                <body data-show-sidebar-toggle-button="true" data-fixed-sidebar="false">
                                <div id="wrapper">
                                    @include('backend.admin.layout.header')
                                    <div id="content-wrap">
                                        <div id="content">
                                            <div id="content-outer">
                                                <div id="content-inner">
                                                    @include('backend.admin.layout.sidebar')
                                                    <section id="main" class="clearfix">
                                                        @include('backend.admin.layout.breadcrumb')	
                                                        <div id="main-content">
                                                            @yield('content')
                                                        </div>   
                                                    </section>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @include('backend.admin.layout.footer')   
                                </div>
                                <!-- Core Scripts -->
                                <script src="{{ URL::to('Backend/assets/js/libs/jquery-1.8.3.min.js') }}"></script>
                                <script src="{{ URL::to('Backend/bootstrap/js/bootstrap.min.js') }}"></script>
                                <script src="{{ URL::to('Backend/assets/js/libs/jquery.placeholder.min.js') }}"></script>
                                <script src="{{ URL::to('Backend/assets/js/libs/jquery.mousewheel.min.js') }}"></script>

                                <!-- Template Script -->
                                <script src="{{ URL::to('Backend/assets/js/template.js') }}"></script>
                                <script src="{{ URL::to('Backend/assets/js/setup.js') }}"></script>

                                <!-- Uniform Script -->
                                <script src="{{ URL::to('Backend/plugins/uniform/jquery.uniform.min.js') }}"></script>

                                <!-- jquery-ui Scripts -->
                                <script src="{{ URL::to('Backend/assets/jui/js/jquery-ui-1.9.2.min.js') }}"></script>
                                <script src="{{ URL::to('Backend/assets/jui/jquery-ui.custom.min.js') }}"></script>
                                <script src="{{ URL::to('Backend/assets/jui/timepicker/jquery-ui-timepicker.min.js') }}"></script>
                                <script src="{{ URL::to('Backend/assets/jui/jquery.ui.touch-punch.min.js') }}"></script>

                                <!-- Demo Scripts -->
                                <script src="{{ URL::to('Backend/assets/js/demo/dashboard.js') }}"></script>
                                <!-- Pnotify -->
                                {{HTML::script('Backend/plugins/pnotify/jquery.pnotify.min.js')}}

                                @if(Session::has('mesaj'))
                                <script type="text/javascript">
                                        $.pnotify.defaults.history = false;
                                        $.pnotify({
                                                    title: '{{Session::get("title")}}',
                                                    text: '{{Session::get("text")}}',
                                                    type: '{{Session::get("type")}}'
                                                    });
                                </script>
                                @endif
                                @yield('js')
                                </body>

                                </html>
