<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Option;

use Predis\Connection\MasterSlaveReplication;
use Predis\Connection\ReplicationConnectionInterface;

/**
 * Option class that returns a replication connection be used by a client.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ClientReplication extends AbstractOption
{
    /**
     * Checks if the specified value is a valid instance of ReplicationConnectionInterface.
     *
     * @param  ReplicationConnectionInterface $connection Instance of a replication connection.
     * @return ReplicationConnectionInterface
     */
    protected function checkInstance($connection)
    {
        if (!$connection instanceof ReplicationConnectionInterface) {
            throw new \InvalidArgumentException('Instance of Predis\Connection\ReplicationConnectionInterface expected');
        }

        return $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(ClientOptionsInterface $options, $value)
    {
        if (is_callable($value)) {
            $connection = call_user_func($value, $options, $this);

            if (!$connection instanceof ReplicationConnectionInterface) {
                throw new \InvalidArgumentException('Instance of Predis\Connection\ReplicationConnectionInterface expected');
            }

            return $connection;
        }

        if (is_string($value)) {
            if (!class_exists($value)) {
                throw new \InvalidArgumentException("Class $value does not exist");
            }

            if (!($connection = new $value()) instanceof ReplicationConnectionInterface) {
                throw new \InvalidArgumentException('Instance of Predis\Connection\ReplicationConnectionInterface expected');
            }

            return $connection;
        }

        if ($value == true) {
            return $this->getDefault($options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(ClientOptionsInterface $options)
    {
        return new MasterSlaveReplication();
    }
}
