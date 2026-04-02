<?php

namespace Illuminate\Queue;

use Illuminate\Bus\DebounceLock;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Queue\ShouldBeDebounced;

/**
 * Adds debounce lock rollback handling to the Queue base class.
 *
 * Integration: Use this trait in Queue and call
 * $this->registerDebounceLockRollback($job) in enqueueUsing()
 * alongside the existing ShouldBeUnique rollback handler.
 *
 * @see Queue::enqueueUsing()
 */
trait HandlesDebouncedJobTransactions
{
    /**
     * Register a rollback handler to release the debounce lock
     * if the database transaction rolls back.
     *
     * This mirrors the existing ShouldBeUnique rollback pattern
     * in Queue::enqueueUsing().
     *
     * @param  mixed  $job
     */
    protected function registerDebounceLockRollback($job): void
    {
        if (! $job instanceof ShouldBeDebounced) {
            return;
        }

        if ($this->shouldDispatchAfterCommit($job) &&
            $this->container->bound('db.transactions')) {
            $this->container->make('db.transactions')->addCallbackForRollback(
                function () use ($job) {
                    (new DebounceLock(
                        $this->container->make(Cache::class)
                    ))->release($job);
                }
            );
        }
    }
}
