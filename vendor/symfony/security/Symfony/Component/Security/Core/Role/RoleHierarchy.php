<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Role;

/**
 * RoleHierarchy defines a role hierarchy.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RoleHierarchy implements RoleHierarchyInterface
{
    private $hierarchy;
    private $map;

    /**
     * Constructor.
     *
     * @param array $hierarchy An array defining the hierarchy
     */
    public function __construct(array $hierarchy)
    {
        $this->hierarchy = $hierarchy;

        $this->buildRoleMap();
    }

    /**
     * {@inheritdoc}
     */
    public function getReachableRoles(array $roles)
    {
        $reachableRoles = $roles;
        foreach ($roles as $role) {
            if (!isset($this->map[$role->getRole()])) {
                continue;
            }

            foreach ($this->map[$role->getRole()] as $r) {
                $reachableRoles[] = new Role($r);
            }
        }

        return $reachableRoles;
    }

    private function buildRoleMap()
    {
        $this->map = array();
        foreach ($this->hierarchy as $main => $roles) {
            $this->map[$main] = $roles;
            $visited = array();
            $additionalRoles = $roles;
            while ($role = array_shift($additionalRoles)) {
                if (!isset($this->hierarchy[$role])) {
                    continue;
                }

                $visited[] = $role;
                $this->map[$main] = array_unique(array_merge($this->map[$main], $this->hierarchy[$role]));
                $additionalRoles = array_merge($additionalRoles, array_diff($this->hierarchy[$role], $visited));
            }
        }
    }
}
