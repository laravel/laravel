<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Cluster\Distribution;

use Predis\Cluster\Hash\HashGeneratorInterface;

/**
 * A distributor implements the logic to automatically distribute
 * keys among several nodes for client-side sharding.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface DistributionStrategyInterface
{
    /**
     * Adds a node to the distributor with an optional weight.
     *
     * @param mixed $node   Node object.
     * @param int   $weight Weight for the node.
     */
    public function add($node, $weight = null);

    /**
     * Removes a node from the distributor.
     *
     * @param mixed $node Node object.
     */
    public function remove($node);

    /**
     * Gets a node from the distributor using the computed hash of a key.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function get($key);

    /**
     * Returns the underlying hash generator instance.
     *
     * @return HashGeneratorInterface
     */
    public function getHashGenerator();
}
