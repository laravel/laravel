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

		$concrete = array_get(static::$registry, "{$type}.resolver", $type);

		// We're ready to instantiate an instance of the concrete type registered for
		// the binding. This will instantiate the type, as well as resolve any of
		// its nested dependencies recursively until they are each resolved.
		if ($concrete == $type or $concrete instanceof Closure)
		{
			$object = static::build($concrete);
		}
		else
		{
			$object = static::resolve($concrete);
		}

		// If the requested type is registered as a singleton, we want to cache off
		// the instance in memory so we can return it later without creating an
		// entirely new instances of the object on each subsequent request.
		if (isset(static::$registry[$type]['singleton']))
		{
			static::$singletons[$type] = $object;
		}

		return $object;
	}

	/**
	 * Instantiate an instance of the given type.
	 *
	 * @param  string  $type
	 * @param  array   $parameters
	 * @return mixed
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
		// an abstract type such as an Interface of Abstract Class and there is
		// no binding registered for the abstraction so we need to bail out.
		if ( ! $reflector->isInstantiable())
		{
			throw new Exception("Resolution target [$type] is not instantiable.");
		}

		$constructor = $reflector->getConstructor();

		// If there is no constructor, that means there are no dependencies and
		// we can just resolve an instance of the object right away without
		// resolving any other types or dependencies from the container.
		if (is_null($constructor))
		{
			return new $type;
		}

		$dependencies = static::dependencies($constructor->getParameters());

		return $reflector->newInstanceArgs($dependencies);
	}

	/**
	 * Resolve all of the dependencies from the ReflectionParameters.
	 *
	 * @param  array  $parameterrs
	 * @return array
	 */
	protected static function dependencies($parameters)
	{
		$dependencies = array();

		foreach ($parameters as $parameter)
		{
			$dependency = $parameter->getClass();

			// If the class is null, it means the dependency is a string or some other
			// primitive type, which we can not esolve since it is not a class and
			// we'll just bomb out with an error since we have nowhere to go.
			if (is_null($dependency))
			{
				throw new Exception("Unresolvable dependency resolving [$parameter].");
			}

			$dependencies[] = static::resolve($dependency->name);
		}

		return (array) $dependencies;
	}

}