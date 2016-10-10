<?php

/**
 * Random Number Generator
 *
 * The idea behind this function is that it can be easily replaced with your own crypt_random_string()
 * function. eg. maybe you have a better source of entropy for creating the initial states or whatever.
 *
 * PHP versions 4 and 5
 *
 * Here's a short example of how to use this library:
 * <code>
 * <?php
 *    include 'Crypt/Random.php';
 *
 *    echo bin2hex(crypt_random_string(8));
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
 * @package   Crypt_Random
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2007 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */

// laravel is a PHP framework that utilizes phpseclib. laravel workbenches may, independently,
// have phpseclib as a requirement as well. if you're developing such a program you may encounter
// a "Cannot redeclare crypt_random_string()" error.
if (!function_exists('crypt_random_string')) {
    /**
     * "Is Windows" test
     *
     * @access private
     */
    define('CRYPT_RANDOM_IS_WINDOWS', strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

    /**
     * Generate a random string.
     *
     * Although microoptimizations are generally discouraged as they impair readability this function is ripe with
     * microoptimizations because this function has the potential of being called a huge number of times.
     * eg. for RSA key generation.
     *
     * @param Integer $length
     * @return String
     * @access public
     */
    function crypt_random_string($length)
    {
        if (CRYPT_RANDOM_IS_WINDOWS) {
            // method 1. prior to PHP 5.3 this would call rand() on windows hence the function_exists('class_alias') call.
            // ie. class_alias is a function that was introduced in PHP 5.3
            if (function_exists('mcrypt_create_iv') && function_exists('class_alias')) {
                return mcrypt_create_iv($length);
            }
            // method 2. openssl_random_pseudo_bytes was introduced in PHP 5.3.0 but prior to PHP 5.3.4 there was,
            // to quote <http://php.net/ChangeLog-5.php#5.3.4>, "possible blocking behavior". as of 5.3.4
            // openssl_random_pseudo_bytes and mcrypt_create_iv do the exact same thing on Windows. ie. they both
            // call php_win32_get_random_bytes():
            //
            // https://github.com/php/php-src/blob/7014a0eb6d1611151a286c0ff4f2238f92c120d6/ext/openssl/openssl.c#L5008
            // https://github.com/php/php-src/blob/7014a0eb6d1611151a286c0ff4f2238f92c120d6/ext/mcrypt/mcrypt.c#L1392
            //
            // php_win32_get_random_bytes() is defined thusly:
            //
            // https://github.com/php/php-src/blob/7014a0eb6d1611151a286c0ff4f2238f92c120d6/win32/winutil.c#L80
            //
            // we're calling it, all the same, in the off chance that the mcrypt extension is not available
            if (function_exists('openssl_random_pseudo_bytes') && version_compare(PHP_VERSION, '5.3.4', '>=')) {
                return openssl_random_pseudo_bytes($length);
            }
        } else {
            // method 1. the fastest
            if (function_exists('openssl_random_pseudo_bytes')) {
                return openssl_random_pseudo_bytes($length);
            }
            // method 2
            static $fp = true;
            if ($fp === true) {
                // warning's will be output unles the error suppression operator is used. errors such as
                // "open_basedir restriction in effect", "Permission denied", "No such file or directory", etc.
                $fp = @fopen('/dev/urandom', 'rb');
            }
            if ($fp !== true && $fp !== false) { // surprisingly faster than !is_bool() or is_resource()
                return fread($fp, $length);
            }
            // method 3. pretty much does the same thing as method 2 per the following url:
            // https://github.com/php/php-src/blob/7014a0eb6d1611151a286c0ff4f2238f92c120d6/ext/mcrypt/mcrypt.c#L1391
            // surprisingly slower than method 2. maybe that's because mcrypt_create_iv does a bunch of error checking that we're
            // not doing. regardless, this'll only be called if this PHP script couldn't open /dev/urandom due to open_basedir
            // restrictions or some such
            if (function_exists('mcrypt_create_iv')) {
                return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
            }
        }
        // at this point we have no choice but to use a pure-PHP CSPRNG

        // cascade entropy across multiple PHP instances by fixing the session and collecting all
        // environmental variables, including the previous session data and the current session
        // data.
        //
        // mt_rand seeds itself by looking at the PID and the time, both of which are (relatively)
        // easy to guess at. linux uses mouse clicks, keyboard timings, etc, as entropy sources, but
        // PHP isn't low level to be able to use those as sources and on a web server there's not likely
        // going to be a ton of keyboard or mouse action. web servers do have one thing that we can use
        // however, a ton of people visiting the website. obviously you don't want to base your seeding
        // soley on parameters a potential attacker sends but (1) not everything in $_SERVER is controlled
        // by the user and (2) this isn't just looking at the data sent by the current user - it's based
        // on the data sent by all users. one user requests the page and a hash of their info is saved.
        // another user visits the page and the serialization of their data is utilized along with the
        // server envirnment stuff and a hash of the previous http request data (which itself utilizes
        // a hash of the session data before that). certainly an attacker should be assumed to have
        // full control over his own http requests. he, however, is not going to have control over
        // everyone's http requests.
        static $crypto = false, $v;
        if ($crypto === false) {
            // save old session data
            $old_session_id = session_id();
            $old_use_cookies = ini_get('session.use_cookies');
            $old_session_cache_limiter = session_cache_limiter();
            $_OLD_SESSION = isset($_SESSION) ? $_SESSION : false;
            if ($old_session_id != '') {
                session_write_close();
            }

            session_id(1);
            ini_set('session.use_cookies', 0);
            session_cache_limiter('');
            session_start();

            $v = $seed = $_SESSION['seed'] = pack('H*', sha1(
                serialize($_SERVER) .
                serialize($_POST) .
                serialize($_GET) .
                serialize($_COOKIE) .
                serialize($GLOBALS) .
                serialize($_SESSION) .
                serialize($_OLD_SESSION)
            ));
            if (!isset($_SESSION['count'])) {
                $_SESSION['count'] = 0;
            }
            $_SESSION['count']++;

            session_write_close();

            // restore old session data
            if ($old_session_id != '') {
                session_id($old_session_id);
                session_start();
                ini_set('session.use_cookies', $old_use_cookies);
                session_cache_limiter($old_session_cache_limiter);
            } else {
               if ($_OLD_SESSION !== false) {
                   $_SESSION = $_OLD_SESSION;
                   unset($_OLD_SESSION);
                } else {
                    unset($_SESSION);
                }
            }

            // in SSH2 a shared secret and an exchange hash are generated through the key exchange process.
            // the IV client to server is the hash of that "nonce" with the letter A and for the encryption key it's the letter C.
            // if the hash doesn't produce enough a key or an IV that's long enough concat successive hashes of the
            // original hash and the current hash. we'll be emulating that. for more info see the following URL:
            //
            // http://tools.ietf.org/html/rfc4253#section-7.2
            //
            // see the is_string($crypto) part for an example of how to expand the keys
            $key = pack('H*', sha1($seed . 'A'));
            $iv = pack('H*', sha1($seed . 'C'));

            // ciphers are used as per the nist.gov link below. also, see this link:
            //
            // http://en.wikipedia.org/wiki/Cryptographically_secure_pseudorandom_number_generator#Designs_based_on_cryptographic_primitives
            switch (true) {
                case phpseclib_resolve_include_path('Crypt/AES.php'):
                    if (!class_exists('Crypt_AES')) {
                        include_once 'AES.php';
                    }
                    $crypto = new Crypt_AES(CRYPT_AES_MODE_CTR);
                    break;
                case phpseclib_resolve_include_path('Crypt/Twofish.php'):
                    if (!class_exists('Crypt_Twofish')) {
                        include_once 'Twofish.php';
                    }
                    $crypto = new Crypt_Twofish(CRYPT_TWOFISH_MODE_CTR);
                    break;
                case phpseclib_resolve_include_path('Crypt/Blowfish.php'):
                    if (!class_exists('Crypt_Blowfish')) {
                        include_once 'Blowfish.php';
                    }
                    $crypto = new Crypt_Blowfish(CRYPT_BLOWFISH_MODE_CTR);
                    break;
                case phpseclib_resolve_include_path('Crypt/TripleDES.php'):
                    if (!class_exists('Crypt_TripleDES')) {
                        include_once 'TripleDES.php';
                    }
                    $crypto = new Crypt_TripleDES(CRYPT_DES_MODE_CTR);
                    break;
                case phpseclib_resolve_include_path('Crypt/DES.php'):
                    if (!class_exists('Crypt_DES')) {
                        include_once 'DES.php';
                    }
                    $crypto = new Crypt_DES(CRYPT_DES_MODE_CTR);
                    break;
                case phpseclib_resolve_include_path('Crypt/RC4.php'):
                    if (!class_exists('Crypt_RC4')) {
                        include_once 'RC4.php';
                    }
                    $crypto = new Crypt_RC4();
                    break;
                default:
                    user_error('crypt_random_string requires at least one symmetric cipher be loaded');
                    return false;
            }

            $crypto->setKey($key);
            $crypto->setIV($iv);
            $crypto->enableContinuousBuffer();
        }

        //return $crypto->encrypt(str_repeat("\0", $length));

        // the following is based off of ANSI X9.31:
        //
        // http://csrc.nist.gov/groups/STM/cavp/documents/rng/931rngext.pdf
        //
        // OpenSSL uses that same standard for it's random numbers:
        //
        // http://www.opensource.apple.com/source/OpenSSL/OpenSSL-38/openssl/fips-1.0/rand/fips_rand.c
        // (do a search for "ANS X9.31 A.2.4")
        $result = '';
        while (strlen($result) < $length) {
            $i = $crypto->encrypt(microtime()); // strlen(microtime()) == 21
            $r = $crypto->encrypt($i ^ $v); // strlen($v) == 20
            $v = $crypto->encrypt($r ^ $i); // strlen($r) == 20
            $result.= $r;
        }
        return substr($result, 0, $length);
    }
}

if (!function_exists('phpseclib_resolve_include_path')) {
    /**
     * Resolve filename against the include path.
     *
     * Wrapper around stream_resolve_include_path() (which was introduced in
     * PHP 5.3.2) with fallback implementation for earlier PHP versions.
     *
     * @param string $filename
     * @return mixed Filename (string) on success, false otherwise.
     * @access public
     */
    function phpseclib_resolve_include_path($filename)
    {
        if (function_exists('stream_resolve_include_path')) {
            return stream_resolve_include_path($filename);
        }

        // handle non-relative paths
        if (file_exists($filename)) {
            return realpath($filename);
        }

        $paths = PATH_SEPARATOR == ':' ?
            preg_split('#(?<!phar):#', get_include_path()) :
            explode(PATH_SEPARATOR, get_include_path());
        foreach ($paths as $prefix) {
            // path's specified in include_path don't always end in /
            $ds = substr($prefix, -1) == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR;
            $file = $prefix . $ds . $filename;
            if (file_exists($file)) {
                return realpath($file);
            }
        }

        return false;
    }
}
