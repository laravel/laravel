<?php

namespace Illuminate\Queue\Events;

class WorkerStopping
{
    /**
     * Create a new event instance.
     *
     * @param  int  $status  The worker exit status.
     * @param  \Illuminate\Queue\WorkerOptions|null  $workerOptions  The worker options.
     * @param  \Illuminate\Queue\WorkerStopReason|null  $reason  The reason why the worker is stopping.
     */
    public function __construct(
        public $status = 0,
        public $workerOptions = null,
        public $reason = null,
    ) {
    }
}
