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
	 * The container is set early in the request cycle and can be access here for
	 * use as a service locator if object injection is not practical.
	 *
	 * <code>
	 *		// Get the active container instance
	 *		$container = IoC::container();
	 *
	 *		// Get the active container instance and call the resolve method
	 *		$container = IoC::container()->resolve('instance');
	 * </code>
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
	 *		// Call the "resolve" method on the active container instance
	 *		$instance = IoC::resolve('instance');
	 *
	 *		// Equivalent operation using the "container" method
	 *		$instance = IoC::container()->resolve('instance');
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
	public $singletons = array();

	/**
	 * The registered dependencies.
	 *
	 * @var array
	 */
	protected $registry = array();

	/**
	 * Create a new IoC container instance.
	 *
	 * @param  array  $registry
	 * @return void
	 */
	public function __construct($registry = array())
	{
		$this->registry = $registry;
	}

	/**
	 * Register an object and its resolver.
	 *
	 * The resolver function is called when the registered object is requested.
	 *
	 * <code>
	 *		// Register an object in the container
	 *		IoC::register('something', function($container) {return new Something;});
	 *
	 *		// Register an object in the container as a singleton
	 *		IoC::register('something', function($container) {return new Something;}, true);
	 * </code>
	 *
	 * @param  string   $name
	 * @param  Closure  $resolver
	 * @return void
	 */
	public function register($name, $resolver, $singleton = false)
	{
		$this->registry[$name] = array('resolver' => $resolver, 'singleton' => $singleton);
	}

	/**
	 * Determine if an object has been registered in the container.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function registered($name)
	{
		return array_key_exists($name, $this->registry);
	}

	/**
	 * Register an object as a singleton.
	 *
	 * Singletons will only be instantiated the first time they are resolved. On subsequent
	 * requests for the object, the original instance will be returned.
	 *
	 * <code>
	 *		// Register an object in the container as a singleton
	 *		IoC::singleton('something', function($container) {return new Something;});
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
	 * container to be managed as a singleton instance.
	 *
	 * <code>
	 *		// Register an instance with the IoC container
	 *		IoC::instance('something', new Something);
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
	 * Resolve an object.
	 *
	 * The object's resolver will be called and its result will be returned. If the
	 * object is registered as a singleton and has already been resolved, the instance
	 * that has already been instantiated will be returned.
	 *
	 * <code>
	 *		// Get the "something" object out of the IoC container
	 *		$something = IoC::resolve('something');
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

		$object = call_user_func($this->registry[$name]['resolver'], $this);

		return (isset($this->registry[$name]['singleton'])) ? $this->singletons[$name] = $object : $object;
	}

	/**
	 * Magic Method for resolving classes out of the IoC container.
	 *
	 * <code>
	 *		// Get the "something" instance out of the IoC container
	 *		$something = IoC::container()->something;
	 *
	 *		// Equivalent method of retrieving the instance using the resolve method
	 *		$something = IoC::container()->resolve('something');
	 * </code>
	 */
	public function __get($key)
	{
		if ($this->registered($key)) return $this->resolve($key);

		throw new \Exception("Attempting to resolve undefined class [$key].");
	}

}