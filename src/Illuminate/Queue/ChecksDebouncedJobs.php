<?php

namespace Illuminate\Queue;

use Illuminate\Bus\DebounceLock;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Queue\ShouldBeDebounced;
use Illuminate\Queue\Events\JobDebounced;

/**
 * Adds debounce ownership checking to CallQueuedHandler.
 *
 * Integration: Use this trait in CallQueuedHandler and:
 * 1. Call commandWasDebounced($command) in call() AFTER deserializing
 *    the command but BEFORE dispatchThroughMiddleware().
 *    If true, call handleDebouncedJob($job, $command) and return.
 * 2. Call ensureDebounceLockIsReleased($command) after successful
 *    execution (when job is not released).
 *
 * @see \Illuminate\Queue\CallQueuedHandler::call()
 */
trait ChecksDebouncedJobs
{
    /**
     * Determine if the debounced command was superseded by a newer dispatch.
     *
     * @param  mixed  $command
     * @return bool
     */
    protected function commandWasDebounced($command): bool
    {
        if (! $command instanceof ShouldBeDebounced) {
            return false;
        }

        $owner = $command->debounceOwner ?? '';

        if (empty($owner)) {
            return false;
        }

        return ! (new DebounceLock(
            $this->container->make(Cache::class)
        ))->isCurrentOwner($command, $owner);
    }

    /**
     * Handle a debounced (superseded) job.
     *
     * Fires the JobDebounced event and deletes the job from the queue.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  mixed  $command
     * @return void
     */
    protected function handleDebouncedJob($job, $command): void
    {
        $this->container->make('events')->dispatch(
            new JobDebounced($job->getConnectionName(), $job, $command)
        );

        $job->delete();
    }

    /**
     * Release the debounce lock after a job finishes execution.
     *
     * @param  mixed  $command
     * @return void
     */
    protected function ensureDebounceLockIsReleased($command): void
    {
        if (! $command instanceof ShouldBeDebounced) {
            return;
        }

        (new DebounceLock(
            $this->container->make(Cache::class)
        ))->release($command);
    }
}
