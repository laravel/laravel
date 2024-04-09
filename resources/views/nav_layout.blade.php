<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title> @yield('title')</title>
    <link rel="stylesheet" href="{{asset('js/bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('js/select.css')}}">
    <link rel="stylesheet" href="{{asset('js/sweetalert.css')}}">
    <link rel="stylesheet" href="{{asset('js/toast.css')}}">
    <link rel="stylesheet" href="{{asset('js/custom.css')}}">
    <script src="{{ asset('js/ttt.css') }}"></script>

   
</head>
<body>
   
    <div id="app">
      @include('navbar')


        <main class="py-4 bg-white">

            @yield('content')
        </main>

    </div>

    <!-- Scripts -->


<script type="text/javascript" src="{{asset('js/jquery.js')}}"></script>

<script type="text/javascript" src="{{asset('js/ajax.js')}}"></script>
<script type="text/javascript" src="{{asset('js/validate.js')}}"></script>

<script type="text/javascript" src="{{asset('js/select.js')}}"></script>
<script type="text/javascript" src="{{asset('js/simpletoggle.js')}}"></script>
<script type="text/javascript" src="{{asset('js/sweetalert.js')}}"></script>
<script type="text/javascript" src="{{asset('js/toast.js')}}"></script>
<script type="text/javascript" src="{{asset('js/bt5.js')}}"></script>
   



<!-- Latest compiled JavaScript -->
@yield('scripts')



@include('footer')


    
    

</body>
</html>