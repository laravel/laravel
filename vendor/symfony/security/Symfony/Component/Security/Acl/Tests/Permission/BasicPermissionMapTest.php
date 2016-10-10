<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Tests\Permission;

use Symfony\Component\Security\Acl\Permission\BasicPermissionMap;

class BasicPermissionMapTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMasksReturnsNullWhenNotSupportedMask()
    {
        $map = new BasicPermissionMap();
        $this->assertNull($map->getMasks('IS_AUTHENTICATED_REMEMBERED', null));
    }
}
