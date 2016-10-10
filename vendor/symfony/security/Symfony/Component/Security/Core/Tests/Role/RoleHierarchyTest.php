<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Tests\Role;

use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\Role;

class RoleHierarchyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetReachableRoles()
    {
        $role = new RoleHierarchy(array(
            'ROLE_ADMIN' => array('ROLE_USER'),
            'ROLE_SUPER_ADMIN' => array('ROLE_ADMIN', 'ROLE_FOO'),
        ));

        $this->assertEquals(array(new Role('ROLE_USER')), $role->getReachableRoles(array(new Role('ROLE_USER'))));
        $this->assertEquals(array(new Role('ROLE_FOO')), $role->getReachableRoles(array(new Role('ROLE_FOO'))));
        $this->assertEquals(array(new Role('ROLE_ADMIN'), new Role('ROLE_USER')), $role->getReachableRoles(array(new Role('ROLE_ADMIN'))));
        $this->assertEquals(array(new Role('ROLE_FOO'), new Role('ROLE_ADMIN'), new Role('ROLE_USER')), $role->getReachableRoles(array(new Role('ROLE_FOO'), new Role('ROLE_ADMIN'))));
        $this->assertEquals(array(new Role('ROLE_SUPER_ADMIN'), new Role('ROLE_ADMIN'), new Role('ROLE_FOO'), new Role('ROLE_USER')), $role->getReachableRoles(array(new Role('ROLE_SUPER_ADMIN'))));
    }
}
