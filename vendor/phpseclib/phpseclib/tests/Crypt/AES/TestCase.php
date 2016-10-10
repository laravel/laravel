<?php
/**
 * @author    Andreas Fischer <bantu@phpbb.com>
 * @copyright MMXIII Andreas Fischer
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

require_once 'Crypt/AES.php';

abstract class Crypt_AES_TestCase extends PhpseclibTestCase
{
    static public function setUpBeforeClass()
    {
        if (!defined('CRYPT_AES_MODE')) {
            define('CRYPT_AES_MODE', CRYPT_AES_MODE_INTERNAL);
        }
    }

    public function setUp()
    {
        if (defined('CRYPT_AES_MODE') && CRYPT_AES_MODE !== CRYPT_AES_MODE_INTERNAL) {
            $this->markTestSkipped(
                'Skipping test because CRYPT_AES_MODE is not defined as CRYPT_AES_MODE_INTERNAL.'
            );
        }
    }
}
