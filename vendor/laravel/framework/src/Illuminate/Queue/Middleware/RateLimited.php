<?php

namespace Illuminate\Queue\Middleware;

use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Unlimited;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;

use function Illuminate\Support\enum_value;

class RateLimited
{
    /**
     * The rate limiter instance.
     *
     * @var \Illuminate\Cache\RateLimiter
     */
    protected $limiter;

    /**
     * The name of the rate limiter.
     *
     * @var string
     */
    protected $limiterName;

    /**
     * The number of seconds before a job should be available again if the limit is exceeded.
     *
     * @var \DateTimeInterface|int|null
     */
    public $releaseAfter;

    /**
     * Indicates if the job should be released if the limit is exceeded.
     *
     * @var bool
     */
    public $shouldRelease = true;

    /**
     * Create a new middleware instance.
     *
     * @param  \UnitEnum|string  $limiterName
     */
    public function __construct($limiterName)
    {
        $this->limiter = Container::getInstance()->make(RateLimiter::class);

        $this->limiterName = (string) enum_value($limiterName);
    }

    /**
     * Process the job.
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        if (is_null($limiter = $this->limiter->limiter($this->limiterName))) {
            return $next($job);
        }

        $limiterResponse = $limiter($job);

        if ($limiterResponse instanceof Unlimited) {
            return $next($job);
        }

        return $this->handleJob(
            $job,
            $next,
            Collection::wrap($limiterResponse)->map(function ($limit) {
                return (object) [
                    'key' => md5($this->limiterName.$limit->key),
                    'maxAttempts' => $limit->maxAttempts,
                    'decaySeconds' => $limit->decaySeconds,
                ];
            })->all()
        );
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
            if ($this->limiter->tooManyAttempts($limit->key, $limit->maxAttempts)) {
                return $this->shouldRelease
                    ? $job->release($this->releaseAfter ?: $this->getTimeUntilNextRetry($limit->key))
                    : false;
            }

            $this->limiter->hit($limit->key, $limit->decaySeconds);
        }

        return $next($job);
    }

    /**
     * Set the delay (in seconds) to release the job back to the queue.
     *
     * @param  \DateTimeInterface|int  $releaseAfter
     * @return $this
     */
    public function releaseAfter($releaseAfter)
    {
        $this->releaseAfter = $releaseAfter;

        return $this;
    }

    /**
     * Do not release the job back to the queue if the limit is exceeded.
     *
     * @return $this
     */
    public function dontRelease()
    {
        $this->shouldRelease = false;

        return $this;
    }

    /**
     * Get the number of seconds that should elapse before the job is retried.
     *
     * @param  string  $key
     * @return int
     */
    protected function getTimeUntilNextRetry($key)
    {
        return $this->limiter->availableIn($key) + 3;
    }

    /**
     * Prepare the object for serialization.
     *
     * @return array
     */
    public function __sleep()
    {
        return [
            'limiterName',
            'shouldRelease',
        ];
    }

    /**
     * Prepare the object after unserialization.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->limiter = Container::getInstance()->make(RateLimiter::class);
    }
}
