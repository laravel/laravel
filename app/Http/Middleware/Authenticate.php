<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    protected $loginPath = 'login';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->isGuest($guard)) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest($this->loginPath);
            }
        }

        return $next($request);
    }

    /**
     * @param string $guard
     *
     * @return bool
     */
    protected function isGuest($guard)
    {
        return Auth::guard($guard)->guest();
    }
}
