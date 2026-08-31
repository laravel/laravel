<?php

namespace Illuminate\Queue\Middleware;

use Illuminate\Container\Container;
use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Redis\Limiters\DurationLimiter;
use Illuminate\Support\InteractsWithTime;

class RateLimitedWithRedis extends RateLimited
{
    use InteractsWithTime;

    /**
     * The name of the Redis connection that should be used.
     *
     * @var string|null
     */
    protected $connectionName = null;

    /**
     * The timestamp of the end of the current duration by key.
     *
     * @var array
     */
    public $decaysAt = [];

    /**
     * Create a new middleware instance.
     *
     * @param  string  $limiterName
     */
    public function __construct($limiterName, ?string $connection = null)
    {
        parent::__construct($limiterName);

        $this->connectionName = $connection;
    }

    /**
     * Handle a rate limited job.
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @param  array  $limits
     * @return mixed
     */
    protected function handleJob($job, $next, array $limits)
    {
        foreach ($limits as $limit) {
            if ($this->tooManyAttempts($limit->key, $limit->maxAttempts, $limit->decaySeconds)) {
                return $this->shouldRelease
                    ? $job->release($this->releaseAfter ?: $this->getTimeUntilNextRetry($limit->key))
                    : false;
            }
        }

        return $next($job);
    }

    /**
     * Determine if the given key has been "accessed" too many times.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @param  int  $decaySeconds
     * @return bool
     */
    protected function tooManyAttempts($key, $maxAttempts, $decaySeconds)
    {
        $redis = Container::getInstance()
            ->make(Redis::class)
            ->connection($this->connectionName);

        $limiter = new DurationLimiter(
            $redis, $key, $maxAttempts, $decaySeconds
        );

        return tap(! $limiter->acquire(), function () use ($key, $limiter) {
            $this->decaysAt[$key] = $limiter->decaysAt;
        });
    }

    /**
     * Get the number of seconds that should elapse before the job is retried.
     *
     * @param  string  $key
     * @return int
     */
    protected function getTimeUntilNextRetry($key)
    {
        return ($this->decaysAt[$key] - $this->currentTime()) + 3;
    }

    /**
     * Specify the Redis connection that should be used.
     *
     * @param  string  $name
     * @return $this
     */
    public function connection(string $name)
    {
        $this->connectionName = $name;

        return $this;
    }

    /**
     * Prepare the object for serialization.
     *
     * @return array
     */
    public function __sleep()
    {
        return array_merge(parent::__sleep(), ['connectionName']);
    }

    /**
     * Prepare the object after unserialization.
     *
     * @return void
     */
    public function __wakeup()
    {
        parent::__wakeup();
    }
}
