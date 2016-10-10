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
 * RedisMock for simulating Redis extension in tests.
 *
 * @author Andrej Hudec <pulzarraider@gmail.com>
 */
class RedisMock
{
    private $connected = false;
    private $storage = array();

    /**
     * Add a server to connection pool
     *
     * @param string  $host
     * @param int     $port
     * @param float   $timeout
     *
     * @return bool
     */
    public function connect($host, $port = 6379, $timeout = 0)
    {
        if ('127.0.0.1' == $host && 6379 == $port) {
            $this->connected = true;

            return true;
        }

        return false;
    }

    /**
     * Set client option.
     *
     * @param int     $name
     * @param int     $value
     *
     * @return bool
     */
    public function setOption($name, $value)
    {
        if (!$this->connected) {
            return false;
        }

        return true;
    }

    /**
     * Verify if the specified key exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists($key)
    {
        if (!$this->connected) {
            return false;
        }

        return isset($this->storage[$key]);
    }

    /**
     * Store data at the server with expiration time.
     *
     * @param string  $key
     * @param int     $ttl
     * @param mixed   $value
     *
     * @return bool
     */
    public function setex($key, $ttl, $value)
    {
        if (!$this->connected) {
            return false;
        }

        $this->storeData($key, $value);

        return true;
    }

    /**
     * Sets an expiration time on an item.
     *
     * @param string  $key
     * @param int     $ttl
     *
     * @return bool
     */
    public function setTimeout($key, $ttl)
    {
        if (!$this->connected) {
            return false;
        }

        if (isset($this->storage[$key])) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve item from the server.
     *
     * @param string $key
     *
     * @return bool
     */
    public function get($key)
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
     * @return int     Size of the value after the append.
     */
    public function append($key, $value)
    {
        if (!$this->connected) {
            return false;
        }

        if (isset($this->storage[$key])) {
            $this->storeData($key, $this->getData($key).$value);

            return strlen($this->storage[$key]);
        }

        return false;
    }

    /**
     * Remove specified keys.
     *
     * @param string|array $key
     *
     * @return int
     */
    public function delete($key)
    {
        if (!$this->connected) {
            return false;
        }

        if (is_array($key)) {
            $result = 0;
            foreach ($key as $k) {
                if (isset($this->storage[$k])) {
                    unset($this->storage[$k]);
                    ++$result;
                }
            }

            return $result;
        }

        if (isset($this->storage[$key])) {
            unset($this->storage[$key]);

            return 1;
        }

        return 0;
    }

    /**
     * Flush all existing items from all databases at the server.
     *
     * @return bool
     */
    public function flushAll()
    {
        if (!$this->connected) {
            return false;
        }

        $this->storage = array();

        return true;
    }

    /**
     * Close Redis server connection
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

    public function select($dbnum)
    {
        if (!$this->connected) {
            return false;
        }

        if (0 > $dbnum) {
            return false;
        }

        return true;
    }
}
