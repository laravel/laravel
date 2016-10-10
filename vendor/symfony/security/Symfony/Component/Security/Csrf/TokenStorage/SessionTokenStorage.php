<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Csrf\TokenStorage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\Exception\TokenNotFoundException;

/**
 * Token storage that uses a Symfony2 Session object.
 *
 * @since  2.4
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class SessionTokenStorage implements TokenStorageInterface
{
    /**
     * The namespace used to store values in the session.
     * @var string
     */
    const SESSION_NAMESPACE = '_csrf';

    /**
     * The user session from which the session ID is returned
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $namespace;

    /**
     * Initializes the storage with a Session object and a session namespace.
     *
     * @param SessionInterface $session   The user session
     * @param string           $namespace The namespace under which the token
     *                                    is stored in the session
     */
    public function __construct(SessionInterface $session, $namespace = self::SESSION_NAMESPACE)
    {
        $this->session = $session;
        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken($tokenId)
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        if (!$this->session->has($this->namespace.'/'.$tokenId)) {
            throw new TokenNotFoundException('The CSRF token with ID '.$tokenId.' does not exist.');
        }

        return (string) $this->session->get($this->namespace.'/'.$tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($tokenId, $token)
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        $this->session->set($this->namespace.'/'.$tokenId, (string) $token);
    }

    /**
     * {@inheritdoc}
     */
    public function hasToken($tokenId)
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        return $this->session->has($this->namespace.'/'.$tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function removeToken($tokenId)
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        return $this->session->remove($this->namespace.'/'.$tokenId);
    }
}
