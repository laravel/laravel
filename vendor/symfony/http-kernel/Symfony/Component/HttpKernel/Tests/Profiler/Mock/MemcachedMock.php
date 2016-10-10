<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\Profiler\Mock;

/**
 * MemcachedMock for simulating Memcached extension in tests.
 *
 * @author Andrej Hudec <pulzarraider@gmail.com>
 */
class MemcachedMock
{
    private $connected = false;
    private $storage = array();

    /**
     * Set a Memcached option
     *
     * @param int     $option
     * @param mixed   $value
     *
     * @return bool
     */
    public function setOption($option, $value)
    {
        return true;
    }

    /**
     * Add a memcached server to connection pool
     *
     * @param string  $host
     * @param int     $port
     * @param int     $weight
     *
     * @return bool
     */
    public function addServer($host, $port = 11211, $weight = 0)
    {
        if ('127.0.0.1' == $host && 11211 == $port) {
            $this->connected = true;

            return true;
        }

        return false;
    }

    /**
     * Add an item to the server only if such key doesn't exist at the server yet.
     *
     * @param string  $key
     * @param mixed   $value
     * @param int     $expiration
     *
     * @return bool
     */
    public function add($key, $value, $expiration = 0)
    {
        if (!$this->connected) {
            return false;
        }

        if (!isset($this->storage[$key])) {
            $this->storeData($key, $value);

            return true;
        }

        return false;
    }

    /**
     * Store data at the server.
     *
     * @param string  $key
     * @param mixed   $value
     * @param int     $expiration
     *
     * @return bool
     */
    public function set($key, $value, $expiration = null)
    {
        if (!$this->connected) {
            return false;
        }

        $this->storeData($key, $value);

        return true;
    }

    /**
     * Replace value of the existing item.
     *
     * @param string  $key
     * @param mixed   $value
     * @param int     $expiration
     *
     * @return bool
     */
    public function replace($key, $value, $expiration = null)
    {
        if (!$this->connected) {
            return false;
        }

        if (isset($this->storage[$key])) {
            $this->storeData($key, $value);

            return true;
        }

        return false;
    }

    /**
     * Retrieve item from the server.
     *
     * @param string   $key
     * @param callable $cache_cb
     * @param float    $cas_token
     *
     * @return bool
     */
    public function get($key, $cache_cb = null, &$cas_token = null)
    {
        if (!$this->connected) {
            return false;
        }

        return $this->getData($key);
    }

    /**
     * Append data to an existing item
     *
     * @param string $key
     * @param string $value
     *
     * @return bool
     */
    public function append($key, $value)
    {
        if (!$this->connected) {
            return false;
        }

        if (isset($this->storage[$key])) {
            $this->storeData($key, $this->getData($key).$value);

            return true;
        }

        return false;
    }

    /**
     * Delete item from the server
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        if (!$this->connected) {
            return false;
        }

        if (isset($this->storage[$key])) {
            unset($this->storage[$key]);

            return true;
        }

        return false;
    }

    /**
     * Flush all existing items at the server
     *
     * @return bool
     */
    public function flush()
    {
        if (!$this->connected) {
            return false;
        }

        $this->storage = array();

        return true;
    }

    private function getData($key)
    {
        if (isset($this->storage[$key])) {
            return unserialize($this->storage[$key]);
        }

        return false;
    }

    private function storeData($key, $value)
    {
        $this->storage[$key] = serialize($value);

        return true;
    }
}
