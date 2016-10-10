<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Tests\Http\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Symfony\Component\Security\Http\HttpUtils;

class ExceptionListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getAuthenticationExceptionProvider
     */
    public function testAuthenticationExceptionWithoutEntryPoint(\Exception $exception, \Exception $eventException = null)
    {
        $event = $this->createEvent($exception);

        $listener = $this->createExceptionListener();
        $listener->onKernelException($event);

        $this->assertNull($event->getResponse());
        $this->assertSame(null === $eventException ? $exception : $eventException, $event->getException());
    }

    /**
     * @dataProvider getAuthenticationExceptionProvider
     */
    public function testAuthenticationExceptionWithEntryPoint(\Exception $exception, \Exception $eventException = null)
    {
        $event = $this->createEvent($exception = new AuthenticationException());

        $listener = $this->createExceptionListener(null, null, null, $this->createEntryPoint());
        $listener->onKernelException($event);

        $this->assertEquals('OK', $event->getResponse()->getContent());
        $this->assertSame($exception, $event->getException());
    }

    public function getAuthenticationExceptionProvider()
    {
        return array(
            array(new AuthenticationException()),
            array(new \LogicException('random', 0, $e = new AuthenticationException()), $e),
            array(new \LogicException('random', 0, $e = new AuthenticationException('embed', 0, new AuthenticationException())), $e),
            array(new \LogicException('random', 0, $e = new AuthenticationException('embed', 0, new AccessDeniedException())), $e),
            array(new AuthenticationException('random', 0, new \LogicException())),
        );
    }

    /**
     * @dataProvider getAccessDeniedExceptionProvider
     */
    public function testAccessDeniedExceptionFullFledgedAndWithoutAccessDeniedHandlerAndWithoutErrorPage(\Exception $exception, \Exception $eventException = null)
    {
        $event = $this->createEvent($exception);

        $listener = $this->createExceptionListener(null, $this->createTrustResolver(true));
        $listener->onKernelException($event);

        $this->assertNull($event->getResponse());
        $this->assertSame(null === $eventException ? $exception : $eventException, $event->getException()->getPrevious());
    }

    /**
     * @dataProvider getAccessDeniedExceptionProvider
     */
    public function testAccessDeniedExceptionFullFledgedAndWithoutAccessDeniedHandlerAndWithErrorPage(\Exception $exception, \Exception $eventException = null)
    {
        $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $kernel->expects($this->once())->method('handle')->will($this->returnValue(new Response('error')));

        $event = $this->createEvent($exception, $kernel);

        $httpUtils = $this->getMock('Symfony\Component\Security\Http\HttpUtils');
        $httpUtils->expects($this->once())->method('createRequest')->will($this->returnValue(Request::create('/error')));

        $listener = $this->createExceptionListener(null, $this->createTrustResolver(true), $httpUtils, null, '/error');
        $listener->onKernelException($event);

        $this->assertEquals('error', $event->getResponse()->getContent());
        $this->assertSame(null === $eventException ? $exception : $eventException, $event->getException()->getPrevious());
    }

    /**
     * @dataProvider getAccessDeniedExceptionProvider
     */
    public function testAccessDeniedExceptionFullFledgedAndWithAccessDeniedHandlerAndWithoutErrorPage(\Exception $exception, \Exception $eventException = null)
    {
        $event = $this->createEvent($exception);

        $accessDeniedHandler = $this->getMock('Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface');
        $accessDeniedHandler->expects($this->once())->method('handle')->will($this->returnValue(new Response('error')));

        $listener = $this->createExceptionListener(null, $this->createTrustResolver(true), null, null, null, $accessDeniedHandler);
        $listener->onKernelException($event);

        $this->assertEquals('error', $event->getResponse()->getContent());
        $this->assertSame(null === $eventException ? $exception : $eventException, $event->getException()->getPrevious());
    }

    /**
     * @dataProvider getAccessDeniedExceptionProvider
     */
    public function testAccessDeniedExceptionNotFullFledged(\Exception $exception, \Exception $eventException = null)
    {
        $event = $this->createEvent($exception);

        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $context->expects($this->once())->method('getToken')->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')));

        $listener = $this->createExceptionListener($context, $this->createTrustResolver(false), null, $this->createEntryPoint());
        $listener->onKernelException($event);

        $this->assertEquals('OK', $event->getResponse()->getContent());
        $this->assertSame(null === $eventException ? $exception : $eventException, $event->getException()->getPrevious());
    }

    public function getAccessDeniedExceptionProvider()
    {
        return array(
            array(new AccessDeniedException()),
            array(new \LogicException('random', 0, $e = new AccessDeniedException()), $e),
            array(new \LogicException('random', 0, $e = new AccessDeniedException('embed', new AccessDeniedException())), $e),
            array(new \LogicException('random', 0, $e = new AccessDeniedException('embed', new AuthenticationException())), $e),
            array(new AccessDeniedException('random', new \LogicException())),
        );
    }

    private function createEntryPoint()
    {
        $entryPoint = $this->getMock('Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface');
        $entryPoint->expects($this->once())->method('start')->will($this->returnValue(new Response('OK')));

        return $entryPoint;
    }

    private function createTrustResolver($fullFledged)
    {
        $trustResolver = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface');
        $trustResolver->expects($this->once())->method('isFullFledged')->will($this->returnValue($fullFledged));

        return $trustResolver;
    }

    private function createEvent(\Exception $exception, $kernel = null)
    {
        if (null === $kernel) {
            $kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        }

        return new GetResponseForExceptionEvent($kernel, Request::create('/'), HttpKernelInterface::MASTER_REQUEST, $exception);
    }

    private function createExceptionListener(SecurityContextInterface $context = null, AuthenticationTrustResolverInterface $trustResolver = null, HttpUtils $httpUtils = null, AuthenticationEntryPointInterface $authenticationEntryPoint = null, $errorPage = null, AccessDeniedHandlerInterface $accessDeniedHandler = null)
    {
        return new ExceptionListener(
            $context ? $context : $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface'),
            $trustResolver ? $trustResolver : $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface'),
            $httpUtils ? $httpUtils : $this->getMock('Symfony\Component\Security\Http\HttpUtils'),
            'key',
            $authenticationEntryPoint,
            $errorPage,
            $accessDeniedHandler
        );
    }
}
