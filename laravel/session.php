<?php namespace Laravel; use Closure;

class Session {

	/**
	 * The session singleton instance for the request.
	 *
	 * @var Session\Payload
	 */
	public static $instance;

	/**
	 * The third-party driver registrar.
	 *
	 * @var array
	 */
	public static $registrar = array();

	/**
	 * The string name of the CSRF token stored in the session.
	 *
	 * @var string
	 */
	const csrf_token = 'csrf_token';

	/**
	 * Create the session payload and load the session.
	 *
	 * @return void
	 */
	public static function load()
	{
		static::start(Config::get('session.driver'));

		static::$instance->load(Cookie::get(Config::get('session.cookie')));
	}

	/**
	 * Create the session payload instance for the request.
	 *
	 * @param  string  $driver
	 * @return void
	 */
	public static function start($driver)
	{
		static::$instance = new Session\Payload(static::factory($driver));
	}

	/**
	 * Create a new session driver instance.
	 *
	 * @param  string  $driver
	 * @return Session\Drivers\Driver
	 */
	public static function factory($driver)
	{
		if (isset(static::$registrar[$driver]))
		{
			$resolver = static::$registrar[$driver];

			return $resolver();
		}

		switch ($driver)
		{
			case 'apc':
				return new Session\Drivers\APC(Cache::driver('apc'));

			case 'cookie':
				return new Session\Drivers\Cookie;

			case 'database':
				return new Session\Drivers\Database(Database::connection());

			case 'file':
				return new Session\Drivers\File(path('storage').'sessions'.DS);

			case 'memcached':
				return new Session\Drivers\Memcached(Cache::driver('memcached'));

			case 'memory':
				return new Session\Drivers\Memory;

			case 'redis':
				return new Session\Drivers\Redis(Cache::driver('redis'));

			default:
				throw new \Exception("Session driver [$driver] is not supported.");
		}
	}

	/**
	 * Retrieve the active session payload instance for the request.
	 *
	 * <code>
	 *		// Retrieve the session instance and get an item
	 *		Session::instance()->get('name');
	 *
	 *		// Retrieve the session instance and place an item in the session
	 *		Session::instance()->put('name', 'Taylor');
	 * </code>
	 *
	 * @return Session\Payload
	 */
	public static function instance()
	{
		if (static::started()) return static::$instance;

		throw new \Exception("A driver must be set before using the session.");
	}

	/**
	 * Determine if session handling has been started for the request.
	 *
	 * @return bool
	 */
	public static function started()
	{
		return ! is_null(static::$instance);
	}

	/**
	 * Register a third-party cache driver.
	 *
	 * @param  string   $driver
	 * @param  Closure  $resolver
	 * @return void
	 */
	public static function extend($driver, Closure $resolver)
	{
		static::$registrar[$driver] = $resolver;
	}

	/**
	 * Magic Method for calling the methods on the session singleton instance.
	 *
	 * <code>
	 *		// Retrieve a value from the session
	 *		$value = Session::get('name');
	 *
	 *		// Write a value to the session storage
	 *		$value = Session::put('name', 'Taylor');
	 *
	 *		// Equivalent statement using the "instance" method
	 *		$value = Session::instance()->put('name', 'Taylor');
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::instance(), $method), $parameters);
	}

}