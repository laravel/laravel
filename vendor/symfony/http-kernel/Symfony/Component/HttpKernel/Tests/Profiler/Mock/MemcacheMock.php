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
 * MemcacheMock for simulating Memcache extension in tests.
 *
 * @author Andrej Hudec <pulzarraider@gmail.com>
 */
class MemcacheMock
{
    private $connected = false;
    private $storage = array();

    /**
     * Open memcached server connection
     *
     * @param string  $host
     * @param int     $port
     * @param int     $timeout
     *
     * @return bool
     */
    public function connect($host, $port = null, $timeout = null)
    {
        if ('127.0.0.1' == $host && 11211 == $port) {
            $this->connected = true;

            return true;
        }

        return false;
    }

    /**
     * Open memcached server persistent connection
     *
     * @param string  $host
     * @param int     $port
     * @param int     $timeout
     *
     * @return bool
     */
    public function pconnect($host, $port = null, $timeout = null)
    {
        if ('127.0.0.1' == $host && 11211 == $port) {
            $this->connected = true;

            return true;
        }

        return false;
    }

    /**
     * Add a memcached server to connection pool
     *
     * @param string   $host
     * @param int      $port
     * @param bool     $persistent
     * @param int      $weight
     * @param int      $timeout
     * @param int      $retry_interval
     * @param bool     $status
     * @param callable $failure_callback
     * @param int      $timeoutms
     *
     * @return bool
     */
    public function addServer($host, $port = 11211, $persistent = null, $weight = null, $timeout = null, $retry_interval = null, $status = null, $failure_callback = null, $timeoutms = null)
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
     * @param mixed   $var
     * @param int     $flag
     * @param int     $expire
     *
     * @return bool
     */
    public function add($key, $var, $flag = null, $expire = null)
    {
        if (!$this->connected) {
            return false;
        }

        if (!isset($this->storage[$key])) {
            $this->storeData($key, $var);

            return true;
        }

        return false;
    }

    /**
     * Store data at the server.
     *
     * @param string  $key
     * @param string  $var
     * @param int     $flag
     * @param int     $expire
     *
     * @return bool
     */
    public function set($key, $var, $flag = null, $expire = null)
    {
        if (!$this->connected) {
            return false;
        }

        $this->storeData($key, $var);

        return true;
    }

    /**
     * Replace value of the existing item.
     *
     * @param string  $key
     * @param mixed   $var
     * @param int     $flag
     * @param int     $expire
     *
     * @return bool
     */
    public function replace($key, $var, $flag = null, $expire = null)
    {
        if (!$this->connected) {
            return false;
        }

        if (isset($this->storage[$key])) {
            $this->storeData($key, $var);

            return true;
        }

        return false;
    }

    /**
     * Retrieve item from the server.
     *
     * @param string|array  $key
     * @param int|array     $flags
     *
     * @return mixed
     */
    public function get($key, &$flags = null)
    {
        if (!$this->connected) {
            return false;
        }

        if (is_array($key)) {
            $result = array();
            foreach ($key as $k) {
                if (isset($this->storage[$k])) {
                    $result[] = $this->getData($k);
                }
            }

            return $result;
        }

        return $this->getData($key);
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

    /**
     * Close memcached server connection
     *
     * @return bool
     */
    public function close()
    {
        $this->connected = false;

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
