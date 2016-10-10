<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Cluster\Hash;

/**
 * A generator of node keys implements the logic used to calculate the hash of
 * a key to distribute the respective operations among nodes.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface HashGeneratorInterface
{
    /**
     * Generates an hash that is used by the distributor algorithm
     *
     * @param  string $value Value used to generate the hash.
     * @return int
     */
    public function hash($value);
}
