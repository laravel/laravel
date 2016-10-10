<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Tests\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class UserAuthenticationProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testSupports()
    {
        $provider = $this->getProvider();

        $this->assertTrue($provider->supports($this->getSupportedToken()));
        $this->assertFalse($provider->supports($this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')));
    }

    public function testAuthenticateWhenTokenIsNotSupported()
    {
        $provider = $this->getProvider();

        $this->assertNull($provider->authenticate($this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testAuthenticateWhenUsernameIsNotFound()
    {
        $provider = $this->getProvider(false, false);
        $provider->expects($this->once())
                 ->method('retrieveUser')
                 ->will($this->throwException($this->getMock('Symfony\Component\Security\Core\Exception\UsernameNotFoundException', null, array(), '', false)))
        ;

        $provider->authenticate($this->getSupportedToken());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testAuthenticateWhenUsernameIsNotFoundAndHideIsTrue()
    {
        $provider = $this->getProvider(false, true);
        $provider->expects($this->once())
                 ->method('retrieveUser')
                 ->will($this->throwException($this->getMock('Symfony\Component\Security\Core\Exception\UsernameNotFoundException', null, array(), '', false)))
        ;

        $provider->authenticate($this->getSupportedToken());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationServiceException
     */
    public function testAuthenticateWhenProviderDoesNotReturnAnUserInterface()
    {
        $provider = $this->getProvider(false, true);
        $provider->expects($this->once())
                 ->method('retrieveUser')
                 ->will($this->returnValue(null))
        ;

        $provider->authenticate($this->getSupportedToken());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\CredentialsExpiredException
     */
    public function testAuthenticateWhenPreChecksFails()
    {
        $userChecker = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $userChecker->expects($this->once())
                    ->method('checkPreAuth')
                    ->will($this->throwException($this->getMock('Symfony\Component\Security\Core\Exception\CredentialsExpiredException', null, array(), '', false)))
        ;

        $provider = $this->getProvider($userChecker);
        $provider->expects($this->once())
                 ->method('retrieveUser')
                 ->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\User\UserInterface')))
        ;

        $provider->authenticate($this->getSupportedToken());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccountExpiredException
     */
    public function testAuthenticateWhenPostChecksFails()
    {
        $userChecker = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $userChecker->expects($this->once())
                    ->method('checkPostAuth')
                    ->will($this->throwException($this->getMock('Symfony\Component\Security\Core\Exception\AccountExpiredException', null, array(), '', false)))
        ;

        $provider = $this->getProvider($userChecker);
        $provider->expects($this->once())
                 ->method('retrieveUser')
                 ->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\User\UserInterface')))
        ;

        $provider->authenticate($this->getSupportedToken());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     * @expectedExceptionMessage Bad credentials
     */
    public function testAuthenticateWhenPostCheckAuthenticationFails()
    {
        $provider = $this->getProvider();
        $provider->expects($this->once())
                 ->method('retrieveUser')
                 ->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\User\UserInterface')))
        ;
        $provider->expects($this->once())
                 ->method('checkAuthentication')
                 ->will($this->throwException($this->getMock('Symfony\Component\Security\Core\Exception\BadCredentialsException', null, array(), '', false)))
        ;

        $provider->authenticate($this->getSupportedToken());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     * @expectedExceptionMessage Foo
     */
    public function testAuthenticateWhenPostCheckAuthenticationFailsWithHideFalse()
    {
        $provider = $this->getProvider(false, false);
        $provider->expects($this->once())
                 ->method('retrieveUser')
                 ->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\User\UserInterface')))
        ;
        $provider->expects($this->once())
                 ->method('checkAuthentication')
                 ->will($this->throwException(new BadCredentialsException('Foo')))
        ;

        $provider->authenticate($this->getSupportedToken());
    }

    public function testAuthenticate()
    {
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->once())
             ->method('getRoles')
             ->will($this->returnValue(array('ROLE_FOO')))
        ;

        $provider = $this->getProvider();
        $provider->expects($this->once())
                 ->method('retrieveUser')
                 ->will($this->returnValue($user))
        ;

        $token = $this->getSupportedToken();
        $token->expects($this->once())
              ->method('getCredentials')
              ->will($this->returnValue('foo'))
        ;

        $token->expects($this->once())
              ->method('getRoles')
              ->will($this->returnValue(array()))
        ;

        $authToken = $provider->authenticate($token);

        $this->assertInstanceOf('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken', $authToken);
        $this->assertSame($user, $authToken->getUser());
        $this->assertEquals(array(new Role('ROLE_FOO')), $authToken->getRoles());
        $this->assertEquals('foo', $authToken->getCredentials());
        $this->assertEquals(array('foo' => 'bar'), $authToken->getAttributes(), '->authenticate() copies token attributes');
    }

    public function testAuthenticateWithPreservingRoleSwitchUserRole()
    {
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->once())
             ->method('getRoles')
             ->will($this->returnValue(array('ROLE_FOO')))
        ;

        $provider = $this->getProvider();
        $provider->expects($this->once())
                 ->method('retrieveUser')
                 ->will($this->returnValue($user))
        ;

        $token = $this->getSupportedToken();
        $token->expects($this->once())
              ->method('getCredentials')
              ->will($this->returnValue('foo'))
        ;

        $switchUserRole = new SwitchUserRole('foo', $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
        $token->expects($this->once())
              ->method('getRoles')
              ->will($this->returnValue(array($switchUserRole)))
        ;

        $authToken = $provider->authenticate($token);

        $this->assertInstanceOf('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken', $authToken);
        $this->assertSame($user, $authToken->getUser());
        $this->assertContains(new Role('ROLE_FOO'), $authToken->getRoles(), '', false, false);
        $this->assertContains($switchUserRole, $authToken->getRoles());
        $this->assertEquals('foo', $authToken->getCredentials());
        $this->assertEquals(array('foo' => 'bar'), $authToken->getAttributes(), '->authenticate() copies token attributes');
    }

    protected function getSupportedToken()
    {
        $mock = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken', array('getCredentials', 'getProviderKey', 'getRoles'), array(), '', false);
        $mock
            ->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue('key'))
        ;

        $mock->setAttributes(array('foo' => 'bar'));

        return $mock;
    }

    protected function getProvider($userChecker = false, $hide = true)
    {
        if (false === $userChecker) {
            $userChecker = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        }

        return $this->getMockForAbstractClass('Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider', array($userChecker, 'key', $hide));
    }
}
