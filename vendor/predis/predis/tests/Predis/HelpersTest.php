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
class HelpersTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testOnCommunicationException()
    {
        $this->setExpectedException('Predis\CommunicationException');

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->once())->method('isConnected')->will($this->returnValue(true));
        $connection->expects($this->once())->method('disconnect');

        $exception = $this->getMockForAbstractClass('Predis\CommunicationException', array($connection));

        Helpers::onCommunicationException($exception);
    }

    /**
     * @group disconnected
     */
    public function testFilterArrayArguments()
    {
        $arguments = array('arg1', 'arg2', 'arg3', 'arg4');

        $this->assertSame($arguments, Helpers::filterArrayArguments($arguments));
        $this->assertSame($arguments, Helpers::filterArrayArguments(array($arguments)));

        $arguments = array(array(), array());
        $this->assertSame($arguments, Helpers::filterArrayArguments($arguments));

        $arguments = array(new \stdClass());
        $this->assertSame($arguments, Helpers::filterArrayArguments($arguments));
    }

    /**
     * @group disconnected
     */
    public function testFilterVariadicValues()
    {
        $arguments = array('key', 'value1', 'value2', 'value3');

        $this->assertSame($arguments, Helpers::filterVariadicValues($arguments));
        $this->assertSame($arguments, Helpers::filterVariadicValues(array('key', array('value1', 'value2', 'value3'))));

        $arguments = array(array(), array());
        $this->assertSame($arguments, Helpers::filterArrayArguments($arguments));

        $arguments = array(new \stdClass());
        $this->assertSame($arguments, Helpers::filterArrayArguments($arguments));
    }
}
