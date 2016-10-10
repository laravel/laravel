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
class TextCommandSerializerTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testSerializerIdWithNoArguments()
    {
        $serializer = new TextCommandSerializer();

        $command = $this->getMock('Predis\Command\CommandInterface');

        $command->expects($this->once())
                ->method('getId')
                ->will($this->returnValue('PING'));

        $command->expects($this->once())
                ->method('getArguments')
                ->will($this->returnValue(array()));

        $result = $serializer->serialize($command);

        $this->assertSame("*1\r\n$4\r\nPING\r\n", $result);
    }

    /**
     * @group disconnected
     */
    public function testSerializerIdWithArguments()
    {
        $serializer = new TextCommandSerializer();

        $command = $this->getMock('Predis\Command\CommandInterface');

        $command->expects($this->once())
                ->method('getId')
                ->will($this->returnValue('SET'));

        $command->expects($this->once())
                ->method('getArguments')
                ->will($this->returnValue(array('key', 'value')));

        $result = $serializer->serialize($command);

        $this->assertSame("*3\r\n$3\r\nSET\r\n$3\r\nkey\r\n$5\r\nvalue\r\n", $result);
    }
}
