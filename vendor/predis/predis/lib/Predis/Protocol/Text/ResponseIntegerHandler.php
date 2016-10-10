<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Protocol\Text;

use Predis\CommunicationException;
use Predis\Connection\ComposableConnectionInterface;
use Predis\Protocol\ProtocolException;
use Predis\Protocol\ResponseHandlerInterface;

/**
 * Implements a response handler for integer replies using the standard wire
 * protocol defined by Redis.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseIntegerHandler implements ResponseHandlerInterface
{
    /**
     * Handles an integer reply returned by Redis.
     *
     * @param  ComposableConnectionInterface $connection Connection to Redis.
     * @param  string                        $number     String representation of an integer.
     * @return int
     */
    public function handle(ComposableConnectionInterface $connection, $number)
    {
        if (is_numeric($number)) {
            return (int) $number;
        }

        if ($number !== 'nil') {
            CommunicationException::handle(new ProtocolException(
                $connection, "Cannot parse '$number' as numeric response"
            ));
        }

        return null;
    }
}
