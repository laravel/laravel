<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Router;

class AuthForgotPasswordFormHandler extends Handler implements RouteDefiner
{
    protected const ROUTE_NAME = 'backoffice.auth.password.forgot';

    public function __invoke(Factory $view)
    {
        return $view->make('backoffice::auth.request-reset-password');
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/auth/password/forgot", static::class)
            ->name(static::ROUTE_NAME)
            ->middleware([Kernel::BACKOFFICE_PUBLIC]);
    }

    public static function route(): string
    {
        return route(static::ROUTE_NAME);
    }
}
