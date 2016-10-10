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
 * Implements a response handler for bulk replies using the standard wire
 * protocol defined by Redis.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseBulkHandler implements ResponseHandlerInterface
{
    /**
     * Handles a bulk reply returned by Redis.
     *
     * @param  ComposableConnectionInterface $connection   Connection to Redis.
     * @param  string                        $lengthString Bytes size of the bulk reply.
     * @return string
     */
    public function handle(ComposableConnectionInterface $connection, $lengthString)
    {
        $length = (int) $lengthString;

        if ("$length" !== $lengthString) {
            CommunicationException::handle(new ProtocolException(
                $connection, "Cannot parse '$lengthString' as bulk length"
            ));
        }

        if ($length >= 0) {
            return substr($connection->readBytes($length + 2), 0, -2);
        }

        if ($length == -1) {
            return null;
        }
    }
}
