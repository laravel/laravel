<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * AuthenticatedVoter votes if an attribute like IS_AUTHENTICATED_FULLY,
 * IS_AUTHENTICATED_REMEMBERED, or IS_AUTHENTICATED_ANONYMOUSLY is present.
 *
 * This list is most restrictive to least restrictive checking.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class AuthenticatedVoter implements VoterInterface
{
    const IS_AUTHENTICATED_FULLY = 'IS_AUTHENTICATED_FULLY';
    const IS_AUTHENTICATED_REMEMBERED = 'IS_AUTHENTICATED_REMEMBERED';
    const IS_AUTHENTICATED_ANONYMOUSLY = 'IS_AUTHENTICATED_ANONYMOUSLY';

    private $authenticationTrustResolver;

    /**
     * Constructor.
     *
     * @param AuthenticationTrustResolverInterface $authenticationTrustResolver
     */
    public function __construct(AuthenticationTrustResolverInterface $authenticationTrustResolver)
    {
        $this->authenticationTrustResolver = $authenticationTrustResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return null !== $attribute && (self::IS_AUTHENTICATED_FULLY === $attribute || self::IS_AUTHENTICATED_REMEMBERED === $attribute || self::IS_AUTHENTICATED_ANONYMOUSLY === $attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $result = VoterInterface::ACCESS_ABSTAIN;
        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            $result = VoterInterface::ACCESS_DENIED;

            if (self::IS_AUTHENTICATED_FULLY === $attribute
                && $this->authenticationTrustResolver->isFullFledged($token)) {
                return VoterInterface::ACCESS_GRANTED;
            }

            if (self::IS_AUTHENTICATED_REMEMBERED === $attribute
                && ($this->authenticationTrustResolver->isRememberMe($token)
                    || $this->authenticationTrustResolver->isFullFledged($token))) {
                return VoterInterface::ACCESS_GRANTED;
            }

            if (self::IS_AUTHENTICATED_ANONYMOUSLY === $attribute
                && ($this->authenticationTrustResolver->isAnonymous($token)
                    || $this->authenticationTrustResolver->isRememberMe($token)
                    || $this->authenticationTrustResolver->isFullFledged($token))) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return $result;
    }
}
