<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;

/**
 * Secure
 * Redirects any non-secure requests to their secure counterparts.
 * 
 * @param request The request object.
 * @param $next The next closure.
 * @return redirects to the secure counterpart of the requested uri.
*/
class Secure implements Middleware {
	
	public function handle($request, Closure $next)
	{
		if (!$request->secure() && \App::environment() === 'production') {
	    		return redirect()->secure($request->getRequestUri());
		}
	
		return $next($request);
	}
	
}
