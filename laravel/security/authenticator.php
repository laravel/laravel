<?php namespace Laravel\Security;

use Laravel\Session\Driver;

class Authenticator {

	/**
	 * The current user of the application.
	 *
	 * @var object
	 */
	protected $user;

	/**
	 * The session driver being used by the Auth instance.
	 *
	 * @var Session\Driver
	 */
	protected $session;

	/**
	 * The configuration manager instance.
	 *
	 * @var Config
	 */
	protected $engine;

	/**
	 * Create a new authenticator instance.
	 *
	 * @param  Config          $config
	 * @param  Session\Driver  $session
	 * @return void
	 */
	public function __construct(Config $config, Driver $session)
	{
		$this->config = $config;
		$this->session = $session;
	}

	/**
	 * Determine if the current user of the application is authenticated.
	 *
	 * @return bool
	 */
	public function check()
	{
		return ! is_null($this->user());
	}

	/**
	 * Get the current user of the application.
	 *
	 * If the current user is not authenticated, NULL will be returned.
	 *
	 * @return object
	 */
	public function user()
	{
		if ( ! is_null($this->user)) return $this->user;

		return $this->user = call_user_func($this->config->get('auth.user'), $this->session->get('laravel_user_id'));
	}

	/**
	 * Attempt to log a user into the application.
	 *
	 * If the given credentials are valid, the user will be considered logged into the
	 * application and their user ID will be stored in the session data.
	 *
	 * @param  string       $username
	 * @param  string       $password
	 * @return bool
	 */
	public function attempt($username, $password = null)
	{
		if ( ! is_null($user = call_user_func($this->config->get('auth.attempt'), $username, $password)))
		{
			$this->remember($user);

			return true;
		}

		return false;
	}

	/**
	 * Log a user into the application.
	 *
	 * The user ID will be stored in the session so it is available on subsequent requests.
	 *
	 * @param  object  $user
	 * @return void
	 */
	public function remember($user)
	{
		$this->user = $user;

		$this->session->put('laravel_user_id', $user->id);
	}

	/**
	 * Log the current user out of the application.
	 *
	 * @return void
	 */
	public function logout()
	{
		call_user_func($this->config->get('auth.logout'), $this->user()->id);

		$this->user = null;

		$this->session->forget('laravel_user_id');
	}

}