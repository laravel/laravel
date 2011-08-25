<?php namespace Laravel;

class IoC {

	/**
	 * The available IoC containers.
	 *
	 * @var array
	 */
	public static $containers = array();

	/**
	 * Bootstrap the default container and register the dependencies.
	 *
	 * @param  array  $dependencies
	 * @return void
	 */
	public static function bootstrap($dependencies)
	{
		$container = static::container();

		foreach ($dependencies as $key => $value)
		{
			$container->register($key, $value['resolver'], (isset($value['singleton'])) ? $value['singleton'] : false);
		}
	}

	/**
	 * Get a container instance.
	 *
	 * If no container name is specified, the default container will be returned.
	 *
	 * <code>
	 *		// Get the default container instance
	 *		$container = IoC::container();
	 *
	 *		// Get a specific container instance
	 *		$container = IoC::container('models');
	 * </code>
	 *
	 * @param  string     $container
	 * @return Container
	 */
	public static function container($container = 'default')
	{
		if ( ! array_key_exists($container, static::$containers))
		{
			static::$containers[$container] = new Container;
		}

		return static::$containers[$container];
	}

	/**
	 * Magic Method for passing methods to the default container.
	 *
	 * <code>
	 *		// Resolve an object from the default container
	 *		$user = IoC::resolve('user');
	 *
	 *		// Equivalent method of resolving using the container method
	 *		$user = IoC::container()->resolve('user');
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::container(), $method), $parameters);
	}

}