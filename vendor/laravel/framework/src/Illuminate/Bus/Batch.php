<?php

namespace Illuminate\Bus;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Bus\Events\BatchCanceled;
use Illuminate\Bus\Events\BatchFinished;
use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\Factory as QueueFactory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JsonSerializable;
use Throwable;

class Batch implements Arrayable, JsonSerializable
{
    /**
     * The queue factory implementation.
     *
     * @var \Illuminate\Contracts\Queue\Factory
     */
    protected $queue;

    /**
     * The repository implementation.
     *
     * @var \Illuminate\Bus\BatchRepository
     */
    protected $repository;

    /**
     * The batch ID.
     *
     * @var string
     */
    public $id;

    /**
     * The batch name.
     *
     * @var string
     */
    public $name;

    /**
     * The total number of jobs that belong to the batch.
     *
     * @var int
     */
    public $totalJobs;

    /**
     * The total number of jobs that are still pending.
     *
     * @var int
     */
    public $pendingJobs;

    /**
     * The total number of jobs that have failed.
     *
     * @var int
     */
    public $failedJobs;

    /**
     * The IDs of the jobs that have failed.
     *
     * @var array
     */
    public $failedJobIds;

    /**
     * The batch options.
     *
     * @var array
     */
    public $options;

    /**
     * The date indicating when the batch was created.
     *
     * @var \Carbon\CarbonImmutable
     */
    public $createdAt;

    /**
     * The date indicating when the batch was cancelled.
     *
     * @var \Carbon\CarbonImmutable|null
     */
    public $cancelledAt;

    /**
     * The date indicating when the batch was finished.
     *
     * @var \Carbon\CarbonImmutable|null
     */
    public $finishedAt;

    /**
     * Create a new batch instance.
     */
    public function __construct(
        QueueFactory $queue,
        BatchRepository $repository,
        string $id,
        string $name,
        int $totalJobs,
        int $pendingJobs,
        int $failedJobs,
        array $failedJobIds,
        array $options,
        CarbonImmutable $createdAt,
        ?CarbonImmutable $cancelledAt = null,
        ?CarbonImmutable $finishedAt = null,
    ) {
        $this->queue = $queue;
        $this->repository = $repository;
        $this->id = $id;
        $this->name = $name;
        $this->totalJobs = $totalJobs;
        $this->pendingJobs = $pendingJobs;
        $this->failedJobs = $failedJobs;
        $this->failedJobIds = $failedJobIds;
        $this->options = $options;
        $this->createdAt = $createdAt;
        $this->cancelledAt = $cancelledAt;
        $this->finishedAt = $finishedAt;
    }

    /**
     * Get a fresh instance of the batch represented by this ID.
     *
     * @return self
     */
    public function fresh()
    {
        return $this->repository->find($this->id);
    }

    /**
     * Add additional jobs to the batch.
     *
     * @param  \Illuminate\Support\Enumerable|object|array  $jobs
     * @return self
     */
    public function add($jobs)
    {
        $count = 0;

        $jobs = Collection::wrap($jobs)->map(function ($job) use (&$count) {
            $job = $job instanceof Closure ? CallQueuedClosure::create($job) : $job;

            if (is_array($job)) {
                $count += count($job);

                $chain = $this->prepareBatchedChain($job);

                return $chain->first()
                    ->allOnQueue($this->options['queue'] ?? null)
                    ->allOnConnection($this->options['connection'] ?? null)
                    ->chain($chain->slice(1)->values()->all());
            } else {
                $job->withBatchId($this->id);

                $count++;
            }

            return $job;
        });

        $this->repository->transaction(function () use ($jobs, $count) {
            $this->repository->incrementTotalJobs($this->id, $count);

            $this->queue->connection($this->options['connection'] ?? null)->bulk(
                $jobs->all(),
                $data = '',
                $this->options['queue'] ?? null
            );
        });

        return $this->fresh();
    }

    /**
     * Prepare a chain that exists within the jobs being added.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function prepareBatchedChain(array $chain)
    {
        return (new Collection($chain))->map(function ($job) {
            $job = $job instanceof Closure ? CallQueuedClosure::create($job) : $job;

            return $job->withBatchId($this->id);
        });
    }

    /**
     * Get the total number of jobs that have been processed by the batch thus far.
     *
     * @return int
     */
    public function processedJobs()
    {
        return $this->totalJobs - $this->pendingJobs;
    }

    /**
     * Get the percentage of jobs that have been processed (between 0-100).
     *
     * @return int<0, 100>
     */
    public function progress()
    {
        return $this->totalJobs > 0 ? (int) round(($this->processedJobs() / $this->totalJobs) * 100) : 0;
    }

    /**
     * Record that a job within the batch finished successfully, executing any callbacks if necessary.
     *
     * @return void
     */
    public function recordSuccessfulJob(string $jobId)
    {
        $counts = $this->decrementPendingJobs($jobId);

        if ($this->hasProgressCallbacks()) {
            $this->invokeCallbacks('progress');
        }

        if ($counts->pendingJobs === 0) {
            $this->repository->markAsFinished($this->id);

            $container = Container::getInstance();

            if ($container->bound(Dispatcher::class)) {
                $container->make(Dispatcher::class)->dispatch(new BatchFinished($this));
            }
        }

        if ($counts->pendingJobs === 0 && $this->hasThenCallbacks()) {
            $this->invokeCallbacks('then');
        }

        if ($counts->allJobsHaveRanExactlyOnce() && $this->hasFinallyCallbacks()) {
            $this->invokeCallbacks('finally');
        }
    }

    /**
     * Decrement the pending jobs for the batch.
     *
     * @return \Illuminate\Bus\UpdatedBatchJobCounts
     */
    public function decrementPendingJobs(string $jobId)
    {
        return $this->repository->decrementPendingJobs($this->id, $jobId);
    }

    /**
     * Invoke the callbacks of the given type.
     */
    protected function invokeCallbacks(string $type, ?Throwable $e = null): void
    {
        $batch = $this->fresh();

        foreach ($this->options[$type] ?? [] as $handler) {
            $this->invokeHandlerCallback($handler, $batch, $e);
        }
    }

    /**
     * Determine if the batch has finished executing.
     *
     * @return bool
     */
    public function finished()
    {
        return ! is_null($this->finishedAt);
    }

    /**
     * Determine if the batch has "progress" callbacks.
     *
     * @return bool
     */
    public function hasProgressCallbacks()
    {
        return isset($this->options['progress']) && ! empty($this->options['progress']);
    }

    /**
     * Determine if the batch has "success" callbacks.
     *
     * @return bool
     */
    public function hasThenCallbacks()
    {
        return isset($this->options['then']) && ! empty($this->options['then']);
    }

    /**
     * Determine if the batch allows jobs to fail without cancelling the batch.
     *
     * @return bool
     */
    public function allowsFailures()
    {
        return Arr::get($this->options, 'allowFailures', false) === true;
    }

    /**
     * Determine if the batch has job failures.
     *
     * @return bool
     */
    public function hasFailures()
    {
        return $this->failedJobs > 0;
    }

    /**
     * Record that a job within the batch failed to finish successfully, executing any callbacks if necessary.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function recordFailedJob(string $jobId, $e)
    {
        $counts = $this->incrementFailedJobs($jobId);

        if ($counts->failedJobs === 1 && ! $this->allowsFailures()) {
            $this->cancel();
        }

        if ($this->allowsFailures()) {
            if ($this->hasProgressCallbacks()) {
                $this->invokeCallbacks('progress', $e);
            }

            if ($this->hasFailureCallbacks()) {
                $this->invokeCallbacks('failure', $e);
            }
        }

        if ($counts->failedJobs === 1 && $this->hasCatchCallbacks()) {
            $this->invokeCallbacks('catch', $e);
        }

        if ($counts->allJobsHaveRanExactlyOnce() && $this->hasFinallyCallbacks()) {
            $this->invokeCallbacks('finally');
        }
    }

    /**
     * Increment the failed jobs for the batch.
     *
     * @return \Illuminate\Bus\UpdatedBatchJobCounts
     */
    public function incrementFailedJobs(string $jobId)
    {
        return $this->repository->incrementFailedJobs($this->id, $jobId);
    }

    /**
     * Determine if the batch has "catch" callbacks.
     *
     * @return bool
     */
    public function hasCatchCallbacks()
    {
        return isset($this->options['catch']) && ! empty($this->options['catch']);
    }

    /**
     * Determine if the batch has "failure" callbacks.
     */
    public function hasFailureCallbacks(): bool
    {
        return isset($this->options['failure']) && ! empty($this->options['failure']);
    }

    /**
     * Determine if the batch has "finally" callbacks.
     *
     * @return bool
     */
    public function hasFinallyCallbacks()
    {
        return isset($this->options['finally']) && ! empty($this->options['finally']);
    }

    /**
     * Cancel the batch.
     *
     * @return void
     */
    public function cancel()
    {
        $this->repository->cancel($this->id);

        $container = Container::getInstance();

        if ($container->bound(Dispatcher::class)) {
            $container->make(Dispatcher::class)->dispatch(new BatchCanceled($this));
        }
    }

    /**
     * Determine if the batch has been cancelled.
     *
     * @return bool
     */
    public function canceled()
    {
        return $this->cancelled();
    }

    /**
     * Determine if the batch has been cancelled.
     *
     * @return bool
     */
    public function cancelled()
    {
        return ! is_null($this->cancelledAt);
    }

    /**
     * Delete the batch from storage.
     *
     * @return void
     */
    public function delete()
    {
        $this->repository->delete($this->id);
    }

    /**
     * Invoke a batch callback handler.
     *
     * @param  callable  $handler
     * @return void
     */
    protected function invokeHandlerCallback($handler, Batch $batch, ?Throwable $e = null)
    {
        try {
            $handler($batch, $e);
        } catch (Throwable $e) {
            if (function_exists('report')) {
                report($e);
            }
        }
    }

    /**
     * Convert the batch to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'totalJobs' => $this->totalJobs,
            'pendingJobs' => $this->pendingJobs,
            'processedJobs' => $this->processedJobs(),
            'progress' => $this->progress(),
            'failedJobs' => $this->failedJobs,
            'options' => $this->options,
            'createdAt' => $this->createdAt,
            'cancelledAt' => $this->cancelledAt,
            'finishedAt' => $this->finishedAt,
        ];
    }

    /**
     * Get the JSON serializable representation of the object.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Dynamically access the batch's "options" via properties.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->options[$key] ?? null;
    }
}
