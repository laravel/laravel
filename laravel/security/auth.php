<?php namespace Laravel\Security;

use Laravel\IoC;
use Laravel\Config;
use Laravel\Session\Payload;

class Auth {

	/**
	 * The current user of the application.
	 *
	 * @var object
	 */
	protected static $user;

	/**
	 * The key used when storing the user ID in the session.
	 *
	 * @var string
	 */
	const user_key = 'laravel_user_id';

	/**
	 * Determine if the current user of the application is authenticated.
	 *
	 * @return bool
	 */
	public static function check()
	{
		return ! is_null(static::user());
	}

	/**
	 * Get the current user of the application.
	 *
	 * If the current user is not authenticated, null will be returned. This method
	 * will call the "user" closure in the authentication configuration file.
	 *
	 * <code>
	 *		// Get the current user of the application
	 *		$user = Auth::user();
	 *
	 *		// Access a property on the current user of the application
	 *		$email = Auth::user()->email;
	 * </code>
	 *
	 * @return object
	 */
	public static function user()
	{
		if ( ! is_null(static::$user)) return static::$user;

		$id = IoC::container()->core('session')->get(Auth::user_key);

		if (is_null($id) AND ! is_null($cookie = strrev(Crypter::decrypt(\Cookie::get('remember')))))
		{
			$cookie = explode('|', $cookie);
			if ($cookie[2] == md5(\Request::server('HTTP_USER_AGENT')))
			{
				$id = $cookie[0];
			}
		}

		return static::$user = call_user_func(Config::get('auth.user'), $id);
	}

	/**
	 * Attempt to log a user into the application.
	 *
	 * If the given credentials are valid, the user will be considered logged into
	 * the application and their user ID will be stored in the session data.
	 *
	 * @param  string  $username
	 * @param  string  $password
	 * @return bool
	 */
	public static function attempt($username, $password = null, $remember = false)
	{
		if ( ! is_null($user = call_user_func(Config::get('auth.attempt'), $username, $password)))
		{
			static::login($user);

			if ($remember) static::remember($user);

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
	public static function login($user)
	{
		static::$user = $user;

		IoC::container()->core('session')->put(Auth::user_key, $user->id);
	}

	/**
	 * Log the current user out of the application.
	 *
	 * The "logout" closure in the authenciation configuration file will be called.
	 *
	 * @return void
	 */
	public static function logout()
	{
		call_user_func(Config::get('auth.logout'), static::user());

		static::$user = null;

		IoC::container()->core('session')->forget(Auth::user_key);
	}

	/**
	 * Set a cookie so that users are remembered.
	 *
	 * @return bool
	 */
	public static function remember($user)
	{
		static::$user = $user;
		$cookie = Crypter::encrypt(strrev($user->id.'|'.\Request::ip().'|'.md5(\Request::server('HTTP_USER_AGENT')).'|'.time()));
		\Cookie::put('remember', $cookie, 60);
	}
}