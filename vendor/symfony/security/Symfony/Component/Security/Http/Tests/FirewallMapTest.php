<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Tests;

use Symfony\Component\Security\Http\FirewallMap;
use Symfony\Component\HttpFoundation\Request;

class FirewallMapTest extends \PHPUnit_Framework_TestCase
{
    public function testGetListeners()
    {
        $map = new FirewallMap();

        $request = new Request();

        $notMatchingMatcher = $this->getMock('Symfony\Component\HttpFoundation\RequestMatcher');
        $notMatchingMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($this->equalTo($request))
            ->will($this->returnValue(false))
        ;

        $map->add($notMatchingMatcher, array($this->getMock('Symfony\Component\Security\Http\Firewall\ListenerInterface')));

        $matchingMatcher = $this->getMock('Symfony\Component\HttpFoundation\RequestMatcher');
        $matchingMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($this->equalTo($request))
            ->will($this->returnValue(true))
        ;
        $theListener = $this->getMock('Symfony\Component\Security\Http\Firewall\ListenerInterface');
        $theException = $this->getMock('Symfony\Component\Security\Http\Firewall\ExceptionListener', array(), array(), '', false);

        $map->add($matchingMatcher, array($theListener), $theException);

        $tooLateMatcher = $this->getMock('Symfony\Component\HttpFoundation\RequestMatcher');
        $tooLateMatcher
            ->expects($this->never())
            ->method('matches')
        ;

        $map->add($tooLateMatcher, array($this->getMock('Symfony\Component\Security\Http\Firewall\ListenerInterface')));

        list($listeners, $exception) = $map->getListeners($request);

        $this->assertEquals(array($theListener), $listeners);
        $this->assertEquals($theException, $exception);
    }

    public function testGetListenersWithAnEntryHavingNoRequestMatcher()
    {
        $map = new FirewallMap();

        $request = new Request();

        $notMatchingMatcher = $this->getMock('Symfony\Component\HttpFoundation\RequestMatcher');
        $notMatchingMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($this->equalTo($request))
            ->will($this->returnValue(false))
        ;

        $map->add($notMatchingMatcher, array($this->getMock('Symfony\Component\Security\Http\Firewall\ListenerInterface')));

        $theListener = $this->getMock('Symfony\Component\Security\Http\Firewall\ListenerInterface');
        $theException = $this->getMock('Symfony\Component\Security\Http\Firewall\ExceptionListener', array(), array(), '', false);

        $map->add(null, array($theListener), $theException);

        $tooLateMatcher = $this->getMock('Symfony\Component\HttpFoundation\RequestMatcher');
        $tooLateMatcher
            ->expects($this->never())
            ->method('matches')
        ;

        $map->add($tooLateMatcher, array($this->getMock('Symfony\Component\Security\Http\Firewall\ListenerInterface')));

        list($listeners, $exception) = $map->getListeners($request);

        $this->assertEquals(array($theListener), $listeners);
        $this->assertEquals($theException, $exception);
    }

    public function testGetListenersWithNoMatchingEntry()
    {
        $map = new FirewallMap();

        $request = new Request();

        $notMatchingMatcher = $this->getMock('Symfony\Component\HttpFoundation\RequestMatcher');
        $notMatchingMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($this->equalTo($request))
            ->will($this->returnValue(false))
        ;

        $map->add($notMatchingMatcher, array($this->getMock('Symfony\Component\Security\Http\Firewall\ListenerInterface')));

        list($listeners, $exception) = $map->getListeners($request);

        $this->assertEquals(array(), $listeners);
        $this->assertNull($exception);
    }
}
