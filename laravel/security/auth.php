<?php namespace Laravel\Security;

use Laravel\Config;
use Laravel\Session\Payload;

class Auth {

	/**
	 * The current user of the application.
	 *
	 * @var object
	 */
	protected $user;

	/**
	 * The session payload instance.
	 *
	 * @var Session\Payload
	 */
	protected $session;

	/**
	 * The key used when storing the user ID in the session.
	 *
	 * @var string
	 */
	const user_key = 'laravel_user_id';

	/**
	 * Create a new authenticator instance.
	 *
	 * @param  Session\Payload  $session
	 * @return void
	 */
	public function __construct(Payload $session)
	{
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

		return $this->user = call_user_func(Config::get('auth.user'), $this->session->get(Auth::user_key));
	}

	/**
	 * Attempt to log a user into the application.
	 *
	 * If the given credentials are valid, the user will be considered logged into the
	 * application and their user ID will be stored in the session data.
	 *
	 * @param  string  $username
	 * @param  string  $password
	 * @return bool
	 */
	public function attempt($username, $password = null)
	{
		if ( ! is_null($user = call_user_func(Config::get('auth.attempt'), $username, $password)))
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

		$this->session->put(Auth::user_key, $user->id);
	}

	/**
	 * Log the current user out of the application.
	 *
	 * @return void
	 */
	public function logout()
	{
		call_user_func(Config::get('auth.logout'), $this->user());

		$this->user = null;

		$this->session->forget(Auth::user_key);
	}

}