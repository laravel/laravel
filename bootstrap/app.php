<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel. We can
| also use the application to configure core, foundational behavior.
|
*/

return Application::configure()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        channels: __DIR__.'/../routes/channels.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withCommands([
        __DIR__.'/../routes/console.php',
        __DIR__.'/../app/Console/Commands',
    ])
    ->withExceptionHandling(function (Exceptions $exceptions) {
        //
    })->create();
