<?php

/**
 * Pure-PHP implementation of Twofish.
 *
 * Uses mcrypt, if available, and an internal implementation, otherwise.
 *
 * PHP versions 4 and 5
 *
 * Useful resources are as follows:
 *
 *  - {@link http://en.wikipedia.org/wiki/Twofish Wikipedia description of Twofish}
 *
 * Here's a short example of how to use this library:
 * <code>
 * <?php
 *    include 'Crypt/Twofish.php';
 *
 *    $twofish = new Crypt_Twofish();
 *
 *    $twofish->setKey('12345678901234567890123456789012');
 *
 *    $plaintext = str_repeat('a', 1024);
 *
 *    echo $twofish->decrypt($twofish->encrypt($plaintext));
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
 * @package   Crypt_Twofish
 * @author    Jim Wigginton <terrafrost@php.net>
 * @author    Hans-Juergen Petrich <petrich@tronic-media.com>
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
 * @access public
 * @see Crypt_Twofish::encrypt()
 * @see Crypt_Twofish::decrypt()
 */
/**
 * Encrypt / decrypt using the Counter mode.
 *
 * Set to -1 since that's what Crypt/Random.php uses to index the CTR mode.
 *
 * @link http://en.wikipedia.org/wiki/Block_cipher_modes_of_operation#Counter_.28CTR.29
 */
define('CRYPT_TWOFISH_MODE_CTR', CRYPT_MODE_CTR);
/**
 * Encrypt / decrypt using the Electronic Code Book mode.
 *
 * @link http://en.wikipedia.org/wiki/Block_cipher_modes_of_operation#Electronic_codebook_.28ECB.29
 */
define('CRYPT_TWOFISH_MODE_ECB', CRYPT_MODE_ECB);
/**
 * Encrypt / decrypt using the Code Book Chaining mode.
 *
 * @link http://en.wikipedia.org/wiki/Block_cipher_modes_of_operation#Cipher-block_chaining_.28CBC.29
 */
define('CRYPT_TWOFISH_MODE_CBC', CRYPT_MODE_CBC);
/**
 * Encrypt / decrypt using the Cipher Feedback mode.
 *
 * @link http://en.wikipedia.org/wiki/Block_cipher_modes_of_operation#Cipher_feedback_.28CFB.29
 */
define('CRYPT_TWOFISH_MODE_CFB', CRYPT_MODE_CFB);
/**
 * Encrypt / decrypt using the Cipher Feedback mode.
 *
 * @link http://en.wikipedia.org/wiki/Block_cipher_modes_of_operation#Output_feedback_.28OFB.29
 */
define('CRYPT_TWOFISH_MODE_OFB', CRYPT_MODE_OFB);
/**#@-*/

/**#@+
 * @access private
 * @see Crypt_Base::Crypt_Base()
 */
/**
 * Toggles the internal implementation
 */
define('CRYPT_TWOFISH_MODE_INTERNAL', CRYPT_MODE_INTERNAL);
/**
 * Toggles the mcrypt implementation
 */
define('CRYPT_TWOFISH_MODE_MCRYPT', CRYPT_MODE_MCRYPT);
/**#@-*/

/**
 * Pure-PHP implementation of Twofish.
 *
 * @package Crypt_Twofish
 * @author  Jim Wigginton <terrafrost@php.net>
 * @author  Hans-Juergen Petrich <petrich@tronic-media.com>
 * @access  public
 */
class Crypt_Twofish extends Crypt_Base
{
    /**
     * The namespace used by the cipher for its constants.
     *
     * @see Crypt_Base::const_namespace
     * @var String
     * @access private
     */
    var $const_namespace = 'TWOFISH';

    /**
     * The mcrypt specific name of the cipher
     *
     * @see Crypt_Base::cipher_name_mcrypt
     * @var String
     * @access private
     */
    var $cipher_name_mcrypt = 'twofish';

    /**
     * Optimizing value while CFB-encrypting
     *
     * @see Crypt_Base::cfb_init_len
     * @var Integer
     * @access private
     */
    var $cfb_init_len = 800;

    /**
     * Q-Table
     *
     * @var Array
     * @access private
     */
    var $q0 = array (
        0xA9, 0x67, 0xB3, 0xE8, 0x04, 0xFD, 0xA3, 0x76,
        0x9A, 0x92, 0x80, 0x78, 0xE4, 0xDD, 0xD1, 0x38,
        0x0D, 0xC6, 0x35, 0x98, 0x18, 0xF7, 0xEC, 0x6C,
        0x43, 0x75, 0x37, 0x26, 0xFA, 0x13, 0x94, 0x48,
        0xF2, 0xD0, 0x8B, 0x30, 0x84, 0x54, 0xDF, 0x23,
        0x19, 0x5B, 0x3D, 0x59, 0xF3, 0xAE, 0xA2, 0x82,
        0x63, 0x01, 0x83, 0x2E, 0xD9, 0x51, 0x9B, 0x7C,
        0xA6, 0xEB, 0xA5, 0xBE, 0x16, 0x0C, 0xE3, 0x61,
        0xC0, 0x8C, 0x3A, 0xF5, 0x73, 0x2C, 0x25, 0x0B,
        0xBB, 0x4E, 0x89, 0x6B, 0x53, 0x6A, 0xB4, 0xF1,
        0xE1, 0xE6, 0xBD, 0x45, 0xE2, 0xF4, 0xB6, 0x66,
        0xCC, 0x95, 0x03, 0x56, 0xD4, 0x1C, 0x1E, 0xD7,
        0xFB, 0xC3, 0x8E, 0xB5, 0xE9, 0xCF, 0xBF, 0xBA,
        0xEA, 0x77, 0x39, 0xAF, 0x33, 0xC9, 0x62, 0x71,
        0x81, 0x79, 0x09, 0xAD, 0x24, 0xCD, 0xF9, 0xD8,
        0xE5, 0xC5, 0xB9, 0x4D, 0x44, 0x08, 0x86, 0xE7,
        0xA1, 0x1D, 0xAA, 0xED, 0x06, 0x70, 0xB2, 0xD2,
        0x41, 0x7B, 0xA0, 0x11, 0x31, 0xC2, 0x27, 0x90,
        0x20, 0xF6, 0x60, 0xFF, 0x96, 0x5C, 0xB1, 0xAB,
        0x9E, 0x9C, 0x52, 0x1B, 0x5F, 0x93, 0x0A, 0xEF,
        0x91, 0x85, 0x49, 0xEE, 0x2D, 0x4F, 0x8F, 0x3B,
        0x47, 0x87, 0x6D, 0x46, 0xD6, 0x3E, 0x69, 0x64,
        0x2A, 0xCE, 0xCB, 0x2F, 0xFC, 0x97, 0x05, 0x7A,
        0xAC, 0x7F, 0xD5, 0x1A, 0x4B, 0x0E, 0xA7, 0x5A,
        0x28, 0x14, 0x3F, 0x29, 0x88, 0x3C, 0x4C, 0x02,
        0xB8, 0xDA, 0xB0, 0x17, 0x55, 0x1F, 0x8A, 0x7D,
        0x57, 0xC7, 0x8D, 0x74, 0xB7, 0xC4, 0x9F, 0x72,
        0x7E, 0x15, 0x22, 0x12, 0x58, 0x07, 0x99, 0x34,
        0x6E, 0x50, 0xDE, 0x68, 0x65, 0xBC, 0xDB, 0xF8,
        0xC8, 0xA8, 0x2B, 0x40, 0xDC, 0xFE, 0x32, 0xA4,
        0xCA, 0x10, 0x21, 0xF0, 0xD3, 0x5D, 0x0F, 0x00,
        0x6F, 0x9D, 0x36, 0x42, 0x4A, 0x5E, 0xC1, 0xE0
    );

    /**
     * Q-Table
     *
     * @var Array
     * @access private
     */
    var $q1 = array (
        0x75, 0xF3, 0xC6, 0xF4, 0xDB, 0x7B, 0xFB, 0xC8,
        0x4A, 0xD3, 0xE6, 0x6B, 0x45, 0x7D, 0xE8, 0x4B,
        0xD6, 0x32, 0xD8, 0xFD, 0x37, 0x71, 0xF1, 0xE1,
        0x30, 0x0F, 0xF8, 0x1B, 0x87, 0xFA, 0x06, 0x3F,
        0x5E, 0xBA, 0xAE, 0x5B, 0x8A, 0x00, 0xBC, 0x9D,
        0x6D, 0xC1, 0xB1, 0x0E, 0x80, 0x5D, 0xD2, 0xD5,
        0xA0, 0x84, 0x07, 0x14, 0xB5, 0x90, 0x2C, 0xA3,
        0xB2, 0x73, 0x4C, 0x54, 0x92, 0x74, 0x36, 0x51,
        0x38, 0xB0, 0xBD, 0x5A, 0xFC, 0x60, 0x62, 0x96,
        0x6C, 0x42, 0xF7, 0x10, 0x7C, 0x28, 0x27, 0x8C,
        0x13, 0x95, 0x9C, 0xC7, 0x24, 0x46, 0x3B, 0x70,
        0xCA, 0xE3, 0x85, 0xCB, 0x11, 0xD0, 0x93, 0xB8,
        0xA6, 0x83, 0x20, 0xFF, 0x9F, 0x77, 0xC3, 0xCC,
        0x03, 0x6F, 0x08, 0xBF, 0x40, 0xE7, 0x2B, 0xE2,
        0x79, 0x0C, 0xAA, 0x82, 0x41, 0x3A, 0xEA, 0xB9,
        0xE4, 0x9A, 0xA4, 0x97, 0x7E, 0xDA, 0x7A, 0x17,
        0x66, 0x94, 0xA1, 0x1D, 0x3D, 0xF0, 0xDE, 0xB3,
        0x0B, 0x72, 0xA7, 0x1C, 0xEF, 0xD1, 0x53, 0x3E,
        0x8F, 0x33, 0x26, 0x5F, 0xEC, 0x76, 0x2A, 0x49,
        0x81, 0x88, 0xEE, 0x21, 0xC4, 0x1A, 0xEB, 0xD9,
        0xC5, 0x39, 0x99, 0xCD, 0xAD, 0x31, 0x8B, 0x01,
        0x18, 0x23, 0xDD, 0x1F, 0x4E, 0x2D, 0xF9, 0x48,
        0x4F, 0xF2, 0x65, 0x8E, 0x78, 0x5C, 0x58, 0x19,
        0x8D, 0xE5, 0x98, 0x57, 0x67, 0x7F, 0x05, 0x64,
        0xAF, 0x63, 0xB6, 0xFE, 0xF5, 0xB7, 0x3C, 0xA5,
        0xCE, 0xE9, 0x68, 0x44, 0xE0, 0x4D, 0x43, 0x69,
        0x29, 0x2E, 0xAC, 0x15, 0x59, 0xA8, 0x0A, 0x9E,
        0x6E, 0x47, 0xDF, 0x34, 0x35, 0x6A, 0xCF, 0xDC,
        0x22, 0xC9, 0xC0, 0x9B, 0x89, 0xD4, 0xED, 0xAB,
        0x12, 0xA2, 0x0D, 0x52, 0xBB, 0x02, 0x2F, 0xA9,
        0xD7, 0x61, 0x1E, 0xB4, 0x50, 0x04, 0xF6, 0xC2,
        0x16, 0x25, 0x86, 0x56, 0x55, 0x09, 0xBE, 0x91
    );

    /**
     * M-Table
     *
     * @var Array
     * @access private
     */
    var $m0 = array (
        0xBCBC3275, 0xECEC21F3, 0x202043C6, 0xB3B3C9F4, 0xDADA03DB, 0x02028B7B, 0xE2E22BFB, 0x9E9EFAC8,
        0xC9C9EC4A, 0xD4D409D3, 0x18186BE6, 0x1E1E9F6B, 0x98980E45, 0xB2B2387D, 0xA6A6D2E8, 0x2626B74B,
        0x3C3C57D6, 0x93938A32, 0x8282EED8, 0x525298FD, 0x7B7BD437, 0xBBBB3771, 0x5B5B97F1, 0x474783E1,
        0x24243C30, 0x5151E20F, 0xBABAC6F8, 0x4A4AF31B, 0xBFBF4887, 0x0D0D70FA, 0xB0B0B306, 0x7575DE3F,
        0xD2D2FD5E, 0x7D7D20BA, 0x666631AE, 0x3A3AA35B, 0x59591C8A, 0x00000000, 0xCDCD93BC, 0x1A1AE09D,
        0xAEAE2C6D, 0x7F7FABC1, 0x2B2BC7B1, 0xBEBEB90E, 0xE0E0A080, 0x8A8A105D, 0x3B3B52D2, 0x6464BAD5,
        0xD8D888A0, 0xE7E7A584, 0x5F5FE807, 0x1B1B1114, 0x2C2CC2B5, 0xFCFCB490, 0x3131272C, 0x808065A3,
        0x73732AB2, 0x0C0C8173, 0x79795F4C, 0x6B6B4154, 0x4B4B0292, 0x53536974, 0x94948F36, 0x83831F51,
        0x2A2A3638, 0xC4C49CB0, 0x2222C8BD, 0xD5D5F85A, 0xBDBDC3FC, 0x48487860, 0xFFFFCE62, 0x4C4C0796,
        0x4141776C, 0xC7C7E642, 0xEBEB24F7, 0x1C1C1410, 0x5D5D637C, 0x36362228, 0x6767C027, 0xE9E9AF8C,
        0x4444F913, 0x1414EA95, 0xF5F5BB9C, 0xCFCF18C7, 0x3F3F2D24, 0xC0C0E346, 0x7272DB3B, 0x54546C70,
        0x29294CCA, 0xF0F035E3, 0x0808FE85, 0xC6C617CB, 0xF3F34F11, 0x8C8CE4D0, 0xA4A45993, 0xCACA96B8,
        0x68683BA6, 0xB8B84D83, 0x38382820, 0xE5E52EFF, 0xADAD569F, 0x0B0B8477, 0xC8C81DC3, 0x9999FFCC,
        0x5858ED03, 0x19199A6F, 0x0E0E0A08, 0x95957EBF, 0x70705040, 0xF7F730E7, 0x6E6ECF2B, 0x1F1F6EE2,
        0xB5B53D79, 0x09090F0C, 0x616134AA, 0x57571682, 0x9F9F0B41, 0x9D9D803A, 0x111164EA, 0x2525CDB9,
        0xAFAFDDE4, 0x4545089A, 0xDFDF8DA4, 0xA3A35C97, 0xEAEAD57E, 0x353558DA, 0xEDEDD07A, 0x4343FC17,
        0xF8F8CB66, 0xFBFBB194, 0x3737D3A1, 0xFAFA401D, 0xC2C2683D, 0xB4B4CCF0, 0x32325DDE, 0x9C9C71B3,
        0x5656E70B, 0xE3E3DA72, 0x878760A7, 0x15151B1C, 0xF9F93AEF, 0x6363BFD1, 0x3434A953, 0x9A9A853E,
        0xB1B1428F, 0x7C7CD133, 0x88889B26, 0x3D3DA65F, 0xA1A1D7EC, 0xE4E4DF76, 0x8181942A, 0x91910149,
        0x0F0FFB81, 0xEEEEAA88, 0x161661EE, 0xD7D77321, 0x9797F5C4, 0xA5A5A81A, 0xFEFE3FEB, 0x6D6DB5D9,
        0x7878AEC5, 0xC5C56D39, 0x1D1DE599, 0x7676A4CD, 0x3E3EDCAD, 0xCBCB6731, 0xB6B6478B, 0xEFEF5B01,
        0x12121E18, 0x6060C523, 0x6A6AB0DD, 0x4D4DF61F, 0xCECEE94E, 0xDEDE7C2D, 0x55559DF9, 0x7E7E5A48,
        0x2121B24F, 0x03037AF2, 0xA0A02665, 0x5E5E198E, 0x5A5A6678, 0x65654B5C, 0x62624E58, 0xFDFD4519,
        0x0606F48D, 0x404086E5, 0xF2F2BE98, 0x3333AC57, 0x17179067, 0x05058E7F, 0xE8E85E05, 0x4F4F7D64,
        0x89896AAF, 0x10109563, 0x74742FB6, 0x0A0A75FE, 0x5C5C92F5, 0x9B9B74B7, 0x2D2D333C, 0x3030D6A5,
        0x2E2E49CE, 0x494989E9, 0x46467268, 0x77775544, 0xA8A8D8E0, 0x9696044D, 0x2828BD43, 0xA9A92969,
        0xD9D97929, 0x8686912E, 0xD1D187AC, 0xF4F44A15, 0x8D8D1559, 0xD6D682A8, 0xB9B9BC0A, 0x42420D9E,
        0xF6F6C16E, 0x2F2FB847, 0xDDDD06DF, 0x23233934, 0xCCCC6235, 0xF1F1C46A, 0xC1C112CF, 0x8585EBDC,
        0x8F8F9E22, 0x7171A1C9, 0x9090F0C0, 0xAAAA539B, 0x0101F189, 0x8B8BE1D4, 0x4E4E8CED, 0x8E8E6FAB,
        0xABABA212, 0x6F6F3EA2, 0xE6E6540D, 0xDBDBF252, 0x92927BBB, 0xB7B7B602, 0x6969CA2F, 0x3939D9A9,
        0xD3D30CD7, 0xA7A72361, 0xA2A2AD1E, 0xC3C399B4, 0x6C6C4450, 0x07070504, 0x04047FF6, 0x272746C2,
        0xACACA716, 0xD0D07625, 0x50501386, 0xDCDCF756, 0x84841A55, 0xE1E15109, 0x7A7A25BE, 0x1313EF91
    );

    /**
     * M-Table
     *
     * @var Array
     * @access private
     */
    var $m1 = array (
        0xA9D93939, 0x67901717, 0xB3719C9C, 0xE8D2A6A6, 0x04050707, 0xFD985252, 0xA3658080, 0x76DFE4E4,
        0x9A084545, 0x92024B4B, 0x80A0E0E0, 0x78665A5A, 0xE4DDAFAF, 0xDDB06A6A, 0xD1BF6363, 0x38362A2A,
        0x0D54E6E6, 0xC6432020, 0x3562CCCC, 0x98BEF2F2, 0x181E1212, 0xF724EBEB, 0xECD7A1A1, 0x6C774141,
        0x43BD2828, 0x7532BCBC, 0x37D47B7B, 0x269B8888, 0xFA700D0D, 0x13F94444, 0x94B1FBFB, 0x485A7E7E,
        0xF27A0303, 0xD0E48C8C, 0x8B47B6B6, 0x303C2424, 0x84A5E7E7, 0x54416B6B, 0xDF06DDDD, 0x23C56060,
        0x1945FDFD, 0x5BA33A3A, 0x3D68C2C2, 0x59158D8D, 0xF321ECEC, 0xAE316666, 0xA23E6F6F, 0x82165757,
        0x63951010, 0x015BEFEF, 0x834DB8B8, 0x2E918686, 0xD9B56D6D, 0x511F8383, 0x9B53AAAA, 0x7C635D5D,
        0xA63B6868, 0xEB3FFEFE, 0xA5D63030, 0xBE257A7A, 0x16A7ACAC, 0x0C0F0909, 0xE335F0F0, 0x6123A7A7,
        0xC0F09090, 0x8CAFE9E9, 0x3A809D9D, 0xF5925C5C, 0x73810C0C, 0x2C273131, 0x2576D0D0, 0x0BE75656,
        0xBB7B9292, 0x4EE9CECE, 0x89F10101, 0x6B9F1E1E, 0x53A93434, 0x6AC4F1F1, 0xB499C3C3, 0xF1975B5B,
        0xE1834747, 0xE66B1818, 0xBDC82222, 0x450E9898, 0xE26E1F1F, 0xF4C9B3B3, 0xB62F7474, 0x66CBF8F8,
        0xCCFF9999, 0x95EA1414, 0x03ED5858, 0x56F7DCDC, 0xD4E18B8B, 0x1C1B1515, 0x1EADA2A2, 0xD70CD3D3,
        0xFB2BE2E2, 0xC31DC8C8, 0x8E195E5E, 0xB5C22C2C, 0xE9894949, 0xCF12C1C1, 0xBF7E9595, 0xBA207D7D,
        0xEA641111, 0x77840B0B, 0x396DC5C5, 0xAF6A8989, 0x33D17C7C, 0xC9A17171, 0x62CEFFFF, 0x7137BBBB,
        0x81FB0F0F, 0x793DB5B5, 0x0951E1E1, 0xADDC3E3E, 0x242D3F3F, 0xCDA47676, 0xF99D5555, 0xD8EE8282,
        0xE5864040, 0xC5AE7878, 0xB9CD2525, 0x4D049696, 0x44557777, 0x080A0E0E, 0x86135050, 0xE730F7F7,
        0xA1D33737, 0x1D40FAFA, 0xAA346161, 0xED8C4E4E, 0x06B3B0B0, 0x706C5454, 0xB22A7373, 0xD2523B3B,
        0x410B9F9F, 0x7B8B0202, 0xA088D8D8, 0x114FF3F3, 0x3167CBCB, 0xC2462727, 0x27C06767, 0x90B4FCFC,
        0x20283838, 0xF67F0404, 0x60784848, 0xFF2EE5E5, 0x96074C4C, 0x5C4B6565, 0xB1C72B2B, 0xAB6F8E8E,
        0x9E0D4242, 0x9CBBF5F5, 0x52F2DBDB, 0x1BF34A4A, 0x5FA63D3D, 0x9359A4A4, 0x0ABCB9B9, 0xEF3AF9F9,
        0x91EF1313, 0x85FE0808, 0x49019191, 0xEE611616, 0x2D7CDEDE, 0x4FB22121, 0x8F42B1B1, 0x3BDB7272,
        0x47B82F2F, 0x8748BFBF, 0x6D2CAEAE, 0x46E3C0C0, 0xD6573C3C, 0x3E859A9A, 0x6929A9A9, 0x647D4F4F,
        0x2A948181, 0xCE492E2E, 0xCB17C6C6, 0x2FCA6969, 0xFCC3BDBD, 0x975CA3A3, 0x055EE8E8, 0x7AD0EDED,
        0xAC87D1D1, 0x7F8E0505, 0xD5BA6464, 0x1AA8A5A5, 0x4BB72626, 0x0EB9BEBE, 0xA7608787, 0x5AF8D5D5,
        0x28223636, 0x14111B1B, 0x3FDE7575, 0x2979D9D9, 0x88AAEEEE, 0x3C332D2D, 0x4C5F7979, 0x02B6B7B7,
        0xB896CACA, 0xDA583535, 0xB09CC4C4, 0x17FC4343, 0x551A8484, 0x1FF64D4D, 0x8A1C5959, 0x7D38B2B2,
        0x57AC3333, 0xC718CFCF, 0x8DF40606, 0x74695353, 0xB7749B9B, 0xC4F59797, 0x9F56ADAD, 0x72DAE3E3,
        0x7ED5EAEA, 0x154AF4F4, 0x229E8F8F, 0x12A2ABAB, 0x584E6262, 0x07E85F5F, 0x99E51D1D, 0x34392323,
        0x6EC1F6F6, 0x50446C6C, 0xDE5D3232, 0x68724646, 0x6526A0A0, 0xBC93CDCD, 0xDB03DADA, 0xF8C6BABA,
        0xC8FA9E9E, 0xA882D6D6, 0x2BCF6E6E, 0x40507070, 0xDCEB8585, 0xFE750A0A, 0x328A9393, 0xA48DDFDF,
        0xCA4C2929, 0x10141C1C, 0x2173D7D7, 0xF0CCB4B4, 0xD309D4D4, 0x5D108A8A, 0x0FE25151, 0x00000000,
        0x6F9A1919, 0x9DE01A1A, 0x368F9494, 0x42E6C7C7, 0x4AECC9C9, 0x5EFDD2D2, 0xC1AB7F7F, 0xE0D8A8A8
    );

    /**
     * M-Table
     *
     * @var Array
     * @access private
     */
    var $m2 = array (
        0xBC75BC32, 0xECF3EC21, 0x20C62043, 0xB3F4B3C9, 0xDADBDA03, 0x027B028B, 0xE2FBE22B, 0x9EC89EFA,
        0xC94AC9EC, 0xD4D3D409, 0x18E6186B, 0x1E6B1E9F, 0x9845980E, 0xB27DB238, 0xA6E8A6D2, 0x264B26B7,
        0x3CD63C57, 0x9332938A, 0x82D882EE, 0x52FD5298, 0x7B377BD4, 0xBB71BB37, 0x5BF15B97, 0x47E14783,
        0x2430243C, 0x510F51E2, 0xBAF8BAC6, 0x4A1B4AF3, 0xBF87BF48, 0x0DFA0D70, 0xB006B0B3, 0x753F75DE,
        0xD25ED2FD, 0x7DBA7D20, 0x66AE6631, 0x3A5B3AA3, 0x598A591C, 0x00000000, 0xCDBCCD93, 0x1A9D1AE0,
        0xAE6DAE2C, 0x7FC17FAB, 0x2BB12BC7, 0xBE0EBEB9, 0xE080E0A0, 0x8A5D8A10, 0x3BD23B52, 0x64D564BA,
        0xD8A0D888, 0xE784E7A5, 0x5F075FE8, 0x1B141B11, 0x2CB52CC2, 0xFC90FCB4, 0x312C3127, 0x80A38065,
        0x73B2732A, 0x0C730C81, 0x794C795F, 0x6B546B41, 0x4B924B02, 0x53745369, 0x9436948F, 0x8351831F,
        0x2A382A36, 0xC4B0C49C, 0x22BD22C8, 0xD55AD5F8, 0xBDFCBDC3, 0x48604878, 0xFF62FFCE, 0x4C964C07,
        0x416C4177, 0xC742C7E6, 0xEBF7EB24, 0x1C101C14, 0x5D7C5D63, 0x36283622, 0x672767C0, 0xE98CE9AF,
        0x441344F9, 0x149514EA, 0xF59CF5BB, 0xCFC7CF18, 0x3F243F2D, 0xC046C0E3, 0x723B72DB, 0x5470546C,
        0x29CA294C, 0xF0E3F035, 0x088508FE, 0xC6CBC617, 0xF311F34F, 0x8CD08CE4, 0xA493A459, 0xCAB8CA96,
        0x68A6683B, 0xB883B84D, 0x38203828, 0xE5FFE52E, 0xAD9FAD56, 0x0B770B84, 0xC8C3C81D, 0x99CC99FF,
        0x580358ED, 0x196F199A, 0x0E080E0A, 0x95BF957E, 0x70407050, 0xF7E7F730, 0x6E2B6ECF, 0x1FE21F6E,
        0xB579B53D, 0x090C090F, 0x61AA6134, 0x57825716, 0x9F419F0B, 0x9D3A9D80, 0x11EA1164, 0x25B925CD,
        0xAFE4AFDD, 0x459A4508, 0xDFA4DF8D, 0xA397A35C, 0xEA7EEAD5, 0x35DA3558, 0xED7AEDD0, 0x431743FC,
        0xF866F8CB, 0xFB94FBB1, 0x37A137D3, 0xFA1DFA40, 0xC23DC268, 0xB4F0B4CC, 0x32DE325D, 0x9CB39C71,
        0x560B56E7, 0xE372E3DA, 0x87A78760, 0x151C151B, 0xF9EFF93A, 0x63D163BF, 0x345334A9, 0x9A3E9A85,
        0xB18FB142, 0x7C337CD1, 0x8826889B, 0x3D5F3DA6, 0xA1ECA1D7, 0xE476E4DF, 0x812A8194, 0x91499101,
        0x0F810FFB, 0xEE88EEAA, 0x16EE1661, 0xD721D773, 0x97C497F5, 0xA51AA5A8, 0xFEEBFE3F, 0x6DD96DB5,
        0x78C578AE, 0xC539C56D, 0x1D991DE5, 0x76CD76A4, 0x3EAD3EDC, 0xCB31CB67, 0xB68BB647, 0xEF01EF5B,
        0x1218121E, 0x602360C5, 0x6ADD6AB0, 0x4D1F4DF6, 0xCE4ECEE9, 0xDE2DDE7C, 0x55F9559D, 0x7E487E5A,
        0x214F21B2, 0x03F2037A, 0xA065A026, 0x5E8E5E19, 0x5A785A66, 0x655C654B, 0x6258624E, 0xFD19FD45,
        0x068D06F4, 0x40E54086, 0xF298F2BE, 0x335733AC, 0x17671790, 0x057F058E, 0xE805E85E, 0x4F644F7D,
        0x89AF896A, 0x10631095, 0x74B6742F, 0x0AFE0A75, 0x5CF55C92, 0x9BB79B74, 0x2D3C2D33, 0x30A530D6,
        0x2ECE2E49, 0x49E94989, 0x46684672, 0x77447755, 0xA8E0A8D8, 0x964D9604, 0x284328BD, 0xA969A929,
        0xD929D979, 0x862E8691, 0xD1ACD187, 0xF415F44A, 0x8D598D15, 0xD6A8D682, 0xB90AB9BC, 0x429E420D,
        0xF66EF6C1, 0x2F472FB8, 0xDDDFDD06, 0x23342339, 0xCC35CC62, 0xF16AF1C4, 0xC1CFC112, 0x85DC85EB,
        0x8F228F9E, 0x71C971A1, 0x90C090F0, 0xAA9BAA53, 0x018901F1, 0x8BD48BE1, 0x4EED4E8C, 0x8EAB8E6F,
        0xAB12ABA2, 0x6FA26F3E, 0xE60DE654, 0xDB52DBF2, 0x92BB927B, 0xB702B7B6, 0x692F69CA, 0x39A939D9,
        0xD3D7D30C, 0xA761A723, 0xA21EA2AD, 0xC3B4C399, 0x6C506C44, 0x07040705, 0x04F6047F, 0x27C22746,
        0xAC16ACA7, 0xD025D076, 0x50865013, 0xDC56DCF7, 0x8455841A, 0xE109E151, 0x7ABE7A25, 0x139113EF
    );

    /**
     * M-Table
     *
     * @var Array
     * @access private
     */
    var $m3 = array (
        0xD939A9D9, 0x90176790, 0x719CB371, 0xD2A6E8D2, 0x05070405, 0x9852FD98, 0x6580A365, 0xDFE476DF,
        0x08459A08, 0x024B9202, 0xA0E080A0, 0x665A7866, 0xDDAFE4DD, 0xB06ADDB0, 0xBF63D1BF, 0x362A3836,
        0x54E60D54, 0x4320C643, 0x62CC3562, 0xBEF298BE, 0x1E12181E, 0x24EBF724, 0xD7A1ECD7, 0x77416C77,
        0xBD2843BD, 0x32BC7532, 0xD47B37D4, 0x9B88269B, 0x700DFA70, 0xF94413F9, 0xB1FB94B1, 0x5A7E485A,
        0x7A03F27A, 0xE48CD0E4, 0x47B68B47, 0x3C24303C, 0xA5E784A5, 0x416B5441, 0x06DDDF06, 0xC56023C5,
        0x45FD1945, 0xA33A5BA3, 0x68C23D68, 0x158D5915, 0x21ECF321, 0x3166AE31, 0x3E6FA23E, 0x16578216,
        0x95106395, 0x5BEF015B, 0x4DB8834D, 0x91862E91, 0xB56DD9B5, 0x1F83511F, 0x53AA9B53, 0x635D7C63,
        0x3B68A63B, 0x3FFEEB3F, 0xD630A5D6, 0x257ABE25, 0xA7AC16A7, 0x0F090C0F, 0x35F0E335, 0x23A76123,
        0xF090C0F0, 0xAFE98CAF, 0x809D3A80, 0x925CF592, 0x810C7381, 0x27312C27, 0x76D02576, 0xE7560BE7,
        0x7B92BB7B, 0xE9CE4EE9, 0xF10189F1, 0x9F1E6B9F, 0xA93453A9, 0xC4F16AC4, 0x99C3B499, 0x975BF197,
        0x8347E183, 0x6B18E66B, 0xC822BDC8, 0x0E98450E, 0x6E1FE26E, 0xC9B3F4C9, 0x2F74B62F, 0xCBF866CB,
        0xFF99CCFF, 0xEA1495EA, 0xED5803ED, 0xF7DC56F7, 0xE18BD4E1, 0x1B151C1B, 0xADA21EAD, 0x0CD3D70C,
        0x2BE2FB2B, 0x1DC8C31D, 0x195E8E19, 0xC22CB5C2, 0x8949E989, 0x12C1CF12, 0x7E95BF7E, 0x207DBA20,
        0x6411EA64, 0x840B7784, 0x6DC5396D, 0x6A89AF6A, 0xD17C33D1, 0xA171C9A1, 0xCEFF62CE, 0x37BB7137,
        0xFB0F81FB, 0x3DB5793D, 0x51E10951, 0xDC3EADDC, 0x2D3F242D, 0xA476CDA4, 0x9D55F99D, 0xEE82D8EE,
        0x8640E586, 0xAE78C5AE, 0xCD25B9CD, 0x04964D04, 0x55774455, 0x0A0E080A, 0x13508613, 0x30F7E730,
        0xD337A1D3, 0x40FA1D40, 0x3461AA34, 0x8C4EED8C, 0xB3B006B3, 0x6C54706C, 0x2A73B22A, 0x523BD252,
        0x0B9F410B, 0x8B027B8B, 0x88D8A088, 0x4FF3114F, 0x67CB3167, 0x4627C246, 0xC06727C0, 0xB4FC90B4,
        0x28382028, 0x7F04F67F, 0x78486078, 0x2EE5FF2E, 0x074C9607, 0x4B655C4B, 0xC72BB1C7, 0x6F8EAB6F,
        0x0D429E0D, 0xBBF59CBB, 0xF2DB52F2, 0xF34A1BF3, 0xA63D5FA6, 0x59A49359, 0xBCB90ABC, 0x3AF9EF3A,
        0xEF1391EF, 0xFE0885FE, 0x01914901, 0x6116EE61, 0x7CDE2D7C, 0xB2214FB2, 0x42B18F42, 0xDB723BDB,
        0xB82F47B8, 0x48BF8748, 0x2CAE6D2C, 0xE3C046E3, 0x573CD657, 0x859A3E85, 0x29A96929, 0x7D4F647D,
        0x94812A94, 0x492ECE49, 0x17C6CB17, 0xCA692FCA, 0xC3BDFCC3, 0x5CA3975C, 0x5EE8055E, 0xD0ED7AD0,
        0x87D1AC87, 0x8E057F8E, 0xBA64D5BA, 0xA8A51AA8, 0xB7264BB7, 0xB9BE0EB9, 0x6087A760, 0xF8D55AF8,
        0x22362822, 0x111B1411, 0xDE753FDE, 0x79D92979, 0xAAEE88AA, 0x332D3C33, 0x5F794C5F, 0xB6B702B6,
        0x96CAB896, 0x5835DA58, 0x9CC4B09C, 0xFC4317FC, 0x1A84551A, 0xF64D1FF6, 0x1C598A1C, 0x38B27D38,
        0xAC3357AC, 0x18CFC718, 0xF4068DF4, 0x69537469, 0x749BB774, 0xF597C4F5, 0x56AD9F56, 0xDAE372DA,
        0xD5EA7ED5, 0x4AF4154A, 0x9E8F229E, 0xA2AB12A2, 0x4E62584E, 0xE85F07E8, 0xE51D99E5, 0x39233439,
        0xC1F66EC1, 0x446C5044, 0x5D32DE5D, 0x72466872, 0x26A06526, 0x93CDBC93, 0x03DADB03, 0xC6BAF8C6,
        0xFA9EC8FA, 0x82D6A882, 0xCF6E2BCF, 0x50704050, 0xEB85DCEB, 0x750AFE75, 0x8A93328A, 0x8DDFA48D,
        0x4C29CA4C, 0x141C1014, 0x73D72173, 0xCCB4F0CC, 0x09D4D309, 0x108A5D10, 0xE2510FE2, 0x00000000,
        0x9A196F9A, 0xE01A9DE0, 0x8F94368F, 0xE6C742E6, 0xECC94AEC, 0xFDD25EFD, 0xAB7FC1AB, 0xD8A8E0D8
    );

    /**
     * The Key Schedule Array
     *
     * @var Array
     * @access private
     */
    var $K = array();

    /**
     * The Key depended S-Table 0
     *
     * @var Array
     * @access private
     */
    var $S0 = array();

    /**
     * The Key depended S-Table 1
     *
     * @var Array
     * @access private
     */
    var $S1 = array();

    /**
     * The Key depended S-Table 2
     *
     * @var Array
     * @access private
     */
    var $S2 = array();

    /**
     * The Key depended S-Table 3
     *
     * @var Array
     * @access private
     */
    var $S3 = array();

    /**
     * Holds the last used key
     *
     * @var Array
     * @access private
     */
    var $kl;

    /**
     * Sets the key.
     *
     * Keys can be of any length. Twofish, itself, requires the use of a key that's 128, 192 or 256-bits long.
     * If the key is less than 256-bits we round the length up to the closest valid key length,
     * padding $key with null bytes. If the key is more than 256-bits, we trim the excess bits.
     *
     * If the key is not explicitly set, it'll be assumed a 128 bits key to be all null bytes.
     *
     * @access public
     * @see Crypt_Base::setKey()
     * @param String $key
     */
    function setKey($key)
    {
        $keylength = strlen($key);
        switch (true) {
            case $keylength <= 16:
                $key = str_pad($key, 16, "\0");
                break;
            case $keylength <= 24:
                $key = str_pad($key, 24, "\0");
                break;
            case $keylength < 32:
                $key = str_pad($key, 32, "\0");
                break;
            case $keylength > 32:
                $key = substr($key, 0, 32);
        }
        parent::setKey($key);
    }

    /**
     * Setup the key (expansion)
     *
     * @see Crypt_Base::_setupKey()
     * @access private
     */
    function _setupKey()
    {
        if (isset($this->kl['key']) && $this->key === $this->kl['key']) {
            // already expanded
            return;
        }
        $this->kl = array('key' => $this->key);

        /* Key expanding and generating the key-depended s-boxes */
        $le_longs = unpack('V*', $this->key);
        $key = unpack('C*', $this->key);
        $m0 = $this->m0;
        $m1 = $this->m1;
        $m2 = $this->m2;
        $m3 = $this->m3;
        $q0 = $this->q0;
        $q1 = $this->q1;

        $K = $S0 = $S1 = $S2 = $S3 = array();

        switch (strlen($this->key)) {
            case 16:
                list ($s7, $s6, $s5, $s4) = $this->_mdsrem($le_longs[1], $le_longs[2]);
                list ($s3, $s2, $s1, $s0) = $this->_mdsrem($le_longs[3], $le_longs[4]);
                for ($i = 0, $j = 1; $i < 40; $i+= 2,$j+= 2) {
                    $A = $m0[$q0[$q0[$i] ^ $key[ 9]] ^ $key[1]] ^
                         $m1[$q0[$q1[$i] ^ $key[10]] ^ $key[2]] ^
                         $m2[$q1[$q0[$i] ^ $key[11]] ^ $key[3]] ^
                         $m3[$q1[$q1[$i] ^ $key[12]] ^ $key[4]];
                    $B = $m0[$q0[$q0[$j] ^ $key[13]] ^ $key[5]] ^
                         $m1[$q0[$q1[$j] ^ $key[14]] ^ $key[6]] ^
                         $m2[$q1[$q0[$j] ^ $key[15]] ^ $key[7]] ^
                         $m3[$q1[$q1[$j] ^ $key[16]] ^ $key[8]];
                    $B = ($B << 8) | ($B >> 24 & 0xff);
                    $K[] = $A+= $B;
                    $K[] = (($A+= $B) << 9 | $A >> 23 & 0x1ff);
                }
                for ($i = 0; $i < 256; ++$i) {
                    $S0[$i] = $m0[$q0[$q0[$i] ^ $s4] ^ $s0];
                    $S1[$i] = $m1[$q0[$q1[$i] ^ $s5] ^ $s1];
                    $S2[$i] = $m2[$q1[$q0[$i] ^ $s6] ^ $s2];
                    $S3[$i] = $m3[$q1[$q1[$i] ^ $s7] ^ $s3];
                }
                break;
            case 24:
                list ($sb, $sa, $s9, $s8) = $this->_mdsrem($le_longs[1], $le_longs[2]);
                list ($s7, $s6, $s5, $s4) = $this->_mdsrem($le_longs[3], $le_longs[4]);
                list ($s3, $s2, $s1, $s0) = $this->_mdsrem($le_longs[5], $le_longs[6]);
                for ($i = 0, $j = 1; $i < 40; $i+= 2, $j+= 2) {
                    $A = $m0[$q0[$q0[$q1[$i] ^ $key[17]] ^ $key[ 9]] ^ $key[1]] ^
                         $m1[$q0[$q1[$q1[$i] ^ $key[18]] ^ $key[10]] ^ $key[2]] ^
                         $m2[$q1[$q0[$q0[$i] ^ $key[19]] ^ $key[11]] ^ $key[3]] ^
                         $m3[$q1[$q1[$q0[$i] ^ $key[20]] ^ $key[12]] ^ $key[4]];
                    $B = $m0[$q0[$q0[$q1[$j] ^ $key[21]] ^ $key[13]] ^ $key[5]] ^
                         $m1[$q0[$q1[$q1[$j] ^ $key[22]] ^ $key[14]] ^ $key[6]] ^
                         $m2[$q1[$q0[$q0[$j] ^ $key[23]] ^ $key[15]] ^ $key[7]] ^
                         $m3[$q1[$q1[$q0[$j] ^ $key[24]] ^ $key[16]] ^ $key[8]];
                    $B = ($B << 8) | ($B >> 24 & 0xff);
                    $K[] = $A+= $B;
                    $K[] = (($A+= $B) << 9 | $A >> 23 & 0x1ff);
                }
                for ($i = 0; $i < 256; ++$i) {
                    $S0[$i] = $m0[$q0[$q0[$q1[$i] ^ $s8] ^ $s4] ^ $s0];
                    $S1[$i] = $m1[$q0[$q1[$q1[$i] ^ $s9] ^ $s5] ^ $s1];
                    $S2[$i] = $m2[$q1[$q0[$q0[$i] ^ $sa] ^ $s6] ^ $s2];
                    $S3[$i] = $m3[$q1[$q1[$q0[$i] ^ $sb] ^ $s7] ^ $s3];
                }
                break;
            default: // 32
                list ($sf, $se, $sd, $sc) = $this->_mdsrem($le_longs[1], $le_longs[2]);
                list ($sb, $sa, $s9, $s8) = $this->_mdsrem($le_longs[3], $le_longs[4]);
                list ($s7, $s6, $s5, $s4) = $this->_mdsrem($le_longs[5], $le_longs[6]);
                list ($s3, $s2, $s1, $s0) = $this->_mdsrem($le_longs[7], $le_longs[8]);
                for ($i = 0, $j = 1; $i < 40; $i+= 2, $j+= 2) {
                    $A = $m0[$q0[$q0[$q1[$q1[$i] ^ $key[25]] ^ $key[17]] ^ $key[ 9]] ^ $key[1]] ^
                         $m1[$q0[$q1[$q1[$q0[$i] ^ $key[26]] ^ $key[18]] ^ $key[10]] ^ $key[2]] ^
                         $m2[$q1[$q0[$q0[$q0[$i] ^ $key[27]] ^ $key[19]] ^ $key[11]] ^ $key[3]] ^
                         $m3[$q1[$q1[$q0[$q1[$i] ^ $key[28]] ^ $key[20]] ^ $key[12]] ^ $key[4]];
                    $B = $m0[$q0[$q0[$q1[$q1[$j] ^ $key[29]] ^ $key[21]] ^ $key[13]] ^ $key[5]] ^
                         $m1[$q0[$q1[$q1[$q0[$j] ^ $key[30]] ^ $key[22]] ^ $key[14]] ^ $key[6]] ^
                         $m2[$q1[$q0[$q0[$q0[$j] ^ $key[31]] ^ $key[23]] ^ $key[15]] ^ $key[7]] ^
                         $m3[$q1[$q1[$q0[$q1[$j] ^ $key[32]] ^ $key[24]] ^ $key[16]] ^ $key[8]];
                    $B = ($B << 8) | ($B >> 24 & 0xff);
                    $K[] = $A+= $B;
                    $K[] = (($A+= $B) << 9 | $A >> 23 & 0x1ff);
                }
                for ($i = 0; $i < 256; ++$i) {
                    $S0[$i] = $m0[$q0[$q0[$q1[$q1[$i] ^ $sc] ^ $s8] ^ $s4] ^ $s0];
                    $S1[$i] = $m1[$q0[$q1[$q1[$q0[$i] ^ $sd] ^ $s9] ^ $s5] ^ $s1];
                    $S2[$i] = $m2[$q1[$q0[$q0[$q0[$i] ^ $se] ^ $sa] ^ $s6] ^ $s2];
                    $S3[$i] = $m3[$q1[$q1[$q0[$q1[$i] ^ $sf] ^ $sb] ^ $s7] ^ $s3];
                }
        }

        $this->K  = $K;
        $this->S0 = $S0;
        $this->S1 = $S1;
        $this->S2 = $S2;
        $this->S3 = $S3;
    }

    /**
     * _mdsrem function using by the twofish cipher algorithm
     *
     * @access private
     * @param String $A
     * @param String $B
     * @return Array
     */
    function _mdsrem($A, $B)
    {
        // No gain by unrolling this loop.
        for ($i = 0; $i < 8; ++$i) {
            // Get most significant coefficient.
            $t = 0xff & ($B >> 24);

            // Shift the others up.
            $B = ($B << 8) | (0xff & ($A >> 24));
            $A<<= 8;

            $u = $t << 1;

            // Subtract the modular polynomial on overflow.
            if ($t & 0x80) {
                $u^= 0x14d;
            }

            // Remove t * (a * x^2 + 1).
            $B ^= $t ^ ($u << 16);

            // Form u = a*t + t/a = t*(a + 1/a).
            $u^= 0x7fffffff & ($t >> 1);

            // Add the modular polynomial on underflow.
            if ($t & 0x01) $u^= 0xa6 ;

            // Remove t * (a + 1/a) * (x^3 + x).
            $B^= ($u << 24) | ($u << 8);
        }

        return array(
            0xff & $B >> 24,
            0xff & $B >> 16,
            0xff & $B >>  8,
            0xff & $B);
    }

    /**
     * Encrypts a block
     *
     * @access private
     * @param String $in
     * @return String
     */
    function _encryptBlock($in)
    {
        $S0 = $this->S0;
        $S1 = $this->S1;
        $S2 = $this->S2;
        $S3 = $this->S3;
        $K  = $this->K;

        $in = unpack("V4", $in);
        $R0 = $K[0] ^ $in[1];
        $R1 = $K[1] ^ $in[2];
        $R2 = $K[2] ^ $in[3];
        $R3 = $K[3] ^ $in[4];

        $ki = 7;
        while ($ki < 39) {
            $t0 = $S0[ $R0        & 0xff] ^
                  $S1[($R0 >>  8) & 0xff] ^
                  $S2[($R0 >> 16) & 0xff] ^
                  $S3[($R0 >> 24) & 0xff];
            $t1 = $S0[($R1 >> 24) & 0xff] ^
                  $S1[ $R1        & 0xff] ^
                  $S2[($R1 >>  8) & 0xff] ^
                  $S3[($R1 >> 16) & 0xff];
            $R2^= $t0 + $t1 + $K[++$ki];
            $R2 = ($R2 >> 1 & 0x7fffffff) | ($R2 << 31);
            $R3 = ((($R3 >> 31) & 1) | ($R3 << 1)) ^ ($t0 + ($t1 << 1) + $K[++$ki]);

            $t0 = $S0[ $R2        & 0xff] ^
                  $S1[($R2 >>  8) & 0xff] ^
                  $S2[($R2 >> 16) & 0xff] ^
                  $S3[($R2 >> 24) & 0xff];
            $t1 = $S0[($R3 >> 24) & 0xff] ^
                  $S1[ $R3        & 0xff] ^
                  $S2[($R3 >>  8) & 0xff] ^
                  $S3[($R3 >> 16) & 0xff];
            $R0^= ($t0 + $t1 + $K[++$ki]);
            $R0 = ($R0 >> 1 & 0x7fffffff) | ($R0 << 31);
            $R1 = ((($R1 >> 31) & 1) | ($R1 << 1)) ^ ($t0 + ($t1 << 1) + $K[++$ki]);
        }

        // @codingStandardsIgnoreStart
        return pack("V4", $K[4] ^ $R2,
                          $K[5] ^ $R3,
                          $K[6] ^ $R0,
                          $K[7] ^ $R1);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Decrypts a block
     *
     * @access private
     * @param String $in
     * @return String
     */
    function _decryptBlock($in)
    {
        $S0 = $this->S0;
        $S1 = $this->S1;
        $S2 = $this->S2;
        $S3 = $this->S3;
        $K  = $this->K;

        $in = unpack("V4", $in);
        $R0 = $K[4] ^ $in[1];
        $R1 = $K[5] ^ $in[2];
        $R2 = $K[6] ^ $in[3];
        $R3 = $K[7] ^ $in[4];

        $ki = 40;
        while ($ki > 8) {
            $t0 = $S0[$R0       & 0xff] ^
                  $S1[$R0 >>  8 & 0xff] ^
                  $S2[$R0 >> 16 & 0xff] ^
                  $S3[$R0 >> 24 & 0xff];
            $t1 = $S0[$R1 >> 24 & 0xff] ^
                  $S1[$R1       & 0xff] ^
                  $S2[$R1 >>  8 & 0xff] ^
                  $S3[$R1 >> 16 & 0xff];
            $R3^= $t0 + ($t1 << 1) + $K[--$ki];
            $R3 = $R3 >> 1 & 0x7fffffff | $R3 << 31;
            $R2 = ($R2 >> 31 & 0x1 | $R2 << 1) ^ ($t0 + $t1 + $K[--$ki]);

            $t0 = $S0[$R2       & 0xff] ^
                  $S1[$R2 >>  8 & 0xff] ^
                  $S2[$R2 >> 16 & 0xff] ^
                  $S3[$R2 >> 24 & 0xff];
            $t1 = $S0[$R3 >> 24 & 0xff] ^
                  $S1[$R3       & 0xff] ^
                  $S2[$R3 >>  8 & 0xff] ^
                  $S3[$R3 >> 16 & 0xff];
            $R1^= $t0 + ($t1 << 1) + $K[--$ki];
            $R1 = $R1 >> 1 & 0x7fffffff | $R1 << 31;
            $R0 = ($R0 >> 31 & 0x1 | $R0 << 1) ^ ($t0 + $t1 + $K[--$ki]);
        }

        // @codingStandardsIgnoreStart
        return pack("V4", $K[0] ^ $R2,
                          $K[1] ^ $R3,
                          $K[2] ^ $R0,
                          $K[3] ^ $R1);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Setup the performance-optimized function for de/encrypt()
     *
     * @see Crypt_Base::_setupInlineCrypt()
     * @access private
     */
    function _setupInlineCrypt()
    {
        $lambda_functions =& Crypt_Twofish::_getLambdaFunctions();

        // Max. 10 Ultra-Hi-optimized inline-crypt functions. After that, we'll (still) create very fast code, but not the ultimate fast one.
        $gen_hi_opt_code = (bool)( count($lambda_functions) < 10 );

        switch (true) {
            case $gen_hi_opt_code:
                $code_hash = md5(str_pad("Crypt_Twofish, {$this->mode}, ", 32, "\0") . $this->key);
                break;
            default:
                $code_hash = "Crypt_Twofish, {$this->mode}";
        }

        if (!isset($lambda_functions[$code_hash])) {
            switch (true) {
                case $gen_hi_opt_code:
                    $K = $this->K;

                    $init_crypt = '
                        static $S0, $S1, $S2, $S3;
                        if (!$S0) {
                            for ($i = 0; $i < 256; ++$i) {
                                $S0[] = (int)$self->S0[$i];
                                $S1[] = (int)$self->S1[$i];
                                $S2[] = (int)$self->S2[$i];
                                $S3[] = (int)$self->S3[$i];
                            }
                        }
                    ';
                    break;
                default:
                    $K   = array();
                    for ($i = 0; $i < 40; ++$i) {
                        $K[] = '$K_' . $i;
                    }

                    $init_crypt = '
                        $S0 = $self->S0;
                        $S1 = $self->S1;
                        $S2 = $self->S2;
                        $S3 = $self->S3;
                        list(' . implode(',', $K) . ') = $self->K;
                    ';
            }

            // Generating encrypt code:
            $encrypt_block = '
                $in = unpack("V4", $in);
                $R0 = '.$K[0].' ^ $in[1];
                $R1 = '.$K[1].' ^ $in[2];
                $R2 = '.$K[2].' ^ $in[3];
                $R3 = '.$K[3].' ^ $in[4];
            ';
            for ($ki = 7, $i = 0; $i < 8; ++$i) {
                $encrypt_block.= '
                    $t0 = $S0[ $R0        & 0xff] ^
                          $S1[($R0 >>  8) & 0xff] ^
                          $S2[($R0 >> 16) & 0xff] ^
                          $S3[($R0 >> 24) & 0xff];
                    $t1 = $S0[($R1 >> 24) & 0xff] ^
                          $S1[ $R1        & 0xff] ^
                          $S2[($R1 >>  8) & 0xff] ^
                          $S3[($R1 >> 16) & 0xff];
                    $R2^= ($t0 + $t1 + '.$K[++$ki].');
                    $R2 = ($R2 >> 1 & 0x7fffffff) | ($R2 << 31);
                    $R3 = ((($R3 >> 31) & 1) | ($R3 << 1)) ^ ($t0 + ($t1 << 1) + '.$K[++$ki].');

                    $t0 = $S0[ $R2        & 0xff] ^
                          $S1[($R2 >>  8) & 0xff] ^
                          $S2[($R2 >> 16) & 0xff] ^
                          $S3[($R2 >> 24) & 0xff];
                    $t1 = $S0[($R3 >> 24) & 0xff] ^
                          $S1[ $R3        & 0xff] ^
                          $S2[($R3 >>  8) & 0xff] ^
                          $S3[($R3 >> 16) & 0xff];
                    $R0^= ($t0 + $t1 + '.$K[++$ki].');
                    $R0 = ($R0 >> 1 & 0x7fffffff) | ($R0 << 31);
                    $R1 = ((($R1 >> 31) & 1) | ($R1 << 1)) ^ ($t0 + ($t1 << 1) + '.$K[++$ki].');
                ';
            }
            $encrypt_block.= '
                $in = pack("V4", '.$K[4].' ^ $R2,
                                 '.$K[5].' ^ $R3,
                                 '.$K[6].' ^ $R0,
                                 '.$K[7].' ^ $R1);
            ';

            // Generating decrypt code:
            $decrypt_block = '
                $in = unpack("V4", $in);
                $R0 = '.$K[4].' ^ $in[1];
                $R1 = '.$K[5].' ^ $in[2];
                $R2 = '.$K[6].' ^ $in[3];
                $R3 = '.$K[7].' ^ $in[4];
            ';
            for ($ki = 40, $i = 0; $i < 8; ++$i) {
                $decrypt_block.= '
                    $t0 = $S0[$R0       & 0xff] ^
                          $S1[$R0 >>  8 & 0xff] ^
                          $S2[$R0 >> 16 & 0xff] ^
                          $S3[$R0 >> 24 & 0xff];
                    $t1 = $S0[$R1 >> 24 & 0xff] ^
                          $S1[$R1       & 0xff] ^
                          $S2[$R1 >>  8 & 0xff] ^
                          $S3[$R1 >> 16 & 0xff];
                    $R3^= $t0 + ($t1 << 1) + '.$K[--$ki].';
                    $R3 = $R3 >> 1 & 0x7fffffff | $R3 << 31;
                    $R2 = ($R2 >> 31 & 0x1 | $R2 << 1) ^ ($t0 + $t1 + '.$K[--$ki].');

                    $t0 = $S0[$R2       & 0xff] ^
                          $S1[$R2 >>  8 & 0xff] ^
                          $S2[$R2 >> 16 & 0xff] ^
                          $S3[$R2 >> 24 & 0xff];
                    $t1 = $S0[$R3 >> 24 & 0xff] ^
                          $S1[$R3       & 0xff] ^
                          $S2[$R3 >>  8 & 0xff] ^
                          $S3[$R3 >> 16 & 0xff];
                    $R1^= $t0 + ($t1 << 1) + '.$K[--$ki].';
                    $R1 = $R1 >> 1 & 0x7fffffff | $R1 << 31;
                    $R0 = ($R0 >> 31 & 0x1 | $R0 << 1) ^ ($t0 + $t1 + '.$K[--$ki].');
                ';
            }
            $decrypt_block.= '
                $in = pack("V4", '.$K[0].' ^ $R2,
                                 '.$K[1].' ^ $R3,
                                 '.$K[2].' ^ $R0,
                                 '.$K[3].' ^ $R1);
            ';

            $lambda_functions[$code_hash] = $this->_createInlineCryptFunction(
                array(
                   'init_crypt'    => $init_crypt,
                   'init_encrypt'  => '',
                   'init_decrypt'  => '',
                   'encrypt_block' => $encrypt_block,
                   'decrypt_block' => $decrypt_block
                )
            );
        }
        $this->inline_crypt = $lambda_functions[$code_hash];
    }
}
