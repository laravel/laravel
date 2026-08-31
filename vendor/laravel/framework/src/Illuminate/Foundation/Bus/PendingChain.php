<?php

namespace Illuminate\Foundation\Bus;

use Closure;
use Illuminate\Bus\ChainedBatch;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use Laravel\SerializableClosure\SerializableClosure;

use function Illuminate\Support\enum_value;

class PendingChain
{
    use Conditionable;

    /**
     * The class name of the job being dispatched.
     *
     * @var mixed
     */
    public $job;

    /**
     * The jobs to be chained.
     *
     * @var array
     */
    public $chain;

    /**
     * The name of the connection the chain should be sent to.
     *
     * @var string|null
     */
    public $connection;

    /**
     * The name of the queue the chain should be sent to.
     *
     * @var string|null
     */
    public $queue;

    /**
     * The number of seconds before the chain should be made available.
     *
     * @var \DateTimeInterface|\DateInterval|int|null
     */
    public $delay;

    /**
     * The callbacks to be executed on failure.
     *
     * @var array
     */
    public $catchCallbacks = [];

    /**
     * Create a new PendingChain instance.
     *
     * @param  mixed  $job
     * @param  array  $chain
     */
    public function __construct($job, $chain)
    {
        $this->job = $job;
        $this->chain = $chain;
    }

    /**
     * Set the desired connection for the job.
     *
     * @param  \UnitEnum|string|null  $connection
     * @return $this
     */
    public function onConnection($connection)
    {
        $this->connection = enum_value($connection);

        return $this;
    }

    /**
     * Set the desired queue for the job.
     *
     * @param  \UnitEnum|string|null  $queue
     * @return $this
     */
    public function onQueue($queue)
    {
        $this->queue = enum_value($queue);

        return $this;
    }

    /**
     * Prepend a job to the chain.
     *
     * @param  mixed  $job
     * @return $this
     */
    public function prepend($job)
    {
        $jobs = ChainedBatch::prepareNestedBatches(
            Collection::wrap($job)
        );

        if ($this->job) {
            array_unshift($this->chain, $this->job);
        }

        $this->job = $jobs->shift();

        array_unshift($this->chain, ...$jobs->toArray());

        return $this;
    }

    /**
     * Append a job to the chain.
     *
     * @param  mixed  $job
     * @return $this
     */
    public function append($job)
    {
        $jobs = ChainedBatch::prepareNestedBatches(
            Collection::wrap($job)
        );

        if (! $this->job) {
            $this->job = $jobs->shift();
        }

        array_push($this->chain, ...$jobs->toArray());

        return $this;
    }

    /**
     * Set the desired delay in seconds for the chain.
     *
     * @param  \DateTimeInterface|\DateInterval|int|null  $delay
     * @return $this
     */
    public function delay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Add a callback to be executed on job failure.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function catch($callback)
    {
        $this->catchCallbacks[] = $callback instanceof Closure
            ? new SerializableClosure($callback)
            : $callback;

        return $this;
    }

    /**
     * Get the "catch" callbacks that have been registered.
     *
     * @return array
     */
    public function catchCallbacks()
    {
        return $this->catchCallbacks ?? [];
    }

    /**
     * Dispatch the job chain.
     *
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function dispatch()
    {
        if (is_string($this->job)) {
            $firstJob = new $this->job(...func_get_args());
        } elseif ($this->job instanceof Closure) {
            $firstJob = CallQueuedClosure::create($this->job);
        } else {
            $firstJob = $this->job;
        }

        if ($this->connection) {
            $firstJob->chainConnection = $this->connection;
            $firstJob->connection = $firstJob->connection ?: $this->connection;
        }

        if ($this->queue) {
            $firstJob->chainQueue = $this->queue;
            $firstJob->queue = $firstJob->queue ?: $this->queue;
        }

        if ($this->delay) {
            $firstJob->delay = ! is_null($firstJob->delay) ? $firstJob->delay : $this->delay;
        }

        $firstJob->chain($this->chain);
        $firstJob->chainCatchCallbacks = $this->catchCallbacks();

        return app(Dispatcher::class)->dispatch($firstJob);
    }

    /**
     * Dispatch the job chain if the given truth test passes.
     *
     * @param  bool|\Closure  $boolean
     * @return \Illuminate\Foundation\Bus\PendingDispatch|null
     */
    public function dispatchIf($boolean)
    {
        return value($boolean) ? $this->dispatch() : null;
    }

    /**
     * Dispatch the job chain unless the given truth test passes.
     *
     * @param  bool|\Closure  $boolean
     * @return \Illuminate\Foundation\Bus\PendingDispatch|null
     */
    public function dispatchUnless($boolean)
    {
        return ! value($boolean) ? $this->dispatch() : null;
    }
}
