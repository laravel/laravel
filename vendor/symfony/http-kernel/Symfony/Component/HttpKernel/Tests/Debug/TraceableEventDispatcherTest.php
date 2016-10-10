<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\Debug;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;

class TraceableEventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testAddRemoveListener()
    {
        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());

        $tdispatcher->addListener('foo', $listener = function () {; });
        $listeners = $dispatcher->getListeners('foo');
        $this->assertCount(1, $listeners);
        $this->assertSame($listener, $listeners[0]);

        $tdispatcher->removeListener('foo', $listener);
        $this->assertCount(0, $dispatcher->getListeners('foo'));
    }

    public function testGetListeners()
    {
        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());

        $tdispatcher->addListener('foo', $listener = function () {; });
        $this->assertSame($dispatcher->getListeners('foo'), $tdispatcher->getListeners('foo'));
    }

    public function testHasListeners()
    {
        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());

        $this->assertFalse($dispatcher->hasListeners('foo'));
        $this->assertFalse($tdispatcher->hasListeners('foo'));

        $tdispatcher->addListener('foo', $listener = function () {; });
        $this->assertTrue($dispatcher->hasListeners('foo'));
        $this->assertTrue($tdispatcher->hasListeners('foo'));
    }

    public function testAddRemoveSubscriber()
    {
        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());

        $subscriber = new EventSubscriber();

        $tdispatcher->addSubscriber($subscriber);
        $listeners = $dispatcher->getListeners('foo');
        $this->assertCount(1, $listeners);
        $this->assertSame(array($subscriber, 'call'), $listeners[0]);

        $tdispatcher->removeSubscriber($subscriber);
        $this->assertCount(0, $dispatcher->getListeners('foo'));
    }

    public function testGetCalledListeners()
    {
        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());
        $tdispatcher->addListener('foo', $listener = function () {; });

        $this->assertEquals(array(), $tdispatcher->getCalledListeners());
        $this->assertEquals(array('foo.closure' => array('event' => 'foo', 'type' => 'Closure', 'pretty' => 'closure')), $tdispatcher->getNotCalledListeners());

        $tdispatcher->dispatch('foo');

        $this->assertEquals(array('foo.closure' => array('event' => 'foo', 'type' => 'Closure', 'pretty' => 'closure')), $tdispatcher->getCalledListeners());
        $this->assertEquals(array(), $tdispatcher->getNotCalledListeners());
    }

    public function testLogger()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch(), $logger);
        $tdispatcher->addListener('foo', $listener1 = function () {; });
        $tdispatcher->addListener('foo', $listener2 = function () {; });

        $logger->expects($this->at(0))->method('debug')->with("Notified event \"foo\" to listener \"closure\".");
        $logger->expects($this->at(1))->method('debug')->with("Notified event \"foo\" to listener \"closure\".");

        $tdispatcher->dispatch('foo');
    }

    public function testLoggerWithStoppedEvent()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch(), $logger);
        $tdispatcher->addListener('foo', $listener1 = function (Event $event) { $event->stopPropagation(); });
        $tdispatcher->addListener('foo', $listener2 = function () {; });

        $logger->expects($this->at(0))->method('debug')->with("Notified event \"foo\" to listener \"closure\".");
        $logger->expects($this->at(1))->method('debug')->with("Listener \"closure\" stopped propagation of the event \"foo\".");
        $logger->expects($this->at(2))->method('debug')->with("Listener \"closure\" was not called for event \"foo\".");

        $tdispatcher->dispatch('foo');
    }

    public function testDispatchCallListeners()
    {
        $called = array();

        $dispatcher = new EventDispatcher();
        $tdispatcher = new TraceableEventDispatcher($dispatcher, new Stopwatch());
        $tdispatcher->addListener('foo', $listener1 = function () use (&$called) { $called[] = 'foo1'; });
        $tdispatcher->addListener('foo', $listener2 = function () use (&$called) { $called[] = 'foo2'; });

        $tdispatcher->dispatch('foo');

        $this->assertEquals(array('foo1', 'foo2'), $called);
    }

    public function testDispatchNested()
    {
        $dispatcher = new TraceableEventDispatcher(new EventDispatcher(), new Stopwatch());
        $loop = 1;
        $dispatcher->addListener('foo', $listener1 = function () use ($dispatcher, &$loop) {
            ++$loop;
            if (2 == $loop) {
                $dispatcher->dispatch('foo');
            }
        });

        $dispatcher->dispatch('foo');
    }

    public function testDispatchReusedEventNested()
    {
        $nestedCall = false;
        $dispatcher = new TraceableEventDispatcher(new EventDispatcher(), new Stopwatch());
        $dispatcher->addListener('foo', function (Event $e) use ($dispatcher) {
            $dispatcher->dispatch('bar', $e);
        });
        $dispatcher->addListener('bar', function (Event $e) use (&$nestedCall) {
            $nestedCall = true;
        });

        $this->assertFalse($nestedCall);
        $dispatcher->dispatch('foo');
        $this->assertTrue($nestedCall);
    }

    public function testStopwatchSections()
    {
        $dispatcher = new TraceableEventDispatcher(new EventDispatcher(), $stopwatch = new Stopwatch());
        $kernel = $this->getHttpKernel($dispatcher, function () { return new Response(); });
        $request = Request::create('/');
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        $events = $stopwatch->getSectionEvents($response->headers->get('X-Debug-Token'));
        $this->assertEquals(array(
            '__section__',
            'kernel.request',
            'kernel.request.loading',
            'kernel.controller',
            'kernel.controller.loading',
            'controller',
            'kernel.response',
            'kernel.response.loading',
            'kernel.terminate',
            'kernel.terminate.loading',
        ), array_keys($events));
    }

    public function testStopwatchCheckControllerOnRequestEvent()
    {
        $stopwatch = $this->getMockBuilder('Symfony\Component\Stopwatch\Stopwatch')
            ->setMethods(array('isStarted'))
            ->getMock();
        $stopwatch->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(false));

        $dispatcher = new TraceableEventDispatcher(new EventDispatcher(), $stopwatch);

        $kernel = $this->getHttpKernel($dispatcher, function () { return new Response(); });
        $request = Request::create('/');
        $kernel->handle($request);
    }

    public function testStopwatchStopControllerOnRequestEvent()
    {
        $stopwatch = $this->getMockBuilder('Symfony\Component\Stopwatch\Stopwatch')
            ->setMethods(array('isStarted', 'stop', 'stopSection'))
            ->getMock();
        $stopwatch->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(true));
        $stopwatch->expects($this->once())
            ->method('stop');
        $stopwatch->expects($this->once())
            ->method('stopSection');

        $dispatcher = new TraceableEventDispatcher(new EventDispatcher(), $stopwatch);

        $kernel = $this->getHttpKernel($dispatcher, function () { return new Response(); });
        $request = Request::create('/');
        $kernel->handle($request);
    }

    protected function getHttpKernel($dispatcher, $controller)
    {
        $resolver = $this->getMock('Symfony\Component\HttpKernel\Controller\ControllerResolverInterface');
        $resolver->expects($this->once())->method('getController')->will($this->returnValue($controller));
        $resolver->expects($this->once())->method('getArguments')->will($this->returnValue(array()));

        return new HttpKernel($dispatcher, $resolver);
    }
}

class EventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array('foo' => 'call');
    }
}
