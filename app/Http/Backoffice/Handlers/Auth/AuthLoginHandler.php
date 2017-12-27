<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Router;

class AuthLoginHandler extends Handler implements RouteDefiner
{
    public const ROUTE_NAME = 'backoffice.auth.login';

    public function __invoke(Factory $view)
    {
        return $view->make('backoffice::auth.login');
    }

    public static function defineRoute(Router $router): void
    {
        $router
            ->get(config('backoffice.global_url_prefix') . '/auth/login', static::class)
            ->name(static::ROUTE_NAME)
            ->middleware([Kernel::BACKOFFICE_PUBLIC]);
    }

    public static function route(): string
    {
        return route(static::ROUTE_NAME);
    }
}
