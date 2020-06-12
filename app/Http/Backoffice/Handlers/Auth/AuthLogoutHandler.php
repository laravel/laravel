<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Router;

class AuthLogoutHandler extends Handler implements RouteDefiner
{
    protected const ROUTE_NAME = 'backoffice.auth.logout';

    public function __invoke(SecurityApi $securityApi): RedirectResponse
    {
        $securityApi->logout();

        return redirect()->to(AuthLoginHandler::route());
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/auth/logout", self::class)
            ->name(self::ROUTE_NAME)
            ->middleware([Kernel::BACKOFFICE_PUBLIC]);
    }

    public static function route(): string
    {
        return route(self::ROUTE_NAME);
    }
}
