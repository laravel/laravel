<?php

namespace Illuminate\Bus;

class UpdatedBatchJobCounts
{
    /**
     * The number of pending jobs remaining for the batch.
     *
     * @var int
     */
    public $pendingJobs;

    /**
     * The number of failed jobs that belong to the batch.
     *
     * @var int
     */
    public $failedJobs;

    /**
     * Create a new batch job counts object.
     *
     * @param  int  $pendingJobs
     * @param  int  $failedJobs
     */
    public function __construct(int $pendingJobs = 0, int $failedJobs = 0)
    {
        $this->pendingJobs = $pendingJobs;
        $this->failedJobs = $failedJobs;
    }

    /**
     * Determine if all jobs have run exactly once.
     *
     * @return bool
     */
    public function allJobsHaveRanExactlyOnce()
    {
        return ($this->pendingJobs - $this->failedJobs) === 0;
    }
}
