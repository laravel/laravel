<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Connection;

use Predis\Command\CommandInterface;

/**
 * Defines a connection object used to communicate with a single Redis server.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface SingleConnectionInterface extends ConnectionInterface
{
    /**
     * Returns a string representation of the connection.
     *
     * @return string
     */
    public function __toString();

    /**
     * Returns the underlying resource used to communicate with a Redis server.
     *
     * @return mixed
     */
    public function getResource();

    /**
     * Gets the parameters used to initialize the connection object.
     *
     * @return ConnectionParametersInterface
     */
    public function getParameters();

    /**
     * Pushes the instance of a Redis command to the queue of commands executed
     * when the actual connection to a server is estabilished.
     *
     * @param CommandInterface $command Instance of a Redis command.
     */
    public function pushInitCommand(CommandInterface $command);

    /**
     * Reads a reply from the server.
     *
     * @return mixed
     */
    public function read();
}
