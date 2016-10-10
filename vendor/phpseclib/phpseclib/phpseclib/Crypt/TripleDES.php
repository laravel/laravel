<?php

/**
 * Pure-PHP implementation of Triple DES.
 *
 * Uses mcrypt, if available, and an internal implementation, otherwise.  Operates in the EDE3 mode (encrypt-decrypt-encrypt).
 *
 * PHP versions 4 and 5
 *
 * Here's a short example of how to use this library:
 * <code>
 * <?php
 *    include 'Crypt/TripleDES.php';
 *
 *    $des = new Crypt_TripleDES();
 *
 *    $des->setKey('abcdefghijklmnopqrstuvwx');
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
 * @package   Crypt_TripleDES
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2007 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */

/**
 * Include Crypt_DES
 */
if (!class_exists('Crypt_DES')) {
    include_once 'DES.php';
}

/**
 * Encrypt / decrypt using inner chaining
 *
 * Inner chaining is used by SSH-1 and is generally considered to be less secure then outer chaining (CRYPT_DES_MODE_CBC3).
 */
define('CRYPT_DES_MODE_3CBC', -2);

/**
 * Encrypt / decrypt using outer chaining
 *
 * Outer chaining is used by SSH-2 and when the mode is set to CRYPT_DES_MODE_CBC.
 */
define('CRYPT_DES_MODE_CBC3', CRYPT_DES_MODE_CBC);

/**
 * Pure-PHP implementation of Triple DES.
 *
 * @package Crypt_TripleDES
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
class Crypt_TripleDES extends Crypt_DES
{
    /**
     * The default password key_size used by setPassword()
     *
     * @see Crypt_DES::password_key_size
     * @see Crypt_Base::password_key_size
     * @see Crypt_Base::setPassword()
     * @var Integer
     * @access private
     */
    var $password_key_size = 24;

    /**
     * The default salt used by setPassword()
     *
     * @see Crypt_Base::password_default_salt
     * @see Crypt_Base::setPassword()
     * @var String
     * @access private
     */
    var $password_default_salt = 'phpseclib';

    /**
     * The namespace used by the cipher for its constants.
     *
     * @see Crypt_DES::const_namespace
     * @see Crypt_Base::const_namespace
     * @var String
     * @access private
     */
    var $const_namespace = 'DES';

    /**
     * The mcrypt specific name of the cipher
     *
     * @see Crypt_DES::cipher_name_mcrypt
     * @see Crypt_Base::cipher_name_mcrypt
     * @var String
     * @access private
     */
    var $cipher_name_mcrypt = 'tripledes';

    /**
     * Optimizing value while CFB-encrypting
     *
     * @see Crypt_Base::cfb_init_len
     * @var Integer
     * @access private
     */
    var $cfb_init_len = 750;

    /**
     * max possible size of $key
     *
     * @see Crypt_TripleDES::setKey()
     * @see Crypt_DES::setKey()
     * @var String
     * @access private
     */
    var $key_size_max = 24;

    /**
     * Internal flag whether using CRYPT_DES_MODE_3CBC or not
     *
     * @var Boolean
     * @access private
     */
    var $mode_3cbc;

    /**
     * The Crypt_DES objects
     *
     * Used only if $mode_3cbc === true
     *
     * @var Array
     * @access private
     */
    var $des;

    /**
     * Default Constructor.
     *
     * Determines whether or not the mcrypt extension should be used.
     *
     * $mode could be:
     *
     * - CRYPT_DES_MODE_ECB
     *
     * - CRYPT_DES_MODE_CBC
     *
     * - CRYPT_DES_MODE_CTR
     *
     * - CRYPT_DES_MODE_CFB
     *
     * - CRYPT_DES_MODE_OFB
     *
     * - CRYPT_DES_MODE_3CBC
     *
     * If not explicitly set, CRYPT_DES_MODE_CBC will be used.
     *
     * @see Crypt_DES::Crypt_DES()
     * @see Crypt_Base::Crypt_Base()
     * @param optional Integer $mode
     * @access public
     */
    function Crypt_TripleDES($mode = CRYPT_DES_MODE_CBC)
    {
        switch ($mode) {
            // In case of CRYPT_DES_MODE_3CBC, we init as CRYPT_DES_MODE_CBC
            // and additional flag us internally as 3CBC
            case CRYPT_DES_MODE_3CBC:
                parent::Crypt_Base(CRYPT_DES_MODE_CBC);
                $this->mode_3cbc = true;

                // This three $des'es will do the 3CBC work (if $key > 64bits)
                $this->des = array(
                    new Crypt_DES(CRYPT_DES_MODE_CBC),
                    new Crypt_DES(CRYPT_DES_MODE_CBC),
                    new Crypt_DES(CRYPT_DES_MODE_CBC),
                );

                // we're going to be doing the padding, ourselves, so disable it in the Crypt_DES objects
                $this->des[0]->disablePadding();
                $this->des[1]->disablePadding();
                $this->des[2]->disablePadding();
                break;
            // If not 3CBC, we init as usual
            default:
                parent::Crypt_Base($mode);
        }
    }

    /**
     * Sets the initialization vector. (optional)
     *
     * SetIV is not required when CRYPT_DES_MODE_ECB is being used.  If not explicitly set, it'll be assumed
     * to be all zero's.
     *
     * @see Crypt_Base::setIV()
     * @access public
     * @param String $iv
     */
    function setIV($iv)
    {
        parent::setIV($iv);
        if ($this->mode_3cbc) {
            $this->des[0]->setIV($iv);
            $this->des[1]->setIV($iv);
            $this->des[2]->setIV($iv);
        }
    }

    /**
     * Sets the key.
     *
     * Keys can be of any length.  Triple DES, itself, can use 128-bit (eg. strlen($key) == 16) or
     * 192-bit (eg. strlen($key) == 24) keys.  This function pads and truncates $key as appropriate.
     *
     * DES also requires that every eighth bit be a parity bit, however, we'll ignore that.
     *
     * If the key is not explicitly set, it'll be assumed to be all null bytes.
     *
     * @access public
     * @see Crypt_DES::setKey()
     * @see Crypt_Base::setKey()
     * @param String $key
     */
    function setKey($key)
    {
        $length = strlen($key);
        if ($length > 8) {
            $key = str_pad(substr($key, 0, 24), 24, chr(0));
            // if $key is between 64 and 128-bits, use the first 64-bits as the last, per this:
            // http://php.net/function.mcrypt-encrypt#47973
            //$key = $length <= 16 ? substr_replace($key, substr($key, 0, 8), 16) : substr($key, 0, 24);
        } else {
            $key = str_pad($key, 8, chr(0));
        }
        parent::setKey($key);

        // And in case of CRYPT_DES_MODE_3CBC:
        // if key <= 64bits we not need the 3 $des to work,
        // because we will then act as regular DES-CBC with just a <= 64bit key.
        // So only if the key > 64bits (> 8 bytes) we will call setKey() for the 3 $des.
        if ($this->mode_3cbc && $length > 8) {
            $this->des[0]->setKey(substr($key,  0, 8));
            $this->des[1]->setKey(substr($key,  8, 8));
            $this->des[2]->setKey(substr($key, 16, 8));
        }
    }

    /**
     * Encrypts a message.
     *
     * @see Crypt_Base::encrypt()
     * @access public
     * @param String $plaintext
     * @return String $cipertext
     */
    function encrypt($plaintext)
    {
        // parent::en/decrypt() is able to do all the work for all modes and keylengths,
        // except for: CRYPT_DES_MODE_3CBC (inner chaining CBC) with a key > 64bits

        // if the key is smaller then 8, do what we'd normally do
        if ($this->mode_3cbc && strlen($this->key) > 8) {
            return $this->des[2]->encrypt(
                $this->des[1]->decrypt(
                    $this->des[0]->encrypt(
                        $this->_pad($plaintext)
                    )
                )
            );
        }

        return parent::encrypt($plaintext);
    }

    /**
     * Decrypts a message.
     *
     * @see Crypt_Base::decrypt()
     * @access public
     * @param String $ciphertext
     * @return String $plaintext
     */
    function decrypt($ciphertext)
    {
        if ($this->mode_3cbc && strlen($this->key) > 8) {
            return $this->_unpad(
                $this->des[0]->decrypt(
                    $this->des[1]->encrypt(
                        $this->des[2]->decrypt(
                            str_pad($ciphertext, (strlen($ciphertext) + 7) & 0xFFFFFFF8, "\0")
                        )
                    )
                )
            );
        }

        return parent::decrypt($ciphertext);
    }

    /**
     * Treat consecutive "packets" as if they are a continuous buffer.
     *
     * Say you have a 16-byte plaintext $plaintext.  Using the default behavior, the two following code snippets
     * will yield different outputs:
     *
     * <code>
     *    echo $des->encrypt(substr($plaintext, 0, 8));
     *    echo $des->encrypt(substr($plaintext, 8, 8));
     * </code>
     * <code>
     *    echo $des->encrypt($plaintext);
     * </code>
     *
     * The solution is to enable the continuous buffer.  Although this will resolve the above discrepancy, it creates
     * another, as demonstrated with the following:
     *
     * <code>
     *    $des->encrypt(substr($plaintext, 0, 8));
     *    echo $des->decrypt($des->encrypt(substr($plaintext, 8, 8)));
     * </code>
     * <code>
     *    echo $des->decrypt($des->encrypt(substr($plaintext, 8, 8)));
     * </code>
     *
     * With the continuous buffer disabled, these would yield the same output.  With it enabled, they yield different
     * outputs.  The reason is due to the fact that the initialization vector's change after every encryption /
     * decryption round when the continuous buffer is enabled.  When it's disabled, they remain constant.
     *
     * Put another way, when the continuous buffer is enabled, the state of the Crypt_DES() object changes after each
     * encryption / decryption round, whereas otherwise, it'd remain constant.  For this reason, it's recommended that
     * continuous buffers not be used.  They do offer better security and are, in fact, sometimes required (SSH uses them),
     * however, they are also less intuitive and more likely to cause you problems.
     *
     * @see Crypt_Base::enableContinuousBuffer()
     * @see Crypt_TripleDES::disableContinuousBuffer()
     * @access public
     */
    function enableContinuousBuffer()
    {
        parent::enableContinuousBuffer();
        if ($this->mode_3cbc) {
            $this->des[0]->enableContinuousBuffer();
            $this->des[1]->enableContinuousBuffer();
            $this->des[2]->enableContinuousBuffer();
        }
    }

    /**
     * Treat consecutive packets as if they are a discontinuous buffer.
     *
     * The default behavior.
     *
     * @see Crypt_Base::disableContinuousBuffer()
     * @see Crypt_TripleDES::enableContinuousBuffer()
     * @access public
     */
    function disableContinuousBuffer()
    {
        parent::disableContinuousBuffer();
        if ($this->mode_3cbc) {
            $this->des[0]->disableContinuousBuffer();
            $this->des[1]->disableContinuousBuffer();
            $this->des[2]->disableContinuousBuffer();
        }
    }

    /**
     * Creates the key schedule
     *
     * @see Crypt_DES::_setupKey()
     * @see Crypt_Base::_setupKey()
     * @access private
     */
    function _setupKey()
    {
        switch (true) {
            // if $key <= 64bits we configure our internal pure-php cipher engine
            // to act as regular [1]DES, not as 3DES. mcrypt.so::tripledes does the same.
            case strlen($this->key) <= 8:
                $this->des_rounds = 1;
                break;

            // otherwise, if $key > 64bits, we configure our engine to work as 3DES.
            default:
                $this->des_rounds = 3;

                // (only) if 3CBC is used we have, of course, to setup the $des[0-2] keys also separately.
                if ($this->mode_3cbc) {
                    $this->des[0]->_setupKey();
                    $this->des[1]->_setupKey();
                    $this->des[2]->_setupKey();

                    // because $des[0-2] will, now, do all the work we can return here
                    // not need unnecessary stress parent::_setupKey() with our, now unused, $key.
                    return;
                }
        }
        // setup our key
        parent::_setupKey();
    }
}
