<?php

namespace Illuminate\Redis\Connections;

use Predis\Command\Redis\FLUSHDB;
use Predis\Command\ServerFlushDatabase;

class PredisClusterConnection extends PredisConnection
{
    /**
     * Get the keys that match the given pattern.
     *
     * @param  string  $pattern
     * @return array
     */
    public function keys(string $pattern)
    {
        $keys = [];

        foreach ($this->client as $node) {
            $keys[] = $node->keys($pattern);
        }

        return array_merge(...$keys);
    }

    /**
     * Flush the selected Redis database on all cluster nodes.
     *
     * @return void
     */
    public function flushdb()
    {
        $command = class_exists(ServerFlushDatabase::class)
            ? ServerFlushDatabase::class
            : FLUSHDB::class;

        foreach ($this->client as $node) {
            $node->executeCommand(tap(new $command)->setArguments(func_get_args()));
        }
    }
}
