<?php

namespace Illuminate\Broadcasting;

use Ably\AblyRest;
use Closure;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Broadcasting\Broadcasters\AblyBroadcaster;
use Illuminate\Broadcasting\Broadcasters\LogBroadcaster;
use Illuminate\Broadcasting\Broadcasters\NullBroadcaster;
use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Illuminate\Broadcasting\Broadcasters\RedisBroadcaster;
use Illuminate\Bus\UniqueLock;
use Illuminate\Contracts\Broadcasting\Factory as FactoryContract;
use Illuminate\Contracts\Broadcasting\ShouldBeUnique;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldRescue;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcherContract;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Foundation\CachesRoutes;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Pusher\Pusher;
use RuntimeException;
use Throwable;

/**
 * @mixin \Illuminate\Contracts\Broadcasting\Broadcaster
 */
class BroadcastManager implements FactoryContract
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * The array of resolved broadcast drivers.
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * Create a new manager instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Register the routes for handling broadcast channel authentication and sockets.
     *
     * @param  array|null  $attributes
     * @return void
     */
    public function routes(?array $attributes = null)
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        $attributes = $attributes ?: ['middleware' => ['web']];

        $this->app['router']->group($attributes, function ($router) {
            $router->match(
                ['get', 'post'], '/broadcasting/auth',
                '\\'.BroadcastController::class.'@authenticate'
            )->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
        });
    }

    /**
     * Register the routes for handling broadcast user authentication.
     *
     * @param  array|null  $attributes
     * @return void
     */
    public function userRoutes(?array $attributes = null)
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        $attributes = $attributes ?: ['middleware' => ['web']];

        $this->app['router']->group($attributes, function ($router) {
            $router->match(
                ['get', 'post'], '/broadcasting/user-auth',
                '\\'.BroadcastController::class.'@authenticateUser'
            )->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
        });
    }

    /**
     * Register the routes for handling broadcast authentication and sockets.
     *
     * Alias of "routes" method.
     *
     * @param  array|null  $attributes
     * @return void
     */
    public function channelRoutes(?array $attributes = null)
    {
        $this->routes($attributes);
    }

    /**
     * Get the socket ID for the given request.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @return string|null
     */
    public function socket($request = null)
    {
        if (! $request && ! $this->app->bound('request')) {
            return;
        }

        $request = $request ?: $this->app['request'];

        return $request->header('X-Socket-ID');
    }

    /**
     * Begin sending an anonymous broadcast to the given channels.
     */
    public function on(Channel|string|array $channels): AnonymousEvent
    {
        return new AnonymousEvent($channels);
    }

    /**
     * Begin sending an anonymous broadcast to the given private channels.
     */
    public function private(string $channel): AnonymousEvent
    {
        return $this->on(new PrivateChannel($channel));
    }

    /**
     * Begin sending an anonymous broadcast to the given presence channels.
     */
    public function presence(string $channel): AnonymousEvent
    {
        return $this->on(new PresenceChannel($channel));
    }

    /**
     * Begin broadcasting an event.
     *
     * @param  mixed  $event
     * @return \Illuminate\Broadcasting\PendingBroadcast
     */
    public function event($event = null)
    {
        return new PendingBroadcast($this->app->make('events'), $event);
    }

    /**
     * Queue the given event for broadcast.
     *
     * @param  mixed  $event
     * @return void
     */
    public function queue($event)
    {
        if ($event instanceof ShouldBroadcastNow ||
            (is_object($event) &&
             method_exists($event, 'shouldBroadcastNow') &&
             $event->shouldBroadcastNow())) {
            $dispatch = fn () => $this->app->make(BusDispatcherContract::class)
                ->dispatchNow(new BroadcastEvent(clone $event));

            return $event instanceof ShouldRescue
                ? $this->rescue($dispatch)
                : $dispatch();
        }

        $queue = match (true) {
            method_exists($event, 'broadcastQueue') => $event->broadcastQueue(),
            isset($event->broadcastQueue) => $event->broadcastQueue,
            isset($event->queue) => $event->queue,
            default => null,
        };

        $broadcastEvent = new BroadcastEvent(clone $event);

        if ($event instanceof ShouldBeUnique) {
            $broadcastEvent = new UniqueBroadcastEvent(clone $event);

            if ($this->mustBeUniqueAndCannotAcquireLock($broadcastEvent)) {
                return;
            }
        }

        $push = fn () => $this->app->make('queue')
            ->connection($event->connection ?? null)
            ->pushOn($queue, $broadcastEvent);

        $event instanceof ShouldRescue
            ? $this->rescue($push)
            : $push();
    }

    /**
     * Determine if the broadcastable event must be unique and determine if we can acquire the necessary lock.
     *
     * @param  mixed  $event
     * @return bool
     */
    protected function mustBeUniqueAndCannotAcquireLock($event)
    {
        return ! (new UniqueLock(
            method_exists($event, 'uniqueVia')
                ? $event->uniqueVia()
                : $this->app->make(Cache::class)
        ))->acquire($event);
    }

    /**
     * Get a driver instance.
     *
     * @param  string|null  $driver
     * @return mixed
     */
    public function connection($driver = null)
    {
        return $this->driver($driver);
    }

    /**
     * Get a driver instance.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function driver($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->drivers[$name] = $this->get($name);
    }

    /**
     * Attempt to get the connection from the local cache.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Broadcasting\Broadcaster
     */
    protected function get($name)
    {
        return $this->drivers[$name] ?? $this->resolve($name);
    }

    /**
     * Resolve the given broadcaster.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Broadcasting\Broadcaster
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Broadcast connection [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (! method_exists($this, $driverMethod)) {
            throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
        }

        try {
            return $this->{$driverMethod}($config);
        } catch (Throwable $e) {
            throw new RuntimeException("Failed to create broadcaster for connection \"{$name}\" with error: {$e->getMessage()}.", 0, $e);
        }
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array  $config
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }

    /**
     * Create an instance of the driver.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Broadcasting\Broadcaster
     */
    protected function createReverbDriver(array $config)
    {
        return $this->createPusherDriver($config);
    }

    /**
     * Create an instance of the driver.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Broadcasting\Broadcaster
     */
    protected function createPusherDriver(array $config)
    {
        return new PusherBroadcaster($this->pusher($config), $config['jsonp'] ?? false);
    }

    /**
     * Get a Pusher instance for the given configuration.
     *
     * @param  array  $config
     * @return \Pusher\Pusher
     */
    public function pusher(array $config)
    {
        $guzzleClient = new GuzzleClient(
            array_merge(
                [
                    'connect_timeout' => 10,
                    'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                    'timeout' => 30,
                ],
                $config['client_options'] ?? [],
            ),
        );

        $pusher = new Pusher(
            $config['key'],
            $config['secret'],
            $config['app_id'],
            $config['options'] ?? [],
            $guzzleClient,
        );

        if ($config['log'] ?? false) {
            $pusher->setLogger($this->app->make(LoggerInterface::class));
        }

        return $pusher;
    }

    /**
     * Create an instance of the driver.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Broadcasting\Broadcaster
     */
    protected function createAblyDriver(array $config)
    {
        return new AblyBroadcaster($this->ably($config));
    }

    /**
     * Get an Ably instance for the given configuration.
     *
     * @param  array  $config
     * @return \Ably\AblyRest
     */
    public function ably(array $config)
    {
        return new AblyRest($config);
    }

    /**
     * Create an instance of the driver.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Broadcasting\Broadcaster
     */
    protected function createRedisDriver(array $config)
    {
        return new RedisBroadcaster(
            $this->app->make('redis'), $config['connection'] ?? null,
            $this->app['config']->get('database.redis.options.prefix', '')
        );
    }

    /**
     * Create an instance of the driver.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Broadcasting\Broadcaster
     */
    protected function createLogDriver(array $config)
    {
        return new LogBroadcaster(
            $this->app->make(LoggerInterface::class)
        );
    }

    /**
     * Create an instance of the driver.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Broadcasting\Broadcaster
     */
    protected function createNullDriver(array $config)
    {
        return new NullBroadcaster;
    }

    /**
     * Get the connection configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        if (! is_null($name) && $name !== 'null') {
            return $this->app['config']["broadcasting.connections.{$name}"];
        }

        return ['driver' => 'null'];
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['broadcasting.default'];
    }

    /**
     * Set the default driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['broadcasting.default'] = $name;
    }

    /**
     * Disconnect the given driver / connection and remove it from local cache.
     *
     * @param  string|null  $name
     * @return void
     */
    public function purge($name = null)
    {
        $name ??= $this->getDefaultDriver();

        unset($this->drivers[$name]);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Execute the given callback using "rescue" if possible.
     *
     * @param  \Closure  $callback
     * @return mixed
     */
    protected function rescue(Closure $callback)
    {
        if (function_exists('rescue')) {
            return rescue($callback);
        }

        return $callback();
    }

    /**
     * Get the application instance used by the manager.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Set the application instance used by the manager.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return $this
     */
    public function setApplication($app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Forget all of the resolved driver instances.
     *
     * @return $this
     */
    public function forgetDrivers()
    {
        $this->drivers = [];

        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}
