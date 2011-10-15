<?php namespace Laravel\Session\Transporters;

class Cookie implements Transporter {

	/**
	 * The name of the cookie used to store the session ID.
	 *
	 * @var string
	 */
	const key = 'laravel_session';

	/**
	 * Get the session identifier for the request.
	 *
	 * @param  array   $config
	 * @return string
	 */
	public function get($config)
	{
		return \Laravel\Cookie::get(Cookie::key);
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
		extract($config, EXTR_SKIP);

		$minutes = ( ! $expire_on_close) ? $lifetime : 0;

		\Laravel\Cookie::put(Cookie::key, $id, $minutes, $path, $domain, $secure);
	}

}