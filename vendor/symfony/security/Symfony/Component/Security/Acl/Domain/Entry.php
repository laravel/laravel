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

use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\AuditableEntryInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;

/**
 * Auditable ACE implementation
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class Entry implements AuditableEntryInterface
{
    private $acl;
    private $mask;
    private $id;
    private $securityIdentity;
    private $strategy;
    private $auditFailure;
    private $auditSuccess;
    private $granting;

    /**
     * Constructor
     *
     * @param int                       $id
     * @param AclInterface              $acl
     * @param SecurityIdentityInterface $sid
     * @param string                    $strategy
     * @param int                       $mask
     * @param bool                      $granting
     * @param bool                      $auditFailure
     * @param bool                      $auditSuccess
     */
    public function __construct($id, AclInterface $acl, SecurityIdentityInterface $sid, $strategy, $mask, $granting, $auditFailure, $auditSuccess)
    {
        $this->id = $id;
        $this->acl = $acl;
        $this->securityIdentity = $sid;
        $this->strategy = $strategy;
        $this->mask = $mask;
        $this->granting = $granting;
        $this->auditFailure = $auditFailure;
        $this->auditSuccess = $auditSuccess;
    }

    /**
     * {@inheritdoc}
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * {@inheritdoc}
     */
    public function getMask()
    {
        return $this->mask;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecurityIdentity()
    {
        return $this->securityIdentity;
    }

    /**
     * {@inheritdoc}
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuditFailure()
    {
        return $this->auditFailure;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuditSuccess()
    {
        return $this->auditSuccess;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranting()
    {
        return $this->granting;
    }

    /**
     * Turns on/off auditing on permissions denials.
     *
     * Do never call this method directly. Use the respective methods on the
     * AclInterface instead.
     *
     * @param bool    $boolean
     */
    public function setAuditFailure($boolean)
    {
        $this->auditFailure = $boolean;
    }

    /**
     * Turns on/off auditing on permission grants.
     *
     * Do never call this method directly. Use the respective methods on the
     * AclInterface instead.
     *
     * @param bool    $boolean
     */
    public function setAuditSuccess($boolean)
    {
        $this->auditSuccess = $boolean;
    }

    /**
     * Sets the permission mask
     *
     * Do never call this method directly. Use the respective methods on the
     * AclInterface instead.
     *
     * @param int     $mask
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
    }

    /**
     * Sets the mask comparison strategy
     *
     * Do never call this method directly. Use the respective methods on the
     * AclInterface instead.
     *
     * @param string $strategy
     */
    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * Implementation of \Serializable
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->mask,
            $this->id,
            $this->securityIdentity,
            $this->strategy,
            $this->auditFailure,
            $this->auditSuccess,
            $this->granting,
        ));
    }

    /**
     * Implementation of \Serializable
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list($this->mask,
             $this->id,
             $this->securityIdentity,
             $this->strategy,
             $this->auditFailure,
             $this->auditSuccess,
             $this->granting
        ) = unserialize($serialized);
    }
}
