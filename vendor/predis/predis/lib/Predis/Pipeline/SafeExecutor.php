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
 * Implements a pipeline executor strategy that does not fail when an error is
 * encountered, but adds the returned error in the replies array.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SafeExecutor implements PipelineExecutorInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(ConnectionInterface $connection, SplQueue $commands)
    {
        $size = count($commands);
        $values = array();

        foreach ($commands as $command) {
            try {
                $connection->writeCommand($command);
            } catch (CommunicationException $exception) {
                return array_fill(0, $size, $exception);
            }
        }

        for ($i = 0; $i < $size; $i++) {
            $command = $commands->dequeue();

            try {
                $response = $connection->readResponse($command);
                $values[$i] = $response instanceof \Iterator ? iterator_to_array($response) : $response;
            } catch (CommunicationException $exception) {
                $toAdd = count($commands) - count($values);
                $values = array_merge($values, array_fill(0, $toAdd, $exception));
                break;
            }
        }

        return $values;
    }
}
