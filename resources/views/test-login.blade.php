<!DOCTYPE html>
<html>
<head>
    <title>Test Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 200px; padding: 8px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        .alert { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <h1>Test Login Page</h1>
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <form method="POST" action="/login">
        {{ csrf_field() }}
        
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Login</button>
    </form>
    
    <hr>
    <h3>Demo Credentials:</h3>
    <p><strong>Username:</strong> admin <br><strong>Password:</strong> admin123</p>
        
    <hr>
    <h3>Current Session:</h3>
    @if(session('user'))
        <p>Logged in as: {{ session('user')['name'] }} ({{ session('user')['username'] }})</p>
        <p>Role: {{ session('user')['role'] }}</p>
        <p>Login time: {{ session('user')['login_time'] }}</p>
        <a href="/logout">Logout</a>
    @else
        <p>Not logged in</p>
    @endif
</body>
</html>
