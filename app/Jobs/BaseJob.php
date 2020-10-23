<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class BaseJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected const QUEUE = 'default';

    public function __construct()
    {
        $this->configureJob();
    }

    protected function configureJob(): void
    {
        $this->onConnection(config('queue.jobs.' . static::QUEUE . '.connection'));
        $this->queue = config('queue.jobs.' . static::QUEUE . '.queue');
    }
}
