<?php
/**
 * @author    Andreas Fischer <bantu@phpbb.com>
 * @copyright 2014 Andreas Fischer
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

class Unit_Crypt_Hash_SHA512Test extends Unit_Crypt_Hash_TestCase
{
    public function getInstance()
    {
        return new Crypt_Hash('sha512');
    }

    /**
    * @dataProvider hashData()
    */
    public function testHash($message, $result)
    {
        $this->assertHashesTo($this->getInstance(), $message, $result);
    }

    static public function hashData()
    {
        return array(
            array(
                '',
                'cf83e1357eefb8bdf1542850d66d8007d620e4050b5715dc83f4a921d36ce9ce47d0d13c5d85f2b0ff8318d2877eec2f63b931bd47417a81a538327af927da3e'
            ),
            array(
                'The quick brown fox jumps over the lazy dog',
                '07e547d9586f6a73f73fbac0435ed76951218fb7d0c8d788a309d785436bbb642e93a252a954f23912547d1e8a3b5ed6e1bfd7097821233fa0538f3db854fee6',
            ),
            array(
                'The quick brown fox jumps over the lazy dog.',
                '91ea1245f20d46ae9a037a989f54f1f790f0a47607eeb8a14d12890cea77a1bbc6c7ed9cf205e67b7f2b8fd4c7dfd3a7a8617e45f3c463d481c7e586c39ac1ed',
            ),
        );
    }

    /**
    * @dataProvider hmacData()
    */
    public function testHMAC($key, $message, $result)
    {
        $this->assertHMACsTo($this->getInstance(), $key, $message, $result);
    }

    static public function hmacData()
    {
        return array(
            // RFC 4231
            // Test Case 1
            array(
                pack('H*', '0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b0b'),
                pack('H*', '4869205468657265'),
                '87aa7cdea5ef619d4ff0b4241a1d6cb02379f4e2ce4ec2787ad0b30545e17cdedaa833b7d6b8a702038b274eaea3f4e4be9d914eeb61f1702e696c203a126854',
            ),
            // Test Case 2
            array(
                pack('H*', '4a656665'),
                pack('H*', '7768617420646f2079612077616e7420666f72206e6f7468696e673f'),
                '164b7a7bfcf819e2e395fbe73b56e0a387bd64222e831fd610270cd7ea2505549758bf75c05a994a6d034f65f8f0e6fdcaeab1a34d4a6b4b636e070a38bce737',
            ),
            // Test Case 3
            array(
                pack('H*', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'),
                pack('H*', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'),
                'fa73b0089d56a284efb0f0756c890be9b1b5dbdd8ee81a3655f83e33b2279d39bf3e848279a722c806b485a47e67c807b946a337bee8942674278859e13292fb',
            ),
            // Test Case 4
            array(
                pack('H*', '0102030405060708090a0b0c0d0e0f10111213141516171819'),
                pack('H*', 'cdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcd'),
                'b0ba465637458c6990e5a8c5f61d4af7e576d97ff94b872de76f8050361ee3dba91ca5c11aa25eb4d679275cc5788063a5f19741120c4f2de2adebeb10a298dd',
            ),
        );
    }
}
