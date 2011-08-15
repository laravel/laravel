<?php namespace System;

class Session {

	/**
	 * The active session driver.
	 *
	 * @var Session\Driver
	 */
	public static $driver;

	/**
	 * The session payload, which contains the session ID, data and last activity timestamp.
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

		return static::$driver;
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

		if (static::invalid(static::$session)) static::$session = array('id' => Str::random(40), 'data' => array());

		if ( ! static::has('csrf_token')) static::put('csrf_token', Str::random(16));

		static::$session['last_activity'] = time();
	}

	/**
	 * Determine if a session is valid.
	 *
	 * A session is considered valid if it exists and has not expired.
	 *
	 * @param  array  $session
	 * @return bool
	 */
	private static function invalid($session)
	{
		return is_null($session) or (time() - $session['last_activity']) > (Config::get('session.lifetime') * 60);
	}

	/**
	 * Determine if the session or flash data contains an item.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function has($key)
	{
		return ( ! is_null(static::get($key)));
	}

	/**
	 * Get an item from the session or flash data.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public static function get($key, $default = null)
	{
		foreach (array($key, ':old:'.$key, ':new:'.$key) as $possibility)
		{
			if (array_key_exists($possibility, static::$session['data'])) return static::$session['data'][$possibility];
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
	 * The session will be stored in persistant storage and the session cookie will be
	 * session cookie will be sent to the browser. The old input data will also be
	 * stored in the session flash data.
	 *
	 * @return void
	 */
	public static function close()
	{
		static::flash('laravel_old_input', Input::get());

		static::age_flash();

		static::driver()->save(static::$session);

		static::write_cookie();

		if (mt_rand(1, 100) <= 2 and static::driver() instanceof Session\Sweeper)
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
		foreach (static::$session['data'] as $key => $value)
		{
			if (strpos($key, ':old:') === 0) static::forget($key);
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

	/**
	 * Write the session cookie.
	 *
	 * @return void
	 */
	private static function write_cookie()
	{
		if ( ! headers_sent())
		{
			extract(Config::get('session'));

			$minutes = ($expire_on_close) ? 0 : $lifetime;

			Cookie::put('laravel_session', static::$session['id'], $minutes, $path, $domain, $https, $http_only);
		}
	}

}