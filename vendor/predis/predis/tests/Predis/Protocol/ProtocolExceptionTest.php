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

require_once __DIR__.'/../CommunicationExceptionTest.php';

use Predis\CommunicationExceptionTest;
use Predis\Connection\SingleConnectionInterface;

/**
 *
 */
class ProtocolExceptionTest extends CommunicationExceptionTest
{
    /**
     * {@inheritdoc}
     */
    protected function getException(SingleConnectionInterface $connection, $message, $code = 0, \Exception $inner = null)
    {
        return new ProtocolException($connection, $message, $code, $inner);
    }
}
