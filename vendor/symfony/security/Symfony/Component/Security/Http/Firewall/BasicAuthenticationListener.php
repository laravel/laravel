<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Firewall;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * BasicAuthenticationListener implements Basic HTTP authentication.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class BasicAuthenticationListener implements ListenerInterface
{
    private $securityContext;
    private $authenticationManager;
    private $providerKey;
    private $authenticationEntryPoint;
    private $logger;
    private $ignoreFailure;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $providerKey, AuthenticationEntryPointInterface $authenticationEntryPoint, LoggerInterface $logger = null)
    {
        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->providerKey = $providerKey;
        $this->authenticationEntryPoint = $authenticationEntryPoint;
        $this->logger = $logger;
        $this->ignoreFailure = false;
    }

    /**
     * Handles basic authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (false === $username = $request->headers->get('PHP_AUTH_USER', false)) {
            return;
        }

        if (null !== $token = $this->securityContext->getToken()) {
            if ($token instanceof UsernamePasswordToken && $token->isAuthenticated() && $token->getUsername() === $username) {
                return;
            }
        }

        if (null !== $this->logger) {
            $this->logger->info(sprintf('Basic Authentication Authorization header found for user "%s"', $username));
        }

        try {
            $token = $this->authenticationManager->authenticate(new UsernamePasswordToken($username, $request->headers->get('PHP_AUTH_PW'), $this->providerKey));
            $this->securityContext->setToken($token);
        } catch (AuthenticationException $failed) {
            $token = $this->securityContext->getToken();
            if ($token instanceof UsernamePasswordToken && $this->providerKey === $token->getProviderKey()) {
                $this->securityContext->setToken(null);
            }

            if (null !== $this->logger) {
                $this->logger->info(sprintf('Authentication request failed for user "%s": %s', $username, $failed->getMessage()));
            }

            if ($this->ignoreFailure) {
                return;
            }

            $event->setResponse($this->authenticationEntryPoint->start($request, $failed));
        }
    }
}
