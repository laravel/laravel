<?php namespace Laravel\Session\Transporters;

class Cookie implements Transporter {

	/**
	 * The cookie manager instance.
	 *
	 * @var Cookie
	 */
	protected $cookies;

	/**
	 * Create a new cookie session transporter instance.
	 *
	 * @param  Cookie  $cookie
	 * @return void
	 */
	public function __construct(\Laravel\Cookie $cookies)
	{
		$this->cookies = $cookies;
	}

	/**
	 * Get the session identifier for the request.
	 *
	 * @param  array   $config
	 * @return string
	 */
	public function get($config)
	{
		return $this->cookies->get('laravel_session');
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

		$this->cookies->put('laravel_session', $id, $minutes, $config['path'], $config['domain']);
	}

}