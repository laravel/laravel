<?php namespace Laravel\Session\Transporters;

use Laravel\Cookie as C;

class Cookie implements Transporter {

	/**
	 * Get the session identifier for the request.
	 *
	 * @param  array   $config
	 * @return string
	 */
	public function get($config)
	{
		return C::get('laravel_session');
	}

	/**
	 * Store the session identifier for the request.
	 *
	 * @param  string  $id
	 * @param  array   $config
	 * @return void
	 */
	public function put($id, $config)
	{
		$minutes = ($config['expire_on_close']) ? 0 : $config['lifetime'];

		C::put('laravel_session', $id, $minutes, $config['path'], $config['domain']);
	}

}