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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AbstractPreAuthenticatedListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleWithValidValues()
    {
        $userCredentials = array('TheUser', 'TheCredentials');

        $request = new Request(array(), array(), array(), array(), array(), array());

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue(null))
        ;
        $context
            ->expects($this->once())
            ->method('setToken')
            ->with($this->equalTo($token))
        ;

        $authenticationManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $authenticationManager
            ->expects($this->once())
            ->method('authenticate')
            ->with($this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken'))
            ->will($this->returnValue($token))
        ;

        $listener = $this->getMockForAbstractClass('Symfony\Component\Security\Http\Firewall\AbstractPreAuthenticatedListener', array(
            $context,
            $authenticationManager,
            'TheProviderKey'
        ));
        $listener
            ->expects($this->once())
            ->method('getPreAuthenticatedData')
            ->will($this->returnValue($userCredentials));

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        $listener->handle($event);
    }

    public function testHandleWhenAuthenticationFails()
    {
        $userCredentials = array('TheUser', 'TheCredentials');

        $request = new Request(array(), array(), array(), array(), array(), array());

        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue(null))
        ;
        $context
            ->expects($this->never())
            ->method('setToken')
        ;

        $exception = new AuthenticationException('Authentication failed.');
        $authenticationManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $authenticationManager
            ->expects($this->once())
            ->method('authenticate')
            ->with($this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken'))
            ->will($this->throwException($exception))
        ;

        $listener = $this->getMockForAbstractClass('Symfony\Component\Security\Http\Firewall\AbstractPreAuthenticatedListener', array(
            $context,
            $authenticationManager,
            'TheProviderKey'
        ));
        $listener
            ->expects($this->once())
            ->method('getPreAuthenticatedData')
            ->will($this->returnValue($userCredentials));

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        $listener->handle($event);
    }

    public function testHandleWhenAuthenticationFailsWithDifferentToken()
    {
        $userCredentials = array('TheUser', 'TheCredentials');

        $token = new UsernamePasswordToken('TheUsername', 'ThePassword', 'TheProviderKey', array('ROLE_FOO'));

        $request = new Request(array(), array(), array(), array(), array(), array());

        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($token))
        ;
        $context
            ->expects($this->never())
            ->method('setToken')
        ;

        $exception = new AuthenticationException('Authentication failed.');
        $authenticationManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $authenticationManager
            ->expects($this->once())
            ->method('authenticate')
            ->with($this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken'))
            ->will($this->throwException($exception))
        ;

        $listener = $this->getMockForAbstractClass('Symfony\Component\Security\Http\Firewall\AbstractPreAuthenticatedListener', array(
            $context,
            $authenticationManager,
            'TheProviderKey'
        ));
        $listener
            ->expects($this->once())
            ->method('getPreAuthenticatedData')
            ->will($this->returnValue($userCredentials));

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        $listener->handle($event);
    }

    public function testHandleWithASimilarAuthenticatedToken()
    {
        $userCredentials = array('TheUser', 'TheCredentials');

        $request = new Request(array(), array(), array(), array(), array(), array());

        $token = new PreAuthenticatedToken('TheUser', 'TheCredentials', 'TheProviderKey', array('ROLE_FOO'));

        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($token))
        ;

        $authenticationManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $authenticationManager
            ->expects($this->never())
            ->method('authenticate')
        ;

        $listener = $this->getMockForAbstractClass('Symfony\Component\Security\Http\Firewall\AbstractPreAuthenticatedListener', array(
            $context,
            $authenticationManager,
            'TheProviderKey'
        ));
        $listener
            ->expects($this->once())
            ->method('getPreAuthenticatedData')
            ->will($this->returnValue($userCredentials));

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        $listener->handle($event);
    }

    public function testHandleWithAnInvalidSimilarToken()
    {
        $userCredentials = array('TheUser', 'TheCredentials');

        $request = new Request(array(), array(), array(), array(), array(), array());

        $token = new PreAuthenticatedToken('AnotherUser', 'TheCredentials', 'TheProviderKey', array('ROLE_FOO'));

        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($token))
        ;
        $context
            ->expects($this->once())
            ->method('setToken')
            ->with($this->equalTo(null))
        ;

        $exception = new AuthenticationException('Authentication failed.');
        $authenticationManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $authenticationManager
            ->expects($this->once())
            ->method('authenticate')
            ->with($this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken'))
            ->will($this->throwException($exception))
        ;

        $listener = $this->getMockForAbstractClass('Symfony\Component\Security\Http\Firewall\AbstractPreAuthenticatedListener', array(
            $context,
            $authenticationManager,
            'TheProviderKey'
        ));
        $listener
            ->expects($this->once())
            ->method('getPreAuthenticatedData')
            ->will($this->returnValue($userCredentials));

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        $listener->handle($event);
    }
}
