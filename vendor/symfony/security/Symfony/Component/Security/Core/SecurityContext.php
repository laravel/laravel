<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core;

use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * SecurityContext is the main entry point of the Security component.
 *
 * It gives access to the token representing the current user authentication.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class SecurityContext implements SecurityContextInterface
{
    private $token;
    private $accessDecisionManager;
    private $authenticationManager;
    private $alwaysAuthenticate;

    /**
     * Constructor.
     *
     * @param AuthenticationManagerInterface      $authenticationManager An AuthenticationManager instance
     * @param AccessDecisionManagerInterface|null $accessDecisionManager An AccessDecisionManager instance
     * @param bool                                $alwaysAuthenticate
     */
    public function __construct(AuthenticationManagerInterface $authenticationManager, AccessDecisionManagerInterface $accessDecisionManager, $alwaysAuthenticate = false)
    {
        $this->authenticationManager = $authenticationManager;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->alwaysAuthenticate = $alwaysAuthenticate;
    }

    /**
     * {@inheritdoc}
     *
     * @throws AuthenticationCredentialsNotFoundException when the security context has no authentication token.
     */
    final public function isGranted($attributes, $object = null)
    {
        if (null === $this->token) {
            throw new AuthenticationCredentialsNotFoundException('The security context contains no authentication token. One possible reason may be that there is no firewall configured for this URL.');
        }

        if ($this->alwaysAuthenticate || !$this->token->isAuthenticated()) {
            $this->token = $this->authenticationManager->authenticate($this->token);
        }

        if (!is_array($attributes)) {
            $attributes = array($attributes);
        }

        return $this->accessDecisionManager->decide($this->token, $attributes, $object);
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(TokenInterface $token = null)
    {
        $this->token = $token;
    }
}
