<?php namespace System;

class Session {

	/**
	 * The active session driver.
	 *
	 * @var Session\Driver
	 */
	public static $driver;

	/**
	 * The session.
	 *
	 * @var array
	 */
	public static $session = array();

	/**
	 * Get the session driver.
	 *
	 * @return Session\Driver
	 */
	public static function driver()
	{
		if (is_null(static::$driver))
		{
			switch (Config::get('session.driver'))
			{
				case 'cookie':
					return static::$driver = new Session\Cookie;

				case 'file':
					return static::$driver = new Session\File;

				case 'db':
					return static::$driver = new Session\DB;

				case 'memcached':
					return static::$driver = new Session\Memcached;

				case 'apc':
					return static::$driver = new Session\APC;

				default:
					throw new \Exception("Session driver [$driver] is not supported.");
			}			
		}
	}

	/**
	 * Load a user session by ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public static function load($id)
	{
		static::$session = ( ! is_null($id)) ? static::driver()->load($id) : null;

		if (is_null(static::$session) or static::expired(static::$session['last_activity']))
		{
			static::$session = array('id' => Str::random(40), 'data' => array());
		}

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

		return is_callable($default) ? call_user_func($default) : $default;
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
		// Flash the old input data to the session. This allows the Input::old method to
		// retrieve the input from the previous request made by the user.
		static::flash('laravel_old_input', Input::get());

		static::age_flash();

		static::driver()->save(static::$session);

		$config = Config::get('session');

		if ( ! headers_sent())
		{
			$minutes = ($config['expire_on_close']) ? 0 : $config['lifetime'];

			Cookie::put('laravel_session', static::$session['id'], $minutes, $config['path'], $config['domain'], $config['https'], $config['http_only']);
		}

		// 2% chance of performing session garbage collection on any given request...
		if (mt_rand(1, 100) <= 2 and static::driver() instanceof Session\Sweeper)
		{
			static::driver()->sweep(time() - ($config['lifetime'] * 60));
		}
	}

	/**
	 * Age the session flash data.
	 *
	 * @return void
	 */
	private static function age_flash()
	{
		foreach (static::$session['data'] as $key => $value)
		{
			if (strpos($key, ':old:') === 0)
			{
				static::forget($key);
			}
		}

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