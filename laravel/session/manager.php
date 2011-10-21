<?php namespace Laravel\Session;

use Closure;
use Laravel\Str;
use Laravel\Config;
use Laravel\Session\Drivers\Driver;
use Laravel\Session\Transporters\Transporter;

class Manager {

	/**
	 * The current session payload.
	 *
	 * @var array
	 */
	public static $session = array();

	/**
	 * Indicates if the session exists in persistent storage.
	 *
	 * @var bool
	 */
	public static $exists = true;

	/**
	 * Indicates if the session ID has been regenerated.
	 *
	 * @var bool
	 */
	public static $regenerated = false;

	/**
	 * The driver being used by the session.
	 *
	 * @var Drivers\Driver
	 */
	protected static $driver;

	/**
	 * The session ID transporter used by the session.
	 *
	 * @var Transporters\Transpoter
	 */
	protected static $transporter;

	/**
	 * Start the session handling for the current request.
	 *
	 * @param  Drivers\Driver            $driver
	 * @param  Transporters\Transporter  $transporter
	 * @return Payload
	 */
	public static function start(Driver $driver, Transporter $transporter)
	{
		$config = Config::$items['session'];

		$session = $driver->load($transporter->get($config));

		// If the session is expired, a new session will be generated and all of
		// the data from the previous session will be lost. The new session will
		// be assigned a random, long string ID to uniquely identify it among
		// the application's current users.
		if (is_null($session) or (time() - $session['last_activity']) > ($config['lifetime'] * 60))
		{
			static::$exists = false;

			$session = array('id' => Str::random(40), 'data' => array());
		}

		// Now that we should have a valid session, we can set the static session
		// property and check for session data such as the CSRF token. We will
		// also set the static driver and transporter properties, since they
		// will be used to close the session at the end of the request.
		static::$session = $session;

		if ( ! static::has('csrf_token')) static::put('csrf_token', Str::random(16));

		list(static::$driver, static::$transporter) = array($driver, $transporter);
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
	 * Get an item from the session.
	 *
	 * <code>
	 *		// Get an item from the session
	 *		$name = Session::get('name');
	 *
	 *		// Return a default value if the item doesn't exist
	 *		$name = Session::get('name', 'Taylor');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function get($key, $default = null)
	{
		foreach (array($key, ':old:'.$key, ':new:'.$key) as $possibility)
		{
			if (array_key_exists($possibility, static::$session['data']))
			{
				return static::$session['data'][$possibility];
			}
		}

		return ($default instanceof Closure) ? call_user_func($default) : $default;
	}

	/**
	 * Write an item to the session.
	 *
	 * <code>
	 *		// Write an item to the session
	 *		Session::put('name', 'Taylor');
	 * </code>
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
	 * Flash data only exists for the next request. After that, it will be
	 * removed from the session. Flash data is useful for temporary status
	 * or welcome messages.
	 *
	 * <code>
	 *		// Flash an item to the session
	 *		Session::flash('name', 'Taylor');
	 * </code>
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
	 * Keep all of the session flash data from expiring at the end of the request.
	 *
	 * @return void
	 */
	public static function reflash()
	{
		static::replace(':old:', ':new:', array_keys(static::$session['data']));
	}

	/**
	 * Keep a session flash item from expiring at the end of the request.
	 *
	 * If a string is passed to the method, only that item will be kept.
	 * An array may also be passed to the method, in which case all
	 * items in the array will be kept.
	 *
	 * <code>
	 *		// Keep a session flash item from expiring
	 *		Session::keep('name');
	 * </code>
	 *
	 * @param  string|array  $key
	 * @return void
	 */
	public static function keep($key)
	{
		if (is_array($key))
		{
			return array_map(array('Laravel\\Session\\Manager', 'keep'), $key);
		}

		static::flash($key, static::get($key));

		static::forget(':old:'.$key);
	}

	/**
	 * Remove an item from the session.
	 *
	 * @param  string  $key
	 * @return Driver
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
		static::$session['id'] = Str::random(40);

		static::$regenerated = true;

		static::$exists = false;
	}

	/**
	 * Age the session payload, preparing it for storage after a request.
	 *
	 * @return array
	 */
	public static function age()
	{
		static::$session['last_activity'] = time();

		foreach (static::$session['data'] as $key => $value)
		{
			if (strpos($key, ':old:') === 0) static::forget($key);
		}

		static::replace(':new:', ':old:', array_keys(static::$session['data']));

		return static::$session;
	}

	/**
	 * Readdress the session data by performing a string replacement on the keys.
	 *
	 * @param  string  $search
	 * @param  string  $replace
	 * @param  array   $keys
	 * @return void
	 */
	protected static function replace($search, $replace, $keys)
	{
		$keys = str_replace($search, $replace, $keys);

		static::$session['data'] = array_combine($keys, array_values(static::$session['data']));
	}

	/**
	 * Close the session handling for the request.
	 *
	 * @return void
	 */
	public static function close()
	{
		$config = Config::$items['session'];

		static::$driver->save(static::age(), $config, static::$exists);

		static::$transporter->put(static::$session['id'], $config);

		// Some session drivers may implement the Sweeper interface, meaning the
		// driver must do its garbage collection manually. Alternatively, some
		// drivers such as APC and Memcached are not required to manually
		// clean up their sessions.
		if (mt_rand(1, $config['sweepage'][1]) <= $config['sweepage'][0] and static::$driver instanceof Drivers\Sweeper)
		{
			static::$driver->sweep(time() - ($config['lifetime'] * 60));
		}
	}

}