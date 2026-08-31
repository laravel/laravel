<?php

namespace Illuminate\Cache;

use Memcached;

class MemcachedConnector
{
    /**
     * Create a new Memcached connection.
     *
     * @param  array  $servers
     * @param  string|null  $connectionId
     * @param  array  $options
     * @param  array  $credentials
     * @return \Memcached
     */
    public function connect(array $servers, $connectionId = null, array $options = [], array $credentials = [])
    {
        $memcached = $this->getMemcached(
            $connectionId, $credentials, $options
        );

        if (! $memcached->getServerList()) {
            // For each server in the array, we'll just extract the configuration and add
            // the server to the Memcached connection. Once we have added all of these
            // servers we'll verify the connection is successful and return it back.
            foreach ($servers as $server) {
                $memcached->addServer(
                    $server['host'], $server['port'], $server['weight']
                );
            }
        }

        return $memcached;
    }

    /**
     * Get a new Memcached instance.
     *
     * @param  string|null  $connectionId
     * @param  array  $credentials
     * @param  array  $options
     * @return \Memcached
     */
    protected function getMemcached($connectionId, array $credentials, array $options)
    {
        $memcached = $this->createMemcachedInstance($connectionId);

        if (count($credentials) === 2) {
            $this->setCredentials($memcached, $credentials);
        }

        if (count($options)) {
            $memcached->setOptions($options);
        }

        return $memcached;
    }

    /**
     * Create the Memcached instance.
     *
     * @param  string|null  $connectionId
     * @return \Memcached
     */
    protected function createMemcachedInstance($connectionId)
    {
        return empty($connectionId) ? new Memcached : new Memcached($connectionId);
    }

    /**
     * Set the SASL credentials on the Memcached connection.
     *
     * @param  \Memcached  $memcached
     * @param  array  $credentials
     * @return void
     */
    protected function setCredentials($memcached, $credentials)
    {
        [$username, $password] = $credentials;

        $memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);

        $memcached->setSaslAuthData($username, $password);
    }
}
