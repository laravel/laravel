<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    
    /**
    *
    * It doesn't correspond with the written documentation. 
    * https://laravel.com/docs/5.3/authentication#included-authenticating
    * Accodring to it the variable $redirectTo = '' in files 'Auth/*'
    * is supposed to redirect you after being logged in to desired address. Is is not happening though.
    * What actually really matters is changing the content of redirect() in the function handle(). 
    *
    */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect('/home');
        }

        return $next($request);
    }
}
