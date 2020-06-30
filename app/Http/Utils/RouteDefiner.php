<?php

namespace App\Http\Utils;

use Illuminate\Routing\Router;

interface RouteDefiner
{
    public static function defineRoute(Router $router): void;

    /**
     *  This will sort desc. So higher number means better priority.
     */
    public static function routePriority(): int;
}
