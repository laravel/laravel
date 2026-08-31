<?php

namespace Illuminate\Routing\Controllers;

interface HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int,\Illuminate\Routing\Controllers\Middleware|\Closure|string>
     */
    public static function middleware();
}
