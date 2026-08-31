<?php

declare(strict_types=1);

namespace League\Flysystem;

use RuntimeException;

/**
 * @internal
 */
trait ProxyArrayAccessToProperties
{
    private function formatPropertyName(string $offset): string
    {
        return str_replace('_', '', lcfirst(ucwords($offset, '_')));
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        $property = $this->formatPropertyName((string) $offset);

        return isset($this->{$property});
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        $property = $this->formatPropertyName((string) $offset);

        return $this->{$property};
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Properties can not be manipulated');
    }

    /**
     * @param mixed $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Properties can not be manipulated');
    }
}
