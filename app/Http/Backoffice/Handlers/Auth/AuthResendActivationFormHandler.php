<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Router;

class AuthResendActivationFormHandler extends Handler implements RouteDefiner
{
    protected const ROUTE_NAME = 'backoffice.auth.resend_activation';

    public function __invoke(): View
    {
        return view()->make('backoffice::auth.request-activation');
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get("$backofficePrefix/auth/activate/resend", self::class)
            ->name(self::ROUTE_NAME)
            ->middleware([Kernel::BACKOFFICE_PUBLIC]);
    }

    public static function route(): string
    {
        return route(self::ROUTE_NAME);
    }
}
