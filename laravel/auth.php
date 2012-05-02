<?php namespace Laravel; use Closure;

class Auth {

	/**
	 * The currently active authentication drivers.
	 *
	 * @var array
	 */
	public static $drivers = array();

	/**
	 * The third-party driver registrar.
	 *
	 * @var array
	 */
	public static $registrar = array();

	/**
	 * Get an authentication driver instance.
	 *
	 * @param  string  $driver
	 * @return Driver
	 */
	public static function driver($driver = null)
	{
		if (is_null($driver)) $driver = Config::get('auth.driver');

		if ( ! isset(static::$drivers[$driver]))
		{
			static::$drivers[$driver] = static::factory($driver);
		}

		return static::$drivers[$driver];
	}

	/**
	 * Create a new authentication driver instance.
	 *
	 * @param  string  $driver
	 * @return Driver
	 */
	protected static function factory($driver)
	{
		if (isset(static::$registrar[$driver]))
		{
			return static::$registrar[$driver]();
		}

		switch ($driver)
		{
			case 'fluent':
				return new Auth\Drivers\Fluent(Config::get('auth.table'));

			case 'eloquent':
				return new Auth\Drivers\Eloquent(Config::get('auth.model'));

			default:
				throw new \Exception("Auth driver {$driver} is not supported.");
		}
	}

	/**
	 * Register a third-party authentication driver.
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
	 * Magic Method for calling the methods on the default cache driver.
	 *
	 * <code>
	 *		// Call the "user" method on the default auth driver
	 *		$user = Auth::user();
	 *
	 *		// Call the "check" method on the default auth driver
	 *		Auth::check();
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::driver(), $method), $parameters);
	}

}