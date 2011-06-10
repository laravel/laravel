<?php namespace System;

class Redirect {

	/**
	 * Create a redirect response.
	 *
	 * @param  string    $url
	 * @param  string    $method
	 * @param  int       $status
	 * @param  bool      $https
	 * @return Response
	 */
	public static function to($url, $method = 'location', $status = 302, $https = false)
	{
		$url = URL::to($url, $https);

		return ($method == 'refresh')
							? Response::make('', $status)->header('Refresh', '0;url='.$url)
							: Response::make('', $status)->header('Location', $url);
	}

	/**
	 * Create a redirect response to a HTTPS URL.
	 *
	 * @param  string    $url
	 * @param  string    $method
	 * @param  int       $status
	 * @return Response
	 */
	public static function to_secure($url, $method = 'location', $status = 302)
	{
		return static::to($url, $method, $status, true);
	}

	/**
	 * Magic Method to handle redirecting to routes.
	 */
	public static function __callStatic($method, $parameters)
	{
		// ----------------------------------------------------
		// Dynamically redirect to a secure route URL.
		// ----------------------------------------------------
		if (strpos($method, 'to_secure_') === 0)
		{
			return static::to(URL::to_route(substr($method, 10), $parameters, true));
		}

		// ----------------------------------------------------
		// Dynamically redirect a route URL.
		// ----------------------------------------------------
		if (strpos($method, 'to_') === 0)
		{
			return static::to(URL::to_route(substr($method, 3), $parameters));
		}

		throw new \Exception("Method [$method] is not defined on the Redirect class.");
	}

}