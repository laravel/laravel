<?php namespace System;

class Auth {

	/**
	 * The current user of the application.
	 *
	 * @var object
	 */
	public static $user;

	/**
	 * The key used to store the user ID in the session.
	 *
	 * @var string
	 */
	private static $key = 'laravel_user_id';

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
	 * @return object
	 */
	public static function user()
	{
		// -----------------------------------------------------
		// Verify that sessions are enabled.
		// -----------------------------------------------------
		if (Config::get('session.driver') == '')
		{
			throw new \Exception("You must specify a session driver before using the Auth class.");
		}

		$model = static::model();

		// -----------------------------------------------------
		// Load the user using the ID stored in the session.
		// -----------------------------------------------------
		if (is_null(static::$user) and Session::has(static::$key))
		{
			static::$user = $model::find(Session::get(static::$key));
		}

		return static::$user;
	}

	/**
	 * Attempt to login a user.
	 *
	 * @param  string  $username
	 * @param  string  $password
	 */
	public static function login($username, $password)
	{
		$model = static::model();

		// -----------------------------------------------------
		// Get the user by username.
		// -----------------------------------------------------
		$user = $model::where(Config::get('auth.username'), '=', $username)->first();

		if ( ! is_null($user))
		{
			// -----------------------------------------------------
			// Hash the password.
			// -----------------------------------------------------
			$password = (isset($user->salt)) ? Hash::make($password, $user->salt)->value : sha1($password);

			if ($user->password == $password)
			{
				static::$user = $user;

				Session::put(static::$key, $user->id);

				return true;
			}
		}

		return false;
	}

	/**
	 * Logout the current user of the application.
	 *
	 * @return void
	 */
	public static function logout()
	{
		Session::forget(static::$key);
		static::$user = null;
	}

	/**
	 * Get the authentication model.
	 *
	 * @return string
	 */
	private static function model()
	{
		return '\\'.Config::get('auth.model');
	}

}