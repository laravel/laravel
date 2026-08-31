<?php

namespace Illuminate\Queue;

class WorkerOptions
{
    /**
     * The name of the worker.
     *
     * @var string
     */
    public $name;

    /**
     * The number of seconds to wait before retrying a job that encountered an uncaught exception.
     *
     * @var int|int[]
     */
    public $backoff;

    /**
     * The maximum amount of RAM the worker may consume.
     *
     * @var int
     */
    public $memory;

    /**
     * The maximum number of seconds a child worker may run.
     *
     * @var int
     */
    public $timeout;

    /**
     * The number of seconds to wait in between polling the queue.
     *
     * @var int
     */
    public $sleep;

    /**
     * The number of seconds to rest between jobs.
     *
     * @var int
     */
    public $rest;

    /**
     * The maximum number of times a job may be attempted.
     *
     * @var int
     */
    public $maxTries;

    /**
     * Indicates if the worker should run in maintenance mode.
     *
     * @var bool
     */
    public $force;

    /**
     * Indicates if the worker should stop when the queue is empty.
     *
     * @var bool
     */
    public $stopWhenEmpty;

    /**
     * The number of seconds to wait for a job before stopping.
     *
     * @var int
     */
    public $stopWhenEmptyFor;

    /**
     * The maximum number of jobs to run.
     *
     * @var int
     */
    public $maxJobs;

    /**
     * The maximum number of seconds a worker may live.
     *
     * @var int
     */
    public $maxTime;

    /**
     * Create a new worker options instance.
     *
     * @param  string  $name
     * @param  int|int[]  $backoff
     * @param  int  $memory
     * @param  int  $timeout
     * @param  int  $sleep
     * @param  int  $maxTries
     * @param  bool  $force
     * @param  bool  $stopWhenEmpty
     * @param  int  $maxJobs
     * @param  int  $maxTime
     * @param  int  $rest
     * @param  int  $stopWhenEmptyFor
     */
    public function __construct(
        $name = 'default',
        $backoff = 0,
        $memory = 128,
        $timeout = 60,
        $sleep = 3,
        $maxTries = 1,
        $force = false,
        $stopWhenEmpty = false,
        $maxJobs = 0,
        $maxTime = 0,
        $rest = 0,
        $stopWhenEmptyFor = 0,
    ) {
        $this->name = $name;
        $this->backoff = $backoff;
        $this->sleep = $sleep;
        $this->rest = $rest;
        $this->force = $force;
        $this->memory = $memory;
        $this->timeout = $timeout;
        $this->maxTries = $maxTries;
        $this->stopWhenEmpty = $stopWhenEmpty;
        $this->stopWhenEmptyFor = $stopWhenEmptyFor;
        $this->maxJobs = $maxJobs;
        $this->maxTime = $maxTime;
    }
}
