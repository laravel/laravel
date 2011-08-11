<?php namespace System;

if (Config::get('session.driver') == '')
{
	throw new \Exception("You must specify a session driver before using the Auth class.");
}

class Auth {

	/**
	 * The current user of the application.
	 *
	 * If no user is logged in, this variable will be NULL. Otherwise, it will contain
	 * the result of the "by_id" closure in the authentication configuration file.
	 *
	 * However, the user should typically be accessed via the "user" method.
	 *
	 * @var object
	 * @see user()
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
	 * <code>
	 * if (Auth::check())
	 * {
	 *		// The user is logged in...
	 * }
	 * </code>
	 *
	 * @return bool
	 * @see    login
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
	 * <code>
	 * $email = Auth::user()->email;
	 * </code>
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
	 * Attempt to login a user.
	 *
	 * If the user credentials are valid. The user's ID will be stored in the session and the
	 * user will be considered "logged in" on subsequent requests to the application.
	 *
	 * The password passed to the method should be plain text, as it will be hashed
	 * by the Hash class when authenticating.
	 *
	 * <code>
	 * if (Auth::login('test@gmail.com', 'secret'))
	 * {
	 *		// The credentials are valid...
	 * }
	 * </code>
	 *
	 * @param  string  $username
	 * @param  string  $password
	 * @return bool
	 * @see    Hash::check()
	 */
	public static function attempt($username, $password)
	{
		if ( ! is_null($user = call_user_func(Config::get('auth.by_username'), $username)))
		{
			if (Hash::check($password, $user->password))
			{
				static::login($user);

				return true;
			}
		}

		return false;
	}

	/**
	 * Login a given user into the application.
	 *
	 * The user's ID will be stored in the session and the user will be considered
	 * "logged in" on subsequent requests to the application.
	 *
	 * Note: The user given to this method should be an object having a "id" property.
	 *
	 * @param  object  $user
	 * @return void
	 */
	public static function login($user)
	{
		static::$user = $user;

		Session::put(static::$key, $user->id);
	}

	/**
	 * Log the user out of the application.
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