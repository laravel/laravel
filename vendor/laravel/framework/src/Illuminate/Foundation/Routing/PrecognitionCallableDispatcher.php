<?php

namespace Illuminate\Foundation\Routing;

use Illuminate\Routing\CallableDispatcher;
use Illuminate\Routing\Route;

class PrecognitionCallableDispatcher extends CallableDispatcher
{
    /**
     * Dispatch a request to a given callable.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  callable  $callable
     * @return mixed
     */
    public function dispatch(Route $route, $callable)
    {
        $this->resolveParameters($route, $callable);

        abort(204, headers: ['Precognition-Success' => 'true']);
    }
}
