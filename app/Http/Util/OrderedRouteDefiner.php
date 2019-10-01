<?php

namespace App\Http\Util;

interface OrderedRouteDefiner extends RouteDefiner
{
    public static function getRouteOrder(): int;
}
