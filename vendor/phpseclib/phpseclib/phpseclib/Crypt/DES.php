<?php

/**
 * Pure-PHP implementation of DES.
 *
 * Uses mcrypt, if available, and an internal implementation, otherwise.
 *
 * PHP versions 4 and 5
 *
 * Useful resources are as follows:
 *
 *  - {@link http://en.wikipedia.org/wiki/DES_supplementary_material Wikipedia: DES supplementary material}
 *  - {@link http://www.itl.nist.gov/fipspubs/fip46-2.htm FIPS 46-2 - (DES), Data Encryption Standard}
 *  - {@link http://www.cs.eku.edu/faculty/styer/460/Encrypt/JS-DES.html JavaScript DES Example}
 *
 * Here's a short example of how to use this library:
 * <code>
 * <?php
 *    include 'Crypt/DES.php';
 *
 *    $des = new Crypt_DES();
 *
 *    $des->setKey('abcdefgh');
 *
 *    $size = 10 * 1024;
 *    $plaintext = '';
 *    for ($i = 0; $i < $size; $i++) {
 *        $plaintext.= 'a';
 *    }
 *
 *    echo $des->decrypt($des->encrypt($plaintext));
 * ?>
 * </code>
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category  Crypt
 * @package   Crypt_DES
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2007 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */

/**
 * Include Crypt_Base
 *
 * Base cipher class
 */
if (!class_exists('Crypt_Base')) {
    include_once 'Base.php';
}

/**#@+
 * @access private
 * @see Crypt_DES::_setupKey()
 * @see Crypt_DES::_processBlock()
 */
/**
 * Contains $keys[CRYPT_DES_ENCRYPT]
 */
define('CRYPT_DES_ENCRYPT', 0);
/**
 * Contains $keys[CRYPT_DES_DECRYPT]
 */
define('CRYPT_DES_DECRYPT', 1);
/**#@-*/

/**#@+
 * @access public
 * @see Crypt_DES::encrypt()
 * @see Crypt_DES::decrypt()
 */
/**
 * Encrypt / decrypt using the Counter mode.
 *
 * Set to -1 since that's what Crypt/Random.php uses to index the CTR mode.
 *
 * @link http://en.wikipedia.org/wiki/Block_cipher_modes_of_operation#Counter_.28CTR.29
 */
define('CRYPT_DES_MODE_CTR', CRYPT_MODE_CTR);
/**
 * Encrypt / decrypt using the Electronic Code Book mode.
 *
 * @link http://en.wikipedia.org/wiki/Block_cipher_modes_of_operation#Electronic_codebook_.28ECB.29
 */
define('CRYPT_DES_MODE_ECB', CRYPT_MODE_ECB);
/**
 * Encrypt / decrypt using the Code Book Chaining mode.
 *
 * @link http://en.wikipedia.org/wiki/Block_cipher_modes_of_operation#Cipher-block_chaining_.28CBC.29
 */
define('CRYPT_DES_MODE_CBC', CRYPT_MODE_CBC);
/**
 * Encrypt / decrypt using the Cipher Feedback mode.
 *
 * @link http://en.wikipedia.org/wiki/Block_cipher_modes_of_operation#Cipher_feedback_.28CFB.29
 */
define('CRYPT_DES_MODE_CFB', CRYPT_MODE_CFB);
/**
 * Encrypt / decrypt using the Cipher Feedback mode.
 *
 * @link http://en.wikipedia.org/wiki/Block_cipher_modes_of_operation#Output_feedback_.28OFB.29
 */
define('CRYPT_DES_MODE_OFB', CRYPT_MODE_OFB);
/**#@-*/

/**#@+
 * @access private
 * @see Crypt_Base::Crypt_Base()
 */
/**
 * Toggles the internal implementation
 */
define('CRYPT_DES_MODE_INTERNAL', CRYPT_MODE_INTERNAL);
/**
 * Toggles the mcrypt implementation
 */
define('CRYPT_DES_MODE_MCRYPT', CRYPT_MODE_MCRYPT);
/**#@-*/

/**
 * Pure-PHP implementation of DES.
 *
 * @package Crypt_DES
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
class Crypt_DES extends Crypt_Base
{
    /**
     * Block Length of the cipher
     *
     * @see Crypt_Base::block_size
     * @var Integer
     * @access private
     */
    var $block_size = 8;

    /**
     * The Key
     *
     * @see Crypt_Base::key
     * @see setKey()
     * @var String
     * @access private
     */
    var $key = "\0\0\0\0\0\0\0\0";

    /**
     * The default password key_size used by setPassword()
     *
     * @see Crypt_Base::password_key_size
     * @see Crypt_Base::setPassword()
     * @var Integer
     * @access private
     */
    var $password_key_size = 8;

    /**
     * The namespace used by the cipher for its constants.
     *
     * @see Crypt_Base::const_namespace
     * @var String
     * @access private
     */
    var $const_namespace = 'DES';

    /**
     * The mcrypt specific name of the cipher
     *
     * @see Crypt_Base::cipher_name_mcrypt
     * @var String
     * @access private
     */
    var $cipher_name_mcrypt = 'des';

    /**
     * Optimizing value while CFB-encrypting
     *
     * @see Crypt_Base::cfb_init_len
     * @var Integer
     * @access private
     */
    var $cfb_init_len = 500;

    /**
     * Switch for DES/3DES encryption
     *
     * Used only if $engine == CRYPT_DES_MODE_INTERNAL
     *
     * @see Crypt_DES::_setupKey()
     * @see Crypt_DES::_processBlock()
     * @var Integer
     * @access private
     */
    var $des_rounds = 1;

    /**
     * max possible size of $key
     *
     * @see Crypt_DES::setKey()
     * @var String
     * @access private
     */
    var $key_size_max = 8;

    /**
     * The Key Schedule
     *
     * @see Crypt_DES::_setupKey()
     * @var Array
     * @access private
     */
    var $keys;

    /**
     * Shuffle table.
     *
     * For each byte value index, the entry holds an 8-byte string
     * with each byte containing all bits in the same state as the
     * corresponding bit in the index value.
     *
     * @see Crypt_DES::_processBlock()
     * @see Crypt_DES::_setupKey()
     * @var Array
     * @access private
     */
    var $shuffle = array(
        "\x00\x00\x00\x00\x00\x00\x00\x00", "\x00\x00\x00\x00\x00\x00\x00\xFF",
        "\x00\x00\x00\x00\x00\x00\xFF\x00", "\x00\x00\x00\x00\x00\x00\xFF\xFF",
        "\x00\x00\x00\x00\x00\xFF\x00\x00", "\x00\x00\x00\x00\x00\xFF\x00\xFF",
        "\x00\x00\x00\x00\x00\xFF\xFF\x00", "\x00\x00\x00\x00\x00\xFF\xFF\xFF",
        "\x00\x00\x00\x00\xFF\x00\x00\x00", "\x00\x00\x00\x00\xFF\x00\x00\xFF",
        "\x00\x00\x00\x00\xFF\x00\xFF\x00", "\x00\x00\x00\x00\xFF\x00\xFF\xFF",
        "\x00\x00\x00\x00\xFF\xFF\x00\x00", "\x00\x00\x00\x00\xFF\xFF\x00\xFF",
        "\x00\x00\x00\x00\xFF\xFF\xFF\x00", "\x00\x00\x00\x00\xFF\xFF\xFF\xFF",
        "\x00\x00\x00\xFF\x00\x00\x00\x00", "\x00\x00\x00\xFF\x00\x00\x00\xFF",
        "\x00\x00\x00\xFF\x00\x00\xFF\x00", "\x00\x00\x00\xFF\x00\x00\xFF\xFF",
        "\x00\x00\x00\xFF\x00\xFF\x00\x00", "\x00\x00\x00\xFF\x00\xFF\x00\xFF",
        "\x00\x00\x00\xFF\x00\xFF\xFF\x00", "\x00\x00\x00\xFF\x00\xFF\xFF\xFF",
        "\x00\x00\x00\xFF\xFF\x00\x00\x00", "\x00\x00\x00\xFF\xFF\x00\x00\xFF",
        "\x00\x00\x00\xFF\xFF\x00\xFF\x00", "\x00\x00\x00\xFF\xFF\x00\xFF\xFF",
        "\x00\x00\x00\xFF\xFF\xFF\x00\x00", "\x00\x00\x00\xFF\xFF\xFF\x00\xFF",
        "\x00\x00\x00\xFF\xFF\xFF\xFF\x00", "\x00\x00\x00\xFF\xFF\xFF\xFF\xFF",
        "\x00\x00\xFF\x00\x00\x00\x00\x00", "\x00\x00\xFF\x00\x00\x00\x00\xFF",
        "\x00\x00\xFF\x00\x00\x00\xFF\x00", "\x00\x00\xFF\x00\x00\x00\xFF\xFF",
        "\x00\x00\xFF\x00\x00\xFF\x00\x00", "\x00\x00\xFF\x00\x00\xFF\x00\xFF",
        "\x00\x00\xFF\x00\x00\xFF\xFF\x00", "\x00\x00\xFF\x00\x00\xFF\xFF\xFF",
        "\x00\x00\xFF\x00\xFF\x00\x00\x00", "\x00\x00\xFF\x00\xFF\x00\x00\xFF",
        "\x00\x00\xFF\x00\xFF\x00\xFF\x00", "\x00\x00\xFF\x00\xFF\x00\xFF\xFF",
        "\x00\x00\xFF\x00\xFF\xFF\x00\x00", "\x00\x00\xFF\x00\xFF\xFF\x00\xFF",
        "\x00\x00\xFF\x00\xFF\xFF\xFF\x00", "\x00\x00\xFF\x00\xFF\xFF\xFF\xFF",
        "\x00\x00\xFF\xFF\x00\x00\x00\x00", "\x00\x00\xFF\xFF\x00\x00\x00\xFF",
        "\x00\x00\xFF\xFF\x00\x00\xFF\x00", "\x00\x00\xFF\xFF\x00\x00\xFF\xFF",
        "\x00\x00\xFF\xFF\x00\xFF\x00\x00", "\x00\x00\xFF\xFF\x00\xFF\x00\xFF",
        "\x00\x00\xFF\xFF\x00\xFF\xFF\x00", "\x00\x00\xFF\xFF\x00\xFF\xFF\xFF",
        "\x00\x00\xFF\xFF\xFF\x00\x00\x00", "\x00\x00\xFF\xFF\xFF\x00\x00\xFF",
        "\x00\x00\xFF\xFF\xFF\x00\xFF\x00", "\x00\x00\xFF\xFF\xFF\x00\xFF\xFF",
        "\x00\x00\xFF\xFF\xFF\xFF\x00\x00", "\x00\x00\xFF\xFF\xFF\xFF\x00\xFF",
        "\x00\x00\xFF\xFF\xFF\xFF\xFF\x00", "\x00\x00\xFF\xFF\xFF\xFF\xFF\xFF",
        "\x00\xFF\x00\x00\x00\x00\x00\x00", "\x00\xFF\x00\x00\x00\x00\x00\xFF",
        "\x00\xFF\x00\x00\x00\x00\xFF\x00", "\x00\xFF\x00\x00\x00\x00\xFF\xFF",
        "\x00\xFF\x00\x00\x00\xFF\x00\x00", "\x00\xFF\x00\x00\x00\xFF\x00\xFF",
        "\x00\xFF\x00\x00\x00\xFF\xFF\x00", "\x00\xFF\x00\x00\x00\xFF\xFF\xFF",
        "\x00\xFF\x00\x00\xFF\x00\x00\x00", "\x00\xFF\x00\x00\xFF\x00\x00\xFF",
        "\x00\xFF\x00\x00\xFF\x00\xFF\x00", "\x00\xFF\x00\x00\xFF\x00\xFF\xFF",
        "\x00\xFF\x00\x00\xFF\xFF\x00\x00", "\x00\xFF\x00\x00\xFF\xFF\x00\xFF",
        "\x00\xFF\x00\x00\xFF\xFF\xFF\x00", "\x00\xFF\x00\x00\xFF\xFF\xFF\xFF",
        "\x00\xFF\x00\xFF\x00\x00\x00\x00", "\x00\xFF\x00\xFF\x00\x00\x00\xFF",
        "\x00\xFF\x00\xFF\x00\x00\xFF\x00", "\x00\xFF\x00\xFF\x00\x00\xFF\xFF",
        "\x00\xFF\x00\xFF\x00\xFF\x00\x00", "\x00\xFF\x00\xFF\x00\xFF\x00\xFF",
        "\x00\xFF\x00\xFF\x00\xFF\xFF\x00", "\x00\xFF\x00\xFF\x00\xFF\xFF\xFF",
        "\x00\xFF\x00\xFF\xFF\x00\x00\x00", "\x00\xFF\x00\xFF\xFF\x00\x00\xFF",
        "\x00\xFF\x00\xFF\xFF\x00\xFF\x00", "\x00\xFF\x00\xFF\xFF\x00\xFF\xFF",
        "\x00\xFF\x00\xFF\xFF\xFF\x00\x00", "\x00\xFF\x00\xFF\xFF\xFF\x00\xFF",
        "\x00\xFF\x00\xFF\xFF\xFF\xFF\x00", "\x00\xFF\x00\xFF\xFF\xFF\xFF\xFF",
        "\x00\xFF\xFF\x00\x00\x00\x00\x00", "\x00\xFF\xFF\x00\x00\x00\x00\xFF",
        "\x00\xFF\xFF\x00\x00\x00\xFF\x00", "\x00\xFF\xFF\x00\x00\x00\xFF\xFF",
        "\x00\xFF\xFF\x00\x00\xFF\x00\x00", "\x00\xFF\xFF\x00\x00\xFF\x00\xFF",
        "\x00\xFF\xFF\x00\x00\xFF\xFF\x00", "\x00\xFF\xFF\x00\x00\xFF\xFF\xFF",
        "\x00\xFF\xFF\x00\xFF\x00\x00\x00", "\x00\xFF\xFF\x00\xFF\x00\x00\xFF",
        "\x00\xFF\xFF\x00\xFF\x00\xFF\x00", "\x00\xFF\xFF\x00\xFF\x00\xFF\xFF",
        "\x00\xFF\xFF\x00\xFF\xFF\x00\x00", "\x00\xFF\xFF\x00\xFF\xFF\x00\xFF",
        "\x00\xFF\xFF\x00\xFF\xFF\xFF\x00", "\x00\xFF\xFF\x00\xFF\xFF\xFF\xFF",
        "\x00\xFF\xFF\xFF\x00\x00\x00\x00", "\x00\xFF\xFF\xFF\x00\x00\x00\xFF",
        "\x00\xFF\xFF\xFF\x00\x00\xFF\x00", "\x00\xFF\xFF\xFF\x00\x00\xFF\xFF",
        "\x00\xFF\xFF\xFF\x00\xFF\x00\x00", "\x00\xFF\xFF\xFF\x00\xFF\x00\xFF",
        "\x00\xFF\xFF\xFF\x00\xFF\xFF\x00", "\x00\xFF\xFF\xFF\x00\xFF\xFF\xFF",
        "\x00\xFF\xFF\xFF\xFF\x00\x00\x00", "\x00\xFF\xFF\xFF\xFF\x00\x00\xFF",
        "\x00\xFF\xFF\xFF\xFF\x00\xFF\x00", "\x00\xFF\xFF\xFF\xFF\x00\xFF\xFF",
        "\x00\xFF\xFF\xFF\xFF\xFF\x00\x00", "\x00\xFF\xFF\xFF\xFF\xFF\x00\xFF",
        "\x00\xFF\xFF\xFF\xFF\xFF\xFF\x00", "\x00\xFF\xFF\xFF\xFF\xFF\xFF\xFF",
        "\xFF\x00\x00\x00\x00\x00\x00\x00", "\xFF\x00\x00\x00\x00\x00\x00\xFF",
        "\xFF\x00\x00\x00\x00\x00\xFF\x00", "\xFF\x00\x00\x00\x00\x00\xFF\xFF",
        "\xFF\x00\x00\x00\x00\xFF\x00\x00", "\xFF\x00\x00\x00\x00\xFF\x00\xFF",
        "\xFF\x00\x00\x00\x00\xFF\xFF\x00", "\xFF\x00\x00\x00\x00\xFF\xFF\xFF",
        "\xFF\x00\x00\x00\xFF\x00\x00\x00", "\xFF\x00\x00\x00\xFF\x00\x00\xFF",
        "\xFF\x00\x00\x00\xFF\x00\xFF\x00", "\xFF\x00\x00\x00\xFF\x00\xFF\xFF",
        "\xFF\x00\x00\x00\xFF\xFF\x00\x00", "\xFF\x00\x00\x00\xFF\xFF\x00\xFF",
        "\xFF\x00\x00\x00\xFF\xFF\xFF\x00", "\xFF\x00\x00\x00\xFF\xFF\xFF\xFF",
        "\xFF\x00\x00\xFF\x00\x00\x00\x00", "\xFF\x00\x00\xFF\x00\x00\x00\xFF",
        "\xFF\x00\x00\xFF\x00\x00\xFF\x00", "\xFF\x00\x00\xFF\x00\x00\xFF\xFF",
        "\xFF\x00\x00\xFF\x00\xFF\x00\x00", "\xFF\x00\x00\xFF\x00\xFF\x00\xFF",
        "\xFF\x00\x00\xFF\x00\xFF\xFF\x00", "\xFF\x00\x00\xFF\x00\xFF\xFF\xFF",
        "\xFF\x00\x00\xFF\xFF\x00\x00\x00", "\xFF\x00\x00\xFF\xFF\x00\x00\xFF",
        "\xFF\x00\x00\xFF\xFF\x00\xFF\x00", "\xFF\x00\x00\xFF\xFF\x00\xFF\xFF",
        "\xFF\x00\x00\xFF\xFF\xFF\x00\x00", "\xFF\x00\x00\xFF\xFF\xFF\x00\xFF",
        "\xFF\x00\x00\xFF\xFF\xFF\xFF\x00", "\xFF\x00\x00\xFF\xFF\xFF\xFF\xFF",
        "\xFF\x00\xFF\x00\x00\x00\x00\x00", "\xFF\x00\xFF\x00\x00\x00\x00\xFF",
        "\xFF\x00\xFF\x00\x00\x00\xFF\x00", "\xFF\x00\xFF\x00\x00\x00\xFF\xFF",
        "\xFF\x00\xFF\x00\x00\xFF\x00\x00", "\xFF\x00\xFF\x00\x00\xFF\x00\xFF",
        "\xFF\x00\xFF\x00\x00\xFF\xFF\x00", "\xFF\x00\xFF\x00\x00\xFF\xFF\xFF",
        "\xFF\x00\xFF\x00\xFF\x00\x00\x00", "\xFF\x00\xFF\x00\xFF\x00\x00\xFF",
        "\xFF\x00\xFF\x00\xFF\x00\xFF\x00", "\xFF\x00\xFF\x00\xFF\x00\xFF\xFF",
        "\xFF\x00\xFF\x00\xFF\xFF\x00\x00", "\xFF\x00\xFF\x00\xFF\xFF\x00\xFF",
        "\xFF\x00\xFF\x00\xFF\xFF\xFF\x00", "\xFF\x00\xFF\x00\xFF\xFF\xFF\xFF",
        "\xFF\x00\xFF\xFF\x00\x00\x00\x00", "\xFF\x00\xFF\xFF\x00\x00\x00\xFF",
        "\xFF\x00\xFF\xFF\x00\x00\xFF\x00", "\xFF\x00\xFF\xFF\x00\x00\xFF\xFF",
        "\xFF\x00\xFF\xFF\x00\xFF\x00\x00", "\xFF\x00\xFF\xFF\x00\xFF\x00\xFF",
        "\xFF\x00\xFF\xFF\x00\xFF\xFF\x00", "\xFF\x00\xFF\xFF\x00\xFF\xFF\xFF",
        "\xFF\x00\xFF\xFF\xFF\x00\x00\x00", "\xFF\x00\xFF\xFF\xFF\x00\x00\xFF",
        "\xFF\x00\xFF\xFF\xFF\x00\xFF\x00", "\xFF\x00\xFF\xFF\xFF\x00\xFF\xFF",
        "\xFF\x00\xFF\xFF\xFF\xFF\x00\x00", "\xFF\x00\xFF\xFF\xFF\xFF\x00\xFF",
        "\xFF\x00\xFF\xFF\xFF\xFF\xFF\x00", "\xFF\x00\xFF\xFF\xFF\xFF\xFF\xFF",
        "\xFF\xFF\x00\x00\x00\x00\x00\x00", "\xFF\xFF\x00\x00\x00\x00\x00\xFF",
        "\xFF\xFF\x00\x00\x00\x00\xFF\x00", "\xFF\xFF\x00\x00\x00\x00\xFF\xFF",
        "\xFF\xFF\x00\x00\x00\xFF\x00\x00", "\xFF\xFF\x00\x00\x00\xFF\x00\xFF",
        "\xFF\xFF\x00\x00\x00\xFF\xFF\x00", "\xFF\xFF\x00\x00\x00\xFF\xFF\xFF",
        "\xFF\xFF\x00\x00\xFF\x00\x00\x00", "\xFF\xFF\x00\x00\xFF\x00\x00\xFF",
        "\xFF\xFF\x00\x00\xFF\x00\xFF\x00", "\xFF\xFF\x00\x00\xFF\x00\xFF\xFF",
        "\xFF\xFF\x00\x00\xFF\xFF\x00\x00", "\xFF\xFF\x00\x00\xFF\xFF\x00\xFF",
        "\xFF\xFF\x00\x00\xFF\xFF\xFF\x00", "\xFF\xFF\x00\x00\xFF\xFF\xFF\xFF",
        "\xFF\xFF\x00\xFF\x00\x00\x00\x00", "\xFF\xFF\x00\xFF\x00\x00\x00\xFF",
        "\xFF\xFF\x00\xFF\x00\x00\xFF\x00", "\xFF\xFF\x00\xFF\x00\x00\xFF\xFF",
        "\xFF\xFF\x00\xFF\x00\xFF\x00\x00", "\xFF\xFF\x00\xFF\x00\xFF\x00\xFF",
        "\xFF\xFF\x00\xFF\x00\xFF\xFF\x00", "\xFF\xFF\x00\xFF\x00\xFF\xFF\xFF",
        "\xFF\xFF\x00\xFF\xFF\x00\x00\x00", "\xFF\xFF\x00\xFF\xFF\x00\x00\xFF",
        "\xFF\xFF\x00\xFF\xFF\x00\xFF\x00", "\xFF\xFF\x00\xFF\xFF\x00\xFF\xFF",
        "\xFF\xFF\x00\xFF\xFF\xFF\x00\x00", "\xFF\xFF\x00\xFF\xFF\xFF\x00\xFF",
        "\xFF\xFF\x00\xFF\xFF\xFF\xFF\x00", "\xFF\xFF\x00\xFF\xFF\xFF\xFF\xFF",
        "\xFF\xFF\xFF\x00\x00\x00\x00\x00", "\xFF\xFF\xFF\x00\x00\x00\x00\xFF",
        "\xFF\xFF\xFF\x00\x00\x00\xFF\x00", "\xFF\xFF\xFF\x00\x00\x00\xFF\xFF",
        "\xFF\xFF\xFF\x00\x00\xFF\x00\x00", "\xFF\xFF\xFF\x00\x00\xFF\x00\xFF",
        "\xFF\xFF\xFF\x00\x00\xFF\xFF\x00", "\xFF\xFF\xFF\x00\x00\xFF\xFF\xFF",
        "\xFF\xFF\xFF\x00\xFF\x00\x00\x00", "\xFF\xFF\xFF\x00\xFF\x00\x00\xFF",
        "\xFF\xFF\xFF\x00\xFF\x00\xFF\x00", "\xFF\xFF\xFF\x00\xFF\x00\xFF\xFF",
        "\xFF\xFF\xFF\x00\xFF\xFF\x00\x00", "\xFF\xFF\xFF\x00\xFF\xFF\x00\xFF",
        "\xFF\xFF\xFF\x00\xFF\xFF\xFF\x00", "\xFF\xFF\xFF\x00\xFF\xFF\xFF\xFF",
        "\xFF\xFF\xFF\xFF\x00\x00\x00\x00", "\xFF\xFF\xFF\xFF\x00\x00\x00\xFF",
        "\xFF\xFF\xFF\xFF\x00\x00\xFF\x00", "\xFF\xFF\xFF\xFF\x00\x00\xFF\xFF",
        "\xFF\xFF\xFF\xFF\x00\xFF\x00\x00", "\xFF\xFF\xFF\xFF\x00\xFF\x00\xFF",
        "\xFF\xFF\xFF\xFF\x00\xFF\xFF\x00", "\xFF\xFF\xFF\xFF\x00\xFF\xFF\xFF",
        "\xFF\xFF\xFF\xFF\xFF\x00\x00\x00", "\xFF\xFF\xFF\xFF\xFF\x00\x00\xFF",
        "\xFF\xFF\xFF\xFF\xFF\x00\xFF\x00", "\xFF\xFF\xFF\xFF\xFF\x00\xFF\xFF",
        "\xFF\xFF\xFF\xFF\xFF\xFF\x00\x00", "\xFF\xFF\xFF\xFF\xFF\xFF\x00\xFF",
        "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\x00", "\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"
    );

    /**
     * IP mapping helper table.
     *
     * Indexing this table with each source byte performs the initial bit permutation.
     *
     * @var Array
     * @access private
     */
    var $ipmap = array(
        0x00, 0x10, 0x01, 0x11, 0x20, 0x30, 0x21, 0x31,
        0x02, 0x12, 0x03, 0x13, 0x22, 0x32, 0x23, 0x33,
        0x40, 0x50, 0x41, 0x51, 0x60, 0x70, 0x61, 0x71,
        0x42, 0x52, 0x43, 0x53, 0x62, 0x72, 0x63, 0x73,
        0x04, 0x14, 0x05, 0x15, 0x24, 0x34, 0x25, 0x35,
        0x06, 0x16, 0x07, 0x17, 0x26, 0x36, 0x27, 0x37,
        0x44, 0x54, 0x45, 0x55, 0x64, 0x74, 0x65, 0x75,
        0x46, 0x56, 0x47, 0x57, 0x66, 0x76, 0x67, 0x77,
        0x80, 0x90, 0x81, 0x91, 0xA0, 0xB0, 0xA1, 0xB1,
        0x82, 0x92, 0x83, 0x93, 0xA2, 0xB2, 0xA3, 0xB3,
        0xC0, 0xD0, 0xC1, 0xD1, 0xE0, 0xF0, 0xE1, 0xF1,
        0xC2, 0xD2, 0xC3, 0xD3, 0xE2, 0xF2, 0xE3, 0xF3,
        0x84, 0x94, 0x85, 0x95, 0xA4, 0xB4, 0xA5, 0xB5,
        0x86, 0x96, 0x87, 0x97, 0xA6, 0xB6, 0xA7, 0xB7,
        0xC4, 0xD4, 0xC5, 0xD5, 0xE4, 0xF4, 0xE5, 0xF5,
        0xC6, 0xD6, 0xC7, 0xD7, 0xE6, 0xF6, 0xE7, 0xF7,
        0x08, 0x18, 0x09, 0x19, 0x28, 0x38, 0x29, 0x39,
        0x0A, 0x1A, 0x0B, 0x1B, 0x2A, 0x3A, 0x2B, 0x3B,
        0x48, 0x58, 0x49, 0x59, 0x68, 0x78, 0x69, 0x79,
        0x4A, 0x5A, 0x4B, 0x5B, 0x6A, 0x7A, 0x6B, 0x7B,
        0x0C, 0x1C, 0x0D, 0x1D, 0x2C, 0x3C, 0x2D, 0x3D,
        0x0E, 0x1E, 0x0F, 0x1F, 0x2E, 0x3E, 0x2F, 0x3F,
        0x4C, 0x5C, 0x4D, 0x5D, 0x6C, 0x7C, 0x6D, 0x7D,
        0x4E, 0x5E, 0x4F, 0x5F, 0x6E, 0x7E, 0x6F, 0x7F,
        0x88, 0x98, 0x89, 0x99, 0xA8, 0xB8, 0xA9, 0xB9,
        0x8A, 0x9A, 0x8B, 0x9B, 0xAA, 0xBA, 0xAB, 0xBB,
        0xC8, 0xD8, 0xC9, 0xD9, 0xE8, 0xF8, 0xE9, 0xF9,
        0xCA, 0xDA, 0xCB, 0xDB, 0xEA, 0xFA, 0xEB, 0xFB,
        0x8C, 0x9C, 0x8D, 0x9D, 0xAC, 0xBC, 0xAD, 0xBD,
        0x8E, 0x9E, 0x8F, 0x9F, 0xAE, 0xBE, 0xAF, 0xBF,
        0xCC, 0xDC, 0xCD, 0xDD, 0xEC, 0xFC, 0xED, 0xFD,
        0xCE, 0xDE, 0xCF, 0xDF, 0xEE, 0xFE, 0xEF, 0xFF
    );

    /**
     * Inverse IP mapping helper table.
     * Indexing this table with a byte value reverses the bit order.
     *
     * @var Array
     * @access private
     */
    var $invipmap = array(
        0x00, 0x80, 0x40, 0xC0, 0x20, 0xA0, 0x60, 0xE0,
        0x10, 0x90, 0x50, 0xD0, 0x30, 0xB0, 0x70, 0xF0,
        0x08, 0x88, 0x48, 0xC8, 0x28, 0xA8, 0x68, 0xE8,
        0x18, 0x98, 0x58, 0xD8, 0x38, 0xB8, 0x78, 0xF8,
        0x04, 0x84, 0x44, 0xC4, 0x24, 0xA4, 0x64, 0xE4,
        0x14, 0x94, 0x54, 0xD4, 0x34, 0xB4, 0x74, 0xF4,
        0x0C, 0x8C, 0x4C, 0xCC, 0x2C, 0xAC, 0x6C, 0xEC,
        0x1C, 0x9C, 0x5C, 0xDC, 0x3C, 0xBC, 0x7C, 0xFC,
        0x02, 0x82, 0x42, 0xC2, 0x22, 0xA2, 0x62, 0xE2,
        0x12, 0x92, 0x52, 0xD2, 0x32, 0xB2, 0x72, 0xF2,
        0x0A, 0x8A, 0x4A, 0xCA, 0x2A, 0xAA, 0x6A, 0xEA,
        0x1A, 0x9A, 0x5A, 0xDA, 0x3A, 0xBA, 0x7A, 0xFA,
        0x06, 0x86, 0x46, 0xC6, 0x26, 0xA6, 0x66, 0xE6,
        0x16, 0x96, 0x56, 0xD6, 0x36, 0xB6, 0x76, 0xF6,
        0x0E, 0x8E, 0x4E, 0xCE, 0x2E, 0xAE, 0x6E, 0xEE,
        0x1E, 0x9E, 0x5E, 0xDE, 0x3E, 0xBE, 0x7E, 0xFE,
        0x01, 0x81, 0x41, 0xC1, 0x21, 0xA1, 0x61, 0xE1,
        0x11, 0x91, 0x51, 0xD1, 0x31, 0xB1, 0x71, 0xF1,
        0x09, 0x89, 0x49, 0xC9, 0x29, 0xA9, 0x69, 0xE9,
        0x19, 0x99, 0x59, 0xD9, 0x39, 0xB9, 0x79, 0xF9,
        0x05, 0x85, 0x45, 0xC5, 0x25, 0xA5, 0x65, 0xE5,
        0x15, 0x95, 0x55, 0xD5, 0x35, 0xB5, 0x75, 0xF5,
        0x0D, 0x8D, 0x4D, 0xCD, 0x2D, 0xAD, 0x6D, 0xED,
        0x1D, 0x9D, 0x5D, 0xDD, 0x3D, 0xBD, 0x7D, 0xFD,
        0x03, 0x83, 0x43, 0xC3, 0x23, 0xA3, 0x63, 0xE3,
        0x13, 0x93, 0x53, 0xD3, 0x33, 0xB3, 0x73, 0xF3,
        0x0B, 0x8B, 0x4B, 0xCB, 0x2B, 0xAB, 0x6B, 0xEB,
        0x1B, 0x9B, 0x5B, 0xDB, 0x3B, 0xBB, 0x7B, 0xFB,
        0x07, 0x87, 0x47, 0xC7, 0x27, 0xA7, 0x67, 0xE7,
        0x17, 0x97, 0x57, 0xD7, 0x37, 0xB7, 0x77, 0xF7,
        0x0F, 0x8F, 0x4F, 0xCF, 0x2F, 0xAF, 0x6F, 0xEF,
        0x1F, 0x9F, 0x5F, 0xDF, 0x3F, 0xBF, 0x7F, 0xFF
    );

    /**
     * Pre-permuted S-box1
     *
     * Each box ($sbox1-$sbox8) has been vectorized, then each value pre-permuted using the
     * P table: concatenation can then be replaced by exclusive ORs.
     *
     * @var Array
     * @access private
     */
    var $sbox1 = array(
        0x00808200, 0x00000000, 0x00008000, 0x00808202,
        0x00808002, 0x00008202, 0x00000002, 0x00008000,
        0x00000200, 0x00808200, 0x00808202, 0x00000200,
        0x00800202, 0x00808002, 0x00800000, 0x00000002,
        0x00000202, 0x00800200, 0x00800200, 0x00008200,
        0x00008200, 0x00808000, 0x00808000, 0x00800202,
        0x00008002, 0x00800002, 0x00800002, 0x00008002,
        0x00000000, 0x00000202, 0x00008202, 0x00800000,
        0x00008000, 0x00808202, 0x00000002, 0x00808000,
        0x00808200, 0x00800000, 0x00800000, 0x00000200,
        0x00808002, 0x00008000, 0x00008200, 0x00800002,
        0x00000200, 0x00000002, 0x00800202, 0x00008202,
        0x00808202, 0x00008002, 0x00808000, 0x00800202,
        0x00800002, 0x00000202, 0x00008202, 0x00808200,
        0x00000202, 0x00800200, 0x00800200, 0x00000000,
        0x00008002, 0x00008200, 0x00000000, 0x00808002
    );

    /**
     * Pre-permuted S-box2
     *
     * @var Array
     * @access private
     */
    var $sbox2 = array(
        0x40084010, 0x40004000, 0x00004000, 0x00084010,
        0x00080000, 0x00000010, 0x40080010, 0x40004010,
        0x40000010, 0x40084010, 0x40084000, 0x40000000,
        0x40004000, 0x00080000, 0x00000010, 0x40080010,
        0x00084000, 0x00080010, 0x40004010, 0x00000000,
        0x40000000, 0x00004000, 0x00084010, 0x40080000,
        0x00080010, 0x40000010, 0x00000000, 0x00084000,
        0x00004010, 0x40084000, 0x40080000, 0x00004010,
        0x00000000, 0x00084010, 0x40080010, 0x00080000,
        0x40004010, 0x40080000, 0x40084000, 0x00004000,
        0x40080000, 0x40004000, 0x00000010, 0x40084010,
        0x00084010, 0x00000010, 0x00004000, 0x40000000,
        0x00004010, 0x40084000, 0x00080000, 0x40000010,
        0x00080010, 0x40004010, 0x40000010, 0x00080010,
        0x00084000, 0x00000000, 0x40004000, 0x00004010,
        0x40000000, 0x40080010, 0x40084010, 0x00084000
    );

    /**
     * Pre-permuted S-box3
     *
     * @var Array
     * @access private
     */
    var $sbox3 = array(
        0x00000104, 0x04010100, 0x00000000, 0x04010004,
        0x04000100, 0x00000000, 0x00010104, 0x04000100,
        0x00010004, 0x04000004, 0x04000004, 0x00010000,
        0x04010104, 0x00010004, 0x04010000, 0x00000104,
        0x04000000, 0x00000004, 0x04010100, 0x00000100,
        0x00010100, 0x04010000, 0x04010004, 0x00010104,
        0x04000104, 0x00010100, 0x00010000, 0x04000104,
        0x00000004, 0x04010104, 0x00000100, 0x04000000,
        0x04010100, 0x04000000, 0x00010004, 0x00000104,
        0x00010000, 0x04010100, 0x04000100, 0x00000000,
        0x00000100, 0x00010004, 0x04010104, 0x04000100,
        0x04000004, 0x00000100, 0x00000000, 0x04010004,
        0x04000104, 0x00010000, 0x04000000, 0x04010104,
        0x00000004, 0x00010104, 0x00010100, 0x04000004,
        0x04010000, 0x04000104, 0x00000104, 0x04010000,
        0x00010104, 0x00000004, 0x04010004, 0x00010100
    );

    /**
     * Pre-permuted S-box4
     *
     * @var Array
     * @access private
     */
    var $sbox4 = array(
        0x80401000, 0x80001040, 0x80001040, 0x00000040,
        0x00401040, 0x80400040, 0x80400000, 0x80001000,
        0x00000000, 0x00401000, 0x00401000, 0x80401040,
        0x80000040, 0x00000000, 0x00400040, 0x80400000,
        0x80000000, 0x00001000, 0x00400000, 0x80401000,
        0x00000040, 0x00400000, 0x80001000, 0x00001040,
        0x80400040, 0x80000000, 0x00001040, 0x00400040,
        0x00001000, 0x00401040, 0x80401040, 0x80000040,
        0x00400040, 0x80400000, 0x00401000, 0x80401040,
        0x80000040, 0x00000000, 0x00000000, 0x00401000,
        0x00001040, 0x00400040, 0x80400040, 0x80000000,
        0x80401000, 0x80001040, 0x80001040, 0x00000040,
        0x80401040, 0x80000040, 0x80000000, 0x00001000,
        0x80400000, 0x80001000, 0x00401040, 0x80400040,
        0x80001000, 0x00001040, 0x00400000, 0x80401000,
        0x00000040, 0x00400000, 0x00001000, 0x00401040
    );

    /**
     * Pre-permuted S-box5
     *
     * @var Array
     * @access private
     */
    var $sbox5 = array(
        0x00000080, 0x01040080, 0x01040000, 0x21000080,
        0x00040000, 0x00000080, 0x20000000, 0x01040000,
        0x20040080, 0x00040000, 0x01000080, 0x20040080,
        0x21000080, 0x21040000, 0x00040080, 0x20000000,
        0x01000000, 0x20040000, 0x20040000, 0x00000000,
        0x20000080, 0x21040080, 0x21040080, 0x01000080,
        0x21040000, 0x20000080, 0x00000000, 0x21000000,
        0x01040080, 0x01000000, 0x21000000, 0x00040080,
        0x00040000, 0x21000080, 0x00000080, 0x01000000,
        0x20000000, 0x01040000, 0x21000080, 0x20040080,
        0x01000080, 0x20000000, 0x21040000, 0x01040080,
        0x20040080, 0x00000080, 0x01000000, 0x21040000,
        0x21040080, 0x00040080, 0x21000000, 0x21040080,
        0x01040000, 0x00000000, 0x20040000, 0x21000000,
        0x00040080, 0x01000080, 0x20000080, 0x00040000,
        0x00000000, 0x20040000, 0x01040080, 0x20000080
    );

    /**
     * Pre-permuted S-box6
     *
     * @var Array
     * @access private
     */
    var $sbox6 = array(
        0x10000008, 0x10200000, 0x00002000, 0x10202008,
        0x10200000, 0x00000008, 0x10202008, 0x00200000,
        0x10002000, 0x00202008, 0x00200000, 0x10000008,
        0x00200008, 0x10002000, 0x10000000, 0x00002008,
        0x00000000, 0x00200008, 0x10002008, 0x00002000,
        0x00202000, 0x10002008, 0x00000008, 0x10200008,
        0x10200008, 0x00000000, 0x00202008, 0x10202000,
        0x00002008, 0x00202000, 0x10202000, 0x10000000,
        0x10002000, 0x00000008, 0x10200008, 0x00202000,
        0x10202008, 0x00200000, 0x00002008, 0x10000008,
        0x00200000, 0x10002000, 0x10000000, 0x00002008,
        0x10000008, 0x10202008, 0x00202000, 0x10200000,
        0x00202008, 0x10202000, 0x00000000, 0x10200008,
        0x00000008, 0x00002000, 0x10200000, 0x00202008,
        0x00002000, 0x00200008, 0x10002008, 0x00000000,
        0x10202000, 0x10000000, 0x00200008, 0x10002008
    );

    /**
     * Pre-permuted S-box7
     *
     * @var Array
     * @access private
     */
    var $sbox7 = array(
        0x00100000, 0x02100001, 0x02000401, 0x00000000,
        0x00000400, 0x02000401, 0x00100401, 0x02100400,
        0x02100401, 0x00100000, 0x00000000, 0x02000001,
        0x00000001, 0x02000000, 0x02100001, 0x00000401,
        0x02000400, 0x00100401, 0x00100001, 0x02000400,
        0x02000001, 0x02100000, 0x02100400, 0x00100001,
        0x02100000, 0x00000400, 0x00000401, 0x02100401,
        0x00100400, 0x00000001, 0x02000000, 0x00100400,
        0x02000000, 0x00100400, 0x00100000, 0x02000401,
        0x02000401, 0x02100001, 0x02100001, 0x00000001,
        0x00100001, 0x02000000, 0x02000400, 0x00100000,
        0x02100400, 0x00000401, 0x00100401, 0x02100400,
        0x00000401, 0x02000001, 0x02100401, 0x02100000,
        0x00100400, 0x00000000, 0x00000001, 0x02100401,
        0x00000000, 0x00100401, 0x02100000, 0x00000400,
        0x02000001, 0x02000400, 0x00000400, 0x00100001
    );

    /**
     * Pre-permuted S-box8
     *
     * @var Array
     * @access private
     */
    var $sbox8 = array(
        0x08000820, 0x00000800, 0x00020000, 0x08020820,
        0x08000000, 0x08000820, 0x00000020, 0x08000000,
        0x00020020, 0x08020000, 0x08020820, 0x00020800,
        0x08020800, 0x00020820, 0x00000800, 0x00000020,
        0x08020000, 0x08000020, 0x08000800, 0x00000820,
        0x00020800, 0x00020020, 0x08020020, 0x08020800,
        0x00000820, 0x00000000, 0x00000000, 0x08020020,
        0x08000020, 0x08000800, 0x00020820, 0x00020000,
        0x00020820, 0x00020000, 0x08020800, 0x00000800,
        0x00000020, 0x08020020, 0x00000800, 0x00020820,
        0x08000800, 0x00000020, 0x08000020, 0x08020000,
        0x08020020, 0x08000000, 0x00020000, 0x08000820,
        0x00000000, 0x08020820, 0x00020020, 0x08000020,
        0x08020000, 0x08000800, 0x08000820, 0x00000000,
        0x08020820, 0x00020800, 0x00020800, 0x00000820,
        0x00000820, 0x00020020, 0x08000000, 0x08020800
    );

    /**
     * Sets the key.
     *
     * Keys can be of any length.  DES, itself, uses 64-bit keys (eg. strlen($key) == 8), however, we
     * only use the first eight, if $key has more then eight characters in it, and pad $key with the
     * null byte if it is less then eight characters long.
     *
     * DES also requires that every eighth bit be a parity bit, however, we'll ignore that.
     *
     * If the key is not explicitly set, it'll be assumed to be all zero's.
     *
     * @see Crypt_Base::setKey()
     * @access public
     * @param String $key
     */
    function setKey($key)
    {
        // We check/cut here only up to max length of the key.
        // Key padding to the proper length will be done in _setupKey()
        if (strlen($key) > $this->key_size_max) {
            $key = substr($key, 0, $this->key_size_max);
        }

        // Sets the key
        parent::setKey($key);
    }

    /**
     * Encrypts a block
     *
     * @see Crypt_Base::_encryptBlock()
     * @see Crypt_Base::encrypt()
     * @see Crypt_DES::encrypt()
     * @access private
     * @param String $in
     * @return String
     */
    function _encryptBlock($in)
    {
        return $this->_processBlock($in, CRYPT_DES_ENCRYPT);
    }

    /**
     * Decrypts a block
     *
     * @see Crypt_Base::_decryptBlock()
     * @see Crypt_Base::decrypt()
     * @see Crypt_DES::decrypt()
     * @access private
     * @param String $in
     * @return String
     */
    function _decryptBlock($in)
    {
        return $this->_processBlock($in, CRYPT_DES_DECRYPT);
    }

    /**
     * Encrypts or decrypts a 64-bit block
     *
     * $mode should be either CRYPT_DES_ENCRYPT or CRYPT_DES_DECRYPT.  See
     * {@link http://en.wikipedia.org/wiki/Image:Feistel.png Feistel.png} to get a general
     * idea of what this function does.
     *
     * @see Crypt_DES::_encryptBlock()
     * @see Crypt_DES::_decryptBlock()
     * @access private
     * @param String $block
     * @param Integer $mode
     * @return String
     */
    function _processBlock($block, $mode)
    {
        static $sbox1, $sbox2, $sbox3, $sbox4, $sbox5, $sbox6, $sbox7, $sbox8, $shuffleip, $shuffleinvip;
        if (!$sbox1) {
            $sbox1 = array_map("intval", $this->sbox1);
            $sbox2 = array_map("intval", $this->sbox2);
            $sbox3 = array_map("intval", $this->sbox3);
            $sbox4 = array_map("intval", $this->sbox4);
            $sbox5 = array_map("intval", $this->sbox5);
            $sbox6 = array_map("intval", $this->sbox6);
            $sbox7 = array_map("intval", $this->sbox7);
            $sbox8 = array_map("intval", $this->sbox8);
            /* Merge $shuffle with $[inv]ipmap */
            for ($i = 0; $i < 256; ++$i) {
                $shuffleip[]    =  $this->shuffle[$this->ipmap[$i]];
                $shuffleinvip[] =  $this->shuffle[$this->invipmap[$i]];
            }
        }

        $keys  = $this->keys[$mode];
        $ki    = -1;

        // Do the initial IP permutation.
        $t = unpack('Nl/Nr', $block);
        list($l, $r) = array($t['l'], $t['r']);
        $block = ($shuffleip[ $r        & 0xFF] & "\x80\x80\x80\x80\x80\x80\x80\x80") |
                 ($shuffleip[($r >>  8) & 0xFF] & "\x40\x40\x40\x40\x40\x40\x40\x40") |
                 ($shuffleip[($r >> 16) & 0xFF] & "\x20\x20\x20\x20\x20\x20\x20\x20") |
                 ($shuffleip[($r >> 24) & 0xFF] & "\x10\x10\x10\x10\x10\x10\x10\x10") |
                 ($shuffleip[ $l        & 0xFF] & "\x08\x08\x08\x08\x08\x08\x08\x08") |
                 ($shuffleip[($l >>  8) & 0xFF] & "\x04\x04\x04\x04\x04\x04\x04\x04") |
                 ($shuffleip[($l >> 16) & 0xFF] & "\x02\x02\x02\x02\x02\x02\x02\x02") |
                 ($shuffleip[($l >> 24) & 0xFF] & "\x01\x01\x01\x01\x01\x01\x01\x01");

        // Extract L0 and R0.
        $t = unpack('Nl/Nr', $block);
        list($l, $r) = array($t['l'], $t['r']);

        for ($des_round = 0; $des_round < $this->des_rounds; ++$des_round) {
            // Perform the 16 steps.
            for ($i = 0; $i < 16; $i++) {
                // start of "the Feistel (F) function" - see the following URL:
                // http://en.wikipedia.org/wiki/Image:Data_Encryption_Standard_InfoBox_Diagram.png
                // Merge key schedule.
                $b1 = (($r >>  3) & 0x1FFFFFFF) ^ ($r << 29) ^ $keys[++$ki];
                $b2 = (($r >> 31) & 0x00000001) ^ ($r <<  1) ^ $keys[++$ki];

                // S-box indexing.
                $t = $sbox1[($b1 >> 24) & 0x3F] ^ $sbox2[($b2 >> 24) & 0x3F] ^
                     $sbox3[($b1 >> 16) & 0x3F] ^ $sbox4[($b2 >> 16) & 0x3F] ^
                     $sbox5[($b1 >>  8) & 0x3F] ^ $sbox6[($b2 >>  8) & 0x3F] ^
                     $sbox7[ $b1        & 0x3F] ^ $sbox8[ $b2        & 0x3F] ^ $l;
                // end of "the Feistel (F) function"

                $l = $r;
                $r = $t;
            }

            // Last step should not permute L & R.
            $t = $l;
            $l = $r;
            $r = $t;
        }

        // Perform the inverse IP permutation.
        return ($shuffleinvip[($r >> 24) & 0xFF] & "\x80\x80\x80\x80\x80\x80\x80\x80") |
               ($shuffleinvip[($l >> 24) & 0xFF] & "\x40\x40\x40\x40\x40\x40\x40\x40") |
               ($shuffleinvip[($r >> 16) & 0xFF] & "\x20\x20\x20\x20\x20\x20\x20\x20") |
               ($shuffleinvip[($l >> 16) & 0xFF] & "\x10\x10\x10\x10\x10\x10\x10\x10") |
               ($shuffleinvip[($r >>  8) & 0xFF] & "\x08\x08\x08\x08\x08\x08\x08\x08") |
               ($shuffleinvip[($l >>  8) & 0xFF] & "\x04\x04\x04\x04\x04\x04\x04\x04") |
               ($shuffleinvip[ $r        & 0xFF] & "\x02\x02\x02\x02\x02\x02\x02\x02") |
               ($shuffleinvip[ $l        & 0xFF] & "\x01\x01\x01\x01\x01\x01\x01\x01");
    }

    /**
     * Creates the key schedule
     *
     * @see Crypt_Base::_setupKey()
     * @access private
     */
    function _setupKey()
    {
        if (isset($this->kl['key']) && $this->key === $this->kl['key'] && $this->des_rounds === $this->kl['des_rounds']) {
            // already expanded
            return;
        }
        $this->kl = array('key' => $this->key, 'des_rounds' => $this->des_rounds);

        static $shifts = array( // number of key bits shifted per round
            1, 1, 2, 2, 2, 2, 2, 2, 1, 2, 2, 2, 2, 2, 2, 1
        );

        static $pc1map = array(
            0x00, 0x00, 0x08, 0x08, 0x04, 0x04, 0x0C, 0x0C,
            0x02, 0x02, 0x0A, 0x0A, 0x06, 0x06, 0x0E, 0x0E,
            0x10, 0x10, 0x18, 0x18, 0x14, 0x14, 0x1C, 0x1C,
            0x12, 0x12, 0x1A, 0x1A, 0x16, 0x16, 0x1E, 0x1E,
            0x20, 0x20, 0x28, 0x28, 0x24, 0x24, 0x2C, 0x2C,
            0x22, 0x22, 0x2A, 0x2A, 0x26, 0x26, 0x2E, 0x2E,
            0x30, 0x30, 0x38, 0x38, 0x34, 0x34, 0x3C, 0x3C,
            0x32, 0x32, 0x3A, 0x3A, 0x36, 0x36, 0x3E, 0x3E,
            0x40, 0x40, 0x48, 0x48, 0x44, 0x44, 0x4C, 0x4C,
            0x42, 0x42, 0x4A, 0x4A, 0x46, 0x46, 0x4E, 0x4E,
            0x50, 0x50, 0x58, 0x58, 0x54, 0x54, 0x5C, 0x5C,
            0x52, 0x52, 0x5A, 0x5A, 0x56, 0x56, 0x5E, 0x5E,
            0x60, 0x60, 0x68, 0x68, 0x64, 0x64, 0x6C, 0x6C,
            0x62, 0x62, 0x6A, 0x6A, 0x66, 0x66, 0x6E, 0x6E,
            0x70, 0x70, 0x78, 0x78, 0x74, 0x74, 0x7C, 0x7C,
            0x72, 0x72, 0x7A, 0x7A, 0x76, 0x76, 0x7E, 0x7E,
            0x80, 0x80, 0x88, 0x88, 0x84, 0x84, 0x8C, 0x8C,
            0x82, 0x82, 0x8A, 0x8A, 0x86, 0x86, 0x8E, 0x8E,
            0x90, 0x90, 0x98, 0x98, 0x94, 0x94, 0x9C, 0x9C,
            0x92, 0x92, 0x9A, 0x9A, 0x96, 0x96, 0x9E, 0x9E,
            0xA0, 0xA0, 0xA8, 0xA8, 0xA4, 0xA4, 0xAC, 0xAC,
            0xA2, 0xA2, 0xAA, 0xAA, 0xA6, 0xA6, 0xAE, 0xAE,
            0xB0, 0xB0, 0xB8, 0xB8, 0xB4, 0xB4, 0xBC, 0xBC,
            0xB2, 0xB2, 0xBA, 0xBA, 0xB6, 0xB6, 0xBE, 0xBE,
            0xC0, 0xC0, 0xC8, 0xC8, 0xC4, 0xC4, 0xCC, 0xCC,
            0xC2, 0xC2, 0xCA, 0xCA, 0xC6, 0xC6, 0xCE, 0xCE,
            0xD0, 0xD0, 0xD8, 0xD8, 0xD4, 0xD4, 0xDC, 0xDC,
            0xD2, 0xD2, 0xDA, 0xDA, 0xD6, 0xD6, 0xDE, 0xDE,
            0xE0, 0xE0, 0xE8, 0xE8, 0xE4, 0xE4, 0xEC, 0xEC,
            0xE2, 0xE2, 0xEA, 0xEA, 0xE6, 0xE6, 0xEE, 0xEE,
            0xF0, 0xF0, 0xF8, 0xF8, 0xF4, 0xF4, 0xFC, 0xFC,
            0xF2, 0xF2, 0xFA, 0xFA, 0xF6, 0xF6, 0xFE, 0xFE
        );

        // Mapping tables for the PC-2 transformation.
        static $pc2mapc1 = array(
            0x00000000, 0x00000400, 0x00200000, 0x00200400,
            0x00000001, 0x00000401, 0x00200001, 0x00200401,
            0x02000000, 0x02000400, 0x02200000, 0x02200400,
            0x02000001, 0x02000401, 0x02200001, 0x02200401
        );
        static $pc2mapc2 = array(
            0x00000000, 0x00000800, 0x08000000, 0x08000800,
            0x00010000, 0x00010800, 0x08010000, 0x08010800,
            0x00000000, 0x00000800, 0x08000000, 0x08000800,
            0x00010000, 0x00010800, 0x08010000, 0x08010800,
            0x00000100, 0x00000900, 0x08000100, 0x08000900,
            0x00010100, 0x00010900, 0x08010100, 0x08010900,
            0x00000100, 0x00000900, 0x08000100, 0x08000900,
            0x00010100, 0x00010900, 0x08010100, 0x08010900,
            0x00000010, 0x00000810, 0x08000010, 0x08000810,
            0x00010010, 0x00010810, 0x08010010, 0x08010810,
            0x00000010, 0x00000810, 0x08000010, 0x08000810,
            0x00010010, 0x00010810, 0x08010010, 0x08010810,
            0x00000110, 0x00000910, 0x08000110, 0x08000910,
            0x00010110, 0x00010910, 0x08010110, 0x08010910,
            0x00000110, 0x00000910, 0x08000110, 0x08000910,
            0x00010110, 0x00010910, 0x08010110, 0x08010910,
            0x00040000, 0x00040800, 0x08040000, 0x08040800,
            0x00050000, 0x00050800, 0x08050000, 0x08050800,
            0x00040000, 0x00040800, 0x08040000, 0x08040800,
            0x00050000, 0x00050800, 0x08050000, 0x08050800,
            0x00040100, 0x00040900, 0x08040100, 0x08040900,
            0x00050100, 0x00050900, 0x08050100, 0x08050900,
            0x00040100, 0x00040900, 0x08040100, 0x08040900,
            0x00050100, 0x00050900, 0x08050100, 0x08050900,
            0x00040010, 0x00040810, 0x08040010, 0x08040810,
            0x00050010, 0x00050810, 0x08050010, 0x08050810,
            0x00040010, 0x00040810, 0x08040010, 0x08040810,
            0x00050010, 0x00050810, 0x08050010, 0x08050810,
            0x00040110, 0x00040910, 0x08040110, 0x08040910,
            0x00050110, 0x00050910, 0x08050110, 0x08050910,
            0x00040110, 0x00040910, 0x08040110, 0x08040910,
            0x00050110, 0x00050910, 0x08050110, 0x08050910,
            0x01000000, 0x01000800, 0x09000000, 0x09000800,
            0x01010000, 0x01010800, 0x09010000, 0x09010800,
            0x01000000, 0x01000800, 0x09000000, 0x09000800,
            0x01010000, 0x01010800, 0x09010000, 0x09010800,
            0x01000100, 0x01000900, 0x09000100, 0x09000900,
            0x01010100, 0x01010900, 0x09010100, 0x09010900,
            0x01000100, 0x01000900, 0x09000100, 0x09000900,
            0x01010100, 0x01010900, 0x09010100, 0x09010900,
            0x01000010, 0x01000810, 0x09000010, 0x09000810,
            0x01010010, 0x01010810, 0x09010010, 0x09010810,
            0x01000010, 0x01000810, 0x09000010, 0x09000810,
            0x01010010, 0x01010810, 0x09010010, 0x09010810,
            0x01000110, 0x01000910, 0x09000110, 0x09000910,
            0x01010110, 0x01010910, 0x09010110, 0x09010910,
            0x01000110, 0x01000910, 0x09000110, 0x09000910,
            0x01010110, 0x01010910, 0x09010110, 0x09010910,
            0x01040000, 0x01040800, 0x09040000, 0x09040800,
            0x01050000, 0x01050800, 0x09050000, 0x09050800,
            0x01040000, 0x01040800, 0x09040000, 0x09040800,
            0x01050000, 0x01050800, 0x09050000, 0x09050800,
            0x01040100, 0x01040900, 0x09040100, 0x09040900,
            0x01050100, 0x01050900, 0x09050100, 0x09050900,
            0x01040100, 0x01040900, 0x09040100, 0x09040900,
            0x01050100, 0x01050900, 0x09050100, 0x09050900,
            0x01040010, 0x01040810, 0x09040010, 0x09040810,
            0x01050010, 0x01050810, 0x09050010, 0x09050810,
            0x01040010, 0x01040810, 0x09040010, 0x09040810,
            0x01050010, 0x01050810, 0x09050010, 0x09050810,
            0x01040110, 0x01040910, 0x09040110, 0x09040910,
            0x01050110, 0x01050910, 0x09050110, 0x09050910,
            0x01040110, 0x01040910, 0x09040110, 0x09040910,
            0x01050110, 0x01050910, 0x09050110, 0x09050910
        );
        static $pc2mapc3 = array(
            0x00000000, 0x00000004, 0x00001000, 0x00001004,
            0x00000000, 0x00000004, 0x00001000, 0x00001004,
            0x10000000, 0x10000004, 0x10001000, 0x10001004,
            0x10000000, 0x10000004, 0x10001000, 0x10001004,
            0x00000020, 0x00000024, 0x00001020, 0x00001024,
            0x00000020, 0x00000024, 0x00001020, 0x00001024,
            0x10000020, 0x10000024, 0x10001020, 0x10001024,
            0x10000020, 0x10000024, 0x10001020, 0x10001024,
            0x00080000, 0x00080004, 0x00081000, 0x00081004,
            0x00080000, 0x00080004, 0x00081000, 0x00081004,
            0x10080000, 0x10080004, 0x10081000, 0x10081004,
            0x10080000, 0x10080004, 0x10081000, 0x10081004,
            0x00080020, 0x00080024, 0x00081020, 0x00081024,
            0x00080020, 0x00080024, 0x00081020, 0x00081024,
            0x10080020, 0x10080024, 0x10081020, 0x10081024,
            0x10080020, 0x10080024, 0x10081020, 0x10081024,
            0x20000000, 0x20000004, 0x20001000, 0x20001004,
            0x20000000, 0x20000004, 0x20001000, 0x20001004,
            0x30000000, 0x30000004, 0x30001000, 0x30001004,
            0x30000000, 0x30000004, 0x30001000, 0x30001004,
            0x20000020, 0x20000024, 0x20001020, 0x20001024,
            0x20000020, 0x20000024, 0x20001020, 0x20001024,
            0x30000020, 0x30000024, 0x30001020, 0x30001024,
            0x30000020, 0x30000024, 0x30001020, 0x30001024,
            0x20080000, 0x20080004, 0x20081000, 0x20081004,
            0x20080000, 0x20080004, 0x20081000, 0x20081004,
            0x30080000, 0x30080004, 0x30081000, 0x30081004,
            0x30080000, 0x30080004, 0x30081000, 0x30081004,
            0x20080020, 0x20080024, 0x20081020, 0x20081024,
            0x20080020, 0x20080024, 0x20081020, 0x20081024,
            0x30080020, 0x30080024, 0x30081020, 0x30081024,
            0x30080020, 0x30080024, 0x30081020, 0x30081024,
            0x00000002, 0x00000006, 0x00001002, 0x00001006,
            0x00000002, 0x00000006, 0x00001002, 0x00001006,
            0x10000002, 0x10000006, 0x10001002, 0x10001006,
            0x10000002, 0x10000006, 0x10001002, 0x10001006,
            0x00000022, 0x00000026, 0x00001022, 0x00001026,
            0x00000022, 0x00000026, 0x00001022, 0x00001026,
            0x10000022, 0x10000026, 0x10001022, 0x10001026,
            0x10000022, 0x10000026, 0x10001022, 0x10001026,
            0x00080002, 0x00080006, 0x00081002, 0x00081006,
            0x00080002, 0x00080006, 0x00081002, 0x00081006,
            0x10080002, 0x10080006, 0x10081002, 0x10081006,
            0x10080002, 0x10080006, 0x10081002, 0x10081006,
            0x00080022, 0x00080026, 0x00081022, 0x00081026,
            0x00080022, 0x00080026, 0x00081022, 0x00081026,
            0x10080022, 0x10080026, 0x10081022, 0x10081026,
            0x10080022, 0x10080026, 0x10081022, 0x10081026,
            0x20000002, 0x20000006, 0x20001002, 0x20001006,
            0x20000002, 0x20000006, 0x20001002, 0x20001006,
            0x30000002, 0x30000006, 0x30001002, 0x30001006,
            0x30000002, 0x30000006, 0x30001002, 0x30001006,
            0x20000022, 0x20000026, 0x20001022, 0x20001026,
            0x20000022, 0x20000026, 0x20001022, 0x20001026,
            0x30000022, 0x30000026, 0x30001022, 0x30001026,
            0x30000022, 0x30000026, 0x30001022, 0x30001026,
            0x20080002, 0x20080006, 0x20081002, 0x20081006,
            0x20080002, 0x20080006, 0x20081002, 0x20081006,
            0x30080002, 0x30080006, 0x30081002, 0x30081006,
            0x30080002, 0x30080006, 0x30081002, 0x30081006,
            0x20080022, 0x20080026, 0x20081022, 0x20081026,
            0x20080022, 0x20080026, 0x20081022, 0x20081026,
            0x30080022, 0x30080026, 0x30081022, 0x30081026,
            0x30080022, 0x30080026, 0x30081022, 0x30081026
        );
        static $pc2mapc4 = array(
            0x00000000, 0x00100000, 0x00000008, 0x00100008,
            0x00000200, 0x00100200, 0x00000208, 0x00100208,
            0x00000000, 0x00100000, 0x00000008, 0x00100008,
            0x00000200, 0x00100200, 0x00000208, 0x00100208,
            0x04000000, 0x04100000, 0x04000008, 0x04100008,
            0x04000200, 0x04100200, 0x04000208, 0x04100208,
            0x04000000, 0x04100000, 0x04000008, 0x04100008,
            0x04000200, 0x04100200, 0x04000208, 0x04100208,
            0x00002000, 0x00102000, 0x00002008, 0x00102008,
            0x00002200, 0x00102200, 0x00002208, 0x00102208,
            0x00002000, 0x00102000, 0x00002008, 0x00102008,
            0x00002200, 0x00102200, 0x00002208, 0x00102208,
            0x04002000, 0x04102000, 0x04002008, 0x04102008,
            0x04002200, 0x04102200, 0x04002208, 0x04102208,
            0x04002000, 0x04102000, 0x04002008, 0x04102008,
            0x04002200, 0x04102200, 0x04002208, 0x04102208,
            0x00000000, 0x00100000, 0x00000008, 0x00100008,
            0x00000200, 0x00100200, 0x00000208, 0x00100208,
            0x00000000, 0x00100000, 0x00000008, 0x00100008,
            0x00000200, 0x00100200, 0x00000208, 0x00100208,
            0x04000000, 0x04100000, 0x04000008, 0x04100008,
            0x04000200, 0x04100200, 0x04000208, 0x04100208,
            0x04000000, 0x04100000, 0x04000008, 0x04100008,
            0x04000200, 0x04100200, 0x04000208, 0x04100208,
            0x00002000, 0x00102000, 0x00002008, 0x00102008,
            0x00002200, 0x00102200, 0x00002208, 0x00102208,
            0x00002000, 0x00102000, 0x00002008, 0x00102008,
            0x00002200, 0x00102200, 0x00002208, 0x00102208,
            0x04002000, 0x04102000, 0x04002008, 0x04102008,
            0x04002200, 0x04102200, 0x04002208, 0x04102208,
            0x04002000, 0x04102000, 0x04002008, 0x04102008,
            0x04002200, 0x04102200, 0x04002208, 0x04102208,
            0x00020000, 0x00120000, 0x00020008, 0x00120008,
            0x00020200, 0x00120200, 0x00020208, 0x00120208,
            0x00020000, 0x00120000, 0x00020008, 0x00120008,
            0x00020200, 0x00120200, 0x00020208, 0x00120208,
            0x04020000, 0x04120000, 0x04020008, 0x04120008,
            0x04020200, 0x04120200, 0x04020208, 0x04120208,
            0x04020000, 0x04120000, 0x04020008, 0x04120008,
            0x04020200, 0x04120200, 0x04020208, 0x04120208,
            0x00022000, 0x00122000, 0x00022008, 0x00122008,
            0x00022200, 0x00122200, 0x00022208, 0x00122208,
            0x00022000, 0x00122000, 0x00022008, 0x00122008,
            0x00022200, 0x00122200, 0x00022208, 0x00122208,
            0x04022000, 0x04122000, 0x04022008, 0x04122008,
            0x04022200, 0x04122200, 0x04022208, 0x04122208,
            0x04022000, 0x04122000, 0x04022008, 0x04122008,
            0x04022200, 0x04122200, 0x04022208, 0x04122208,
            0x00020000, 0x00120000, 0x00020008, 0x00120008,
            0x00020200, 0x00120200, 0x00020208, 0x00120208,
            0x00020000, 0x00120000, 0x00020008, 0x00120008,
            0x00020200, 0x00120200, 0x00020208, 0x00120208,
            0x04020000, 0x04120000, 0x04020008, 0x04120008,
            0x04020200, 0x04120200, 0x04020208, 0x04120208,
            0x04020000, 0x04120000, 0x04020008, 0x04120008,
            0x04020200, 0x04120200, 0x04020208, 0x04120208,
            0x00022000, 0x00122000, 0x00022008, 0x00122008,
            0x00022200, 0x00122200, 0x00022208, 0x00122208,
            0x00022000, 0x00122000, 0x00022008, 0x00122008,
            0x00022200, 0x00122200, 0x00022208, 0x00122208,
            0x04022000, 0x04122000, 0x04022008, 0x04122008,
            0x04022200, 0x04122200, 0x04022208, 0x04122208,
            0x04022000, 0x04122000, 0x04022008, 0x04122008,
            0x04022200, 0x04122200, 0x04022208, 0x04122208
        );
        static $pc2mapd1 = array(
            0x00000000, 0x00000001, 0x08000000, 0x08000001,
            0x00200000, 0x00200001, 0x08200000, 0x08200001,
            0x00000002, 0x00000003, 0x08000002, 0x08000003,
            0x00200002, 0x00200003, 0x08200002, 0x08200003
        );
        static $pc2mapd2 = array(
            0x00000000, 0x00100000, 0x00000800, 0x00100800,
            0x00000000, 0x00100000, 0x00000800, 0x00100800,
            0x04000000, 0x04100000, 0x04000800, 0x04100800,
            0x04000000, 0x04100000, 0x04000800, 0x04100800,
            0x00000004, 0x00100004, 0x00000804, 0x00100804,
            0x00000004, 0x00100004, 0x00000804, 0x00100804,
            0x04000004, 0x04100004, 0x04000804, 0x04100804,
            0x04000004, 0x04100004, 0x04000804, 0x04100804,
            0x00000000, 0x00100000, 0x00000800, 0x00100800,
            0x00000000, 0x00100000, 0x00000800, 0x00100800,
            0x04000000, 0x04100000, 0x04000800, 0x04100800,
            0x04000000, 0x04100000, 0x04000800, 0x04100800,
            0x00000004, 0x00100004, 0x00000804, 0x00100804,
            0x00000004, 0x00100004, 0x00000804, 0x00100804,
            0x04000004, 0x04100004, 0x04000804, 0x04100804,
            0x04000004, 0x04100004, 0x04000804, 0x04100804,
            0x00000200, 0x00100200, 0x00000A00, 0x00100A00,
            0x00000200, 0x00100200, 0x00000A00, 0x00100A00,
            0x04000200, 0x04100200, 0x04000A00, 0x04100A00,
            0x04000200, 0x04100200, 0x04000A00, 0x04100A00,
            0x00000204, 0x00100204, 0x00000A04, 0x00100A04,
            0x00000204, 0x00100204, 0x00000A04, 0x00100A04,
            0x04000204, 0x04100204, 0x04000A04, 0x04100A04,
            0x04000204, 0x04100204, 0x04000A04, 0x04100A04,
            0x00000200, 0x00100200, 0x00000A00, 0x00100A00,
            0x00000200, 0x00100200, 0x00000A00, 0x00100A00,
            0x04000200, 0x04100200, 0x04000A00, 0x04100A00,
            0x04000200, 0x04100200, 0x04000A00, 0x04100A00,
            0x00000204, 0x00100204, 0x00000A04, 0x00100A04,
            0x00000204, 0x00100204, 0x00000A04, 0x00100A04,
            0x04000204, 0x04100204, 0x04000A04, 0x04100A04,
            0x04000204, 0x04100204, 0x04000A04, 0x04100A04,
            0x00020000, 0x00120000, 0x00020800, 0x00120800,
            0x00020000, 0x00120000, 0x00020800, 0x00120800,
            0x04020000, 0x04120000, 0x04020800, 0x04120800,
            0x04020000, 0x04120000, 0x04020800, 0x04120800,
            0x00020004, 0x00120004, 0x00020804, 0x00120804,
            0x00020004, 0x00120004, 0x00020804, 0x00120804,
            0x04020004, 0x04120004, 0x04020804, 0x04120804,
            0x04020004, 0x04120004, 0x04020804, 0x04120804,
            0x00020000, 0x00120000, 0x00020800, 0x00120800,
            0x00020000, 0x00120000, 0x00020800, 0x00120800,
            0x04020000, 0x04120000, 0x04020800, 0x04120800,
            0x04020000, 0x04120000, 0x04020800, 0x04120800,
            0x00020004, 0x00120004, 0x00020804, 0x00120804,
            0x00020004, 0x00120004, 0x00020804, 0x00120804,
            0x04020004, 0x04120004, 0x04020804, 0x04120804,
            0x04020004, 0x04120004, 0x04020804, 0x04120804,
            0x00020200, 0x00120200, 0x00020A00, 0x00120A00,
            0x00020200, 0x00120200, 0x00020A00, 0x00120A00,
            0x04020200, 0x04120200, 0x04020A00, 0x04120A00,
            0x04020200, 0x04120200, 0x04020A00, 0x04120A00,
            0x00020204, 0x00120204, 0x00020A04, 0x00120A04,
            0x00020204, 0x00120204, 0x00020A04, 0x00120A04,
            0x04020204, 0x04120204, 0x04020A04, 0x04120A04,
            0x04020204, 0x04120204, 0x04020A04, 0x04120A04,
            0x00020200, 0x00120200, 0x00020A00, 0x00120A00,
            0x00020200, 0x00120200, 0x00020A00, 0x00120A00,
            0x04020200, 0x04120200, 0x04020A00, 0x04120A00,
            0x04020200, 0x04120200, 0x04020A00, 0x04120A00,
            0x00020204, 0x00120204, 0x00020A04, 0x00120A04,
            0x00020204, 0x00120204, 0x00020A04, 0x00120A04,
            0x04020204, 0x04120204, 0x04020A04, 0x04120A04,
            0x04020204, 0x04120204, 0x04020A04, 0x04120A04
        );
        static $pc2mapd3 = array(
            0x00000000, 0x00010000, 0x02000000, 0x02010000,
            0x00000020, 0x00010020, 0x02000020, 0x02010020,
            0x00040000, 0x00050000, 0x02040000, 0x02050000,
            0x00040020, 0x00050020, 0x02040020, 0x02050020,
            0x00002000, 0x00012000, 0x02002000, 0x02012000,
            0x00002020, 0x00012020, 0x02002020, 0x02012020,
            0x00042000, 0x00052000, 0x02042000, 0x02052000,
            0x00042020, 0x00052020, 0x02042020, 0x02052020,
            0x00000000, 0x00010000, 0x02000000, 0x02010000,
            0x00000020, 0x00010020, 0x02000020, 0x02010020,
            0x00040000, 0x00050000, 0x02040000, 0x02050000,
            0x00040020, 0x00050020, 0x02040020, 0x02050020,
            0x00002000, 0x00012000, 0x02002000, 0x02012000,
            0x00002020, 0x00012020, 0x02002020, 0x02012020,
            0x00042000, 0x00052000, 0x02042000, 0x02052000,
            0x00042020, 0x00052020, 0x02042020, 0x02052020,
            0x00000010, 0x00010010, 0x02000010, 0x02010010,
            0x00000030, 0x00010030, 0x02000030, 0x02010030,
            0x00040010, 0x00050010, 0x02040010, 0x02050010,
            0x00040030, 0x00050030, 0x02040030, 0x02050030,
            0x00002010, 0x00012010, 0x02002010, 0x02012010,
            0x00002030, 0x00012030, 0x02002030, 0x02012030,
            0x00042010, 0x00052010, 0x02042010, 0x02052010,
            0x00042030, 0x00052030, 0x02042030, 0x02052030,
            0x00000010, 0x00010010, 0x02000010, 0x02010010,
            0x00000030, 0x00010030, 0x02000030, 0x02010030,
            0x00040010, 0x00050010, 0x02040010, 0x02050010,
            0x00040030, 0x00050030, 0x02040030, 0x02050030,
            0x00002010, 0x00012010, 0x02002010, 0x02012010,
            0x00002030, 0x00012030, 0x02002030, 0x02012030,
            0x00042010, 0x00052010, 0x02042010, 0x02052010,
            0x00042030, 0x00052030, 0x02042030, 0x02052030,
            0x20000000, 0x20010000, 0x22000000, 0x22010000,
            0x20000020, 0x20010020, 0x22000020, 0x22010020,
            0x20040000, 0x20050000, 0x22040000, 0x22050000,
            0x20040020, 0x20050020, 0x22040020, 0x22050020,
            0x20002000, 0x20012000, 0x22002000, 0x22012000,
            0x20002020, 0x20012020, 0x22002020, 0x22012020,
            0x20042000, 0x20052000, 0x22042000, 0x22052000,
            0x20042020, 0x20052020, 0x22042020, 0x22052020,
            0x20000000, 0x20010000, 0x22000000, 0x22010000,
            0x20000020, 0x20010020, 0x22000020, 0x22010020,
            0x20040000, 0x20050000, 0x22040000, 0x22050000,
            0x20040020, 0x20050020, 0x22040020, 0x22050020,
            0x20002000, 0x20012000, 0x22002000, 0x22012000,
            0x20002020, 0x20012020, 0x22002020, 0x22012020,
            0x20042000, 0x20052000, 0x22042000, 0x22052000,
            0x20042020, 0x20052020, 0x22042020, 0x22052020,
            0x20000010, 0x20010010, 0x22000010, 0x22010010,
            0x20000030, 0x20010030, 0x22000030, 0x22010030,
            0x20040010, 0x20050010, 0x22040010, 0x22050010,
            0x20040030, 0x20050030, 0x22040030, 0x22050030,
            0x20002010, 0x20012010, 0x22002010, 0x22012010,
            0x20002030, 0x20012030, 0x22002030, 0x22012030,
            0x20042010, 0x20052010, 0x22042010, 0x22052010,
            0x20042030, 0x20052030, 0x22042030, 0x22052030,
            0x20000010, 0x20010010, 0x22000010, 0x22010010,
            0x20000030, 0x20010030, 0x22000030, 0x22010030,
            0x20040010, 0x20050010, 0x22040010, 0x22050010,
            0x20040030, 0x20050030, 0x22040030, 0x22050030,
            0x20002010, 0x20012010, 0x22002010, 0x22012010,
            0x20002030, 0x20012030, 0x22002030, 0x22012030,
            0x20042010, 0x20052010, 0x22042010, 0x22052010,
            0x20042030, 0x20052030, 0x22042030, 0x22052030
        );
        static $pc2mapd4 = array(
            0x00000000, 0x00000400, 0x01000000, 0x01000400,
            0x00000000, 0x00000400, 0x01000000, 0x01000400,
            0x00000100, 0x00000500, 0x01000100, 0x01000500,
            0x00000100, 0x00000500, 0x01000100, 0x01000500,
            0x10000000, 0x10000400, 0x11000000, 0x11000400,
            0x10000000, 0x10000400, 0x11000000, 0x11000400,
            0x10000100, 0x10000500, 0x11000100, 0x11000500,
            0x10000100, 0x10000500, 0x11000100, 0x11000500,
            0x00080000, 0x00080400, 0x01080000, 0x01080400,
            0x00080000, 0x00080400, 0x01080000, 0x01080400,
            0x00080100, 0x00080500, 0x01080100, 0x01080500,
            0x00080100, 0x00080500, 0x01080100, 0x01080500,
            0x10080000, 0x10080400, 0x11080000, 0x11080400,
            0x10080000, 0x10080400, 0x11080000, 0x11080400,
            0x10080100, 0x10080500, 0x11080100, 0x11080500,
            0x10080100, 0x10080500, 0x11080100, 0x11080500,
            0x00000008, 0x00000408, 0x01000008, 0x01000408,
            0x00000008, 0x00000408, 0x01000008, 0x01000408,
            0x00000108, 0x00000508, 0x01000108, 0x01000508,
            0x00000108, 0x00000508, 0x01000108, 0x01000508,
            0x10000008, 0x10000408, 0x11000008, 0x11000408,
            0x10000008, 0x10000408, 0x11000008, 0x11000408,
            0x10000108, 0x10000508, 0x11000108, 0x11000508,
            0x10000108, 0x10000508, 0x11000108, 0x11000508,
            0x00080008, 0x00080408, 0x01080008, 0x01080408,
            0x00080008, 0x00080408, 0x01080008, 0x01080408,
            0x00080108, 0x00080508, 0x01080108, 0x01080508,
            0x00080108, 0x00080508, 0x01080108, 0x01080508,
            0x10080008, 0x10080408, 0x11080008, 0x11080408,
            0x10080008, 0x10080408, 0x11080008, 0x11080408,
            0x10080108, 0x10080508, 0x11080108, 0x11080508,
            0x10080108, 0x10080508, 0x11080108, 0x11080508,
            0x00001000, 0x00001400, 0x01001000, 0x01001400,
            0x00001000, 0x00001400, 0x01001000, 0x01001400,
            0x00001100, 0x00001500, 0x01001100, 0x01001500,
            0x00001100, 0x00001500, 0x01001100, 0x01001500,
            0x10001000, 0x10001400, 0x11001000, 0x11001400,
            0x10001000, 0x10001400, 0x11001000, 0x11001400,
            0x10001100, 0x10001500, 0x11001100, 0x11001500,
            0x10001100, 0x10001500, 0x11001100, 0x11001500,
            0x00081000, 0x00081400, 0x01081000, 0x01081400,
            0x00081000, 0x00081400, 0x01081000, 0x01081400,
            0x00081100, 0x00081500, 0x01081100, 0x01081500,
            0x00081100, 0x00081500, 0x01081100, 0x01081500,
            0x10081000, 0x10081400, 0x11081000, 0x11081400,
            0x10081000, 0x10081400, 0x11081000, 0x11081400,
            0x10081100, 0x10081500, 0x11081100, 0x11081500,
            0x10081100, 0x10081500, 0x11081100, 0x11081500,
            0x00001008, 0x00001408, 0x01001008, 0x01001408,
            0x00001008, 0x00001408, 0x01001008, 0x01001408,
            0x00001108, 0x00001508, 0x01001108, 0x01001508,
            0x00001108, 0x00001508, 0x01001108, 0x01001508,
            0x10001008, 0x10001408, 0x11001008, 0x11001408,
            0x10001008, 0x10001408, 0x11001008, 0x11001408,
            0x10001108, 0x10001508, 0x11001108, 0x11001508,
            0x10001108, 0x10001508, 0x11001108, 0x11001508,
            0x00081008, 0x00081408, 0x01081008, 0x01081408,
            0x00081008, 0x00081408, 0x01081008, 0x01081408,
            0x00081108, 0x00081508, 0x01081108, 0x01081508,
            0x00081108, 0x00081508, 0x01081108, 0x01081508,
            0x10081008, 0x10081408, 0x11081008, 0x11081408,
            0x10081008, 0x10081408, 0x11081008, 0x11081408,
            0x10081108, 0x10081508, 0x11081108, 0x11081508,
            0x10081108, 0x10081508, 0x11081108, 0x11081508
        );

        $keys = array();
        for ($des_round = 0; $des_round < $this->des_rounds; ++$des_round) {
            // pad the key and remove extra characters as appropriate.
            $key = str_pad(substr($this->key, $des_round * 8, 8), 8, "\0");

            // Perform the PC/1 transformation and compute C and D.
            $t = unpack('Nl/Nr', $key);
            list($l, $r) = array($t['l'], $t['r']);
            $key = ($this->shuffle[$pc1map[ $r        & 0xFF]] & "\x80\x80\x80\x80\x80\x80\x80\x00") |
                   ($this->shuffle[$pc1map[($r >>  8) & 0xFF]] & "\x40\x40\x40\x40\x40\x40\x40\x00") |
                   ($this->shuffle[$pc1map[($r >> 16) & 0xFF]] & "\x20\x20\x20\x20\x20\x20\x20\x00") |
                   ($this->shuffle[$pc1map[($r >> 24) & 0xFF]] & "\x10\x10\x10\x10\x10\x10\x10\x00") |
                   ($this->shuffle[$pc1map[ $l        & 0xFF]] & "\x08\x08\x08\x08\x08\x08\x08\x00") |
                   ($this->shuffle[$pc1map[($l >>  8) & 0xFF]] & "\x04\x04\x04\x04\x04\x04\x04\x00") |
                   ($this->shuffle[$pc1map[($l >> 16) & 0xFF]] & "\x02\x02\x02\x02\x02\x02\x02\x00") |
                   ($this->shuffle[$pc1map[($l >> 24) & 0xFF]] & "\x01\x01\x01\x01\x01\x01\x01\x00");
            $key = unpack('Nc/Nd', $key);
            $c = ( $key['c'] >> 4) & 0x0FFFFFFF;
            $d = (($key['d'] >> 4) & 0x0FFFFFF0) | ($key['c'] & 0x0F);

            $keys[$des_round] = array(
                CRYPT_DES_ENCRYPT => array(),
                CRYPT_DES_DECRYPT => array_fill(0, 32, 0)
            );
            for ($i = 0, $ki = 31; $i < 16; ++$i, $ki-= 2) {
                $c <<= $shifts[$i];
                $c = ($c | ($c >> 28)) & 0x0FFFFFFF;
                $d <<= $shifts[$i];
                $d = ($d | ($d >> 28)) & 0x0FFFFFFF;

                // Perform the PC-2 transformation.
                $cp = $pc2mapc1[ $c >> 24        ] | $pc2mapc2[($c >> 16) & 0xFF] |
                      $pc2mapc3[($c >>  8) & 0xFF] | $pc2mapc4[ $c        & 0xFF];
                $dp = $pc2mapd1[ $d >> 24        ] | $pc2mapd2[($d >> 16) & 0xFF] |
                      $pc2mapd3[($d >>  8) & 0xFF] | $pc2mapd4[ $d        & 0xFF];

                // Reorder: odd bytes/even bytes. Push the result in key schedule.
                $val1 = ( $cp        & 0xFF000000) | (($cp <<  8) & 0x00FF0000) |
                        (($dp >> 16) & 0x0000FF00) | (($dp >>  8) & 0x000000FF);
                $val2 = (($cp <<  8) & 0xFF000000) | (($cp << 16) & 0x00FF0000) |
                        (($dp >>  8) & 0x0000FF00) | ( $dp        & 0x000000FF);
                $keys[$des_round][CRYPT_DES_ENCRYPT][       ] = $val1;
                $keys[$des_round][CRYPT_DES_DECRYPT][$ki - 1] = $val1;
                $keys[$des_round][CRYPT_DES_ENCRYPT][       ] = $val2;
                $keys[$des_round][CRYPT_DES_DECRYPT][$ki    ] = $val2;
            }
        }

        switch ($this->des_rounds) {
            case 3: // 3DES keys
                $this->keys = array(
                    CRYPT_DES_ENCRYPT => array_merge(
                        $keys[0][CRYPT_DES_ENCRYPT],
                        $keys[1][CRYPT_DES_DECRYPT],
                        $keys[2][CRYPT_DES_ENCRYPT]
                    ),
                    CRYPT_DES_DECRYPT => array_merge(
                        $keys[2][CRYPT_DES_DECRYPT],
                        $keys[1][CRYPT_DES_ENCRYPT],
                        $keys[0][CRYPT_DES_DECRYPT]
                    )
                );
                break;
            // case 1: // DES keys
            default:
                $this->keys = array(
                    CRYPT_DES_ENCRYPT => $keys[0][CRYPT_DES_ENCRYPT],
                    CRYPT_DES_DECRYPT => $keys[0][CRYPT_DES_DECRYPT]
                );
        }
    }

    /**
     * Setup the performance-optimized function for de/encrypt()
     *
     * @see Crypt_Base::_setupInlineCrypt()
     * @access private
     */
    function _setupInlineCrypt()
    {
        $lambda_functions =& Crypt_DES::_getLambdaFunctions();

        // Engine configuration for:
        // -  DES ($des_rounds == 1) or
        // - 3DES ($des_rounds == 3)
        $des_rounds = $this->des_rounds;

        // We create max. 10 hi-optimized code for memory reason. Means: For each $key one ultra fast inline-crypt function.
        // After that, we'll still create very fast optimized code but not the hi-ultimative code, for each $mode one
        $gen_hi_opt_code = (bool)( count($lambda_functions) < 10 );

        // Generation of a uniqe hash for our generated code
        switch (true) {
            case $gen_hi_opt_code:
                // For hi-optimized code, we create for each combination of
                // $mode, $des_rounds and $this->key its own encrypt/decrypt function.
                $code_hash = md5(str_pad("Crypt_DES, $des_rounds, {$this->mode}, ", 32, "\0") . $this->key);
                break;
            default:
                // After max 10 hi-optimized functions, we create generic
                // (still very fast.. but not ultra) functions for each $mode/$des_rounds
                // Currently 2 * 5 generic functions will be then max. possible.
                $code_hash = "Crypt_DES, $des_rounds, {$this->mode}";
        }

        // Is there a re-usable $lambda_functions in there? If not, we have to create it.
        if (!isset($lambda_functions[$code_hash])) {
            // Init code for both, encrypt and decrypt.
            $init_crypt = 'static $sbox1, $sbox2, $sbox3, $sbox4, $sbox5, $sbox6, $sbox7, $sbox8, $shuffleip, $shuffleinvip;
                if (!$sbox1) {
                    $sbox1 = array_map("intval", $self->sbox1);
                    $sbox2 = array_map("intval", $self->sbox2);
                    $sbox3 = array_map("intval", $self->sbox3);
                    $sbox4 = array_map("intval", $self->sbox4);
                    $sbox5 = array_map("intval", $self->sbox5);
                    $sbox6 = array_map("intval", $self->sbox6);
                    $sbox7 = array_map("intval", $self->sbox7);
                    $sbox8 = array_map("intval", $self->sbox8);'
                    /* Merge $shuffle with $[inv]ipmap */ . '
                    for ($i = 0; $i < 256; ++$i) {
                        $shuffleip[]    =  $self->shuffle[$self->ipmap[$i]];
                        $shuffleinvip[] =  $self->shuffle[$self->invipmap[$i]];
                    }
                }
            ';

            switch (true) {
                case $gen_hi_opt_code:
                    // In Hi-optimized code mode, we use our [3]DES key schedule as hardcoded integers.
                    // No futher initialisation of the $keys schedule is necessary.
                    // That is the extra performance boost.
                    $k = array(
                        CRYPT_DES_ENCRYPT => $this->keys[CRYPT_DES_ENCRYPT],
                        CRYPT_DES_DECRYPT => $this->keys[CRYPT_DES_DECRYPT]
                    );
                    $init_encrypt = '';
                    $init_decrypt = '';
                    break;
                default:
                    // In generic optimized code mode, we have to use, as the best compromise [currently],
                    // our key schedule as $ke/$kd arrays. (with hardcoded indexes...)
                    $k = array(
                        CRYPT_DES_ENCRYPT => array(),
                        CRYPT_DES_DECRYPT => array()
                    );
                    for ($i = 0, $c = count($this->keys[CRYPT_DES_ENCRYPT]); $i < $c; ++$i) {
                        $k[CRYPT_DES_ENCRYPT][$i] = '$ke[' . $i . ']';
                        $k[CRYPT_DES_DECRYPT][$i] = '$kd[' . $i . ']';
                    }
                    $init_encrypt = '$ke = $self->keys[CRYPT_DES_ENCRYPT];';
                    $init_decrypt = '$kd = $self->keys[CRYPT_DES_DECRYPT];';
                    break;
            }

            // Creating code for en- and decryption.
            $crypt_block = array();
            foreach (array(CRYPT_DES_ENCRYPT, CRYPT_DES_DECRYPT) as $c) {

                /* Do the initial IP permutation. */
                $crypt_block[$c] = '
                    $in = unpack("N*", $in);
                    $l  = $in[1];
                    $r  = $in[2];
                    $in = unpack("N*",
                        ($shuffleip[ $r        & 0xFF] & "\x80\x80\x80\x80\x80\x80\x80\x80") |
                        ($shuffleip[($r >>  8) & 0xFF] & "\x40\x40\x40\x40\x40\x40\x40\x40") |
                        ($shuffleip[($r >> 16) & 0xFF] & "\x20\x20\x20\x20\x20\x20\x20\x20") |
                        ($shuffleip[($r >> 24) & 0xFF] & "\x10\x10\x10\x10\x10\x10\x10\x10") |
                        ($shuffleip[ $l        & 0xFF] & "\x08\x08\x08\x08\x08\x08\x08\x08") |
                        ($shuffleip[($l >>  8) & 0xFF] & "\x04\x04\x04\x04\x04\x04\x04\x04") |
                        ($shuffleip[($l >> 16) & 0xFF] & "\x02\x02\x02\x02\x02\x02\x02\x02") |
                        ($shuffleip[($l >> 24) & 0xFF] & "\x01\x01\x01\x01\x01\x01\x01\x01")
                    );
                    ' . /* Extract L0 and R0 */ '
                    $l = $in[1];
                    $r = $in[2];
                ';

                $l = '$l';
                $r = '$r';

                // Perform DES or 3DES.
                for ($ki = -1, $des_round = 0; $des_round < $des_rounds; ++$des_round) {
                    // Perform the 16 steps.
                    for ($i = 0; $i < 16; ++$i) {
                        // start of "the Feistel (F) function" - see the following URL:
                        // http://en.wikipedia.org/wiki/Image:Data_Encryption_Standard_InfoBox_Diagram.png
                        // Merge key schedule.
                        $crypt_block[$c].= '
                            $b1 = ((' . $r . ' >>  3) & 0x1FFFFFFF)  ^ (' . $r . ' << 29) ^ ' . $k[$c][++$ki] . ';
                            $b2 = ((' . $r . ' >> 31) & 0x00000001)  ^ (' . $r . ' <<  1) ^ ' . $k[$c][++$ki] . ';' .
                            /* S-box indexing. */
                            $l . ' = $sbox1[($b1 >> 24) & 0x3F] ^ $sbox2[($b2 >> 24) & 0x3F] ^
                                     $sbox3[($b1 >> 16) & 0x3F] ^ $sbox4[($b2 >> 16) & 0x3F] ^
                                     $sbox5[($b1 >>  8) & 0x3F] ^ $sbox6[($b2 >>  8) & 0x3F] ^
                                     $sbox7[ $b1        & 0x3F] ^ $sbox8[ $b2        & 0x3F] ^ ' . $l . ';
                        ';
                        // end of "the Feistel (F) function"

                        // swap L & R
                        list($l, $r) = array($r, $l);
                    }
                    list($l, $r) = array($r, $l);
                }

                // Perform the inverse IP permutation.
                $crypt_block[$c].= '$in =
                    ($shuffleinvip[($l >> 24) & 0xFF] & "\x80\x80\x80\x80\x80\x80\x80\x80") |
                    ($shuffleinvip[($r >> 24) & 0xFF] & "\x40\x40\x40\x40\x40\x40\x40\x40") |
                    ($shuffleinvip[($l >> 16) & 0xFF] & "\x20\x20\x20\x20\x20\x20\x20\x20") |
                    ($shuffleinvip[($r >> 16) & 0xFF] & "\x10\x10\x10\x10\x10\x10\x10\x10") |
                    ($shuffleinvip[($l >>  8) & 0xFF] & "\x08\x08\x08\x08\x08\x08\x08\x08") |
                    ($shuffleinvip[($r >>  8) & 0xFF] & "\x04\x04\x04\x04\x04\x04\x04\x04") |
                    ($shuffleinvip[ $l        & 0xFF] & "\x02\x02\x02\x02\x02\x02\x02\x02") |
                    ($shuffleinvip[ $r        & 0xFF] & "\x01\x01\x01\x01\x01\x01\x01\x01");
                ';
            }

            // Creates the inline-crypt function
            $lambda_functions[$code_hash] = $this->_createInlineCryptFunction(
                array(
                   'init_crypt'    => $init_crypt,
                   'init_encrypt'  => $init_encrypt,
                   'init_decrypt'  => $init_decrypt,
                   'encrypt_block' => $crypt_block[CRYPT_DES_ENCRYPT],
                   'decrypt_block' => $crypt_block[CRYPT_DES_DECRYPT]
                )
            );
        }

        // Set the inline-crypt function as callback in: $this->inline_crypt
        $this->inline_crypt = $lambda_functions[$code_hash];
    }
}
