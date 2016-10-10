<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Tests\Dbal;

use Symfony\Component\Security\Acl\Dbal\AclProvider;
use Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Dbal\Schema;
use Doctrine\DBAL\DriverManager;

class AclProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $con;
    protected $insertClassStmt;
    protected $insertEntryStmt;
    protected $insertOidStmt;
    protected $insertOidAncestorStmt;
    protected $insertSidStmt;

    /**
     * @expectedException \Symfony\Component\Security\Acl\Exception\AclNotFoundException
     * @expectedMessage There is no ACL for the given object identity.
     */
    public function testFindAclThrowsExceptionWhenNoAclExists()
    {
        $this->getProvider()->findAcl(new ObjectIdentity('foo', 'foo'));
    }

    public function testFindAclsThrowsExceptionUnlessAnACLIsFoundForEveryOID()
    {
        $oids = array();
        $oids[] = new ObjectIdentity('1', 'foo');
        $oids[] = new ObjectIdentity('foo', 'foo');

        try {
            $this->getProvider()->findAcls($oids);

            $this->fail('Provider did not throw an expected exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('Symfony\Component\Security\Acl\Exception\AclNotFoundException', $ex);
            $this->assertInstanceOf('Symfony\Component\Security\Acl\Exception\NotAllAclsFoundException', $ex);

            $partialResult = $ex->getPartialResult();
            $this->assertTrue($partialResult->contains($oids[0]));
            $this->assertFalse($partialResult->contains($oids[1]));
        }
    }

    public function testFindAcls()
    {
        $oids = array();
        $oids[] = new ObjectIdentity('1', 'foo');
        $oids[] = new ObjectIdentity('2', 'foo');

        $provider = $this->getProvider();

        $acls = $provider->findAcls($oids);
        $this->assertInstanceOf('SplObjectStorage', $acls);
        $this->assertCount(2, $acls);
        $this->assertInstanceOf('Symfony\Component\Security\Acl\Domain\Acl', $acl0 = $acls->offsetGet($oids[0]));
        $this->assertInstanceOf('Symfony\Component\Security\Acl\Domain\Acl', $acl1 = $acls->offsetGet($oids[1]));
        $this->assertTrue($oids[0]->equals($acl0->getObjectIdentity()));
        $this->assertTrue($oids[1]->equals($acl1->getObjectIdentity()));
    }

    public function testFindAclsWithDifferentTypes()
    {
        $oids = array();
        $oids[] = new ObjectIdentity('123', 'Bundle\SomeVendor\MyBundle\Entity\SomeEntity');
        $oids[] = new ObjectIdentity('123', 'Bundle\MyBundle\Entity\AnotherEntity');

        $provider = $this->getProvider();

        $acls = $provider->findAcls($oids);
        $this->assertInstanceOf('SplObjectStorage', $acls);
        $this->assertCount(2, $acls);
        $this->assertInstanceOf('Symfony\Component\Security\Acl\Domain\Acl', $acl0 = $acls->offsetGet($oids[0]));
        $this->assertInstanceOf('Symfony\Component\Security\Acl\Domain\Acl', $acl1 = $acls->offsetGet($oids[1]));
        $this->assertTrue($oids[0]->equals($acl0->getObjectIdentity()));
        $this->assertTrue($oids[1]->equals($acl1->getObjectIdentity()));
    }

    public function testFindAclCachesAclInMemory()
    {
        $oid = new ObjectIdentity('1', 'foo');
        $provider = $this->getProvider();

        $acl = $provider->findAcl($oid);
        $this->assertSame($acl, $cAcl = $provider->findAcl($oid));

        $cAces = $cAcl->getObjectAces();
        foreach ($acl->getObjectAces() as $index => $ace) {
            $this->assertSame($ace, $cAces[$index]);
        }
    }

    public function testFindAcl()
    {
        $oid = new ObjectIdentity('1', 'foo');
        $provider = $this->getProvider();

        $acl = $provider->findAcl($oid);

        $this->assertInstanceOf('Symfony\Component\Security\Acl\Domain\Acl', $acl);
        $this->assertTrue($oid->equals($acl->getObjectIdentity()));
        $this->assertEquals(4, $acl->getId());
        $this->assertCount(0, $acl->getClassAces());
        $this->assertCount(0, $this->getField($acl, 'classFieldAces'));
        $this->assertCount(3, $acl->getObjectAces());
        $this->assertCount(0, $this->getField($acl, 'objectFieldAces'));

        $aces = $acl->getObjectAces();
        $this->assertInstanceOf('Symfony\Component\Security\Acl\Domain\Entry', $aces[0]);
        $this->assertTrue($aces[0]->isGranting());
        $this->assertTrue($aces[0]->isAuditSuccess());
        $this->assertTrue($aces[0]->isAuditFailure());
        $this->assertEquals('all', $aces[0]->getStrategy());
        $this->assertSame(2, $aces[0]->getMask());

        // check ACE are in correct order
        $i = 0;
        foreach ($aces as $index => $ace) {
            $this->assertEquals($i, $index);
            $i++;
        }

        $sid = $aces[0]->getSecurityIdentity();
        $this->assertInstanceOf('Symfony\Component\Security\Acl\Domain\UserSecurityIdentity', $sid);
        $this->assertEquals('john.doe', $sid->getUsername());
        $this->assertEquals('SomeClass', $sid->getClass());
    }

    protected function setUp()
    {
        if (!class_exists('PDO') || !in_array('sqlite', \PDO::getAvailableDrivers())) {
            self::markTestSkipped('This test requires SQLite support in your environment');
        }

        $this->con = DriverManager::getConnection(array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ));

        // import the schema
        $schema = new Schema($options = $this->getOptions());
        foreach ($schema->toSql($this->con->getDatabasePlatform()) as $sql) {
            $this->con->exec($sql);
        }

        // populate the schema with some test data
        $this->insertClassStmt = $this->con->prepare('INSERT INTO acl_classes (id, class_type) VALUES (?, ?)');
        foreach ($this->getClassData() as $data) {
            $this->insertClassStmt->execute($data);
        }

        $this->insertSidStmt = $this->con->prepare('INSERT INTO acl_security_identities (id, identifier, username) VALUES (?, ?, ?)');
        foreach ($this->getSidData() as $data) {
            $this->insertSidStmt->execute($data);
        }

        $this->insertOidStmt = $this->con->prepare('INSERT INTO acl_object_identities (id, class_id, object_identifier, parent_object_identity_id, entries_inheriting) VALUES (?, ?, ?, ?, ?)');
        foreach ($this->getOidData() as $data) {
            $this->insertOidStmt->execute($data);
        }

        $this->insertEntryStmt = $this->con->prepare('INSERT INTO acl_entries (id, class_id, object_identity_id, field_name, ace_order, security_identity_id, mask, granting, granting_strategy, audit_success, audit_failure) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($this->getEntryData() as $data) {
            $this->insertEntryStmt->execute($data);
        }

        $this->insertOidAncestorStmt = $this->con->prepare('INSERT INTO acl_object_identity_ancestors (object_identity_id, ancestor_id) VALUES (?, ?)');
        foreach ($this->getOidAncestorData() as $data) {
            $this->insertOidAncestorStmt->execute($data);
        }
    }

    protected function tearDown()
    {
        $this->con = null;
    }

    protected function getField($object, $field)
    {
        $reflection = new \ReflectionProperty($object, $field);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function getEntryData()
    {
        // id, cid, oid, field, order, sid, mask, granting, strategy, a success, a failure
        return array(
            array(1, 1, 1, null, 0, 1, 1, 1, 'all', 1, 1),
            array(2, 1, 1, null, 1, 2, 1 << 2 | 1 << 1, 0, 'any', 0, 0),
            array(3, 3, 4, null, 0, 1, 2, 1, 'all', 1, 1),
            array(4, 3, 4, null, 2, 2, 1, 1, 'all', 1, 1),
            array(5, 3, 4, null, 1, 3, 1, 1, 'all', 1, 1),
        );
    }

    protected function getOidData()
    {
        // id, cid, oid, parent_oid, entries_inheriting
        return array(
            array(1, 1, '123', null, 1),
            array(2, 2, '123', 1, 1),
            array(3, 2, 'i:3:123', 1, 1),
            array(4, 3, '1', 2, 1),
            array(5, 3, '2', 2, 1),
        );
    }

    protected function getOidAncestorData()
    {
        return array(
            array(1, 1),
            array(2, 1),
            array(2, 2),
            array(3, 1),
            array(3, 3),
            array(4, 2),
            array(4, 1),
            array(4, 4),
            array(5, 2),
            array(5, 1),
            array(5, 5),
        );
    }

    protected function getSidData()
    {
        return array(
            array(1, 'SomeClass-john.doe', 1),
            array(2, 'MyClass-john.doe@foo.com', 1),
            array(3, 'FooClass-123', 1),
            array(4, 'MooClass-ROLE_USER', 1),
            array(5, 'ROLE_USER', 0),
            array(6, 'IS_AUTHENTICATED_FULLY', 0),
        );
    }

    protected function getClassData()
    {
        return array(
            array(1, 'Bundle\SomeVendor\MyBundle\Entity\SomeEntity'),
            array(2, 'Bundle\MyBundle\Entity\AnotherEntity'),
            array(3, 'foo'),
        );
    }

    protected function getOptions()
    {
        return array(
            'oid_table_name' => 'acl_object_identities',
            'oid_ancestors_table_name' => 'acl_object_identity_ancestors',
            'class_table_name' => 'acl_classes',
            'sid_table_name' => 'acl_security_identities',
            'entry_table_name' => 'acl_entries',
        );
    }

    protected function getStrategy()
    {
        return new PermissionGrantingStrategy();
    }

    protected function getProvider()
    {
        return new AclProvider($this->con, $this->getStrategy(), $this->getOptions());
    }
}
