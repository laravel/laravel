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

use Symfony\Component\Security\Core\Role\Role;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRole()
    {
        $role = new Role('FOO');

        $this->assertEquals('FOO', $role->getRole());
    }
}
