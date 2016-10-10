<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Tests\Firewall;

use Symfony\Component\Security\Http\Firewall\ChannelListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;

class ChannelListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleWithNotSecuredRequestAndHttpChannel()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false, false);
        $request
            ->expects($this->any())
            ->method('isSecure')
            ->will($this->returnValue(false))
        ;

        $accessMap = $this->getMock('Symfony\Component\Security\Http\AccessMapInterface');
        $accessMap
            ->expects($this->any())
            ->method('getPatterns')
            ->with($this->equalTo($request))
            ->will($this->returnValue(array(array(), 'http')))
        ;

        $entryPoint = $this->getMock('Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface');
        $entryPoint
            ->expects($this->never())
            ->method('start')
        ;

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;
        $event
            ->expects($this->never())
            ->method('setResponse')
        ;

        $listener = new ChannelListener($accessMap, $entryPoint);
        $listener->handle($event);
    }

    public function testHandleWithSecuredRequestAndHttpsChannel()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false, false);
        $request
            ->expects($this->any())
            ->method('isSecure')
            ->will($this->returnValue(true))
        ;

        $accessMap = $this->getMock('Symfony\Component\Security\Http\AccessMapInterface');
        $accessMap
            ->expects($this->any())
            ->method('getPatterns')
            ->with($this->equalTo($request))
            ->will($this->returnValue(array(array(), 'https')))
        ;

        $entryPoint = $this->getMock('Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface');
        $entryPoint
            ->expects($this->never())
            ->method('start')
        ;

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;
        $event
            ->expects($this->never())
            ->method('setResponse')
        ;

        $listener = new ChannelListener($accessMap, $entryPoint);
        $listener->handle($event);
    }

    public function testHandleWithNotSecuredRequestAndHttpsChannel()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false, false);
        $request
            ->expects($this->any())
            ->method('isSecure')
            ->will($this->returnValue(false))
        ;

        $response = new Response();

        $accessMap = $this->getMock('Symfony\Component\Security\Http\AccessMapInterface');
        $accessMap
            ->expects($this->any())
            ->method('getPatterns')
            ->with($this->equalTo($request))
            ->will($this->returnValue(array(array(), 'https')))
        ;

        $entryPoint = $this->getMock('Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface');
        $entryPoint
            ->expects($this->once())
            ->method('start')
            ->with($this->equalTo($request))
            ->will($this->returnValue($response))
        ;

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;
        $event
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->equalTo($response))
        ;

        $listener = new ChannelListener($accessMap, $entryPoint);
        $listener->handle($event);
    }

    public function testHandleWithSecuredRequestAndHttpChannel()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false, false);
        $request
            ->expects($this->any())
            ->method('isSecure')
            ->will($this->returnValue(true))
        ;

        $response = new Response();

        $accessMap = $this->getMock('Symfony\Component\Security\Http\AccessMapInterface');
        $accessMap
            ->expects($this->any())
            ->method('getPatterns')
            ->with($this->equalTo($request))
            ->will($this->returnValue(array(array(), 'http')))
        ;

        $entryPoint = $this->getMock('Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface');
        $entryPoint
            ->expects($this->once())
            ->method('start')
            ->with($this->equalTo($request))
            ->will($this->returnValue($response))
        ;

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;
        $event
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->equalTo($response))
        ;

        $listener = new ChannelListener($accessMap, $entryPoint);
        $listener->handle($event);
    }
}
