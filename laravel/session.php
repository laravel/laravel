<?php namespace Laravel;

use Closure;
use Laravel\Session\Drivers\Driver;
use Laravel\Session\Drivers\Sweeper;

if (Config::$items['application']['key'] === '')
{
	throw new \Exception("An application key is required to use sessions.");
}

class Session {

	/**
	 * The session array that is stored by the driver.
	 *
	 * @var array
	 */
	protected static $session;

	/**
	 * Indicates if the session already exists in storage.
	 *
	 * @var bool
	 */
	protected static $exists = true;

	/**
	 * Start the session handling for the current request.
	 *
	 * @param  Driver  $driver
	 * @return void
	 */
	public static function start(Driver $driver)
	{
		if ( ! is_null($id = Cookie::get(Config::$items['session']['cookie'])))
		{
			static::$session = $driver->load($id);
		}

		if (is_null(static::$session) or static::invalid())
		{
			static::$exists = false;

			static::$session = array('id' => Str::random(40), 'data' => array());
		}

		if ( ! static::has('csrf_token'))
		{
			// A CSRF token is stored in every session. The token is used by the
			// Form class and the "csrf" filter to protect the application from
			// cross-site request forgery attacks. The token is simply a long,
			// random string which should be posted with each request.
			static::put('csrf_token', Str::random(40));
		}
	}

	/**
	 * Deteremine if the session payload instance is valid.
	 *
	 * The session is considered valid if it exists and has not expired.
	 *
	 * @return bool
	 */
	protected static function invalid()
	{
		$lifetime = Config::$items['session']['lifetime'];

		return (time() - static::$session['last_activity']) > ($lifetime * 60);
	}

	/**
	 * Determine if session handling has been started for the request.
	 *
	 * @return bool
	 */
	public static function started()
	{
		return is_array(static::$session);
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
	 * The session flash data will also be checked for the requested item.
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
	 * Flash data only exists for the next request to the application.
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
		$flash = array();

		foreach (static::$session['data'] as $key => $value)
		{
			if (strpos($key, ':old:') === 0)
			{
				$flash[] = str_replace(':old:', '', $key);
			}
		}

		static::keep($flash);
	}

	/**
	 * Keep a session flash item from expiring at the end of the request.
	 *
	 * @param  string|array  $key
	 * @return void
	 */
	public static function keep($keys)
	{
		foreach ((array) $keys as $key)
		{
			static::flash($key, static::get($key));
		}
	}

	/**
	 * Remove an item from the session data.
	 *
	 * @param  string  $key
	 * @return Driver
	 */
	public static function forget($key)
	{
		unset(static::$session['data'][$key]);
	}

	/**
	 * Remove all of the items from the session.
	 *
	 * @return void
	 */
	public static function flush()
	{
		static::$session['data'] = array();
	}

	/**
	 * Assign a new, random ID to the session.
	 *
	 * @return void
	 */
	public static function regenerate()
	{
		static::$session['id'] = Str::random(40);

		static::$exists = false;
	}

	/**
	 * Get the CSRF token that is stored in the session data.
	 *
	 * @return string
	 */
	public static function token()
	{
		return static::get('csrf_token');
	}

	/**
	 * Store the session payload in storage.
	 *
	 * @param  Driver  $driver
	 * @return void
	 */
	public static function save(Driver $driver)
	{
		static::$session['last_activity'] = time();

		static::age();

		$config = Config::$items['session'];

		$driver->save(static::$session, $config, static::$exists);

		static::cookie();

		// Some session drivers implement the Sweeper interface, meaning that they
		// must clean up expired sessions manually. If the driver is a sweeper, we
		// need to determine if garbage collection should be run for the request.
		// Since garbage collection can be expensive, the probability of it
		// occuring is controlled by the "sweepage" configuration option.
		if ($driver instanceof Sweeper and (mt_rand(1, $config['sweepage'][1]) <= $config['sweepage'][0]))
		{
			$driver->sweep(time() - ($config['lifetime'] * 60));
		}
	}

	/**
	 * Age the session flash data.
	 *
	 * Session flash data is only available during the request in which it
	 * was flashed, and the request after that. To "age" the data, we will
	 * remove all of the :old: items and re-address the new items.
	 *
	 * @return void
	 */
	protected static function age()
	{
		foreach (static::$session['data'] as $key => $value)
		{
			if (strpos($key, ':old:') === 0) static::forget($key);
		}

		// Now that all of the "old" keys have been removed from the session data,
		// we can re-address all of the newly flashed keys to have old addresses.
		// The array_combine method uses the first array for keys, and the second
		// array for values to construct a single array from both.
		$keys = str_replace(':new:', ':old:', array_keys(static::$session['data']));

		static::$session['data'] = array_combine($keys, array_values(static::$session['data']));
	}

	/**
	 * Send the session ID cookie to the browser.
	 *
	 * @return void
	 */
	protected static function cookie()
	{
		$config = Config::$items['session'];

		extract($config, EXTR_SKIP);

		$minutes = ( ! $expire_on_close) ? $lifetime : 0;

		Cookie::put($cookie, static::$session['id'], $minutes, $path, $domain, $secure);	
	}

}