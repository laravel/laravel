<?php namespace Laravel\Session\Transporters;

class Cookie implements Transporter {

	/**
	 * The cookie manager instance.
	 *
	 * @var Cookie
	 */
	protected $cookies;

	/**
	 * The name of the cookie used to store the session ID.
	 *
	 * @var string
	 */
	const key = 'laravel_session';

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
		return $this->cookies->get(Cookie::key);
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
		// Session cookies may be set to expire on close, which means we will need to
		// pass "0" into the cookie manager. This will cause the cookie to not be
		// deleted until the user closes their browser.
		$minutes = ( ! $config['expire_on_close']) ? $config['lifetime'] : 0;

		$this->cookies->put(Cookie::key, $id, $minutes, $config['path'], $config['domain']);
	}

}