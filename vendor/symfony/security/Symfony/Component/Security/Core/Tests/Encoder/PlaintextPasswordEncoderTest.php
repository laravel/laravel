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

use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

class PlaintextPasswordEncoderTest extends \PHPUnit_Framework_TestCase
{
    public function testIsPasswordValid()
    {
        $encoder = new PlaintextPasswordEncoder();

        $this->assertTrue($encoder->isPasswordValid('foo', 'foo', ''));
        $this->assertFalse($encoder->isPasswordValid('bar', 'foo', ''));
        $this->assertFalse($encoder->isPasswordValid('FOO', 'foo', ''));

        $encoder = new PlaintextPasswordEncoder(true);

        $this->assertTrue($encoder->isPasswordValid('foo', 'foo', ''));
        $this->assertFalse($encoder->isPasswordValid('bar', 'foo', ''));
        $this->assertTrue($encoder->isPasswordValid('FOO', 'foo', ''));
    }

    public function testEncodePassword()
    {
        $encoder = new PlaintextPasswordEncoder();

        $this->assertSame('foo', $encoder->encodePassword('foo', ''));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testEncodePasswordLength()
    {
        $encoder = new PlaintextPasswordEncoder();

        $encoder->encodePassword(str_repeat('a', 5000), 'salt');
    }

    public function testCheckPasswordLength()
    {
        $encoder = new PlaintextPasswordEncoder();

        $this->assertFalse($encoder->isPasswordValid('encoded', str_repeat('a', 5000), 'salt'));
    }
}
