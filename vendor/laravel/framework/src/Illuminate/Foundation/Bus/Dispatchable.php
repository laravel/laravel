<?php

namespace Illuminate\Foundation\Bus;

use Closure;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Fluent;

trait Dispatchable
{
    /**
     * Dispatch the job with the given arguments.
     *
     * @param  mixed  ...$arguments
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public static function dispatch(...$arguments)
    {
        return static::newPendingDispatch(new static(...$arguments));
    }

    /**
     * Dispatch the job with the given arguments if the given truth test passes.
     *
     * @param  bool|\Closure  $boolean
     * @param  mixed  ...$arguments
     * @return \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent
     */
    public static function dispatchIf($boolean, ...$arguments)
    {
        if ($boolean instanceof Closure) {
            $dispatchable = new static(...$arguments);

            return value($boolean, $dispatchable)
                ? static::newPendingDispatch($dispatchable)
                : new Fluent;
        }

        return value($boolean)
            ? static::newPendingDispatch(new static(...$arguments))
            : new Fluent;
    }

    /**
     * Dispatch the job with the given arguments unless the given truth test passes.
     *
     * @param  bool|\Closure  $boolean
     * @param  mixed  ...$arguments
     * @return \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent
     */
    public static function dispatchUnless($boolean, ...$arguments)
    {
        if ($boolean instanceof Closure) {
            $dispatchable = new static(...$arguments);

            return ! value($boolean, $dispatchable)
                ? static::newPendingDispatch($dispatchable)
                : new Fluent;
        }

        return ! value($boolean)
            ? static::newPendingDispatch(new static(...$arguments))
            : new Fluent;
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * Queueable jobs will be dispatched to the "sync" queue.
     *
     * @param  mixed  ...$arguments
     * @return mixed
     */
    public static function dispatchSync(...$arguments)
    {
        return app(Dispatcher::class)->dispatchSync(new static(...$arguments));
    }

    /**
     * Dispatch a command to its appropriate handler after the current process.
     *
     * @param  mixed  ...$arguments
     * @return mixed
     */
    public static function dispatchAfterResponse(...$arguments)
    {
        return self::dispatch(...$arguments)->afterResponse();
    }

    /**
     * Set the jobs that should run if this job is successful.
     *
     * @param  array  $chain
     * @return \Illuminate\Foundation\Bus\PendingChain
     */
    public static function withChain($chain)
    {
        return new PendingChain(static::class, $chain);
    }

    /**
     * Create a new pending job dispatch instance.
     *
     * @param  mixed  $job
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    protected static function newPendingDispatch($job)
    {
        return new PendingDispatch($job);
    }
}
