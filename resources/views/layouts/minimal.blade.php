{{-- 
    
    |-----------------------------------------------------------------|
    |  Warning: limited functionality should be added to this layout  |
    |  It can render if the app is 'critically' impaired.             |
    |-----------------------------------------------------------------|

--}}
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Error @yield('code') | @yield('title')</title>
        
        <link href="/compiled/css/app.css" rel="stylesheet">
    </head>
    <body>
        <main class="flex items-center min-h-screen p-3 text-center">
            <div class="e-copy m-auto max-w-copy">
                <h1>@yield('heading')</h1>
                <p>@yield('message')</p>
                <p><a href="/">Return to the homepage</a></p>
            </div>
        </main>
    </body>
</html>
