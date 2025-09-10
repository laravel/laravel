<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Slip Gaji') }} - Login</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-top: 50px;
        }
        
        .panel-default {
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .panel-heading {
            background: linear-gradient(45deg, #3097d1, #2ab27b);
            color: white;
            border: none;
            padding: 20px;
        }
        
        .panel-body {
            padding: 30px;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #3097d1, #2ab27b);
            border: none;
            border-radius: 25px;
            padding: 12px 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .form-control {
            border-radius: 25px;
            padding: 12px 15px;
            border: 2px solid #eee;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #3097d1;
            box-shadow: 0 0 10px rgba(48, 151, 209, 0.2);
        }
        
        .input-group-addon {
            border-radius: 25px 0 0 25px;
            border: 2px solid #eee;
            border-right: none;
            background: #f8f9fa;
        }
        
        .well {
            background: #f8f9fa;
            border-radius: 10px;
            border: none;
        }
        
        .panel-footer {
            background: #f8f9fa;
            border: none;
            padding: 15px;
        }
    </style>
</head>
<body>
    @yield('content')

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
