<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string $guard = null)
    {
        if (! $request->expectsJson() && Auth::guard($guard)->check()) {
            return redirect('/home');
        }

        return $next($request);
    }
}
