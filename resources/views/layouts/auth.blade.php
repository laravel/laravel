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
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .login-box {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        
        .login-header {
            background: linear-gradient(45deg, #3097d1, #2ab27b);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        
        .login-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 24px;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 15px;
            border: 2px solid #e1e5e9;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: #3097d1;
            box-shadow: 0 0 0 0.2rem rgba(48, 151, 209, 0.25);
            outline: none;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #3097d1, #2ab27b);
            border: none;
            border-radius: 8px;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            background: linear-gradient(45deg, #2ab27b, #3097d1);
        }
        
        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border: none;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-info {
            background-color: #cce7ff;
            color: #004085;
            font-size: 12px;
        }
        
        .demo-credentials {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid #3097d1;
        }
        
        .demo-credentials h5 {
            margin: 0 0 15px 0;
            color: #3097d1;
            font-weight: 600;
        }
        
        .credential-item {
            background: white;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
        }
        
        .credential-item:last-child {
            margin-bottom: 0;
        }
        
        @media (max-width: 768px) {
            .login-body {
                padding: 30px 20px;
            }
            
            .container {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div id="app">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
