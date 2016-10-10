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
use Predis\CommunicationException;
use Predis\Connection\ConnectionInterface;

/**
 * Implements a pipeline executor strategy for connection clusters that does
 * not fail when an error is encountered, but adds the returned error in the
 * replies array.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SafeClusterExecutor implements PipelineExecutorInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(ConnectionInterface $connection, SplQueue $commands)
    {
        $size = count($commands);
        $values = array();
        $connectionExceptions = array();

        foreach ($commands as $command) {
            $cmdConnection = $connection->getConnection($command);

            if (isset($connectionExceptions[spl_object_hash($cmdConnection)])) {
                continue;
            }

            try {
                $cmdConnection->writeCommand($command);
            } catch (CommunicationException $exception) {
                $connectionExceptions[spl_object_hash($cmdConnection)] = $exception;
            }
        }

        for ($i = 0; $i < $size; $i++) {
            $command = $commands->dequeue();

            $cmdConnection = $connection->getConnection($command);
            $connectionObjectHash = spl_object_hash($cmdConnection);

            if (isset($connectionExceptions[$connectionObjectHash])) {
                $values[$i] = $connectionExceptions[$connectionObjectHash];
                continue;
            }

            try {
                $response = $cmdConnection->readResponse($command);
                $values[$i] = $response instanceof \Iterator ? iterator_to_array($response) : $response;
            } catch (CommunicationException $exception) {
                $values[$i] = $exception;
                $connectionExceptions[$connectionObjectHash] = $exception;
            }
        }

        return $values;
    }
}
