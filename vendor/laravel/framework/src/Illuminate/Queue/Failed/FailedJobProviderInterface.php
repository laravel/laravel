<?php

namespace Illuminate\Queue\Failed;

interface FailedJobProviderInterface
{
    /**
     * Log a failed job into storage.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  string  $payload
     * @param  \Throwable  $exception
     * @return string|int|null
     */
    public function log($connection, $queue, $payload, $exception);

    /**
     * Get the IDs of all of the failed jobs.
     *
     * @param  string|null  $queue
     * @return array
     */
    public function ids($queue = null);

    /**
     * Get a list of all of the failed jobs.
     *
     * @return array
     */
    public function all();

    /**
     * Get a single failed job.
     *
     * @param  mixed  $id
     * @return object|null
     */
    public function find($id);

    /**
     * Delete a single failed job from storage.
     *
     * @param  mixed  $id
     * @return bool
     */
    public function forget($id);

    /**
     * Flush all of the failed jobs from storage.
     *
     * @param  int|null  $hours
     * @return void
     */
    public function flush($hours = null);
}
