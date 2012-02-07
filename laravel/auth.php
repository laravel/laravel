<?php namespace Laravel;

class Auth {

	/**
	 * The current user of the application.
	 *
	 * @var object
	 */
	public static $user;

	/**
	 * The key used when storing the user ID in the session.
	 *
	 * @var string
	 */
	const user_key = 'laravel_user_id';

	/**
	 * Determine if the user of the application is not logged in.
	 *
	 * This method is the inverse of the "check" method.
	 *
	 * @return bool
	 */
	public static function guest()
	{
		return ! static::check();
	}

	/**
	 * Determine if the user of the application is logged in.
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
	 * <code>
	 *		// Get the current user of the application
	 *		$user = Auth::user();
	 *
	 *		// Access a property on the current user of the application
	 *		$email = Auth::user()->email;
	 * </code>
	 *
	 * @return object|null
	 */
	public static function user()
	{
		if ( ! is_null(static::$user)) return static::$user;

		$id = Session::get(Auth::user_key);

		// To retrieve the user, we'll first attempt to use the "user" Closure
		// defined in the auth configuration file, passing in the ID. The user
		// Closure gives the developer a ton of freedom surrounding how the
		// user is actually retrieved.
		$config = Config::get('auth');

		static::$user = call_user_func($config['user'], $id);

		// If the user wasn't found in the database but a "remember me" cookie
		// exists, we'll attempt to recall the user based on the cookie value.
		// Since all cookies contain a fingerprint hash verifying that they
		// haven't changed, we can trust it.
		$recaller = Cookie::get($config['cookie']);

		if (is_null(static::$user) and ! is_null($recaller))
		{
			static::$user = static::recall($recaller);
		}

		return static::$user;
	}

	/**
	 * Attempt to login a user based on a long-lived "remember me" cookie.
	 *
	 * @param  string  $recaller
	 * @return mixed
	 */
	protected static function recall($recaller)
	{
		// When the remember me cookie is stored, it is encrypted and contains
		// the user's ID and a long, random string. The segments are separated
		// by a pipe character so we'll explode on that.
		$recaller = explode('|', Crypter::decrypt($recaller));

		// We'll pass the ID that was stored in the cookie into the same user
		// Closure that is used by the "user" method. If the method returns
		// a user, we will log them into the application.
		$user = call_user_func(Config::get('auth.user'), $recaller[0]);

		if ( ! is_null($user))
		{
			static::login($user);

			return $user;
		}
	}

	/**
	 * Attempt to log a user into the application.
	 *
	 * <code>
	 *		// Attempt to log a user into the application
	 *		$success = Auth::attempt('username', 'password');
	 *
	 *		// Attempt to login a user and set the "remember me" cookie
	 *		Auth::attempt('username', 'password', true);
	 * </code>
	 *
	 * @param  string  $username
	 * @param  string  $password
	 * @param  bool    $remember
	 * @return bool
	 */
	public static function attempt($username, $password = null, $remember = false)
	{
		$config = Config::get('auth');

		// When attempting to login the user, we will call the "attempt" closure
		// from the configuration file. This gives the developer the freedom to
		// authenticate based on the needs of their application.
		//
		// All of the password hashing and checking and left totally up to the
		// developer, as this gives them the freedom to use any hashing scheme
		// or authentication provider they wish.
		$user = call_user_func($config['attempt'], $username, $password);

		if (is_null($user)) return false;

		static::login($user, $remember);

		return true;
	}

	/**
	 * Log a user into the application.
	 *
	 * <code>
	 *		// Login the user with an ID of 15
	 *		Auth::login(15);
	 *
	 *		// Login a user by passing a user object
	 *		Auth::login($user);
	 *
	 *		// Login a user and set a "remember me" cookie
	 *		Auth::login($user, true);
	 * </code>
	 *
	 * @param  object|int  $user
	 * @param  bool        $remember
	 * @return void
	 */
	public static function login($user, $remember = false)
	{
		$id = (is_object($user)) ? $user->id : (int) $user;

		if ($remember) static::remember($id);

		Session::put(Auth::user_key, $id);
	}

	/**
	 * Set a cookie so that the user is "remembered".
	 *
	 * @param  string  $id
	 * @return void
	 */
	protected static function remember($id)
	{
		$recaller = Crypter::encrypt($id.'|'.Str::random(40));

		// This method assumes the "remember me" cookie should have the same
		// configuration as the session cookie. Since this cookie, like the
		// session cookie, should be kept very secure, it's probably safe.
		// to assume the cookie settings are the same.
		$config = Config::get('session');

		extract($config, EXTR_SKIP);

		$cookie = Config::get('auth.cookie');

		Cookie::forever($cookie, $recaller, $path, $domain, $secure);
	}

	/**
	 * Log the current user out of the application.
	 *
	 * @return void
	 */
	public static function logout()
	{
		// We will call the "logout" closure first, which gives the developer
		// the chance to do any clean-up or before the user is logged out of
		// the application. No action is taken by default.
		call_user_func(Config::get('auth.logout'), static::user());

		static::$user = null;

		$config = Config::get('session');

		extract($config, EXTR_SKIP);

		// When forgetting the cookie, we need to also pass in the path and
		// domain that would have been used when the cookie was originally
		// set by the framework, otherwise it will not be deleted.
		$cookie = Config::get('auth.cookie');

		Cookie::forget($cookie, $path, $domain, $secure);

		Session::forget(Auth::user_key);
	}

}