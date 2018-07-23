<?php

namespace App\Domain\Accounts\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotVerified
{
    /**
     * Ensures authenticated user has verified, logging them out and
     * redirecting them to login page with flash message if so.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // If already consented we can move on.
        if (Auth::guard($guard)->user()->verified_at) {
            return $next($request);
        }

        Auth::logout();

        return redirect(route('verify-codes.create'));
    }
}
