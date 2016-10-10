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

use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\DoctrineAclCache;
use Doctrine\Common\Cache\ArrayCache;

class DoctrineAclCacheTest extends \PHPUnit_Framework_TestCase
{
    protected $permissionGrantingStrategy;

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider getEmptyValue
     */
    public function testConstructorDoesNotAcceptEmptyPrefix($empty)
    {
        new DoctrineAclCache(new ArrayCache(), $this->getPermissionGrantingStrategy(), $empty);
    }

    public function getEmptyValue()
    {
        return array(
            array(null),
            array(false),
            array(''),
        );
    }

    public function test()
    {
        $cache = $this->getCache();

        $aclWithParent = $this->getAcl(1);
        $acl = $this->getAcl();

        $cache->putInCache($aclWithParent);
        $cache->putInCache($acl);

        $cachedAcl = $cache->getFromCacheByIdentity($acl->getObjectIdentity());
        $this->assertEquals($acl->getId(), $cachedAcl->getId());
        $this->assertNull($acl->getParentAcl());

        $cachedAclWithParent = $cache->getFromCacheByIdentity($aclWithParent->getObjectIdentity());
        $this->assertEquals($aclWithParent->getId(), $cachedAclWithParent->getId());
        $this->assertNotNull($cachedParentAcl = $cachedAclWithParent->getParentAcl());
        $this->assertEquals($aclWithParent->getParentAcl()->getId(), $cachedParentAcl->getId());
    }

    protected function getAcl($depth = 0)
    {
        static $id = 1;

        $acl = new Acl($id, new ObjectIdentity($id, 'foo'), $this->getPermissionGrantingStrategy(), array(), $depth > 0);

        // insert some ACEs
        $sid = new UserSecurityIdentity('johannes', 'Foo');
        $acl->insertClassAce($sid, 1);
        $acl->insertClassFieldAce('foo', $sid, 1);
        $acl->insertObjectAce($sid, 1);
        $acl->insertObjectFieldAce('foo', $sid, 1);
        $id++;

        if ($depth > 0) {
            $acl->setParentAcl($this->getAcl($depth - 1));
        }

        return $acl;
    }

    protected function getPermissionGrantingStrategy()
    {
        if (null === $this->permissionGrantingStrategy) {
            $this->permissionGrantingStrategy = new PermissionGrantingStrategy();
        }

        return $this->permissionGrantingStrategy;
    }

    protected function getCache($cacheDriver = null, $prefix = DoctrineAclCache::PREFIX)
    {
        if (null === $cacheDriver) {
            $cacheDriver = new ArrayCache();
        }

        return new DoctrineAclCache($cacheDriver, $this->getPermissionGrantingStrategy(), $prefix);
    }
}
