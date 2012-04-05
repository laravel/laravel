<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

/**
 * MemcachedSessionHandler.
 *
 * Memcached based session storage handler based on the Memcached class
 * provided by the PHP memcached extension.
 *
 * @see http://php.net/memcached
 *
 * @author Drak <drak@zikula.org>
 */
class MemcachedSessionHandler implements \SessionHandlerInterface
{
    /**
     * Memcached driver.
     *
     * @var \Memcached
     */
    private $memcached;

    /**
     * Configuration options.
     *
     * @var array
     */
    private $memcachedOptions;

    /**
     * Constructor.
     *
     * @param \Memcached $memcached        A \Memcached instance
     * @param array      $memcachedOptions An associative array of Memcached options
     * @param array      $options          Session configuration options.
     */
    public function __construct(\Memcached $memcached, array $memcachedOptions = array(), array $options = array())
    {
        $this->memcached = $memcached;

        // defaults
        if (!isset($memcachedOptions['serverpool'])) {
            $memcachedOptions['serverpool'][] = array(
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 1);
        }

        $memcachedOptions['expiretime'] = isset($memcachedOptions['expiretime']) ? (int)$memcachedOptions['expiretime'] : 86400;

        $this->memcached->setOption(\Memcached::OPT_PREFIX_KEY, isset($memcachedOptions['prefix']) ? $memcachedOptions['prefix'] : 'sf2s');

        $this->memcachedOptions = $memcachedOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName)
    {
        return $this->memcached->addServers($this->memcachedOptions['serverpool']);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        return $this->memcached->get($sessionId) ?: '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data)
    {
        return $this->memcached->set($sessionId, $data, $this->memcachedOptions['expiretime']);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        return $this->memcached->delete($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function gc($lifetime)
    {
        // not required here because memcached will auto expire the records anyhow.
        return true;
    }

    /**
     * Adds a server to the memcached handler.
     *
     * @param array $server
     */
    protected function addServer(array $server)
    {
        if (array_key_exists('host', $server)) {
            throw new \InvalidArgumentException('host key must be set');
        }
        $server['port'] = isset($server['port']) ? (int)$server['port'] : 11211;
        $server['timeout'] = isset($server['timeout']) ? (int)$server['timeout'] : 1;
        $server['presistent'] = isset($server['presistent']) ? (bool)$server['presistent'] : false;
        $server['weight'] = isset($server['weight']) ? (bool)$server['weight'] : 1;
    }
}
