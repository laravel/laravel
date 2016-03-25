<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Excepted URIs array.
     *
     * @var array
     */
    protected $except = [
        // eg. '/logout'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check() && $this->isNotExceptedUri($request)) {
            return redirect('/');
        }

        return $next($request);
    }

    /**
     * Checking request for excepted URI.
     *
     * @param Request $request
     * @return bool
     */
    public function isNotExceptedUri(Request $request)
    {
        foreach ($this->except as $exceptedUri) {
            if ($exceptedUri == $request->getRequestUri()) {
                return false;
            }
        }

        return true;
    }
}
