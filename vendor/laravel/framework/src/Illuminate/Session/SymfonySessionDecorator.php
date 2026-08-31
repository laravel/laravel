<?php

namespace Illuminate\Session;

use BadMethodCallException;
use Illuminate\Contracts\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;

class SymfonySessionDecorator implements SessionInterface
{
    /**
     * The underlying Laravel session store.
     *
     * @var \Illuminate\Contracts\Session\Session
     */
    public readonly Session $store;

    /**
     * Create a new session decorator.
     *
     * @param  \Illuminate\Contracts\Session\Session  $store
     */
    public function __construct(Session $store)
    {
        $this->store = $store;
    }

    /**
     * {@inheritdoc}
     */
    public function start(): bool
    {
        return $this->store->start();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->store->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $id): void
    {
        $this->store->setId($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->store->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): void
    {
        $this->store->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function invalidate(?int $lifetime = null): bool
    {
        $this->store->invalidate();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function migrate(bool $destroy = false, ?int $lifetime = null): bool
    {
        $this->store->migrate($destroy);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function save(): void
    {
        $this->store->save();
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return $this->store->has($name);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, mixed $default = null): mixed
    {
        return $this->store->get($name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, mixed $value): void
    {
        $this->store->put($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->store->all();
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $attributes): void
    {
        $this->store->replace($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): mixed
    {
        return $this->store->remove($name);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $this->store->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted(): bool
    {
        return $this->store->isStarted();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \BadMethodCallException
     */
    public function registerBag(SessionBagInterface $bag): void
    {
        throw new BadMethodCallException('Method not implemented by Laravel.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \BadMethodCallException
     */
    public function getBag(string $name): SessionBagInterface
    {
        throw new BadMethodCallException('Method not implemented by Laravel.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \BadMethodCallException
     */
    public function getMetadataBag(): MetadataBag
    {
        throw new BadMethodCallException('Method not implemented by Laravel.');
    }
}
