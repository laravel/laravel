<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher\Tests;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ImmutableEventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $innerDispatcher;

    /**
     * @var ImmutableEventDispatcher
     */
    private $dispatcher;

    protected function setUp()
    {
        $this->innerDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->dispatcher = new ImmutableEventDispatcher($this->innerDispatcher);
    }

    public function testDispatchDelegates()
    {
        $event = new Event();

        $this->innerDispatcher->expects($this->once())
            ->method('dispatch')
            ->with('event', $event)
            ->will($this->returnValue('result'));

        $this->assertSame('result', $this->dispatcher->dispatch('event', $event));
    }

    public function testGetListenersDelegates()
    {
        $this->innerDispatcher->expects($this->once())
            ->method('getListeners')
            ->with('event')
            ->will($this->returnValue('result'));

        $this->assertSame('result', $this->dispatcher->getListeners('event'));
    }

    public function testHasListenersDelegates()
    {
        $this->innerDispatcher->expects($this->once())
            ->method('hasListeners')
            ->with('event')
            ->will($this->returnValue('result'));

        $this->assertSame('result', $this->dispatcher->hasListeners('event'));
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testAddListenerDisallowed()
    {
        $this->dispatcher->addListener('event', function () { return 'foo'; });
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testAddSubscriberDisallowed()
    {
        $subscriber = $this->getMock('Symfony\Component\EventDispatcher\EventSubscriberInterface');

        $this->dispatcher->addSubscriber($subscriber);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testRemoveListenerDisallowed()
    {
        $this->dispatcher->removeListener('event', function () { return 'foo'; });
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testRemoveSubscriberDisallowed()
    {
        $subscriber = $this->getMock('Symfony\Component\EventDispatcher\EventSubscriberInterface');

        $this->dispatcher->removeSubscriber($subscriber);
    }
}
