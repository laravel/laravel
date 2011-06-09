<?php namespace System;

class Session {

	/**
	 * The active session driver.
	 *
	 * @var Session\Driver
	 */
	private static $driver;

	/**
	 * The session.
	 *
	 * @var array
	 */
	private static $session = array();

	/**
	 * Get the session driver instance.
	 *
	 * @return Session\Driver
	 */
	public static function driver()
	{
		if (is_null(static::$driver))
		{
			static::$driver = Session\Factory::make(Config::get('session.driver'));
		}

		return static::$driver;
	}

	/**
	 * Load the session for the user.
	 *
	 * @return void
	 */
	public static function load()
	{
		// -----------------------------------------------------
		// If a valid ID is present, load the session.
		// -----------------------------------------------------
		if ( ! is_null($id = Cookie::get('laravel_session')))
		{
			static::$session = static::driver()->load($id);
		}

		// -----------------------------------------------------
		// If the session is invalid, start a new one.
		// -----------------------------------------------------
		if (is_null($id) or is_null(static::$session) or (time() - static::$session['last_activity']) > (Config::get('session.lifetime') * 60))
		{
			static::$session['id'] = Str::random(40);
			static::$session['data'] = array();
		}

		// -----------------------------------------------------
		// Generate a CSRF token if one does not exist.
		// -----------------------------------------------------
		if ( ! static::has('csrf_token'))
		{
			static::put('csrf_token', Str::random(16));
		}

		// -----------------------------------------------------
		// Set the last activity timestamp for the user.
		// -----------------------------------------------------
		static::$session['last_activity'] = time();
	}

	/**
	 * Determine if the session contains an item.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function has($key)
	{
		return array_key_exists($key, static::$session['data']) or
		       array_key_exists(':old:'.$key, static::$session['data']) or
		       array_key_exists(':new:'.$key, static::$session['data']);
	}

	/**
	 * Get an item from the session.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public static function get($key, $default = null)
	{
		if (static::has($key))
		{
			if (array_key_exists($key, static::$session['data']))
			{
				return static::$session['data'][$key];
			}
			// -----------------------------------------------------
			// Check the flash data for the item.
			// -----------------------------------------------------
			elseif (array_key_exists(':old:'.$key, static::$session['data']))
			{
				return static::$session['data'][':old:'.$key];
			}
			elseif (array_key_exists(':new:'.$key, static::$session['data']))
			{
				return static::$session['data'][':new:'.$key];
			}
		}

		return $default;
	}

	/**
	 * Get an item from the session and delete it.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public static function once($key, $default = null)
	{
		// -----------------------------------------------------
		// Get the item from the session.
		// -----------------------------------------------------
		$value = static::get($key, $default);

		// -----------------------------------------------------
		// Delete the item from the session.
		// -----------------------------------------------------
		static::forget($key);

		return $value;
	}

	/**
	 * Write an item to the session.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function put($key, $value)
	{
		static::$session['data'][$key] = $value;
	}

	/**
	 * Write a flash item to the session.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function flash($key, $value)
	{
		static::put(':new:'.$key, $value);
	}

	/**
	 * Remove an item from the session.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public static function forget($key)
	{
		unset(static::$session['data'][$key]);
	}

	/**
	 * Remove all items from the session.
	 *
	 * @return void
	 */
	public static function flush()
	{
		static::$session['data'] = array();
	}

	/**
	 * Regenerate the session ID.
	 *
	 * @return void
	 */
	public static function regenerate()
	{
		// -----------------------------------------------------
		// Delete the old session from storage.
		// -----------------------------------------------------
		static::driver()->delete(static::$session['id']);

		// -----------------------------------------------------
		// Create a new session ID.
		// -----------------------------------------------------
		static::$session['id'] = Str::random(40);
	}

	/**
	 * Close the session.
	 *
	 * @return void
	 */
	public static function close()
	{
		// -----------------------------------------------------
		// Do we need to re-flash the old Input data?
		// -----------------------------------------------------
		static::flash('laravel_old_input', Input::get());			

		// -----------------------------------------------------
		// Age the session flash data.
		// -----------------------------------------------------
		static::age_flash();

		// -----------------------------------------------------
		// Save the session to storage.
		// -----------------------------------------------------
		static::driver()->save(static::$session);

		if ( ! headers_sent())
		{
			// -----------------------------------------------------
			// Calculate the cookie lifetime.
			// -----------------------------------------------------
			$lifetime = (Config::get('session.expire_on_close')) ? 0 : Config::get('session.lifetime');

			// -----------------------------------------------------
			// Write the session cookie.
			// -----------------------------------------------------
			Cookie::put('laravel_session', static::$session['id'], $lifetime, Config::get('session.path'), Config::get('session.domain'), Config::get('session.https'));
		}

		// -----------------------------------------------------
		// Perform session garbage collection (2% chance).
		// -----------------------------------------------------
		if (mt_rand(1, 100) <= 2)
		{
			static::driver()->sweep(time() - (Config::get('session.lifetime') * 60));
		}
	}

	/**
	 * Age the session flash data.
	 *
	 * @return void
	 */
	private static function age_flash()
	{
		// -----------------------------------------------------
		// Expire all of the old flash data.
		// -----------------------------------------------------
		foreach (static::$session['data'] as $key => $value)
		{
			if (strpos($key, ':old:') === 0)
			{
				static::forget($key);
			}
		}

		// -----------------------------------------------------
		// Age all of the new flash data.
		// -----------------------------------------------------
		foreach (static::$session['data'] as $key => $value)
		{
			if (strpos($key, ':new:') === 0)
			{
				// -----------------------------------------------------
				// Create an :old: flash item.
				// -----------------------------------------------------
				static::put(':old:'.substr($key, 5), $value);

				// -----------------------------------------------------
				// Forget the :new: flash item.
				// -----------------------------------------------------
				static::forget($key);
			}
		}
	}

}