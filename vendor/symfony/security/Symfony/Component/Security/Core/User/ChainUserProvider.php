<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\User;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Chain User Provider.
 *
 * This provider calls several leaf providers in a chain until one is able to
 * handle the request.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ChainUserProvider implements UserProviderInterface
{
    private $providers;

    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        foreach ($this->providers as $provider) {
            try {
                return $provider->loadUserByUsername($username);
            } catch (UsernameNotFoundException $notFound) {
                // try next one
            }
        }

        $ex = new UsernameNotFoundException(sprintf('There is no user with name "%s".', $username));
        $ex->setUsername($username);
        throw $ex;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $supportedUserFound = false;

        foreach ($this->providers as $provider) {
            try {
                return $provider->refreshUser($user);
            } catch (UnsupportedUserException $unsupported) {
                // try next one
            } catch (UsernameNotFoundException $notFound) {
                $supportedUserFound = true;
                // try next one
            }
        }

        if ($supportedUserFound) {
            $ex = new UsernameNotFoundException(sprintf('There is no user with name "%s".', $user->getUsername()));
            $ex->setUsername($user->getUsername());
            throw $ex;
        } else {
            throw new UnsupportedUserException(sprintf('The account "%s" is not supported.', get_class($user)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        foreach ($this->providers as $provider) {
            if ($provider->supportsClass($class)) {
                return true;
            }
        }

        return false;
    }
}
