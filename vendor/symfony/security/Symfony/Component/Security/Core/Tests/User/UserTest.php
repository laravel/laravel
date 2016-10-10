<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Tests\User;

use Symfony\Component\Security\Core\User\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Symfony\Component\Security\Core\User\User::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorException()
    {
        new User('', 'superpass');
    }

    /**
     * @covers Symfony\Component\Security\Core\User\User::__construct
     * @covers Symfony\Component\Security\Core\User\User::getRoles
     */
    public function testGetRoles()
    {
        $user = new User('fabien', 'superpass');
        $this->assertEquals(array(), $user->getRoles());

        $user = new User('fabien', 'superpass', array('ROLE_ADMIN'));
        $this->assertEquals(array('ROLE_ADMIN'), $user->getRoles());
    }

    /**
     * @covers Symfony\Component\Security\Core\User\User::__construct
     * @covers Symfony\Component\Security\Core\User\User::getPassword
     */
    public function testGetPassword()
    {
        $user = new User('fabien', 'superpass');
        $this->assertEquals('superpass', $user->getPassword());
    }

    /**
     * @covers Symfony\Component\Security\Core\User\User::__construct
     * @covers Symfony\Component\Security\Core\User\User::getUsername
     */
    public function testGetUsername()
    {
        $user = new User('fabien', 'superpass');
        $this->assertEquals('fabien', $user->getUsername());
    }

    /**
     * @covers Symfony\Component\Security\Core\User\User::getSalt
     */
    public function testGetSalt()
    {
        $user = new User('fabien', 'superpass');
        $this->assertEquals('', $user->getSalt());
    }

    /**
     * @covers Symfony\Component\Security\Core\User\User::isAccountNonExpired
     */
    public function testIsAccountNonExpired()
    {
        $user = new User('fabien', 'superpass');
        $this->assertTrue($user->isAccountNonExpired());

        $user = new User('fabien', 'superpass', array(), true, false);
        $this->assertFalse($user->isAccountNonExpired());
    }

    /**
     * @covers Symfony\Component\Security\Core\User\User::isCredentialsNonExpired
     */
    public function testIsCredentialsNonExpired()
    {
        $user = new User('fabien', 'superpass');
        $this->assertTrue($user->isCredentialsNonExpired());

        $user = new User('fabien', 'superpass', array(), true, true, false);
        $this->assertFalse($user->isCredentialsNonExpired());
    }

    /**
     * @covers Symfony\Component\Security\Core\User\User::isAccountNonLocked
     */
    public function testIsAccountNonLocked()
    {
        $user = new User('fabien', 'superpass');
        $this->assertTrue($user->isAccountNonLocked());

        $user = new User('fabien', 'superpass', array(), true, true, true, false);
        $this->assertFalse($user->isAccountNonLocked());
    }

    /**
     * @covers Symfony\Component\Security\Core\User\User::isEnabled
     */
    public function testIsEnabled()
    {
        $user = new User('fabien', 'superpass');
        $this->assertTrue($user->isEnabled());

        $user = new User('fabien', 'superpass', array(), false);
        $this->assertFalse($user->isEnabled());
    }

    /**
     * @covers Symfony\Component\Security\Core\User\User::eraseCredentials
     */
    public function testEraseCredentials()
    {
        $user = new User('fabien', 'superpass');
        $user->eraseCredentials();
        $this->assertEquals('superpass', $user->getPassword());
    }
}
