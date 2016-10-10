<?php namespace Illuminate\Container;

use Closure;
use ArrayAccess;
use ReflectionClass;
use ReflectionParameter;

class BindingResolutionException extends \Exception {}

class Container implements ArrayAccess {

	/**
	 * An array of the types that have been resolved.
	 *
	 * @var array
	 */
	protected $resolved = array();

	/**
	 * The container's bindings.
	 *
	 * @var array
	 */
	protected $bindings = array();

	/**
	 * The container's shared instances.
	 *
	 * @var array
	 */
	protected $instances = array();

	/**
	 * The registered type aliases.
	 *
	 * @var array
	 */
	protected $aliases = array();

	/**
	 * All of the registered rebound callbacks.
	 *
	 * @var array
	 */
	protected $reboundCallbacks = array();

	/**
	 * All of the registered resolving callbacks.
	 *
	 * @var array
	 */
	protected $resolvingCallbacks = array();

	/**
	 * All of the global resolving callbacks.
	 *
	 * @var array
	 */
	protected $globalResolvingCallbacks = array();

	/**
	 * Determine if a given string is resolvable.
	 *
	 * @param  string  $abstract
	 * @return bool
	 */
	protected function resolvable($abstract)
	{
		return $this->bound($abstract) || $this->isAlias($abstract);
	}

	/**
	 * Determine if the given abstract type has been bound.
	 *
	 * @param  string  $abstract
	 * @return bool
	 */
	public function bound($abstract)
	{
		return isset($this[$abstract]) || isset($this->instances[$abstract]);
	}

	/**
	 * Determine if a given string is an alias.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function isAlias($name)
	{
		return isset($this->aliases[$name]);
	}

	/**
	 * Register a binding with the container.
	 *
	 * @param  string               $abstract
	 * @param  Closure|string|null  $concrete
	 * @param  bool                 $shared
	 * @return void
	 */
	public function bind($abstract, $concrete = null, $shared = false)
	{
		// If the given types are actually an array, we will assume an alias is being
		// defined and will grab this "real" abstract class name and register this
		// alias with the container so that it can be used as a shortcut for it.
		if (is_array($abstract))
		{
			list($abstract, $alias) = $this->extractAlias($abstract);

			$this->alias($abstract, $alias);
		}

		// If no concrete type was given, we will simply set the concrete type to the
		// abstract type. This will allow concrete type to be registered as shared
		// without being forced to state their classes in both of the parameter.
		$this->dropStaleInstances($abstract);

		if (is_null($concrete))
		{
			$concrete = $abstract;
		}

		// If the factory is not a Closure, it means it is just a class name which is
		// is bound into this container to the abstract type and we will just wrap
		// it up inside a Closure to make things more convenient when extending.
		if ( ! $concrete instanceof Closure)
		{
			$concrete = $this->getClosure($abstract, $concrete);
		}

		$bound = $this->bound($abstract);

		$this->bindings[$abstract] = compact('concrete', 'shared');

		// If the abstract type was already bound in this container, we will fire the
		// rebound listener so that any objects which have already gotten resolved
		// can have their copy of the object updated via the listener callbacks.
		if ($bound)
		{
			$this->rebound($abstract);
		}
	}

	/**
	 * Get the Closure to be used when building a type.
	 *
	 * @param  string  $abstract
	 * @param  string  $concrete
	 * @return \Closure
	 */
	protected function getClosure($abstract, $concrete)
	{
		return function($c, $parameters = array()) use ($abstract, $concrete)
		{
			$method = ($abstract == $concrete) ? 'build' : 'make';

			return $c->$method($concrete, $parameters);
		};
	}

	/**
	 * Register a binding if it hasn't already been registered.
	 *
	 * @param  string               $abstract
	 * @param  Closure|string|null  $concrete
	 * @param  bool                 $shared
	 * @return void
	 */
	public function bindIf($abstract, $concrete = null, $shared = false)
	{
		if ( ! $this->bound($abstract))
		{
			$this->bind($abstract, $concrete, $shared);
		}
	}

	/**
	 * Register a shared binding in the container.
	 *
	 * @param  string               $abstract
	 * @param  Closure|string|null  $concrete
	 * @return void
	 */
	public function singleton($abstract, $concrete = null)
	{
		return $this->bind($abstract, $concrete, true);
	}

	/**
	 * Wrap a Closure such that it is shared.
	 *
	 * @param  Closure  $closure
	 * @return Closure
	 */
	public function share(Closure $closure)
	{
		return function($container) use ($closure)
		{
			// We'll simply declare a static variable within the Closures and if it has
			// not been set we will execute the given Closures to resolve this value
			// and return it back to these consumers of the method as an instance.
			static $object;

			if (is_null($object))
			{
				$object = $closure($container);
			}

			return $object;
		};
	}

	/**
	 * Bind a shared Closure into the container.
	 *
	 * @param  string  $abstract
	 * @param  \Closure  $closure
	 * @return void
	 */
	public function bindShared($abstract, Closure $closure)
	{
		return $this->bind($abstract, $this->share($closure), true);
	}

	/**
	 * "Extend" an abstract type in the container.
	 *
	 * @param  string   $abstract
	 * @param  Closure  $closure
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	public function extend($abstract, Closure $closure)
	{
		if ( ! isset($this->bindings[$abstract]))
		{
			throw new \InvalidArgumentException("Type {$abstract} is not bound.");
		}

		if (isset($this->instances[$abstract]))
		{
			$this->instances[$abstract] = $closure($this->instances[$abstract], $this);

			$this->rebound($abstract);
		}
		else
		{
			$extender = $this->getExtender($abstract, $closure);

			$this->bind($abstract, $extender, $this->isShared($abstract));
		}
	}

	/**
	 * Get an extender Closure for resolving a type.
	 *
	 * @param  string  $abstract
	 * @param  \Closure  $closure
	 * @return \Closure
	 */
	protected function getExtender($abstract, Closure $closure)
	{
		// To "extend" a binding, we will grab the old "resolver" Closure and pass it
		// into a new one. The old resolver will be called first and the result is
		// handed off to the "new" resolver, along with this container instance.
		$resolver = $this->bindings[$abstract]['concrete'];

		return function($container) use ($resolver, $closure)
		{
			return $closure($resolver($container), $container);
		};
	}

	/**
	 * Register an existing instance as shared in the container.
	 *
	 * @param  string  $abstract
	 * @param  mixed   $instance
	 * @return void
	 */
	public function instance($abstract, $instance)
	{
		// First, we will extract the alias from the abstract if it is an array so we
		// are using the correct name when binding the type. If we get an alias it
		// will be registered with the container so we can resolve it out later.
		if (is_array($abstract))
		{
			list($abstract, $alias) = $this->extractAlias($abstract);

			$this->alias($abstract, $alias);
		}

		unset($this->aliases[$abstract]);

		// We'll check to determine if this type has been bound before, and if it has
		// we will fire the rebound callbacks registered with the container and it
		// can be updated with consuming classes that have gotten resolved here.
		$bound = $this->bound($abstract);

		$this->instances[$abstract] = $instance;

		if ($bound)
		{
			$this->rebound($abstract);
		}
	}

	/**
	 * Alias a type to a shorter name.
	 *
	 * @param  string  $abstract
	 * @param  string  $alias
	 * @return void
	 */
	public function alias($abstract, $alias)
	{
		$this->aliases[$alias] = $abstract;
	}

	/**
	 * Extract the type and alias from a given definition.
	 *
	 * @param  array  $definition
	 * @return array
	 */
	protected function extractAlias(array $definition)
	{
		return array(key($definition), current($definition));
	}

	/**
	 * Bind a new callback to an abstract's rebind event.
	 *
	 * @param  string  $abstract
	 * @param  \Closure  $callback
	 * @return mixed
	 */
	public function rebinding($abstract, Closure $callback)
	{
		$this->reboundCallbacks[$abstract][] = $callback;

		if ($this->bound($abstract)) return $this->make($abstract);
	}

	/**
	 * Refresh an instance on the given target and method.
	 *
	 * @param  string  $abstract
	 * @param  mixed  $target
	 * @param  string  $method
	 * @return mixed
	 */
	public function refresh($abstract, $target, $method)
	{
		return $this->rebinding($abstract, function($app, $instance) use ($target, $method)
		{
			$target->{$method}($instance);
		});
	}

	/**
	 * Fire the "rebound" callbacks for the given abstract type.
	 *
	 * @param  string  $abstract
	 * @return void
	 */
	protected function rebound($abstract)
	{
		$instance = $this->make($abstract);

		foreach ($this->getReboundCallbacks($abstract) as $callback)
		{
			call_user_func($callback, $this, $instance);
		}
	}

	/**
	 * Get the rebound callbacks for a given type.
	 *
	 * @param  string  $abstract
	 * @return array
	 */
	protected function getReboundCallbacks($abstract)
	{
		if (isset($this->reboundCallbacks[$abstract]))
		{
			return $this->reboundCallbacks[$abstract];
		}
		else
		{
			return array();
		}
	}

	/**
	 * Resolve the given type from the container.
	 *
	 * @param  string  $abstract
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function make($abstract, $parameters = array())
	{
		$abstract = $this->getAlias($abstract);

		$this->resolved[$abstract] = true;

		// If an instance of the type is currently being managed as a singleton we'll
		// just return an existing instance instead of instantiating new instances
		// so the developer can keep using the same objects instance every time.
		if (isset($this->instances[$abstract]))
		{
			return $this->instances[$abstract];
		}

		$concrete = $this->getConcrete($abstract);

		// We're ready to instantiate an instance of the concrete type registered for
		// the binding. This will instantiate the types, as well as resolve any of
		// its "nested" dependencies recursively until all have gotten resolved.
		if ($this->isBuildable($concrete, $abstract))
		{
			$object = $this->build($concrete, $parameters);
		}
		else
		{
			$object = $this->make($concrete, $parameters);
		}

		// If the requested type is registered as a singleton we'll want to cache off
		// the instances in "memory" so we can return it later without creating an
		// entirely new instance of an object on each subsequent request for it.
		if ($this->isShared($abstract))
		{
			$this->instances[$abstract] = $object;
		}

		$this->fireResolvingCallbacks($abstract, $object);

		return $object;
	}

	/**
	 * Get the concrete type for a given abstract.
	 *
	 * @param  string  $abstract
	 * @return mixed   $concrete
	 */
	protected function getConcrete($abstract)
	{
		// If we don't have a registered resolver or concrete for the type, we'll just
		// assume each type is a concrete name and will attempt to resolve it as is
		// since the container should be able to resolve concretes automatically.
		if ( ! isset($this->bindings[$abstract]))
		{
			if ($this->missingLeadingSlash($abstract) && isset($this->bindings['\\'.$abstract]))
			{
				$abstract = '\\'.$abstract;
			}

			return $abstract;
		}
		else
		{
			return $this->bindings[$abstract]['concrete'];
		}
	}

	/**
	 * Determine if the given abstract has a leading slash.
	 *
	 * @param  string  $abstract
	 * @return bool
	 */
	protected function missingLeadingSlash($abstract)
	{
		return is_string($abstract) && strpos($abstract, '\\') !== 0;
	}

	/**
	 * Instantiate a concrete instance of the given type.
	 *
	 * @param  string  $concrete
	 * @param  array   $parameters
	 * @return mixed
	 *
	 * @throws BindingResolutionException
	 */
	public function build($concrete, $parameters = array())
	{
		// If the concrete type is actually a Closure, we will just execute it and
		// hand back the results of the functions, which allows functions to be
		// used as resolvers for more fine-tuned resolution of these objects.
		if ($concrete instanceof Closure)
		{
			return $concrete($this, $parameters);
		}

		$reflector = new ReflectionClass($concrete);

		// If the type is not instantiable, the developer is attempting to resolve
		// an abstract type such as an Interface of Abstract Class and there is
		// no binding registered for the abstractions so we need to bail out.
		if ( ! $reflector->isInstantiable())
		{
			$message = "Target [$concrete] is not instantiable.";

			throw new BindingResolutionException($message);
		}

		$constructor = $reflector->getConstructor();

		// If there are no constructors, that means there are no dependencies then
		// we can just resolve the instances of the objects right away, without
		// resolving any other types or dependencies out of these containers.
		if (is_null($constructor))
		{
			return new $concrete;
		}

		$dependencies = $constructor->getParameters();

		// Once we have all the constructor's parameters we can create each of the
		// dependency instances and then use the reflection instances to make a
		// new instance of this class, injecting the created dependencies in.
		$parameters = $this->keyParametersByArgument(
			$dependencies, $parameters
		);

		$instances = $this->getDependencies(
			$dependencies, $parameters
		);

		return $reflector->newInstanceArgs($instances);
	}

	/**
	 * Resolve all of the dependencies from the ReflectionParameters.
	 *
	 * @param  array  $parameters
	 * @param  array  $primitives
	 * @return array
	 */
	protected function getDependencies($parameters, array $primitives = array())
	{
		$dependencies = array();

		foreach ($parameters as $parameter)
		{
			$dependency = $parameter->getClass();

			// If the class is null, it means the dependency is a string or some other
			// primitive type which we can not resolve since it is not a class and
			// we will just bomb out with an error since we have no-where to go.
			if (array_key_exists($parameter->name, $primitives))
			{
				$dependencies[] = $primitives[$parameter->name];
			}
			elseif (is_null($dependency))
			{
				$dependencies[] = $this->resolveNonClass($parameter);
			}
			else
			{
				$dependencies[] = $this->resolveClass($parameter);
			}
		}

		return (array) $dependencies;
	}

	/**
	 * Resolve a non-class hinted dependency.
	 *
	 * @param  ReflectionParameter  $parameter
	 * @return mixed
	 *
	 * @throws BindingResolutionException
	 */
	protected function resolveNonClass(ReflectionParameter $parameter)
	{
		if ($parameter->isDefaultValueAvailable())
		{
			return $parameter->getDefaultValue();
		}
		else
		{
			$message = "Unresolvable dependency resolving [$parameter].";

			throw new BindingResolutionException($message);
		}
	}

	/**
	 * Resolve a class based dependency from the container.
	 *
	 * @param  \ReflectionParameter  $parameter
	 * @return mixed
	 *
	 * @throws BindingResolutionException
	 */
	protected function resolveClass(ReflectionParameter $parameter)
	{
		try
		{
			return $this->make($parameter->getClass()->name);
		}

		// If we can not resolve the class instance, we will check to see if the value
		// is optional, and if it is we will return the optional parameter value as
		// the value of the dependency, similarly to how we do this with scalars.
		catch (BindingResolutionException $e)
		{
			if ($parameter->isOptional())
			{
				return $parameter->getDefaultValue();
			}
			else
			{
				throw $e;
			}
		}
	}

	/**
	 * If extra parameters are passed by numeric ID, rekey them by argument name.
	 *
	 * @param  array  $dependencies
	 * @param  array  $parameters
	 * @param  array
	 * @return array
	 */
	protected function keyParametersByArgument(array $dependencies, array $parameters)
	{
		foreach ($parameters as $key => $value)
		{
			if (is_numeric($key))
			{
				unset($parameters[$key]);

				$parameters[$dependencies[$key]->name] = $value;
			}
		}

		return $parameters;
	}

	/**
	 * Register a new resolving callback.
	 *
	 * @param  string  $abstract
	 * @param  \Closure  $callback
	 * @return void
	 */
	public function resolving($abstract, Closure $callback)
	{
		$this->resolvingCallbacks[$abstract][] = $callback;
	}

	/**
	 * Register a new resolving callback for all types.
	 *
	 * @param  \Closure  $callback
	 * @return void
	 */
	public function resolvingAny(Closure $callback)
	{
		$this->globalResolvingCallbacks[] = $callback;
	}

	/**
	 * Fire all of the resolving callbacks.
	 *
	 * @param  mixed  $object
	 * @return void
	 */
	protected function fireResolvingCallbacks($abstract, $object)
	{
		if (isset($this->resolvingCallbacks[$abstract]))
		{
			$this->fireCallbackArray($object, $this->resolvingCallbacks[$abstract]);
		}

		$this->fireCallbackArray($object, $this->globalResolvingCallbacks);
	}

	/**
	 * Fire an array of callbacks with an object.
	 *
	 * @param  mixed  $object
	 * @param  array  $callbacks
	 */
	protected function fireCallbackArray($object, array $callbacks)
	{
		foreach ($callbacks as $callback)
		{
			call_user_func($callback, $object, $this);
		}
	}

	/**
	 * Determine if a given type is shared.
	 *
	 * @param  string  $abstract
	 * @return bool
	 */
	public function isShared($abstract)
	{
		if (isset($this->bindings[$abstract]['shared']))
		{
			$shared = $this->bindings[$abstract]['shared'];
		}
		else
		{
			$shared = false;
		}

		return isset($this->instances[$abstract]) || $shared === true;
	}

	/**
	 * Determine if the given concrete is buildable.
	 *
	 * @param  mixed   $concrete
	 * @param  string  $abstract
	 * @return bool
	 */
	protected function isBuildable($concrete, $abstract)
	{
		return $concrete === $abstract || $concrete instanceof Closure;
	}

	/**
	 * Get the alias for an abstract if available.
	 *
	 * @param  string  $abstract
	 * @return string
	 */
	protected function getAlias($abstract)
	{
		return isset($this->aliases[$abstract]) ? $this->aliases[$abstract] : $abstract;
	}

	/**
	 * Get the container's bindings.
	 *
	 * @return array
	 */
	public function getBindings()
	{
		return $this->bindings;
	}

	/**
	 * Drop all of the stale instances and aliases.
	 *
	 * @param  string  $abstract
	 * @return void
	 */
	protected function dropStaleInstances($abstract)
	{
		unset($this->instances[$abstract]);

		unset($this->aliases[$abstract]);
	}

	/**
	 * Remove a resolved instance from the instance cache.
	 *
	 * @param  string  $abstract
	 * @return void
	 */
	public function forgetInstance($abstract)
	{
		unset($this->instances[$abstract]);
	}

	/**
	 * Clear all of the instances from the container.
	 *
	 * @return void
	 */
	public function forgetInstances()
	{
		$this->instances = array();
	}

	/**
	 * Determine if a given offset exists.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return isset($this->bindings[$key]);
	}

	/**
	 * Get the value at a given offset.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->make($key);
	}

	/**
	 * Set the value at a given offset.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		// If the value is not a Closure, we will make it one. This simply gives
		// more "drop-in" replacement functionality for the Pimple which this
		// container's simplest functions are base modeled and built after.
		if ( ! $value instanceof Closure)
		{
			$value = function() use ($value)
			{
				return $value;
			};
		}

		$this->bind($key, $value);
	}

	/**
	 * Unset the value at a given offset.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function offsetUnset($key)
	{
		unset($this->bindings[$key]);

		unset($this->instances[$key]);
	}

}
