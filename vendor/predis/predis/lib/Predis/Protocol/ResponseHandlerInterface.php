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
 * Interface that defines an handler able to parse a reply.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ResponseHandlerInterface
{
    /**
     * Parses a type of reply returned by Redis and reads more data from the
     * connection if needed.
     *
     * @param  ComposableConnectionInterface $connection Connection to Redis.
     * @param  string                        $payload    Initial payload of the reply.
     * @return mixed
     */
    public function handle(ComposableConnectionInterface $connection, $payload);
}
