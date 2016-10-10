<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Authentication\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

/**
 * UserProviderInterface retrieves users for UsernamePasswordToken tokens.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class UserAuthenticationProvider implements AuthenticationProviderInterface
{
    private $hideUserNotFoundExceptions;
    private $userChecker;
    private $providerKey;

    /**
     * Constructor.
     *
     * @param UserCheckerInterface $userChecker                An UserCheckerInterface interface
     * @param string               $providerKey                A provider key
     * @param bool                 $hideUserNotFoundExceptions Whether to hide user not found exception or not
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(UserCheckerInterface $userChecker, $providerKey, $hideUserNotFoundExceptions = true)
    {
        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->userChecker = $userChecker;
        $this->providerKey = $providerKey;
        $this->hideUserNotFoundExceptions = $hideUserNotFoundExceptions;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return;
        }

        $username = $token->getUsername();
        if (empty($username)) {
            $username = 'NONE_PROVIDED';
        }

        try {
            $user = $this->retrieveUser($username, $token);
        } catch (UsernameNotFoundException $notFound) {
            if ($this->hideUserNotFoundExceptions) {
                throw new BadCredentialsException('Bad credentials', 0, $notFound);
            }
            $notFound->setUsername($username);

            throw $notFound;
        }

        if (!$user instanceof UserInterface) {
            throw new AuthenticationServiceException('retrieveUser() must return a UserInterface.');
        }

        try {
            $this->userChecker->checkPreAuth($user);
            $this->checkAuthentication($user, $token);
            $this->userChecker->checkPostAuth($user);
        } catch (BadCredentialsException $e) {
            if ($this->hideUserNotFoundExceptions) {
                throw new BadCredentialsException('Bad credentials', 0, $e);
            }

            throw $e;
        }

        $authenticatedToken = new UsernamePasswordToken($user, $token->getCredentials(), $this->providerKey, $this->getRoles($user, $token));
        $authenticatedToken->setAttributes($token->getAttributes());

        return $authenticatedToken;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof UsernamePasswordToken && $this->providerKey === $token->getProviderKey();
    }

    /**
     * Retrieves roles from user and appends SwitchUserRole if original token contained one.
     *
     * @param UserInterface  $user  The user
     * @param TokenInterface $token The token
     *
     * @return Role[] The user roles
     */
    private function getRoles(UserInterface $user, TokenInterface $token)
    {
        $roles = $user->getRoles();

        foreach ($token->getRoles() as $role) {
            if ($role instanceof SwitchUserRole) {
                $roles[] = $role;

                break;
            }
        }

        return $roles;
    }

    /**
     * Retrieves the user from an implementation-specific location.
     *
     * @param string                $username The username to retrieve
     * @param UsernamePasswordToken $token    The Token
     *
     * @return UserInterface The user
     *
     * @throws AuthenticationException if the credentials could not be validated
     */
    abstract protected function retrieveUser($username, UsernamePasswordToken $token);

    /**
     * Does additional checks on the user and token (like validating the
     * credentials).
     *
     * @param UserInterface         $user  The retrieved UserInterface instance
     * @param UsernamePasswordToken $token The UsernamePasswordToken token to be authenticated
     *
     * @throws AuthenticationException if the credentials could not be validated
     */
    abstract protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token);
}
