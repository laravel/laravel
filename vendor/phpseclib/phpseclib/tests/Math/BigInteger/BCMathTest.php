<?php
/**
 * @author    Andreas Fischer <bantu@phpbb.com>
 * @copyright MMXIII Andreas Fischer
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

class Math_BigInteger_BCMathTest extends Math_BigInteger_TestCase
{
    static public function setUpBeforeClass()
    {
        if (!extension_loaded('bcmath')) {
            self::markTestSkipped('BCMath extension is not available.');
        }

        parent::setUpBeforeClass();

        self::ensureConstant('MATH_BIGINTEGER_MODE', MATH_BIGINTEGER_MODE_BCMATH);
    }
}
