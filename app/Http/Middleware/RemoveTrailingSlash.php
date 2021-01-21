<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;

class RemoveTrailingSlash
{
    public function handle($request, Closure $next)
    {
        if (preg_match('/.+\/$/', $request->getRequestUri())) {
            return Redirect::to(rtrim($request->getRequestUri(), '/'), 301);
        }

        return $next($request);
    }
}
