<?php

namespace Illuminate\Routing;

use BackedEnum;
use BadMethodCallException;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Reflector;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;

/**
 * @method \Illuminate\Routing\Route any(string $uri, \Closure|array|string|null $action = null)
 * @method \Illuminate\Routing\Route delete(string $uri, \Closure|array|string|null $action = null)
 * @method \Illuminate\Routing\Route get(string $uri, \Closure|array|string|null $action = null)
 * @method \Illuminate\Routing\Route options(string $uri, \Closure|array|string|null $action = null)
 * @method \Illuminate\Routing\Route patch(string $uri, \Closure|array|string|null $action = null)
 * @method \Illuminate\Routing\Route post(string $uri, \Closure|array|string|null $action = null)
 * @method \Illuminate\Routing\Route put(string $uri, \Closure|array|string|null $action = null)
 * @method \Illuminate\Routing\RouteRegistrar as(string $value)
 * @method \Illuminate\Routing\RouteRegistrar can(\UnitEnum|string  $ability, array|string $models = [])
 * @method \Illuminate\Routing\RouteRegistrar controller(string $controller)
 * @method \Illuminate\Routing\RouteRegistrar domain(\BackedEnum|string $value)
 * @method \Illuminate\Routing\RouteRegistrar middleware(array|string|null $middleware)
 * @method \Illuminate\Routing\RouteRegistrar missing(\Closure $missing)
 * @method \Illuminate\Routing\RouteRegistrar name(\BackedEnum|string $value)
 * @method \Illuminate\Routing\RouteRegistrar namespace(string|null $value)
 * @method \Illuminate\Routing\RouteRegistrar prefix(string $prefix)
 * @method \Illuminate\Routing\RouteRegistrar scopeBindings()
 * @method \Illuminate\Routing\RouteRegistrar where(array $where)
 * @method \Illuminate\Routing\RouteRegistrar withoutMiddleware(array|string $middleware)
 * @method \Illuminate\Routing\RouteRegistrar withoutScopedBindings()
 */
class RouteRegistrar
{
    use CreatesRegularExpressionRouteConstraints;
    use Macroable {
        __call as macroCall;
    }

    /**
     * The router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * The attributes to pass on to the router.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The methods to dynamically pass through to the router.
     *
     * @var string[]
     */
    protected $passthru = [
        'get', 'post', 'put', 'patch', 'delete', 'options', 'any',
    ];

    /**
     * The attributes that can be set through this class.
     *
     * @var string[]
     */
    protected $allowedAttributes = [
        'as',
        'can',
        'controller',
        'domain',
        'middleware',
        'missing',
        'name',
        'namespace',
        'prefix',
        'scopeBindings',
        'where',
        'withoutMiddleware',
        'withoutScopedBindings',
    ];

    /**
     * The attributes that are aliased.
     *
     * @var array
     */
    protected $aliases = [
        'name' => 'as',
        'scopeBindings' => 'scope_bindings',
        'withoutScopedBindings' => 'scope_bindings',
        'withoutMiddleware' => 'excluded_middleware',
    ];

    /**
     * Create a new route registrar instance.
     *
     * @param  \Illuminate\Routing\Router  $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Set the value for a given attribute.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function attribute($key, $value)
    {
        if (! in_array($key, $this->allowedAttributes)) {
            throw new InvalidArgumentException("Attribute [{$key}] does not exist.");
        }

        if ($key === 'middleware') {
            $value = array_filter(Arr::wrap($value));

            foreach ($value as $index => $middleware) {
                $value[$index] = (string) $middleware;
            }
        }

        $attributeKey = Arr::get($this->aliases, $key, $key);

        if ($key === 'withoutMiddleware') {
            $value = array_merge(
                (array) ($this->attributes[$attributeKey] ?? []), Arr::wrap($value)
            );
        }

        if ($key === 'withoutScopedBindings') {
            $value = false;
        }

        if ($value instanceof BackedEnum && ! is_string($value = $value->value)) {
            throw new InvalidArgumentException("Attribute [{$key}] expects a string backed enum.");
        }

        $this->attributes[$attributeKey] = $value;

        return $this;
    }

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function resource($name, $controller, array $options = [])
    {
        return $this->router->resource($name, $controller, $this->attributes + $options);
    }

    /**
     * Route an API resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function apiResource($name, $controller, array $options = [])
    {
        return $this->router->apiResource($name, $controller, $this->attributes + $options);
    }

    /**
     * Route a singleton resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return \Illuminate\Routing\PendingSingletonResourceRegistration
     */
    public function singleton($name, $controller, array $options = [])
    {
        return $this->router->singleton($name, $controller, $this->attributes + $options);
    }

    /**
     * Route an API singleton resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return \Illuminate\Routing\PendingSingletonResourceRegistration
     */
    public function apiSingleton($name, $controller, array $options = [])
    {
        return $this->router->apiSingleton($name, $controller, $this->attributes + $options);
    }

    /**
     * Create a route group with shared attributes.
     *
     * @param  \Closure|array|string  $callback
     * @return $this
     */
    public function group($callback)
    {
        $this->router->group($this->attributes, $callback);

        return $this;
    }

    /**
     * Register a new route with the given verbs.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return \Illuminate\Routing\Route
     */
    public function match($methods, $uri, $action = null)
    {
        return $this->router->match($methods, $uri, $this->compileAction($action));
    }

    /**
     * Register a new route with the router.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return \Illuminate\Routing\Route
     */
    protected function registerRoute($method, $uri, $action = null)
    {
        if (! is_array($action)) {
            $action = array_merge($this->attributes, $action ? ['uses' => $action] : []);
        }

        return $this->router->{$method}($uri, $this->compileAction($action));
    }

    /**
     * Compile the action into an array including the attributes.
     *
     * @param  \Closure|array|string|null  $action
     * @return array
     */
    protected function compileAction($action)
    {
        if (is_null($action)) {
            return $this->attributes;
        }

        if (is_string($action) || $action instanceof Closure) {
            $action = ['uses' => $action];
        }

        if (is_array($action) &&
            array_is_list($action) &&
            Reflector::isCallable($action)) {
            if (strncmp($action[0], '\\', 1)) {
                $action[0] = '\\'.$action[0];
            }
            $action = [
                'uses' => $action[0].'@'.$action[1],
                'controller' => $action[0].'@'.$action[1],
            ];
        }

        return array_merge($this->attributes, $action);
    }

    /**
     * Dynamically handle calls into the route registrar.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return \Illuminate\Routing\Route|$this
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if (in_array($method, $this->passthru)) {
            return $this->registerRoute($method, ...$parameters);
        }

        if (in_array($method, $this->allowedAttributes)) {
            if ($method === 'middleware') {
                return $this->attribute($method, is_array($parameters[0]) ? $parameters[0] : $parameters);
            }

            if ($method === 'can') {
                return $this->attribute($method, [$parameters]);
            }

            return $this->attribute($method, array_key_exists(0, $parameters) ? $parameters[0] : true);
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }
}
