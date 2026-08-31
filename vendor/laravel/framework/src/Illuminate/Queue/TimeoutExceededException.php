<?php

namespace Illuminate\Queue;

class TimeoutExceededException extends MaxAttemptsExceededException
{
    /**
     * Create a new instance for the job.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @return static
     */
    public static function forJob($job)
    {
        return tap(new static($job->resolveName().' has timed out.'), function ($e) use ($job) {
            $e->job = $job;
        });
    }
}
