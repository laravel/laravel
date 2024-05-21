<?php

declare(strict_types=1);

use Lightit\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\{ConvertEmptyStringsToNull, PreventRequestsDuringMaintenance, TrimStrings};
use Illuminate\Http\Middleware\{FrameGuard, HandleCors, TrustHosts, TrustProxies, ValidatePostSize};
use Lightit\Security\App\Middlewares\SecurityHeaders;
use Lightit\Shared\App\Console\CommandManager;
use Lightit\Shared\App\Exceptions\ExceptionHandler;

$exceptionManager = new ExceptionHandler();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withEvents(discover: [
        __DIR__.'/../src/Shared/App/Listeners',
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            \Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks::class,
            // Uncomment the following line if you are running your server from behind an HTTP proxy, such as a load balancer.
            // TrustProxies::class,

            TrustHosts::class, // Default: allSubdomainsOfApplicationUrl
            FrameGuard::class,
            HandleCors::class,
            PreventRequestsDuringMaintenance::class,
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,

            SecurityHeaders::class,
        ]);

        // Use the following to modify web and api middlewares
        // https://laravel.com/docs/11.x/middleware#laravels-default-middleware-groups

        // $middleware->web(append: [
        //     EnsureUserIsSubscribed::class,
        // ]);

        // $middleware->api(prepend: [
        //     EnsureTokenIsValid::class,
        // ]);
    })
    ->withCommands(commands: CommandManager::getCommands())
    ->withExceptions(using: $exceptionManager->getClosure())
    ->create();
