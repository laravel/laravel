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

use Symfony\Component\Security\Acl\Domain\Entry;

class EntryTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $ace = $this->getAce($acl = $this->getAcl(), $sid = $this->getSid());

        $this->assertEquals(123, $ace->getId());
        $this->assertSame($acl, $ace->getAcl());
        $this->assertSame($sid, $ace->getSecurityIdentity());
        $this->assertEquals('foostrat', $ace->getStrategy());
        $this->assertEquals(123456, $ace->getMask());
        $this->assertTrue($ace->isGranting());
        $this->assertTrue($ace->isAuditSuccess());
        $this->assertFalse($ace->isAuditFailure());
    }

    public function testSetAuditSuccess()
    {
        $ace = $this->getAce();

        $this->assertTrue($ace->isAuditSuccess());
        $ace->setAuditSuccess(false);
        $this->assertFalse($ace->isAuditSuccess());
        $ace->setAuditsuccess(true);
        $this->assertTrue($ace->isAuditSuccess());
    }

    public function testSetAuditFailure()
    {
        $ace = $this->getAce();

        $this->assertFalse($ace->isAuditFailure());
        $ace->setAuditFailure(true);
        $this->assertTrue($ace->isAuditFailure());
        $ace->setAuditFailure(false);
        $this->assertFalse($ace->isAuditFailure());
    }

    public function testSetMask()
    {
        $ace = $this->getAce();

        $this->assertEquals(123456, $ace->getMask());
        $ace->setMask(4321);
        $this->assertEquals(4321, $ace->getMask());
    }

    public function testSetStrategy()
    {
        $ace = $this->getAce();

        $this->assertEquals('foostrat', $ace->getStrategy());
        $ace->setStrategy('foo');
        $this->assertEquals('foo', $ace->getStrategy());
    }

    public function testSerializeUnserialize()
    {
        $ace = $this->getAce();

        $serialized = serialize($ace);
        $uAce = unserialize($serialized);

        $this->assertNull($uAce->getAcl());
        $this->assertInstanceOf('Symfony\Component\Security\Acl\Model\SecurityIdentityInterface', $uAce->getSecurityIdentity());
        $this->assertEquals($ace->getId(), $uAce->getId());
        $this->assertEquals($ace->getMask(), $uAce->getMask());
        $this->assertEquals($ace->getStrategy(), $uAce->getStrategy());
        $this->assertEquals($ace->isGranting(), $uAce->isGranting());
        $this->assertEquals($ace->isAuditSuccess(), $uAce->isAuditSuccess());
        $this->assertEquals($ace->isAuditFailure(), $uAce->isAuditFailure());
    }

    protected function getAce($acl = null, $sid = null)
    {
        if (null === $acl) {
            $acl = $this->getAcl();
        }
        if (null === $sid) {
            $sid = $this->getSid();
        }

        return new Entry(
            123,
            $acl,
            $sid,
            'foostrat',
            123456,
            true,
            false,
            true
        );
    }

    protected function getAcl()
    {
        return $this->getMock('Symfony\Component\Security\Acl\Model\AclInterface');
    }

    protected function getSid()
    {
        return $this->getMock('Symfony\Component\Security\Acl\Model\SecurityIdentityInterface');
    }
}
