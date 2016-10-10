<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Model;

/**
 * This interface adds mutators for the AclInterface.
 *
 * All changes to Access Control Entries must go through this interface. Access
 * Control Entries must never be modified directly.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface MutableAclInterface extends AclInterface
{
    /**
     * Deletes a class-based ACE
     *
     * @param int     $index
     */
    public function deleteClassAce($index);

    /**
     * Deletes a class-field-based ACE
     *
     * @param int     $index
     * @param string  $field
     */
    public function deleteClassFieldAce($index, $field);

    /**
     * Deletes an object-based ACE
     *
     * @param int     $index
     */
    public function deleteObjectAce($index);

    /**
     * Deletes an object-field-based ACE
     *
     * @param int     $index
     * @param string  $field
     */
    public function deleteObjectFieldAce($index, $field);

    /**
     * Returns the primary key of this ACL
     *
     * @return int
     */
    public function getId();

    /**
     * Inserts a class-based ACE
     *
     * @param SecurityIdentityInterface $sid
     * @param int                       $mask
     * @param int                       $index
     * @param bool                      $granting
     * @param string                    $strategy
     */
    public function insertClassAce(SecurityIdentityInterface $sid, $mask, $index = 0, $granting = true, $strategy = null);

    /**
     * Inserts a class-field-based ACE
     *
     * @param string                    $field
     * @param SecurityIdentityInterface $sid
     * @param int                       $mask
     * @param int                       $index
     * @param bool                      $granting
     * @param string                    $strategy
     */
    public function insertClassFieldAce($field, SecurityIdentityInterface $sid, $mask, $index = 0, $granting = true, $strategy = null);

    /**
     * Inserts an object-based ACE
     *
     * @param SecurityIdentityInterface $sid
     * @param int                       $mask
     * @param int                       $index
     * @param bool                      $granting
     * @param string                    $strategy
     */
    public function insertObjectAce(SecurityIdentityInterface $sid, $mask, $index = 0, $granting = true, $strategy = null);

    /**
     * Inserts an object-field-based ACE
     *
     * @param string                    $field
     * @param SecurityIdentityInterface $sid
     * @param int                       $mask
     * @param int                       $index
     * @param bool                      $granting
     * @param string                    $strategy
     */
    public function insertObjectFieldAce($field, SecurityIdentityInterface $sid, $mask, $index = 0, $granting = true, $strategy = null);

    /**
     * Sets whether entries are inherited
     *
     * @param bool    $boolean
     */
    public function setEntriesInheriting($boolean);

    /**
     * Sets the parent ACL
     *
     * @param AclInterface|null $acl
     */
    public function setParentAcl(AclInterface $acl = null);

    /**
     * Updates a class-based ACE
     *
     * @param int     $index
     * @param int     $mask
     * @param string  $strategy if null the strategy should not be changed
     */
    public function updateClassAce($index, $mask, $strategy = null);

    /**
     * Updates a class-field-based ACE
     *
     * @param int     $index
     * @param string  $field
     * @param int     $mask
     * @param string  $strategy if null the strategy should not be changed
     */
    public function updateClassFieldAce($index, $field, $mask, $strategy = null);

    /**
     * Updates an object-based ACE
     *
     * @param int     $index
     * @param int     $mask
     * @param string  $strategy if null the strategy should not be changed
     */
    public function updateObjectAce($index, $mask, $strategy = null);

    /**
     * Updates an object-field-based ACE
     *
     * @param int     $index
     * @param string  $field
     * @param int     $mask
     * @param string  $strategy if null the strategy should not be changed
     */
    public function updateObjectFieldAce($index, $field, $mask, $strategy = null);
}
