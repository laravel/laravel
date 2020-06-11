<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetApiDefaultHeaders
{
    /**
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
