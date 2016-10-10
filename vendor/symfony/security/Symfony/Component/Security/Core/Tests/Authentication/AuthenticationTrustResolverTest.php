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

use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;

class AuthenticationTrustResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAnonymous()
    {
        $resolver = $this->getResolver();

        $this->assertFalse($resolver->isAnonymous(null));
        $this->assertFalse($resolver->isAnonymous($this->getToken()));
        $this->assertFalse($resolver->isAnonymous($this->getRememberMeToken()));
        $this->assertTrue($resolver->isAnonymous($this->getAnonymousToken()));
    }

    public function testIsRememberMe()
    {
        $resolver = $this->getResolver();

        $this->assertFalse($resolver->isRememberMe(null));
        $this->assertFalse($resolver->isRememberMe($this->getToken()));
        $this->assertFalse($resolver->isRememberMe($this->getAnonymousToken()));
        $this->assertTrue($resolver->isRememberMe($this->getRememberMeToken()));
    }

    public function testisFullFledged()
    {
        $resolver = $this->getResolver();

        $this->assertFalse($resolver->isFullFledged(null));
        $this->assertFalse($resolver->isFullFledged($this->getAnonymousToken()));
        $this->assertFalse($resolver->isFullFledged($this->getRememberMeToken()));
        $this->assertTrue($resolver->isFullFledged($this->getToken()));
    }

    protected function getToken()
    {
        return $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
    }

    protected function getAnonymousToken()
    {
        return $this->getMock('Symfony\Component\Security\Core\Authentication\Token\AnonymousToken', null, array('', ''));
    }

    protected function getRememberMeToken()
    {
        return $this->getMock('Symfony\Component\Security\Core\Authentication\Token\RememberMeToken', array('setPersistent'), array(), '', false);
    }

    protected function getResolver()
    {
        return new AuthenticationTrustResolver(
            'Symfony\\Component\\Security\\Core\\Authentication\\Token\\AnonymousToken',
            'Symfony\\Component\\Security\\Core\\Authentication\\Token\\RememberMeToken'
        );
    }
}
