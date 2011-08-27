<?php namespace Laravel;

class IoC {

	/**
	 * The active container instance.
	 *
	 * @var Container
	 */
	public static $container;

	/**
	 * Get the active container instance.
	 *
	 * @return Container
	 */
	public static function container()
	{
		return static::$container;
	}

	/**
	 * Magic Method for calling methods on the active container instance.
	 *
	 * <code>
	 *		// Get the request registered in the container
	 *		$request = IoC::resolve('laravel.request');
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::$container, $method), $parameters);
	}

}

class Container {

	/**
	 * The resolved singleton instances.
	 *
	 * @var array
	 */
	private $singletons = array();

	/**
	 * The registered dependencies.
	 *
	 * @var array
	 */
	private $resolvers = array();

	/**
	 * Create a new IoC container instance.
	 *
	 * @param  array  $dependencies
	 * @return void
	 */
	public function __construct($dependencies = array())
	{
		foreach ($dependencies as $key => $value)
		{
			$this->register($key, $value['resolver'], (isset($value['singleton'])) ? $value['singleton'] : false);
		}
	}

	/**
	 * Register a dependency and its resolver.
	 *
	 * The resolver function when the registered dependency is requested.
	 *
	 * <code>
	 *		// Register a simple dependency
	 *		$container->register('name', function() { return 'Fred'; });
	 *
	 *		// Register a dependency as a singleton
	 *		$container->register('name', function() { return new Name; }, true);
	 * </code>
	 *
	 * @param  string   $name
	 * @param  Closure  $resolver
	 * @return void
	 */
	public function register($name, $resolver, $singleton = false)
	{
		$this->resolvers[$name] = array('resolver' => $resolver, 'singleton' => $singleton);
	}

	/**
	 * Determine if a dependency has been registered in the container.
	 *
	 * <code>
	 *		// Determine if the "user" dependency is registered in the container
	 *		$registered = $container->registered('user');
	 * </code>
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function registered($name)
	{
		return array_key_exists($name, $this->resolvers);
	}

	/**
	 * Register a dependency as a singleton.
	 *
	 * Singletons will only be instantiated the first time they are resolved. On subsequent
	 * requests for the object, the original instance will be returned.
	 *
	 * <code>
	 *		// Register a dependency as a singleton
	 *		$container->singleton('user', function() { return new User; })
	 * </code>
	 *
	 * @param  string   $name
	 * @param  Closure  $resolver
	 * @return void
	 */
	public function singleton($name, $resolver)
	{
		$this->register($name, $resolver, true);
	}

	/**
	 * Register an instance as a singleton.
	 *
	 * This method allows you to register an already existing object instance with the
	 * container as a singleton instance.
	 *
	 * <code>
	 *		// Register an object instance as a singleton in the container
	 *		$container->instance('user', new User);
	 * </code>
	 *
	 * @param  string  $name
	 * @param  mixed   $instance
	 * @return void
	 */
	public function instance($name, $instance)
	{
		$this->singletons[$name] = $instance;
	}

	/**
	 * Resolve a dependency.
	 *
	 * The dependency's resolver will be called and its result will be returned.
	 *
	 * <code>
	 *		// Resolver the "name" dependency
	 *		$name = $container->resolve('name');
	 * </code>
	 *
	 * @param  string  $name
	 * @return mixed
	 */
	public function resolve($name)
	{
		if (array_key_exists($name, $this->singletons)) return $this->singletons[$name];

		if ( ! $this->registered($name))
		{
			throw new \Exception("Error resolving [$name]. No resolver has been registered in the container.");
		}

		$object = call_user_func($this->resolvers[$name]['resolver'], $this);

		if ($this->resolvers[$name]['singleton']) $this->singletons[$name] = $object;

		return $object;
	}

}