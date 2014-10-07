<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Authenticator;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Routing\ResponseFactory;

class AuthMiddleware implements Middleware {

	/**
	 * The authenticator implementation.
	 *
	 * @var \Illuminate\Contracts\Auth\Authenticator
	 */
	protected $auth;

	/**
	 * The response factory implementation.
	 *
	 * @var \Illuminate\Contracts\Routing\ResponseFactory
	 */
	protected $response;

	/**
	 * Create a new filter instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticator  $auth
	 * @param  \Illuminate\Contracts\Routing\ResponseFactory  $response
	 * @return void
	 */
	public function __construct(Authenticator $auth, ResponseFactory $response)
	{
		$this->auth = $auth;
		$this->response = $response;
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
		if ($this->auth->guest())
		{
			if ($request->ajax())
			{
				return $this->response->make('Unauthorized', 401);
			}
			else
			{
				return $this->response->redirectGuest('auth/login');
			}
		}

		return $next($request);
	}

}
