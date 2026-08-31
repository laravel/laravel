<?php

namespace Illuminate\Database\Connectors;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\MariaDbConnection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\SqlServerConnection;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use PDOException;

class ConnectionFactory
{
    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Create a new connection factory instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Establish a PDO connection based on the configuration.
     *
     * @param  array  $config
     * @param  string|null  $name
     * @return \Illuminate\Database\Connection
     */
    public function make(array $config, $name = null)
    {
        $config = $this->parseConfig($config, $name);

        if (isset($config['read'])) {
            return $this->createReadWriteConnection($config);
        }

        return $this->createSingleConnection($config);
    }

    /**
     * Parse and prepare the database configuration.
     *
     * @param  array  $config
     * @param  string  $name
     * @return array
     */
    protected function parseConfig(array $config, $name)
    {
        return Arr::add(Arr::add($config, 'prefix', ''), 'name', $name);
    }

    /**
     * Create a single database connection instance.
     *
     * @param  array  $config
     * @return \Illuminate\Database\Connection
     */
    protected function createSingleConnection(array $config)
    {
        $pdo = $this->createPdoResolver($config);

        return $this->createConnection(
            $config['driver'], $pdo, $config['database'], $config['prefix'], $config
        );
    }

    /**
     * Create a read / write database connection instance.
     *
     * @param  array  $config
     * @return \Illuminate\Database\Connection
     */
    protected function createReadWriteConnection(array $config)
    {
        $connection = $this->createSingleConnection($this->getWriteConfig($config));

        return $connection
            ->setReadPdo($this->createReadPdo($config))
            ->setReadPdoConfig($this->getReadConfig($config));
    }

    /**
     * Create a new PDO instance for reading.
     *
     * @param  array  $config
     * @return \Closure
     */
    protected function createReadPdo(array $config)
    {
        return $this->createPdoResolver($this->getReadConfig($config));
    }

    /**
     * Get the read configuration for a read / write connection.
     *
     * @param  array  $config
     * @return array
     */
    protected function getReadConfig(array $config)
    {
        return $this->mergeReadWriteConfig(
            $config, $this->getReadWriteConfig($config, 'read')
        );
    }

    /**
     * Get the write configuration for a read / write connection.
     *
     * @param  array  $config
     * @return array
     */
    protected function getWriteConfig(array $config)
    {
        return $this->mergeReadWriteConfig(
            $config, $this->getReadWriteConfig($config, 'write')
        );
    }

    /**
     * Get a read / write level configuration.
     *
     * @param  array  $config
     * @param  string  $type
     * @return array
     */
    protected function getReadWriteConfig(array $config, $type)
    {
        return isset($config[$type][0])
            ? Arr::random($config[$type])
            : $config[$type];
    }

    /**
     * Merge a configuration for a read / write connection.
     *
     * @param  array  $config
     * @param  array  $merge
     * @return array
     */
    protected function mergeReadWriteConfig(array $config, array $merge)
    {
        return Arr::except(array_merge($config, $merge), ['read', 'write']);
    }

    /**
     * Create a new Closure that resolves to a PDO instance.
     *
     * @param  array  $config
     * @return \Closure
     */
    protected function createPdoResolver(array $config)
    {
        return array_key_exists('host', $config)
            ? $this->createPdoResolverWithHosts($config)
            : $this->createPdoResolverWithoutHosts($config);
    }

    /**
     * Create a new Closure that resolves to a PDO instance with a specific host or an array of hosts.
     *
     * @param  array  $config
     * @return \Closure
     *
     * @throws \PDOException
     */
    protected function createPdoResolverWithHosts(array $config)
    {
        return function () use ($config) {
            foreach (Arr::shuffle($this->parseHosts($config)) as $host) {
                $config['host'] = $host;

                try {
                    return $this->createConnector($config)->connect($config);
                } catch (PDOException $e) {
                    continue;
                }
            }

            if (isset($e)) {
                throw $e;
            }
        };
    }

    /**
     * Parse the hosts configuration item into an array.
     *
     * @param  array  $config
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function parseHosts(array $config)
    {
        $hosts = Arr::wrap($config['host']);

        if (empty($hosts)) {
            throw new InvalidArgumentException('Database hosts array is empty.');
        }

        return $hosts;
    }

    /**
     * Create a new Closure that resolves to a PDO instance where there is no configured host.
     *
     * @param  array  $config
     * @return \Closure
     */
    protected function createPdoResolverWithoutHosts(array $config)
    {
        return fn () => $this->createConnector($config)->connect($config);
    }

    /**
     * Create a connector instance based on the configuration.
     *
     * @param  array  $config
     * @return \Illuminate\Database\Connectors\ConnectorInterface
     *
     * @throws \InvalidArgumentException
     */
    public function createConnector(array $config)
    {
        if (! isset($config['driver'])) {
            throw new InvalidArgumentException('A driver must be specified.');
        }

        if ($this->container->bound($key = "db.connector.{$config['driver']}")) {
            return $this->container->make($key);
        }

        return match ($config['driver']) {
            'mysql' => new MySqlConnector,
            'mariadb' => new MariaDbConnector,
            'pgsql' => new PostgresConnector,
            'sqlite' => new SQLiteConnector,
            'sqlsrv' => new SqlServerConnector,
            default => throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]."),
        };
    }

    /**
     * Create a new connection instance.
     *
     * @param  string  $driver
     * @param  \PDO|\Closure  $connection
     * @param  string  $database
     * @param  string  $prefix
     * @param  array  $config
     * @return \Illuminate\Database\Connection
     *
     * @throws \InvalidArgumentException
     */
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
    {
        if ($resolver = Connection::getResolver($driver)) {
            return $resolver($connection, $database, $prefix, $config);
        }

        return match ($driver) {
            'mysql' => new MySqlConnection($connection, $database, $prefix, $config),
            'mariadb' => new MariaDbConnection($connection, $database, $prefix, $config),
            'pgsql' => new PostgresConnection($connection, $database, $prefix, $config),
            'sqlite' => new SQLiteConnection($connection, $database, $prefix, $config),
            'sqlsrv' => new SqlServerConnection($connection, $database, $prefix, $config),
            default => throw new InvalidArgumentException("Unsupported driver [{$driver}]."),
        };
    }
}
