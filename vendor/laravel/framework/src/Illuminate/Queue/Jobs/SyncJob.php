<?php

namespace Illuminate\Queue\Jobs;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;

class SyncJob extends Job implements JobContract
{
    /**
     * The class name of the job.
     *
     * @var string
     */
    protected $job;

    /**
     * The queue message data.
     *
     * @var string
     */
    protected $payload;

    /**
     * Create a new job instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  string  $payload
     * @param  string  $connectionName
     * @param  string  $queue
     */
    public function __construct(Container $container, $payload, $connectionName, $queue)
    {
        $this->queue = $queue;
        $this->payload = $payload;
        $this->container = $container;
        $this->connectionName = $connectionName;
    }

    /**
     * Release the job back into the queue after (n) seconds.
     *
     * @param  int  $delay
     * @return void
     */
    public function release($delay = 0)
    {
        parent::release($delay);
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return 1;
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        return '';
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->payload;
    }

    /**
     * Get the name of the queue the job belongs to.
     *
     * @return string
     */
    public function getQueue()
    {
        return 'sync';
    }
}
