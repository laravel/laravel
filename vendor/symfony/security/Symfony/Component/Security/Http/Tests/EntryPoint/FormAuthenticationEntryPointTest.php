<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Tests\EntryPoint;

use Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class FormAuthenticationEntryPointTest extends \PHPUnit_Framework_TestCase
{
    public function testStart()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false, false);
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');

        $httpKernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $httpUtils = $this->getMock('Symfony\Component\Security\Http\HttpUtils');
        $httpUtils
            ->expects($this->once())
            ->method('createRedirectResponse')
            ->with($this->equalTo($request), $this->equalTo('/the/login/path'))
            ->will($this->returnValue($response))
        ;

        $entryPoint = new FormAuthenticationEntryPoint($httpKernel, $httpUtils, '/the/login/path', false);

        $this->assertEquals($response, $entryPoint->start($request));
    }

    public function testStartWithUseForward()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false, false);
        $subRequest = $this->getMock('Symfony\Component\HttpFoundation\Request', array(), array(), '', false, false);
        $response = new \Symfony\Component\HttpFoundation\Response('', 200);

        $httpUtils = $this->getMock('Symfony\Component\Security\Http\HttpUtils');
        $httpUtils
            ->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo($request), $this->equalTo('/the/login/path'))
            ->will($this->returnValue($subRequest))
        ;

        $httpKernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $httpKernel
            ->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($subRequest), $this->equalTo(HttpKernelInterface::SUB_REQUEST))
            ->will($this->returnValue($response))
        ;

        $entryPoint = new FormAuthenticationEntryPoint($httpKernel, $httpUtils, '/the/login/path', true);

        $entryPointResponse = $entryPoint->start($request);

        $this->assertEquals($response, $entryPointResponse);
        $this->assertEquals(401, $entryPointResponse->headers->get('X-Status-Code'));
    }
}
