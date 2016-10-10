<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\EventListener\LocaleListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocaleListenerTest extends \PHPUnit_Framework_TestCase
{
    private $requestStack;

    protected function setUp()
    {
        $this->requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack', array(), array(), '', false);
    }

    public function testDefaultLocaleWithoutSession()
    {
        $listener = new LocaleListener('fr', null, $this->requestStack);
        $event = $this->getEvent($request = Request::create('/'));

        $listener->onKernelRequest($event);
        $this->assertEquals('fr', $request->getLocale());
    }

    public function testLocaleFromRequestAttribute()
    {
        $request = Request::create('/');
        session_name('foo');
        $request->cookies->set('foo', 'value');

        $request->attributes->set('_locale', 'es');
        $listener = new LocaleListener('fr', null, $this->requestStack);
        $event = $this->getEvent($request);

        $listener->onKernelRequest($event);
        $this->assertEquals('es', $request->getLocale());
    }

    public function testLocaleSetForRoutingContext()
    {
        // the request context is updated
        $context = $this->getMock('Symfony\Component\Routing\RequestContext');
        $context->expects($this->once())->method('setParameter')->with('_locale', 'es');

        $router = $this->getMock('Symfony\Component\Routing\Router', array('getContext'), array(), '', false);
        $router->expects($this->once())->method('getContext')->will($this->returnValue($context));

        $request = Request::create('/');

        $request->attributes->set('_locale', 'es');
        $listener = new LocaleListener('fr', $router, $this->requestStack);
        $listener->onKernelRequest($this->getEvent($request));
    }

    public function testRouterResetWithParentRequestOnKernelFinishRequest()
    {
        if (!class_exists('Symfony\Component\Routing\Router')) {
            $this->markTestSkipped('The "Routing" component is not available');
        }

        // the request context is updated
        $context = $this->getMock('Symfony\Component\Routing\RequestContext');
        $context->expects($this->once())->method('setParameter')->with('_locale', 'es');

        $router = $this->getMock('Symfony\Component\Routing\Router', array('getContext'), array(), '', false);
        $router->expects($this->once())->method('getContext')->will($this->returnValue($context));

        $parentRequest = Request::create('/');
        $parentRequest->setLocale('es');

        $this->requestStack->expects($this->once())->method('getParentRequest')->will($this->returnValue($parentRequest));

        $event = $this->getMock('Symfony\Component\HttpKernel\Event\FinishRequestEvent', array(), array(), '', false);

        $listener = new LocaleListener('fr', $router, $this->requestStack);
        $listener->onKernelFinishRequest($event);
    }

    public function testRequestLocaleIsNotOverridden()
    {
        $request = Request::create('/');
        $request->setLocale('de');
        $listener = new LocaleListener('fr', null, $this->requestStack);
        $event = $this->getEvent($request);

        $listener->onKernelRequest($event);
        $this->assertEquals('de', $request->getLocale());
    }

    private function getEvent(Request $request)
    {
        return new GetResponseEvent($this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'), $request, HttpKernelInterface::MASTER_REQUEST);
    }
}
