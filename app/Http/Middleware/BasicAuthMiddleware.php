<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Auth\Authenticator;

class BasicAuthMiddleware implements Middleware {

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
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		return $this->auth->basic() ?: $next($request);
	}

}
