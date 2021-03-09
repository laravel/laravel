<?php

namespace App\Http\Backoffice\Handlers;

use App\Http\Utils\RouteDefiner;
use Illuminate\Routing\Controller as BaseHandler;

abstract class Handler extends BaseHandler implements RouteDefiner
{
    public static function routePriority(): int
    {
        return 0;
    }
}
