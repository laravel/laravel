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
 * This is the interface that must be implemented by permission maps.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface PermissionMapInterface
{
    /**
     * Returns an array of bitmasks.
     *
     * The security identity must have been granted access to at least one of
     * these bitmasks.
     *
     * @param string $permission
     * @param object $object
     * @return array may return null if permission/object combination is not supported
     */
    public function getMasks($permission, $object);

    /**
     * Whether this map contains the given permission
     *
     * @param string $permission
     * @return bool
     */
    public function contains($permission);
}
