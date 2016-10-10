<?php namespace Illuminate\Cookie;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Queue implements HttpKernelInterface {

	/**
	 * The wrapped kernel implementation.
	 *
	 * @var \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	protected $app;

	/**
	 * The cookie jar instance.
	 *
	 * @var \Illuminate\Cookie\CookieJar
	 */
	protected $cookies;

	/**
	 * Create a new CookieQueue instance.
	 *
	 * @param  \Symfony\Component\HttpKernel\HttpKernelInterface  $app
	 * @param  \Illuminate\Cookie\CookieJar  $cookies
	 * @return void
	 */
	public function __construct(HttpKernelInterface $app, CookieJar $cookies)
	{
		$this->app = $app;
		$this->cookies = $cookies;
	}

	/**
	 * Handle the given request and get the response.
	 *
	 * @implements HttpKernelInterface::handle
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @param  int   $type
	 * @param  bool  $catch
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
	{
		$response = $this->app->handle($request, $type, $catch);

		foreach ($this->cookies->getQueuedCookies() as $cookie)
		{
			$response->headers->setCookie($cookie);
		}

		return $response;
	}

}
