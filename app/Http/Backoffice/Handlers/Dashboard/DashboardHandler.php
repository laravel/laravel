<?php

namespace App\Http\Backoffice\Handlers\Dashboard;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Illuminate\Routing\Router;

class DashboardHandler extends Handler implements RouteDefiner
{
    public function __invoke(SecurityApi $securityApi)
    {
        return view('backoffice::empty', [
            'user' => $securityApi->getUser(),
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $router
            ->get('/', static::class)
            ->prefix(config('backoffice.global_url_prefix'))
            ->name(static::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(): string
    {
        return route(static::class);
    }
}
