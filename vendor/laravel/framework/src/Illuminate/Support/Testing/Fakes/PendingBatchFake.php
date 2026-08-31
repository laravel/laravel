<?php

namespace Illuminate\Support\Testing\Fakes;

use Closure;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ReflectsClosures;

class PendingBatchFake extends PendingBatch
{
    use ReflectsClosures;

    /**
     * The fake bus instance.
     *
     * @var \Illuminate\Support\Testing\Fakes\BusFake
     */
    protected $bus;

    /**
     * Create a new pending batch instance.
     *
     * @param  \Illuminate\Support\Testing\Fakes\BusFake  $bus
     * @param  \Illuminate\Support\Collection  $jobs
     */
    public function __construct(BusFake $bus, Collection $jobs)
    {
        $this->bus = $bus;
        $this->jobs = $jobs->filter()->values();
    }

    /**
     * Dispatch the batch.
     *
     * @return \Illuminate\Bus\Batch
     */
    public function dispatch()
    {
        return $this->bus->recordPendingBatch($this);
    }

    /**
     * Dispatch the batch after the response is sent to the browser.
     *
     * @return \Illuminate\Bus\Batch
     */
    public function dispatchAfterResponse()
    {
        return $this->bus->recordPendingBatch($this);
    }

    /**
     * Determine if the jobs in the batch match the given jobs.
     *
     * @param  array  $expectedJobs
     * @return bool
     */
    public function hasJobs(array $expectedJobs)
    {
        if (count($this->jobs) !== count($expectedJobs)) {
            return false;
        }

        foreach ($expectedJobs as $index => $expectedJob) {
            if ($expectedJob instanceof Closure) {
                $expectedType = $this->firstClosureParameterType($expectedJob);

                if (! $this->jobs[$index] instanceof $expectedType) {
                    return false;
                }

                if (! $expectedJob($this->jobs[$index])) {
                    return false;
                }
            } elseif (is_string($expectedJob)) {
                if ($expectedJob != get_class($this->jobs[$index])) {
                    return false;
                }
            } elseif (serialize($expectedJob) != serialize($this->jobs[$index])) {
                return false;
            }
        }

        return true;
    }
}
