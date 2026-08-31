<?php

namespace Illuminate\Foundation\Http;

use Carbon\CarbonInterval;
use DateTimeInterface;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as KernelContract;
use Illuminate\Foundation\Events\Terminating;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Routing\Pipeline;
use Illuminate\Routing\Router;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\InteractsWithTime;
use InvalidArgumentException;
use Throwable;

class Kernel implements KernelContract
{
    use InteractsWithTime;

    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * The bootstrap classes for the application.
     *
     * @var string[]
     */
    protected $bootstrappers = [
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ];

    /**
     * The application's middleware stack.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [];

    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     *
     * @deprecated
     */
    protected $routeMiddleware = [];

    /**
     * The application's middleware aliases.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [];

    /**
     * All of the registered request duration handlers.
     *
     * @var array
     */
    protected $requestLifecycleDurationHandlers = [];

    /**
     * When the kernel starting handling the current request.
     *
     * @var \Illuminate\Support\Carbon|null
     */
    protected $requestStartedAt;

    /**
     * The priority-sorted list of middleware.
     *
     * Forces non-global middleware to always be in the given order.
     *
     * @var string[]
     */
    protected $middlewarePriority = [
        \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
        \Illuminate\Contracts\Session\Middleware\AuthenticatesSessions::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];

    /**
     * Create a new HTTP kernel instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Routing\Router  $router
     */
    public function __construct(Application $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;

        $this->syncMiddlewareToRouter();
    }

    /**
     * Handle an incoming HTTP request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handle($request)
    {
        $this->requestStartedAt = Carbon::now();

        try {
            $request->enableHttpMethodParameterOverride();

            $response = $this->sendRequestThroughRouter($request);
        } catch (Throwable $e) {
            $this->reportException($e);

            $response = $this->renderException($request, $e);
        }

        $this->app['events']->dispatch(
            new RequestHandled($request, $response)
        );

        return $response;
    }

    /**
     * Send the given request through the middleware / router.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendRequestThroughRouter($request)
    {
        $this->app->instance('request', $request);

        Request::clearResolvedInstance();

        $this->bootstrap();

        return (new Pipeline($this->app))
            ->send($request)
            ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
            ->then($this->dispatchToRouter());
    }

    /**
     * Bootstrap the application for HTTP requests.
     *
     * @return void
     */
    public function bootstrap()
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

    /**
     * Get the route dispatcher callback.
     *
     * @return \Closure
     */
    protected function dispatchToRouter()
    {
        return function ($request) {
            $this->app->instance('request', $request);

            return $this->router->dispatch($request);
        };
    }

    /**
     * Call the terminate method on any terminable middleware.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        $this->app['events']->dispatch(new Terminating);

        $this->terminateMiddleware($request, $response);

        $this->app->terminate();

        if ($this->requestStartedAt === null) {
            return;
        }

        $this->requestStartedAt->setTimezone($this->app['config']->get('app.timezone') ?? 'UTC');

        foreach ($this->requestLifecycleDurationHandlers as ['threshold' => $threshold, 'handler' => $handler]) {
            $end ??= Carbon::now();

            if ($this->requestStartedAt->diffInMilliseconds($end) > $threshold) {
                $handler($this->requestStartedAt, $request, $response);
            }
        }

        $this->requestStartedAt = null;
    }

    /**
     * Call the terminate method on any terminable middleware.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    protected function terminateMiddleware($request, $response)
    {
        $middlewares = $this->app->shouldSkipMiddleware() ? [] : array_merge(
            $this->gatherRouteMiddleware($request),
            $this->middleware
        );

        foreach ($middlewares as $middleware) {
            if (! is_string($middleware)) {
                continue;
            }

            [$name] = $this->parseMiddleware($middleware);

            $instance = $this->app->make($name);

            if (method_exists($instance, 'terminate')) {
                $instance->terminate($request, $response);
            }
        }
    }

    /**
     * Register a callback to be invoked when the requests lifecycle duration exceeds a given amount of time.
     *
     * @param  \DateTimeInterface|\Carbon\CarbonInterval|float|int  $threshold
     * @param  callable  $handler
     * @return void
     */
    public function whenRequestLifecycleIsLongerThan($threshold, $handler)
    {
        $threshold = $threshold instanceof DateTimeInterface
            ? $this->secondsUntil($threshold) * 1000
            : $threshold;

        $threshold = $threshold instanceof CarbonInterval
            ? $threshold->totalMilliseconds
            : $threshold;

        $this->requestLifecycleDurationHandlers[] = [
            'threshold' => $threshold,
            'handler' => $handler,
        ];
    }

    /**
     * When the request being handled started.
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function requestStartedAt()
    {
        return $this->requestStartedAt;
    }

    /**
     * Gather the route middleware for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function gatherRouteMiddleware($request)
    {
        if ($route = $request->route()) {
            return $this->router->gatherRouteMiddleware($route);
        }

        return [];
    }

    /**
     * Parse a middleware string to get the name and parameters.
     *
     * @param  string  $middleware
     * @return array
     */
    protected function parseMiddleware($middleware)
    {
        [$name, $parameters] = array_pad(explode(':', $middleware, 2), 2, []);

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
    }

    /**
     * Determine if the kernel has a given middleware.
     *
     * @param  string  $middleware
     * @return bool
     */
    public function hasMiddleware($middleware)
    {
        return in_array($middleware, $this->middleware);
    }

    /**
     * Add a new middleware to the beginning of the stack if it does not already exist.
     *
     * @param  string  $middleware
     * @return $this
     */
    public function prependMiddleware($middleware)
    {
        if (array_search($middleware, $this->middleware) === false) {
            array_unshift($this->middleware, $middleware);
        }

        return $this;
    }

    /**
     * Add a new middleware to end of the stack if it does not already exist.
     *
     * @param  string  $middleware
     * @return $this
     */
    public function pushMiddleware($middleware)
    {
        if (array_search($middleware, $this->middleware) === false) {
            $this->middleware[] = $middleware;
        }

        return $this;
    }

    /**
     * Prepend the given middleware to the given middleware group.
     *
     * @param  string  $group
     * @param  string  $middleware
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function prependMiddlewareToGroup($group, $middleware)
    {
        if (! isset($this->middlewareGroups[$group])) {
            throw new InvalidArgumentException("The [{$group}] middleware group has not been defined.");
        }

        if (array_search($middleware, $this->middlewareGroups[$group]) === false) {
            array_unshift($this->middlewareGroups[$group], $middleware);
        }

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * Append the given middleware to the given middleware group.
     *
     * @param  string  $group
     * @param  string  $middleware
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function appendMiddlewareToGroup($group, $middleware)
    {
        if (! isset($this->middlewareGroups[$group])) {
            throw new InvalidArgumentException("The [{$group}] middleware group has not been defined.");
        }

        if (array_search($middleware, $this->middlewareGroups[$group]) === false) {
            $this->middlewareGroups[$group][] = $middleware;
        }

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * Prepend the given middleware to the middleware priority list.
     *
     * @param  string  $middleware
     * @return $this
     */
    public function prependToMiddlewarePriority($middleware)
    {
        if (! in_array($middleware, $this->middlewarePriority)) {
            array_unshift($this->middlewarePriority, $middleware);
        }

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * Append the given middleware to the middleware priority list.
     *
     * @param  string  $middleware
     * @return $this
     */
    public function appendToMiddlewarePriority($middleware)
    {
        if (! in_array($middleware, $this->middlewarePriority)) {
            $this->middlewarePriority[] = $middleware;
        }

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * Add the given middleware to the middleware priority list before other middleware.
     *
     * @param  array|string  $before
     * @param  string  $middleware
     * @return $this
     */
    public function addToMiddlewarePriorityBefore($before, $middleware)
    {
        return $this->addToMiddlewarePriorityRelative($before, $middleware, after: false);
    }

    /**
     * Add the given middleware to the middleware priority list after other middleware.
     *
     * @param  array|string  $after
     * @param  string  $middleware
     * @return $this
     */
    public function addToMiddlewarePriorityAfter($after, $middleware)
    {
        return $this->addToMiddlewarePriorityRelative($after, $middleware);
    }

    /**
     * Add the given middleware to the middleware priority list relative to other middleware.
     *
     * @param  string|array  $existing
     * @param  string  $middleware
     * @param  bool  $after
     * @return $this
     */
    protected function addToMiddlewarePriorityRelative($existing, $middleware, $after = true)
    {
        if (! in_array($middleware, $this->middlewarePriority)) {
            $index = $after ? 0 : count($this->middlewarePriority);

            foreach ((array) $existing as $existingMiddleware) {
                if (in_array($existingMiddleware, $this->middlewarePriority)) {
                    $middlewareIndex = array_search($existingMiddleware, $this->middlewarePriority);

                    if ($after && $middlewareIndex > $index) {
                        $index = $middlewareIndex + 1;
                    } elseif ($after === false && $middlewareIndex < $index) {
                        $index = $middlewareIndex;
                    }
                }
            }

            if ($index === 0 && $after === false) {
                array_unshift($this->middlewarePriority, $middleware);
            } elseif (($after && $index === 0) || $index === count($this->middlewarePriority)) {
                $this->middlewarePriority[] = $middleware;
            } else {
                array_splice($this->middlewarePriority, $index, 0, $middleware);
            }
        }

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * Sync the current state of the middleware to the router.
     *
     * @return void
     */
    protected function syncMiddlewareToRouter()
    {
        $this->router->middlewarePriority = $this->middlewarePriority;

        foreach ($this->middlewareGroups as $key => $middleware) {
            $this->router->middlewareGroup($key, $middleware);
        }

        foreach (array_merge($this->routeMiddleware, $this->middlewareAliases) as $key => $middleware) {
            $this->router->aliasMiddleware($key, $middleware);
        }
    }

    /**
     * Get the priority-sorted list of middleware.
     *
     * @return array
     */
    public function getMiddlewarePriority()
    {
        return $this->middlewarePriority;
    }

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Throwable  $e
     * @return void
     */
    protected function reportException(Throwable $e)
    {
        $this->app[ExceptionHandler::class]->report($e);
    }

    /**
     * Render the exception to a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderException($request, Throwable $e)
    {
        return $this->app[ExceptionHandler::class]->render($request, $e);
    }

    /**
     * Get the application's global middleware.
     *
     * @return array
     */
    public function getGlobalMiddleware()
    {
        return $this->middleware;
    }

    /**
     * Set the application's global middleware.
     *
     * @param  array  $middleware
     * @return $this
     */
    public function setGlobalMiddleware(array $middleware)
    {
        $this->middleware = $middleware;

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * Get the application's route middleware groups.
     *
     * @return array
     */
    public function getMiddlewareGroups()
    {
        return $this->middlewareGroups;
    }

    /**
     * Set the application's middleware groups.
     *
     * @param  array  $groups
     * @return $this
     */
    public function setMiddlewareGroups(array $groups)
    {
        $this->middlewareGroups = $groups;

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * Get the application's route middleware aliases.
     *
     * @return array
     *
     * @deprecated
     */
    public function getRouteMiddleware()
    {
        return $this->getMiddlewareAliases();
    }

    /**
     * Get the application's route middleware aliases.
     *
     * @return array
     */
    public function getMiddlewareAliases()
    {
        return array_merge($this->routeMiddleware, $this->middlewareAliases);
    }

    /**
     * Set the application's route middleware aliases.
     *
     * @param  array  $aliases
     * @return $this
     */
    public function setMiddlewareAliases(array $aliases)
    {
        $this->middlewareAliases = $aliases;

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * Set the application's middleware priority.
     *
     * @param  array  $priority
     * @return $this
     */
    public function setMiddlewarePriority(array $priority)
    {
        $this->middlewarePriority = $priority;

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * Get the Laravel application instance.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Set the Laravel application instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return $this
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;

        return $this;
    }
}
