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
class TextResponseReaderTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testDefaultHandlers()
    {
        $reader = new TextResponseReader();

        $this->assertInstanceOf('Predis\Protocol\Text\ResponseStatusHandler', $reader->getHandler('+'));
        $this->assertInstanceOf('Predis\Protocol\Text\ResponseErrorHandler', $reader->getHandler('-'));
        $this->assertInstanceOf('Predis\Protocol\Text\ResponseIntegerHandler', $reader->getHandler(':'));
        $this->assertInstanceOf('Predis\Protocol\Text\ResponseBulkHandler', $reader->getHandler('$'));
        $this->assertInstanceOf('Predis\Protocol\Text\ResponseMultiBulkHandler', $reader->getHandler('*'));

        $this->assertNull($reader->getHandler('!'));
    }

    /**
     * @group disconnected
     */
    public function testReplaceHandler()
    {
        $handler = $this->getMock('Predis\Protocol\ResponseHandlerInterface');

        $reader = new TextResponseReader();
        $reader->setHandler('+', $handler);

        $this->assertSame($handler, $reader->getHandler('+'));
    }

    /**
     * @group disconnected
     */
    public function testReadResponse()
    {
        $reader = new TextResponseReader();

        $protocol = new ComposableTextProtocol();
        $protocol->setReader($reader);

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->at(0))
                   ->method('readLine')
                   ->will($this->returnValue("+OK"));

        $connection->expects($this->at(1))
                   ->method('readLine')
                   ->will($this->returnValue("-ERR error message"));

        $connection->expects($this->at(2))
                   ->method('readLine')
                   ->will($this->returnValue(":2"));

        $connection->expects($this->at(3))
                   ->method('readLine')
                   ->will($this->returnValue("$-1"));

        $connection->expects($this->at(4))
                   ->method('readLine')
                   ->will($this->returnValue("*-1"));

        $this->assertTrue($reader->read($connection));
        $this->assertEquals("ERR error message", $reader->read($connection));
        $this->assertSame(2, $reader->read($connection));
        $this->assertNull($reader->read($connection));
        $this->assertNull($reader->read($connection));
    }

    /**
     * @group disconnected
     * @expectedException Predis\Protocol\ProtocolException
     * @expectedExceptionMessage Unexpected empty header
     */
    public function testEmptyResponseHeader()
    {
        $reader = new TextResponseReader();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->once())
                   ->method('readLine')
                   ->will($this->returnValue(''));

        $reader->read($connection);
    }
    /**
     * @group disconnected
     * @expectedException Predis\Protocol\ProtocolException
     * @expectedExceptionMessage Unknown prefix: '!'
     */
    public function testUnknownResponsePrefix()
    {
        $reader = new TextResponseReader();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->once())
                   ->method('readLine')
                   ->will($this->returnValue('!'));

        $reader->read($connection);
    }
}
