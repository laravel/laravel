<?php

namespace Illuminate\Queue\Events;

class QueueBusy
{
    /**
     * Create a new event instance.
     *
     * @param  string  $connection  The connection name.
     * @param  string  $queue  The queue name.
     * @param  int  $size  The size of the queue.
     */
    public function __construct(
        public $connection,
        public $queue,
        public $size,
    ) {
    }
}
