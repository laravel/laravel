<?php

namespace App\Http\Util;

use Illuminate\Routing\Router;

interface RouteDefiner
{
    /** @param Router $router */
    public static function defineRoute(Router $router): void;
}
