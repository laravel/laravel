<?php

namespace Illuminate\Routing\Matching;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

interface ValidatorInterface
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function matches(Route $route, Request $request);
}
