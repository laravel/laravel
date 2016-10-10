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
class ResponseBulkHandlerTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testZeroLengthBulk()
    {
        $handler = new ResponseBulkHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->never())->method('readLine');
        $connection->expects($this->once())
                   ->method('readBytes')
                   ->with($this->equalTo(2))
                   ->will($this->returnValue("\r\n"));

        $this->assertSame('', $handler->handle($connection, '0'));
    }

    /**
     * @group disconnected
     */
    public function testBulk()
    {
        $bulk = "This is a bulk string.";
        $bulkLengh = strlen($bulk);

        $handler = new ResponseBulkHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->never())->method('readLine');
        $connection->expects($this->once())
                   ->method('readBytes')
                   ->with($this->equalTo($bulkLengh + 2))
                   ->will($this->returnValue("$bulk\r\n"));

        $this->assertSame($bulk, $handler->handle($connection, (string) $bulkLengh));
    }

    /**
     * @group disconnected
     */
    public function testNull()
    {
        $handler = new ResponseBulkHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->never())->method('readLine');
        $connection->expects($this->never())->method('readBytes');

        $this->assertNull($handler->handle($connection, '-1'));
    }

    /**
     * @group disconnected
     * @expectedException Predis\Protocol\ProtocolException
     * @expectedExceptionMessage Cannot parse 'invalid' as bulk length
     */
    public function testInvalidLength()
    {
        $handler = new ResponseBulkHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->never())->method('readLine');
        $connection->expects($this->never())->method('readBytes');

        $handler->handle($connection, 'invalid');
    }
}
