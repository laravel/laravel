<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\SimpleAuthenticatorInterface;

/**
 * Class to proxy authentication success/failure handlers
 *
 * Events are sent to the SimpleAuthenticatorInterface if it implements
 * the right interface, otherwise (or if it fails to return a Response)
 * the default handlers are triggered.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class SimpleAuthenticationHandler implements AuthenticationFailureHandlerInterface, AuthenticationSuccessHandlerInterface
{
    protected $successHandler;
    protected $failureHandler;
    protected $simpleAuthenticator;
    protected $logger;

    /**
     * Constructor.
     *
     * @param SimpleAuthenticatorInterface          $authenticator  SimpleAuthenticatorInterface instance
     * @param AuthenticationSuccessHandlerInterface $successHandler Default success handler
     * @param AuthenticationFailureHandlerInterface $failureHandler Default failure handler
     * @param LoggerInterface                       $logger         Optional logger
     */
    public function __construct(SimpleAuthenticatorInterface $authenticator, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, LoggerInterface $logger = null)
    {
        $this->simpleAuthenticator = $authenticator;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($this->simpleAuthenticator instanceof AuthenticationSuccessHandlerInterface) {
            if ($this->logger) {
                $this->logger->debug(sprintf('Using the %s object as authentication success handler', get_class($this->simpleAuthenticator)));
            }

            $response = $this->simpleAuthenticator->onAuthenticationSuccess($request, $token);
            if ($response instanceof Response) {
                return $response;
            }

            if (null !== $response) {
                throw new \UnexpectedValueException(sprintf('The %s::onAuthenticationSuccess method must return null to use the default success handler, or a Response object', get_class($this->simpleAuthenticator)));
            }
        }

        if ($this->logger) {
            $this->logger->debug('Fallback to the default authentication success handler');
        }

        return $this->successHandler->onAuthenticationSuccess($request, $token);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($this->simpleAuthenticator instanceof AuthenticationFailureHandlerInterface) {
            if ($this->logger) {
                $this->logger->debug(sprintf('Using the %s object as authentication failure handler', get_class($this->simpleAuthenticator)));
            }

            $response = $this->simpleAuthenticator->onAuthenticationFailure($request, $exception);
            if ($response instanceof Response) {
                return $response;
            }

            if (null !== $response) {
                throw new \UnexpectedValueException(sprintf('The %s::onAuthenticationFailure method must return null to use the default failure handler, or a Response object', get_class($this->simpleAuthenticator)));
            }
        }

        if ($this->logger) {
            $this->logger->debug('Fallback to the default authentication failure handler');
        }

        return $this->failureHandler->onAuthenticationFailure($request, $exception);
    }
}
