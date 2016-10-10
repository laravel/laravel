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

use Predis\Connection\ComposableConnectionInterface;

/**
 * Interface that defines a response reader able to parse replies returned by
 * Redis and deserialize them to PHP objects.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ResponseReaderInterface
{
    /**
     * Reads replies from a connection to Redis and deserializes them.
     *
     * @param  ComposableConnectionInterface $connection Connection to Redis.
     * @return mixed
     */
    public function read(ComposableConnectionInterface $connection);
}
