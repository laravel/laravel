<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Auth\Authenticator;
use Illuminate\Contracts\Routing\Middleware;

class GuestMiddleware implements Middleware {

	/**
	 * The authenticator implementation.
	 *
	 * @var Authenticator
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Authenticator  $auth
	 * @return void
	 */
	public function __construct(Authenticator $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @param  \Closure  $next
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle($request, Closure $next)
	{
		if ($this->auth->check())
		{
			return new RedirectResponse(url('/'));
		}

		return $next($request);
	}

}
