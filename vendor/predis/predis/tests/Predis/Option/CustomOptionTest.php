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
class CustomOptionTest extends PredisTestCase
{
    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     */
    public function testConstructorAcceptsOnlyCallablesForFilter()
    {
        $option = new CustomOption(array('filter' => new \stdClass()));
    }

    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     */
    public function testConstructorAcceptsOnlyCallablesForDefault()
    {
        $option = new CustomOption(array('default' => new \stdClass()));
    }

    /**
     * @group disconnected
     */
    public function testConstructorIgnoresUnrecognizedParameters()
    {
        $option = new CustomOption(array('unknown' => new \stdClass()));

        $this->assertNotNull($option);
    }

    /**
     * @group disconnected
     */
    public function testFilterWithoutCallbackReturnsValue()
    {
        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new CustomOption();

        $this->assertEquals('test', $option->filter($options, 'test'));
    }

    /**
     * @group disconnected
     */
    public function testDefaultWithoutCallbackReturnsNull()
    {
        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new CustomOption();

        $this->assertNull($option->getDefault($options));
    }

    /**
     * @group disconnected
     */
    public function testInvokeCallsFilterCallback()
    {
        $value = 'test';

        $options = $this->getMock('Predis\Option\ClientOptionsInterface');

        $filter = $this->getMock('stdClass', array('__invoke'));
        $filter->expects($this->once())
               ->method('__invoke')
               ->with($this->isInstanceOf('Predis\Option\ClientOptionsInterface'), $value)
               ->will($this->returnValue(true));

        $default = $this->getMock('stdClass', array('__invoke'));
        $default->expects($this->never())->method('__invoke');

        $option = new CustomOption(array('filter' => $filter, 'default' => $default));

        $this->assertTrue($option($options, $value));
    }

    /**
     * @group disconnected
     */
    public function testInvokeCallsDefaultCallback()
    {
        $options = $this->getMock('Predis\Option\ClientOptionsInterface');

        $filter = $this->getMock('stdClass', array('__invoke'));
        $filter->expects($this->never())->method('__invoke');

        $default = $this->getMock('stdClass', array('__invoke'));
        $default->expects($this->once())
                ->method('__invoke')
                ->with($this->isInstanceOf('Predis\Option\ClientOptionsInterface'))
                ->will($this->returnValue(true));

        $option = new CustomOption(array('filter' => $filter, 'default' => $default));

        $this->assertTrue($option($options, null));
    }
}
