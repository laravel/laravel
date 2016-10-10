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
use Predis\Iterator\MultiBulkResponseSimple;
use Predis\Protocol\ProtocolException;
use Predis\Protocol\ResponseHandlerInterface;

/**
 * Implements a response handler for iterable multi-bulk replies using the
 * standard wire protocol defined by Redis.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseMultiBulkStreamHandler implements ResponseHandlerInterface
{
    /**
     * Handles a multi-bulk reply returned by Redis in a streamable fashion.
     *
     * @param  ComposableConnectionInterface $connection   Connection to Redis.
     * @param  string                        $lengthString Number of items in the multi-bulk reply.
     * @return MultiBulkResponseSimple
     */
    public function handle(ComposableConnectionInterface $connection, $lengthString)
    {
        $length = (int) $lengthString;

        if ("$length" != $lengthString) {
            CommunicationException::handle(new ProtocolException(
                $connection, "Cannot parse '$lengthString' as multi-bulk length"
            ));
        }

        return new MultiBulkResponseSimple($connection, $length);
    }
}
