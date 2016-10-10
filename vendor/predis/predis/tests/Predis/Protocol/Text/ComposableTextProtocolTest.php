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
class ComposableTextProtocolTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testCustomSerializer()
    {
        $serializer = $this->getMock('Predis\Protocol\CommandSerializerInterface');

        $protocol = new ComposableTextProtocol();
        $protocol->setSerializer($serializer);

        $this->assertSame($serializer, $protocol->getSerializer());
    }

    /**
     * @group disconnected
     */
    public function testCustomReader()
    {
        $reader = $this->getMock('Predis\Protocol\ResponseReaderInterface');

        $protocol = new ComposableTextProtocol();
        $protocol->setReader($reader);

        $this->assertSame($reader, $protocol->getReader());
    }

    /**
     * @group disconnected
     */
    public function testConnectionWrite()
    {
        $serialized = "*1\r\n$4\r\nPING\r\n";

        $command = $this->getMock('Predis\Command\CommandInterface');
        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');
        $serializer = $this->getMock('Predis\Protocol\CommandSerializerInterface');

        $protocol = new ComposableTextProtocol();
        $protocol->setSerializer($serializer);

        $connection->expects($this->once())
                   ->method('writeBytes')
                   ->with($this->equalTo($serialized));

        $serializer->expects($this->once())
                   ->method('serialize')
                   ->with($command)
                   ->will($this->returnValue($serialized));

        $protocol->write($connection, $command);
    }

    /**
     * @group disconnected
     */
    public function testConnectionRead()
    {
        $serialized = "*1\r\n$4\r\nPING\r\n";

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');
        $reader = $this->getMock('Predis\Protocol\ResponseReaderInterface');

        $protocol = new ComposableTextProtocol();
        $protocol->setReader($reader);

        $reader->expects($this->once())
                   ->method('read')
                   ->with($connection)
                   ->will($this->returnValue('bulk'));

        $this->assertSame('bulk', $protocol->read($connection));
    }

    /**
     * @group disconnected
     */
    public function testSetMultibulkOption()
    {
        $protocol = new ComposableTextProtocol();
        $reader = $protocol->getReader();

        $protocol->setOption('iterable_multibulk', true);
        $this->assertInstanceOf('Predis\Protocol\Text\ResponseMultiBulkStreamHandler', $reader->getHandler('*'));

        $protocol->setOption('iterable_multibulk', false);
        $this->assertInstanceOf('Predis\Protocol\Text\ResponseMultiBulkHandler', $reader->getHandler('*'));
    }

    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The option unknown_option is not supported by the current protocol
     */
    public function testSetInvalidOption()
    {
        $protocol = new ComposableTextProtocol();
        $protocol->setOption('unknown_option', true);
    }
}
