<?php

namespace App\Http\Utils;

interface OrderedRouteDefiner extends RouteDefiner
{
    public static function getRouteOrder(): int;
}
