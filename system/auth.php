<?php namespace System;

if (Config::get('session.driver') == '')
{
	throw new \Exception("You must specify a session driver before using the Auth class.");
}

class Auth {

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
	public static $user;

	/**
	 * The key used to store the user ID in the session.
	 *
	 * @var string
	 */
	protected static $key = 'laravel_user_id';

	/**
	 * Determine if the current user of the application is authenticated.
	 *
	 * @return bool
	 */
	public static function check()
	{
		return ( ! is_null(static::user()));
	}

	/**
	 * Get the current user of the application.
	 *
	 * To retrieve the user, the user ID stored in the session will be passed to
	 * the "by_id" closure in the authentication configuration file. The result
	 * of the closure will be cached and returned.
	 *
	 * @return object
	 * @see    $user
	 */
	public static function user()
	{
		if (is_null(static::$user) and Session::has(static::$key))
		{
			static::$user = call_user_func(Config::get('auth.by_id'), Session::get(static::$key));
		}

		return static::$user;
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
	 * @param  string  $username
	 * @param  string  $password
	 * @return bool
	 */
	public static function login($username, $password)
	{
		if ( ! is_null($user = call_user_func(Config::get('auth.by_username'), $username)))
		{
			if (Hash::check($password, $user->password))
			{
				static::remember($user);

				return true;
			}
		}

		return false;
	}

	/**
	 * Log a user into your application.
	 *
	 * The user's ID will be stored in the session and the user will be considered
	 * "logged in" on subsequent requests to your application.
	 *
	 * Note: The user given to this method should be an object having an "id" property.
	 *
	 * @param  object  $user
	 * @return void
	 */
	public static function remember($user)
	{
		static::$user = $user;

		Session::put(static::$key, $user->id);
	}

	/**
	 * Log the user out of your application.
	 *
	 * The user ID will be removed from the session and the user will no longer
	 * be considered logged in on subsequent requests.
	 *
	 * @return void
	 */
	public static function logout()
	{
		static::$user = null;

		Session::forget(static::$key);
	}

}