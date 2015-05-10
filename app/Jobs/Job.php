<?php namespace App\Jobs;

abstract class Job
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string
     */
    public $queue;

    /**
     * The seconds before the job should be made available.
     *
     * @var int
     */
    public $delay;
}
