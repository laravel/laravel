<?php namespace App\Http;

use Exception;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

	/**
	 * The application's HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
		'App\Http\Middleware\UnderMaintenance',
		'Illuminate\Cookie\Middleware\EncryptCookies',
		'Illuminate\Cookie\Middleware\AddQueuedCookiesToRequest',
		'Illuminate\Session\Middleware\ReadSession',
		'Illuminate\Session\Middleware\WriteSession',
		'Illuminate\View\Middleware\ShareErrorsFromSession',
		'App\Http\Middleware\VerifyCsrfToken',
	];

	/**
	 * Handle an incoming HTTP request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function handle($request)
	{
		try
		{
			return parent::handle($request);
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

}
