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

use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    private $dispatcher;

    private $kernel;

    protected function setUp()
    {
        $this->dispatcher = new EventDispatcher();
        $listener = new ResponseListener('UTF-8');
        $this->dispatcher->addListener(KernelEvents::RESPONSE, array($listener, 'onKernelResponse'));

        $this->kernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
    }

    protected function tearDown()
    {
        $this->dispatcher = null;
        $this->kernel = null;
    }

    public function testFilterDoesNothingForSubRequests()
    {
        $response = new Response('foo');

        $event = new FilterResponseEvent($this->kernel, new Request(), HttpKernelInterface::SUB_REQUEST, $response);
        $this->dispatcher->dispatch(KernelEvents::RESPONSE, $event);

        $this->assertEquals('', $event->getResponse()->headers->get('content-type'));
    }

    public function testFilterSetsNonDefaultCharsetIfNotOverridden()
    {
        $listener = new ResponseListener('ISO-8859-15');
        $this->dispatcher->addListener(KernelEvents::RESPONSE, array($listener, 'onKernelResponse'), 1);

        $response = new Response('foo');

        $event = new FilterResponseEvent($this->kernel, Request::create('/'), HttpKernelInterface::MASTER_REQUEST, $response);
        $this->dispatcher->dispatch(KernelEvents::RESPONSE, $event);

        $this->assertEquals('ISO-8859-15', $response->getCharset());
    }

    public function testFilterDoesNothingIfCharsetIsOverridden()
    {
        $listener = new ResponseListener('ISO-8859-15');
        $this->dispatcher->addListener(KernelEvents::RESPONSE, array($listener, 'onKernelResponse'), 1);

        $response = new Response('foo');
        $response->setCharset('ISO-8859-1');

        $event = new FilterResponseEvent($this->kernel, Request::create('/'), HttpKernelInterface::MASTER_REQUEST, $response);
        $this->dispatcher->dispatch(KernelEvents::RESPONSE, $event);

        $this->assertEquals('ISO-8859-1', $response->getCharset());
    }

    public function testFiltersSetsNonDefaultCharsetIfNotOverriddenOnNonTextContentType()
    {
        $listener = new ResponseListener('ISO-8859-15');
        $this->dispatcher->addListener(KernelEvents::RESPONSE, array($listener, 'onKernelResponse'), 1);

        $response = new Response('foo');
        $request = Request::create('/');
        $request->setRequestFormat('application/json');

        $event = new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
        $this->dispatcher->dispatch(KernelEvents::RESPONSE, $event);

        $this->assertEquals('ISO-8859-15', $response->getCharset());
    }
}
