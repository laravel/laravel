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

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * RememberMeListener implements authentication capabilities via a cookie
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class RememberMeListener implements ListenerInterface
{
    private $securityContext;
    private $rememberMeServices;
    private $authenticationManager;
    private $logger;
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface       $securityContext
     * @param RememberMeServicesInterface    $rememberMeServices
     * @param AuthenticationManagerInterface $authenticationManager
     * @param LoggerInterface                $logger
     * @param EventDispatcherInterface       $dispatcher
     */
    public function __construct(SecurityContextInterface $securityContext, RememberMeServicesInterface $rememberMeServices, AuthenticationManagerInterface $authenticationManager, LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->securityContext = $securityContext;
        $this->rememberMeServices = $rememberMeServices;
        $this->authenticationManager = $authenticationManager;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handles remember-me cookie based authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function handle(GetResponseEvent $event)
    {
        if (null !== $this->securityContext->getToken()) {
            return;
        }

        $request = $event->getRequest();
        if (null === $token = $this->rememberMeServices->autoLogin($request)) {
            return;
        }

        try {
            $token = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($token);

            if (null !== $this->dispatcher) {
                $loginEvent = new InteractiveLoginEvent($request, $token);
                $this->dispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
            }

            if (null !== $this->logger) {
                $this->logger->debug('SecurityContext populated with remember-me token.');
            }
        } catch (AuthenticationException $failed) {
            if (null !== $this->logger) {
                $this->logger->warning(
                    'SecurityContext not populated with remember-me token as the'
                   .' AuthenticationManager rejected the AuthenticationToken returned'
                   .' by the RememberMeServices: '.$failed->getMessage()
                );
            }

            $this->rememberMeServices->loginFail($request);
        }
    }
}
