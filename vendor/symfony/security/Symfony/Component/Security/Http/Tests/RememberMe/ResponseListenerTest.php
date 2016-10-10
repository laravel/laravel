<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Tests\RememberMe;

use Symfony\Component\Security\Http\RememberMe\ResponseListener;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testRememberMeCookieIsSentWithResponse()
    {
        $cookie = new Cookie('rememberme');

        $request = $this->getRequest(array(
            RememberMeServicesInterface::COOKIE_ATTR_NAME => $cookie
        ));

        $response = $this->getResponse();
        $response->headers->expects($this->once())->method('setCookie')->with($cookie);

        $listener = new ResponseListener();
        $listener->onKernelResponse($this->getEvent($request, $response));
    }

    public function testRememberMeCookieIsNotSendWithResponse()
    {
        $request = $this->getRequest();

        $response = $this->getResponse();
        $response->headers->expects($this->never())->method('setCookie');

        $listener = new ResponseListener();
        $listener->onKernelResponse($this->getEvent($request, $response));
    }

    public function testItSubscribesToTheOnKernelResponseEvent()
    {
        $listener = new ResponseListener();

        $this->assertSame(array(KernelEvents::RESPONSE => 'onKernelResponse'), ResponseListener::getSubscribedEvents());
    }

    private function getRequest(array $attributes = array())
    {
        $request = new Request();

        foreach ($attributes as $name => $value) {
            $request->attributes->set($name, $value);
        }

        return $request;
    }

    private function getResponse()
    {
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $response->headers = $this->getMock('Symfony\Component\HttpFoundation\ResponseHeaderBag');

        return $response;
    }

    private function getEvent($request, $response)
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->any())->method('getRequest')->will($this->returnValue($request));
        $event->expects($this->any())->method('getResponse')->will($this->returnValue($response));

        return $event;
    }
}
