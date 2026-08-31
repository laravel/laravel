<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage\Proxy;

/**
 * @author Drak <drak@zikula.org>
 */
abstract class AbstractProxy
{
    protected bool $wrapper = false;

    protected ?string $saveHandlerName = null;

    /**
     * Gets the session.save_handler name.
     */
    public function getSaveHandlerName(): ?string
    {
        return $this->saveHandlerName;
    }

    /**
     * Is this proxy handler and instance of \SessionHandlerInterface.
     */
    public function isSessionHandlerInterface(): bool
    {
        return $this instanceof \SessionHandlerInterface;
    }

    /**
     * Returns true if this handler wraps an internal PHP session save handler using \SessionHandler.
     */
    public function isWrapper(): bool
    {
        return $this->wrapper;
    }

    /**
     * Has a session started?
     */
    public function isActive(): bool
    {
        return \PHP_SESSION_ACTIVE === session_status();
    }

    /**
     * Gets the session ID.
     */
    public function getId(): string
    {
        return session_id();
    }

    /**
     * Sets the session ID.
     *
     * @throws \LogicException
     */
    public function setId(string $id): void
    {
        if ($this->isActive()) {
            throw new \LogicException('Cannot change the ID of an active session.');
        }

        session_id($id);
    }

    /**
     * Gets the session name.
     */
    public function getName(): string
    {
        return session_name();
    }

    /**
     * Sets the session name.
     *
     * @throws \LogicException
     */
    public function setName(string $name): void
    {
        if ($this->isActive()) {
            throw new \LogicException('Cannot change the name of an active session.');
        }

        session_name($name);
    }
}
