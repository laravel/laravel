<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Tests\Encoder;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;

class EncoderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetEncoderWithMessageDigestEncoder()
    {
        $factory = new EncoderFactory(array('Symfony\Component\Security\Core\User\UserInterface' => array(
            'class' => 'Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder',
            'arguments' => array('sha512', true, 5),
        )));

        $encoder = $factory->getEncoder($this->getMock('Symfony\Component\Security\Core\User\UserInterface'));
        $expectedEncoder = new MessageDigestPasswordEncoder('sha512', true, 5);

        $this->assertEquals($expectedEncoder->encodePassword('foo', 'moo'), $encoder->encodePassword('foo', 'moo'));
    }

    public function testGetEncoderWithService()
    {
        $factory = new EncoderFactory(array(
            'Symfony\Component\Security\Core\User\UserInterface' => new MessageDigestPasswordEncoder('sha1'),
        ));

        $encoder = $factory->getEncoder($this->getMock('Symfony\Component\Security\Core\User\UserInterface'));
        $expectedEncoder = new MessageDigestPasswordEncoder('sha1');
        $this->assertEquals($expectedEncoder->encodePassword('foo', ''), $encoder->encodePassword('foo', ''));

        $encoder = $factory->getEncoder(new User('user', 'pass'));
        $expectedEncoder = new MessageDigestPasswordEncoder('sha1');
        $this->assertEquals($expectedEncoder->encodePassword('foo', ''), $encoder->encodePassword('foo', ''));
    }

    public function testGetEncoderWithClassName()
    {
        $factory = new EncoderFactory(array(
            'Symfony\Component\Security\Core\User\UserInterface' => new MessageDigestPasswordEncoder('sha1'),
        ));

        $encoder = $factory->getEncoder('Symfony\Component\Security\Core\Tests\Encoder\SomeChildUser');
        $expectedEncoder = new MessageDigestPasswordEncoder('sha1');
        $this->assertEquals($expectedEncoder->encodePassword('foo', ''), $encoder->encodePassword('foo', ''));
    }

    public function testGetEncoderConfiguredForConcreteClassWithService()
    {
        $factory = new EncoderFactory(array(
            'Symfony\Component\Security\Core\User\User' => new MessageDigestPasswordEncoder('sha1'),
        ));

        $encoder = $factory->getEncoder(new User('user', 'pass'));
        $expectedEncoder = new MessageDigestPasswordEncoder('sha1');
        $this->assertEquals($expectedEncoder->encodePassword('foo', ''), $encoder->encodePassword('foo', ''));
    }

    public function testGetEncoderConfiguredForConcreteClassWithClassName()
    {
        $factory = new EncoderFactory(array(
            'Symfony\Component\Security\Core\Tests\Encoder\SomeUser' => new MessageDigestPasswordEncoder('sha1'),
        ));

        $encoder = $factory->getEncoder('Symfony\Component\Security\Core\Tests\Encoder\SomeChildUser');
        $expectedEncoder = new MessageDigestPasswordEncoder('sha1');
        $this->assertEquals($expectedEncoder->encodePassword('foo', ''), $encoder->encodePassword('foo', ''));
    }
}

class SomeUser implements UserInterface
{
    public function getRoles() {}
    public function getPassword() {}
    public function getSalt() {}
    public function getUsername() {}
    public function eraseCredentials() {}
}

class SomeChildUser extends SomeUser
{
}
