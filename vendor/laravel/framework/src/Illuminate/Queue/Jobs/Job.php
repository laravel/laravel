<?php

namespace Illuminate\Queue\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\BatchRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\ManuallyFailedException;
use Illuminate\Queue\TimeoutExceededException;
use Illuminate\Support\InteractsWithTime;
use Throwable;

abstract class Job
{
    use InteractsWithTime;

    /**
     * The job handler instance.
     *
     * @var mixed
     */
    protected $instance;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Indicates if the job has been deleted.
     *
     * @var bool
     */
    protected $deleted = false;

    /**
     * Indicates if the job has been released.
     *
     * @var bool
     */
    protected $released = false;

    /**
     * Indicates if the job has failed.
     *
     * @var bool
     */
    protected $failed = false;

    /**
     * The name of the connection the job belongs to.
     *
     * @var string
     */
    protected $connectionName;

    /**
     * The name of the queue the job belongs to.
     *
     * @var string
     */
    protected $queue;

    /**
     * Get the job identifier.
     *
     * @return string|int|null
     */
    abstract public function getJobId();

    /**
     * Get the raw body of the job.
     *
     * @return string
     */
    abstract public function getRawBody();

    /**
     * Get the UUID of the job.
     *
     * @return string|null
     */
    public function uuid()
    {
        return $this->payload()['uuid'] ?? null;
    }

    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire()
    {
        $payload = $this->payload();

        [$class, $method] = JobName::parse($payload['job']);

        ($this->instance = $this->resolve($class))->{$method}($this, $payload['data']);
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        $this->deleted = true;
    }

    /**
     * Determine if the job has been deleted.
     *
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Release the job back into the queue after (n) seconds.
     *
     * @param  int  $delay
     * @return void
     */
    public function release($delay = 0)
    {
        $this->released = true;
    }

    /**
     * Determine if the job was released back into the queue.
     *
     * @return bool
     */
    public function isReleased()
    {
        return $this->released;
    }

    /**
     * Determine if the job has been deleted or released.
     *
     * @return bool
     */
    public function isDeletedOrReleased()
    {
        return $this->isDeleted() || $this->isReleased();
    }

    /**
     * Determine if the job has been marked as a failure.
     *
     * @return bool
     */
    public function hasFailed()
    {
        return $this->failed;
    }

    /**
     * Mark the job as "failed".
     *
     * @return void
     */
    public function markAsFailed()
    {
        $this->failed = true;
    }

    /**
     * Delete the job, call the "failed" method, and raise the failed job event.
     *
     * @param  \Throwable|null  $e
     * @return void
     */
    public function fail($e = null)
    {
        $this->markAsFailed();

        if ($this->isDeleted()) {
            return;
        }

        $commandName = $this->payload()['data']['commandName'] ?? false;

        // If the exception is due to a job timing out, we need to rollback the current
        // database transaction so that the failed job count can be incremented with
        // the proper value. Otherwise, the current transaction will never commit.
        if ($e instanceof TimeoutExceededException &&
            $commandName &&
            in_array(Batchable::class, class_uses_recursive($commandName))) {
            $batchRepository = $this->resolve(BatchRepository::class);

            try {
                $batchRepository->rollBack();
            } catch (Throwable $e) {
                // ...
            }
        }

        if ($this->shouldRollBackDatabaseTransaction($e)) {
            $this->container->make('db')
                ->connection($this->container['config']['queue.failed.database'])
                ->rollBack(toLevel: 0);
        }

        try {
            // If the job has failed, we will delete it, call the "failed" method and then call
            // an event indicating the job has failed so it can be logged if needed. This is
            // to allow every developer to better keep monitor of their failed queue jobs.
            $this->delete();

            $this->failed($e);
        } finally {
            $this->resolve(Dispatcher::class)->dispatch(new JobFailed(
                $this->connectionName, $this, $e ?: new ManuallyFailedException
            ));
        }
    }

    /**
     * Determine if the current database transaction should be rolled back to level zero.
     *
     * @param  \Throwable  $e
     * @return bool
     */
    protected function shouldRollBackDatabaseTransaction($e)
    {
        return $e instanceof TimeoutExceededException &&
            $this->container['config']['queue.failed.database'] &&
            in_array($this->container['config']['queue.failed.driver'], ['database', 'database-uuids']) &&
            $this->container->bound('db');
    }

    /**
     * Process an exception that caused the job to fail.
     *
     * @param  \Throwable|null  $e
     * @return void
     */
    protected function failed($e)
    {
        $payload = $this->payload();

        [$class] = JobName::parse($payload['job']);

        if (method_exists($this->instance = $this->resolve($class), 'failed')) {
            $this->instance->failed($payload['data'], $e, $payload['uuid'] ?? '', $this);
        }
    }

    /**
     * Resolve the given class.
     *
     * @param  string  $class
     * @return mixed
     */
    protected function resolve($class)
    {
        return $this->container->make($class);
    }

    /**
     * Get the resolved job handler instance.
     *
     * @return mixed
     */
    public function getResolvedJob()
    {
        return $this->instance;
    }

    /**
     * Get the decoded body of the job.
     *
     * @return array
     */
    public function payload()
    {
        return json_decode($this->getRawBody(), true);
    }

    /**
     * Get the number of times to attempt a job.
     *
     * @return int|null
     */
    public function maxTries()
    {
        return $this->payload()['maxTries'] ?? null;
    }

    /**
     * Get the number of times to attempt a job after an exception.
     *
     * @return int|null
     */
    public function maxExceptions()
    {
        return $this->payload()['maxExceptions'] ?? null;
    }

    /**
     * Determine if the job should fail when it timeouts.
     *
     * @return bool
     */
    public function shouldFailOnTimeout()
    {
        return $this->payload()['failOnTimeout'] ?? false;
    }

    /**
     * The number of seconds to wait before retrying a job that encountered an uncaught exception.
     *
     * @return int|int[]|null
     */
    public function backoff()
    {
        return $this->payload()['backoff'] ?? $this->payload()['delay'] ?? null;
    }

    /**
     * Get the number of seconds the job can run.
     *
     * @return int|null
     */
    public function timeout()
    {
        return $this->payload()['timeout'] ?? null;
    }

    /**
     * Get the timestamp indicating when the job should timeout.
     *
     * @return int|null
     */
    public function retryUntil()
    {
        return $this->payload()['retryUntil'] ?? null;
    }

    /**
     * Get the name of the queued job class.
     *
     * @return string
     */
    public function getName()
    {
        return $this->payload()['job'];
    }

    /**
     * Get the resolved display name of the queued job class.
     *
     * Resolves the name of "wrapped" jobs such as class-based handlers.
     *
     * @return string
     */
    public function resolveName()
    {
        return JobName::resolve($this->getName(), $this->payload());
    }

    /**
     * Get the class of the queued job.
     *
     * Resolves the class of "wrapped" jobs such as class-based handlers.
     *
     * @return string
     */
    public function resolveQueuedJobClass()
    {
        return JobName::resolveClassName($this->getName(), $this->payload());
    }

    /**
     * Get the name of the connection the job belongs to.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connectionName;
    }

    /**
     * Get the name of the queue the job belongs to.
     *
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Get the service container instance.
     *
     * @return \Illuminate\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
