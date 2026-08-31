<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Attribute;

/**
 * This class relates to session attribute storage.
 *
 * @implements \IteratorAggregate<string, mixed>
 */
class AttributeBag implements AttributeBagInterface, \IteratorAggregate, \Countable
{
    protected array $attributes = [];

    private string $name = 'attributes';

    /**
     * @param string $storageKey The key used to store attributes in the session
     */
    public function __construct(
        private string $storageKey = '_sf2_attributes',
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function initialize(array &$attributes): void
    {
        $this->attributes = &$attributes;
    }

    public function getStorageKey(): string
    {
        return $this->storageKey;
    }

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->attributes);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return \array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }

    public function set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function all(): array
    {
        return $this->attributes;
    }

    public function replace(array $attributes): void
    {
        $this->attributes = [];
        foreach ($attributes as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function remove(string $name): mixed
    {
        $retval = null;
        if (\array_key_exists($name, $this->attributes)) {
            $retval = $this->attributes[$name];
            unset($this->attributes[$name]);
        }

        return $retval;
    }

    public function clear(): mixed
    {
        $return = $this->attributes;
        $this->attributes = [];

        return $return;
    }

    /**
     * Returns an iterator for attributes.
     *
     * @return \ArrayIterator<string, mixed>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->attributes);
    }

    /**
     * Returns the number of attributes.
     */
    public function count(): int
    {
        return \count($this->attributes);
    }
}
