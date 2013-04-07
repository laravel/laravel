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
	 * @param  mixed    $resolver
	 * @param  bool     $singleton
	 * @return void
	 */
	public static function register($name, $resolver = null, $singleton = false)
	{
		if (is_null($resolver)) $resolver = $name;

		static::$registry[$name] = compact('resolver', 'singleton');
	}

    /**
     * Unregister an object
     *
     * @param string $name
     */
    public static function unregister($name)
    {
        if (array_key_exists($name, static::$registry)) {
            unset(static::$registry[$name]);
            unset(static::$singletons[$name]);
        }
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
	public static function singleton($name, $resolver = null)
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
	 * Resolve a given type to an instance.
	 *
	 * <code>
	 *		// Get an instance of the "mailer" object registered in the container
	 *		$mailer = IoC::resolve('mailer');
	 *
	 *		// Get an instance of the "mailer" object and pass parameters to the resolver
	 *		$mailer = IoC::resolve('mailer', array('test'));
	 * </code>
	 *
	 * @param  string  $type
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function resolve($type, $parameters = array())
	{
		// If an instance of the type is currently being managed as a singleton, we will
		// just return the existing instance instead of instantiating a fresh instance
		// so the developer can keep re-using the exact same object instance from us.
		if (isset(static::$singletons[$type]))
		{
			return static::$singletons[$type];
		}

		// If we don't have a registered resolver or concrete for the type, we'll just
		// assume the type is the concrete name and will attempt to resolve it as is
		// since the container should be able to resolve concretes automatically.
		if ( ! isset(static::$registry[$type]))
		{
			$concrete = $type;
		}
		else
		{
			$concrete = array_get(static::$registry[$type], 'resolver', $type);
		}

		// We're ready to instantiate an instance of the concrete type registered for
		// the binding. This will instantiate the type, as well as resolve any of
		// its nested dependencies recursively until they are each resolved.
		if ($concrete == $type or $concrete instanceof Closure)
		{
			$object = static::build($concrete, $parameters);
		}
		else
		{
			$object = static::resolve($concrete);
		}

		// If the requested type is registered as a singleton, we want to cache off
		// the instance in memory so we can return it later without creating an
		// entirely new instances of the object on each subsequent request.
		if (isset(static::$registry[$type]['singleton']) && static::$registry[$type]['singleton'] === true)
		{
			static::$singletons[$type] = $object;
		}

		Event::fire('laravel.resolving', array($type, $object));

		return $object;
	}

	/**
	 * Instantiate an instance of the given type.
	 *
	 * @param  string  $type
	 * @param  array   $parameters
	 * @return mixed
     * @throws \Exception
	 */
	protected static function build($type, $parameters = array())
	{
		// If the concrete type is actually a Closure, we will just execute it and
		// hand back the results of the function, which allows functions to be
		// used as resolvers for more fine-tuned resolution of the objects.
		if ($type instanceof Closure)
		{
			return call_user_func_array($type, $parameters);
		}

		$reflector = new \ReflectionClass($type);

		// If the type is not instantiable, the developer is attempting to resolve
		// an abstract type such as an Interface of an Abstract Class and there is
		// no binding registered for the abstraction so we need to bail out.
		if ( ! $reflector->isInstantiable())
		{
			throw new \Exception("Resolution target [$type] is not instantiable.");
		}

		$constructor = $reflector->getConstructor();

		// If there is no constructor, that means there are no dependencies and
		// we can just resolve an instance of the object right away without
		// resolving any other types or dependencies from the container.
		if (is_null($constructor))
		{
			return new $type;
		}

		$dependencies = static::dependencies($constructor->getParameters(), $parameters);

		return $reflector->newInstanceArgs($dependencies);
	}

	/**
	 * Resolve all of the dependencies from the ReflectionParameters.
	 *
	 * @param  array  $parameters
	 * @param  array  $arguments that might have been passed into our resolve
	 * @return array
	 */
	protected static function dependencies($parameters, $arguments)
	{
		$dependencies = array();

		foreach ($parameters as $parameter)
		{
			$dependency = $parameter->getClass();

			// If the person passed in some parameters to the class
			// then we should probably use those instead of trying
			// to resolve a new instance of the class
			if (count($arguments) > 0)
			{
				$dependencies[] = array_shift($arguments);
			}
			else if (is_null($dependency))
			{
				$dependency[] = static::resolveNonClass($parameter);
			}
			else
			{
				$dependencies[] = static::resolve($dependency->name);
			}
		}

		return (array) $dependencies;
	}

	/**
	 * Resolves optional parameters for our dependency injection
	 * pretty much took backport straight from L4's Illuminate\Container
	 *
	 * @param ReflectionParameter
	 * @return default value
     * @throws \Exception
	 */
	protected static function resolveNonClass($parameter)
	{
		if ($parameter->isDefaultValueAvailable())
		{
			return $parameter->getDefaultValue();
		}
		else
		{
			throw new \Exception("Unresolvable dependency resolving [$parameter].");
		}
	}

}