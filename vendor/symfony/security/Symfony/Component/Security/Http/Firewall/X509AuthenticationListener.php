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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * X509 authentication listener.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class X509AuthenticationListener extends AbstractPreAuthenticatedListener
{
    private $userKey;
    private $credentialKey;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $providerKey, $userKey = 'SSL_CLIENT_S_DN_Email', $credentialKey = 'SSL_CLIENT_S_DN', LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        parent::__construct($securityContext, $authenticationManager, $providerKey, $logger, $dispatcher);

        $this->userKey = $userKey;
        $this->credentialKey = $credentialKey;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPreAuthenticatedData(Request $request)
    {
        $user = null;
        if ($request->server->has($this->userKey)) {
            $user = $request->server->get($this->userKey);
        } elseif ($request->server->has($this->credentialKey) && preg_match('#/emailAddress=(.+\@.+\..+)(/|$)#', $request->server->get($this->credentialKey), $matches)) {
            $user = $matches[1];
        }

        if (null === $user) {
            throw new BadCredentialsException(sprintf('SSL credentials not found: %s, %s', $this->userKey, $this->credentialKey));
        }

        return array($user, $request->server->get($this->credentialKey, ''));
    }
}
