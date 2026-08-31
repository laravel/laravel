<?php

namespace Illuminate\Queue\Middleware;

use Closure;
use Throwable;

class FailOnException
{
    /**
     * The truth-test callback to determine if the job should fail.
     *
     * @var \Closure(\Throwable, mixed): bool
     */
    protected Closure $callback;

    /**
     * Create a middleware instance.
     *
     * @param  (\Closure(\Throwable, mixed): bool)|array<array-key, class-string<\Throwable>>  $callback
     */
    public function __construct($callback)
    {
        if (is_array($callback)) {
            $callback = $this->failForExceptions($callback);
        }

        $this->callback = $callback;
    }

    /**
     * Indicate that the job should fail if it encounters the given exceptions.
     *
     * @param  array<array-key, class-string<\Throwable>>  $exceptions
     * @return \Closure(\Throwable, mixed): bool
     */
    protected function failForExceptions(array $exceptions)
    {
        return static function (Throwable $throwable) use ($exceptions) {
            foreach ($exceptions as $exception) {
                if ($throwable instanceof $exception) {
                    return true;
                }
            }

            return false;
        };
    }

    /**
     * Mark the job as failed if an exception is thrown that passes a truth-test callback.
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     *
     * @throws \Throwable
     */
    public function handle($job, callable $next)
    {
        try {
            return $next($job);
        } catch (Throwable $e) {
            if (call_user_func($this->callback, $e, $job) === true) {
                $job->fail($e);
            }

            throw $e;
        }
    }
}
