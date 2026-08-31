<?php

namespace Illuminate\Queue\Failed;

interface CountableFailedJobProvider
{
    /**
     * Count the failed jobs.
     *
     * @param  string|null  $connection
     * @param  string|null  $queue
     * @return int
     */
    public function count($connection = null, $queue = null);
}
