<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Tests\Domain;

use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;

class RoleSecurityIdentityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $id = new RoleSecurityIdentity('ROLE_FOO');

        $this->assertEquals('ROLE_FOO', $id->getRole());
    }

    public function testConstructorWithRoleInstance()
    {
        $id = new RoleSecurityIdentity(new Role('ROLE_FOO'));

        $this->assertEquals('ROLE_FOO', $id->getRole());
    }

    /**
     * @dataProvider getCompareData
     */
    public function testEquals($id1, $id2, $equal)
    {
        if ($equal) {
            $this->assertTrue($id1->equals($id2));
        } else {
            $this->assertFalse($id1->equals($id2));
        }
    }

    public function getCompareData()
    {
        return array(
            array(new RoleSecurityIdentity('ROLE_FOO'), new RoleSecurityIdentity('ROLE_FOO'), true),
            array(new RoleSecurityIdentity('ROLE_FOO'), new RoleSecurityIdentity(new Role('ROLE_FOO')), true),
            array(new RoleSecurityIdentity('ROLE_USER'), new RoleSecurityIdentity('ROLE_FOO'), false),
            array(new RoleSecurityIdentity('ROLE_FOO'), new UserSecurityIdentity('ROLE_FOO', 'Foo'), false),
        );
    }
}
