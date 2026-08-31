<?php

namespace Illuminate\View\Concerns;

use Closure;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Str;

trait ManagesEvents
{
    /**
     * Register a view creator event.
     *
     * @param  array|string  $views
     * @param  \Closure|string  $callback
     * @return array
     */
    public function creator($views, $callback)
    {
        $creators = [];

        foreach ((array) $views as $view) {
            $creators[] = $this->addViewEvent($view, $callback, 'creating: ');
        }

        return $creators;
    }

    /**
     * Register multiple view composers via an array.
     *
     * @param  array  $composers
     * @return array
     */
    public function composers(array $composers)
    {
        $registered = [];

        foreach ($composers as $callback => $views) {
            $registered = array_merge($registered, $this->composer($views, $callback));
        }

        return $registered;
    }

    /**
     * Register a view composer event.
     *
     * @param  array|string  $views
     * @param  \Closure|string  $callback
     * @return array
     */
    public function composer($views, $callback)
    {
        $composers = [];

        foreach ((array) $views as $view) {
            $composers[] = $this->addViewEvent($view, $callback);
        }

        return $composers;
    }

    /**
     * Add an event for a given view.
     *
     * @param  string  $view
     * @param  \Closure|string  $callback
     * @param  string  $prefix
     * @return \Closure|null
     */
    protected function addViewEvent($view, $callback, $prefix = 'composing: ')
    {
        $view = $this->normalizeName($view);

        if ($callback instanceof Closure) {
            $this->addEventListener($prefix.$view, $callback);

            return $callback;
        } elseif (is_string($callback)) {
            return $this->addClassEvent($view, $callback, $prefix);
        }
    }

    /**
     * Register a class based view composer.
     *
     * @param  string  $view
     * @param  string  $class
     * @param  string  $prefix
     * @return \Closure
     */
    protected function addClassEvent($view, $class, $prefix)
    {
        $name = $prefix.$view;

        // When registering a class based view "composer", we will simply resolve the
        // classes from the application IoC container then call the compose method
        // on the instance. This allows for convenient, testable view composers.
        $callback = $this->buildClassEventCallback(
            $class, $prefix
        );

        $this->addEventListener($name, $callback);

        return $callback;
    }

    /**
     * Build a class based container callback Closure.
     *
     * @param  string  $class
     * @param  string  $prefix
     * @return \Closure
     */
    protected function buildClassEventCallback($class, $prefix)
    {
        [$class, $method] = $this->parseClassEvent($class, $prefix);

        // Once we have the class and method name, we can build the Closure to resolve
        // the instance out of the IoC container and call the method on it with the
        // given arguments that are passed to the Closure as the composer's data.
        return function () use ($class, $method) {
            return $this->container->make($class)->{$method}(...func_get_args());
        };
    }

    /**
     * Parse a class based composer name.
     *
     * @param  string  $class
     * @param  string  $prefix
     * @return array
     */
    protected function parseClassEvent($class, $prefix)
    {
        return Str::parseCallback($class, $this->classEventMethodForPrefix($prefix));
    }

    /**
     * Determine the class event method based on the given prefix.
     *
     * @param  string  $prefix
     * @return string
     */
    protected function classEventMethodForPrefix($prefix)
    {
        return str_contains($prefix, 'composing') ? 'compose' : 'create';
    }

    /**
     * Add a listener to the event dispatcher.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return void
     */
    protected function addEventListener($name, $callback)
    {
        if (str_contains($name, '*')) {
            $callback = function ($name, array $data) use ($callback) {
                return $callback($data[0]);
            };
        }

        $this->events->listen($name, $callback);
    }

    /**
     * Call the composer for a given view.
     *
     * @param  \Illuminate\Contracts\View\View  $view
     * @return void
     */
    public function callComposer(ViewContract $view)
    {
        if ($this->events->hasListeners($event = 'composing: '.$view->name())) {
            $this->events->dispatch($event, [$view]);
        }
    }

    /**
     * Call the creator for a given view.
     *
     * @param  \Illuminate\Contracts\View\View  $view
     * @return void
     */
    public function callCreator(ViewContract $view)
    {
        if ($this->events->hasListeners($event = 'creating: '.$view->name())) {
            $this->events->dispatch($event, [$view]);
        }
    }
}
