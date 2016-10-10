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

use Iterator;
use SplQueue;
use Predis\ResponseErrorInterface;
use Predis\ResponseObjectInterface;
use Predis\ServerException;
use Predis\Command\CommandInterface;
use Predis\Connection\ConnectionInterface;
use Predis\Connection\ReplicationConnectionInterface;

/**
 * Implements the standard pipeline executor strategy used
 * to write a list of commands and read their replies over
 * a connection to Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StandardExecutor implements PipelineExecutorInterface
{
    protected $exceptions;

    /**
     * @param bool $exceptions Specifies if the executor should throw exceptions on server errors.
     */
    public function __construct($exceptions = true)
    {
        $this->exceptions = (bool) $exceptions;
    }

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
     * Handles a response object.
     *
     * @param  ConnectionInterface     $connection
     * @param  CommandInterface        $command
     * @param  ResponseObjectInterface $response
     * @return mixed
     */
    protected function onResponseObject(ConnectionInterface $connection, CommandInterface $command, ResponseObjectInterface $response)
    {
        if ($response instanceof ResponseErrorInterface) {
            return $this->onResponseError($connection, $response);
        }

        if ($response instanceof Iterator) {
            return $command->parseResponse(iterator_to_array($response));
        }

        return $response;
    }

    /**
     * Handles -ERR responses returned by Redis.
     *
     * @param  ConnectionInterface    $connection The connection that returned the error.
     * @param  ResponseErrorInterface $response   The error response instance.
     * @return ResponseErrorInterface
     */
    protected function onResponseError(ConnectionInterface $connection, ResponseErrorInterface $response)
    {
        if (!$this->exceptions) {
            return $response;
        }

        // Force disconnection to prevent protocol desynchronization.
        $connection->disconnect();
        $message = $response->getMessage();

        throw new ServerException($message);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ConnectionInterface $connection, SplQueue $commands)
    {
        $this->checkConnection($connection);

        foreach ($commands as $command) {
            $connection->writeCommand($command);
        }

        $values = array();

        while (!$commands->isEmpty()) {
            $command = $commands->dequeue();
            $response = $connection->readResponse($command);

            if ($response instanceof ResponseObjectInterface) {
                $values[] = $this->onResponseObject($connection, $command, $response);
            } else {
                $values[] = $command->parseResponse($response);
            }
        }

        return $values;
    }
}
