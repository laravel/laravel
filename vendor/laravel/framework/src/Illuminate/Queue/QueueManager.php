<?php

namespace Illuminate\Queue;

use Closure;
use Illuminate\Contracts\Queue\Factory as FactoryContract;
use Illuminate\Contracts\Queue\Monitor as MonitorContract;
use InvalidArgumentException;

/**
 * @mixin \Illuminate\Contracts\Queue\Queue
 */
class QueueManager implements FactoryContract, MonitorContract
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved queue connections.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * The array of resolved queue connectors.
     *
     * @var array
     */
    protected $connectors = [];

    /**
     * Create a new queue manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Register an event listener for the before job event.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function before($callback)
    {
        $this->app['events']->listen(Events\JobProcessing::class, $callback);
    }

    /**
     * Register an event listener for the after job event.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function after($callback)
    {
        $this->app['events']->listen(Events\JobProcessed::class, $callback);
    }

    /**
     * Register an event listener for the exception occurred job event.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function exceptionOccurred($callback)
    {
        $this->app['events']->listen(Events\JobExceptionOccurred::class, $callback);
    }

    /**
     * Register an event listener for the daemon queue loop.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function looping($callback)
    {
        $this->app['events']->listen(Events\Looping::class, $callback);
    }

    /**
     * Register an event listener for the failed job event.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function failing($callback)
    {
        $this->app['events']->listen(Events\JobFailed::class, $callback);
    }

    /**
     * Register an event listener for the daemon queue starting.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function starting($callback)
    {
        $this->app['events']->listen(Events\WorkerStarting::class, $callback);
    }

    /**
     * Register an event listener for the daemon queue stopping.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function stopping($callback)
    {
        $this->app['events']->listen(Events\WorkerStopping::class, $callback);
    }

    /**
     * Determine if the driver is connected.
     *
     * @param  string|null  $name
     * @return bool
     */
    public function connected($name = null)
    {
        return isset($this->connections[$name ?: $this->getDefaultDriver()]);
    }

    /**
     * Resolve a queue connection instance.
     *
     * @param  string|null  $name
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connection($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        // If the connection has not been resolved yet we will resolve it now as all
        // of the connections are resolved when they are actually needed so we do
        // not make any unnecessary connection to the various queue end-points.
        if (! isset($this->connections[$name])) {
            $this->connections[$name] = $this->resolve($name);

            $this->connections[$name]->setContainer($this->app);
        }

        return $this->connections[$name];
    }

    /**
     * Resolve a queue connection.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Queue\Queue
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("The [{$name}] queue connection has not been configured.");
        }

        $queue = $this->getConnector($config['driver'])
            ->connect($config)
            ->setConnectionName($name);

        if (method_exists($queue, 'setConfig')) {
            $queue->setConfig($config);
        }

        return $queue;
    }

    /**
     * Get the connector for a given driver.
     *
     * @param  string  $driver
     * @return \Illuminate\Queue\Connectors\ConnectorInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function getConnector($driver)
    {
        if (! isset($this->connectors[$driver])) {
            throw new InvalidArgumentException("No connector for [$driver].");
        }

        return call_user_func($this->connectors[$driver]);
    }

    /**
     * Pause a queue by its connection and name.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @return void
     */
    public function pause($connection, $queue)
    {
        $this->app['cache']
            ->store()
            ->forever("illuminate:queue:paused:{$connection}:{$queue}", true);

        $this->app['events']->dispatch(
            new Events\QueuePaused($connection, $queue)
        );
    }

    /**
     * Pause a queue by its connection and name for a given amount of time.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  \DateTimeInterface|\DateInterval|int  $ttl
     * @return void
     */
    public function pauseFor($connection, $queue, $ttl)
    {
        $this->app['cache']
            ->store()
            ->put("illuminate:queue:paused:{$connection}:{$queue}", true, $ttl);

        $this->app['events']->dispatch(
            new Events\QueuePaused($connection, $queue, $ttl)
        );
    }

    /**
     * Resume a paused queue by its connection and name.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @return void
     */
    public function resume($connection, $queue)
    {
        $this->app['cache']
            ->store()
            ->forget("illuminate:queue:paused:{$connection}:{$queue}");

        $this->app['events']->dispatch(
            new Events\QueueResumed($connection, $queue)
        );
    }

    /**
     * Determine if a queue is paused.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @return bool
     */
    public function isPaused($connection, $queue)
    {
        return (bool) $this->app['cache']
            ->store()
            ->get("illuminate:queue:paused:{$connection}:{$queue}", false);
    }

    /**
     * Indicate that queue workers should not poll for restart or pause signals.
     *
     * This prevents the workers from hitting the application cache to determine if they need to pause or restart.
     *
     * @return void
     */
    public function withoutInterruptionPolling()
    {
        Worker::$restartable = false;
        Worker::$pausable = false;
    }

    /**
     * Add a queue connection resolver.
     *
     * @param  string  $driver
     * @param  \Closure  $resolver
     * @return void
     */
    public function extend($driver, Closure $resolver)
    {
        $this->addConnector($driver, $resolver);
    }

    /**
     * Add a queue connection resolver.
     *
     * @param  string  $driver
     * @param  \Closure  $resolver
     * @return void
     */
    public function addConnector($driver, Closure $resolver)
    {
        $this->connectors[$driver] = $resolver;
    }

    /**
     * Get the queue connection configuration.
     *
     * @param  string  $name
     * @return array|null
     */
    protected function getConfig($name)
    {
        if (! is_null($name) && $name !== 'null') {
            return $this->app['config']["queue.connections.{$name}"];
        }

        return ['driver' => 'null'];
    }

    /**
     * Get the name of the default queue connection.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['queue.default'];
    }

    /**
     * Set the name of the default queue connection.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['queue.default'] = $name;
    }

    /**
     * Get the full name for the given connection.
     *
     * @param  string|null  $connection
     * @return string
     */
    public function getName($connection = null)
    {
        return $connection ?: $this->getDefaultDriver();
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

        foreach ($this->connections as $connection) {
            $connection->setContainer($app);
        }

        return $this;
    }

    /**
     * Dynamically pass calls to the default connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->connection()->$method(...$parameters);
    }
}
