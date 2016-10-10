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

use Symfony\Component\Security\Core\Authentication\Provider\RememberMeAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Role\Role;

class RememberMeAuthenticationProviderTest extends \PHPUnit_Framework_TestCase
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

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->assertNull($provider->authenticate($token));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testAuthenticateWhenKeysDoNotMatch()
    {
        $provider = $this->getProvider(null, 'key1');
        $token = $this->getSupportedToken(null, 'key2');

        $provider->authenticate($token);
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

        $provider->authenticate($this->getSupportedToken());
    }

    public function testAuthenticate()
    {
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->exactly(2))
             ->method('getRoles')
             ->will($this->returnValue(array('ROLE_FOO')))
        ;

        $provider = $this->getProvider();

        $token = $this->getSupportedToken($user);
        $authToken = $provider->authenticate($token);

        $this->assertInstanceOf('Symfony\Component\Security\Core\Authentication\Token\RememberMeToken', $authToken);
        $this->assertSame($user, $authToken->getUser());
        $this->assertEquals(array(new Role('ROLE_FOO')), $authToken->getRoles());
        $this->assertEquals('', $authToken->getCredentials());
    }

    protected function getSupportedToken($user = null, $key = 'test')
    {
        if (null === $user) {
            $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
            $user
                ->expects($this->any())
                ->method('getRoles')
                ->will($this->returnValue(array()))
            ;
        }

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\RememberMeToken', array('getProviderKey'), array($user, 'foo', $key));
        $token
            ->expects($this->once())
            ->method('getProviderKey')
            ->will($this->returnValue('foo'))
        ;

        return $token;
    }

    protected function getProvider($userChecker = null, $key = 'test')
    {
        if (null === $userChecker) {
            $userChecker = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        }

        return new RememberMeAuthenticationProvider($userChecker, $key, 'foo');
    }
}
