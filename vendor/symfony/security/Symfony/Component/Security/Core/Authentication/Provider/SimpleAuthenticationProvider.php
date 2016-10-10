<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Authentication\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\SimpleAuthenticatorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class SimpleAuthenticationProvider implements AuthenticationProviderInterface
{
    private $simpleAuthenticator;
    private $userProvider;
    private $providerKey;

    public function __construct(SimpleAuthenticatorInterface $simpleAuthenticator, UserProviderInterface $userProvider, $providerKey)
    {
        $this->simpleAuthenticator = $simpleAuthenticator;
        $this->userProvider = $userProvider;
        $this->providerKey = $providerKey;
    }

    public function authenticate(TokenInterface $token)
    {
        $authToken = $this->simpleAuthenticator->authenticateToken($token, $this->userProvider, $this->providerKey);

        if ($authToken instanceof TokenInterface) {
            return $authToken;
        }

        throw new AuthenticationException('Simple authenticator failed to return an authenticated token.');
    }

    public function supports(TokenInterface $token)
    {
        return $this->simpleAuthenticator->supportsToken($token, $this->providerKey);
    }
}
