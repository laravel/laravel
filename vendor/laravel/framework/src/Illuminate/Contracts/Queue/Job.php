<?php

namespace Illuminate\Contracts\Queue;

interface Job
{
    /**
     * Get the UUID of the job.
     *
     * @return string|null
     */
    public function uuid();

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId();

    /**
     * Get the decoded body of the job.
     *
     * @return array
     */
    public function payload();

    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire();

    /**
     * Release the job back into the queue after (n) seconds.
     *
     * @param  int  $delay
     * @return void
     */
    public function release($delay = 0);

    /**
     * Determine if the job was released back into the queue.
     *
     * @return bool
     */
    public function isReleased();

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete();

    /**
     * Determine if the job has been deleted.
     *
     * @return bool
     */
    public function isDeleted();

    /**
     * Determine if the job has been deleted or released.
     *
     * @return bool
     */
    public function isDeletedOrReleased();

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts();

    /**
     * Determine if the job has been marked as a failure.
     *
     * @return bool
     */
    public function hasFailed();

    /**
     * Mark the job as "failed".
     *
     * @return void
     */
    public function markAsFailed();

    /**
     * Delete the job, call the "failed" method, and raise the failed job event.
     *
     * @param  \Throwable|null  $e
     * @return void
     */
    public function fail($e = null);

    /**
     * Get the number of times to attempt a job.
     *
     * @return int|null
     */
    public function maxTries();

    /**
     * Get the maximum number of exceptions allowed, regardless of attempts.
     *
     * @return int|null
     */
    public function maxExceptions();

    /**
     * Get the number of seconds the job can run.
     *
     * @return int|null
     */
    public function timeout();

    /**
     * Get the timestamp indicating when the job should timeout.
     *
     * @return int|null
     */
    public function retryUntil();

    /**
     * Get the name of the queued job class.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the display name of the queued job class.
     *
     * Resolves the name of "wrapped" jobs such as class-based handlers.
     *
     * @return string
     */
    public function resolveName();

    /**
     * Get the class of the queued job.
     *
     * Resolves the class of "wrapped" jobs such as class-based handlers.
     *
     * @return string
     */
    public function resolveQueuedJobClass();

    /**
     * Get the name of the connection the job belongs to.
     *
     * @return string
     */
    public function getConnectionName();

    /**
     * Get the name of the queue the job belongs to.
     *
     * @return string
     */
    public function getQueue();

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody();
}
