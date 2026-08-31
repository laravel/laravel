<?php

namespace Illuminate\Database\Connectors;

use PDO;

class MySqlConnector extends Connector implements ConnectorInterface
{
    /**
     * Establish a database connection.
     *
     * @param  array  $config
     * @return \PDO
     */
    public function connect(array $config)
    {
        $dsn = $this->getDsn($config);

        $options = $this->getOptions($config);

        // We need to grab the PDO options that should be used while making the brand
        // new connection instance. The PDO options control various aspects of the
        // connection's behavior, and some might be specified by the developers.
        $connection = $this->createConnection($dsn, $config, $options);

        if (! empty($config['database']) &&
            (! isset($config['use_db_after_connecting']) ||
             $config['use_db_after_connecting'])) {
            $connection->exec("use `{$config['database']}`;");
        }

        $this->configureConnection($connection, $config);

        return $connection;
    }

    /**
     * Create a DSN string from a configuration.
     *
     * Chooses socket or host/port based on the 'unix_socket' config value.
     *
     * @param  array  $config
     * @return string
     */
    protected function getDsn(array $config)
    {
        return $this->hasSocket($config)
            ? $this->getSocketDsn($config)
            : $this->getHostDsn($config);
    }

    /**
     * Determine if the given configuration array has a UNIX socket value.
     *
     * @param  array  $config
     * @return bool
     */
    protected function hasSocket(array $config)
    {
        return isset($config['unix_socket']) && ! empty($config['unix_socket']);
    }

    /**
     * Get the DSN string for a socket configuration.
     *
     * @param  array  $config
     * @return string
     */
    protected function getSocketDsn(array $config)
    {
        return "mysql:unix_socket={$config['unix_socket']};dbname={$config['database']}";
    }

    /**
     * Get the DSN string for a host / port configuration.
     *
     * @param  array  $config
     * @return string
     */
    protected function getHostDsn(array $config)
    {
        return isset($config['port'])
            ? "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}"
            : "mysql:host={$config['host']};dbname={$config['database']}";
    }

    /**
     * Configure the given PDO connection.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return void
     */
    protected function configureConnection(PDO $connection, array $config)
    {
        if (isset($config['isolation_level'])) {
            $connection->exec(sprintf('SET SESSION TRANSACTION ISOLATION LEVEL %s;', $config['isolation_level']));
        }

        $statements = [];

        if (isset($config['charset'])) {
            if (isset($config['collation'])) {
                $statements[] = sprintf("NAMES '%s' COLLATE '%s'", $config['charset'], $config['collation']);
            } else {
                $statements[] = sprintf("NAMES '%s'", $config['charset']);
            }
        }

        if (isset($config['timezone'])) {
            $statements[] = sprintf("time_zone='%s'", $config['timezone']);
        }

        $sqlMode = $this->getSqlMode($connection, $config);

        if ($sqlMode !== null) {
            $statements[] = sprintf("SESSION sql_mode='%s'", $sqlMode);
        }

        if ($statements !== []) {
            $connection->exec(sprintf('SET %s;', implode(', ', $statements)));
        }
    }

    /**
     * Get the sql_mode value.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return string|null
     */
    protected function getSqlMode(PDO $connection, array $config)
    {
        if (isset($config['modes'])) {
            return implode(',', $config['modes']);
        }

        if (! isset($config['strict'])) {
            return null;
        }

        if (! $config['strict']) {
            return 'NO_ENGINE_SUBSTITUTION';
        }

        $version = $config['version'] ?? $connection->getAttribute(PDO::ATTR_SERVER_VERSION);

        if (version_compare($version, '8.0.11', '>=')) {
            return 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
        }

        return 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
    }
}
