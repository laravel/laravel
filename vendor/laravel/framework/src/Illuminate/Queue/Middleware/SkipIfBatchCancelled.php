<?php

namespace Illuminate\Queue\Middleware;

class SkipIfBatchCancelled
{
    /**
     * Process the job.
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        if (method_exists($job, 'batch') && $job->batch()?->cancelled()) {
            return;
        }

        $next($job);
    }
}
