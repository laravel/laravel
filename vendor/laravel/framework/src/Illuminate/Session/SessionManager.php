<?php

namespace Illuminate\Session;

use Illuminate\Support\Manager;

/**
 * @mixin \Illuminate\Session\Store
 */
class SessionManager extends Manager
{
    /**
     * Call a custom driver creator.
     *
     * @param  string  $driver
     * @return \Illuminate\Session\Store
     */
    protected function callCustomCreator($driver)
    {
        return $this->buildSession(parent::callCustomCreator($driver));
    }

    /**
     * Create an instance of the "null" session driver.
     *
     * @return \Illuminate\Session\Store
     */
    protected function createNullDriver()
    {
        return $this->buildSession(new NullSessionHandler);
    }

    /**
     * Create an instance of the "array" session driver.
     *
     * @return \Illuminate\Session\Store
     */
    protected function createArrayDriver()
    {
        return $this->buildSession(new ArraySessionHandler(
            $this->config->get('session.lifetime')
        ));
    }

    /**
     * Create an instance of the "cookie" session driver.
     *
     * @return \Illuminate\Session\Store
     */
    protected function createCookieDriver()
    {
        return $this->buildSession(new CookieSessionHandler(
            $this->container->make('cookie'),
            $this->config->get('session.lifetime'),
            $this->config->get('session.expire_on_close')
        ));
    }

    /**
     * Create an instance of the file session driver.
     *
     * @return \Illuminate\Session\Store
     */
    protected function createFileDriver()
    {
        return $this->createNativeDriver();
    }

    /**
     * Create an instance of the file session driver.
     *
     * @return \Illuminate\Session\Store
     */
    protected function createNativeDriver()
    {
        $lifetime = $this->config->get('session.lifetime');

        return $this->buildSession(new FileSessionHandler(
            $this->container->make('files'), $this->config->get('session.files'), $lifetime
        ));
    }

    /**
     * Create an instance of the database session driver.
     *
     * @return \Illuminate\Session\Store
     */
    protected function createDatabaseDriver()
    {
        $table = $this->config->get('session.table');

        $lifetime = $this->config->get('session.lifetime');

        return $this->buildSession(new DatabaseSessionHandler(
            $this->getDatabaseConnection(), $table, $lifetime, $this->container
        ));
    }

    /**
     * Get the database connection for the database driver.
     *
     * @return \Illuminate\Database\Connection
     */
    protected function getDatabaseConnection()
    {
        $connection = $this->config->get('session.connection');

        return $this->container->make('db')->connection($connection);
    }

    /**
     * Create an instance of the APC session driver.
     *
     * @return \Illuminate\Session\Store
     */
    protected function createApcDriver()
    {
        return $this->createCacheBased('apc');
    }

    /**
     * Create an instance of the Memcached session driver.
     *
     * @return \Illuminate\Session\Store
     */
    protected function createMemcachedDriver()
    {
        return $this->createCacheBased('memcached');
    }

    /**
     * Create an instance of the Redis session driver.
     *
     * @return \Illuminate\Session\Store
     */
    protected function createRedisDriver()
    {
        $handler = $this->createCacheHandler('redis');

        $handler->getCache()->getStore()->setConnection(
            $this->config->get('session.connection')
        );

        return $this->buildSession($handler);
    }

    /**
     * Create an instance of the DynamoDB session driver.
     *
     * @return \Illuminate\Session\Store
     */
    protected function createDynamodbDriver()
    {
        return $this->createCacheBased('dynamodb');
    }

    /**
     * Create an instance of a cache driven driver.
     *
     * @param  string  $driver
     * @return \Illuminate\Session\Store
     */
    protected function createCacheBased($driver)
    {
        return $this->buildSession($this->createCacheHandler($driver));
    }

    /**
     * Create the cache based session handler instance.
     *
     * @param  string  $driver
     * @return \Illuminate\Session\CacheBasedSessionHandler
     */
    protected function createCacheHandler($driver)
    {
        $store = $this->config->get('session.store') ?: $driver;

        return new CacheBasedSessionHandler(
            clone $this->container->make('cache')->store($store),
            $this->config->get('session.lifetime')
        );
    }

    /**
     * Build the session instance.
     *
     * @param  \SessionHandlerInterface  $handler
     * @return \Illuminate\Session\Store
     */
    protected function buildSession($handler)
    {
        return $this->config->get('session.encrypt')
            ? $this->buildEncryptedSession($handler)
            : new Store(
                $this->config->get('session.cookie'),
                $handler,
                $id = null,
                $this->config->get('session.serialization', 'php')
            );
    }

    /**
     * Build the encrypted session instance.
     *
     * @param  \SessionHandlerInterface  $handler
     * @return \Illuminate\Session\EncryptedStore
     */
    protected function buildEncryptedSession($handler)
    {
        return new EncryptedStore(
            $this->config->get('session.cookie'),
            $handler,
            $this->container['encrypter'],
            $id = null,
            $this->config->get('session.serialization', 'php'),
        );
    }

    /**
     * Determine if requests for the same session should wait for each to finish before executing.
     *
     * @return bool
     */
    public function shouldBlock()
    {
        return $this->config->get('session.block', false);
    }

    /**
     * Get the name of the cache store / driver that should be used to acquire session locks.
     *
     * @return string|null
     */
    public function blockDriver()
    {
        return $this->config->get('session.block_store');
    }

    /**
     * Get the maximum number of seconds the session lock should be held for.
     *
     * @return int
     */
    public function defaultRouteBlockLockSeconds()
    {
        return $this->config->get('session.block_lock_seconds', 10);
    }

    /**
     * Get the maximum number of seconds to wait while attempting to acquire a route block session lock.
     *
     * @return int
     */
    public function defaultRouteBlockWaitSeconds()
    {
        return $this->config->get('session.block_wait_seconds', 10);
    }

    /**
     * Get the session configuration.
     *
     * @return array
     */
    public function getSessionConfig()
    {
        return $this->config->get('session');
    }

    /**
     * Get the default session driver name.
     *
     * @return string|null
     */
    public function getDefaultDriver()
    {
        return $this->config->get('session.driver');
    }

    /**
     * Set the default session driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->config->set('session.driver', $name);
    }
}
