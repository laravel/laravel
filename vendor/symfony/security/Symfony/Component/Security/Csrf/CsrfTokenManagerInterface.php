<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Csrf;

/**
 * Manages CSRF tokens.
 *
 * @since  2.4
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
interface CsrfTokenManagerInterface
{
    /**
     * Returns a CSRF token for the given ID.
     *
     * If previously no token existed for the given ID, a new token is
     * generated. Otherwise the existing token is returned (with the same value,
     * not the same instance).
     *
     * @param string $tokenId The token ID. You may choose an arbitrary value
     *                        for the ID
     *
     * @return CsrfToken The CSRF token
     */
    public function getToken($tokenId);

    /**
     * Generates a new token value for the given ID.
     *
     * This method will generate a new token for the given token ID, independent
     * of whether a token value previously existed or not. It can be used to
     * enforce once-only tokens in environments with high security needs.
     *
     * @param string $tokenId The token ID. You may choose an arbitrary value
     *                        for the ID
     *
     * @return CsrfToken The CSRF token
     */
    public function refreshToken($tokenId);

    /**
     * Invalidates the CSRF token with the given ID, if one exists.
     *
     * @param string $tokenId The token ID
     *
     * @return string|null Returns the removed token value if one existed, NULL
     *                     otherwise
     */
    public function removeToken($tokenId);

    /**
     * Returns whether the given CSRF token is valid.
     *
     * @param CsrfToken $token A CSRF token
     *
     * @return bool    Returns true if the token is valid, false otherwise
     */
    public function isTokenValid(CsrfToken $token);
}
