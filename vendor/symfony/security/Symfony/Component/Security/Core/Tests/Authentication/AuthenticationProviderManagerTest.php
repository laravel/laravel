<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Tests\Authentication;

use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Exception\ProviderNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthenticationProviderManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAuthenticateWithoutProviders()
    {
        new AuthenticationProviderManager(array());
    }

    public function testAuthenticateWhenNoProviderSupportsToken()
    {
        $manager = new AuthenticationProviderManager(array(
            $this->getAuthenticationProvider(false),
        ));

        try {
            $manager->authenticate($token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
            $this->fail();
        } catch (ProviderNotFoundException $e) {
            $this->assertSame($token, $e->getToken());
        }
    }

    public function testAuthenticateWhenProviderReturnsAccountStatusException()
    {
        $manager = new AuthenticationProviderManager(array(
            $this->getAuthenticationProvider(true, null, 'Symfony\Component\Security\Core\Exception\AccountStatusException'),
        ));

        try {
            $manager->authenticate($token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
            $this->fail();
        } catch (AccountStatusException $e) {
            $this->assertSame($token, $e->getToken());
        }
    }

    public function testAuthenticateWhenProviderReturnsAuthenticationException()
    {
        $manager = new AuthenticationProviderManager(array(
            $this->getAuthenticationProvider(true, null, 'Symfony\Component\Security\Core\Exception\AuthenticationException'),
        ));

        try {
            $manager->authenticate($token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
            $this->fail();
        } catch (AuthenticationException $e) {
            $this->assertSame($token, $e->getToken());
        }
    }

    public function testAuthenticateWhenOneReturnsAuthenticationExceptionButNotAll()
    {
        $manager = new AuthenticationProviderManager(array(
            $this->getAuthenticationProvider(true, null, 'Symfony\Component\Security\Core\Exception\AuthenticationException'),
            $this->getAuthenticationProvider(true, $expected = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')),
        ));

        $token = $manager->authenticate($this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
        $this->assertSame($expected, $token);
    }

    public function testAuthenticateReturnsTokenOfTheFirstMatchingProvider()
    {
        $second = $this->getMock('Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface');
        $second
            ->expects($this->never())
            ->method('supports')
        ;
        $manager = new AuthenticationProviderManager(array(
            $this->getAuthenticationProvider(true, $expected = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')),
            $second,
        ));

        $token = $manager->authenticate($this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
        $this->assertSame($expected, $token);
    }

    public function testEraseCredentialFlag()
    {
        $manager = new AuthenticationProviderManager(array(
            $this->getAuthenticationProvider(true, $token = new UsernamePasswordToken('foo', 'bar', 'key')),
        ));

        $token = $manager->authenticate($this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
        $this->assertEquals('', $token->getCredentials());

        $manager = new AuthenticationProviderManager(array(
            $this->getAuthenticationProvider(true, $token = new UsernamePasswordToken('foo', 'bar', 'key')),
        ), false);

        $token = $manager->authenticate($this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
        $this->assertEquals('bar', $token->getCredentials());
    }

    protected function getAuthenticationProvider($supports, $token = null, $exception = null)
    {
        $provider = $this->getMock('Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface');
        $provider->expects($this->once())
                 ->method('supports')
                 ->will($this->returnValue($supports))
        ;

        if (null !== $token) {
            $provider->expects($this->once())
                     ->method('authenticate')
                     ->will($this->returnValue($token))
            ;
        } elseif (null !== $exception) {
            $provider->expects($this->once())
                     ->method('authenticate')
                     ->will($this->throwException($this->getMock($exception, null, array(), '', false)))
            ;
        }

        return $provider;
    }
}
