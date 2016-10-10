<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Pipeline;

use SplQueue;
use Predis\Connection\ConnectionInterface;
use Predis\Connection\ReplicationConnectionInterface;

/**
 * Implements a pipeline executor strategy that writes a list of commands to
 * the connection object but does not read back their replies.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class FireAndForgetExecutor implements PipelineExecutorInterface
{
    /**
     * Allows the pipeline executor to perform operations on the
     * connection before starting to execute the commands stored
     * in the pipeline.
     *
     * @param ConnectionInterface $connection Connection instance.
     */
    protected function checkConnection(ConnectionInterface $connection)
    {
        if ($connection instanceof ReplicationConnectionInterface) {
            $connection->switchTo('master');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ConnectionInterface $connection, SplQueue $commands)
    {
        $this->checkConnection($connection);

        while (!$commands->isEmpty()) {
            $connection->writeCommand($commands->dequeue());
        }

        $connection->disconnect();

        return array();
    }
}
