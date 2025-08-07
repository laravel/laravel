<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureInstalled;
use App\Http\Middleware\AdminOnly;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(EnsureInstalled::class);
        $middleware->alias([
            'admin' => AdminOnly::class,
        ]);
        $middleware->statefulApi();
        // $middleware->throttleApi(); // Disabled to avoid missing rate limiter binding
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Optionally define API rate limiter here if needed in production
        // RateLimiter::for('api', function ($request) {
        //     return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        // });
    })
    ->create();
