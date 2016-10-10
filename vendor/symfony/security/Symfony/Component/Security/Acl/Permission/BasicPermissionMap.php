<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Permission;

/**
 * This is basic permission map complements the masks which have been defined
 * on the standard implementation of the MaskBuilder.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class BasicPermissionMap implements PermissionMapInterface
{
    const PERMISSION_VIEW        = 'VIEW';
    const PERMISSION_EDIT        = 'EDIT';
    const PERMISSION_CREATE      = 'CREATE';
    const PERMISSION_DELETE      = 'DELETE';
    const PERMISSION_UNDELETE    = 'UNDELETE';
    const PERMISSION_OPERATOR    = 'OPERATOR';
    const PERMISSION_MASTER      = 'MASTER';
    const PERMISSION_OWNER       = 'OWNER';

    protected $map;

    public function __construct()
    {
        $this->map = array(
            self::PERMISSION_VIEW => array(
                MaskBuilder::MASK_VIEW,
                MaskBuilder::MASK_EDIT,
                MaskBuilder::MASK_OPERATOR,
                MaskBuilder::MASK_MASTER,
                MaskBuilder::MASK_OWNER,
            ),

            self::PERMISSION_EDIT => array(
                MaskBuilder::MASK_EDIT,
                MaskBuilder::MASK_OPERATOR,
                MaskBuilder::MASK_MASTER,
                MaskBuilder::MASK_OWNER,
            ),

            self::PERMISSION_CREATE => array(
                MaskBuilder::MASK_CREATE,
                MaskBuilder::MASK_OPERATOR,
                MaskBuilder::MASK_MASTER,
                MaskBuilder::MASK_OWNER,
            ),

            self::PERMISSION_DELETE => array(
                MaskBuilder::MASK_DELETE,
                MaskBuilder::MASK_OPERATOR,
                MaskBuilder::MASK_MASTER,
                MaskBuilder::MASK_OWNER,
            ),

            self::PERMISSION_UNDELETE => array(
                MaskBuilder::MASK_UNDELETE,
                MaskBuilder::MASK_OPERATOR,
                MaskBuilder::MASK_MASTER,
                MaskBuilder::MASK_OWNER,
            ),

            self::PERMISSION_OPERATOR => array(
                MaskBuilder::MASK_OPERATOR,
                MaskBuilder::MASK_MASTER,
                MaskBuilder::MASK_OWNER,
            ),

            self::PERMISSION_MASTER => array(
                MaskBuilder::MASK_MASTER,
                MaskBuilder::MASK_OWNER,
            ),

            self::PERMISSION_OWNER => array(
                MaskBuilder::MASK_OWNER,
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMasks($permission, $object)
    {
        if (!isset($this->map[$permission])) {
            return;
        }

        return $this->map[$permission];
    }

    /**
     * {@inheritdoc}
     */
    public function contains($permission)
    {
        return isset($this->map[$permission]);
    }
}
