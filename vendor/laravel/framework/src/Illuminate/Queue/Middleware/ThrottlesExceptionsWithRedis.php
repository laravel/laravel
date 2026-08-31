<?php

namespace Illuminate\Queue\Middleware;

use Illuminate\Container\Container;
use Illuminate\Contracts\Redis\Connection;
use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Redis\Limiters\DurationLimiter;
use Illuminate\Support\InteractsWithTime;
use Throwable;

class ThrottlesExceptionsWithRedis extends ThrottlesExceptions
{
    use InteractsWithTime;

    /**
     * The Redis connection instance.
     *
     * @var \Illuminate\Contracts\Redis\Connection
     */
    protected $redis;

    /**
     * The Redis connection that should be used.
     *
     * @var string|null
     */
    protected $connectionName = null;

    /**
     * The rate limiter instance.
     *
     * @var \Illuminate\Redis\Limiters\DurationLimiter
     */
    protected $limiter;

    /**
     * Process the job.
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        $this->redis = Container::getInstance()
            ->make(Redis::class)
            ->connection($this->connectionName);

        $this->limiter = new DurationLimiter(
            $this->redis, $this->getKey($job), $this->maxAttempts, $this->decaySeconds
        );

        if ($this->limiter->tooManyAttempts()) {
            return $job->release($this->limiter->decaysAt - $this->currentTime());
        }

        try {
            $next($job);

            $this->limiter->clear();
        } catch (Throwable $throwable) {
            if ($this->whenCallback && ! call_user_func($this->whenCallback, $throwable, $this->limiter)) {
                throw $throwable;
            }

            if ($this->reportCallback && call_user_func($this->reportCallback, $throwable, $this->limiter)) {
                report($throwable);
            }

            if ($this->shouldDelete($throwable)) {
                return $job->delete();
            }

            if ($this->shouldFail($throwable)) {
                return $job->fail($throwable);
            }

            $this->limiter->acquire();

            return $job->release($this->retryAfterMinutes * 60);
        }
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
}
