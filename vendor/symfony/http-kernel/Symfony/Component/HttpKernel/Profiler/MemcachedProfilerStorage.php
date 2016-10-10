<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Profiler;

/**
 * Memcached Profiler Storage
 *
 * @author Andrej Hudec <pulzarraider@gmail.com>
 */
class MemcachedProfilerStorage extends BaseMemcacheProfilerStorage
{
    /**
     * @var \Memcached
     */
    private $memcached;

    /**
     * Internal convenience method that returns the instance of the Memcached
     *
     * @return \Memcached
     *
     * @throws \RuntimeException
     */
    protected function getMemcached()
    {
        if (null === $this->memcached) {
            if (!preg_match('#^memcached://(?(?=\[.*\])\[(.*)\]|(.*)):(.*)$#', $this->dsn, $matches)) {
                throw new \RuntimeException(sprintf('Please check your configuration. You are trying to use Memcached with an invalid dsn "%s". The expected format is "memcached://[host]:port".', $this->dsn));
            }

            $host = $matches[1] ?: $matches[2];
            $port = $matches[3];

            $memcached = new \Memcached();

            // disable compression to allow appending
            $memcached->setOption(\Memcached::OPT_COMPRESSION, false);

            $memcached->addServer($host, $port);

            $this->memcached = $memcached;
        }

        return $this->memcached;
    }

    /**
     * Set instance of the Memcached
     *
     * @param \Memcached $memcached
     */
    public function setMemcached($memcached)
    {
        $this->memcached = $memcached;
    }

    /**
     * {@inheritdoc}
     */
    protected function getValue($key)
    {
        return $this->getMemcached()->get($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function setValue($key, $value, $expiration = 0)
    {
        return $this->getMemcached()->set($key, $value, time() + $expiration);
    }

    /**
     * {@inheritdoc}
     */
    protected function delete($key)
    {
        return $this->getMemcached()->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function appendValue($key, $value, $expiration = 0)
    {
        $memcached = $this->getMemcached();

        if (!$result = $memcached->append($key, $value)) {
            return $memcached->set($key, $value, $expiration);
        }

        return $result;
    }
}
