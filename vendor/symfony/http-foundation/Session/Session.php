<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session;

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

// Help opcache.preload discover always-needed symbols
class_exists(AttributeBag::class);
class_exists(FlashBag::class);
class_exists(SessionBagProxy::class);

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Drak <drak@zikula.org>
 *
 * @implements \IteratorAggregate<string, mixed>
 */
class Session implements FlashBagAwareSessionInterface, \IteratorAggregate, \Countable
{
    protected SessionStorageInterface $storage;

    private string $flashName;
    private string $attributeName;
    private array $data = [];
    private int $usageIndex = 0;
    private ?\Closure $usageReporter;

    public function __construct(?SessionStorageInterface $storage = null, ?AttributeBagInterface $attributes = null, ?FlashBagInterface $flashes = null, ?callable $usageReporter = null)
    {
        $this->storage = $storage ?? new NativeSessionStorage();
        $this->usageReporter = null === $usageReporter ? null : $usageReporter(...);

        $attributes ??= new AttributeBag();
        $this->attributeName = $attributes->getName();
        $this->registerBag($attributes);

        $flashes ??= new FlashBag();
        $this->flashName = $flashes->getName();
        $this->registerBag($flashes);
    }

    public function start(): bool
    {
        return $this->storage->start();
    }

    public function has(string $name): bool
    {
        return $this->getAttributeBag()->has($name);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->getAttributeBag()->get($name, $default);
    }

    public function set(string $name, mixed $value): void
    {
        $this->getAttributeBag()->set($name, $value);
    }

    public function all(): array
    {
        return $this->getAttributeBag()->all();
    }

    public function replace(array $attributes): void
    {
        $this->getAttributeBag()->replace($attributes);
    }

    public function remove(string $name): mixed
    {
        return $this->getAttributeBag()->remove($name);
    }

    public function clear(): void
    {
        $this->getAttributeBag()->clear();
    }

    public function isStarted(): bool
    {
        return $this->storage->isStarted();
    }

    /**
     * Returns an iterator for attributes.
     *
     * @return \ArrayIterator<string, mixed>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->getAttributeBag()->all());
    }

    /**
     * Returns the number of attributes.
     */
    public function count(): int
    {
        return \count($this->getAttributeBag()->all());
    }

    public function &getUsageIndex(): int
    {
        return $this->usageIndex;
    }

    /**
     * @internal
     */
    public function isEmpty(): bool
    {
        if ($this->isStarted()) {
            ++$this->usageIndex;
            if ($this->usageReporter && 0 <= $this->usageIndex) {
                ($this->usageReporter)();
            }
        }
        foreach ($this->data as &$data) {
            if ($data) {
                return false;
            }
        }

        return true;
    }

    public function invalidate(?int $lifetime = null): bool
    {
        $this->storage->clear();

        return $this->migrate(true, $lifetime);
    }

    public function migrate(bool $destroy = false, ?int $lifetime = null): bool
    {
        return $this->storage->regenerate($destroy, $lifetime);
    }

    public function save(): void
    {
        $this->storage->save();
    }

    public function getId(): string
    {
        return $this->storage->getId();
    }

    public function setId(string $id): void
    {
        if ($this->storage->getId() !== $id) {
            $this->storage->setId($id);
        }
    }

    public function getName(): string
    {
        return $this->storage->getName();
    }

    public function setName(string $name): void
    {
        $this->storage->setName($name);
    }

    public function getMetadataBag(): MetadataBag
    {
        ++$this->usageIndex;
        if ($this->usageReporter && 0 <= $this->usageIndex) {
            ($this->usageReporter)();
        }

        return $this->storage->getMetadataBag();
    }

    public function registerBag(SessionBagInterface $bag): void
    {
        $this->storage->registerBag(new SessionBagProxy($bag, $this->data, $this->usageIndex, $this->usageReporter));
    }

    public function getBag(string $name): SessionBagInterface
    {
        $bag = $this->storage->getBag($name);

        return method_exists($bag, 'getBag') ? $bag->getBag() : $bag;
    }

    /**
     * Gets the flashbag interface.
     */
    public function getFlashBag(): FlashBagInterface
    {
        return $this->getBag($this->flashName);
    }

    /**
     * Gets the attributebag interface.
     *
     * Note that this method was added to help with IDE autocompletion.
     */
    private function getAttributeBag(): AttributeBagInterface
    {
        return $this->getBag($this->attributeName);
    }
}
