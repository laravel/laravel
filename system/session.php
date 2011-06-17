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
	 * Get the session driver.
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
		if ( ! is_null($id = Cookie::get('laravel_session')))
		{
			static::$session = static::driver()->load($id);
		}

		// ---------------------------------------------------------
		// If the session is invalid or expired, start a new one.
		// ---------------------------------------------------------
		if (is_null($id) or is_null(static::$session) or static::expired(static::$session['last_activity']))
		{
			static::$session['id'] = Str::random(40);
			static::$session['data'] = array();
		}

		// ---------------------------------------------------------
		// Create a CSRF token for the session if necessary. This
		// token is used by the Form class and filters to protect
		// against cross-site request forgeries.
		// ---------------------------------------------------------
		if ( ! static::has('csrf_token'))
		{
			static::put('csrf_token', Str::random(16));
		}

		static::$session['last_activity'] = time();
	}

	/**
	 * Determine if a session has expired based on the last activity.
	 *
	 * @param  int  $last_activity
	 * @return bool
	 */
	private static function expired($last_activity)
	{
		return (time() - $last_activity) > (Config::get('session.lifetime') * 60);
	}

	/**
	 * Determine if the session or flash data contains an item.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function has($key)
	{
		return (array_key_exists($key, static::$session['data']) or
			    array_key_exists(':old:'.$key, static::$session['data']) or
			    array_key_exists(':new:'.$key, static::$session['data']));
	}

	/**
	 * Get an item from the session or flash data.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public static function get($key, $default = null)
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
		// ---------------------------------------------------------
		// When regenerating the session ID, we go ahead and delete
		// the session data from storage. Then, we assign a new ID.
		//
		// The session will be re-written to storage at the end
		// of the request to the application.
		// ---------------------------------------------------------
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
		// ---------------------------------------------------------
		// Flash the old input data to the session. This allows
		// the Input::old method to retrieve input from the
		// previous request made by the user.
		// ---------------------------------------------------------
		static::flash('laravel_old_input', Input::get());

		static::age_flash();

		static::driver()->save(static::$session);

		// ---------------------------------------------------------
		// Send the session cookie the browser so we can remember
		// who the session belongs to on subsequent requests.
		// ---------------------------------------------------------
		if ( ! headers_sent())
		{
			$cookie = new Cookie('laravel_session', static::$session['id']);

			$cookie->lifetime = (Config::get('session.expire_on_close')) ? 0 : Config::get('session.lifetime');
			$cookie->path = Config::get('session.path');
			$cookie->domain = Config::get('session.domain');
			$cookie->secure = Config::get('session.https');

			$cookie->send();
		}

		// ---------------------------------------------------------
		// Perform session garbage collection (2% chance).
		// Session garbage collection removes all expired sessions.
		// ---------------------------------------------------------
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
		// Remove all of the :old: items from the session.
		// -----------------------------------------------------
		foreach (static::$session['data'] as $key => $value)
		{
			if (strpos($key, ':old:') === 0)
			{
				static::forget($key);
			}
		}

		// -----------------------------------------------------
		// Copy all of the :new: items to :old: items and then
		// remove the :new: items from the session.
		// -----------------------------------------------------
		foreach (static::$session['data'] as $key => $value)
		{
			if (strpos($key, ':new:') === 0)
			{
				static::put(':old:'.substr($key, 5), $value);

				static::forget($key);
			}
		}
	}

}