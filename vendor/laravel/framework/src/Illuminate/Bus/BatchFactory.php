<?php

namespace Illuminate\Bus;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Queue\Factory as QueueFactory;

class BatchFactory
{
    /**
     * The queue factory implementation.
     *
     * @var \Illuminate\Contracts\Queue\Factory
     */
    protected $queue;

    /**
     * Create a new batch factory instance.
     *
     * @param  \Illuminate\Contracts\Queue\Factory  $queue
     */
    public function __construct(QueueFactory $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Create a new batch instance.
     *
     * @param  \Illuminate\Bus\BatchRepository  $repository
     * @param  string  $id
     * @param  string  $name
     * @param  int  $totalJobs
     * @param  int  $pendingJobs
     * @param  int  $failedJobs
     * @param  array  $failedJobIds
     * @param  array  $options
     * @param  \Carbon\CarbonImmutable  $createdAt
     * @param  \Carbon\CarbonImmutable|null  $cancelledAt
     * @param  \Carbon\CarbonImmutable|null  $finishedAt
     * @return \Illuminate\Bus\Batch
     */
    public function make(BatchRepository $repository,
                         string $id,
                         string $name,
                         int $totalJobs,
                         int $pendingJobs,
                         int $failedJobs,
                         array $failedJobIds,
                         array $options,
                         CarbonImmutable $createdAt,
                         ?CarbonImmutable $cancelledAt,
                         ?CarbonImmutable $finishedAt)
    {
        return new Batch($this->queue, $repository, $id, $name, $totalJobs, $pendingJobs, $failedJobs, $failedJobIds, $options, $createdAt, $cancelledAt, $finishedAt);
    }
}
