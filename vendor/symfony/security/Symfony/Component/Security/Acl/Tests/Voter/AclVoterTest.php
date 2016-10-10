<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Tests\Voter;

use Symfony\Component\Security\Acl\Exception\NoAceFoundException;
use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Acl\Voter\AclVoter;

class AclVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSupportsAttributeTests
     */
    public function testSupportsAttribute($attribute, $supported)
    {
        list($voter,, $permissionMap,,) = $this->getVoter();

        $permissionMap
            ->expects($this->once())
            ->method('contains')
            ->with($this->identicalTo($attribute))
            ->will($this->returnValue($supported))
        ;

        $this->assertSame($supported, $voter->supportsAttribute($attribute));
    }

    public function getSupportsAttributeTests()
    {
        return array(
            array('foo', true),
            array('foo', false),
        );
    }

    /**
     * @dataProvider getSupportsClassTests
     */
    public function testSupportsClass($class)
    {
        list($voter,,,,) = $this->getVoter();

        $this->assertTrue($voter->supportsClass($class));
    }

    public function getSupportsClassTests()
    {
        return array(
            array('foo'),
            array('bar'),
            array('moo'),
        );
    }

    public function testVote()
    {
        list($voter,, $permissionMap,,) = $this->getVoter();
        $permissionMap
            ->expects($this->atLeastOnce())
            ->method('getMasks')
            ->will($this->returnValue(null))
        ;

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($this->getToken(), null, array('VIEW', 'EDIT', 'DELETE')));
    }

    /**
     * @dataProvider getTrueFalseTests
     */
    public function testVoteWhenNoObjectIsPassed($allowIfObjectIdentityUnavailable)
    {
        list($voter,, $permissionMap,,) = $this->getVoter($allowIfObjectIdentityUnavailable);
        $permissionMap
            ->expects($this->once())
            ->method('getMasks')
            ->will($this->returnValue(array()))
        ;

        if ($allowIfObjectIdentityUnavailable) {
            $vote = VoterInterface::ACCESS_GRANTED;
        } else {
            $vote = VoterInterface::ACCESS_ABSTAIN;
        }

        $this->assertSame($vote, $voter->vote($this->getToken(), null, array('VIEW')));
    }

    /**
     * @dataProvider getTrueFalseTests
     */
    public function testVoteWhenOidStrategyReturnsNull($allowIfUnavailable)
    {
        list($voter,, $permissionMap, $oidStrategy,) = $this->getVoter($allowIfUnavailable);
        $permissionMap
            ->expects($this->once())
            ->method('getMasks')
            ->will($this->returnValue(array()))
        ;

        $oidStrategy
            ->expects($this->once())
            ->method('getObjectIdentity')
            ->will($this->returnValue(null))
        ;

        if ($allowIfUnavailable) {
            $vote = VoterInterface::ACCESS_GRANTED;
        } else {
            $vote = VoterInterface::ACCESS_ABSTAIN;
        }

        $this->assertSame($vote, $voter->vote($this->getToken(), new \stdClass(), array('VIEW')));
    }

    public function getTrueFalseTests()
    {
        return array(array(true), array(false));
    }

    public function testVoteNoAclFound()
    {
        list($voter, $provider, $permissionMap, $oidStrategy, $sidStrategy) = $this->getVoter();

        $permissionMap
            ->expects($this->once())
            ->method('getMasks')
            ->will($this->returnValue(array()))
        ;

        $oidStrategy
            ->expects($this->once())
            ->method('getObjectIdentity')
            ->will($this->returnValue($oid = new ObjectIdentity('1', 'Foo')))
        ;

        $sidStrategy
            ->expects($this->once())
            ->method('getSecurityIdentities')
            ->will($this->returnValue($sids = array(new UserSecurityIdentity('johannes', 'Foo'), new RoleSecurityIdentity('ROLE_FOO'))))
        ;

        $provider
            ->expects($this->once())
            ->method('findAcl')
            ->with($this->equalTo($oid), $this->equalTo($sids))
            ->will($this->throwException(new AclNotFoundException('Not found.')))
        ;

        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($this->getToken(), new \stdClass(), array('VIEW')));
    }

    /**
     * @dataProvider getTrueFalseTests
     */
    public function testVoteGrantsAccess($grant)
    {
        list($voter, $provider, $permissionMap, $oidStrategy, $sidStrategy) = $this->getVoter();

        $permissionMap
            ->expects($this->once())
            ->method('getMasks')
            ->with($this->equalTo('VIEW'))
            ->will($this->returnValue($masks = array(1, 2, 3)))
        ;

        $oidStrategy
            ->expects($this->once())
            ->method('getObjectIdentity')
            ->will($this->returnValue($oid = new ObjectIdentity('1', 'Foo')))
        ;

        $sidStrategy
            ->expects($this->once())
            ->method('getSecurityIdentities')
            ->will($this->returnValue($sids = array(new UserSecurityIdentity('johannes', 'Foo'), new RoleSecurityIdentity('ROLE_FOO'))))
        ;

        $provider
            ->expects($this->once())
            ->method('findAcl')
            ->with($this->equalTo($oid), $this->equalTo($sids))
            ->will($this->returnValue($acl = $this->getMock('Symfony\Component\Security\Acl\Model\AclInterface')))
        ;

        $acl
            ->expects($this->once())
            ->method('isGranted')
            ->with($this->identicalTo($masks), $this->equalTo($sids), $this->isFalse())
            ->will($this->returnValue($grant))
        ;

        if ($grant) {
            $vote = VoterInterface::ACCESS_GRANTED;
        } else {
            $vote = VoterInterface::ACCESS_DENIED;
        }

        $this->assertSame($vote, $voter->vote($this->getToken(), new \stdClass(), array('VIEW')));
    }

    public function testVoteNoAceFound()
    {
        list($voter, $provider, $permissionMap, $oidStrategy, $sidStrategy) = $this->getVoter();

        $permissionMap
            ->expects($this->once())
            ->method('getMasks')
            ->with($this->equalTo('VIEW'))
            ->will($this->returnValue($masks = array(1, 2, 3)))
        ;

        $oidStrategy
            ->expects($this->once())
            ->method('getObjectIdentity')
            ->will($this->returnValue($oid = new ObjectIdentity('1', 'Foo')))
        ;

        $sidStrategy
            ->expects($this->once())
            ->method('getSecurityIdentities')
            ->will($this->returnValue($sids = array(new UserSecurityIdentity('johannes', 'Foo'), new RoleSecurityIdentity('ROLE_FOO'))))
        ;

        $provider
            ->expects($this->once())
            ->method('findAcl')
            ->with($this->equalTo($oid), $this->equalTo($sids))
            ->will($this->returnValue($acl = $this->getMock('Symfony\Component\Security\Acl\Model\AclInterface')))
        ;

        $acl
            ->expects($this->once())
            ->method('isGranted')
            ->with($this->identicalTo($masks), $this->equalTo($sids), $this->isFalse())
            ->will($this->throwException(new NoAceFoundException('No ACE')))
        ;

        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($this->getToken(), new \stdClass(), array('VIEW')));
    }

    /**
     * @dataProvider getTrueFalseTests
     */
    public function testVoteGrantsFieldAccess($grant)
    {
        list($voter, $provider, $permissionMap, $oidStrategy, $sidStrategy) = $this->getVoter();

        $permissionMap
            ->expects($this->once())
            ->method('getMasks')
            ->with($this->equalTo('VIEW'))
            ->will($this->returnValue($masks = array(1, 2, 3)))
        ;

        $oidStrategy
            ->expects($this->once())
            ->method('getObjectIdentity')
            ->will($this->returnValue($oid = new ObjectIdentity('1', 'Foo')))
        ;

        $sidStrategy
            ->expects($this->once())
            ->method('getSecurityIdentities')
            ->will($this->returnValue($sids = array(new UserSecurityIdentity('johannes', 'Foo'), new RoleSecurityIdentity('ROLE_FOO'))))
        ;

        $provider
            ->expects($this->once())
            ->method('findAcl')
            ->with($this->equalTo($oid), $this->equalTo($sids))
            ->will($this->returnValue($acl = $this->getMock('Symfony\Component\Security\Acl\Model\AclInterface')))
        ;

        $acl
            ->expects($this->once())
            ->method('isFieldGranted')
            ->with($this->identicalTo('foo'), $this->identicalTo($masks), $this->equalTo($sids), $this->isFalse())
            ->will($this->returnValue($grant))
        ;

        if ($grant) {
            $vote = VoterInterface::ACCESS_GRANTED;
        } else {
            $vote = VoterInterface::ACCESS_DENIED;
        }

        $this->assertSame($vote, $voter->vote($this->getToken(), new FieldVote(new \stdClass(), 'foo'), array('VIEW')));
    }

    public function testVoteNoFieldAceFound()
    {
        list($voter, $provider, $permissionMap, $oidStrategy, $sidStrategy) = $this->getVoter();

        $permissionMap
            ->expects($this->once())
            ->method('getMasks')
            ->with($this->equalTo('VIEW'))
            ->will($this->returnValue($masks = array(1, 2, 3)))
        ;

        $oidStrategy
            ->expects($this->once())
            ->method('getObjectIdentity')
            ->will($this->returnValue($oid = new ObjectIdentity('1', 'Foo')))
        ;

        $sidStrategy
            ->expects($this->once())
            ->method('getSecurityIdentities')
            ->will($this->returnValue($sids = array(new UserSecurityIdentity('johannes', 'Foo'), new RoleSecurityIdentity('ROLE_FOO'))))
        ;

        $provider
            ->expects($this->once())
            ->method('findAcl')
            ->with($this->equalTo($oid), $this->equalTo($sids))
            ->will($this->returnValue($acl = $this->getMock('Symfony\Component\Security\Acl\Model\AclInterface')))
        ;

        $acl
            ->expects($this->once())
            ->method('isFieldGranted')
            ->with($this->identicalTo('foo'), $this->identicalTo($masks), $this->equalTo($sids), $this->isFalse())
            ->will($this->throwException(new NoAceFoundException('No ACE')))
        ;

        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($this->getToken(), new FieldVote(new \stdClass(), 'foo'), array('VIEW')));
    }

    public function testWhenReceivingAnObjectIdentityInterfaceWeDontRetrieveANewObjectIdentity()
    {
        list($voter, $provider, $permissionMap, $oidStrategy, $sidStrategy) = $this->getVoter();

        $oid = new ObjectIdentity('someID','someType');

        $permissionMap
            ->expects($this->once())
            ->method('getMasks')
            ->with($this->equalTo('VIEW'))
            ->will($this->returnValue($masks = array(1, 2, 3)))
        ;

        $oidStrategy
            ->expects($this->never())
            ->method('getObjectIdentity')
        ;

        $sidStrategy
            ->expects($this->once())
            ->method('getSecurityIdentities')
            ->will($this->returnValue($sids = array(new UserSecurityIdentity('johannes', 'Foo'), new RoleSecurityIdentity('ROLE_FOO'))))
        ;

        $provider
            ->expects($this->once())
            ->method('findAcl')
            ->with($this->equalTo($oid), $this->equalTo($sids))
            ->will($this->returnValue($acl = $this->getMock('Symfony\Component\Security\Acl\Model\AclInterface')))
        ;

        $acl
            ->expects($this->once())
            ->method('isGranted')
            ->with($this->identicalTo($masks), $this->equalTo($sids), $this->isFalse())
            ->will($this->throwException(new NoAceFoundException('No ACE')))
        ;

        $voter->vote($this->getToken(), $oid, array('VIEW'));
    }

    protected function getToken()
    {
        return $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
    }

    protected function getVoter($allowIfObjectIdentityUnavailable = true)
    {
        $provider = $this->getMock('Symfony\Component\Security\Acl\Model\AclProviderInterface');
        $permissionMap = $this->getMock('Symfony\Component\Security\Acl\Permission\PermissionMapInterface');
        $oidStrategy = $this->getMock('Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface');
        $sidStrategy = $this->getMock('Symfony\Component\Security\Acl\Model\SecurityIdentityRetrievalStrategyInterface');

        return array(
            new AclVoter($provider, $oidStrategy, $sidStrategy, $permissionMap, null, $allowIfObjectIdentityUnavailable),
            $provider,
            $permissionMap,
            $oidStrategy,
            $sidStrategy,
        );
    }
}
