<?php

namespace Illuminate\Container;

use ArrayAccess;
use Closure;
use Exception;
use Illuminate\Container\Attributes\Bind;
use Illuminate\Container\Attributes\Scoped;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\CircularDependencyException;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Contracts\Container\ContextualAttribute;
use Illuminate\Contracts\Container\SelfBuilding;
use Illuminate\Support\Traits\ReflectsClosures;
use LogicException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionParameter;
use TypeError;

class Container implements ArrayAccess, ContainerContract
{
    use ReflectsClosures;

    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    /**
     * An array of the types that have been resolved.
     *
     * @var bool[]
     */
    protected $resolved = [];

    /**
     * The container's bindings.
     *
     * @var array[]
     */
    protected $bindings = [];

    /**
     * The container's method bindings.
     *
     * @var \Closure[]
     */
    protected $methodBindings = [];

    /**
     * The container's shared instances.
     *
     * @var object[]
     */
    protected $instances = [];

    /**
     * The container's scoped instances.
     *
     * @var array
     */
    protected $scopedInstances = [];

    /**
     * The registered type aliases.
     *
     * @var string[]
     */
    protected $aliases = [];

    /**
     * The registered aliases keyed by the abstract name.
     *
     * @var array[]
     */
    protected $abstractAliases = [];

    /**
     * The extension closures for services.
     *
     * @var array[]
     */
    protected $extenders = [];

    /**
     * All of the registered tags.
     *
     * @var array[]
     */
    protected $tags = [];

    /**
     * The stack of concretions currently being built.
     *
     * @var array[]
     */
    protected $buildStack = [];

    /**
     * The parameter override stack.
     *
     * @var array[]
     */
    protected $with = [];

    /**
     * The contextual binding map.
     *
     * @var array[]
     */
    public $contextual = [];

    /**
     * The contextual attribute handlers.
     *
     * @var array[]
     */
    public $contextualAttributes = [];

    /**
     * Whether an abstract class has already had its attributes checked for bindings.
     *
     * @var array<class-string, true>
     */
    protected $checkedForAttributeBindings = [];

    /**
     * Whether a class has already been checked for Singleton or Scoped attributes.
     *
     * @var array<class-string, "scoped"|"singleton"|null>
     */
    protected $checkedForSingletonOrScopedAttributes = [];

    /**
     * All of the registered rebound callbacks.
     *
     * @var array[]
     */
    protected $reboundCallbacks = [];

    /**
     * All of the global before resolving callbacks.
     *
     * @var \Closure[]
     */
    protected $globalBeforeResolvingCallbacks = [];

    /**
     * All of the global resolving callbacks.
     *
     * @var \Closure[]
     */
    protected $globalResolvingCallbacks = [];

    /**
     * All of the global after resolving callbacks.
     *
     * @var \Closure[]
     */
    protected $globalAfterResolvingCallbacks = [];

    /**
     * All of the before resolving callbacks by class type.
     *
     * @var array[]
     */
    protected $beforeResolvingCallbacks = [];

    /**
     * All of the resolving callbacks by class type.
     *
     * @var array[]
     */
    protected $resolvingCallbacks = [];

    /**
     * All of the after resolving callbacks by class type.
     *
     * @var array[]
     */
    protected $afterResolvingCallbacks = [];

    /**
     * All of the after resolving attribute callbacks by class type.
     *
     * @var array[]
     */
    protected $afterResolvingAttributeCallbacks = [];

    /**
     * The callback used to determine the container's environment.
     *
     * @var (callable(array<int, string>|string): bool|string)|null
     */
    protected $environmentResolver = null;

    /**
     * Define a contextual binding.
     *
     * @param  array|string  $concrete
     * @return \Illuminate\Contracts\Container\ContextualBindingBuilder
     */
    public function when($concrete)
    {
        $aliases = [];

        foreach (Util::arrayWrap($concrete) as $c) {
            $aliases[] = $this->getAlias($c);
        }

        return new ContextualBindingBuilder($this, $aliases);
    }

    /**
     * Define a contextual binding based on an attribute.
     *
     * @return void
     */
    public function whenHasAttribute(string $attribute, Closure $handler)
    {
        $this->contextualAttributes[$attribute] = $handler;
    }

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound($abstract)
    {
        return isset($this->bindings[$abstract]) ||
               isset($this->instances[$abstract]) ||
               $this->isAlias($abstract);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $id): bool
    {
        return $this->bound($id);
    }

    /**
     * Determine if the given abstract type has been resolved.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function resolved($abstract)
    {
        if ($this->isAlias($abstract)) {
            $abstract = $this->getAlias($abstract);
        }

        return isset($this->resolved[$abstract]) ||
               isset($this->instances[$abstract]);
    }

    /**
     * Determine if a given type is shared.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function isShared($abstract)
    {
        if (isset($this->instances[$abstract])) {
            return true;
        }

        if (isset($this->bindings[$abstract]['shared']) && $this->bindings[$abstract]['shared'] === true) {
            return true;
        }

        if (! class_exists($abstract)) {
            return false;
        }

        if (($scopedType = $this->getScopedTyped($abstract)) === null) {
            return false;
        }

        if ($scopedType === 'scoped') {
            if (! in_array($abstract, $this->scopedInstances, true)) {
                $this->scopedInstances[] = $abstract;
            }
        }

        return true;
    }

    /**
     * Determine if a ReflectionClass has scoping attributes applied.
     *
     * @param  ReflectionClass<object>|class-string  $reflection
     * @return "singleton"|"scoped"|null
     */
    protected function getScopedTyped(ReflectionClass|string $reflection): ?string
    {
        $className = $reflection instanceof ReflectionClass
            ? $reflection->getName()
            : $reflection;

        if (array_key_exists($className, $this->checkedForSingletonOrScopedAttributes)) {
            return $this->checkedForSingletonOrScopedAttributes[$className];
        }

        try {
            $reflection = $reflection instanceof ReflectionClass
                ? $reflection
                : new ReflectionClass($reflection);
        } catch (ReflectionException) {
            return $this->checkedForSingletonOrScopedAttributes[$className] = null;
        }

        $type = null;

        if (! empty($reflection->getAttributes(Singleton::class))) {
            $type = 'singleton';
        } elseif (! empty($reflection->getAttributes(Scoped::class))) {
            $type = 'scoped';
        }

        return $this->checkedForSingletonOrScopedAttributes[$className] = $type;
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
     * @param  \Closure|string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     *
     * @throws \TypeError
     * @throws ReflectionException
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        if ($abstract instanceof Closure) {
            return $this->bindBasedOnClosureReturnTypes(
                $abstract, $concrete, $shared
            );
        }

        $this->dropStaleInstances($abstract);

        // If no concrete type was given, we will simply set the concrete type to the
        // abstract type. After that, the concrete type to be registered as shared
        // without being forced to state their classes in both of the parameters.
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        // If the factory is not a Closure, it means it is just a class name which is
        // bound into this container to the abstract type and we will just wrap it
        // up inside its own Closure to give us more convenience when extending.
        if (! $concrete instanceof Closure) {
            if (! is_string($concrete)) {
                throw new TypeError(self::class.'::bind(): Argument #2 ($concrete) must be of type Closure|string|null');
            }

            $concrete = $this->getClosure($abstract, $concrete);
        }

        $this->bindings[$abstract] = ['concrete' => $concrete, 'shared' => $shared];

        // If the abstract type was already resolved in this container we'll fire the
        // rebound listener so that any objects which have already gotten resolved
        // can have their copy of the object updated via the listener callbacks.
        if ($this->resolved($abstract)) {
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
        return function ($container, $parameters = []) use ($abstract, $concrete) {
            if ($abstract == $concrete) {
                return $container->build($concrete);
            }

            return $container->resolve(
                $concrete, $parameters, raiseEvents: false
            );
        };
    }

    /**
     * Determine if the container has a method binding.
     *
     * @param  string  $method
     * @return bool
     */
    public function hasMethodBinding($method)
    {
        return isset($this->methodBindings[$method]);
    }

    /**
     * Bind a callback to resolve with Container::call.
     *
     * @param  array|string  $method
     * @param  \Closure  $callback
     * @return void
     */
    public function bindMethod($method, $callback)
    {
        $this->methodBindings[$this->parseBindMethod($method)] = $callback;
    }

    /**
     * Get the method to be bound in class@method format.
     *
     * @param  array|string  $method
     * @return string
     */
    protected function parseBindMethod($method)
    {
        if (is_array($method)) {
            return $method[0].'@'.$method[1];
        }

        return $method;
    }

    /**
     * Get the method binding for the given method.
     *
     * @param  string  $method
     * @param  mixed  $instance
     * @return mixed
     */
    public function callMethodBinding($method, $instance)
    {
        return call_user_func($this->methodBindings[$method], $instance, $this);
    }

    /**
     * Add a contextual binding to the container.
     *
     * @param  string  $concrete
     * @param  \Closure|string  $abstract
     * @param  \Closure|string  $implementation
     * @return void
     */
    public function addContextualBinding($concrete, $abstract, $implementation)
    {
        $this->contextual[$concrete][$this->getAlias($abstract)] = $implementation;
    }

    /**
     * Register a binding if it hasn't already been registered.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bindIf($abstract, $concrete = null, $shared = false)
    {
        if (! $this->bound($abstract)) {
            $this->bind($abstract, $concrete, $shared);
        }
    }

    /**
     * Register a shared binding in the container.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register a shared binding if it hasn't already been registered.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singletonIf($abstract, $concrete = null)
    {
        if (! $this->bound($abstract)) {
            $this->singleton($abstract, $concrete);
        }
    }

    /**
     * Register a scoped binding in the container.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function scoped($abstract, $concrete = null)
    {
        $this->scopedInstances[] = $abstract;

        $this->singleton($abstract, $concrete);
    }

    /**
     * Register a scoped binding if it hasn't already been registered.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function scopedIf($abstract, $concrete = null)
    {
        if (! $this->bound($abstract)) {
            $this->scoped($abstract, $concrete);
        }
    }

    /**
     * Register a binding with the container based on the given Closure's return types.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    protected function bindBasedOnClosureReturnTypes($abstract, $concrete = null, $shared = false)
    {
        $abstracts = $this->closureReturnTypes($abstract);

        $concrete = $abstract;

        foreach ($abstracts as $abstract) {
            $this->bind($abstract, $concrete, $shared);
        }
    }

    /**
     * "Extend" an abstract type in the container.
     *
     * @param  string  $abstract
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function extend($abstract, Closure $closure)
    {
        $abstract = $this->getAlias($abstract);

        if (isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $closure($this->instances[$abstract], $this);

            $this->rebound($abstract);
        } else {
            $this->extenders[$abstract][] = $closure;

            if ($this->resolved($abstract)) {
                $this->rebound($abstract);
            }
        }
    }

    /**
     * Register an existing instance as shared in the container.
     *
     * @template TInstance of mixed
     *
     * @param  string  $abstract
     * @param  TInstance  $instance
     * @return TInstance
     */
    public function instance($abstract, $instance)
    {
        $this->removeAbstractAlias($abstract);

        $isBound = $this->bound($abstract);

        unset($this->aliases[$abstract]);

        // We'll check to determine if this type has been bound before, and if it has
        // we will fire the rebound callbacks registered with the container and it
        // can be updated with consuming classes that have gotten resolved here.
        $this->instances[$abstract] = $instance;

        if ($isBound) {
            $this->rebound($abstract);
        }

        return $instance;
    }

    /**
     * Remove an alias from the contextual binding alias cache.
     *
     * @param  string  $searched
     * @return void
     */
    protected function removeAbstractAlias($searched)
    {
        if (! isset($this->aliases[$searched])) {
            return;
        }

        foreach ($this->abstractAliases as $abstract => $aliases) {
            foreach ($aliases as $index => $alias) {
                if ($alias == $searched) {
                    unset($this->abstractAliases[$abstract][$index]);
                }
            }
        }
    }

    /**
     * Assign a set of tags to a given binding.
     *
     * @param  array|string  $abstracts
     * @param  mixed  ...$tags
     * @return void
     */
    public function tag($abstracts, $tags)
    {
        $tags = is_array($tags) ? $tags : array_slice(func_get_args(), 1);

        foreach ($tags as $tag) {
            if (! isset($this->tags[$tag])) {
                $this->tags[$tag] = [];
            }

            foreach ((array) $abstracts as $abstract) {
                $this->tags[$tag][] = $abstract;
            }
        }
    }

    /**
     * Resolve all of the bindings for a given tag.
     *
     * @param  string  $tag
     * @return iterable
     */
    public function tagged($tag)
    {
        if (! isset($this->tags[$tag])) {
            return [];
        }

        return new RewindableGenerator(function () use ($tag) {
            foreach ($this->tags[$tag] as $abstract) {
                yield $this->make($abstract);
            }
        }, count($this->tags[$tag]));
    }

    /**
     * Alias a type to a different name.
     *
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     *
     * @throws \LogicException
     */
    public function alias($abstract, $alias)
    {
        if ($alias === $abstract) {
            throw new LogicException("[{$abstract}] is aliased to itself.");
        }

        $this->removeAbstractAlias($alias);

        $this->aliases[$alias] = $abstract;

        $this->abstractAliases[$abstract][] = $alias;
    }

    /**
     * Bind a new callback to an abstract's rebind event.
     *
     * @param  string  $abstract
     * @return mixed
     */
    public function rebinding($abstract, Closure $callback)
    {
        $this->reboundCallbacks[$abstract = $this->getAlias($abstract)][] = $callback;

        if ($this->bound($abstract)) {
            return $this->make($abstract);
        }
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
        return $this->rebinding($abstract, function ($app, $instance) use ($target, $method) {
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
        if (! $callbacks = $this->getReboundCallbacks($abstract)) {
            return;
        }

        $instance = $this->make($abstract);

        foreach ($callbacks as $callback) {
            $callback($this, $instance);
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
        return $this->reboundCallbacks[$abstract] ?? [];
    }

    /**
     * Wrap the given closure such that its dependencies will be injected when executed.
     *
     * @return \Closure
     */
    public function wrap(Closure $callback, array $parameters = [])
    {
        return fn () => $this->call($callback, $parameters);
    }

    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param  callable|string  $callback
     * @param  array<string, mixed>  $parameters
     * @param  string|null  $defaultMethod
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function call($callback, array $parameters = [], $defaultMethod = null)
    {
        $pushedToBuildStack = false;

        if (($className = $this->getClassForCallable($callback)) && ! in_array(
            $className,
            $this->buildStack,
            true
        )) {
            $this->buildStack[] = $className;

            $pushedToBuildStack = true;
        }

        $result = BoundMethod::call($this, $callback, $parameters, $defaultMethod);

        if ($pushedToBuildStack) {
            array_pop($this->buildStack);
        }

        return $result;
    }

    /**
     * Get the class name for the given callback, if one can be determined.
     *
     * @param  callable|string  $callback
     * @return string|false
     */
    protected function getClassForCallable($callback)
    {
        if (is_callable($callback) &&
            ! ($reflector = new ReflectionFunction($callback(...)))->isAnonymous()) {
            return $reflector->getClosureScopeClass()->name ?? false;
        }

        return false;
    }

    /**
     * Get a closure to resolve the given type from the container.
     *
     * @template TClass of object
     *
     * @param  string|class-string<TClass>  $abstract
     * @return ($abstract is class-string<TClass> ? \Closure(): TClass : \Closure(): mixed)
     */
    public function factory($abstract)
    {
        return fn () => $this->make($abstract);
    }

    /**
     * An alias function name for make().
     *
     * @template TClass of object
     *
     * @param  string|class-string<TClass>|callable  $abstract
     * @return ($abstract is class-string<TClass> ? TClass : mixed)
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function makeWith($abstract, array $parameters = [])
    {
        return $this->make($abstract, $parameters);
    }

    /**
     * Resolve the given type from the container.
     *
     * @template TClass of object
     *
     * @param  string|class-string<TClass>  $abstract
     * @return ($abstract is class-string<TClass> ? TClass : mixed)
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function make($abstract, array $parameters = [])
    {
        return $this->resolve($abstract, $parameters);
    }

    /**
     * {@inheritdoc}
     *
     * @template TClass of object
     *
     * @param  string|class-string<TClass>  $id
     * @return ($id is class-string<TClass> ? TClass : mixed)
     */
    public function get(string $id)
    {
        try {
            return $this->resolve($id);
        } catch (Exception $e) {
            if ($this->has($id) || $e instanceof CircularDependencyException) {
                throw $e;
            }

            throw new EntryNotFoundException($id, is_int($e->getCode()) ? $e->getCode() : 0, $e);
        }
    }

    /**
     * Resolve the given type from the container.
     *
     * @template TClass of object
     *
     * @param  string|class-string<TClass>|callable  $abstract
     * @param  array  $parameters
     * @param  bool  $raiseEvents
     * @return ($abstract is class-string<TClass> ? TClass : mixed)
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Contracts\Container\CircularDependencyException
     */
    protected function resolve($abstract, $parameters = [], $raiseEvents = true)
    {
        $abstract = $this->getAlias($abstract);

        // First we'll fire any event handlers which handle the "before" resolving of
        // specific types. This gives some hooks the chance to add various extends
        // calls to change the resolution of objects that they're interested in.
        if ($raiseEvents) {
            $this->fireBeforeResolvingCallbacks($abstract, $parameters);
        }

        $concrete = $this->getContextualConcrete($abstract);

        $needsContextualBuild = ! empty($parameters) || ! is_null($concrete);

        // If an instance of the type is currently being managed as a singleton we'll
        // just return an existing instance instead of instantiating new instances
        // so the developer can keep using the same objects instance every time.
        if (isset($this->instances[$abstract]) && ! $needsContextualBuild) {
            return $this->instances[$abstract];
        }

        $this->with[] = $parameters;

        if (is_null($concrete)) {
            $concrete = $this->getConcrete($abstract);
        }

        // We're ready to instantiate an instance of the concrete type registered for
        // the binding. This will instantiate the types, as well as resolve any of
        // its "nested" dependencies recursively until all have gotten resolved.
        $object = $this->isBuildable($concrete, $abstract)
            ? $this->build($concrete)
            : $this->make($concrete);

        // If we defined any extenders for this type, we'll need to spin through them
        // and apply them to the object being built. This allows for the extension
        // of services, such as changing configuration or decorating the object.
        foreach ($this->getExtenders($abstract) as $extender) {
            $object = $extender($object, $this);
        }

        // If the requested type is registered as a singleton we'll want to cache off
        // the instances in "memory" so we can return it later without creating an
        // entirely new instance of an object on each subsequent request for it.
        if ($this->isShared($abstract) && ! $needsContextualBuild) {
            $this->instances[$abstract] = $object;
        }

        if ($raiseEvents) {
            $this->fireResolvingCallbacks($abstract, $object);
        }

        // Before returning, we will also set the resolved flag to "true" and pop off
        // the parameter overrides for this build. After those two things are done
        // we will be ready to return back the fully constructed class instance.
        if (! $needsContextualBuild) {
            $this->resolved[$abstract] = true;
        }

        array_pop($this->with);

        return $object;
    }

    /**
     * Get the concrete type for a given abstract.
     *
     * @param  string|callable  $abstract
     * @return mixed
     */
    protected function getConcrete($abstract)
    {
        // If we don't have a registered resolver or concrete for the type, we'll just
        // assume each type is a concrete name and will attempt to resolve it as is
        // since the container should be able to resolve concretes automatically.
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        if ($this->environmentResolver === null ||
            ($this->checkedForAttributeBindings[$abstract] ?? false) || ! is_string($abstract)) {
            return $abstract;
        }

        return $this->getConcreteBindingFromAttributes($abstract);
    }

    /**
     * Get the concrete binding for an abstract from the Bind attribute.
     *
     * @param  string  $abstract
     * @return mixed
     */
    protected function getConcreteBindingFromAttributes($abstract)
    {
        $this->checkedForAttributeBindings[$abstract] = true;

        try {
            $reflected = new ReflectionClass($abstract);
        } catch (ReflectionException) {
            return $abstract;
        }

        $bindAttributes = $reflected->getAttributes(Bind::class);

        if ($bindAttributes === []) {
            return $abstract;
        }

        $concrete = $maybeConcrete = null;

        foreach ($bindAttributes as $reflectedAttribute) {
            $instance = $reflectedAttribute->newInstance();

            if ($instance->environments === ['*']) {
                $maybeConcrete = $instance->concrete;

                continue;
            }

            if ($this->currentEnvironmentIs($instance->environments)) {
                $concrete = $instance->concrete;

                break;
            }
        }

        if ($maybeConcrete !== null && $concrete === null) {
            $concrete = $maybeConcrete;
        }

        if ($concrete === null) {
            return $abstract;
        }

        match ($this->getScopedTyped($reflected)) {
            'scoped' => $this->scoped($abstract, $concrete),
            'singleton' => $this->singleton($abstract, $concrete),
            null => $this->bind($abstract, $concrete),
        };

        return $this->bindings[$abstract]['concrete'];
    }

    /**
     * Get the contextual concrete binding for the given abstract.
     *
     * @param  string|callable  $abstract
     * @return \Closure|string|array|null
     */
    protected function getContextualConcrete($abstract)
    {
        if (! is_null($binding = $this->findInContextualBindings($abstract))) {
            return $binding;
        }

        // Next we need to see if a contextual binding might be bound under an alias of the
        // given abstract type. So, we will need to check if any aliases exist with this
        // type and then spin through them and check for contextual bindings on these.
        if (empty($this->abstractAliases[$abstract])) {
            return;
        }

        foreach ($this->abstractAliases[$abstract] as $alias) {
            if (! is_null($binding = $this->findInContextualBindings($alias))) {
                return $binding;
            }
        }
    }

    /**
     * Find the concrete binding for the given abstract in the contextual binding array.
     *
     * @param  string|callable  $abstract
     * @return \Closure|string|null
     */
    protected function findInContextualBindings($abstract)
    {
        return $this->contextual[end($this->buildStack)][$abstract] ?? null;
    }

    /**
     * Determine if the given concrete is buildable.
     *
     * @param  mixed  $concrete
     * @param  string  $abstract
     * @return bool
     */
    protected function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    /**
     * Instantiate a concrete instance of the given type.
     *
     * @template TClass of object
     *
     * @param  \Closure(static, array): TClass|class-string<TClass>  $concrete
     * @return TClass
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Contracts\Container\CircularDependencyException
     */
    public function build($concrete)
    {
        // If the concrete type is actually a Closure, we will just execute it and
        // hand back the results of the functions, which allows functions to be
        // used as resolvers for more fine-tuned resolution of these objects.
        if ($concrete instanceof Closure) {
            $this->buildStack[] = spl_object_hash($concrete);

            try {
                return $concrete($this, $this->getLastParameterOverride());
            } finally {
                array_pop($this->buildStack);
            }
        }

        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new BindingResolutionException("Target class [$concrete] does not exist.", 0, $e);
        }

        // If the type is not instantiable, the developer is attempting to resolve
        // an abstract type such as an Interface or Abstract Class and there is
        // no binding registered for the abstractions so we need to bail out.
        if (! $reflector->isInstantiable()) {
            return $this->notInstantiable($concrete);
        }

        if (is_a($concrete, SelfBuilding::class, true) &&
            ! in_array($concrete, $this->buildStack, true)) {
            return $this->buildSelfBuildingInstance($concrete, $reflector);
        }

        $this->buildStack[] = $concrete;

        $constructor = $reflector->getConstructor();

        // If there are no constructors, that means there are no dependencies then
        // we can just resolve the instances of the objects right away, without
        // resolving any other types or dependencies out of these containers.
        if (is_null($constructor)) {
            array_pop($this->buildStack);

            $this->fireAfterResolvingAttributeCallbacks(
                $reflector->getAttributes(), $instance = new $concrete
            );

            return $instance;
        }

        $dependencies = $constructor->getParameters();

        // Once we have all the constructor's parameters we can create each of the
        // dependency instances and then use the reflection instances to make a
        // new instance of this class, injecting the created dependencies in.
        try {
            $instances = $this->resolveDependencies($dependencies);
        } finally {
            array_pop($this->buildStack);
        }

        $this->fireAfterResolvingAttributeCallbacks(
            $reflector->getAttributes(), $instance = new $concrete(...$instances)
        );

        return $instance;
    }

    /**
     * Instantiate a concrete instance of the given self building type.
     *
     * @template TClass of object
     *
     * @param  object{'newInstance': \Closure(static, array): TClass|class-string<TClass>}  $concrete
     * @param  \ReflectionClass  $reflector
     * @return TClass
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function buildSelfBuildingInstance($concrete, $reflector)
    {
        if (! method_exists($concrete, 'newInstance')) {
            throw new BindingResolutionException("No newInstance method exists for [$concrete].");
        }

        $this->buildStack[] = $concrete;

        $instance = $this->call([$concrete, 'newInstance']);

        array_pop($this->buildStack);

        $this->fireAfterResolvingAttributeCallbacks(
            $reflector->getAttributes(), $instance
        );

        return $instance;
    }

    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     *
     * @param  \ReflectionParameter[]  $dependencies
     * @return array
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function resolveDependencies(array $dependencies)
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            // If the dependency has an override for this particular build we will use
            // that instead as the value. Otherwise, we will continue with this run
            // of resolutions and let reflection attempt to determine the result.
            if ($this->hasParameterOverride($dependency)) {
                $results[] = $this->getParameterOverride($dependency);

                continue;
            }

            $result = null;

            if (! is_null($attribute = Util::getContextualAttributeFromDependency($dependency))) {
                $result = $this->resolveFromAttribute($attribute);
            }

            // If the class is null, it means the dependency is a string or some other
            // primitive type which we can not resolve since it is not a class and
            // we will just bomb out with an error since we have no-where to go.
            $result ??= is_null($className = Util::getParameterClassName($dependency))
                ? $this->resolvePrimitive($dependency)
                : $this->resolveClass($dependency, $className);

            $this->fireAfterResolvingAttributeCallbacks($dependency->getAttributes(), $result);

            if ($dependency->isVariadic()) {
                $results = array_merge($results, $result);
            } else {
                $results[] = $result;
            }
        }

        return $results;
    }

    /**
     * Determine if the given dependency has a parameter override.
     *
     * @param  \ReflectionParameter  $dependency
     * @return bool
     */
    protected function hasParameterOverride($dependency)
    {
        return array_key_exists(
            $dependency->name, $this->getLastParameterOverride()
        );
    }

    /**
     * Get a parameter override for a dependency.
     *
     * @param  \ReflectionParameter  $dependency
     * @return mixed
     */
    protected function getParameterOverride($dependency)
    {
        return $this->getLastParameterOverride()[$dependency->name];
    }

    /**
     * Get the last parameter override.
     *
     * @return array
     */
    protected function getLastParameterOverride()
    {
        return count($this->with) ? array_last($this->with) : [];
    }

    /**
     * Resolve a non-class hinted primitive dependency.
     *
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function resolvePrimitive(ReflectionParameter $parameter)
    {
        if (! is_null($concrete = $this->getContextualConcrete('$'.$parameter->getName()))) {
            return Util::unwrapIfClosure($concrete, $this);
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->isVariadic()) {
            return [];
        }

        if ($parameter->hasType() && $parameter->allowsNull()) {
            return null;
        }

        $this->unresolvablePrimitive($parameter);
    }

    /**
     * Resolve a class based dependency from the container.
     *
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function resolveClass(ReflectionParameter $parameter, ?string $className = null)
    {
        $className ??= Util::getParameterClassName($parameter);

        // First we will check if a default value has been defined for the parameter.
        // If it has, and no explicit binding exists, we should return it to avoid
        // overriding any of the developer specified defaults for the parameters.
        if ($parameter->isDefaultValueAvailable() &&
            ! $this->bound($className) &&
            $this->findInContextualBindings($className) === null) {
            return $parameter->getDefaultValue();
        }

        try {
            return $parameter->isVariadic()
                ? $this->resolveVariadicClass($parameter)
                : $this->make($className);
        }

        // If we can not resolve the class instance, we will check to see if the value
        // is variadic. If it is, we will return an empty array as the value of the
        // dependency similarly to how we handle scalar values in this situation.
        catch (BindingResolutionException $e) {
            if ($parameter->isVariadic()) {
                array_pop($this->with);

                return [];
            }

            throw $e;
        }
    }

    /**
     * Resolve a class based variadic dependency from the container.
     *
     * @return mixed
     */
    protected function resolveVariadicClass(ReflectionParameter $parameter)
    {
        $className = Util::getParameterClassName($parameter);

        $abstract = $this->getAlias($className);

        if (! is_array($concrete = $this->getContextualConcrete($abstract))) {
            return $this->make($className);
        }

        return array_map(fn ($abstract) => $this->resolve($abstract), $concrete);
    }

    /**
     * Resolve a dependency based on an attribute.
     *
     * @return mixed
     */
    public function resolveFromAttribute(ReflectionAttribute $attribute)
    {
        $handler = $this->contextualAttributes[$attribute->getName()] ?? null;

        $instance = $attribute->newInstance();

        if (is_null($handler) && method_exists($instance, 'resolve')) {
            $handler = $instance->resolve(...);
        }

        if (is_null($handler)) {
            throw new BindingResolutionException("Contextual binding attribute [{$attribute->getName()}] has no registered handler.");
        }

        return $handler($instance, $this);
    }

    /**
     * Throw an exception that the concrete is not instantiable.
     *
     * @param  string  $concrete
     * @return void
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function notInstantiable($concrete)
    {
        if (! empty($this->buildStack)) {
            $previous = implode(', ', $this->buildStack);

            $message = "Target [$concrete] is not instantiable while building [$previous].";
        } else {
            $message = "Target [$concrete] is not instantiable.";
        }

        throw new BindingResolutionException($message);
    }

    /**
     * Throw an exception for an unresolvable primitive.
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function unresolvablePrimitive(ReflectionParameter $parameter)
    {
        $message = "Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}";

        throw new BindingResolutionException($message);
    }

    /**
     * Register a new before resolving callback for all types.
     *
     * @param  \Closure|string  $abstract
     * @return void
     */
    public function beforeResolving($abstract, ?Closure $callback = null)
    {
        if (is_string($abstract)) {
            $abstract = $this->getAlias($abstract);
        }

        if ($abstract instanceof Closure && is_null($callback)) {
            $this->globalBeforeResolvingCallbacks[] = $abstract;
        } else {
            $this->beforeResolvingCallbacks[$abstract][] = $callback;
        }
    }

    /**
     * Register a new resolving callback.
     *
     * @param  \Closure|string  $abstract
     * @return void
     */
    public function resolving($abstract, ?Closure $callback = null)
    {
        if (is_string($abstract)) {
            $abstract = $this->getAlias($abstract);
        }

        if (is_null($callback) && $abstract instanceof Closure) {
            $this->globalResolvingCallbacks[] = $abstract;
        } else {
            $this->resolvingCallbacks[$abstract][] = $callback;
        }
    }

    /**
     * Register a new after resolving callback for all types.
     *
     * @param  \Closure|string  $abstract
     * @return void
     */
    public function afterResolving($abstract, ?Closure $callback = null)
    {
        if (is_string($abstract)) {
            $abstract = $this->getAlias($abstract);
        }

        if ($abstract instanceof Closure && is_null($callback)) {
            $this->globalAfterResolvingCallbacks[] = $abstract;
        } else {
            $this->afterResolvingCallbacks[$abstract][] = $callback;
        }
    }

    /**
     * Register a new after resolving attribute callback for all types.
     *
     * @return void
     */
    public function afterResolvingAttribute(string $attribute, \Closure $callback)
    {
        $this->afterResolvingAttributeCallbacks[$attribute][] = $callback;
    }

    /**
     * Fire all of the before resolving callbacks.
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @return void
     */
    protected function fireBeforeResolvingCallbacks($abstract, $parameters = [])
    {
        $this->fireBeforeCallbackArray($abstract, $parameters, $this->globalBeforeResolvingCallbacks);

        foreach ($this->beforeResolvingCallbacks as $type => $callbacks) {
            if ($type === $abstract || is_subclass_of($abstract, $type)) {
                $this->fireBeforeCallbackArray($abstract, $parameters, $callbacks);
            }
        }
    }

    /**
     * Fire an array of callbacks with an object.
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @return void
     */
    protected function fireBeforeCallbackArray($abstract, $parameters, array $callbacks)
    {
        foreach ($callbacks as $callback) {
            $callback($abstract, $parameters, $this);
        }
    }

    /**
     * Fire all of the resolving callbacks.
     *
     * @param  string  $abstract
     * @param  mixed  $object
     * @return void
     */
    protected function fireResolvingCallbacks($abstract, $object)
    {
        $this->fireCallbackArray($object, $this->globalResolvingCallbacks);

        $this->fireCallbackArray(
            $object, $this->getCallbacksForType($abstract, $object, $this->resolvingCallbacks)
        );

        $this->fireAfterResolvingCallbacks($abstract, $object);
    }

    /**
     * Fire all of the after resolving callbacks.
     *
     * @param  string  $abstract
     * @param  mixed  $object
     * @return void
     */
    protected function fireAfterResolvingCallbacks($abstract, $object)
    {
        $this->fireCallbackArray($object, $this->globalAfterResolvingCallbacks);

        $this->fireCallbackArray(
            $object, $this->getCallbacksForType($abstract, $object, $this->afterResolvingCallbacks)
        );
    }

    /**
     * Fire all of the after resolving attribute callbacks.
     *
     * @param  \ReflectionAttribute[]  $attributes
     * @param  mixed  $object
     * @return void
     */
    public function fireAfterResolvingAttributeCallbacks(array $attributes, $object)
    {
        foreach ($attributes as $attribute) {
            if (is_a($attribute->getName(), ContextualAttribute::class, true)) {
                $instance = $attribute->newInstance();

                if (method_exists($instance, 'after')) {
                    $instance->after($instance, $object, $this);
                }
            }

            $callbacks = $this->getCallbacksForType(
                $attribute->getName(), $object, $this->afterResolvingAttributeCallbacks
            );

            foreach ($callbacks as $callback) {
                $callback($attribute->newInstance(), $object, $this);
            }
        }
    }

    /**
     * Get all callbacks for a given type.
     *
     * @param  string  $abstract
     * @param  object  $object
     * @return array
     */
    protected function getCallbacksForType($abstract, $object, array $callbacksPerType)
    {
        $results = [];

        foreach ($callbacksPerType as $type => $callbacks) {
            if ($type === $abstract || $object instanceof $type) {
                $results = array_merge($results, $callbacks);
            }
        }

        return $results;
    }

    /**
     * Fire an array of callbacks with an object.
     *
     * @param  mixed  $object
     * @return void
     */
    protected function fireCallbackArray($object, array $callbacks)
    {
        foreach ($callbacks as $callback) {
            $callback($object, $this);
        }
    }

    /**
     * Get the name of the binding the container is currently resolving.
     *
     * @return class-string|string|null
     */
    public function currentlyResolving()
    {
        return array_last($this->buildStack) ?: null;
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
     * Get the alias for an abstract if available.
     *
     * @param  string  $abstract
     * @return string
     */
    public function getAlias($abstract)
    {
        return isset($this->aliases[$abstract])
            ? $this->getAlias($this->aliases[$abstract])
            : $abstract;
    }

    /**
     * Get the extender callbacks for a given type.
     *
     * @param  string  $abstract
     * @return array
     */
    protected function getExtenders($abstract)
    {
        return $this->extenders[$this->getAlias($abstract)] ?? [];
    }

    /**
     * Remove all of the extender callbacks for a given type.
     *
     * @param  string  $abstract
     * @return void
     */
    public function forgetExtenders($abstract)
    {
        unset($this->extenders[$this->getAlias($abstract)]);
    }

    /**
     * Drop all of the stale instances and aliases.
     *
     * @param  string  $abstract
     * @return void
     */
    protected function dropStaleInstances($abstract)
    {
        unset($this->instances[$abstract], $this->aliases[$abstract]);
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
        $this->instances = [];
    }

    /**
     * Clear all of the scoped instances from the container.
     *
     * @return void
     */
    public function forgetScopedInstances()
    {
        foreach ($this->scopedInstances as $scoped) {
            if ($scoped instanceof Closure) {
                foreach ($this->closureReturnTypes($scoped) as $type) {
                    unset($this->instances[$type]);
                }
            } else {
                unset($this->instances[$scoped]);
            }
        }
    }

    /**
     * Set the callback which determines the current container environment.
     *
     * @param  (callable(array<int, string>|string): bool|string)|null  $callback
     * @return void
     */
    public function resolveEnvironmentUsing(?callable $callback)
    {
        $this->environmentResolver = $callback;
    }

    /**
     * Determine the environment for the container.
     *
     * @param  array<int, string>|string  $environments
     * @return bool
     */
    public function currentEnvironmentIs($environments)
    {
        return $this->environmentResolver === null
            ? false
            : call_user_func($this->environmentResolver, $environments);
    }

    /**
     * Flush the container of all bindings and resolved instances.
     *
     * @return void
     */
    public function flush()
    {
        $this->aliases = [];
        $this->resolved = [];
        $this->bindings = [];
        $this->instances = [];
        $this->abstractAliases = [];
        $this->scopedInstances = [];
        $this->checkedForAttributeBindings = [];
        $this->checkedForSingletonOrScopedAttributes = [];
    }

    /**
     * Get the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        return static::$instance ??= new static;
    }

    /**
     * Set the shared instance of the container.
     *
     * @return \Illuminate\Contracts\Container\Container|static
     */
    public static function setInstance(?ContainerContract $container = null)
    {
        return static::$instance = $container;
    }

    /**
     * Determine if a given offset exists.
     *
     * @param  string  $key
     */
    public function offsetExists($key): bool
    {
        return $this->bound($key);
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string  $key
     */
    public function offsetGet($key): mixed
    {
        return $this->make($key);
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string  $key
     * @param  mixed  $value
     */
    public function offsetSet($key, $value): void
    {
        $this->bind($key, $value instanceof Closure ? $value : fn () => $value);
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string  $key
     */
    public function offsetUnset($key): void
    {
        unset($this->bindings[$key], $this->instances[$key], $this->resolved[$key]);
    }

    /**
     * Dynamically access container services.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this[$key];
    }

    /**
     * Dynamically set container services.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }
}
