<?php

namespace Illuminate\Redis\Limiters;

use Illuminate\Contracts\Redis\LimiterTimeoutException;
use Illuminate\Support\Sleep;

class DurationLimiter
{
    /**
     * The Redis factory implementation.
     *
     * @var \Illuminate\Redis\Connections\Connection
     */
    private $redis;

    /**
     * The unique name of the lock.
     *
     * @var string
     */
    private $name;

    /**
     * The allowed number of concurrent tasks.
     *
     * @var int
     */
    private $maxLocks;

    /**
     * The number of seconds a slot should be maintained.
     *
     * @var int
     */
    private $decay;

    /**
     * The timestamp of the end of the current duration.
     *
     * @var int
     */
    public $decaysAt;

    /**
     * The number of remaining slots.
     *
     * @var int
     */
    public $remaining;

    /**
     * Create a new duration limiter instance.
     *
     * @param  \Illuminate\Redis\Connections\Connection  $redis
     * @param  string  $name
     * @param  int  $maxLocks
     * @param  int  $decay
     */
    public function __construct($redis, $name, $maxLocks, $decay)
    {
        $this->name = $name;
        $this->decay = $decay;
        $this->redis = $redis;
        $this->maxLocks = $maxLocks;
    }

    /**
     * Attempt to acquire the lock for the given number of seconds.
     *
     * @param  int  $timeout
     * @param  callable|null  $callback
     * @param  int  $sleep
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Redis\LimiterTimeoutException
     */
    public function block($timeout, $callback = null, $sleep = 750)
    {
        $starting = time();

        while (! $this->acquire()) {
            if (time() - $timeout >= $starting) {
                throw new LimiterTimeoutException;
            }

            Sleep::usleep($sleep * 1000);
        }

        if (is_callable($callback)) {
            return $callback();
        }

        return true;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire()
    {
        $results = $this->redis->eval(
            $this->luaScript(), 1, $this->name, microtime(true), time(), $this->decay, $this->maxLocks
        );

        $this->decaysAt = $results[1];

        $this->remaining = max(0, $results[2]);

        return (bool) $results[0];
    }

    /**
     * Determine if the key has been "accessed" too many times.
     *
     * @return bool
     */
    public function tooManyAttempts()
    {
        [$this->decaysAt, $this->remaining] = $this->redis->eval(
            $this->tooManyAttemptsLuaScript(), 1, $this->name, microtime(true), time(), $this->decay, $this->maxLocks
        );

        return $this->remaining <= 0;
    }

    /**
     * Clear the limiter.
     *
     * @return void
     */
    public function clear()
    {
        $this->redis->del($this->name);
    }

    /**
     * Get the Lua script for acquiring a lock.
     *
     * KEYS[1] - The limiter name
     * ARGV[1] - Current time in microseconds
     * ARGV[2] - Current time in seconds
     * ARGV[3] - Duration of the bucket
     * ARGV[4] - Allowed number of tasks
     *
     * @return string
     */
    protected function luaScript()
    {
        return <<<'LUA'
local function reset()
    redis.call('HMSET', KEYS[1], 'start', ARGV[2], 'end', ARGV[2] + ARGV[3], 'count', 1)
    return redis.call('EXPIRE', KEYS[1], ARGV[3] * 2)
end

if redis.call('EXISTS', KEYS[1]) == 0 then
    return {reset(), ARGV[2] + ARGV[3], ARGV[4] - 1}
end

if ARGV[1] >= redis.call('HGET', KEYS[1], 'start') and ARGV[1] <= redis.call('HGET', KEYS[1], 'end') then
    return {
        tonumber(redis.call('HINCRBY', KEYS[1], 'count', 1)) <= tonumber(ARGV[4]),
        redis.call('HGET', KEYS[1], 'end'),
        ARGV[4] - redis.call('HGET', KEYS[1], 'count')
    }
end

return {reset(), ARGV[2] + ARGV[3], ARGV[4] - 1}
LUA;
    }

    /**
     * Get the Lua script to determine if the key has been "accessed" too many times.
     *
     * KEYS[1] - The limiter name
     * ARGV[1] - Current time in microseconds
     * ARGV[2] - Current time in seconds
     * ARGV[3] - Duration of the bucket
     * ARGV[4] - Allowed number of tasks
     *
     * @return string
     */
    protected function tooManyAttemptsLuaScript()
    {
        return <<<'LUA'

if redis.call('EXISTS', KEYS[1]) == 0 then
    return {0, ARGV[2] + ARGV[3]}
end

if ARGV[1] >= redis.call('HGET', KEYS[1], 'start') and ARGV[1] <= redis.call('HGET', KEYS[1], 'end') then
    return {
        redis.call('HGET', KEYS[1], 'end'),
        ARGV[4] - redis.call('HGET', KEYS[1], 'count')
    }
end

return {0, ARGV[2] + ARGV[3]}
LUA;
    }
}
