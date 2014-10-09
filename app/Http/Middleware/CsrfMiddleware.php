<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Session\TokenMismatchException;

class CsrfMiddleware implements Middleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @throws TokenMismatchException
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ($request->session()->token() != $request->input('_token'))
		{
			throw new TokenMismatchException;
		}

		return $next($request);
	}

}
