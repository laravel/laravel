<?php

namespace App\Http\Utils;

use Illuminate\Routing\Router;

interface RouteDefiner
{
    public static function defineRoute(Router $router): void;
}
