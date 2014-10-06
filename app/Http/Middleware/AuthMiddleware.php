<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Route;
use Illuminate\Contracts\Auth\Authenticator;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Routing\ResponseFactory;

class AuthMiddleware implements Middleware {

	/**
	 * The authenticator implementation.
	 *
	 * @var Authenticator
	 */
	protected $auth;

	/**
	 * The response factory implementation.
	 *
	 * @var ResponseFactory
	 */
	protected $response;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Authenticator  $auth
	 * @param  ResponseFactory  $response
	 * @return void
	 */
	public function __construct(Authenticator $auth,
								ResponseFactory $response)
	{
		$this->auth = $auth;
		$this->response = $response;
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
