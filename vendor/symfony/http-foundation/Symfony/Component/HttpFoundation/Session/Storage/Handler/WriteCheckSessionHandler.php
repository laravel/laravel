<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

/**
 * Wraps another SessionHandlerInterface to only write the session when it has been modified.
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class WriteCheckSessionHandler implements \SessionHandlerInterface
{
    /**
     * @var \SessionHandlerInterface
     */
    private $wrappedSessionHandler;

    /**
     * @var array sessionId => session
     */
    private $readSessions;

    public function __construct(\SessionHandlerInterface $wrappedSessionHandler)
    {
        $this->wrappedSessionHandler = $wrappedSessionHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return $this->wrappedSessionHandler->close();
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        return $this->wrappedSessionHandler->destroy($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxLifetime)
    {
        return $this->wrappedSessionHandler->gc($maxLifetime);
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionId)
    {
        return $this->wrappedSessionHandler->open($savePath, $sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        $session = $this->wrappedSessionHandler->read($sessionId);

        $this->readSessions[$sessionId] = $session;

        return $session;
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $sessionData)
    {
        if (isset($this->readSessions[$sessionId]) && $sessionData === $this->readSessions[$sessionId]) {
            return true;
        }

        return $this->wrappedSessionHandler->write($sessionId, $sessionData);
    }
}
