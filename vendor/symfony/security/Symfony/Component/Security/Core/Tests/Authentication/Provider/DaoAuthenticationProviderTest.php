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

use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;

class DaoAuthenticationProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationServiceException
     */
    public function testRetrieveUserWhenProviderDoesNotReturnAnUserInterface()
    {
        $provider = $this->getProvider('fabien');
        $method = new \ReflectionMethod($provider, 'retrieveUser');
        $method->setAccessible(true);

        $method->invoke($provider, 'fabien', $this->getSupportedToken());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testRetrieveUserWhenUsernameIsNotFound()
    {
        $userProvider = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserProviderInterface');
        $userProvider->expects($this->once())
                     ->method('loadUserByUsername')
                     ->will($this->throwException($this->getMock('Symfony\\Component\\Security\\Core\\Exception\\UsernameNotFoundException', null, array(), '', false)))
        ;

        $provider = new DaoAuthenticationProvider($userProvider, $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserCheckerInterface'), 'key', $this->getMock('Symfony\\Component\\Security\\Core\\Encoder\\EncoderFactoryInterface'));
        $method = new \ReflectionMethod($provider, 'retrieveUser');
        $method->setAccessible(true);

        $method->invoke($provider, 'fabien', $this->getSupportedToken());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationServiceException
     */
    public function testRetrieveUserWhenAnExceptionOccurs()
    {
        $userProvider = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserProviderInterface');
        $userProvider->expects($this->once())
                     ->method('loadUserByUsername')
                     ->will($this->throwException($this->getMock('RuntimeException', null, array(), '', false)))
        ;

        $provider = new DaoAuthenticationProvider($userProvider, $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserCheckerInterface'), 'key', $this->getMock('Symfony\\Component\\Security\\Core\\Encoder\\EncoderFactoryInterface'));
        $method = new \ReflectionMethod($provider, 'retrieveUser');
        $method->setAccessible(true);

        $method->invoke($provider, 'fabien', $this->getSupportedToken());
    }

    public function testRetrieveUserReturnsUserFromTokenOnReauthentication()
    {
        $userProvider = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserProviderInterface');
        $userProvider->expects($this->never())
                     ->method('loadUserByUsername')
        ;

        $user = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface');
        $token = $this->getSupportedToken();
        $token->expects($this->once())
              ->method('getUser')
              ->will($this->returnValue($user))
        ;

        $provider = new DaoAuthenticationProvider($userProvider, $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserCheckerInterface'), 'key', $this->getMock('Symfony\\Component\\Security\\Core\\Encoder\\EncoderFactoryInterface'));
        $reflection = new \ReflectionMethod($provider, 'retrieveUser');
        $reflection->setAccessible(true);
        $result = $reflection->invoke($provider, null, $token);

        $this->assertSame($user, $result);
    }

    public function testRetrieveUser()
    {
        $user = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface');

        $userProvider = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserProviderInterface');
        $userProvider->expects($this->once())
                     ->method('loadUserByUsername')
                     ->will($this->returnValue($user))
        ;

        $provider = new DaoAuthenticationProvider($userProvider, $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserCheckerInterface'), 'key', $this->getMock('Symfony\\Component\\Security\\Core\\Encoder\\EncoderFactoryInterface'));
        $method = new \ReflectionMethod($provider, 'retrieveUser');
        $method->setAccessible(true);

        $this->assertSame($user, $method->invoke($provider, 'fabien', $this->getSupportedToken()));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testCheckAuthenticationWhenCredentialsAreEmpty()
    {
        $encoder = $this->getMock('Symfony\\Component\\Security\\Core\\Encoder\\PasswordEncoderInterface');
        $encoder
            ->expects($this->never())
            ->method('isPasswordValid')
        ;

        $provider = $this->getProvider(null, null, $encoder);
        $method = new \ReflectionMethod($provider, 'checkAuthentication');
        $method->setAccessible(true);

        $token = $this->getSupportedToken();
        $token
            ->expects($this->once())
            ->method('getCredentials')
            ->will($this->returnValue(''))
        ;

        $method->invoke(
            $provider,
            $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface'),
            $token
        );
    }

    public function testCheckAuthenticationWhenCredentialsAre0()
    {
        $encoder = $this->getMock('Symfony\\Component\\Security\\Core\\Encoder\\PasswordEncoderInterface');
        $encoder
            ->expects($this->once())
            ->method('isPasswordValid')
            ->will($this->returnValue(true))
        ;

        $provider = $this->getProvider(null, null, $encoder);
        $method = new \ReflectionMethod($provider, 'checkAuthentication');
        $method->setAccessible(true);

        $token = $this->getSupportedToken();
        $token
            ->expects($this->once())
            ->method('getCredentials')
            ->will($this->returnValue('0'))
        ;

        $method->invoke(
            $provider,
            $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface'),
            $token
        );
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testCheckAuthenticationWhenCredentialsAreNotValid()
    {
        $encoder = $this->getMock('Symfony\\Component\\Security\\Core\\Encoder\\PasswordEncoderInterface');
        $encoder->expects($this->once())
                ->method('isPasswordValid')
                ->will($this->returnValue(false))
        ;

        $provider = $this->getProvider(null, null, $encoder);
        $method = new \ReflectionMethod($provider, 'checkAuthentication');
        $method->setAccessible(true);

        $token = $this->getSupportedToken();
        $token->expects($this->once())
              ->method('getCredentials')
              ->will($this->returnValue('foo'))
        ;

        $method->invoke($provider, $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface'), $token);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testCheckAuthenticationDoesNotReauthenticateWhenPasswordHasChanged()
    {
        $user = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface');
        $user->expects($this->once())
             ->method('getPassword')
             ->will($this->returnValue('foo'))
        ;

        $token = $this->getSupportedToken();
        $token->expects($this->once())
              ->method('getUser')
              ->will($this->returnValue($user));

        $dbUser = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface');
        $dbUser->expects($this->once())
               ->method('getPassword')
               ->will($this->returnValue('newFoo'))
        ;

        $provider = $this->getProvider();
        $reflection = new \ReflectionMethod($provider, 'checkAuthentication');
        $reflection->setAccessible(true);
        $reflection->invoke($provider, $dbUser, $token);
    }

    public function testCheckAuthenticationWhenTokenNeedsReauthenticationWorksWithoutOriginalCredentials()
    {
        $user = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface');
        $user->expects($this->once())
             ->method('getPassword')
             ->will($this->returnValue('foo'))
        ;

        $token = $this->getSupportedToken();
        $token->expects($this->once())
              ->method('getUser')
              ->will($this->returnValue($user));

        $dbUser = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface');
        $dbUser->expects($this->once())
               ->method('getPassword')
               ->will($this->returnValue('foo'))
        ;

        $provider = $this->getProvider();
        $reflection = new \ReflectionMethod($provider, 'checkAuthentication');
        $reflection->setAccessible(true);
        $reflection->invoke($provider, $dbUser, $token);
    }

    public function testCheckAuthentication()
    {
        $encoder = $this->getMock('Symfony\\Component\\Security\\Core\\Encoder\\PasswordEncoderInterface');
        $encoder->expects($this->once())
                ->method('isPasswordValid')
                ->will($this->returnValue(true))
        ;

        $provider = $this->getProvider(null, null, $encoder);
        $method = new \ReflectionMethod($provider, 'checkAuthentication');
        $method->setAccessible(true);

        $token = $this->getSupportedToken();
        $token->expects($this->once())
              ->method('getCredentials')
              ->will($this->returnValue('foo'))
        ;

        $method->invoke($provider, $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface'), $token);
    }

    protected function getSupportedToken()
    {
        $mock = $this->getMock('Symfony\\Component\\Security\\Core\\Authentication\\Token\\UsernamePasswordToken', array('getCredentials', 'getUser', 'getProviderKey'), array(), '', false);
        $mock
            ->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue('key'))
        ;

        return $mock;
    }

    protected function getProvider($user = null, $userChecker = null, $passwordEncoder = null)
    {
        $userProvider = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserProviderInterface');
        if (null !== $user) {
            $userProvider->expects($this->once())
                         ->method('loadUserByUsername')
                         ->will($this->returnValue($user))
            ;
        }

        if (null === $userChecker) {
            $userChecker = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserCheckerInterface');
        }

        if (null === $passwordEncoder) {
            $passwordEncoder = new PlaintextPasswordEncoder();
        }

        $encoderFactory = $this->getMock('Symfony\\Component\\Security\\Core\\Encoder\\EncoderFactoryInterface');
        $encoderFactory
            ->expects($this->any())
            ->method('getEncoder')
            ->will($this->returnValue($passwordEncoder))
        ;

        return new DaoAuthenticationProvider($userProvider, $userChecker, 'key', $encoderFactory);
    }
}
