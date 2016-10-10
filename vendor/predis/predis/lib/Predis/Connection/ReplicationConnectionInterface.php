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

/**
 * Defines a group of Redis servers in a master/slave replication configuration.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ReplicationConnectionInterface extends AggregatedConnectionInterface
{
    /**
     * Switches the internal connection object being used.
     *
     * @param string $connection Alias of a connection
     */
    public function switchTo($connection);

    /**
     * Retrieves the connection object currently being used.
     *
     * @return SingleConnectionInterface
     */
    public function getCurrent();

    /**
     * Retrieves the connection object to the master Redis server.
     *
     * @return SingleConnectionInterface
     */
    public function getMaster();

    /**
     * Retrieves a list of connection objects to slaves Redis servers.
     *
     * @return SingleConnectionInterface
     */
    public function getSlaves();
}
