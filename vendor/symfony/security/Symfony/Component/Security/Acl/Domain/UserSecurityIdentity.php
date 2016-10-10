<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Acl\Domain;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Util\ClassUtils;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;

/**
 * A SecurityIdentity implementation used for actual users
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class UserSecurityIdentity implements SecurityIdentityInterface
{
    private $username;
    private $class;

    /**
     * Constructor
     *
     * @param string $username the username representation
     * @param string $class    the user's fully qualified class name
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($username, $class)
    {
        if (empty($username)) {
            throw new \InvalidArgumentException('$username must not be empty.');
        }
        if (empty($class)) {
            throw new \InvalidArgumentException('$class must not be empty.');
        }

        $this->username = (string) $username;
        $this->class = $class;
    }

    /**
     * Creates a user security identity from a UserInterface
     *
     * @param UserInterface $user
     * @return UserSecurityIdentity
     */
    public static function fromAccount(UserInterface $user)
    {
        return new self($user->getUsername(), ClassUtils::getRealClass($user));
    }

    /**
     * Creates a user security identity from a TokenInterface
     *
     * @param TokenInterface $token
     * @return UserSecurityIdentity
     */
    public static function fromToken(TokenInterface $token)
    {
        $user = $token->getUser();

        if ($user instanceof UserInterface) {
            return self::fromAccount($user);
        }

        return new self((string) $user, is_object($user) ? ClassUtils::getRealClass($user) : ClassUtils::getRealClass($token));
    }

    /**
     * Returns the username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns the user's class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(SecurityIdentityInterface $sid)
    {
        if (!$sid instanceof UserSecurityIdentity) {
            return false;
        }

        return $this->username === $sid->getUsername()
               && $this->class === $sid->getClass();
    }

    /**
     * A textual representation of this security identity.
     *
     * This is not used for equality comparison, but only for debugging.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('UserSecurityIdentity(%s, %s)', $this->username, $this->class);
    }
}
