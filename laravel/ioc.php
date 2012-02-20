<?php namespace Laravel; use Closure;

class IoC {

	/**
	 * The registered dependencies.
	 *
	 * @var array
	 */
	public static $registry = array();

	/**
	 * The resolved singleton instances.
	 *
	 * @var array
	 */
	public static $singletons = array();

	/**
	 * Register an object and its resolver.
	 *
	 * @param  string   $name
	 * @param  Closure  $resolver
	 * @param  bool     $singleton
	 * @return void
	 */
	public static function register($name, Closure $resolver, $singleton = false)
	{
		static::$registry[$name] = compact('resolver', 'singleton');
	}

	/**
	 * Determine if an object has been registered in the container.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public static function registered($name)
	{
		return array_key_exists($name, static::$registry);
	}

	/**
	 * Register an object as a singleton.
	 *
	 * Singletons will only be instantiated the first time they are resolved.
	 *
	 * @param  string   $name
	 * @param  Closure  $resolver
	 * @return void
	 */
	public static function singleton($name, $resolver)
	{
		static::register($name, $resolver, true);
	}

	/**
	 * Register an existing instance as a singleton.
	 *
	 * <code>
	 *		// Register an instance as a singleton in the container
	 *		IoC::instance('mailer', new Mailer);
	 * </code>
	 *
	 * @param  string  $name
	 * @param  mixed   $instance
	 * @return void
	 */
	public static function instance($name, $instance)
	{
		static::$singletons[$name] = $instance;
	}

	/**
	 * Register a controller with the IoC container.
	 *
	 * @param  string   $name
	 * @param  Closure  $resolver
	 * @return void
	 */
	public static function controller($name, $resolver)
	{
		static::register("controller: {$name}", $resolver);
	}

	/**
	 * Resolve a core Laravel class from the container.
	 *
	 * <code>
	 *		// Resolve the "laravel.router" class from the container
	 *		$input = IoC::core('router');
	 *
	 *		// Equivalent resolution of the router using the "resolve" method
	 *		$input = IoC::resolve('laravel.router');
	 * </code>
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function core($name, $parameters = array())
	{
		return static::resolve("laravel.{$name}", $parameters);
	}

	/**
	 * Resolve an object instance from the container.
	 *
	 * <code>
	 *		// Get an instance of the "mailer" object registered in the container
	 *		$mailer = IoC::resolve('mailer');
	 *
	 *		// Get an instance of the "mailer" object and pass parameters to the resolver
	 *		$mailer = IoC::resolve('mailer', array('test'));
	 * </code>
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function resolve($name, $parameters = array())
	{
		if (array_key_exists($name, static::$singletons))
		{
			return static::$singletons[$name];
		}

		$object = call_user_func(static::$registry[$name]['resolver'], $parameters);

		// If the resolver is registering as a singleton resolver, we will cache
		// the instance of the object in the container so we can resolve it next
		// time without having to instantiate a brand new instance.
		if (isset(static::$registry[$name]['singleton']))
		{
			return static::$singletons[$name] = $object;
		}

		return $object;
	}

}