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
    public function handle($request, Closure $next, $guard = null)
    {
        switch ($guard) {
            case 'admin':
                $redirectTo = '/admin-home-page';
                //OR can use route name
                //$routeName = 'admin.home';
                break;
                
            case 'guardName1':
                $redirectTo = '/guardName1-home-page';
                //OR can use route name
                //$routeName = 'guardName1.home';
                break;

            case 'guardName2':
                $redirectTo = '/guardName2-home-page';
                //OR can use route name
                //$routeName = 'guardName2.home';
                break;    
            
            default:
                $redirectTo = '/home';
                //OR can use route name
                //$routeName = 'home';
                break;
        }
        if (Auth::guard($guard)->check()) {
            return redirect($redirectTo);
            // IF WE USE ROUTE NAME THEN
            //return redirect(route($routeName));
        }

        return $next($request);
    }
}
