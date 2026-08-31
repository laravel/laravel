<?php

namespace Illuminate\Queue\Console\Concerns;

trait ParsesQueue
{
    /**
     * Parse the queue argument into connection and queue name.
     *
     * @param  string  $queue
     * @return array{string, string}
     */
    protected function parseQueue($queue)
    {
        [$connection, $queue] = array_pad(explode(':', $queue, 2), -2, null);

        return [
            $connection ?? $this->laravel['config']['queue.default'],
            $queue ?: 'default',
        ];
    }
}
