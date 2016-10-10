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
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * AbstractPreAuthenticatedListener is the base class for all listener that
 * authenticates users based on a pre-authenticated request (like a certificate
 * for instance).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class AbstractPreAuthenticatedListener implements ListenerInterface
{
    protected $logger;
    private $securityContext;
    private $authenticationManager;
    private $providerKey;
    private $dispatcher;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $providerKey, LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->providerKey = $providerKey;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handles pre-authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    final public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Checking secure context token: %s', $this->securityContext->getToken()));
        }

        try {
            list($user, $credentials) = $this->getPreAuthenticatedData($request);
        } catch (BadCredentialsException $exception) {
            $this->clearToken($exception);

            return;
        }

        if (null !== $token = $this->securityContext->getToken()) {
            if ($token instanceof PreAuthenticatedToken && $this->providerKey == $token->getProviderKey() && $token->isAuthenticated() && $token->getUsername() === $user) {
                return;
            }
        }

        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Trying to pre-authenticate user "%s"', $user));
        }

        try {
            $token = $this->authenticationManager->authenticate(new PreAuthenticatedToken($user, $credentials, $this->providerKey));

            if (null !== $this->logger) {
                $this->logger->info(sprintf('Authentication success: %s', $token));
            }
            $this->securityContext->setToken($token);

            if (null !== $this->dispatcher) {
                $loginEvent = new InteractiveLoginEvent($request, $token);
                $this->dispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
            }
        } catch (AuthenticationException $failed) {
            $this->clearToken($failed);
        }
    }

    /**
     * Clears a PreAuthenticatedToken for this provider (if present)
     *
     * @param AuthenticationException $exception
     */
    private function clearToken(AuthenticationException $exception)
    {
        $token = $this->securityContext->getToken();
        if ($token instanceof PreAuthenticatedToken && $this->providerKey === $token->getProviderKey()) {
            $this->securityContext->setToken(null);

            if (null !== $this->logger) {
                $this->logger->info(sprintf("Cleared security context due to exception: %s", $exception->getMessage()));
            }
        }
    }

    /**
     * Gets the user and credentials from the Request.
     *
     * @param Request $request A Request instance
     *
     * @return array An array composed of the user and the credentials
     */
    abstract protected function getPreAuthenticatedData(Request $request);
}
