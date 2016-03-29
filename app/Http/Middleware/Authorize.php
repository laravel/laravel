<?php

namespace App\Http\Middleware;

use Gate;
use Closure;

class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $ability
     * @param  string|null  $model
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handle($request, Closure $next, $ability, $model = null)
    {
        Gate::authorize($ability, $this->getGateArguments($model));

        return $next($request);
    }

    /**
     * Get the arguments parameter for the gate.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $model
     * @return array|string|\Illuminate\Database\Eloquent\Model
     */
    protected function getGateArguments($request, $model)
    {
        // If there's no model, we'll pass an empty array to the gate. If it
        // looks like a FQCN of a model, we'll send it to the gate as is.
        // Otherwise, we'll resolve the Eloquent model from the route.
        if (is_null($model)) {
            return [];
        }

        if (strpos($model, '\\') !== false) {
            return $model;
        }

        return $request->route($model);
    }
}
