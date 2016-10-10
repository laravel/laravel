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

use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

/**
 * @author Elnur Abdurrakhimov <elnur@elnur.pro>
 */
class BCryptPasswordEncoderTest extends \PHPUnit_Framework_TestCase
{
    const PASSWORD = 'password';
    const BYTES = '0123456789abcdef';
    const VALID_COST = '04';

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCostBelowRange()
    {
        new BCryptPasswordEncoder(3);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCostAboveRange()
    {
        new BCryptPasswordEncoder(32);
    }

    public function testCostInRange()
    {
        for ($cost = 4; $cost <= 31; $cost++) {
            new BCryptPasswordEncoder($cost);
        }
    }

    public function testResultLength()
    {
        $this->skipIfPhpVersionIsNotSupported();

        $encoder = new BCryptPasswordEncoder(self::VALID_COST);
        $result = $encoder->encodePassword(self::PASSWORD, null);
        $this->assertEquals(60, strlen($result));
    }

    public function testValidation()
    {
        $this->skipIfPhpVersionIsNotSupported();

        $encoder = new BCryptPasswordEncoder(self::VALID_COST);
        $result = $encoder->encodePassword(self::PASSWORD, null);
        $this->assertTrue($encoder->isPasswordValid($result, self::PASSWORD, null));
        $this->assertFalse($encoder->isPasswordValid($result, 'anotherPassword', null));
    }

    private function skipIfPhpVersionIsNotSupported()
    {
        if (version_compare(phpversion(), '5.3.7', '<')) {
            $this->markTestSkipped('Requires PHP >= 5.3.7');
        }
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testEncodePasswordLength()
    {
        $encoder = new BCryptPasswordEncoder(self::VALID_COST);

        $encoder->encodePassword(str_repeat('a', 5000), 'salt');
    }

    public function testCheckPasswordLength()
    {
        $encoder = new BCryptPasswordEncoder(self::VALID_COST);

        $this->assertFalse($encoder->isPasswordValid('encoded', str_repeat('a', 5000), 'salt'));
    }
}
