<?php

namespace App\Http\Backoffice\Handlers\Dashboard;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Illuminate\Routing\Router;

class DashboardIndexHandler extends Handler implements RouteDefiner
{
    public function __invoke(SecurityApi $securityApi)
    {
        return view('backoffice::empty', [
            'user' => $securityApi->getUser(),
        ]);
    }

    public static function defineRoute(Router $router)
    {
        $router
            ->get(config('backoffice.global_url_prefix'), static::class)
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE,
            ]);
    }

    public static function route()
    {
        return route(static::class);
    }
}
