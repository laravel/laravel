<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Authentication\Token;

use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * AnonymousToken represents an anonymous token.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AnonymousToken extends AbstractToken
{
    private $key;

    /**
     * Constructor.
     *
     * @param string          $key   The key shared with the authentication provider
     * @param string          $user  The user
     * @param RoleInterface[] $roles An array of roles
     */
    public function __construct($key, $user, array $roles = array())
    {
        parent::__construct($roles);

        $this->key = $key;
        $this->setUser($user);
        $this->setAuthenticated(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return '';
    }

    /**
     * Returns the key.
     *
     * @return string The Key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array($this->key, parent::serialize()));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->key, $parentStr) = unserialize($serialized);
        parent::unserialize($parentStr);
    }
}
