<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Option;

use PredisTestCase;

/**
 *
 */
class ClientReplicationTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testValidationAcceptsFQNStringAsInitializer()
    {
        $replicationClass = get_class($this->getMock('Predis\Connection\ReplicationConnectionInterface'));

        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientReplication();

        $replication = $option->filter($options, $replicationClass);

        $this->assertInstanceOf('Predis\Connection\ReplicationConnectionInterface', $replication);
    }

    /**
     * @group disconnected
     */
    public function testValidationAcceptsBooleanValue()
    {
        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientReplication();

        $replication = $option->filter($options, true);
        $this->assertInstanceOf('Predis\Connection\ReplicationConnectionInterface', $replication);

        $replication = $option->filter($options, false);
        $this->assertNull($replication);
    }

    /**
     * @group disconnected
     */
    public function testValidationAcceptsCallableObjectAsInitializers()
    {
        $value = $this->getMock('Predis\Connection\ReplicationConnectionInterface');

        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientReplication();

        $initializer = $this->getMock('stdClass', array('__invoke'));
        $initializer->expects($this->once())
                    ->method('__invoke')
                    ->with($this->isInstanceOf('Predis\Option\ClientOptionsInterface'), $option)
                    ->will($this->returnValue($value));

        $replication = $option->filter($options, $initializer, $option);

        $this->assertInstanceOf('Predis\Connection\ReplicationConnectionInterface', $replication);
        $this->assertSame($value, $replication);
    }

    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     */
    public function testValidationThrowsExceptionOnInvalidObjectReturnedByCallback()
    {
        $value = function ($options) {
            return new \stdClass();
        };

        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientReplication();

        $option->filter($options, $value);
    }

    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     */
    public function testValidationThrowsExceptionOnInvalidClassTypes()
    {
        $connectionClass = get_class($this->getMock('Predis\Connection\SingleConnectionInterface'));
        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientReplication();

        $option->filter($options, $connectionClass);
    }
}
