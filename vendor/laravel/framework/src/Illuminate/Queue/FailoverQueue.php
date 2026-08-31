<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Events\QueueFailedOver;
use RuntimeException;
use Throwable;

class FailoverQueue extends Queue implements QueueContract
{
    /**
     * The queues which failed on the last action.
     *
     * @var list<string>
     */
    protected array $failingQueues = [];

    /**
     * Create a new failover queue instance.
     */
    public function __construct(
        public QueueManager $manager,
        public EventDispatcher $events,
        public array $connections
    ) {
    }

    /**
     * Get the size of the queue.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function size($queue = null)
    {
        return $this->manager->connection($this->connections[0])->size($queue);
    }

    /**
     * Get the number of pending jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function pendingSize($queue = null)
    {
        return $this->manager->connection($this->connections[0])->pendingSize($queue);
    }

    /**
     * Get the number of delayed jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function delayedSize($queue = null)
    {
        return $this->manager->connection($this->connections[0])->delayedSize($queue);
    }

    /**
     * Get the number of reserved jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function reservedSize($queue = null)
    {
        return $this->manager->connection($this->connections[0])->reservedSize($queue);
    }

    /**
     * Get the creation timestamp of the oldest pending job, excluding delayed jobs.
     *
     * @param  string|null  $queue
     * @return int|null
     */
    public function creationTimeOfOldestPendingJob($queue = null)
    {
        return $this->manager
            ->connection($this->connections[0])
            ->creationTimeOfOldestPendingJob($queue);
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  object|string  $job
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function push($job, $data = '', $queue = null)
    {
        return $this->attemptOnAllConnections(__FUNCTION__, func_get_args(), $job);
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string|null  $queue
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        return $this->attemptOnAllConnections(__FUNCTION__, func_get_args());
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
        return $this->attemptOnAllConnections(__FUNCTION__, func_get_args(), $job);
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        return $this->manager->connection($this->connections[0])->pop($queue);
    }

    /**
     * Attempt the given method on all connections.
     *
     * @param  mixed  $job
     * @return mixed
     *
     * @throws \Throwable
     */
    protected function attemptOnAllConnections(string $method, array $arguments, $job = null)
    {
        [$lastException, $failedQueues] = [null, []];

        try {
            foreach ($this->connections as $connection) {
                try {
                    return $this->manager->connection($connection)->{$method}(...$arguments);
                } catch (Throwable $e) {
                    $lastException = $e;

                    $failedQueues[] = $connection;

                    if ($job !== null && ! in_array($connection, $this->failingQueues)) {
                        $this->events->dispatch(new QueueFailedOver($connection, $job, $e));
                    }
                }
            }
        } finally {
            $this->failingQueues = $failedQueues;
        }

        throw $lastException ?? new RuntimeException('All failover queue connections failed.');
    }
}
