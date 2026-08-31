<?php

namespace Illuminate\Redis;

use Closure;
use Illuminate\Contracts\Redis\Factory;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Redis\Connectors\PhpRedisConnector;
use Illuminate\Redis\Connectors\PredisConnector;
use Illuminate\Support\Arr;
use Illuminate\Support\ConfigurationUrlParser;
use InvalidArgumentException;

use function Illuminate\Support\enum_value;

/**
 * @mixin \Illuminate\Redis\Connections\Connection
 */
class RedisManager implements Factory
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The name of the default driver.
     *
     * @var string
     */
    protected $driver;

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The Redis server configurations.
     *
     * @var array
     */
    protected $config;

    /**
     * The Redis connections.
     *
     * @var mixed
     */
    protected $connections;

    /**
     * Indicates whether event dispatcher is set on connections.
     *
     * @var bool
     */
    protected $events = false;

    /**
     * Create a new Redis manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  string  $driver
     * @param  array  $config
     */
    public function __construct($app, $driver, array $config)
    {
        $this->app = $app;
        $this->driver = $driver;
        $this->config = $config;
    }

    /**
     * Get a Redis connection by name.
     *
     * @param  \UnitEnum|string|null  $name
     * @return \Illuminate\Redis\Connections\Connection
     */
    public function connection($name = null)
    {
        $name = enum_value($name) ?: 'default';

        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        return $this->connections[$name] = $this->configure(
            $this->resolve($name), $name
        );
    }

    /**
     * Resolve the given connection by name.
     *
     * @param  string|null  $name
     * @return \Illuminate\Redis\Connections\Connection
     *
     * @throws \InvalidArgumentException
     */
    public function resolve($name = null)
    {
        $name = $name ?: 'default';

        $options = $this->config['options'] ?? [];

        if (isset($this->config[$name])) {
            return $this->connector()->connect(
                $this->parseConnectionConfiguration($this->config[$name]),
                array_merge(Arr::except($options, 'parameters'), ['parameters' => Arr::get($options, 'parameters.'.$name, Arr::get($options, 'parameters', []))])
            );
        }

        if (isset($this->config['clusters'][$name])) {
            return $this->resolveCluster($name);
        }

        throw new InvalidArgumentException("Redis connection [{$name}] not configured.");
    }

    /**
     * Resolve the given cluster connection by name.
     *
     * @param  string  $name
     * @return \Illuminate\Redis\Connections\Connection
     */
    protected function resolveCluster($name)
    {
        return $this->connector()->connectToCluster(
            array_map(function ($config) {
                return $this->parseConnectionConfiguration($config);
            }, $this->config['clusters'][$name]),
            $this->config['clusters']['options'] ?? [],
            $this->config['options'] ?? []
        );
    }

    /**
     * Configure the given connection to prepare it for commands.
     *
     * @param  \Illuminate\Redis\Connections\Connection  $connection
     * @param  string  $name
     * @return \Illuminate\Redis\Connections\Connection
     */
    protected function configure(Connection $connection, $name)
    {
        $connection->setName($name);

        if ($this->events && $this->app->bound('events')) {
            $connection->setEventDispatcher($this->app->make('events'));
        }

        return $connection;
    }

    /**
     * Get the connector instance for the current driver.
     *
     * @return \Illuminate\Contracts\Redis\Connector|null
     */
    protected function connector()
    {
        $customCreator = $this->customCreators[$this->driver] ?? null;

        if ($customCreator) {
            return $customCreator();
        }

        return match ($this->driver) {
            'predis' => new PredisConnector,
            'phpredis' => new PhpRedisConnector,
            default => null,
        };
    }

    /**
     * Parse the Redis connection configuration.
     *
     * @param  mixed  $config
     * @return array
     */
    protected function parseConnectionConfiguration($config)
    {
        $parsed = (new ConfigurationUrlParser)->parseConfiguration($config);

        $driver = strtolower($parsed['driver'] ?? '');

        if (in_array($driver, ['tcp', 'tls'])) {
            $parsed['scheme'] = $driver;
        }

        return array_filter($parsed, function ($key) {
            return $key !== 'driver';
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Return all of the created connections.
     *
     * @return array
     */
    public function connections()
    {
        return $this->connections;
    }

    /**
     * Enable the firing of Redis command events.
     *
     * @return void
     */
    public function enableEvents()
    {
        $this->events = true;
    }

    /**
     * Disable the firing of Redis command events.
     *
     * @return void
     */
    public function disableEvents()
    {
        $this->events = false;
    }

    /**
     * Set the default driver.
     *
     * @param  string  $driver
     * @return void
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * Disconnect the given connection and remove from local cache.
     *
     * @param  string|null  $name
     * @return void
     */
    public function purge($name = null)
    {
        $name = $name ?: 'default';

        unset($this->connections[$name]);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  \Closure  $callback
     *
     * @param-closure-this  $this  $callback
     *
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    /**
     * Pass methods onto the default Redis connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->connection()->{$method}(...$parameters);
    }
}
