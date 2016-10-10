<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Csrf\TokenStorage;

/**
 * Stores CSRF tokens.
 *
 * @since  2.4
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
interface TokenStorageInterface
{
    /**
     * Reads a stored CSRF token.
     *
     * @param string $tokenId The token ID
     *
     * @return string The stored token
     *
     * @throws \Symfony\Component\Security\Csrf\Exception\TokenNotFoundException If the token ID does not exist
     */
    public function getToken($tokenId);

    /**
     * Stores a CSRF token.
     *
     * @param string $tokenId The token ID
     * @param string $token   The CSRF token
     */
    public function setToken($tokenId, $token);

    /**
     * Removes a CSRF token.
     *
     * @param string $tokenId The token ID
     *
     * @return string|null Returns the removed token if one existed, NULL
     *                     otherwise
     */
    public function removeToken($tokenId);

    /**
     * Checks whether a token with the given token ID exists.
     *
     * @param string $tokenId The token ID
     *
     * @return bool    Whether a token exists with the given ID
     */
    public function hasToken($tokenId);
}
