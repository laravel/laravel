<?php

namespace Illuminate\Contracts\Container;

use Closure;
use Psr\Container\ContainerInterface;

interface Container extends ContainerInterface
{
    /**
     * {@inheritdoc}
     *
     * @template TClass of object
     *
     * @param  string|class-string<TClass>  $id
     * @return ($id is class-string<TClass> ? TClass : mixed)
     */
    public function get(string $id);

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound($abstract);

    /**
     * Alias a type to a different name.
     *
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     *
     * @throws \LogicException
     */
    public function alias($abstract, $alias);

    /**
     * Assign a set of tags to a given binding.
     *
     * @param  array|string  $abstracts
     * @param  mixed  ...$tags
     * @return void
     */
    public function tag($abstracts, $tags);

    /**
     * Resolve all of the bindings for a given tag.
     *
     * @param  string  $tag
     * @return iterable
     */
    public function tagged($tag);

    /**
     * Register a binding with the container.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false);

    /**
     * Bind a callback to resolve with Container::call.
     *
     * @param  array|string  $method
     * @param  \Closure  $callback
     * @return void
     */
    public function bindMethod($method, $callback);

    /**
     * Register a binding if it hasn't already been registered.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bindIf($abstract, $concrete = null, $shared = false);

    /**
     * Register a shared binding in the container.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null);

    /**
     * Register a shared binding if it hasn't already been registered.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singletonIf($abstract, $concrete = null);

    /**
     * Register a scoped binding in the container.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function scoped($abstract, $concrete = null);

    /**
     * Register a scoped binding if it hasn't already been registered.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function scopedIf($abstract, $concrete = null);

    /**
     * "Extend" an abstract type in the container.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure  $closure
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function extend($abstract, Closure $closure);

    /**
     * Register an existing instance as shared in the container.
     *
     * @template TInstance of mixed
     *
     * @param  \Closure|string  $abstract
     * @param  TInstance  $instance
     * @return TInstance
     */
    public function instance($abstract, $instance);

    /**
     * Add a contextual binding to the container.
     *
     * @param  string  $concrete
     * @param  \Closure|string  $abstract
     * @param  \Closure|string  $implementation
     * @return void
     */
    public function addContextualBinding($concrete, $abstract, $implementation);

    /**
     * Define a contextual binding.
     *
     * @param  string|array  $concrete
     * @return \Illuminate\Contracts\Container\ContextualBindingBuilder
     */
    public function when($concrete);

    /**
     * Get a closure to resolve the given type from the container.
     *
     * @template TClass of object
     *
     * @param  string|class-string<TClass>  $abstract
     * @return ($abstract is class-string<TClass> ? \Closure(): TClass : \Closure(): mixed)
     */
    public function factory($abstract);

    /**
     * Flush the container of all bindings and resolved instances.
     *
     * @return void
     */
    public function flush();

    /**
     * Resolve the given type from the container.
     *
     * @template TClass of object
     *
     * @param  string|class-string<TClass>  $abstract
     * @param  array  $parameters
     * @return ($abstract is class-string<TClass> ? TClass : mixed)
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function make($abstract, array $parameters = []);

    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @param  string|null  $defaultMethod
     * @return mixed
     */
    public function call($callback, array $parameters = [], $defaultMethod = null);

    /**
     * Determine if the given abstract type has been resolved.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function resolved($abstract);

    /**
     * Register a new before resolving callback.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    public function beforeResolving($abstract, ?Closure $callback = null);

    /**
     * Register a new resolving callback.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    public function resolving($abstract, ?Closure $callback = null);

    /**
     * Register a new after resolving callback.
     *
     * @param  \Closure|string  $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    public function afterResolving($abstract, ?Closure $callback = null);
}
