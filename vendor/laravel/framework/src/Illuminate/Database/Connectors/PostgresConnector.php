<?php

namespace Illuminate\Database\Connectors;

use Illuminate\Database\Concerns\ParsesSearchPath;
use PDO;

class PostgresConnector extends Connector implements ConnectorInterface
{
    use ParsesSearchPath;

    /**
     * The default PDO connection options.
     *
     * @var array
     */
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ];

    /**
     * Establish a database connection.
     *
     * @param  array  $config
     * @return \PDO
     */
    public function connect(array $config)
    {
        // First we'll create the basic DSN and connection instance connecting to the
        // using the configuration option specified by the developer. We will also
        // set the default character set on the connections to UTF-8 by default.
        $connection = $this->createConnection(
            $this->getDsn($config), $config, $this->getOptions($config)
        );

        $this->configureIsolationLevel($connection, $config);

        // Next, we will check to see if a timezone has been specified in this config
        // and if it has we will issue a statement to modify the timezone with the
        // database. Setting this DB timezone is an optional configuration item.
        $this->configureTimezone($connection, $config);

        $this->configureSearchPath($connection, $config);

        $this->configureSynchronousCommit($connection, $config);

        return $connection;
    }

    /**
     * Create a DSN string from a configuration.
     *
     * @param  array  $config
     * @return string
     */
    protected function getDsn(array $config)
    {
        // First we will create the basic DSN setup as well as the port if it is in
        // in the configuration options. This will give us the basic DSN we will
        // need to establish the PDO connections and return them back for use.
        extract($config, EXTR_SKIP);

        $host = isset($host) ? "host={$host};" : '';

        // Sometimes - users may need to connect to a database that has a different
        // name than the database used for "information_schema" queries. This is
        // typically the case if using "pgbouncer" type software when pooling.
        $database = $connect_via_database ?? $database ?? null;
        $port = $connect_via_port ?? $port ?? null;

        $dsn = "pgsql:{$host}dbname='{$database}'";

        // If a port was specified, we will add it to this Postgres DSN connections
        // format. Once we have done that we are ready to return this connection
        // string back out for usage, as this has been fully constructed here.
        if (! is_null($port)) {
            $dsn .= ";port={$port}";
        }

        if (isset($charset)) {
            $dsn .= ";client_encoding='{$charset}'";
        }

        // Postgres allows an application_name to be set by the user and this name is
        // used to when monitoring the application with pg_stat_activity. So we'll
        // determine if the option has been specified and run a statement if so.
        if (isset($application_name)) {
            $dsn .= ";application_name='".str_replace("'", "\'", $application_name)."'";
        }

        return $this->addSslOptions($dsn, $config);
    }

    /**
     * Add the SSL options to the DSN.
     *
     * @param  string  $dsn
     * @param  array  $config
     * @return string
     */
    protected function addSslOptions($dsn, array $config)
    {
        foreach (['sslmode', 'sslcert', 'sslkey', 'sslrootcert'] as $option) {
            if (isset($config[$option])) {
                $dsn .= ";{$option}={$config[$option]}";
            }
        }

        return $dsn;
    }

    /**
     * Set the connection transaction isolation level.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return void
     */
    protected function configureIsolationLevel($connection, array $config)
    {
        if (isset($config['isolation_level'])) {
            $connection->prepare("set session characteristics as transaction isolation level {$config['isolation_level']}")->execute();
        }
    }

    /**
     * Set the timezone on the connection.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return void
     */
    protected function configureTimezone($connection, array $config)
    {
        if (isset($config['timezone'])) {
            $timezone = $config['timezone'];

            $connection->prepare("set time zone '{$timezone}'")->execute();
        }
    }

    /**
     * Set the "search_path" on the database connection.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return void
     */
    protected function configureSearchPath($connection, $config)
    {
        if (isset($config['search_path']) || isset($config['schema'])) {
            $searchPath = $this->quoteSearchPath(
                $this->parseSearchPath($config['search_path'] ?? $config['schema'])
            );

            $connection->prepare("set search_path to {$searchPath}")->execute();
        }
    }

    /**
     * Format the search path for the DSN.
     *
     * @param  array  $searchPath
     * @return string
     */
    protected function quoteSearchPath($searchPath)
    {
        return count($searchPath) === 1 ? '"'.$searchPath[0].'"' : '"'.implode('", "', $searchPath).'"';
    }

    /**
     * Configure the synchronous_commit setting.
     *
     * @param  \PDO  $connection
     * @param  array  $config
     * @return void
     */
    protected function configureSynchronousCommit($connection, array $config)
    {
        if (isset($config['synchronous_commit'])) {
            $connection->prepare("set synchronous_commit to '{$config['synchronous_commit']}'")->execute();
        }
    }
}
