<?php

namespace Illuminate\Bus;

use Closure;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\SerializableClosure\SerializableClosure;
use PHPUnit\Framework\Assert as PHPUnit;
use RuntimeException;

use function Illuminate\Support\enum_value;

trait Queueable
{
    /**
     * The name of the connection the job should be sent to.
     *
     * @var string|null
     */
    public $connection;

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue;

    /**
     * The job "group" the job should be sent to.
     *
     * @var string|null
     */
    public $messageGroup;

    /**
     * The job deduplicator callback the job should use to generate the deduplication ID.
     *
     * @var \Laravel\SerializableClosure\SerializableClosure|null
     */
    public $deduplicator;

    /**
     * The number of seconds before the job should be made available.
     *
     * @var \DateTimeInterface|\DateInterval|array|int|null
     */
    public $delay;

    /**
     * Indicates whether the job should be dispatched after all database transactions have committed.
     *
     * @var bool|null
     */
    public $afterCommit;

    /**
     * The middleware the job should be dispatched through.
     *
     * @var array
     */
    public $middleware = [];

    /**
     * The jobs that should run if this job is successful.
     *
     * @var array
     */
    public $chained = [];

    /**
     * The name of the connection the chain should be sent to.
     *
     * @var string|null
     */
    public $chainConnection;

    /**
     * The name of the queue the chain should be sent to.
     *
     * @var string|null
     */
    public $chainQueue;

    /**
     * The callbacks to be executed on chain failure.
     *
     * @var array|null
     */
    public $chainCatchCallbacks;

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
     * Set the desired job "group".
     *
     * This feature is only supported by some queues, such as Amazon SQS.
     *
     * @param  \UnitEnum|string  $group
     * @return $this
     */
    public function onGroup($group)
    {
        $this->messageGroup = enum_value($group);

        return $this;
    }

    /**
     * Set the desired job deduplicator callback.
     *
     * This feature is only supported by some queues, such as Amazon SQS FIFO.
     *
     * @param  callable|null  $deduplicator
     * @return $this
     */
    public function withDeduplicator($deduplicator)
    {
        $this->deduplicator = $deduplicator instanceof Closure
            ? new SerializableClosure($deduplicator)
            : $deduplicator;

        return $this;
    }

    /**
     * Set the desired connection for the chain.
     *
     * @param  \UnitEnum|string|null  $connection
     * @return $this
     */
    public function allOnConnection($connection)
    {
        $resolvedConnection = enum_value($connection);

        $this->chainConnection = $resolvedConnection;
        $this->connection = $resolvedConnection;

        return $this;
    }

    /**
     * Set the desired queue for the chain.
     *
     * @param  \UnitEnum|string|null  $queue
     * @return $this
     */
    public function allOnQueue($queue)
    {
        $resolvedQueue = enum_value($queue);

        $this->chainQueue = $resolvedQueue;
        $this->queue = $resolvedQueue;

        return $this;
    }

    /**
     * Set the desired delay in seconds for the job.
     *
     * @param  \DateTimeInterface|\DateInterval|array|int|null  $delay
     * @return $this
     */
    public function delay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Set the delay for the job to zero seconds.
     *
     * @return $this
     */
    public function withoutDelay()
    {
        $this->delay = 0;

        return $this;
    }

    /**
     * Indicate that the job should be dispatched after all database transactions have committed.
     *
     * @return $this
     */
    public function afterCommit()
    {
        $this->afterCommit = true;

        return $this;
    }

    /**
     * Indicate that the job should not wait until database transactions have been committed before dispatching.
     *
     * @return $this
     */
    public function beforeCommit()
    {
        $this->afterCommit = false;

        return $this;
    }

    /**
     * Specify the middleware the job should be dispatched through.
     *
     * @param  array|object  $middleware
     * @return $this
     */
    public function through($middleware)
    {
        $this->middleware = Arr::wrap($middleware);

        return $this;
    }

    /**
     * Set the jobs that should run if this job is successful.
     *
     * @param  array  $chain
     * @return $this
     */
    public function chain($chain)
    {
        $this->chained = ChainedBatch::prepareNestedBatches(new Collection($chain))
            ->map(fn ($job) => $this->serializeJob($job))
            ->all();

        return $this;
    }

    /**
     * Prepend a job to the current chain so that it is run after the currently running job.
     *
     * @param  mixed  $job
     * @return $this
     */
    public function prependToChain($job)
    {
        $jobs = ChainedBatch::prepareNestedBatches(Collection::wrap($job));

        foreach ($jobs->reverse() as $job) {
            $this->chained = Arr::prepend($this->chained, $this->serializeJob($job));
        }

        return $this;
    }

    /**
     * Append a job to the end of the current chain.
     *
     * @param  mixed  $job
     * @return $this
     */
    public function appendToChain($job)
    {
        $jobs = ChainedBatch::prepareNestedBatches(Collection::wrap($job));

        foreach ($jobs as $job) {
            $this->chained = array_merge($this->chained, [$this->serializeJob($job)]);
        }

        return $this;
    }

    /**
     * Serialize a job for queuing.
     *
     * @param  mixed  $job
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function serializeJob($job)
    {
        if ($job instanceof Closure) {
            if (! class_exists(CallQueuedClosure::class)) {
                throw new RuntimeException(
                    'To enable support for closure jobs, please install the illuminate/queue package.'
                );
            }

            $job = CallQueuedClosure::create($job);
        }

        return serialize($job);
    }

    /**
     * Dispatch the next job on the chain.
     *
     * @return void
     */
    public function dispatchNextJobInChain()
    {
        if (is_array($this->chained) && ! empty($this->chained)) {
            dispatch(tap(unserialize(array_shift($this->chained)), function ($next) {
                $next->chained = $this->chained;

                $next->onConnection($next->connection ?: $this->chainConnection);
                $next->onQueue($next->queue ?: $this->chainQueue);

                $next->chainConnection = $this->chainConnection;
                $next->chainQueue = $this->chainQueue;
                $next->chainCatchCallbacks = $this->chainCatchCallbacks;
            }));
        }
    }

    /**
     * Invoke all of the chain's failed job callbacks.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function invokeChainCatchCallbacks($e)
    {
        (new Collection($this->chainCatchCallbacks))->each(function ($callback) use ($e) {
            $callback($e);
        });
    }

    /**
     * Assert that the job has the given chain of jobs attached to it.
     *
     * @param  array  $expectedChain
     * @return void
     */
    public function assertHasChain($expectedChain)
    {
        PHPUnit::assertTrue(
            (new Collection($expectedChain))->isNotEmpty(),
            'The expected chain can not be empty.'
        );

        if ((new Collection($expectedChain))->contains(fn ($job) => is_object($job))) {
            $expectedChain = (new Collection($expectedChain))->map(fn ($job) => serialize($job))->all();
        } else {
            $chain = (new Collection($this->chained))->map(fn ($job) => get_class(unserialize($job)))->all();
        }

        PHPUnit::assertTrue(
            $expectedChain === ($chain ?? $this->chained),
            'The job does not have the expected chain.'
        );
    }

    /**
     * Assert that the job has no remaining chained jobs.
     *
     * @return void
     */
    public function assertDoesntHaveChain()
    {
        PHPUnit::assertEmpty($this->chained, 'The job has chained jobs.');
    }
}
