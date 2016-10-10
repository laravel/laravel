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

use Symfony\Component\Security\Core\Authentication\Provider\PreAuthenticatedAuthenticationProvider;

class PreAuthenticatedAuthenticationProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testSupports()
    {
        $provider = $this->getProvider();

        $this->assertTrue($provider->supports($this->getSupportedToken()));
        $this->assertFalse($provider->supports($this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')));

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken')
                    ->disableOriginalConstructor()
                    ->getMock()
        ;
        $token
            ->expects($this->once())
            ->method('getProviderKey')
            ->will($this->returnValue('foo'))
        ;
        $this->assertFalse($provider->supports($token));
    }

    public function testAuthenticateWhenTokenIsNotSupported()
    {
        $provider = $this->getProvider();

        $this->assertNull($provider->authenticate($this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testAuthenticateWhenNoUserIsSet()
    {
        $provider = $this->getProvider();
        $provider->authenticate($this->getSupportedToken(''));
    }

    public function testAuthenticate()
    {
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user
            ->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue(array()))
        ;
        $provider = $this->getProvider($user);

        $token = $provider->authenticate($this->getSupportedToken('fabien', 'pass'));
        $this->assertInstanceOf('Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken', $token);
        $this->assertEquals('pass', $token->getCredentials());
        $this->assertEquals('key', $token->getProviderKey());
        $this->assertEquals(array(), $token->getRoles());
        $this->assertEquals(array('foo' => 'bar'), $token->getAttributes(), '->authenticate() copies token attributes');
        $this->assertSame($user, $token->getUser());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\LockedException
     */
    public function testAuthenticateWhenUserCheckerThrowsException()
    {
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');

        $userChecker = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $userChecker->expects($this->once())
                    ->method('checkPostAuth')
                    ->will($this->throwException($this->getMock('Symfony\Component\Security\Core\Exception\LockedException', null, array(), '', false)))
        ;

        $provider = $this->getProvider($user, $userChecker);

        $provider->authenticate($this->getSupportedToken('fabien'));
    }

    protected function getSupportedToken($user = false, $credentials = false)
    {
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken', array('getUser', 'getCredentials', 'getProviderKey'), array(), '', false);
        if (false !== $user) {
            $token->expects($this->once())
                  ->method('getUser')
                  ->will($this->returnValue($user))
            ;
        }
        if (false !== $credentials) {
            $token->expects($this->once())
                  ->method('getCredentials')
                  ->will($this->returnValue($credentials))
            ;
        }

        $token
            ->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue('key'))
        ;

        $token->setAttributes(array('foo' => 'bar'));

        return $token;
    }

    protected function getProvider($user = null, $userChecker = null)
    {
        $userProvider = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        if (null !== $user) {
            $userProvider->expects($this->once())
                         ->method('loadUserByUsername')
                         ->will($this->returnValue($user))
            ;
        }

        if (null === $userChecker) {
            $userChecker = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        }

        return new PreAuthenticatedAuthenticationProvider($userProvider, $userChecker, 'key');
    }
}
