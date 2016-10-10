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
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * SimplePreAuthenticationListener implements simple proxying to an authenticator.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class SimplePreAuthenticationListener implements ListenerInterface
{
    private $securityContext;
    private $authenticationManager;
    private $providerKey;
    private $simpleAuthenticator;
    private $logger;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface         $securityContext       A SecurityContext instance
     * @param AuthenticationManagerInterface   $authenticationManager An AuthenticationManagerInterface instance
     * @param string                           $providerKey
     * @param SimplePreAuthenticatorInterface  $simpleAuthenticator   A SimplePreAuthenticatorInterface instance
     * @param LoggerInterface                  $logger                A LoggerInterface instance
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $providerKey, SimplePreAuthenticatorInterface $simpleAuthenticator, LoggerInterface $logger = null)
    {
        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->providerKey = $providerKey;
        $this->simpleAuthenticator = $simpleAuthenticator;
        $this->logger = $logger;
    }

    /**
     * Handles basic authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (null !== $this->logger) {
            $this->logger->info(sprintf('Attempting simple pre-authorization %s', $this->providerKey));
        }

        if (null !== $this->securityContext->getToken() && !$this->securityContext->getToken() instanceof AnonymousToken) {
            return;
        }

        try {
            $token = $this->simpleAuthenticator->createToken($request, $this->providerKey);
            $token = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($token);
        } catch (AuthenticationException $e) {
            $this->securityContext->setToken(null);

            if (null !== $this->logger) {
                $this->logger->info(sprintf('Authentication request failed: %s', $e->getMessage()));
            }

            if ($this->simpleAuthenticator instanceof AuthenticationFailureHandlerInterface) {
                $response = $this->simpleAuthenticator->onAuthenticationFailure($request, $e);
                if ($response instanceof Response) {
                    $event->setResponse($response);
                } elseif (null !== $response) {
                    throw new \UnexpectedValueException(sprintf('The %s::onAuthenticationFailure method must return null or a Response object', get_class($this->simpleAuthenticator)));
                }
            }

            return;
        }

        if ($this->simpleAuthenticator instanceof AuthenticationSuccessHandlerInterface) {
            $response = $this->simpleAuthenticator->onAuthenticationSuccess($request, $token);
            if ($response instanceof Response) {
                $event->setResponse($response);
            } elseif (null !== $response) {
                throw new \UnexpectedValueException(sprintf('The %s::onAuthenticationSuccess method must return null or a Response object', get_class($this->simpleAuthenticator)));
            }
        }
    }
}
