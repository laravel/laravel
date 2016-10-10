<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Tests;

use Symfony\Component\Security\Core\SecurityContext;

class SecurityContextTest extends \PHPUnit_Framework_TestCase
{
    public function testVoteAuthenticatesTokenIfNecessary()
    {
        $authManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $decisionManager = $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface');

        $context = new SecurityContext($authManager, $decisionManager);
        $context->setToken($token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));

        $authManager
            ->expects($this->once())
            ->method('authenticate')
            ->with($this->equalTo($token))
            ->will($this->returnValue($newToken = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')))
        ;

        $decisionManager
            ->expects($this->once())
            ->method('decide')
            ->will($this->returnValue(true))
        ;

        $this->assertTrue($context->isGranted('foo'));
        $this->assertSame($newToken, $context->getToken());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException
     */
    public function testVoteWithoutAuthenticationToken()
    {
        $context = new SecurityContext(
            $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface'),
            $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface')
        );

        $context->isGranted('ROLE_FOO');
    }

    public function testIsGranted()
    {
        $manager = $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface');
        $manager->expects($this->once())->method('decide')->will($this->returnValue(false));
        $context = new SecurityContext($this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface'), $manager);
        $context->setToken($token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
        $token
            ->expects($this->once())
            ->method('isAuthenticated')
            ->will($this->returnValue(true))
        ;
        $this->assertFalse($context->isGranted('ROLE_FOO'));

        $manager = $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface');
        $manager->expects($this->once())->method('decide')->will($this->returnValue(true));
        $context = new SecurityContext($this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface'), $manager);
        $context->setToken($token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
        $token
            ->expects($this->once())
            ->method('isAuthenticated')
            ->will($this->returnValue(true))
        ;
        $this->assertTrue($context->isGranted('ROLE_FOO'));
    }

    public function testGetSetToken()
    {
        $context = new SecurityContext(
            $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface'),
            $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface')
        );
        $this->assertNull($context->getToken());

        $context->setToken($token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
        $this->assertSame($token, $context->getToken());
    }
}
