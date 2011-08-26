<?php namespace Laravel\Security;

use Laravel\IoC;
use Laravel\Config;
use Laravel\Session\Driver;

class Authenticator {

	/**
	 * The current user of the application.
	 *
	 * If no user is logged in, this will be NULL. Otherwise, it will contain the result
	 * of the "by_id" closure in the authentication configuration file.
	 *
	 * Typically, the user should be accessed via the "user" method.
	 *
	 * @var object
	 */
	public $user;

	/**
	 * The session driver being used by the Auth instance.
	 *
	 * @var Session\Driver
	 */
	protected $session;

	/**
	 * The hashing engine that should be used to perform hashing.
	 *
	 * @var Hashing\Engine
	 */
	protected $hasher;

	/**
	 * The key used to store the user ID in the session.
	 *
	 * @var string
	 */
	protected static $key = 'laravel_user_id';

	/**
	 * Create a new Auth class instance.
	 *
	 * @param  Session\Driver  $driver
	 * @param  Hashing\Engine  $hasher
	 * @return void
	 */
	public function __construct(Driver $driver, Hashing\Engine $hasher)
	{
		$this->hasher = $hasher;
		$this->session = $driver;
	}

	/**
	 * Create a new Auth class instance.
	 *
	 * If no session driver or hasher is provided, the default implementations will be used.
	 *
	 * @return Auth
	 */
	public static function make()
	{
		return IoC::container()->resolve('laravel.security.auth');
	}

	/**
	 * Determine if the current user of the application is authenticated.
	 *
	 * @see    login()
	 * @return bool
	 */
	public function check()
	{
		return ! is_null($this->user());
	}

	/**
	 * Get the current user of the application.
	 *
	 * To retrieve the user, the user ID stored in the session will be passed to
	 * the "by_id" closure in the authentication configuration file. The result
	 * of the closure will be cached and returned.
	 *
	 * <code>
	 *		$email = Auth::user()->email;
	 * </code>
	 *
	 * @return object
	 */
	public function user()
	{
		if (is_null($this->user) and $this->session->has(static::$key))
		{
			$this->user = call_user_func(Config::get('auth.by_id'), $this->session->get(static::$key));
		}

		return $this->user;
	}

	/**
	 * Attempt to log a user into your application.
	 *
	 * If the user credentials are valid. The user's ID will be stored in the session and the
	 * user will be considered "logged in" on subsequent requests to the application.
	 *
	 * The password passed to the method should be plain text, as it will be hashed
	 * by the Hash class when authenticating.
	 *
	 * <code>
	 *		if (Auth::login('email@example.com', 'password'))
	 *		{
	 *			// The credentials are valid and the user is now logged in.
	 *		}
	 * </code>
	 *
	 * @param  string  $username
	 * @param  string  $password
	 * @return bool
	 */
	public function login($username, $password)
	{
		if ( ! is_null($user = call_user_func(Config::get('auth.by_username'), $username)))
		{
			if ($this->hasher->check($password, $user->password))
			{
				$this->remember($user);

				return true;
			}
		}

		return false;
	}

	/**
	 * Log a user into your application.
	 *
	 * The user's ID will be stored in the session and the user will be considered
	 * "logged in" on subsequent requests to your application. This method is called
	 * by the login method after determining a user's credentials are valid.
	 *
	 * Note: The user given to this method should be an object having an "id" property.
	 *
	 * @param  object  $user
	 * @return void
	 */
	public function remember($user)
	{
		$this->user = $user;

		$this->session->put(static::$key, $user->id);
	}

	/**
	 * Log the user out of your application.
	 *
	 * The user ID will be removed from the session and the user will no longer
	 * be considered logged in on subsequent requests to your application.
	 *
	 * @return void
	 */
	public function logout()
	{
		$this->user = null;

		$this->session->forget(static::$key);
	}

}