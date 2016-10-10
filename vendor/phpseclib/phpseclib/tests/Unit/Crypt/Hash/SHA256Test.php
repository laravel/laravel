<?php
/**
 * @author    Andreas Fischer <bantu@phpbb.com>
 * @copyright 2014 Andreas Fischer
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

class Unit_Crypt_Hash_SHA256Test extends Unit_Crypt_Hash_TestCase
{
    public function getInstance()
    {
        return new Crypt_Hash('sha256');
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
                'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855'
            ),
            array(
                'The quick brown fox jumps over the lazy dog',
                'd7a8fbb307d7809469ca9abcb0082e4f8d5651e46d3cdb762d02d0bf37c9e592',
            ),
            array(
                'The quick brown fox jumps over the lazy dog.',
                'ef537f25c895bfa782526529a9b63d97aa631564d5d789c2b765448c8635fb6c',
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
                'b0344c61d8db38535ca8afceaf0bf12b881dc200c9833da726e9376c2e32cff7',
            ),
            // Test Case 2
            array(
                pack('H*', '4a656665'),
                pack('H*', '7768617420646f2079612077616e7420666f72206e6f7468696e673f'),
                '5bdcc146bf60754e6a042426089575c75a003f089d2739839dec58b964ec3843',
            ),
            // Test Case 3
            array(
                pack('H*', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'),
                pack('H*', 'dddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'),
                '773ea91e36800e46854db8ebd09181a72959098b3ef8c122d9635514ced565fe',
            ),
            // Test Case 4
            array(
                pack('H*', '0102030405060708090a0b0c0d0e0f10111213141516171819'),
                pack('H*', 'cdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcdcd'),
                '82558a389a443c0ea4cc819899f2083a85f0faa3e578f8077a2e3ff46729665b',
            ),
        );
    }
}
