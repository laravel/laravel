<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel;

/**
 * Signs URIs.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class UriSigner
{
    private $secret;

    /**
     * Constructor.
     *
     * @param string $secret A secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Signs a URI.
     *
     * The given URI is signed by adding a _hash query string parameter
     * which value depends on the URI and the secret.
     *
     * @param string $uri A URI to sign
     *
     * @return string The signed URI
     */
    public function sign($uri)
    {
        return $uri.(false === (strpos($uri, '?')) ? '?' : '&').'_hash='.$this->computeHash($uri);
    }

    /**
     * Checks that a URI contains the correct hash.
     *
     * The _hash query string parameter must be the last one
     * (as it is generated that way by the sign() method, it should
     * never be a problem).
     *
     * @param string $uri A signed URI
     *
     * @return bool    True if the URI is signed correctly, false otherwise
     */
    public function check($uri)
    {
        if (!preg_match('/^(.*)(?:\?|&)_hash=(.+?)$/', $uri, $matches)) {
            return false;
        }

        return $this->computeHash($matches[1]) === $matches[2];
    }

    private function computeHash($uri)
    {
        return urlencode(base64_encode(hash_hmac('sha256', $uri, $this->secret, true)));
    }
}
