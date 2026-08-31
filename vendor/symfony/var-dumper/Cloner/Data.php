<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Cloner;

use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class Data implements \ArrayAccess, \Countable, \IteratorAggregate, \Stringable
{
    private array $data;
    private int $position = 0;
    private int|string $key = 0;
    private int $maxDepth = 20;
    private int $maxItemsPerDepth = -1;
    private int $useRefHandles = -1;
    private array $context = [];

    /**
     * @param array $data An array as returned by ClonerInterface::cloneVar()
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getType(): ?string
    {
        $item = $this->data[$this->position][$this->key];

        if ($item instanceof Stub && Stub::TYPE_REF === $item->type && !$item->position) {
            $item = $item->value;
        }
        if (!$item instanceof Stub) {
            return \gettype($item);
        }
        if (Stub::TYPE_STRING === $item->type) {
            return 'string';
        }
        if (Stub::TYPE_ARRAY === $item->type) {
            return 'array';
        }
        if (Stub::TYPE_OBJECT === $item->type) {
            return $item->class;
        }
        if (Stub::TYPE_RESOURCE === $item->type) {
            return $item->class.' resource';
        }

        return null;
    }

    /**
     * Returns a native representation of the original value.
     *
     * @param array|bool $recursive Whether values should be resolved recursively or not
     *
     * @return string|int|float|bool|array|Data[]|null
     */
    public function getValue(array|bool $recursive = false): string|int|float|bool|array|null
    {
        $item = $this->data[$this->position][$this->key];

        if ($item instanceof Stub && Stub::TYPE_REF === $item->type && !$item->position) {
            $item = $item->value;
        }
        if (!($item = $this->getStub($item)) instanceof Stub) {
            return $item;
        }
        if (Stub::TYPE_STRING === $item->type) {
            return $item->value;
        }

        $children = $item->position ? $this->data[$item->position] : [];

        foreach ($children as $k => $v) {
            if ($recursive && !($v = $this->getStub($v)) instanceof Stub) {
                continue;
            }
            $children[$k] = clone $this;
            $children[$k]->key = $k;
            $children[$k]->position = $item->position;

            if ($recursive) {
                if (Stub::TYPE_REF === $v->type && ($v = $this->getStub($v->value)) instanceof Stub) {
                    $recursive = (array) $recursive;
                    if (isset($recursive[$v->position])) {
                        continue;
                    }
                    $recursive[$v->position] = true;
                }
                $children[$k] = $children[$k]->getValue($recursive);
            }
        }

        return $children;
    }

    public function count(): int
    {
        return \count($this->getValue());
    }

    public function getIterator(): \Traversable
    {
        if (!\is_array($value = $this->getValue())) {
            throw new \LogicException(\sprintf('"%s" object holds non-iterable type "%s".', self::class, get_debug_type($value)));
        }

        yield from $value;
    }

    public function __get(string $key): mixed
    {
        if (null !== $data = $this->seek($key)) {
            $item = $this->getStub($data->data[$data->position][$data->key]);

            return $item instanceof Stub || [] === $item ? $data : $item;
        }

        return null;
    }

    public function __isset(string $key): bool
    {
        return null !== $this->seek($key);
    }

    public function offsetExists(mixed $key): bool
    {
        return $this->__isset($key);
    }

    public function offsetGet(mixed $key): mixed
    {
        return $this->__get($key);
    }

    public function offsetSet(mixed $key, mixed $value): void
    {
        throw new \BadMethodCallException(self::class.' objects are immutable.');
    }

    public function offsetUnset(mixed $key): void
    {
        throw new \BadMethodCallException(self::class.' objects are immutable.');
    }

    public function __toString(): string
    {
        $value = $this->getValue();

        if (!\is_array($value)) {
            return (string) $value;
        }

        return \sprintf('%s (count=%d)', $this->getType(), \count($value));
    }

    /**
     * Returns a depth limited clone of $this.
     */
    public function withMaxDepth(int $maxDepth): static
    {
        $data = clone $this;
        $data->maxDepth = $maxDepth;

        return $data;
    }

    /**
     * Limits the number of elements per depth level.
     */
    public function withMaxItemsPerDepth(int $maxItemsPerDepth): static
    {
        $data = clone $this;
        $data->maxItemsPerDepth = $maxItemsPerDepth;

        return $data;
    }

    /**
     * Enables/disables objects' identifiers tracking.
     *
     * @param bool $useRefHandles False to hide global ref. handles
     */
    public function withRefHandles(bool $useRefHandles): static
    {
        $data = clone $this;
        $data->useRefHandles = $useRefHandles ? -1 : 0;

        return $data;
    }

    public function withContext(array $context): static
    {
        $data = clone $this;
        $data->context = $context;

        return $data;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Seeks to a specific key in nested data structures.
     */
    public function seek(string|int $key): ?static
    {
        $item = $this->data[$this->position][$this->key];

        if ($item instanceof Stub && Stub::TYPE_REF === $item->type && !$item->position) {
            $item = $item->value;
        }
        if (!($item = $this->getStub($item)) instanceof Stub || !$item->position) {
            return null;
        }
        $keys = [$key];

        switch ($item->type) {
            case Stub::TYPE_OBJECT:
                $keys[] = Caster::PREFIX_DYNAMIC.$key;
                $keys[] = Caster::PREFIX_PROTECTED.$key;
                $keys[] = Caster::PREFIX_VIRTUAL.$key;
                $keys[] = "\0$item->class\0$key";
                // no break
            case Stub::TYPE_ARRAY:
            case Stub::TYPE_RESOURCE:
                break;
            default:
                return null;
        }

        $data = null;
        $children = $this->data[$item->position];

        foreach ($keys as $key) {
            if (isset($children[$key]) || \array_key_exists($key, $children)) {
                $data = clone $this;
                $data->key = $key;
                $data->position = $item->position;
                break;
            }
        }

        return $data;
    }

    /**
     * Dumps data with a DumperInterface dumper.
     */
    public function dump(DumperInterface $dumper): void
    {
        $refs = [0];
        $cursor = new Cursor();
        $cursor->hashType = -1;
        $cursor->attr = $this->context[SourceContextProvider::class] ?? [];
        $label = $this->context['label'] ?? '';

        if ($cursor->attr || '' !== $label) {
            $dumper->dumpScalar($cursor, 'label', $label);
        }
        $cursor->hashType = 0;
        $this->dumpItem($dumper, $cursor, $refs, $this->data[$this->position][$this->key]);
    }

    /**
     * Depth-first dumping of items.
     *
     * @param mixed $item A Stub object or the original value being dumped
     */
    private function dumpItem(DumperInterface $dumper, Cursor $cursor, array &$refs, mixed $item): void
    {
        $cursor->refIndex = 0;
        $cursor->softRefTo = $cursor->softRefHandle = $cursor->softRefCount = 0;
        $cursor->hardRefTo = $cursor->hardRefHandle = $cursor->hardRefCount = 0;
        $firstSeen = true;

        if (!$item instanceof Stub) {
            $cursor->attr = [];
            $type = \gettype($item);
            if ('array' === $type && $item) {
                $item = $this->getStub($item);
            }
        } elseif (Stub::TYPE_REF === $item->type) {
            if ($item->handle) {
                if (!isset($refs[$r = $item->handle - (\PHP_INT_MAX >> 1)])) {
                    $cursor->refIndex = $refs[$r] = $cursor->refIndex ?: ++$refs[0];
                } else {
                    $firstSeen = false;
                }
                $cursor->hardRefTo = $refs[$r];
                $cursor->hardRefHandle = $this->useRefHandles & $item->handle;
                $cursor->hardRefCount = 0 < $item->handle ? $item->refCount : 0;
            }
            $cursor->attr = $item->attr;
            $type = $item->class ?: \gettype($item->value);
            $item = $this->getStub($item->value);
        }
        if ($item instanceof Stub) {
            if ($item->refCount) {
                if (!isset($refs[$r = $item->handle])) {
                    $cursor->refIndex = $refs[$r] = $cursor->refIndex ?: ++$refs[0];
                } else {
                    $firstSeen = false;
                }
                $cursor->softRefTo = $refs[$r];
            }
            $cursor->softRefHandle = $this->useRefHandles & $item->handle;
            $cursor->softRefCount = $item->refCount;
            $cursor->attr = $item->attr;
            $cut = $item->cut;

            if ($item->position && $firstSeen) {
                $children = $this->data[$item->position];

                if ($cursor->stop) {
                    if ($cut >= 0) {
                        $cut += \count($children);
                    }
                    $children = [];
                }
            } else {
                $children = [];
            }
            switch ($item->type) {
                case Stub::TYPE_STRING:
                    $dumper->dumpString($cursor, $item->value, Stub::STRING_BINARY === $item->class, $cut);
                    break;

                case Stub::TYPE_ARRAY:
                    $item = clone $item;
                    $item->type = $item->class;
                    $item->class = $item->value;
                    // no break
                case Stub::TYPE_OBJECT:
                case Stub::TYPE_RESOURCE:
                    $withChildren = $children && $cursor->depth !== $this->maxDepth && $this->maxItemsPerDepth;
                    $dumper->enterHash($cursor, $item->type, $item->class, $withChildren);
                    if ($withChildren) {
                        if ($cursor->skipChildren) {
                            $withChildren = false;
                            $cut = -1;
                        } else {
                            $cut = $this->dumpChildren($dumper, $cursor, $refs, $children, $cut, $item->type, null !== $item->class);
                        }
                    } elseif ($children && 0 <= $cut) {
                        $cut += \count($children);
                    }
                    $cursor->skipChildren = false;
                    $dumper->leaveHash($cursor, $item->type, $item->class, $withChildren, $cut);
                    break;

                case Stub::TYPE_SCALAR:
                    $dumper->dumpScalar($cursor, 'default', $item->attr['value']);
                    break;

                default:
                    throw new \RuntimeException(\sprintf('Unexpected Stub type: "%s".', $item->type));
            }
        } elseif ('array' === $type) {
            $dumper->enterHash($cursor, Cursor::HASH_INDEXED, 0, false);
            $dumper->leaveHash($cursor, Cursor::HASH_INDEXED, 0, false, 0);
        } elseif ('string' === $type) {
            $dumper->dumpString($cursor, $item, false, 0);
        } else {
            $dumper->dumpScalar($cursor, $type, $item);
        }
    }

    /**
     * Dumps children of hash structures.
     *
     * @return int The final number of removed items
     */
    private function dumpChildren(DumperInterface $dumper, Cursor $parentCursor, array &$refs, array $children, int $hashCut, int $hashType, bool $dumpKeys): int
    {
        $cursor = clone $parentCursor;
        ++$cursor->depth;
        $cursor->hashType = $hashType;
        $cursor->hashIndex = 0;
        $cursor->hashLength = \count($children);
        $cursor->hashCut = $hashCut;
        foreach ($children as $key => $child) {
            $cursor->hashKeyIsBinary = isset($key[0]) && !preg_match('//u', $key);
            $cursor->hashKey = $dumpKeys ? $key : null;
            $this->dumpItem($dumper, $cursor, $refs, $child);
            if (++$cursor->hashIndex === $this->maxItemsPerDepth || $cursor->stop) {
                $parentCursor->stop = true;

                return $hashCut >= 0 ? $hashCut + $cursor->hashLength - $cursor->hashIndex : $hashCut;
            }
        }

        return $hashCut;
    }

    private function getStub(mixed $item): mixed
    {
        if (!$item || !\is_array($item)) {
            return $item;
        }

        $stub = new Stub();
        $stub->type = Stub::TYPE_ARRAY;
        foreach ($item as $stub->class => $stub->position) {
        }
        if (isset($item[0])) {
            $stub->cut = $item[0];
        }
        $stub->value = $stub->cut + ($stub->position ? \count($this->data[$stub->position]) : 0);

        return $stub;
    }
}
