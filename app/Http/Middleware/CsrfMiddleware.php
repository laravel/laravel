<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Session\TokenMismatchException;

class CsrfMiddleware implements Middleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @param  \Closure  $next
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle($request, Closure $next)
	{
		if ($request->getSession()->token() != $request->input('_token'))
		{
			throw new TokenMismatchException;
		}

		return $next($request);
	}

}
