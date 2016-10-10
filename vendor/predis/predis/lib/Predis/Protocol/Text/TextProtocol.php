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
use Predis\ResponseError;
use Predis\ResponseQueued;
use Predis\Command\CommandInterface;
use Predis\Connection\ComposableConnectionInterface;
use Predis\Iterator\MultiBulkResponseSimple;
use Predis\Protocol\ProtocolException;
use Predis\Protocol\ProtocolInterface;

/**
 * Implements a protocol processor for the standard wire protocol defined by Redis.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class TextProtocol implements ProtocolInterface
{
    const NEWLINE = "\r\n";
    const OK      = 'OK';
    const ERROR   = 'ERR';
    const QUEUED  = 'QUEUED';
    const NULL    = 'nil';

    const PREFIX_STATUS     = '+';
    const PREFIX_ERROR      = '-';
    const PREFIX_INTEGER    = ':';
    const PREFIX_BULK       = '$';
    const PREFIX_MULTI_BULK = '*';

    const BUFFER_SIZE = 4096;

    private $mbiterable;
    private $serializer;

    /**
     *
     */
    public function __construct()
    {
        $this->mbiterable = false;
        $this->serializer = new TextCommandSerializer();
    }

    /**
     * {@inheritdoc}
     */
    public function write(ComposableConnectionInterface $connection, CommandInterface $command)
    {
        $connection->writeBytes($this->serializer->serialize($command));
    }

    /**
     * {@inheritdoc}
     */
    public function read(ComposableConnectionInterface $connection)
    {
        $chunk = $connection->readLine();
        $prefix = $chunk[0];
        $payload = substr($chunk, 1);

        switch ($prefix) {
            case '+':
                switch ($payload) {
                    case 'OK':
                        return true;

                    case 'QUEUED':
                        return new ResponseQueued();

                    default:
                        return $payload;
                }

            case '$':
                $size = (int) $payload;
                if ($size === -1) {
                    return null;
                }

                return substr($connection->readBytes($size + 2), 0, -2);

            case '*':
                $count = (int) $payload;

                if ($count === -1) {
                    return null;
                }
                if ($this->mbiterable) {
                    return new MultiBulkResponseSimple($connection, $count);
                }

                $multibulk = array();

                for ($i = 0; $i < $count; $i++) {
                    $multibulk[$i] = $this->read($connection);
                }

                return $multibulk;

            case ':':
                return (int) $payload;

            case '-':
                return new ResponseError($payload);

            default:
                CommunicationException::handle(new ProtocolException(
                    $connection, "Unknown prefix: '$prefix'"
                ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($option, $value)
    {
        switch ($option) {
            case 'iterable_multibulk':
                $this->mbiterable = (bool) $value;
                break;
        }
    }
}
