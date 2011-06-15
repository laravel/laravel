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
	 * Get the session driver. If the driver has already been instantiated, that
	 * instance will be returned.
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
		// Create a CSRF token for the session if necessary.
		// -----------------------------------------------------
		if ( ! static::has('csrf_token'))
		{
			static::put('csrf_token', Str::random(16));
		}

		static::$session['last_activity'] = time();
	}

	/**
	 * Determine if the session or flash data contains an item or set of items.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function has($key)
	{
		foreach (func_get_args() as $key)
		{
			if ( ! array_key_exists($key, static::$session['data']) and 
			     ! array_key_exists(':old:'.$key, static::$session['data']) and 
			     ! array_key_exists(':new:'.$key, static::$session['data']))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Get an item from the session or flash data.
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
	 * Write an item to the session flash data.
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
		static::driver()->delete(static::$session['id']);
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
		// Flash the old input to the session and age the flash.
		// -----------------------------------------------------
		static::flash('laravel_old_input', Input::get());

		static::age_flash();

		// -----------------------------------------------------
		// Write the session data to storage.
		// -----------------------------------------------------
		static::driver()->save(static::$session);

		// -----------------------------------------------------
		// Set the session cookie.
		// -----------------------------------------------------
		if ( ! headers_sent())
		{
			$cookie = new Cookie('laravel_session', static::$session['id']);

			if ( ! Config::get('session.expire_on_close'))
			{
				$cookie->lifetime = Config::get('session.lifetime');
			}

			$cookie->path = Config::get('session.path');
			$cookie->domain = Config::get('session.domain');
			$cookie->secure = Config::get('session.https');

			$cookie->send();
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
				// Create an :old: item for the :new: item.
				// -----------------------------------------------------
				static::put(':old:'.substr($key, 5), $value);

				// -----------------------------------------------------
				// Forget the :new: item.
				// -----------------------------------------------------
				static::forget($key);
			}
		}
	}

}