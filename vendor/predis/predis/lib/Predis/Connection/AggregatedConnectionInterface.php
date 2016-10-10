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
 * Defines a virtual connection composed by multiple connection objects.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface AggregatedConnectionInterface extends ConnectionInterface
{
    /**
     * Adds a connection instance to the aggregated connection.
     *
     * @param SingleConnectionInterface $connection Instance of a connection.
     */
    public function add(SingleConnectionInterface $connection);

    /**
     * Removes the specified connection instance from the aggregated
     * connection.
     *
     * @param  SingleConnectionInterface $connection Instance of a connection.
     * @return bool                      Returns true if the connection was in the pool.
     */
    public function remove(SingleConnectionInterface $connection);

    /**
     * Gets the actual connection instance in charge of the specified command.
     *
     * @param  CommandInterface          $command Instance of a Redis command.
     * @return SingleConnectionInterface
     */
    public function getConnection(CommandInterface $command);

    /**
     * Retrieves a connection instance from the aggregated connection
     * using an alias.
     *
     * @param  string                    $connectionId Alias of a connection
     * @return SingleConnectionInterface
     */
    public function getConnectionById($connectionId);
}
