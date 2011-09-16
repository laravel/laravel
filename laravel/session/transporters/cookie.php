<?php namespace Laravel\Session\Transporters;

class Cookie implements Transporter {

	/**
	 * Create a new cookie session transporter instance.
	 *
	 * @param  Cookie  $cookie
	 * @return void
	 */
	public function __construct(\Laravel\Cookie $cookie)
	{
		$this->cookie = $cookie;
	}

	/**
	 * Get the session identifier for the request.
	 *
	 * @return string
	 */
	public function get()
	{
		return $this->cookie->get('laravel_session');
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
		if ( ! headers_sent())
		{
			$minutes = ($config['expire_on_close']) ? 0 : $config['lifetime'];

			$this->cookie->put('laravel_session', $id, $minutes, $config['path'], $config['domain']);
		}
	}

}