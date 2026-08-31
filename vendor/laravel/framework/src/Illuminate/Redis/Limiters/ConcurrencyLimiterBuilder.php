<?php

namespace Illuminate\Redis\Limiters;

use Illuminate\Contracts\Redis\LimiterTimeoutException;
use Illuminate\Support\InteractsWithTime;

class ConcurrencyLimiterBuilder
{
    use InteractsWithTime;

    /**
     * The Redis connection.
     *
     * @var \Illuminate\Redis\Connections\Connection
     */
    public $connection;

    /**
     * The name of the lock.
     *
     * @var string
     */
    public $name;

    /**
     * The maximum number of entities that can hold the lock at the same time.
     *
     * @var int
     */
    public $maxLocks;

    /**
     * The number of seconds to maintain the lock until it is automatically released.
     *
     * @var int
     */
    public $releaseAfter = 60;

    /**
     * The amount of time to block until a lock is available.
     *
     * @var int
     */
    public $timeout = 3;

    /**
     * The number of milliseconds to wait between attempts to acquire the lock.
     *
     * @var int
     */
    public $sleep = 250;

    /**
     * Create a new builder instance.
     *
     * @param  \Illuminate\Redis\Connections\Connection  $connection
     * @param  string  $name
     */
    public function __construct($connection, $name)
    {
        $this->name = $name;
        $this->connection = $connection;
    }

    /**
     * Set the maximum number of locks that can be obtained per time window.
     *
     * @param  int  $maxLocks
     * @return $this
     */
    public function limit($maxLocks)
    {
        $this->maxLocks = $maxLocks;

        return $this;
    }

    /**
     * Set the number of seconds until the lock will be released.
     *
     * @param  int  $releaseAfter
     * @return $this
     */
    public function releaseAfter($releaseAfter)
    {
        $this->releaseAfter = $this->secondsUntil($releaseAfter);

        return $this;
    }

    /**
     * Set the amount of time to block until a lock is available.
     *
     * @param  int  $timeout
     * @return $this
     */
    public function block($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * The number of milliseconds to wait between lock acquisition attempts.
     *
     * @param  int  $sleep
     * @return $this
     */
    public function sleep($sleep)
    {
        $this->sleep = $sleep;

        return $this;
    }

    /**
     * Execute the given callback if a lock is obtained, otherwise call the failure callback.
     *
     * @param  callable  $callback
     * @param  callable|null  $failure
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Redis\LimiterTimeoutException
     */
    public function then(callable $callback, ?callable $failure = null)
    {
        try {
            return (new ConcurrencyLimiter(
                $this->connection, $this->name, $this->maxLocks, $this->releaseAfter
            ))->block($this->timeout, $callback, $this->sleep);
        } catch (LimiterTimeoutException $e) {
            if ($failure) {
                return $failure($e);
            }

            throw $e;
        }
    }
}
