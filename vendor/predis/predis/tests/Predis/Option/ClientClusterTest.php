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
class ClientClusterTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testValidationAcceptsFQNStringAsInitializer()
    {
        $clusterClass = get_class($this->getMock('Predis\Connection\ClusterConnectionInterface'));

        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientCluster();

        $cluster = $option->filter($options, $clusterClass);

        $this->assertInstanceOf('Predis\Connection\ClusterConnectionInterface', $cluster);
    }

    /**
     * @group disconnected
     */
    public function testValidationRecognizesCertainPredefinedShortNames()
    {
        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientCluster();

        $cluster = $option->filter($options, 'predis');

        $this->assertInstanceOf('Predis\Connection\ClusterConnectionInterface', $cluster);
    }

    /**
     * @group disconnected
     */
    public function testValidationAcceptsCallableObjectAsInitializers()
    {
        $value = $this->getMock('Predis\Connection\ClusterConnectionInterface');

        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientCluster();

        $initializer = $this->getMock('stdClass', array('__invoke'));
        $initializer->expects($this->once())
                    ->method('__invoke')
                    ->with($this->isInstanceOf('Predis\Option\ClientOptionsInterface'), $option)
                    ->will($this->returnValue($value));

        $cluster = $option->filter($options, $initializer, $option);

        $this->assertInstanceOf('Predis\Connection\ClusterConnectionInterface', $cluster);
        $this->assertSame($value, $cluster);
    }

    /**
     * @group disconnected
     */
    public function testValidationThrowsExceptionOnInvalidClassTypes()
    {
        $this->setExpectedException('InvalidArgumentException');

        $connectionClass = get_class($this->getMock('Predis\Connection\SingleConnectionInterface'));
        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientCluster();

        $option->filter($options, $connectionClass);
    }

    /**
     * @group disconnected
     */
    public function testValidationThrowsExceptionOnInvalidShortName()
    {
        $this->setExpectedException('InvalidArgumentException');

        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientCluster();

        $option->filter($options, 'unknown');
    }

    /**
     * @group disconnected
     */
    public function testValidationThrowsExceptionOnInvalidObjectReturnedByCallback()
    {
        $this->setExpectedException('InvalidArgumentException');

        $value = function ($options) {
            return new \stdClass();
        };

        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientCluster();

        $option->filter($options, $value);
    }

    /**
     * @group disconnected
     */
    public function testValidationThrowsExceptionOnInvalidArguments()
    {
        $this->setExpectedException('InvalidArgumentException');

        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientCluster();

        $option->filter($options, new \stdClass());
    }
}
