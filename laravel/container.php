<?php namespace Laravel;

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
		$this->resolvers[$name] = compact('resolver', 'singleton');
	}

	/**
	 * Register a dependency as a singleton.
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

		if ( ! array_key_exists($name, $this->resolvers))
		{
			throw new \Exception("Error resolving [$name]. No resolver has been registered in the container.");
		}

		$object = call_user_func($this->resolvers[$name]['resolver'], $this);

		if ($this->resolvers[$name]['singleton']) $this->singletons[$name] = $object;

		return $object;
	}

}