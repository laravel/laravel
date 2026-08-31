<?php

namespace Illuminate\Queue;

use Illuminate\Bus\UniqueLock;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Events\JobAttempted;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Jobs\SyncJob;
use Throwable;

class SyncQueue extends Queue implements QueueContract
{
    /**
     * Create a new sync queue instance.
     *
     * @param  bool  $dispatchAfterCommit
     */
    public function __construct($dispatchAfterCommit = false)
    {
        $this->dispatchAfterCommit = $dispatchAfterCommit;
    }

    /**
     * Get the size of the queue.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function size($queue = null)
    {
        return 0;
    }

    /**
     * Get the number of pending jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function pendingSize($queue = null)
    {
        return 0;
    }

    /**
     * Get the number of delayed jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function delayedSize($queue = null)
    {
        return 0;
    }

    /**
     * Get the number of reserved jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function reservedSize($queue = null)
    {
        return 0;
    }

    /**
     * Get the creation timestamp of the oldest pending job, excluding delayed jobs.
     *
     * @param  string|null  $queue
     * @return int|null
     */
    public function creationTimeOfOldestPendingJob($queue = null)
    {
        return null;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string  $job
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     *
     * @throws \Throwable
     */
    public function push($job, $data = '', $queue = null)
    {
        if ($this->shouldDispatchAfterCommit($job) &&
            $this->container->bound('db.transactions')) {
            if ($job instanceof ShouldBeUnique) {
                $this->container->make('db.transactions')->addCallbackForRollback(
                    function () use ($job) {
                        (new UniqueLock($this->container->make(Cache::class)))->release($job);
                    }
                );
            }

            return $this->container->make('db.transactions')->addCallback(
                fn () => $this->executeJob($job, $data, $queue)
            );
        }

        return $this->executeJob($job, $data, $queue);
    }

    /**
     * Execute a given job synchronously.
     *
     * @param  string  $job
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return int
     *
     * @throws \Throwable
     */
    protected function executeJob($job, $data = '', $queue = null)
    {
        $queueJob = $this->resolveJob($this->createPayload($job, $queue, $data), $queue);

        try {
            $this->raiseBeforeJobEvent($queueJob);

            $queueJob->fire();

            $this->raiseAfterJobEvent($queueJob);
        } catch (Throwable $e) {
            $exceptionOccurred = true;

            $this->handleException($queueJob, $e);
        } finally {
            $this->raiseJobAttemptedEvent($queueJob, $exceptionOccurred ?? false);
        }

        return 0;
    }

    /**
     * Resolve a Sync job instance.
     *
     * @param  string  $payload
     * @param  string  $queue
     * @return \Illuminate\Queue\Jobs\SyncJob
     */
    protected function resolveJob($payload, $queue)
    {
        return new SyncJob($this->container, $payload, $this->connectionName, $queue);
    }

    /**
     * Raise the before queue job event.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @return void
     */
    protected function raiseBeforeJobEvent(Job $job)
    {
        if ($this->container->bound('events')) {
            $this->container['events']->dispatch(new JobProcessing($this->connectionName, $job));
        }
    }

    /**
     * Raise the after queue job event.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @return void
     */
    protected function raiseAfterJobEvent(Job $job)
    {
        if ($this->container->bound('events')) {
            $this->container['events']->dispatch(new JobProcessed($this->connectionName, $job));
        }
    }

    /**
     * Raise the job attempted event.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  bool  $exceptionOccurred
     * @return void
     */
    protected function raiseJobAttemptedEvent(Job $job, bool $exceptionOccurred = false)
    {
        if ($this->container->bound('events')) {
            $this->container['events']->dispatch(new JobAttempted($this->connectionName, $job, $exceptionOccurred));
        }
    }

    /**
     * Raise the exception occurred queue job event.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  \Throwable  $e
     * @return void
     */
    protected function raiseExceptionOccurredJobEvent(Job $job, Throwable $e)
    {
        if ($this->container->bound('events')) {
            $this->container['events']->dispatch(new JobExceptionOccurred($this->connectionName, $job, $e));
        }
    }

    /**
     * Handle an exception that occurred while processing a job.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $queueJob
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Throwable
     */
    protected function handleException(Job $queueJob, Throwable $e)
    {
        $this->raiseExceptionOccurredJobEvent($queueJob, $e);

        $queueJob->fail($e);

        throw $e;
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string|null  $queue
     * @param  array  $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        //
    }

    /**
     * Push a new job onto the queue after (n) seconds.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string  $job
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        return $this->push($job, $data, $queue);
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        //
    }
}
