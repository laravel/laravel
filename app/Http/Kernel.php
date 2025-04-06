<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'agency' => \App\Http\Middleware\AgencyMiddleware::class,
        'subagent' => \App\Http\Middleware\SubagentMiddleware::class,
        'customer' => \App\Http\Middleware\CustomerMiddleware::class,
    ];
}