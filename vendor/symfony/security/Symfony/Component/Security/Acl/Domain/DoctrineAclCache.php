<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Domain;

use Doctrine\Common\Cache\Cache;
use Symfony\Component\Security\Acl\Model\AclCacheInterface;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;
use Symfony\Component\Security\Acl\Model\PermissionGrantingStrategyInterface;

/**
 * This class is a wrapper around the actual cache implementation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class DoctrineAclCache implements AclCacheInterface
{
    const PREFIX = 'sf2_acl_';

    private $cache;
    private $prefix;
    private $permissionGrantingStrategy;

    /**
     * Constructor
     *
     * @param Cache                               $cache
     * @param PermissionGrantingStrategyInterface $permissionGrantingStrategy
     * @param string                              $prefix
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Cache $cache, PermissionGrantingStrategyInterface $permissionGrantingStrategy, $prefix = self::PREFIX)
    {
        if (0 === strlen($prefix)) {
            throw new \InvalidArgumentException('$prefix cannot be empty.');
        }

        $this->cache = $cache;
        $this->permissionGrantingStrategy = $permissionGrantingStrategy;
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function clearCache()
    {
        $this->cache->deleteByPrefix($this->prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function evictFromCacheById($aclId)
    {
        $lookupKey = $this->getAliasKeyForIdentity($aclId);
        if (!$this->cache->contains($lookupKey)) {
            return;
        }

        $key = $this->cache->fetch($lookupKey);
        if ($this->cache->contains($key)) {
            $this->cache->delete($key);
        }

        $this->cache->delete($lookupKey);
    }

    /**
     * {@inheritdoc}
     */
    public function evictFromCacheByIdentity(ObjectIdentityInterface $oid)
    {
        $key = $this->getDataKeyByIdentity($oid);
        if (!$this->cache->contains($key)) {
            return;
        }

        $this->cache->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getFromCacheById($aclId)
    {
        $lookupKey = $this->getAliasKeyForIdentity($aclId);
        if (!$this->cache->contains($lookupKey)) {
            return;
        }

        $key = $this->cache->fetch($lookupKey);
        if (!$this->cache->contains($key)) {
            $this->cache->delete($lookupKey);

            return;
        }

        return $this->unserializeAcl($this->cache->fetch($key));
    }

    /**
     * {@inheritdoc}
     */
    public function getFromCacheByIdentity(ObjectIdentityInterface $oid)
    {
        $key = $this->getDataKeyByIdentity($oid);
        if (!$this->cache->contains($key)) {
            return;
        }

        return $this->unserializeAcl($this->cache->fetch($key));
    }

    /**
     * {@inheritdoc}
     */
    public function putInCache(AclInterface $acl)
    {
        if (null === $acl->getId()) {
            throw new \InvalidArgumentException('Transient ACLs cannot be cached.');
        }

        if (null !== $parentAcl = $acl->getParentAcl()) {
            $this->putInCache($parentAcl);
        }

        $key = $this->getDataKeyByIdentity($acl->getObjectIdentity());
        $this->cache->save($key, serialize($acl));
        $this->cache->save($this->getAliasKeyForIdentity($acl->getId()), $key);
    }

    /**
     * Unserializes the ACL.
     *
     * @param string $serialized
     * @return AclInterface
     */
    private function unserializeAcl($serialized)
    {
        $acl = unserialize($serialized);

        if (null !== $parentId = $acl->getParentAcl()) {
            $parentAcl = $this->getFromCacheById($parentId);

            if (null === $parentAcl) {
                return;
            }

            $acl->setParentAcl($parentAcl);
        }

        $reflectionProperty = new \ReflectionProperty($acl, 'permissionGrantingStrategy');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($acl, $this->permissionGrantingStrategy);
        $reflectionProperty->setAccessible(false);

        $aceAclProperty = new \ReflectionProperty('Symfony\Component\Security\Acl\Domain\Entry', 'acl');
        $aceAclProperty->setAccessible(true);

        foreach ($acl->getObjectAces() as $ace) {
            $aceAclProperty->setValue($ace, $acl);
        }
        foreach ($acl->getClassAces() as $ace) {
            $aceAclProperty->setValue($ace, $acl);
        }

        $aceClassFieldProperty = new \ReflectionProperty($acl, 'classFieldAces');
        $aceClassFieldProperty->setAccessible(true);
        foreach ($aceClassFieldProperty->getValue($acl) as $aces) {
            foreach ($aces as $ace) {
                $aceAclProperty->setValue($ace, $acl);
            }
        }
        $aceClassFieldProperty->setAccessible(false);

        $aceObjectFieldProperty = new \ReflectionProperty($acl, 'objectFieldAces');
        $aceObjectFieldProperty->setAccessible(true);
        foreach ($aceObjectFieldProperty->getValue($acl) as $aces) {
            foreach ($aces as $ace) {
                $aceAclProperty->setValue($ace, $acl);
            }
        }
        $aceObjectFieldProperty->setAccessible(false);

        $aceAclProperty->setAccessible(false);

        return $acl;
    }

    /**
     * Returns the key for the object identity
     *
     * @param ObjectIdentityInterface $oid
     * @return string
     */
    private function getDataKeyByIdentity(ObjectIdentityInterface $oid)
    {
        return $this->prefix.md5($oid->getType()).sha1($oid->getType())
               .'_'.md5($oid->getIdentifier()).sha1($oid->getIdentifier());
    }

    /**
     * Returns the alias key for the object identity key
     *
     * @param string $aclId
     * @return string
     */
    private function getAliasKeyForIdentity($aclId)
    {
        return $this->prefix.$aclId;
    }
}
