<?php

namespace Illuminate\Database\Connectors;

use Exception;
use Illuminate\Database\DetectsLostConnections;
use PDO;
use Throwable;

class Connector
{
    use DetectsLostConnections;

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
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * Create a new PDO connection.
     *
     * @param  string  $dsn
     * @param  array  $config
     * @param  array  $options
     * @return \PDO
     *
     * @throws \Exception
     */
    public function createConnection($dsn, array $config, array $options)
    {
        [$username, $password] = [
            $config['username'] ?? null, $config['password'] ?? null,
        ];

        try {
            return $this->createPdoConnection(
                $dsn, $username, $password, $options
            );
        } catch (Exception $e) {
            return $this->tryAgainIfCausedByLostConnection(
                $e, $dsn, $username, $password, $options
            );
        }
    }

    /**
     * Create a new PDO connection instance.
     *
     * @param  string  $dsn
     * @param  string  $username
     * @param  string  $password
     * @param  array  $options
     * @return \PDO
     */
    protected function createPdoConnection($dsn, $username, #[\SensitiveParameter] $password, $options)
    {
        return version_compare(PHP_VERSION, '8.4.0', '<')
            ? new PDO($dsn, $username, $password, $options)
            : PDO::connect($dsn, $username, $password, $options); /** @phpstan-ignore staticMethod.notFound (PHP 8.4) */
    }

    /**
     * Handle an exception that occurred during connect execution.
     *
     * @param  \Throwable  $e
     * @param  string  $dsn
     * @param  string  $username
     * @param  string  $password
     * @param  array  $options
     * @return \PDO
     *
     * @throws \Throwable
     */
    protected function tryAgainIfCausedByLostConnection(Throwable $e, $dsn, $username, #[\SensitiveParameter] $password, $options)
    {
        if ($this->causedByLostConnection($e)) {
            return $this->createPdoConnection($dsn, $username, $password, $options);
        }

        throw $e;
    }

    /**
     * Get the PDO options based on the configuration.
     *
     * @param  array  $config
     * @return array
     */
    public function getOptions(array $config)
    {
        $options = $config['options'] ?? [];

        return array_diff_key($this->options, $options) + $options;
    }

    /**
     * Get the default PDO connection options.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return $this->options;
    }

    /**
     * Set the default PDO connection options.
     *
     * @param  array  $options
     * @return void
     */
    public function setDefaultOptions(array $options)
    {
        $this->options = $options;
    }
}
