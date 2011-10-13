<?php namespace Laravel\Security;

use Laravel\IoC;
use Laravel\Str;
use Laravel\Config;
use Laravel\Cookie;
use Laravel\Session\Manager as Session;

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
	 * The key used when setting the "remember me" cookie.
	 *
	 * @var string
	 */
	const remember_key = 'laravel_remember';

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
	 * This method will call the "user" closure in the authentication configuration file.
	 * If the user is not authenticated, null will be returned.
	 *
	 * If no user exists in the session, the method will check for a "remember me"
	 * cookie and attempt to login the user based on the value of that cookie.
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

		static::$user = call_user_func(Config::get('auth.user'), Session::get(Auth::user_key));

		if (is_null(static::$user) and ! is_null($cookie = Cookie::get(Auth::remember_key)))
		{
			static::$user = static::recall($cookie);
		}

		return static::$user;
	}

	/**
	 * Attempt to login a user based on a long-lived "remember me" cookie.
	 *
	 * @param  string  $cookie
	 * @return mixed
	 */
	protected static function recall($cookie)
	{
		$cookie = explode('|', Crypter::decrypt($cookie));

		// If there are not at least two elements in the array, the decrypted value
		// is not valid and we wil just bail out of the method since the cookie may
		// have been tampered with and should not be considered trustworthy.
		if (count($cookie) < 2) return;

		list($id, $username, $config) = array($cookie[0], $cookie[1], Config::get('auth'));

		if ( ! is_null($user = call_user_func($config['user'], $id)) and $user->{$config['username']} === $username)
		{
			static::login($user);

			return $user;
		}
	}

	/**
	 * Attempt to log a user into the application.
	 *
	 * If the given credentials are valid, the user will be logged into the application
	 * and their user ID will be stored in the session via the "login" method.
	 *
	 * The user may also be "remembered". When this option is set, the user will be
	 * automatically logged into the application for one year via an encrypted cookie
	 * containing their ID. Of course, if the user logs out of the application,
	 * they will no longer be remembered.
	 *
	 * @param  string  $username
	 * @param  string  $password
	 * @param  bool    $remember
	 * @return bool
	 */
	public static function attempt($username, $password = null, $remember = false)
	{
		$config = Config::get('auth');

		if ( ! is_null($user = call_user_func($config['attempt'], $username, $password, $config)))
		{
			static::login($user, $config, $remember);

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
	 * @param  bool    $remember
	 * @return void
	 */
	public static function login($user, $remember = false)
	{
		static::$user = $user;

		if ($remember) static::remember($user->id, $user->{Config::get('auth.username')});

		Session::put(Auth::user_key, $user->id);
	}

	/**
	 * Set a cookie so that users are "remembered" and don't need to login.
	 *
	 * @param  string  $id
	 * @param  string  $username
	 * @return void
	 */
	protected static function remember($id, $username)
	{
		$cookie = Crypter::encrypt($id.'|'.$username.'|'.Str::random(40));

		// This method assumes the "remember me" cookie should have the same configuration
		// as the session cookie. Since this cookie, like the session cookie, should be
		// kept very secure, it's probably safe to assume the settings are the same.
		$config = Config::get('session');

		Cookie::forever(Auth::remember_key, $cookie, $config['path'], $config['domain'], $config['secure']);
	}

	/**
	 * Log the current user out of the application.
	 *
	 * The "logout" closure in the authenciation configuration file will be called.
	 * All authentication cookies will be deleted and the user ID will be removed
	 * from the session.
	 *
	 * @return void
	 */
	public static function logout()
	{
		call_user_func(Config::get('auth.logout'), static::user());

		static::$user = null;

		Cookie::forget(Auth::user_key);

		Cookie::forget(Auth::remember_key);

		Session::forget(Auth::user_key);
	}

}