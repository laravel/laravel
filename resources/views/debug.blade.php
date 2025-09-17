<!DOCTYPE html>
<html>
<head>
    <title>Debug Info</title>
</head>
<body>
    <h1>Debug Information</h1>
    
    <h3>Laravel Version:</h3>
    <p>{{ app()->version() }}</p>
    
    <h3>Environment:</h3>
    <p>{{ app()->environment() }}</p>
    
    <h3>Current Session:</h3>
    @if(session()->all())
        <pre>{{ print_r(session()->all(), true) }}</pre>
    @else
        <p>No session data</p>
    @endif
    
    <h3>Request Info:</h3>
    <p>URL: {{ request()->url() }}</p>
    <p>Method: {{ request()->method() }}</p>
    
    <h3>CSRF Token:</h3>
    <p>{{ csrf_token() }}</p>
    
    <h3>Test Controllers:</h3>
    <p><a href="/test-controller">Test Controller</a></p>
    
    <hr>
    <h3>Quick Login Test:</h3>
    <form method="POST" action="/login">
        {{ csrf_field() }}
        <input type="text" name="username" value="admin" placeholder="Username"><br><br>
        <input type="password" name="password" value="admin123" placeholder="Password"><br><br>
        <button type="submit">Test Login</button>
    </form>
</body>
</html>
