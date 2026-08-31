<?php

namespace Illuminate\Foundation\Queue;

use Illuminate\Bus\Queueable as QueueableByBus;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

trait Queueable
{
    use Dispatchable, InteractsWithQueue, QueueableByBus, SerializesModels;
}
