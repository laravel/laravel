<?php

namespace App\Http\Web\Handlers;

use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Illuminate\Routing\Router;

class WelcomeHandler extends Handler implements RouteDefiner
{
    public function __invoke()
    {
        return view('welcome');
    }

    public static function defineRoute(Router $router)
    {
        $router
            ->get('/', static::class)
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
            ]);
    }

    public static function route()
    {
        return route(static::class);
    }
}
