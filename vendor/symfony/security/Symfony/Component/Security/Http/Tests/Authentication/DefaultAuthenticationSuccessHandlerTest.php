<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Tests\Authentication;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

class DefaultAuthenticationSuccessHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $httpUtils = null;

    private $request = null;

    private $token = null;

    protected function setUp()
    {
        $this->httpUtils = $this->getMock('Symfony\Component\Security\Http\HttpUtils');
        $this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $this->request->headers = $this->getMock('Symfony\Component\HttpFoundation\HeaderBag');
        $this->token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
    }

    public function testRequestIsRedirected()
    {
        $response = $this->expectRedirectResponse('/');

        $handler = new DefaultAuthenticationSuccessHandler($this->httpUtils, array());
        $result = $handler->onAuthenticationSuccess($this->request, $this->token);

        $this->assertSame($response, $result);
    }

    public function testDefaultTargetPathCanBeForced()
    {
        $options = array(
            'always_use_default_target_path' => true,
            'default_target_path' => '/dashboard'
        );

        $response = $this->expectRedirectResponse('/dashboard');

        $handler = new DefaultAuthenticationSuccessHandler($this->httpUtils, $options);
        $result = $handler->onAuthenticationSuccess($this->request, $this->token);

        $this->assertSame($response, $result);
    }

    public function testTargetPathIsPassedWithRequest()
    {
        $this->request->expects($this->once())
            ->method('get')->with('_target_path')
            ->will($this->returnValue('/dashboard'));

        $response = $this->expectRedirectResponse('/dashboard');

        $handler = new DefaultAuthenticationSuccessHandler($this->httpUtils, array());
        $result = $handler->onAuthenticationSuccess($this->request, $this->token);

        $this->assertSame($response, $result);
    }

    public function testTargetPathParameterIsCustomised()
    {
        $options = array('target_path_parameter' => '_my_target_path');

        $this->request->expects($this->once())
            ->method('get')->with('_my_target_path')
            ->will($this->returnValue('/dashboard'));

        $response = $this->expectRedirectResponse('/dashboard');

        $handler = new DefaultAuthenticationSuccessHandler($this->httpUtils, $options);
        $result = $handler->onAuthenticationSuccess($this->request, $this->token);

        $this->assertSame($response, $result);
    }

    public function testTargetPathIsTakenFromTheSession()
    {
        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $session->expects($this->once())
            ->method('get')->with('_security.admin.target_path')
            ->will($this->returnValue('/admin/dashboard'));
        $session->expects($this->once())
            ->method('remove')->with('_security.admin.target_path');

        $this->request->expects($this->any())
            ->method('getSession')
            ->will($this->returnValue($session));

        $response = $this->expectRedirectResponse('/admin/dashboard');

        $handler = new DefaultAuthenticationSuccessHandler($this->httpUtils, array());
        $handler->setProviderKey('admin');

        $result = $handler->onAuthenticationSuccess($this->request, $this->token);

        $this->assertSame($response, $result);
    }

    public function testTargetPathIsPassedAsReferer()
    {
        $options = array('use_referer' => true);

        $this->request->headers->expects($this->once())
            ->method('get')->with('Referer')
            ->will($this->returnValue('/dashboard'));

        $response = $this->expectRedirectResponse('/dashboard');

        $handler = new DefaultAuthenticationSuccessHandler($this->httpUtils, $options);
        $result = $handler->onAuthenticationSuccess($this->request, $this->token);

        $this->assertSame($response, $result);
    }

    public function testRefererHasToBeDifferentThatLoginUrl()
    {
        $options = array('use_referer' => true);

        $this->request->headers->expects($this->any())
            ->method('get')->with('Referer')
            ->will($this->returnValue('/login'));

        $this->httpUtils->expects($this->once())
            ->method('generateUri')->with($this->request, '/login')
            ->will($this->returnValue('/login'));

        $response = $this->expectRedirectResponse('/');

        $handler = new DefaultAuthenticationSuccessHandler($this->httpUtils, $options);
        $result = $handler->onAuthenticationSuccess($this->request, $this->token);

        $this->assertSame($response, $result);
    }

    public function testRefererTargetPathIsIgnoredByDefault()
    {
        $this->request->headers->expects($this->never())->method('get');

        $response = $this->expectRedirectResponse('/');

        $handler = new DefaultAuthenticationSuccessHandler($this->httpUtils, array());
        $result = $handler->onAuthenticationSuccess($this->request, $this->token);

        $this->assertSame($response, $result);
    }

    private function expectRedirectResponse($path)
    {
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');

        $this->httpUtils->expects($this->once())
            ->method('createRedirectResponse')
            ->with($this->request, $path)
            ->will($this->returnValue($response));

        return $response;
    }
}
