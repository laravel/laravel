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

use Symfony\Component\Security\Http\Firewall\AccessListener;

class AccessListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testHandleWhenTheAccessDecisionManagerDecidesToRefuseAccess()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false, false);

        $accessMap = $this->getMock('Symfony\Component\Security\Http\AccessMapInterface');
        $accessMap
            ->expects($this->any())
            ->method('getPatterns')
            ->with($this->equalTo($request))
            ->will($this->returnValue(array(array('foo' => 'bar'), null)))
        ;

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token
            ->expects($this->any())
            ->method('isAuthenticated')
            ->will($this->returnValue(true))
        ;

        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($token))
        ;

        $accessDecisionManager = $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface');
        $accessDecisionManager
            ->expects($this->once())
            ->method('decide')
            ->with($this->equalTo($token), $this->equalTo(array('foo' => 'bar')), $this->equalTo($request))
            ->will($this->returnValue(false))
        ;

        $listener = new AccessListener(
            $context,
            $accessDecisionManager,
            $accessMap,
            $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface')
        );

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        $listener->handle($event);
    }

    public function testHandleWhenTheTokenIsNotAuthenticated()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false, false);

        $accessMap = $this->getMock('Symfony\Component\Security\Http\AccessMapInterface');
        $accessMap
            ->expects($this->any())
            ->method('getPatterns')
            ->with($this->equalTo($request))
            ->will($this->returnValue(array(array('foo' => 'bar'), null)))
        ;

        $notAuthenticatedToken = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $notAuthenticatedToken
            ->expects($this->any())
            ->method('isAuthenticated')
            ->will($this->returnValue(false))
        ;

        $authenticatedToken = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $authenticatedToken
            ->expects($this->any())
            ->method('isAuthenticated')
            ->will($this->returnValue(true))
        ;

        $authManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $authManager
            ->expects($this->once())
            ->method('authenticate')
            ->with($this->equalTo($notAuthenticatedToken))
            ->will($this->returnValue($authenticatedToken))
        ;

        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($notAuthenticatedToken))
        ;
        $context
            ->expects($this->once())
            ->method('setToken')
            ->with($this->equalTo($authenticatedToken))
        ;

        $accessDecisionManager = $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface');
        $accessDecisionManager
            ->expects($this->once())
            ->method('decide')
            ->with($this->equalTo($authenticatedToken), $this->equalTo(array('foo' => 'bar')), $this->equalTo($request))
            ->will($this->returnValue(true))
        ;

        $listener = new AccessListener(
            $context,
            $accessDecisionManager,
            $accessMap,
            $authManager
        );

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        $listener->handle($event);
    }

    public function testHandleWhenThereIsNoAccessMapEntryMatchingTheRequest()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false, false);

        $accessMap = $this->getMock('Symfony\Component\Security\Http\AccessMapInterface');
        $accessMap
            ->expects($this->any())
            ->method('getPatterns')
            ->with($this->equalTo($request))
            ->will($this->returnValue(array(null, null)))
        ;

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token
            ->expects($this->never())
            ->method('isAuthenticated')
        ;

        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($token))
        ;

        $listener = new AccessListener(
            $context,
            $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface'),
            $accessMap,
            $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface')
        );

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        $listener->handle($event);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException
     */
    public function testHandleWhenTheSecurityContextHasNoToken()
    {
        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue(null))
        ;

        $listener = new AccessListener(
            $context,
            $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface'),
            $this->getMock('Symfony\Component\Security\Http\AccessMapInterface'),
            $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface')
        );

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);

        $listener->handle($event);
    }
}
