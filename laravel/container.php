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
	 * Singletons will only be instantiated the first time they are resolved.
	 * On subsequent requests for the object, the original instance will be returned.
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
	 * This method allows you to register an already existing object instance
	 * with the container to be managed as a singleton instance.
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
	 */
	public function __get($key)
	{
		if ($this->registered($key)) return $this->resolve($key);

		throw new \Exception("Attempting to resolve undefined class [$key].");
	}

}