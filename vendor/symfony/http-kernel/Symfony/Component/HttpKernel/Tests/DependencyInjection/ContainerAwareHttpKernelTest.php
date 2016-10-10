<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\DependencyInjection;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\DependencyInjection\ContainerAwareHttpKernel;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ContainerAwareHttpKernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getProviderTypes
     */
    public function testHandle($type)
    {
        $request = new Request();
        $expected = new Response();
        $controller = function () use ($expected) {
            return $expected;
        };

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this
            ->expectsEnterScopeOnce($container)
            ->expectsLeaveScopeOnce($container)
            ->expectsSetRequestWithAt($container, $request, 3)
            ->expectsSetRequestWithAt($container, null, 4)
        ;

        $dispatcher = new EventDispatcher();
        $resolver = $this->getResolverMockFor($controller, $request);
        $stack = new RequestStack();
        $kernel = new ContainerAwareHttpKernel($dispatcher, $container, $resolver, $stack);

        $actual = $kernel->handle($request, $type);

        $this->assertSame($expected, $actual, '->handle() returns the response');
    }

    /**
     * @dataProvider getProviderTypes
     */
    public function testVerifyRequestStackPushPopDuringHandle($type)
    {
        $request = new Request();
        $expected = new Response();
        $controller = function () use ($expected) {
            return $expected;
        };

        $stack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack', array('push', 'pop'));
        $stack->expects($this->at(0))->method('push')->with($this->equalTo($request));
        $stack->expects($this->at(1))->method('pop');

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $dispatcher = new EventDispatcher();
        $resolver = $this->getResolverMockFor($controller, $request);
        $kernel = new ContainerAwareHttpKernel($dispatcher, $container, $resolver, $stack);

        $kernel->handle($request, $type);
    }

    /**
     * @dataProvider getProviderTypes
     */
    public function testHandleRestoresThePreviousRequestOnException($type)
    {
        $request = new Request();
        $expected = new \Exception();
        $controller = function () use ($expected) {
            throw $expected;
        };

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this
            ->expectsEnterScopeOnce($container)
            ->expectsLeaveScopeOnce($container)
            ->expectsSetRequestWithAt($container, $request, 3)
            ->expectsSetRequestWithAt($container, null, 4)
        ;

        $dispatcher = new EventDispatcher();
        $resolver = $this->getMock('Symfony\\Component\\HttpKernel\\Controller\\ControllerResolverInterface');
        $resolver = $this->getResolverMockFor($controller, $request);
        $stack = new RequestStack();
        $kernel = new ContainerAwareHttpKernel($dispatcher, $container, $resolver, $stack);

        try {
            $kernel->handle($request, $type);
            $this->fail('->handle() suppresses the controller exception');
        } catch (\PHPUnit_Framework_Exception $exception) {
            throw $exception;
        } catch (\Exception $actual) {
            $this->assertSame($expected, $actual, '->handle() throws the controller exception');
        }
    }

    public function getProviderTypes()
    {
        return array(
            array(HttpKernelInterface::MASTER_REQUEST),
            array(HttpKernelInterface::SUB_REQUEST),
        );
    }

    private function getResolverMockFor($controller, $request)
    {
        $resolver = $this->getMock('Symfony\\Component\\HttpKernel\\Controller\\ControllerResolverInterface');
        $resolver->expects($this->once())
            ->method('getController')
            ->with($request)
            ->will($this->returnValue($controller));
        $resolver->expects($this->once())
            ->method('getArguments')
            ->with($request, $controller)
            ->will($this->returnValue(array()));

        return $resolver;
    }

    private function expectsSetRequestWithAt($container, $with, $at)
    {
        $container
            ->expects($this->at($at))
            ->method('set')
            ->with($this->equalTo('request'), $this->equalTo($with), $this->equalTo('request'))
        ;

        return $this;
    }

    private function expectsEnterScopeOnce($container)
    {
        $container
            ->expects($this->once())
            ->method('enterScope')
            ->with($this->equalTo('request'))
        ;

        return $this;
    }

    private function expectsLeaveScopeOnce($container)
    {
        $container
            ->expects($this->once())
            ->method('leaveScope')
            ->with($this->equalTo('request'))
        ;

        return $this;
    }
}
