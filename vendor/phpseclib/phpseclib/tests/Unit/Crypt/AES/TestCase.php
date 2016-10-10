<?php
/**
 * @author    Andreas Fischer <bantu@phpbb.com>
 * @copyright 2013 Andreas Fischer
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

require_once 'Crypt/AES.php';

abstract class Unit_Crypt_AES_TestCase extends PhpseclibTestCase
{
    static public function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::reRequireFile('Crypt/Rijndael.php');
        self::reRequireFile('Crypt/AES.php');
    }

    /**
    * Produces all combinations of test values.
    *
    * @return array
    */
    public function continuousBufferCombos()
    {
        $modes = array(
            'CRYPT_AES_MODE_CTR',
            'CRYPT_AES_MODE_OFB',
            'CRYPT_AES_MODE_CFB',
        );
        $plaintexts = array(
            '',
            '12345678901234567', // https://github.com/phpseclib/phpseclib/issues/39
            "\xDE\xAD\xBE\xAF",
            ':-):-):-):-):-):-)', // https://github.com/phpseclib/phpseclib/pull/43
        );
        $ivs = array(
            '',
            'test123',
        );
        $keys = array(
            '',
            ':-8', // https://github.com/phpseclib/phpseclib/pull/43
            'FOOBARZ',
        );

        $result = array();

        // @codingStandardsIgnoreStart
        foreach ($modes as $mode)
        foreach ($plaintexts as $plaintext)
        foreach ($ivs as $iv)
        foreach ($keys as $key)
            $result[] = array($mode, $plaintext, $iv, $key);
        // @codingStandardsIgnoreEnd

        return $result;
    }

    /**
    * @dataProvider continuousBufferCombos
    */
    public function testEncryptDecryptWithContinuousBuffer($mode, $plaintext, $iv, $key)
    {
        $aes = new Crypt_AES(constant($mode));
        $aes->enableContinuousBuffer();
        $aes->setIV($iv);
        $aes->setKey($key);

        $actual = '';
        for ($i = 0, $strlen = strlen($plaintext); $i < $strlen; ++$i) {
            $actual .= $aes->decrypt($aes->encrypt($plaintext[$i]));
        }

        $this->assertEquals($plaintext, $actual);
    }

    /**
    * @group github451
    */
    public function testKeyPaddingRijndael()
    {
        // this test case is from the following URL:
        // https://web.archive.org/web/20070209120224/http://fp.gladman.plus.com/cryptography_technology/rijndael/aesdvec.zip

        $aes = new Crypt_Rijndael();
        $aes->disablePadding();
        $aes->setKey(pack('H*', '2b7e151628aed2a6abf7158809cf4f3c762e7160')); // 160-bit key. Valid in Rijndael.
        $ciphertext = $aes->encrypt(pack('H*', '3243f6a8885a308d313198a2e0370734'));
        $this->assertEquals($ciphertext, pack('H*', '231d844639b31b412211cfe93712b880'));
    }

    /**
    * @group github451
    */
    public function testKeyPaddingAES()
    {
        // same as the above - just with a different ciphertext

        $aes = new Crypt_AES();
        $aes->disablePadding();
        $aes->setKey(pack('H*', '2b7e151628aed2a6abf7158809cf4f3c762e7160')); // 160-bit key. AES should null pad to 192-bits
        $ciphertext = $aes->encrypt(pack('H*', '3243f6a8885a308d313198a2e0370734'));
        $this->assertEquals($ciphertext, pack('H*', 'c109292b173f841b88e0ee49f13db8c0'));
    }
}
