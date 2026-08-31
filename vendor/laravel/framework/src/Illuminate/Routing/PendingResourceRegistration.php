<?php

namespace Illuminate\Routing;

use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;

class PendingResourceRegistration
{
    use CreatesRegularExpressionRouteConstraints, Macroable;

    /**
     * The resource registrar.
     *
     * @var \Illuminate\Routing\ResourceRegistrar
     */
    protected $registrar;

    /**
     * The resource name.
     *
     * @var string
     */
    protected $name;

    /**
     * The resource controller.
     *
     * @var string
     */
    protected $controller;

    /**
     * The resource options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The resource's registration status.
     *
     * @var bool
     */
    protected $registered = false;

    /**
     * Create a new pending resource registration instance.
     *
     * @param  \Illuminate\Routing\ResourceRegistrar  $registrar
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     */
    public function __construct(ResourceRegistrar $registrar, $name, $controller, array $options)
    {
        $this->name = $name;
        $this->options = $options;
        $this->registrar = $registrar;
        $this->controller = $controller;
    }

    /**
     * Set the methods the controller should apply to.
     *
     * @param  mixed  $methods
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function only($methods)
    {
        $this->options['only'] = is_array($methods) ? $methods : func_get_args();

        return $this;
    }

    /**
     * Set the methods the controller should exclude.
     *
     * @param  mixed  $methods
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function except($methods)
    {
        $this->options['except'] = is_array($methods) ? $methods : func_get_args();

        return $this;
    }

    /**
     * Set the route names for controller actions.
     *
     * @param  array|string  $names
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function names($names)
    {
        $this->options['names'] = $names;

        return $this;
    }

    /**
     * Set the route name for a controller action.
     *
     * @param  string  $method
     * @param  string  $name
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function name($method, $name)
    {
        $this->options['names'][$method] = $name;

        return $this;
    }

    /**
     * Override the route parameter names.
     *
     * @param  array|string  $parameters
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function parameters($parameters)
    {
        $this->options['parameters'] = $parameters;

        return $this;
    }

    /**
     * Override a route parameter's name.
     *
     * @param  string  $previous
     * @param  string  $new
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function parameter($previous, $new)
    {
        $this->options['parameters'][$previous] = $new;

        return $this;
    }

    /**
     * Add middleware to the resource routes.
     *
     * @param  mixed  $middleware
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function middleware($middleware)
    {
        $middleware = Arr::wrap($middleware);

        foreach ($middleware as $key => $value) {
            $middleware[$key] = (string) $value;
        }

        $this->options['middleware'] = $middleware;

        if (isset($this->options['middleware_for'])) {
            foreach ($this->options['middleware_for'] as $method => $value) {
                $this->options['middleware_for'][$method] = Router::uniqueMiddleware(array_merge(
                    Arr::wrap($value),
                    $middleware
                ));
            }
        }

        return $this;
    }

    /**
     * Specify middleware that should be added to the specified resource routes.
     *
     * @param  array|string  $methods
     * @param  array|string  $middleware
     * @return $this
     */
    public function middlewareFor($methods, $middleware)
    {
        $methods = Arr::wrap($methods);
        $middleware = Arr::wrap($middleware);

        if (isset($this->options['middleware'])) {
            $middleware = Router::uniqueMiddleware(array_merge(
                $this->options['middleware'],
                $middleware
            ));
        }

        foreach ($methods as $method) {
            $this->options['middleware_for'][$method] = $middleware;
        }

        return $this;
    }

    /**
     * Specify middleware that should be removed from the resource routes.
     *
     * @param  array|string  $middleware
     * @return $this
     */
    public function withoutMiddleware($middleware)
    {
        $this->options['excluded_middleware'] = array_merge(
            (array) ($this->options['excluded_middleware'] ?? []), Arr::wrap($middleware)
        );

        return $this;
    }

    /**
     * Specify middleware that should be removed from the specified resource routes.
     *
     * @param  array|string  $methods
     * @param  array|string  $middleware
     * @return $this
     */
    public function withoutMiddlewareFor($methods, $middleware)
    {
        $methods = Arr::wrap($methods);
        $middleware = Arr::wrap($middleware);

        foreach ($methods as $method) {
            $this->options['excluded_middleware_for'][$method] = $middleware;
        }

        return $this;
    }

    /**
     * Add "where" constraints to the resource routes.
     *
     * @param  mixed  $wheres
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function where($wheres)
    {
        $this->options['wheres'] = $wheres;

        return $this;
    }

    /**
     * Indicate that the resource routes should have "shallow" nesting.
     *
     * @param  bool  $shallow
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function shallow($shallow = true)
    {
        $this->options['shallow'] = $shallow;

        return $this;
    }

    /**
     * Define the callable that should be invoked on a missing model exception.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function missing($callback)
    {
        $this->options['missing'] = $callback;

        return $this;
    }

    /**
     * Indicate that the resource routes should be scoped using the given binding fields.
     *
     * @param  array  $fields
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function scoped(array $fields = [])
    {
        $this->options['bindingFields'] = $fields;

        return $this;
    }

    /**
     * Define which routes should allow "trashed" models to be retrieved when resolving implicit model bindings.
     *
     * @param  array  $methods
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function withTrashed(array $methods = [])
    {
        $this->options['trashed'] = $methods;

        return $this;
    }

    /**
     * Register the resource route.
     *
     * @return \Illuminate\Routing\RouteCollection
     */
    public function register()
    {
        $this->registered = true;

        return $this->registrar->register(
            $this->name, $this->controller, $this->options
        );
    }

    /**
     * Handle the object's destruction.
     *
     * @return void
     */
    public function __destruct()
    {
        if (! $this->registered) {
            $this->register();
        }
    }
}
