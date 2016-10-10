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
class TextProtocolTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testConnectionWrite()
    {
        $serialized = "*1\r\n$4\r\nPING\r\n";
        $protocol = new TextProtocol();

        $command = $this->getMock('Predis\Command\CommandInterface');

        $command->expects($this->once())
                ->method('getId')
                ->will($this->returnValue('PING'));

        $command->expects($this->once())
                ->method('getArguments')
                ->will($this->returnValue(array()));

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->once())
                   ->method('writeBytes')
                   ->with($this->equalTo($serialized));

        $protocol->write($connection, $command);
    }

    /**
     * @todo Improve test coverage
     * @group disconnected
     */
    public function testConnectionRead()
    {
        $protocol = new TextProtocol();

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

        $this->assertTrue($protocol->read($connection));
        $this->assertEquals("ERR error message", $protocol->read($connection));
        $this->assertSame(2, $protocol->read($connection));
        $this->assertNull($protocol->read($connection));
        $this->assertNull($protocol->read($connection));
    }

    /**
     * @group disconnected
     */
    public function testIterableMultibulkSupport()
    {
        $protocol = new TextProtocol();
        $protocol->setOption('iterable_multibulk', true);

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->once(4))
                   ->method('readLine')
                   ->will($this->returnValue("*1"));

        $this->assertInstanceOf('Predis\Iterator\MultiBulkResponseSimple', $protocol->read($connection));
    }

    /**
     * @group disconnected
     * @expectedException Predis\Protocol\ProtocolException
     * @expectedExceptionMessage Unknown prefix: '!'
     */
    public function testUnknownResponsePrefix()
    {
        $protocol = new TextProtocol();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->once())
                   ->method('readLine')
                   ->will($this->returnValue('!'));

        $protocol->read($connection);
    }
}
