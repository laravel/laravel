<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Protocol;

use Predis\Command\CommandInterface;
use Predis\Connection\ComposableConnectionInterface;

/**
 * Interface that defines a protocol processor that serializes Redis commands
 * and parses replies returned by the server to PHP objects.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ProtocolInterface extends ResponseReaderInterface
{
    /**
     * Writes a Redis command on the specified connection.
     *
     * @param ComposableConnectionInterface $connection Connection to Redis.
     * @param CommandInterface              $command    Redis command.
     */
    public function write(ComposableConnectionInterface $connection, CommandInterface $command);

    /**
     * Sets the options for the protocol processor.
     *
     * @param string $option Name of the option.
     * @param mixed  $value  Value of the option.
     */
    public function setOption($option, $value);
}
