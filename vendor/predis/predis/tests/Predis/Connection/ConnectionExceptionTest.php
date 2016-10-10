<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Connection;

require_once __DIR__.'/../CommunicationExceptionTest.php';

use Predis\CommunicationExceptionTest;

/**
 *
 */
class ConnectionExceptionTest extends CommunicationExceptionTest
{
    /**
     * {@inheritdoc}
     */
    protected function getException(SingleConnectionInterface $connection, $message, $code = 0, \Exception $inner = null)
    {
        return new ConnectionException($connection, $message, $code, $inner);
    }
}
