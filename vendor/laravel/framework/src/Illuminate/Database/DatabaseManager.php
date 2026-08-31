<?php

namespace Illuminate\Database;

use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ConfigurationUrlParser;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use PDO;
use RuntimeException;

use function Illuminate\Support\enum_value;

/**
 * @mixin \Illuminate\Database\Connection
 */
class DatabaseManager implements ConnectionResolverInterface
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The database connection factory instance.
     *
     * @var \Illuminate\Database\Connectors\ConnectionFactory
     */
    protected $factory;

    /**
     * The active connection instances.
     *
     * @var array<string, \Illuminate\Database\Connection>
     */
    protected $connections = [];

    /**
     * The dynamically configured (DB::build) connection configurations.
     *
     * @var array<string, array>
     */
    protected $dynamicConnectionConfigurations = [];

    /**
     * The custom connection resolvers.
     *
     * @var array<string, callable>
     */
    protected $extensions = [];

    /**
     * The callback to be executed to reconnect to a database.
     *
     * @var callable
     */
    protected $reconnector;

    /**
     * Create a new database manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Database\Connectors\ConnectionFactory  $factory
     */
    public function __construct($app, ConnectionFactory $factory)
    {
        $this->app = $app;
        $this->factory = $factory;

        $this->reconnector = function ($connection) {
            $connection->setPdo(
                $this->reconnect($connection->getNameWithReadWriteType())->getRawPdo()
            );
        };
    }

    /**
     * Get a database connection instance.
     *
     * @param  \UnitEnum|string|null  $name
     * @return \Illuminate\Database\Connection
     */
    public function connection($name = null)
    {
        [$database, $type] = $this->parseConnectionName($name = enum_value($name) ?: $this->getDefaultConnection());

        // If we haven't created this connection, we'll create it based on the config
        // provided in the application. Once we've created the connections we will
        // set the "fetch mode" for PDO which determines the query return types.
        if (! isset($this->connections[$name])) {
            $this->connections[$name] = $this->configure(
                $this->makeConnection($database), $type
            );

            $this->dispatchConnectionEstablishedEvent($this->connections[$name]);
        }

        return $this->connections[$name];
    }

    /**
     * Build a database connection instance from the given configuration.
     *
     * @param  array  $config
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function build(array $config)
    {
        $config['name'] ??= static::calculateDynamicConnectionName($config);

        $this->dynamicConnectionConfigurations[$config['name']] = $config;

        return $this->connectUsing($config['name'], $config, true);
    }

    /**
     * Calculate the dynamic connection name for an on-demand connection based on its configuration.
     *
     * @param  array  $config
     * @return string
     */
    public static function calculateDynamicConnectionName(array $config)
    {
        return 'dynamic_'.md5((new Collection($config))->map(function ($value, $key) {
            return $key.(is_string($value) || is_int($value) ? $value : '');
        })->implode(''));
    }

    /**
     * Get a database connection instance from the given configuration.
     *
     * @param  \UnitEnum|string  $name
     * @param  array  $config
     * @param  bool  $force
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function connectUsing(string $name, array $config, bool $force = false)
    {
        if ($force) {
            $this->purge($name = enum_value($name));
        }

        if (isset($this->connections[$name])) {
            throw new RuntimeException("Cannot establish connection [$name] because another connection with that name already exists.");
        }

        $connection = $this->configure(
            $this->factory->make($config, $name), null
        );

        $this->dispatchConnectionEstablishedEvent($connection);

        return tap($connection, fn ($connection) => $this->connections[$name] = $connection);
    }

    /**
     * Parse the connection into an array of the name and read / write type.
     *
     * @param  string  $name
     * @return array
     */
    protected function parseConnectionName($name)
    {
        return Str::endsWith($name, ['::read', '::write'])
            ? explode('::', $name, 2)
            : [$name, null];
    }

    /**
     * Make the database connection instance.
     *
     * @param  string  $name
     * @return \Illuminate\Database\Connection
     */
    protected function makeConnection($name)
    {
        $config = $this->configuration($name);

        // First we will check by the connection name to see if an extension has been
        // registered specifically for that connection. If it has we will call the
        // Closure and pass it the config allowing it to resolve the connection.
        if (isset($this->extensions[$name])) {
            return call_user_func($this->extensions[$name], $config, $name);
        }

        // Next we will check to see if an extension has been registered for a driver
        // and will call the Closure if so, which allows us to have a more generic
        // resolver for the drivers themselves which applies to all connections.
        if (isset($this->extensions[$driver = $config['driver']])) {
            return call_user_func($this->extensions[$driver], $config, $name);
        }

        return $this->factory->make($config, $name);
    }

    /**
     * Get the configuration for a connection.
     *
     * @param  string  $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function configuration($name)
    {
        $connections = $this->app['config']['database.connections'];

        $config = $this->dynamicConnectionConfigurations[$name] ?? Arr::get($connections, $name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Database connection [{$name}] not configured.");
        }

        return (new ConfigurationUrlParser)
            ->parseConfiguration($config);
    }

    /**
     * Prepare the database connection instance.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @param  string  $type
     * @return \Illuminate\Database\Connection
     */
    protected function configure(Connection $connection, $type)
    {
        $connection = $this->setPdoForType($connection, $type)->setReadWriteType($type);

        // First we'll set the fetch mode and a few other dependencies of the database
        // connection. This method basically just configures and prepares it to get
        // used by the application. Once we're finished we'll return it back out.
        if ($this->app->bound('events')) {
            $connection->setEventDispatcher($this->app['events']);
        }

        if ($this->app->bound('db.transactions')) {
            $connection->setTransactionManager($this->app['db.transactions']);
        }

        // Here we'll set a reconnector callback. This reconnector can be any callable
        // so we will set a Closure to reconnect from this manager with the name of
        // the connection, which will allow us to reconnect from the connections.
        $connection->setReconnector($this->reconnector);

        return $connection;
    }

    /**
     * Dispatch the ConnectionEstablished event if the event dispatcher is available.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @return void
     */
    protected function dispatchConnectionEstablishedEvent(Connection $connection)
    {
        if (! $this->app->bound('events')) {
            return;
        }

        $this->app['events']->dispatch(
            new ConnectionEstablished($connection)
        );
    }

    /**
     * Prepare the read / write mode for database connection instance.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @param  string|null  $type
     * @return \Illuminate\Database\Connection
     */
    protected function setPdoForType(Connection $connection, $type = null)
    {
        if ($type === 'read') {
            $connection->setPdo($connection->getReadPdo());
        } elseif ($type === 'write') {
            $connection->setReadPdo($connection->getPdo());
        }

        return $connection;
    }

    /**
     * Disconnect from the given database and remove from local cache.
     *
     * @param  \UnitEnum|string|null  $name
     * @return void
     */
    public function purge($name = null)
    {
        $this->disconnect($name = enum_value($name) ?: $this->getDefaultConnection());

        unset($this->connections[$name]);
    }

    /**
     * Disconnect from the given database.
     *
     * @param  \UnitEnum|string|null  $name
     * @return void
     */
    public function disconnect($name = null)
    {
        if (isset($this->connections[$name = enum_value($name) ?: $this->getDefaultConnection()])) {
            $this->connections[$name]->disconnect();
        }
    }

    /**
     * Reconnect to the given database.
     *
     * @param  \UnitEnum|string|null  $name
     * @return \Illuminate\Database\Connection
     */
    public function reconnect($name = null)
    {
        $this->disconnect($name = enum_value($name) ?: $this->getDefaultConnection());

        if (! isset($this->connections[$name])) {
            return $this->connection($name);
        }

        return tap($this->refreshPdoConnections($name), function ($connection) {
            $this->dispatchConnectionEstablishedEvent($connection);
        });
    }

    /**
     * Set the default database connection for the callback execution.
     *
     * @param  \UnitEnum|string  $name
     * @param  callable  $callback
     * @return mixed
     */
    public function usingConnection($name, callable $callback)
    {
        $previousName = $this->getDefaultConnection();

        $this->setDefaultConnection($name = enum_value($name));

        try {
            return $callback();
        } finally {
            $this->setDefaultConnection($previousName);
        }
    }

    /**
     * Refresh the PDO connections on a given connection.
     *
     * @param  string  $name
     * @return \Illuminate\Database\Connection
     */
    protected function refreshPdoConnections($name)
    {
        [$database, $type] = $this->parseConnectionName($name);

        $fresh = $this->configure(
            $this->makeConnection($database), $type
        );

        return $this->connections[$name]
            ->setPdo($fresh->getRawPdo())
            ->setReadPdo($fresh->getRawReadPdo());
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->app['config']['database.default'];
    }

    /**
     * Set the default connection name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultConnection($name)
    {
        $this->app['config']['database.default'] = $name;
    }

    /**
     * Get all of the supported drivers.
     *
     * @return string[]
     */
    public function supportedDrivers()
    {
        return ['mysql', 'mariadb', 'pgsql', 'sqlite', 'sqlsrv'];
    }

    /**
     * Get all of the drivers that are actually available.
     *
     * @return string[]
     */
    public function availableDrivers()
    {
        return array_intersect(
            $this->supportedDrivers(),
            str_replace('dblib', 'sqlsrv', PDO::getAvailableDrivers())
        );
    }

    /**
     * Register an extension connection resolver.
     *
     * @param  string  $name
     * @param  callable  $resolver
     * @return void
     */
    public function extend($name, callable $resolver)
    {
        $this->extensions[$name] = $resolver;
    }

    /**
     * Remove an extension connection resolver.
     *
     * @param  string  $name
     * @return void
     */
    public function forgetExtension($name)
    {
        unset($this->extensions[$name]);
    }

    /**
     * Return all of the created connections.
     *
     * @return array<string, \Illuminate\Database\Connection>
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Set the database reconnector callback.
     *
     * @param  callable  $reconnector
     * @return void
     */
    public function setReconnector(callable $reconnector)
    {
        $this->reconnector = $reconnector;
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
     * Dynamically pass methods to the default connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->connection()->$method(...$parameters);
    }
}
