<?php

namespace Illuminate\Routing;

use Closure;
use LogicException;
use Throwable;

class MiddlewareNameResolver
{
    /**
     * Resolve the middleware name to a class name(s) preserving passed parameters.
     *
     * @param  \Closure|string  $name
     * @param  array  $map
     * @param  array  $middlewareGroups
     * @return \Closure|string|array
     */
    public static function resolve($name, $map, $middlewareGroups)
    {
        // When the middleware is simply a Closure, we will return this Closure instance
        // directly so that Closures can be registered as middleware inline, which is
        // convenient on occasions when the developers are experimenting with them.
        if ($name instanceof Closure) {
            return $name;
        }

        if (isset($map[$name]) && $map[$name] instanceof Closure) {
            return $map[$name];
        }

        // If the middleware is the name of a middleware group, we will return the array
        // of middlewares that belong to the group. This allows developers to group a
        // set of middleware under single keys that can be conveniently referenced.
        if (isset($middlewareGroups[$name])) {
            return static::parseMiddlewareGroup($name, $map, $middlewareGroups);
        }

        // Finally, when the middleware is simply a string mapped to a class name the
        // middleware name will get parsed into the full class name and parameters
        // which may be run using the Pipeline which accepts this string format.
        [$name, $parameters] = array_pad(explode(':', $name, 2), 2, null);

        return ($map[$name] ?? $name).(! is_null($parameters) ? ':'.$parameters : '');
    }

    /**
     * Parse the middleware group and format it for usage.
     *
     * @param  string  $name
     * @param  array  $map
     * @param  array  $middlewareGroups
     * @return array
     *
     * @throws Throwable
     */
    protected static function parseMiddlewareGroup($name, $map, $middlewareGroups)
    {
        $results = [];

        foreach ($middlewareGroups[$name] as $middleware) {
            // If the middleware is another middleware group we will pull in the group and
            // merge its middleware into the results. This allows groups to conveniently
            // reference other groups without needing to repeat all their middlewares.
            if (isset($middlewareGroups[$middleware])) {
                throw_if($name === $middleware, fn () => new LogicException("[$name] middleware group is referencing itself."));

                $results = array_merge($results, static::parseMiddlewareGroup(
                    $middleware, $map, $middlewareGroups
                ));

                continue;
            }

            [$middleware, $parameters] = array_pad(
                explode(':', $middleware, 2), 2, null
            );

            // If this middleware is actually a route middleware, we will extract the full
            // class name out of the middleware list now. Then we'll add the parameters
            // back onto this class' name so the pipeline will properly extract them.
            if (isset($map[$middleware])) {
                $middleware = $map[$middleware];
            }

            $results[] = $middleware.($parameters ? ':'.$parameters : '');
        }

        return $results;
    }
}
