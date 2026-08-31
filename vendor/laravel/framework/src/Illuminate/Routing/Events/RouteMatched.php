<?php

namespace Illuminate\Routing\Events;

class RouteMatched
{
    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Routing\Route  $route  The route instance.
     * @param  \Illuminate\Http\Request  $request  The request instance.
     */
    public function __construct(
        public $route,
        public $request,
    ) {
    }
}
