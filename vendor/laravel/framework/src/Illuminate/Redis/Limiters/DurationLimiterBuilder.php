<?php

namespace Illuminate\Redis\Limiters;

use Illuminate\Contracts\Redis\LimiterTimeoutException;
use Illuminate\Support\InteractsWithTime;

class DurationLimiterBuilder
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
     * The maximum number of locks that can be obtained per time window.
     *
     * @var int
     */
    public $maxLocks;

    /**
     * The amount of time the lock window is maintained.
     *
     * @var int
     */
    public $decay;

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
    public $sleep = 750;

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
    public function allow($maxLocks)
    {
        $this->maxLocks = $maxLocks;

        return $this;
    }

    /**
     * Set the amount of time the lock window is maintained.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $decay
     * @return $this
     */
    public function every($decay)
    {
        $this->decay = $this->secondsUntil($decay);

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
            return (new DurationLimiter(
                $this->connection, $this->name, $this->maxLocks, $this->decay
            ))->block($this->timeout, $callback, $this->sleep);
        } catch (LimiterTimeoutException $e) {
            if ($failure) {
                return $failure($e);
            }

            throw $e;
        }
    }
}
