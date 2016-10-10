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

use PredisTestCase;

/**
 *
 */
class ResponseErrorHandlerTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testOk()
    {
        $handler = new ResponseErrorHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->never())->method('readLine');
        $connection->expects($this->never())->method('readBytes');

        $message = "ERR Operation against a key holding the wrong kind of value";
        $response = $handler->handle($connection, $message);

        $this->assertInstanceOf('Predis\ResponseError', $response);
        $this->assertSame($message, $response->getMessage());
    }
}
