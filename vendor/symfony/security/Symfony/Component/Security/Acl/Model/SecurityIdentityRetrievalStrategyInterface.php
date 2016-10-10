<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Model;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Interface for retrieving security identities from tokens
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface SecurityIdentityRetrievalStrategyInterface
{
    /**
     * Retrieves the available security identities for the given token
     *
     * The order in which the security identities are returned is significant.
     * Typically, security identities should be ordered from most specific to
     * least specific.
     *
     * @param TokenInterface $token
     *
     * @return SecurityIdentityInterface[] An array of SecurityIdentityInterface implementations
     */
    public function getSecurityIdentities(TokenInterface $token);
}
