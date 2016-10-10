<?php
/**
 * A Compatibility library with PHP 5.5's simplified password hashing API.
 *
 * @author Anthony Ferrara <ircmaxell@php.net>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2012 The Authors
 */

namespace {

    if (!defined('PASSWORD_BCRYPT')) {
        /**
         * PHPUnit Process isolation caches constants, but not function declarations.
         * So we need to check if the constants are defined separately from 
         * the functions to enable supporting process isolation in userland
         * code.
         */
        define('PASSWORD_BCRYPT', 1);
        define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);
        define('PASSWORD_BCRYPT_DEFAULT_COST', 10);
    }

    if (!function_exists('password_hash')) {

        /**
         * Hash the password using the specified algorithm
         *
         * @param string $password The password to hash
         * @param int    $algo     The algorithm to use (Defined by PASSWORD_* constants)
         * @param array  $options  The options for the algorithm to use
         *
         * @return string|false The hashed password, or false on error.
         */
        function password_hash($password, $algo, array $options = array()) {
            if (!function_exists('crypt')) {
                trigger_error("Crypt must be loaded for password_hash to function", E_USER_WARNING);
                return null;
            }
            if (is_null($password) || is_int($password)) {
                $password = (string) $password;
            }
            if (!is_string($password)) {
                trigger_error("password_hash(): Password must be a string", E_USER_WARNING);
                return null;
            }
            if (!is_int($algo)) {
                trigger_error("password_hash() expects parameter 2 to be long, " . gettype($algo) . " given", E_USER_WARNING);
                return null;
            }
            $resultLength = 0;
            switch ($algo) {
                case PASSWORD_BCRYPT:
                    $cost = PASSWORD_BCRYPT_DEFAULT_COST;
                    if (isset($options['cost'])) {
                        $cost = $options['cost'];
                        if ($cost < 4 || $cost > 31) {
                            trigger_error(sprintf("password_hash(): Invalid bcrypt cost parameter specified: %d", $cost), E_USER_WARNING);
                            return null;
                        }
                    }
                    // The length of salt to generate
                    $raw_salt_len = 16;
                    // The length required in the final serialization
                    $required_salt_len = 22;
                    $hash_format = sprintf("$2y$%02d$", $cost);
                    // The expected length of the final crypt() output
                    $resultLength = 60;
                    break;
                default:
                    trigger_error(sprintf("password_hash(): Unknown password hashing algorithm: %s", $algo), E_USER_WARNING);
                    return null;
            }
            $salt_requires_encoding = false;
            if (isset($options['salt'])) {
                switch (gettype($options['salt'])) {
                    case 'NULL':
                    case 'boolean':
                    case 'integer':
                    case 'double':
                    case 'string':
                        $salt = (string) $options['salt'];
                        break;
                    case 'object':
                        if (method_exists($options['salt'], '__tostring')) {
                            $salt = (string) $options['salt'];
                            break;
                        }
                    case 'array':
                    case 'resource':
                    default:
                        trigger_error('password_hash(): Non-string salt parameter supplied', E_USER_WARNING);
                        return null;
                }
                if (PasswordCompat\binary\_strlen($salt) < $required_salt_len) {
                    trigger_error(sprintf("password_hash(): Provided salt is too short: %d expecting %d", PasswordCompat\binary\_strlen($salt), $required_salt_len), E_USER_WARNING);
                    return null;
                } elseif (0 == preg_match('#^[a-zA-Z0-9./]+$#D', $salt)) {
                    $salt_requires_encoding = true;
                }
            } else {
                $buffer = '';
                $buffer_valid = false;
                if (function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
                    $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
                    if ($buffer) {
                        $buffer_valid = true;
                    }
                }
                if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
                    $buffer = openssl_random_pseudo_bytes($raw_salt_len);
                    if ($buffer) {
                        $buffer_valid = true;
                    }
                }
                if (!$buffer_valid && @is_readable('/dev/urandom')) {
                    $f = fopen('/dev/urandom', 'r');
                    $read = PasswordCompat\binary\_strlen($buffer);
                    while ($read < $raw_salt_len) {
                        $buffer .= fread($f, $raw_salt_len - $read);
                        $read = PasswordCompat\binary\_strlen($buffer);
                    }
                    fclose($f);
                    if ($read >= $raw_salt_len) {
                        $buffer_valid = true;
                    }
                }
                if (!$buffer_valid || PasswordCompat\binary\_strlen($buffer) < $raw_salt_len) {
                    $bl = PasswordCompat\binary\_strlen($buffer);
                    for ($i = 0; $i < $raw_salt_len; $i++) {
                        if ($i < $bl) {
                            $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                        } else {
                            $buffer .= chr(mt_rand(0, 255));
                        }
                    }
                }
                $salt = $buffer;
                $salt_requires_encoding = true;
            }
            if ($salt_requires_encoding) {
                // encode string with the Base64 variant used by crypt
                $base64_digits =
                    'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
                $bcrypt64_digits =
                    './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

                $base64_string = base64_encode($salt);
                $salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);
            }
            $salt = PasswordCompat\binary\_substr($salt, 0, $required_salt_len);

            $hash = $hash_format . $salt;

            $ret = crypt($password, $hash);

            if (!is_string($ret) || PasswordCompat\binary\_strlen($ret) != $resultLength) {
                return false;
            }

            return $ret;
        }

        /**
         * Get information about the password hash. Returns an array of the information
         * that was used to generate the password hash.
         *
         * array(
         *    'algo' => 1,
         *    'algoName' => 'bcrypt',
         *    'options' => array(
         *        'cost' => PASSWORD_BCRYPT_DEFAULT_COST,
         *    ),
         * )
         *
         * @param string $hash The password hash to extract info from
         *
         * @return array The array of information about the hash.
         */
        function password_get_info($hash) {
            $return = array(
                'algo' => 0,
                'algoName' => 'unknown',
                'options' => array(),
            );
            if (PasswordCompat\binary\_substr($hash, 0, 4) == '$2y$' && PasswordCompat\binary\_strlen($hash) == 60) {
                $return['algo'] = PASSWORD_BCRYPT;
                $return['algoName'] = 'bcrypt';
                list($cost) = sscanf($hash, "$2y$%d$");
                $return['options']['cost'] = $cost;
            }
            return $return;
        }

        /**
         * Determine if the password hash needs to be rehashed according to the options provided
         *
         * If the answer is true, after validating the password using password_verify, rehash it.
         *
         * @param string $hash    The hash to test
         * @param int    $algo    The algorithm used for new password hashes
         * @param array  $options The options array passed to password_hash
         *
         * @return boolean True if the password needs to be rehashed.
         */
        function password_needs_rehash($hash, $algo, array $options = array()) {
            $info = password_get_info($hash);
            if ($info['algo'] != $algo) {
                return true;
            }
            switch ($algo) {
                case PASSWORD_BCRYPT:
                    $cost = isset($options['cost']) ? $options['cost'] : PASSWORD_BCRYPT_DEFAULT_COST;
                    if ($cost != $info['options']['cost']) {
                        return true;
                    }
                    break;
            }
            return false;
        }

        /**
         * Verify a password against a hash using a timing attack resistant approach
         *
         * @param string $password The password to verify
         * @param string $hash     The hash to verify against
         *
         * @return boolean If the password matches the hash
         */
        function password_verify($password, $hash) {
            if (!function_exists('crypt')) {
                trigger_error("Crypt must be loaded for password_verify to function", E_USER_WARNING);
                return false;
            }
            $ret = crypt($password, $hash);
            if (!is_string($ret) || PasswordCompat\binary\_strlen($ret) != PasswordCompat\binary\_strlen($hash) || PasswordCompat\binary\_strlen($ret) <= 13) {
                return false;
            }

            $status = 0;
            for ($i = 0; $i < PasswordCompat\binary\_strlen($ret); $i++) {
                $status |= (ord($ret[$i]) ^ ord($hash[$i]));
            }

            return $status === 0;
        }
    }

}

namespace PasswordCompat\binary {

    if (!function_exists('PasswordCompat\\binary\\_strlen')) {

        /**
         * Count the number of bytes in a string
         *
         * We cannot simply use strlen() for this, because it might be overwritten by the mbstring extension.
         * In this case, strlen() will count the number of *characters* based on the internal encoding. A
         * sequence of bytes might be regarded as a single multibyte character.
         *
         * @param string $binary_string The input string
         *
         * @internal
         * @return int The number of bytes
         */
        function _strlen($binary_string) {
            if (function_exists('mb_strlen')) {
                return mb_strlen($binary_string, '8bit');
            }
            return strlen($binary_string);
        }

        /**
         * Get a substring based on byte limits
         *
         * @see _strlen()
         *
         * @param string $binary_string The input string
         * @param int    $start
         * @param int    $length
         *
         * @internal
         * @return string The substring
         */
        function _substr($binary_string, $start, $length) {
            if (function_exists('mb_substr')) {
                return mb_substr($binary_string, $start, $length, '8bit');
            }
            return substr($binary_string, $start, $length);
        }

        /**
         * Check if current PHP version is compatible with the library
         *
         * @return boolean the check result
         */
        function check() {
            static $pass = NULL;

            if (is_null($pass)) {
                if (function_exists('crypt')) {
                    $hash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
                    $test = crypt("password", $hash);
                    $pass = $test == $hash;
                } else {
                    $pass = false;
                }
            }
            return $pass;
        }

    }
}