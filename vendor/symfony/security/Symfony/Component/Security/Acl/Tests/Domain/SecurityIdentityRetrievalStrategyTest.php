<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Tests\Domain;

use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\SecurityIdentityRetrievalStrategy;

class SecurityIdentityRetrievalStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSecurityIdentityRetrievalTests
     */
    public function testGetSecurityIdentities($user, array $roles, $authenticationStatus, array $sids)
    {
        $strategy = $this->getStrategy($roles, $authenticationStatus);

        if ('anonymous' === $authenticationStatus) {
            $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\AnonymousToken')
                                ->disableOriginalConstructor()
                                ->getMock();
        } else {
            $class = '';
            if (is_string($user)) {
                $class = 'MyCustomTokenImpl';
            }

            $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
                        ->setMockClassName($class)
                        ->getMock();
        }
        $token
            ->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue(array('foo')))
        ;
        if ('anonymous' === $authenticationStatus) {
            $token
                ->expects($this->never())
                ->method('getUser')
            ;
        } else {
            $token
                ->expects($this->once())
                ->method('getUser')
                ->will($this->returnValue($user))
            ;
        }

        $extractedSids = $strategy->getSecurityIdentities($token);

        foreach ($extractedSids as $index => $extractedSid) {
            if (!isset($sids[$index])) {
                $this->fail(sprintf('Expected SID at index %d, but there was none.', true));
            }

            if (false === $sids[$index]->equals($extractedSid)) {
                $this->fail(sprintf('Index: %d, expected SID "%s", but got "%s".', $index, $sids[$index], $extractedSid));
            }
        }
    }

    public function getSecurityIdentityRetrievalTests()
    {
        return array(
            array($this->getAccount('johannes', 'FooUser'), array('ROLE_USER', 'ROLE_SUPERADMIN'), 'fullFledged', array(
                new UserSecurityIdentity('johannes', 'FooUser'),
                new RoleSecurityIdentity('ROLE_USER'),
                new RoleSecurityIdentity('ROLE_SUPERADMIN'),
                new RoleSecurityIdentity('IS_AUTHENTICATED_FULLY'),
                new RoleSecurityIdentity('IS_AUTHENTICATED_REMEMBERED'),
                new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'),
            )),
            array('johannes', array('ROLE_FOO'), 'fullFledged', array(
                new UserSecurityIdentity('johannes', 'MyCustomTokenImpl'),
                new RoleSecurityIdentity('ROLE_FOO'),
                new RoleSecurityIdentity('IS_AUTHENTICATED_FULLY'),
                new RoleSecurityIdentity('IS_AUTHENTICATED_REMEMBERED'),
                new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'),
            )),
            array(new CustomUserImpl('johannes'), array('ROLE_FOO'), 'fullFledged', array(
                new UserSecurityIdentity('johannes', 'Symfony\Component\Security\Acl\Tests\Domain\CustomUserImpl'),
                new RoleSecurityIdentity('ROLE_FOO'),
                new RoleSecurityIdentity('IS_AUTHENTICATED_FULLY'),
                new RoleSecurityIdentity('IS_AUTHENTICATED_REMEMBERED'),
                new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'),
            )),
            array($this->getAccount('foo', 'FooBarUser'), array('ROLE_FOO'), 'rememberMe', array(
                new UserSecurityIdentity('foo', 'FooBarUser'),
                new RoleSecurityIdentity('ROLE_FOO'),
                new RoleSecurityIdentity('IS_AUTHENTICATED_REMEMBERED'),
                new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'),
            )),
            array('guest', array('ROLE_FOO'), 'anonymous', array(
                new RoleSecurityIdentity('ROLE_FOO'),
                new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'),
            ))
        );
    }

    protected function getAccount($username, $class)
    {
        $account = $this->getMock('Symfony\Component\Security\Core\User\UserInterface', array(), array(), $class);
        $account
            ->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue($username))
        ;

        return $account;
    }

    protected function getStrategy(array $roles = array(), $authenticationStatus = 'fullFledged')
    {
        $roleHierarchy = $this->getMock('Symfony\Component\Security\Core\Role\RoleHierarchyInterface');
        $roleHierarchy
            ->expects($this->once())
            ->method('getReachableRoles')
            ->with($this->equalTo(array('foo')))
            ->will($this->returnValue($roles))
        ;

        $trustResolver = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver', array(), array('', ''));

        $trustResolver
            ->expects($this->at(0))
            ->method('isAnonymous')
            ->will($this->returnValue('anonymous' === $authenticationStatus))
        ;

        if ('fullFledged' === $authenticationStatus) {
            $trustResolver
                ->expects($this->once())
                ->method('isFullFledged')
                ->will($this->returnValue(true))
            ;
            $trustResolver
                ->expects($this->never())
                ->method('isRememberMe')
            ;
        } elseif ('rememberMe' === $authenticationStatus) {
            $trustResolver
                ->expects($this->once())
                ->method('isFullFledged')
                ->will($this->returnValue(false))
            ;
            $trustResolver
                ->expects($this->once())
                ->method('isRememberMe')
                ->will($this->returnValue(true))
            ;
        } else {
            $trustResolver
                ->expects($this->at(1))
                ->method('isAnonymous')
                ->will($this->returnValue(true))
            ;
            $trustResolver
                ->expects($this->once())
                ->method('isFullFledged')
                ->will($this->returnValue(false))
            ;
            $trustResolver
                ->expects($this->once())
                ->method('isRememberMe')
                ->will($this->returnValue(false))
            ;
        }

        return new SecurityIdentityRetrievalStrategy($roleHierarchy, $trustResolver);
    }
}

class CustomUserImpl
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
    }
}
