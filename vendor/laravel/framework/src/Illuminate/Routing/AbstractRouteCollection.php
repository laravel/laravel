<?php

namespace Illuminate\Routing;

use ArrayIterator;
use Countable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use IteratorAggregate;
use LogicException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\RouteCollection as SymfonyRouteCollection;
use Traversable;

abstract class AbstractRouteCollection implements Countable, IteratorAggregate, RouteCollectionInterface
{
    /**
     * Handle the matched route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Route|null  $route
     * @return \Illuminate\Routing\Route
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function handleMatchedRoute(Request $request, $route)
    {
        if (! is_null($route)) {
            return $route->bind($request);
        }

        // If no route was found we will now check if a matching route is specified by
        // another HTTP verb. If it is we will need to throw a MethodNotAllowed and
        // inform the user agent of which HTTP verb it should use for this route.
        $others = $this->checkForAlternateVerbs($request);

        if (count($others) > 0) {
            return $this->getRouteForMethods($request, $others);
        }

        throw new NotFoundHttpException(sprintf(
            'The route %s could not be found.',
            $request->path()
        ));
    }

    /**
     * Determine if any routes match on another HTTP verb.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function checkForAlternateVerbs($request)
    {
        $methods = array_diff(Router::$verbs, [$request->getMethod()]);

        // Here we will spin through all verbs except for the current request verb and
        // check to see if any routes respond to them. If they do, we will return a
        // proper error response with the correct headers on the response string.
        return array_values(array_filter(
            $methods,
            function ($method) use ($request) {
                return ! is_null($this->matchAgainstRoutes($this->get($method), $request, false));
            }
        ));
    }

    /**
     * Determine if a route in the array matches the request.
     *
     * @param  \Illuminate\Routing\Route[]  $routes
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $includingMethod
     * @return \Illuminate\Routing\Route|null
     */
    protected function matchAgainstRoutes(array $routes, $request, $includingMethod = true)
    {
        $fallbackRoute = null;

        foreach ($routes as $route) {
            if ($route->matches($request, $includingMethod)) {
                if ($route->isFallback) {
                    $fallbackRoute ??= $route;

                    continue;
                }

                return $route;
            }
        }

        return $fallbackRoute;
    }

    /**
     * Get a route (if necessary) that responds when other available methods are present.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string[]  $methods
     * @return \Illuminate\Routing\Route
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function getRouteForMethods($request, array $methods)
    {
        if ($request->isMethod('OPTIONS')) {
            return (new Route('OPTIONS', $request->path(), function () use ($methods) {
                return new Response('', 200, ['Allow' => implode(',', $methods)]);
            }))->bind($request);
        }

        $this->requestMethodNotAllowed($request, $methods, $request->method());
    }

    /**
     * Throw a method not allowed HTTP exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $others
     * @param  string  $method
     * @return never
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function requestMethodNotAllowed($request, array $others, $method)
    {
        throw new MethodNotAllowedHttpException(
            $others,
            sprintf(
                'The %s method is not supported for route %s. Supported methods: %s.',
                $method,
                $request->path(),
                implode(', ', $others)
            )
        );
    }

    /**
     * Throw a method not allowed HTTP exception.
     *
     * @param  array  $others
     * @param  string  $method
     * @return void
     *
     * @deprecated use requestMethodNotAllowed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function methodNotAllowed(array $others, $method)
    {
        throw new MethodNotAllowedHttpException(
            $others,
            sprintf(
                'The %s method is not supported for this route. Supported methods: %s.',
                $method,
                implode(', ', $others)
            )
        );
    }

    /**
     * Compile the routes for caching.
     *
     * @return array
     */
    public function compile()
    {
        $compiled = $this->dumper()->getCompiledRoutes();

        $attributes = [];

        foreach ($this->getRoutes() as $route) {
            $attributes[$route->getName()] = [
                'methods' => $route->methods(),
                'uri' => $route->uri(),
                'action' => $route->getAction(),
                'fallback' => $route->isFallback,
                'defaults' => $route->defaults,
                'wheres' => $route->wheres,
                'bindingFields' => $route->bindingFields(),
                'lockSeconds' => $route->locksFor(),
                'waitSeconds' => $route->waitsFor(),
                'withTrashed' => $route->allowsTrashedBindings(),
            ];
        }

        return compact('compiled', 'attributes');
    }

    /**
     * Return the CompiledUrlMatcherDumper instance for the route collection.
     *
     * @return \Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper
     */
    public function dumper()
    {
        return new CompiledUrlMatcherDumper($this->toSymfonyRouteCollection());
    }

    /**
     * Convert the collection to a Symfony RouteCollection instance.
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function toSymfonyRouteCollection()
    {
        $symfonyRoutes = new SymfonyRouteCollection;

        $fallbackRoutes = [];

        foreach ($this->getRoutes() as $route) {
            if ($route->isFallback) {
                $fallbackRoutes[] = $route;

                continue;
            }

            $symfonyRoutes = $this->addToSymfonyRoutesCollection($symfonyRoutes, $route);
        }

        foreach ($fallbackRoutes as $route) {
            $symfonyRoutes = $this->addToSymfonyRoutesCollection($symfonyRoutes, $route);
        }

        return $symfonyRoutes;
    }

    /**
     * Add a route to the SymfonyRouteCollection instance.
     *
     * @param  \Symfony\Component\Routing\RouteCollection  $symfonyRoutes
     * @param  \Illuminate\Routing\Route  $route
     * @return \Symfony\Component\Routing\RouteCollection
     *
     * @throws \LogicException
     */
    protected function addToSymfonyRoutesCollection(SymfonyRouteCollection $symfonyRoutes, Route $route)
    {
        $name = $route->getName();

        if (
            ! is_null($name)
            && str_ends_with($name, '.')
            && ! is_null($symfonyRoutes->get($name))
        ) {
            $name = null;
        }

        if (! $name) {
            $route->name($this->generateRouteName());

            $this->add($route);
        } elseif (! is_null($symfonyRoutes->get($name))) {
            throw new LogicException("Unable to prepare route [{$route->uri}] for serialization. Another route has already been assigned name [{$name}].");
        }

        $symfonyRoutes->add($route->getName(), $route->toSymfonyRoute());

        return $symfonyRoutes;
    }

    /**
     * Get a randomly generated route name.
     *
     * @return string
     */
    protected function generateRouteName()
    {
        return 'generated::'.Str::random();
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->getRoutes());
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->getRoutes());
    }
}
