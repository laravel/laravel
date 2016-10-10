<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Authentication;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Interface for resolving the authentication status of a given token.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface AuthenticationTrustResolverInterface
{
    /**
     * Resolves whether the passed token implementation is authenticated
     * anonymously.
     *
     * If null is passed, the method must return false.
     *
     * @param TokenInterface $token
     *
     * @return bool
     */
    public function isAnonymous(TokenInterface $token = null);

    /**
     * Resolves whether the passed token implementation is authenticated
     * using remember-me capabilities.
     *
     * @param TokenInterface $token
     *
     * @return bool
     */
    public function isRememberMe(TokenInterface $token = null);

    /**
     * Resolves whether the passed token implementation is fully authenticated.
     *
     * @param TokenInterface $token
     *
     * @return bool
     */
    public function isFullFledged(TokenInterface $token = null);
}
