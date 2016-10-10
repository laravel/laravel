<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Tests\Logout;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;

class DefaultLogoutSuccessHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testLogout()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');

        $httpUtils = $this->getMock('Symfony\Component\Security\Http\HttpUtils');
        $httpUtils->expects($this->once())
            ->method('createRedirectResponse')
            ->with($request, '/dashboard')
            ->will($this->returnValue($response));

        $handler = new DefaultLogoutSuccessHandler($httpUtils, '/dashboard');
        $result = $handler->onLogoutSuccess($request);

        $this->assertSame($response, $result);
    }
}
