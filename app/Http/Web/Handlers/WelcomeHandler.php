<?php

namespace App\Http\Web\Handlers;

use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Illuminate\Routing\Router;

class WelcomeHandler extends Handler implements RouteDefiner
{
    public function __invoke()
    {
        return view('welcome');
    }

    public static function defineRoute(Router $router): void
    {
        $router
            ->get('/', static::class)
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
            ]);
    }

    public static function route(): string
    {
        return route(static::class);
    }
}
