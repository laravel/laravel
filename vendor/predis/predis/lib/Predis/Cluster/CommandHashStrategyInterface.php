<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Cluster;

use Predis\Command\CommandInterface;

/**
 * Interface for classes defining the strategy used to calculate an hash
 * out of keys extracted from supported commands.
 *
 * This is mostly useful to support clustering via client-side sharding.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface CommandHashStrategyInterface
{
    /**
     * Returns the hash for the given command using the specified algorithm, or null
     * if the command cannot be hashed.
     *
     * @param  CommandInterface $command Command to be hashed.
     * @return int
     */
    public function getHash(CommandInterface $command);

    /**
     * Returns the hash for the given key using the specified algorithm.
     *
     * @param  string $key Key to be hashed.
     * @return string
     */
    public function getKeyHash($key);
}
