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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Firewall\ContextListener;

class ContextListenerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->securityContext = new SecurityContext(
            $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface'),
            $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface')
        );
    }

    protected function tearDown()
    {
        unset($this->securityContext);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $contextKey must not be empty
     */
    public function testItRequiresContextKey()
    {
        new ContextListener(
            $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface'),
            array(),
            ''
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage User provider "stdClass" must implement "Symfony\Component\Security\Core\User\UserProviderInterface
     */
    public function testUserProvidersNeedToImplementAnInterface()
    {
        new ContextListener(
            $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface'),
            array(new \stdClass()),
            'key123'
        );
    }

    public function testOnKernelResponseWillAddSession()
    {
        $session = $this->runSessionOnKernelResponse(
            new UsernamePasswordToken('test1', 'pass1', 'phpunit'),
            null
        );

        $token = unserialize($session->get('_security_session'));
        $this->assertInstanceOf('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken', $token);
        $this->assertEquals('test1', $token->getUsername());
    }

    public function testOnKernelResponseWillReplaceSession()
    {
        $session = $this->runSessionOnKernelResponse(
            new UsernamePasswordToken('test1', 'pass1', 'phpunit'),
            'C:10:"serialized"'
        );

        $token = unserialize($session->get('_security_session'));
        $this->assertInstanceOf('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken', $token);
        $this->assertEquals('test1', $token->getUsername());
    }

    public function testOnKernelResponseWillRemoveSession()
    {
        $session = $this->runSessionOnKernelResponse(
            null,
            'C:10:"serialized"'
        );

        $this->assertFalse($session->has('_security_session'));
    }

    public function testOnKernelResponseWithoutSession()
    {
        $this->securityContext->setToken(new UsernamePasswordToken('test1', 'pass1', 'phpunit'));
        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $event = new FilterResponseEvent(
            $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $listener = new ContextListener($this->securityContext, array(), 'session');
        $listener->onKernelResponse($event);

        $this->assertTrue($session->isStarted());
    }

    public function testOnKernelResponseWithoutSessionNorToken()
    {
        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $event = new FilterResponseEvent(
            $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $listener = new ContextListener($this->securityContext, array(), 'session');
        $listener->onKernelResponse($event);

        $this->assertFalse($session->isStarted());
    }

    /**
     * @dataProvider provideInvalidToken
     */
    public function testInvalidTokenInSession($token)
    {
        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');

        $event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $request->expects($this->any())
            ->method('hasPreviousSession')
            ->will($this->returnValue(true));
        $request->expects($this->any())
            ->method('getSession')
            ->will($this->returnValue($session));
        $session->expects($this->any())
            ->method('get')
            ->with('_security_key123')
            ->will($this->returnValue($token));
        $context->expects($this->once())
            ->method('setToken')
            ->with(null);

        $listener = new ContextListener($context, array(), 'key123');
        $listener->handle($event);
    }

    public function provideInvalidToken()
    {
        return array(
            array(serialize(new \__PHP_Incomplete_Class())),
            array(serialize(null)),
            array(null)
        );
    }

    public function testHandleAddsKernelResponseListener()
    {
        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $listener = new ContextListener($context, array(), 'key123', null, $dispatcher);

        $event->expects($this->any())
            ->method('isMasterRequest')
            ->will($this->returnValue(true));
        $event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->getMock('Symfony\Component\HttpFoundation\Request')));

        $dispatcher->expects($this->once())
            ->method('addListener')
            ->with(KernelEvents::RESPONSE, array($listener, 'onKernelResponse'));

        $listener->handle($event);
    }

    public function testHandleRemovesTokenIfNoPreviousSessionWasFound()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $request->expects($this->any())->method('hasPreviousSession')->will($this->returnValue(false));

        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->any())->method('getRequest')->will($this->returnValue($request));

        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context->expects($this->once())->method('setToken')->with(null);

        $listener = new ContextListener($context, array(), 'key123');
        $listener->handle($event);
    }

    protected function runSessionOnKernelResponse($newToken, $original = null)
    {
        $session = new Session(new MockArraySessionStorage());

        if ($original !== null) {
            $session->set('_security_session', $original);
        }

        $this->securityContext->setToken($newToken);

        $request = new Request();
        $request->setSession($session);
        $request->cookies->set('MOCKSESSID', true);

        $event = new FilterResponseEvent(
            $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $listener = new ContextListener($this->securityContext, array(), 'session');
        $listener->onKernelResponse($event);

        return $session;
    }
}
