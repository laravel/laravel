<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Encoder;

use Symfony\Component\Security\Core\Util\StringUtils;

/**
 * BasePasswordEncoder is the base class for all password encoders.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class BasePasswordEncoder implements PasswordEncoderInterface
{
    const MAX_PASSWORD_LENGTH = 4096;

    /**
     * Demerges a merge password and salt string.
     *
     * @param string $mergedPasswordSalt The merged password and salt string
     *
     * @return array An array where the first element is the password and the second the salt
     */
    protected function demergePasswordAndSalt($mergedPasswordSalt)
    {
        if (empty($mergedPasswordSalt)) {
            return array('', '');
        }

        $password = $mergedPasswordSalt;
        $salt = '';
        $saltBegins = strrpos($mergedPasswordSalt, '{');

        if (false !== $saltBegins && $saltBegins + 1 < strlen($mergedPasswordSalt)) {
            $salt = substr($mergedPasswordSalt, $saltBegins + 1, -1);
            $password = substr($mergedPasswordSalt, 0, $saltBegins);
        }

        return array($password, $salt);
    }

    /**
     * Merges a password and a salt.
     *
     * @param string $password the password to be used
     * @param string $salt     the salt to be used
     *
     * @return string a merged password and salt
     *
     * @throws \InvalidArgumentException
     */
    protected function mergePasswordAndSalt($password, $salt)
    {
        if (empty($salt)) {
            return $password;
        }

        if (false !== strrpos($salt, '{') || false !== strrpos($salt, '}')) {
            throw new \InvalidArgumentException('Cannot use { or } in salt.');
        }

        return $password.'{'.$salt.'}';
    }

    /**
     * Compares two passwords.
     *
     * This method implements a constant-time algorithm to compare passwords to
     * avoid (remote) timing attacks.
     *
     * @param string $password1 The first password
     * @param string $password2 The second password
     *
     * @return bool    true if the two passwords are the same, false otherwise
     */
    protected function comparePasswords($password1, $password2)
    {
        return StringUtils::equals($password1, $password2);
    }

    /**
     * Checks if the password is too long.
     *
     * @return bool    true if the password is too long, false otherwise
     */
    protected function isPasswordTooLong($password)
    {
        return strlen($password) > self::MAX_PASSWORD_LENGTH;
    }
}
