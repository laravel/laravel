<?php

declare(strict_types=1);

/*
 * This file is a part of dflydev/dot-access-data.
 *
 * (c) Dragonfly Development Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dflydev\DotAccessData;

use ArrayAccess;
use Dflydev\DotAccessData\Exception\DataException;
use Dflydev\DotAccessData\Exception\InvalidPathException;
use Dflydev\DotAccessData\Exception\MissingPathException;

/**
 * @implements ArrayAccess<string, mixed>
 */
class Data implements DataInterface, ArrayAccess
{
    private const DELIMITERS = ['.', '/'];

    /**
     * Internal representation of data data
     *
     * @var array<string, mixed>
     */
    protected $data;

    /**
     * Constructor
     *
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function append(string $key, $value = null): void
    {
        $currentValue =& $this->data;
        $keyPath = self::keyToPathArray($key);

        $endKey = array_pop($keyPath);
        foreach ($keyPath as $currentKey) {
            if (! isset($currentValue[$currentKey])) {
                $currentValue[$currentKey] = [];
            }
            $currentValue =& $currentValue[$currentKey];
        }

        if (!isset($currentValue[$endKey])) {
            $currentValue[$endKey] = [];
        }

        if (!is_array($currentValue[$endKey])) {
            // Promote this key to an array.
            // TODO: Is this really what we want to do?
            $currentValue[$endKey] = [$currentValue[$endKey]];
        }

        $currentValue[$endKey][] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value = null): void
    {
        $currentValue =& $this->data;
        $keyPath = self::keyToPathArray($key);

        $endKey = array_pop($keyPath);
        foreach ($keyPath as $currentKey) {
            if (!isset($currentValue[$currentKey])) {
                $currentValue[$currentKey] = [];
            }
            if (!is_array($currentValue[$currentKey])) {
                throw new DataException(sprintf('Key path "%s" within "%s" cannot be indexed into (is not an array)', $currentKey, self::formatPath($key)));
            }
            $currentValue =& $currentValue[$currentKey];
        }
        $currentValue[$endKey] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $key): void
    {
        $currentValue =& $this->data;
        $keyPath = self::keyToPathArray($key);

        $endKey = array_pop($keyPath);
        foreach ($keyPath as $currentKey) {
            if (!isset($currentValue[$currentKey])) {
                return;
            }
            $currentValue =& $currentValue[$currentKey];
        }
        unset($currentValue[$endKey]);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-mutation-free
     */
    public function get(string $key, $default = null)
    {
        /** @psalm-suppress ImpureFunctionCall */
        $hasDefault = \func_num_args() > 1;

        $currentValue = $this->data;
        $keyPath = self::keyToPathArray($key);

        foreach ($keyPath as $currentKey) {
            if (!is_array($currentValue) || !array_key_exists($currentKey, $currentValue)) {
                if ($hasDefault) {
                    return $default;
                }

                throw new MissingPathException($key, sprintf('No data exists at the given path: "%s"', self::formatPath($keyPath)));
            }

            $currentValue = $currentValue[$currentKey];
        }

        return $currentValue === null ? $default : $currentValue;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-mutation-free
     */
    public function has(string $key): bool
    {
        $currentValue = $this->data;

        foreach (self::keyToPathArray($key) as $currentKey) {
            if (
                !is_array($currentValue) ||
                !array_key_exists($currentKey, $currentValue)
            ) {
                return false;
            }
            $currentValue = $currentValue[$currentKey];
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-mutation-free
     */
    public function getData(string $key): DataInterface
    {
        $value = $this->get($key);
        if (is_array($value) && Util::isAssoc($value)) {
            return new Data($value);
        }

        throw new DataException(sprintf('Value at "%s" could not be represented as a DataInterface', self::formatPath($key)));
    }

    /**
     * {@inheritdoc}
     */
    public function import(array $data, int $mode = self::REPLACE): void
    {
        $this->data = Util::mergeAssocArray($this->data, $data, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function importData(DataInterface $data, int $mode = self::REPLACE): void
    {
        $this->import($data->export(), $mode);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-mutation-free
     */
    public function export(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        return $this->get($key, null);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($key)
    {
        $this->remove($key);
    }

    /**
     * @param string $path
     *
     * @return string[]
     *
     * @psalm-return non-empty-list<string>
     *
     * @psalm-pure
     */
    protected static function keyToPathArray(string $path): array
    {
        if (\strlen($path) === 0) {
            throw new InvalidPathException('Path cannot be an empty string');
        }

        $path = \str_replace(self::DELIMITERS, '.', $path);

        return \explode('.', $path);
    }

    /**
     * @param string|string[] $path
     *
     * @return string
     *
     * @psalm-pure
     */
    protected static function formatPath($path): string
    {
        if (is_string($path)) {
            $path = self::keyToPathArray($path);
        }

        return implode(' » ', $path);
    }
}
