<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\RememberMe;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Concrete implementation of the RememberMeServicesInterface providing
 * remember-me capabilities without requiring a TokenProvider.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class TokenBasedRememberMeServices extends AbstractRememberMeServices
{
    /**
     * {@inheritdoc}
     */
    protected function processAutoLoginCookie(array $cookieParts, Request $request)
    {
        if (count($cookieParts) !== 4) {
            throw new AuthenticationException('The cookie is invalid.');
        }

        list($class, $username, $expires, $hash) = $cookieParts;
        if (false === $username = base64_decode($username, true)) {
            throw new AuthenticationException('$username contains a character from outside the base64 alphabet.');
        }
        try {
            $user = $this->getUserProvider($class)->loadUserByUsername($username);
        } catch (\Exception $ex) {
            if (!$ex instanceof AuthenticationException) {
                $ex = new AuthenticationException($ex->getMessage(), $ex->getCode(), $ex);
            }

            throw $ex;
        }

        if (!$user instanceof UserInterface) {
            throw new \RuntimeException(sprintf('The UserProviderInterface implementation must return an instance of UserInterface, but returned "%s".', get_class($user)));
        }

        if (true !== $this->compareHashes($hash, $this->generateCookieHash($class, $username, $expires, $user->getPassword()))) {
            throw new AuthenticationException('The cookie\'s hash is invalid.');
        }

        if ($expires < time()) {
            throw new AuthenticationException('The cookie has expired.');
        }

        return $user;
    }

    /**
     * Compares two hashes using a constant-time algorithm to avoid (remote)
     * timing attacks.
     *
     * This is the same implementation as used in the BasePasswordEncoder.
     *
     * @param string $hash1 The first hash
     * @param string $hash2 The second hash
     *
     * @return bool    true if the two hashes are the same, false otherwise
     */
    private function compareHashes($hash1, $hash2)
    {
        if (strlen($hash1) !== $c = strlen($hash2)) {
            return false;
        }

        $result = 0;
        for ($i = 0; $i < $c; $i++) {
            $result |= ord($hash1[$i]) ^ ord($hash2[$i]);
        }

        return 0 === $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function onLoginSuccess(Request $request, Response $response, TokenInterface $token)
    {
        $user = $token->getUser();
        $expires = time() + $this->options['lifetime'];
        $value = $this->generateCookieValue(get_class($user), $user->getUsername(), $expires, $user->getPassword());

        $response->headers->setCookie(
            new Cookie(
                $this->options['name'],
                $value,
                $expires,
                $this->options['path'],
                $this->options['domain'],
                $this->options['secure'],
                $this->options['httponly']
            )
        );
    }

    /**
     * Generates the cookie value.
     *
     * @param string  $class
     * @param string  $username The username
     * @param int     $expires  The Unix timestamp when the cookie expires
     * @param string  $password The encoded password
     *
     * @throws \RuntimeException if username contains invalid chars
     *
     * @return string
     */
    protected function generateCookieValue($class, $username, $expires, $password)
    {
        return $this->encodeCookie(array(
            $class,
            base64_encode($username),
            $expires,
            $this->generateCookieHash($class, $username, $expires, $password)
        ));
    }

    /**
     * Generates a hash for the cookie to ensure it is not being tempered with
     *
     * @param string  $class
     * @param string  $username The username
     * @param int     $expires  The Unix timestamp when the cookie expires
     * @param string  $password The encoded password
     *
     * @throws \RuntimeException when the private key is empty
     *
     * @return string
     */
    protected function generateCookieHash($class, $username, $expires, $password)
    {
        return hash_hmac('sha256', $class.$username.$expires.$password, $this->getKey());
    }
}
