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
 * Memcache Profiler Storage
 *
 * @author Andrej Hudec <pulzarraider@gmail.com>
 */
class MemcacheProfilerStorage extends BaseMemcacheProfilerStorage
{
    /**
     * @var \Memcache
     */
    private $memcache;

    /**
     * Internal convenience method that returns the instance of the Memcache
     *
     * @return \Memcache
     *
     * @throws \RuntimeException
     */
    protected function getMemcache()
    {
        if (null === $this->memcache) {
            if (!preg_match('#^memcache://(?(?=\[.*\])\[(.*)\]|(.*)):(.*)$#', $this->dsn, $matches)) {
                throw new \RuntimeException(sprintf('Please check your configuration. You are trying to use Memcache with an invalid dsn "%s". The expected format is "memcache://[host]:port".', $this->dsn));
            }

            $host = $matches[1] ?: $matches[2];
            $port = $matches[3];

            $memcache = new \Memcache();
            $memcache->addServer($host, $port);

            $this->memcache = $memcache;
        }

        return $this->memcache;
    }

    /**
     * Set instance of the Memcache
     *
     * @param \Memcache $memcache
     */
    public function setMemcache($memcache)
    {
        $this->memcache = $memcache;
    }

    /**
     * {@inheritdoc}
     */
    protected function getValue($key)
    {
        return $this->getMemcache()->get($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function setValue($key, $value, $expiration = 0)
    {
        return $this->getMemcache()->set($key, $value, false, time() + $expiration);
    }

    /**
     * {@inheritdoc}
     */
    protected function delete($key)
    {
        return $this->getMemcache()->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function appendValue($key, $value, $expiration = 0)
    {
        $memcache = $this->getMemcache();

        if (method_exists($memcache, 'append')) {
            // Memcache v3.0
            if (!$result = $memcache->append($key, $value, false, $expiration)) {
                return $memcache->set($key, $value, false, $expiration);
            }

            return $result;
        }

        // simulate append in Memcache <3.0
        $content = $memcache->get($key);

        return $memcache->set($key, $content.$value, false, $expiration);
    }
}
