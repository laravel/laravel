<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Foundation\Application;

class MaintenanceMiddleware {

	/**
	 * The application implementation.
	 *
	 * @var Application
	 */
	protected $app;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Application  $app
	 * @return void
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
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
		if ($this->app->isDownForMaintenance())
		{
			return new Response('Be right back!', 503);
		}

		return $next($request);
	}

}
