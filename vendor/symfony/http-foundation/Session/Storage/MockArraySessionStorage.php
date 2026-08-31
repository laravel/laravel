<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

/**
 * MockArraySessionStorage mocks the session for unit tests.
 *
 * No PHP session is actually started since a session can be initialized
 * and shutdown only once per PHP execution cycle.
 *
 * When doing functional testing, you should use MockFileSessionStorage instead.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Drak <drak@zikula.org>
 */
class MockArraySessionStorage implements SessionStorageInterface
{
    protected string $id = '';
    protected bool $started = false;
    protected bool $closed = false;
    protected array $data = [];
    protected MetadataBag $metadataBag;

    /**
     * @var SessionBagInterface[]
     */
    protected array $bags = [];

    public function __construct(
        protected string $name = 'MOCKSESSID',
        ?MetadataBag $metaBag = null,
    ) {
        $this->setMetadataBag($metaBag);
    }

    public function setSessionData(array $array): void
    {
        $this->data = $array;
    }

    public function start(): bool
    {
        if ($this->started) {
            return true;
        }

        if (!$this->id) {
            $this->id = $this->generateId();
        }

        $this->loadSession();

        return true;
    }

    public function regenerate(bool $destroy = false, ?int $lifetime = null): bool
    {
        if (!$this->started) {
            $this->start();
        }

        $this->metadataBag->stampNew($lifetime);
        $this->id = $this->generateId();

        return true;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        if ($this->started) {
            throw new \LogicException('Cannot set session ID after the session has started.');
        }

        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function save(): void
    {
        if (!$this->started || $this->closed) {
            throw new \RuntimeException('Trying to save a session that was not started yet or was already closed.');
        }
        // nothing to do since we don't persist the session data
        $this->closed = false;
        $this->started = false;
    }

    public function clear(): void
    {
        // clear out the bags
        foreach ($this->bags as $bag) {
            $bag->clear();
        }

        // clear out the session
        $this->data = [];

        // reconnect the bags to the session
        $this->loadSession();
    }

    public function registerBag(SessionBagInterface $bag): void
    {
        $this->bags[$bag->getName()] = $bag;
    }

    public function getBag(string $name): SessionBagInterface
    {
        if (!isset($this->bags[$name])) {
            throw new \InvalidArgumentException(\sprintf('The SessionBagInterface "%s" is not registered.', $name));
        }

        if (!$this->started) {
            $this->start();
        }

        return $this->bags[$name];
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function setMetadataBag(?MetadataBag $bag): void
    {
        $this->metadataBag = $bag ?? new MetadataBag();
    }

    /**
     * Gets the MetadataBag.
     */
    public function getMetadataBag(): MetadataBag
    {
        return $this->metadataBag;
    }

    /**
     * Generates a session ID.
     *
     * This doesn't need to be particularly cryptographically secure since this is just
     * a mock.
     */
    protected function generateId(): string
    {
        return bin2hex(random_bytes(16));
    }

    protected function loadSession(): void
    {
        $bags = array_merge($this->bags, [$this->metadataBag]);

        foreach ($bags as $bag) {
            $key = $bag->getStorageKey();
            $this->data[$key] ??= [];
            $bag->initialize($this->data[$key]);
        }

        $this->started = true;
        $this->closed = false;
    }
}
