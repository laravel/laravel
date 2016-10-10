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
class ResponseMultiBulkHandlerTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testMultiBulk()
    {
        $handler = new ResponseMultiBulkHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->once())
                   ->method('getProtocol')
                   ->will($this->returnValue(new ComposableTextProtocol()));

        $connection->expects($this->at(1))
                   ->method('readLine')
                   ->will($this->returnValue("$3"));

        $connection->expects($this->at(2))
                   ->method('readBytes')
                   ->will($this->returnValue("foo\r\n"));

        $connection->expects($this->at(3))
                   ->method('readLine')
                   ->will($this->returnValue("$3"));

        $connection->expects($this->at(4))
                   ->method('readBytes')
                   ->will($this->returnValue("bar\r\n"));

        $this->assertSame(array('foo', 'bar'), $handler->handle($connection, '2'));
    }

    /**
     * @group disconnected
     */
    public function testNull()
    {
        $handler = new ResponseMultiBulkHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->never())->method('readLine');
        $connection->expects($this->never())->method('readBytes');

        $this->assertNull($handler->handle($connection, '-1'));
    }

    /**
     * @group disconnected
     * @expectedException Predis\Protocol\ProtocolException
     * @expectedExceptionMessage Cannot parse 'invalid' as multi-bulk length
     */
    public function testInvalid()
    {
        $handler = new ResponseMultiBulkHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->never())->method('readLine');
        $connection->expects($this->never())->method('readBytes');

        $handler->handle($connection, 'invalid');
    }
}
