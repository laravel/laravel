<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis;

use PredisTestCase;

/**
 *
 */
class ClientExceptionTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testExceptionMessage()
    {
        $message = 'This is a client exception.';

        $this->setExpectedException('Predis\ClientException', $message);

        throw new ClientException($message);
    }

    /**
     * @group disconnected
     */
    public function testExceptionClass()
    {
        $exception = new ClientException();

        $this->assertInstanceOf('Predis\ClientException', $exception);
        $this->assertInstanceOf('Predis\PredisException', $exception);
    }
}
