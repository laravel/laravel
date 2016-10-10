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

use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AbstractRememberMeServicesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRememberMeParameter()
    {
        $service = $this->getService(null, array('remember_me_parameter' => 'foo'));

        $this->assertEquals('foo', $service->getRememberMeParameter());
    }

    public function testGetKey()
    {
        $service = $this->getService();
        $this->assertEquals('fookey', $service->getKey());
    }

    public function testAutoLoginReturnsNullWhenNoCookie()
    {
        $service = $this->getService(null, array('name' => 'foo', 'path' => null, 'domain' => null));

        $this->assertNull($service->autoLogin(new Request()));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAutoLoginThrowsExceptionWhenImplementationDoesNotReturnUserInterface()
    {
        $service = $this->getService(null, array('name' => 'foo', 'path' => null, 'domain' => null));
        $request = new Request();
        $request->cookies->set('foo', 'foo');

        $service
            ->expects($this->once())
            ->method('processAutoLoginCookie')
            ->will($this->returnValue(null))
        ;

        $service->autoLogin($request);
    }

    public function testAutoLogin()
    {
        $service = $this->getService(null, array('name' => 'foo', 'path' => null, 'domain' => null));
        $request = new Request();
        $request->cookies->set('foo', 'foo');

        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user
            ->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue(array()))
        ;

        $service
            ->expects($this->once())
            ->method('processAutoLoginCookie')
            ->will($this->returnValue($user))
        ;

        $returnedToken = $service->autoLogin($request);

        $this->assertSame($user, $returnedToken->getUser());
        $this->assertSame('fookey', $returnedToken->getKey());
        $this->assertSame('fookey', $returnedToken->getProviderKey());
    }

    public function testLogout()
    {
        $service = $this->getService(null, array('name' => 'foo', 'path' => null, 'domain' => null));
        $request = new Request();
        $response = new Response();
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $service->logout($request, $response, $token);

        $this->assertTrue($request->attributes->get(RememberMeServicesInterface::COOKIE_ATTR_NAME)->isCleared());
    }

    public function testLoginFail()
    {
        $service = $this->getService(null, array('name' => 'foo', 'path' => null, 'domain' => null));
        $request = new Request();

        $service->loginFail($request);

        $this->assertTrue($request->attributes->get(RememberMeServicesInterface::COOKIE_ATTR_NAME)->isCleared());
    }

    public function testLoginSuccessIsNotProcessedWhenTokenDoesNotContainUserInterfaceImplementation()
    {
        $service = $this->getService(null, array('name' => 'foo', 'always_remember_me' => true, 'path' => null, 'domain' => null));
        $request = new Request();
        $response = new Response();
        $account = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('foo'))
        ;

        $service
            ->expects($this->never())
            ->method('onLoginSuccess')
        ;

        $this->assertFalse($request->request->has('foo'));

        $service->loginSuccess($request, $response, $token);
    }

    public function testLoginSuccessIsNotProcessedWhenRememberMeIsNotRequested()
    {
        $service = $this->getService(null, array('name' => 'foo', 'always_remember_me' => false, 'remember_me_parameter' => 'foo', 'path' => null, 'domain' => null));
        $request = new Request();
        $response = new Response();
        $account = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($account))
        ;

        $service
            ->expects($this->never())
            ->method('onLoginSuccess')
            ->will($this->returnValue(null))
        ;

        $this->assertFalse($request->request->has('foo'));

        $service->loginSuccess($request, $response, $token);
    }

    public function testLoginSuccessWhenRememberMeAlwaysIsTrue()
    {
        $service = $this->getService(null, array('name' => 'foo', 'always_remember_me' => true, 'path' => null, 'domain' => null));
        $request = new Request();
        $response = new Response();
        $account = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($account))
        ;

        $service
            ->expects($this->once())
            ->method('onLoginSuccess')
            ->will($this->returnValue(null))
        ;

        $service->loginSuccess($request, $response, $token);
    }

    /**
     * @dataProvider getPositiveRememberMeParameterValues
     */
    public function testLoginSuccessWhenRememberMeParameterWithPathIsPositive($value)
    {
        $service = $this->getService(null, array('name' => 'foo', 'always_remember_me' => false, 'remember_me_parameter' => 'foo[bar]', 'path' => null, 'domain' => null));

        $request = new Request();
        $request->request->set('foo', array('bar' => $value));
        $response = new Response();
        $account = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($account))
        ;

        $service
            ->expects($this->once())
            ->method('onLoginSuccess')
            ->will($this->returnValue(true))
        ;

        $service->loginSuccess($request, $response, $token);
    }

    /**
     * @dataProvider getPositiveRememberMeParameterValues
     */
    public function testLoginSuccessWhenRememberMeParameterIsPositive($value)
    {
        $service = $this->getService(null, array('name' => 'foo', 'always_remember_me' => false, 'remember_me_parameter' => 'foo', 'path' => null, 'domain' => null));

        $request = new Request();
        $request->request->set('foo', $value);
        $response = new Response();
        $account = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($account))
        ;

        $service
            ->expects($this->once())
            ->method('onLoginSuccess')
            ->will($this->returnValue(true))
        ;

        $service->loginSuccess($request, $response, $token);
    }

    public function getPositiveRememberMeParameterValues()
    {
        return array(
            array('true'),
            array('1'),
            array('on'),
            array('yes'),
        );
    }

    protected function getService($userProvider = null, $options = array(), $logger = null)
    {
        if (null === $userProvider) {
            $userProvider = $this->getProvider();
        }

        return $this->getMockForAbstractClass('Symfony\Component\Security\Http\RememberMe\AbstractRememberMeServices', array(
            array($userProvider), 'fookey', 'fookey', $options, $logger
        ));
    }

    protected function getProvider()
    {
        $provider = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $provider
            ->expects($this->any())
            ->method('supportsClass')
            ->will($this->returnValue(true))
        ;

        return $provider;
    }
}
