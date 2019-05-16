<?php

namespace App\Http\Utils;

use Illuminate\Routing\Router;

interface RouteDefiner
{
    /** @param Router $router */
    public static function defineRoute(Router $router): void;
}
