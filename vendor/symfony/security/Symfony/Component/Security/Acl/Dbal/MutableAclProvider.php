<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Dbal;

use Doctrine\Common\PropertyChangedListener;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Exception\ConcurrentModificationException;
use Symfony\Component\Security\Acl\Model\AclCacheInterface;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\EntryInterface;
use Symfony\Component\Security\Acl\Model\MutableAclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;
use Symfony\Component\Security\Acl\Model\PermissionGrantingStrategyInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;

/**
 * An implementation of the MutableAclProviderInterface using Doctrine DBAL.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class MutableAclProvider extends AclProvider implements MutableAclProviderInterface, PropertyChangedListener
{
    private $propertyChanges;

    /**
     * {@inheritdoc}
     */
    public function __construct(Connection $connection, PermissionGrantingStrategyInterface $permissionGrantingStrategy, array $options, AclCacheInterface $cache = null)
    {
        parent::__construct($connection, $permissionGrantingStrategy, $options, $cache);

        $this->propertyChanges = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function createAcl(ObjectIdentityInterface $oid)
    {
        if (false !== $this->retrieveObjectIdentityPrimaryKey($oid)) {
            throw new AclAlreadyExistsException(sprintf('%s is already associated with an ACL.', $oid));
        }

        $this->connection->beginTransaction();
        try {
            $this->createObjectIdentity($oid);

            $pk = $this->retrieveObjectIdentityPrimaryKey($oid);
            $this->connection->executeQuery($this->getInsertObjectIdentityRelationSql($pk, $pk));

            $this->connection->commit();
        } catch (\Exception $failed) {
            $this->connection->rollBack();

            throw $failed;
        }

        // re-read the ACL from the database to ensure proper caching, etc.
        return $this->findAcl($oid);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAcl(ObjectIdentityInterface $oid)
    {
        $this->connection->beginTransaction();
        try {
            foreach ($this->findChildren($oid, true) as $childOid) {
                $this->deleteAcl($childOid);
            }

            $oidPK = $this->retrieveObjectIdentityPrimaryKey($oid);

            $this->deleteAccessControlEntries($oidPK);
            $this->deleteObjectIdentityRelations($oidPK);
            $this->deleteObjectIdentity($oidPK);

            $this->connection->commit();
        } catch (\Exception $failed) {
            $this->connection->rollBack();

            throw $failed;
        }

        // evict the ACL from the in-memory identity map
        if (isset($this->loadedAcls[$oid->getType()][$oid->getIdentifier()])) {
            $this->propertyChanges->offsetUnset($this->loadedAcls[$oid->getType()][$oid->getIdentifier()]);
            unset($this->loadedAcls[$oid->getType()][$oid->getIdentifier()]);
        }

        // evict the ACL from any caches
        if (null !== $this->cache) {
            $this->cache->evictFromCacheByIdentity($oid);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findAcls(array $oids, array $sids = array())
    {
        $result = parent::findAcls($oids, $sids);

        foreach ($result as $oid) {
            $acl = $result->offsetGet($oid);

            if (false === $this->propertyChanges->contains($acl) && $acl instanceof MutableAclInterface) {
                $acl->addPropertyChangedListener($this);
                $this->propertyChanges->attach($acl, array());
            }

            $parentAcl = $acl->getParentAcl();
            while (null !== $parentAcl) {
                if (false === $this->propertyChanges->contains($parentAcl) && $acl instanceof MutableAclInterface) {
                    $parentAcl->addPropertyChangedListener($this);
                    $this->propertyChanges->attach($parentAcl, array());
                }

                $parentAcl = $parentAcl->getParentAcl();
            }
        }

        return $result;
    }

    /**
     * Implementation of PropertyChangedListener
     *
     * This allows us to keep track of which values have been changed, so we don't
     * have to do a full introspection when ->updateAcl() is called.
     *
     * @param mixed  $sender
     * @param string $propertyName
     * @param mixed  $oldValue
     * @param mixed  $newValue
     *
     * @throws \InvalidArgumentException
     */
    public function propertyChanged($sender, $propertyName, $oldValue, $newValue)
    {
        if (!$sender instanceof MutableAclInterface && !$sender instanceof EntryInterface) {
            throw new \InvalidArgumentException('$sender must be an instance of MutableAclInterface, or EntryInterface.');
        }

        if ($sender instanceof EntryInterface) {
            if (null === $sender->getId()) {
                return;
            }

            $ace = $sender;
            $sender = $ace->getAcl();
        } else {
            $ace = null;
        }

        if (false === $this->propertyChanges->contains($sender)) {
            throw new \InvalidArgumentException('$sender is not being tracked by this provider.');
        }

        $propertyChanges = $this->propertyChanges->offsetGet($sender);
        if (null === $ace) {
            if (isset($propertyChanges[$propertyName])) {
                $oldValue = $propertyChanges[$propertyName][0];
                if ($oldValue === $newValue) {
                    unset($propertyChanges[$propertyName]);
                } else {
                    $propertyChanges[$propertyName] = array($oldValue, $newValue);
                }
            } else {
                $propertyChanges[$propertyName] = array($oldValue, $newValue);
            }
        } else {
            if (!isset($propertyChanges['aces'])) {
                $propertyChanges['aces'] = new \SplObjectStorage();
            }

            $acePropertyChanges = $propertyChanges['aces']->contains($ace)? $propertyChanges['aces']->offsetGet($ace) : array();

            if (isset($acePropertyChanges[$propertyName])) {
                $oldValue = $acePropertyChanges[$propertyName][0];
                if ($oldValue === $newValue) {
                    unset($acePropertyChanges[$propertyName]);
                } else {
                    $acePropertyChanges[$propertyName] = array($oldValue, $newValue);
                }
            } else {
                $acePropertyChanges[$propertyName] = array($oldValue, $newValue);
            }

            if (count($acePropertyChanges) > 0) {
                $propertyChanges['aces']->offsetSet($ace, $acePropertyChanges);
            } else {
                $propertyChanges['aces']->offsetUnset($ace);

                if (0 === count($propertyChanges['aces'])) {
                    unset($propertyChanges['aces']);
                }
            }
        }

        $this->propertyChanges->offsetSet($sender, $propertyChanges);
    }

    /**
     * {@inheritdoc}
     */
    public function updateAcl(MutableAclInterface $acl)
    {
        if (!$this->propertyChanges->contains($acl)) {
            throw new \InvalidArgumentException('$acl is not tracked by this provider.');
        }

        $propertyChanges = $this->propertyChanges->offsetGet($acl);
        // check if any changes were made to this ACL
        if (0 === count($propertyChanges)) {
            return;
        }

        $sets = $sharedPropertyChanges = array();

        $this->connection->beginTransaction();
        try {
            if (isset($propertyChanges['entriesInheriting'])) {
                $sets[] = 'entries_inheriting = '.$this->connection->getDatabasePlatform()->convertBooleans($propertyChanges['entriesInheriting'][1]);
            }

            if (isset($propertyChanges['parentAcl'])) {
                if (null === $propertyChanges['parentAcl'][1]) {
                    $sets[] = 'parent_object_identity_id = NULL';
                } else {
                    $sets[] = 'parent_object_identity_id = '.intval($propertyChanges['parentAcl'][1]->getId());
                }

                $this->regenerateAncestorRelations($acl);
                $childAcls = $this->findAcls($this->findChildren($acl->getObjectIdentity(), false));
                foreach ($childAcls as $childOid) {
                    $this->regenerateAncestorRelations($childAcls[$childOid]);
                }
            }

            // check properties for deleted, and created ACEs, and perform deletions
            // we need to perfom deletions before updating existing ACEs, in order to
            // preserve uniqueness of the order field
            if (isset($propertyChanges['classAces'])) {
                $this->updateOldAceProperty('classAces', $propertyChanges['classAces']);
            }
            if (isset($propertyChanges['classFieldAces'])) {
                $this->updateOldFieldAceProperty('classFieldAces', $propertyChanges['classFieldAces']);
            }
            if (isset($propertyChanges['objectAces'])) {
                $this->updateOldAceProperty('objectAces', $propertyChanges['objectAces']);
            }
            if (isset($propertyChanges['objectFieldAces'])) {
                $this->updateOldFieldAceProperty('objectFieldAces', $propertyChanges['objectFieldAces']);
            }

            // this includes only updates of existing ACEs, but neither the creation, nor
            // the deletion of ACEs; these are tracked by changes to the ACL's respective
            // properties (classAces, classFieldAces, objectAces, objectFieldAces)
            if (isset($propertyChanges['aces'])) {
                $this->updateAces($propertyChanges['aces']);
            }

            // check properties for deleted, and created ACEs, and perform creations
            if (isset($propertyChanges['classAces'])) {
                $this->updateNewAceProperty('classAces', $propertyChanges['classAces']);
                $sharedPropertyChanges['classAces'] = $propertyChanges['classAces'];
            }
            if (isset($propertyChanges['classFieldAces'])) {
                $this->updateNewFieldAceProperty('classFieldAces', $propertyChanges['classFieldAces']);
                $sharedPropertyChanges['classFieldAces'] = $propertyChanges['classFieldAces'];
            }
            if (isset($propertyChanges['objectAces'])) {
                $this->updateNewAceProperty('objectAces', $propertyChanges['objectAces']);
            }
            if (isset($propertyChanges['objectFieldAces'])) {
                $this->updateNewFieldAceProperty('objectFieldAces', $propertyChanges['objectFieldAces']);
            }

            // if there have been changes to shared properties, we need to synchronize other
            // ACL instances for object identities of the same type that are already in-memory
            if (count($sharedPropertyChanges) > 0) {
                $classAcesProperty = new \ReflectionProperty('Symfony\Component\Security\Acl\Domain\Acl', 'classAces');
                $classAcesProperty->setAccessible(true);
                $classFieldAcesProperty = new \ReflectionProperty('Symfony\Component\Security\Acl\Domain\Acl', 'classFieldAces');
                $classFieldAcesProperty->setAccessible(true);

                foreach ($this->loadedAcls[$acl->getObjectIdentity()->getType()] as $sameTypeAcl) {
                    if (isset($sharedPropertyChanges['classAces'])) {
                        if ($acl !== $sameTypeAcl && $classAcesProperty->getValue($sameTypeAcl) !== $sharedPropertyChanges['classAces'][0]) {
                            throw new ConcurrentModificationException('The "classAces" property has been modified concurrently.');
                        }

                        $classAcesProperty->setValue($sameTypeAcl, $sharedPropertyChanges['classAces'][1]);
                    }

                    if (isset($sharedPropertyChanges['classFieldAces'])) {
                        if ($acl !== $sameTypeAcl && $classFieldAcesProperty->getValue($sameTypeAcl) !== $sharedPropertyChanges['classFieldAces'][0]) {
                            throw new ConcurrentModificationException('The "classFieldAces" property has been modified concurrently.');
                        }

                        $classFieldAcesProperty->setValue($sameTypeAcl, $sharedPropertyChanges['classFieldAces'][1]);
                    }
                }
            }

            // persist any changes to the acl_object_identities table
            if (count($sets) > 0) {
                $this->connection->executeQuery($this->getUpdateObjectIdentitySql($acl->getId(), $sets));
            }

            $this->connection->commit();
        } catch (\Exception $failed) {
            $this->connection->rollBack();

            throw $failed;
        }

        $this->propertyChanges->offsetSet($acl, array());

        if (null !== $this->cache) {
            if (count($sharedPropertyChanges) > 0) {
                // FIXME: Currently, there is no easy way to clear the cache for ACLs
                //        of a certain type. The problem here is that we need to make
                //        sure to clear the cache of all child ACLs as well, and these
                //        child ACLs might be of a different class type.
                $this->cache->clearCache();
            } else {
                // if there are no shared property changes, it's sufficient to just delete
                // the cache for this ACL
                $this->cache->evictFromCacheByIdentity($acl->getObjectIdentity());

                foreach ($this->findChildren($acl->getObjectIdentity()) as $childOid) {
                    $this->cache->evictFromCacheByIdentity($childOid);
                }
            }
        }
    }

    /**
     * Constructs the SQL for deleting access control entries.
     *
     * @param int     $oidPK
     * @return string
     */
    protected function getDeleteAccessControlEntriesSql($oidPK)
    {
        return sprintf(
              'DELETE FROM %s WHERE object_identity_id = %d',
            $this->options['entry_table_name'],
            $oidPK
        );
    }

    /**
     * Constructs the SQL for deleting a specific ACE.
     *
     * @param int     $acePK
     * @return string
     */
    protected function getDeleteAccessControlEntrySql($acePK)
    {
        return sprintf(
            'DELETE FROM %s WHERE id = %d',
            $this->options['entry_table_name'],
            $acePK
        );
    }

    /**
     * Constructs the SQL for deleting an object identity.
     *
     * @param int     $pk
     * @return string
     */
    protected function getDeleteObjectIdentitySql($pk)
    {
        return sprintf(
            'DELETE FROM %s WHERE id = %d',
            $this->options['oid_table_name'],
            $pk
        );
    }

    /**
     * Constructs the SQL for deleting relation entries.
     *
     * @param int     $pk
     * @return string
     */
    protected function getDeleteObjectIdentityRelationsSql($pk)
    {
        return sprintf(
            'DELETE FROM %s WHERE object_identity_id = %d',
            $this->options['oid_ancestors_table_name'],
            $pk
        );
    }

    /**
     * Constructs the SQL for inserting an ACE.
     *
     * @param int          $classId
     * @param int|null     $objectIdentityId
     * @param string|null  $field
     * @param int          $aceOrder
     * @param int          $securityIdentityId
     * @param string       $strategy
     * @param int          $mask
     * @param bool         $granting
     * @param bool         $auditSuccess
     * @param bool         $auditFailure
     * @return string
     */
    protected function getInsertAccessControlEntrySql($classId, $objectIdentityId, $field, $aceOrder, $securityIdentityId, $strategy, $mask, $granting, $auditSuccess, $auditFailure)
    {
        $query = <<<QUERY
            INSERT INTO %s (
                class_id,
                object_identity_id,
                field_name,
                ace_order,
                security_identity_id,
                mask,
                granting,
                granting_strategy,
                audit_success,
                audit_failure
            )
            VALUES (%d, %s, %s, %d, %d, %d, %s, %s, %s, %s)
QUERY;

        return sprintf(
            $query,
            $this->options['entry_table_name'],
            $classId,
            null === $objectIdentityId? 'NULL' : intval($objectIdentityId),
            null === $field? 'NULL' : $this->connection->quote($field),
            $aceOrder,
            $securityIdentityId,
            $mask,
            $this->connection->getDatabasePlatform()->convertBooleans($granting),
            $this->connection->quote($strategy),
            $this->connection->getDatabasePlatform()->convertBooleans($auditSuccess),
            $this->connection->getDatabasePlatform()->convertBooleans($auditFailure)
        );
    }

    /**
     * Constructs the SQL for inserting a new class type.
     *
     * @param string $classType
     * @return string
     */
    protected function getInsertClassSql($classType)
    {
        return sprintf(
            'INSERT INTO %s (class_type) VALUES (%s)',
            $this->options['class_table_name'],
            $this->connection->quote($classType)
        );
    }

    /**
     * Constructs the SQL for inserting a relation entry.
     *
     * @param int     $objectIdentityId
     * @param int     $ancestorId
     * @return string
     */
    protected function getInsertObjectIdentityRelationSql($objectIdentityId, $ancestorId)
    {
        return sprintf(
            'INSERT INTO %s (object_identity_id, ancestor_id) VALUES (%d, %d)',
            $this->options['oid_ancestors_table_name'],
            $objectIdentityId,
            $ancestorId
        );
    }

    /**
     * Constructs the SQL for inserting an object identity.
     *
     * @param string  $identifier
     * @param int     $classId
     * @param bool    $entriesInheriting
     * @return string
     */
    protected function getInsertObjectIdentitySql($identifier, $classId, $entriesInheriting)
    {
        $query = <<<QUERY
              INSERT INTO %s (class_id, object_identifier, entries_inheriting)
              VALUES (%d, %s, %s)
QUERY;

        return sprintf(
            $query,
            $this->options['oid_table_name'],
            $classId,
            $this->connection->quote($identifier),
            $this->connection->getDatabasePlatform()->convertBooleans($entriesInheriting)
        );
    }

    /**
     * Constructs the SQL for inserting a security identity.
     *
     * @param SecurityIdentityInterface $sid
     * @throws \InvalidArgumentException
     * @return string
     */
    protected function getInsertSecurityIdentitySql(SecurityIdentityInterface $sid)
    {
        if ($sid instanceof UserSecurityIdentity) {
            $identifier = $sid->getClass().'-'.$sid->getUsername();
            $username = true;
        } elseif ($sid instanceof RoleSecurityIdentity) {
            $identifier = $sid->getRole();
            $username = false;
        } else {
            throw new \InvalidArgumentException('$sid must either be an instance of UserSecurityIdentity, or RoleSecurityIdentity.');
        }

        return sprintf(
            'INSERT INTO %s (identifier, username) VALUES (%s, %s)',
            $this->options['sid_table_name'],
            $this->connection->quote($identifier),
            $this->connection->getDatabasePlatform()->convertBooleans($username)
        );
    }

    /**
     * Constructs the SQL for selecting an ACE.
     *
     * @param int     $classId
     * @param int     $oid
     * @param string  $field
     * @param int     $order
     * @return string
     */
    protected function getSelectAccessControlEntryIdSql($classId, $oid, $field, $order)
    {
        return sprintf(
            'SELECT id FROM %s WHERE class_id = %d AND %s AND %s AND ace_order = %d',
            $this->options['entry_table_name'],
            $classId,
            null === $oid ?
                $this->connection->getDatabasePlatform()->getIsNullExpression('object_identity_id')
                : 'object_identity_id = '.intval($oid),
            null === $field ?
                $this->connection->getDatabasePlatform()->getIsNullExpression('field_name')
                : 'field_name = '.$this->connection->quote($field),
            $order
        );
    }

    /**
     * Constructs the SQL for selecting the primary key associated with
     * the passed class type.
     *
     * @param string $classType
     * @return string
     */
    protected function getSelectClassIdSql($classType)
    {
        return sprintf(
            'SELECT id FROM %s WHERE class_type = %s',
            $this->options['class_table_name'],
            $this->connection->quote($classType)
        );
    }

    /**
     * Constructs the SQL for selecting the primary key of a security identity.
     *
     * @param SecurityIdentityInterface $sid
     * @throws \InvalidArgumentException
     * @return string
     */
    protected function getSelectSecurityIdentityIdSql(SecurityIdentityInterface $sid)
    {
        if ($sid instanceof UserSecurityIdentity) {
            $identifier = $sid->getClass().'-'.$sid->getUsername();
            $username = true;
        } elseif ($sid instanceof RoleSecurityIdentity) {
            $identifier = $sid->getRole();
            $username = false;
        } else {
            throw new \InvalidArgumentException('$sid must either be an instance of UserSecurityIdentity, or RoleSecurityIdentity.');
        }

        return sprintf(
            'SELECT id FROM %s WHERE identifier = %s AND username = %s',
            $this->options['sid_table_name'],
            $this->connection->quote($identifier),
            $this->connection->getDatabasePlatform()->convertBooleans($username)
        );
    }

    /**
     * Constructs the SQL for updating an object identity.
     *
     * @param int     $pk
     * @param array   $changes
     * @throws \InvalidArgumentException
     * @return string
     */
    protected function getUpdateObjectIdentitySql($pk, array $changes)
    {
        if (0 === count($changes)) {
            throw new \InvalidArgumentException('There are no changes.');
        }

        return sprintf(
            'UPDATE %s SET %s WHERE id = %d',
            $this->options['oid_table_name'],
            implode(', ', $changes),
            $pk
        );
    }

    /**
     * Constructs the SQL for updating an ACE.
     *
     * @param int     $pk
     * @param array   $sets
     * @throws \InvalidArgumentException
     * @return string
     */
    protected function getUpdateAccessControlEntrySql($pk, array $sets)
    {
        if (0 === count($sets)) {
            throw new \InvalidArgumentException('There are no changes.');
        }

        return sprintf(
            'UPDATE %s SET %s WHERE id = %d',
            $this->options['entry_table_name'],
            implode(', ', $sets),
            $pk
        );
    }

    /**
     * Creates the ACL for the passed object identity
     *
     * @param ObjectIdentityInterface $oid
     */
    private function createObjectIdentity(ObjectIdentityInterface $oid)
    {
        $classId = $this->createOrRetrieveClassId($oid->getType());

        $this->connection->executeQuery($this->getInsertObjectIdentitySql($oid->getIdentifier(), $classId, true));
    }

    /**
     * Returns the primary key for the passed class type.
     *
     * If the type does not yet exist in the database, it will be created.
     *
     * @param string $classType
     * @return int
     */
    private function createOrRetrieveClassId($classType)
    {
        if (false !== $id = $this->connection->executeQuery($this->getSelectClassIdSql($classType))->fetchColumn()) {
            return $id;
        }

        $this->connection->executeQuery($this->getInsertClassSql($classType));

        return $this->connection->executeQuery($this->getSelectClassIdSql($classType))->fetchColumn();
    }

    /**
     * Returns the primary key for the passed security identity.
     *
     * If the security identity does not yet exist in the database, it will be
     * created.
     *
     * @param SecurityIdentityInterface $sid
     * @return int
     */
    private function createOrRetrieveSecurityIdentityId(SecurityIdentityInterface $sid)
    {
        if (false !== $id = $this->connection->executeQuery($this->getSelectSecurityIdentityIdSql($sid))->fetchColumn()) {
            return $id;
        }

        $this->connection->executeQuery($this->getInsertSecurityIdentitySql($sid));

        return $this->connection->executeQuery($this->getSelectSecurityIdentityIdSql($sid))->fetchColumn();
    }

    /**
     * Deletes all ACEs for the given object identity primary key.
     *
     * @param int     $oidPK
     */
    private function deleteAccessControlEntries($oidPK)
    {
        $this->connection->executeQuery($this->getDeleteAccessControlEntriesSql($oidPK));
    }

    /**
     * Deletes the object identity from the database.
     *
     * @param int     $pk
     */
    private function deleteObjectIdentity($pk)
    {
        $this->connection->executeQuery($this->getDeleteObjectIdentitySql($pk));
    }

    /**
     * Deletes all entries from the relations table from the database.
     *
     * @param int     $pk
     */
    private function deleteObjectIdentityRelations($pk)
    {
        $this->connection->executeQuery($this->getDeleteObjectIdentityRelationsSql($pk));
    }

    /**
     * This regenerates the ancestor table which is used for fast read access.
     *
     * @param AclInterface $acl
     */
    private function regenerateAncestorRelations(AclInterface $acl)
    {
        $pk = $acl->getId();
        $this->connection->executeQuery($this->getDeleteObjectIdentityRelationsSql($pk));
        $this->connection->executeQuery($this->getInsertObjectIdentityRelationSql($pk, $pk));

        $parentAcl = $acl->getParentAcl();
        while (null !== $parentAcl) {
            $this->connection->executeQuery($this->getInsertObjectIdentityRelationSql($pk, $parentAcl->getId()));

            $parentAcl = $parentAcl->getParentAcl();
        }
    }

    /**
     * This processes new entries changes on an ACE related property (classFieldAces, or objectFieldAces).
     *
     * @param string $name
     * @param array  $changes
     */
    private function updateNewFieldAceProperty($name, array $changes)
    {
        $sids = new \SplObjectStorage();
        $classIds = new \SplObjectStorage();
        $currentIds = array();
        foreach ($changes[1] as $field => $new) {
            for ($i=0,$c=count($new); $i<$c; $i++) {
                $ace = $new[$i];

                if (null === $ace->getId()) {
                    if ($sids->contains($ace->getSecurityIdentity())) {
                        $sid = $sids->offsetGet($ace->getSecurityIdentity());
                    } else {
                        $sid = $this->createOrRetrieveSecurityIdentityId($ace->getSecurityIdentity());
                    }

                    $oid = $ace->getAcl()->getObjectIdentity();
                    if ($classIds->contains($oid)) {
                        $classId = $classIds->offsetGet($oid);
                    } else {
                        $classId = $this->createOrRetrieveClassId($oid->getType());
                    }

                    $objectIdentityId = $name === 'classFieldAces' ? null : $ace->getAcl()->getId();

                    $this->connection->executeQuery($this->getInsertAccessControlEntrySql($classId, $objectIdentityId, $field, $i, $sid, $ace->getStrategy(), $ace->getMask(), $ace->isGranting(), $ace->isAuditSuccess(), $ace->isAuditFailure()));
                    $aceId = $this->connection->executeQuery($this->getSelectAccessControlEntryIdSql($classId, $objectIdentityId, $field, $i))->fetchColumn();
                    $this->loadedAces[$aceId] = $ace;

                    $aceIdProperty = new \ReflectionProperty('Symfony\Component\Security\Acl\Domain\Entry', 'id');
                    $aceIdProperty->setAccessible(true);
                    $aceIdProperty->setValue($ace, intval($aceId));
                } else {
                    $currentIds[$ace->getId()] = true;
                }
            }
        }
    }

    /**
     * This process old entries changes on an ACE related property (classFieldAces, or objectFieldAces).
     *
     * @param string $name
     * @param array $changes
     */
    private function updateOldFieldAceProperty($ane, array $changes)
    {
        $currentIds = array();
        foreach ($changes[1] as $field => $new) {
            for ($i = 0, $c = count($new); $i < $c; $i++) {
                $ace = $new[$i];

                if (null !== $ace->getId()) {
                    $currentIds[$ace->getId()] = true;
                }
            }
        }

        foreach ($changes[0] as $old) {
            for ($i = 0, $c = count($old); $i < $c; $i++) {
                $ace = $old[$i];

                if (!isset($currentIds[$ace->getId()])) {
                    $this->connection->executeQuery($this->getDeleteAccessControlEntrySql($ace->getId()));
                    unset($this->loadedAces[$ace->getId()]);
                }
            }
        }
    }

    /**
     * This processes new entries changes on an ACE related property (classAces, or objectAces).
     *
     * @param string $name
     * @param array  $changes
     */
    private function updateNewAceProperty($name, array $changes)
    {
        list($old, $new) = $changes;

        $sids = new \SplObjectStorage();
        $classIds = new \SplObjectStorage();
        $currentIds = array();
        for ($i=0,$c=count($new); $i<$c; $i++) {
            $ace = $new[$i];

            if (null === $ace->getId()) {
                if ($sids->contains($ace->getSecurityIdentity())) {
                    $sid = $sids->offsetGet($ace->getSecurityIdentity());
                } else {
                    $sid = $this->createOrRetrieveSecurityIdentityId($ace->getSecurityIdentity());
                }

                $oid = $ace->getAcl()->getObjectIdentity();
                if ($classIds->contains($oid)) {
                    $classId = $classIds->offsetGet($oid);
                } else {
                    $classId = $this->createOrRetrieveClassId($oid->getType());
                }

                $objectIdentityId = $name === 'classAces' ? null : $ace->getAcl()->getId();

                $this->connection->executeQuery($this->getInsertAccessControlEntrySql($classId, $objectIdentityId, null, $i, $sid, $ace->getStrategy(), $ace->getMask(), $ace->isGranting(), $ace->isAuditSuccess(), $ace->isAuditFailure()));
                $aceId = $this->connection->executeQuery($this->getSelectAccessControlEntryIdSql($classId, $objectIdentityId, null, $i))->fetchColumn();
                $this->loadedAces[$aceId] = $ace;

                $aceIdProperty = new \ReflectionProperty($ace, 'id');
                $aceIdProperty->setAccessible(true);
                $aceIdProperty->setValue($ace, intval($aceId));
            } else {
                $currentIds[$ace->getId()] = true;
            }
        }
    }

    /**
     * This processes old entries changes on an ACE related property (classAces, or objectAces).
     *
     * @param string $name
     * @param array  $changes
     */
    private function updateOldAceProperty($name, array $changes)
    {
        list($old, $new) = $changes;
        $currentIds = array();

        for ($i=0,$c=count($new); $i<$c; $i++) {
            $ace = $new[$i];

            if (null !== $ace->getId()) {
                $currentIds[$ace->getId()] = true;
            }
        }

        for ($i = 0, $c = count($old); $i < $c; $i++) {
            $ace = $old[$i];

            if (!isset($currentIds[$ace->getId()])) {
                $this->connection->executeQuery($this->getDeleteAccessControlEntrySql($ace->getId()));
                unset($this->loadedAces[$ace->getId()]);
            }
        }
    }

    /**
     * Persists the changes which were made to ACEs to the database.
     *
     * @param \SplObjectStorage $aces
     */
    private function updateAces(\SplObjectStorage $aces)
    {
        foreach ($aces as $ace) {
            $this->updateAce($aces, $ace);
        }
    }

    private function updateAce(\SplObjectStorage $aces, $ace)
    {
        $propertyChanges = $aces->offsetGet($ace);
        $sets = array();

        if (isset($propertyChanges['aceOrder'])
            && $propertyChanges['aceOrder'][1] > $propertyChanges['aceOrder'][0]
            && $propertyChanges == $aces->offsetGet($ace)) {
                $aces->next();
                if ($aces->valid()) {
                    $this->updateAce($aces, $aces->current());
                }
            }

        if (isset($propertyChanges['mask'])) {
            $sets[] = sprintf('mask = %d', $propertyChanges['mask'][1]);
        }
        if (isset($propertyChanges['strategy'])) {
            $sets[] = sprintf('granting_strategy = %s', $this->connection->quote($propertyChanges['strategy']));
        }
        if (isset($propertyChanges['aceOrder'])) {
            $sets[] = sprintf('ace_order = %d', $propertyChanges['aceOrder'][1]);
        }
        if (isset($propertyChanges['auditSuccess'])) {
            $sets[] = sprintf('audit_success = %s', $this->connection->getDatabasePlatform()->convertBooleans($propertyChanges['auditSuccess'][1]));
        }
        if (isset($propertyChanges['auditFailure'])) {
            $sets[] = sprintf('audit_failure = %s', $this->connection->getDatabasePlatform()->convertBooleans($propertyChanges['auditFailure'][1]));
        }

        $this->connection->executeQuery($this->getUpdateAccessControlEntrySql($ace->getId(), $sets));
    }

}
