<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\String;

/**
 * A string whose value is computed lazily by a callback.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class LazyString implements \Stringable, \JsonSerializable
{
    private \Closure|string $value;

    /**
     * @param callable|array $callback A callable or a [Closure, method] lazy-callable
     */
    public static function fromCallable(callable|array $callback, mixed ...$arguments): static
    {
        if (\is_array($callback) && !\is_callable($callback) && !(($callback[0] ?? null) instanceof \Closure || 2 < \count($callback))) {
            throw new \TypeError(\sprintf('Argument 1 passed to "%s()" must be a callable or a [Closure, method] lazy-callable, "%s" given.', __METHOD__, '['.implode(', ', array_map('get_debug_type', $callback)).']'));
        }

        $lazyString = new static();
        $lazyString->value = static function () use (&$callback, &$arguments): string {
            static $value;

            if (null !== $arguments) {
                if (!\is_callable($callback)) {
                    $callback[0] = $callback[0]();
                    $callback[1] ??= '__invoke';
                }
                $value = $callback(...$arguments);
                $callback = !\is_scalar($value) && !$value instanceof \Stringable ? self::getPrettyName($callback) : 'callable';
                $arguments = null;
            }

            return $value ?? '';
        };

        return $lazyString;
    }

    public static function fromStringable(string|int|float|bool|\Stringable $value): static
    {
        if (\is_object($value)) {
            return static::fromCallable($value->__toString(...));
        }

        $lazyString = new static();
        $lazyString->value = (string) $value;

        return $lazyString;
    }

    /**
     * Tells whether the provided value can be cast to string.
     */
    final public static function isStringable(mixed $value): bool
    {
        return \is_string($value) || $value instanceof \Stringable || \is_scalar($value);
    }

    /**
     * Casts scalars and stringable objects to strings.
     *
     * @throws \TypeError When the provided value is not stringable
     */
    final public static function resolve(\Stringable|string|int|float|bool $value): string
    {
        return $value;
    }

    public function __toString(): string
    {
        if (\is_string($this->value)) {
            return $this->value;
        }

        try {
            return $this->value = ($this->value)();
        } catch (\Throwable $e) {
            if (\TypeError::class === $e::class && __FILE__ === $e->getFile()) {
                $type = explode(', ', $e->getMessage());
                $type = substr(array_pop($type), 0, -\strlen(' returned'));
                $r = new \ReflectionFunction($this->value);
                $callback = $r->getStaticVariables()['callback'];

                $e = new \TypeError(\sprintf('Return value of %s() passed to %s::fromCallable() must be of the type string, %s returned.', $callback, static::class, $type));
            }

            throw $e;
        }
    }

    public function __serialize(): array
    {
        if (self::class === (new \ReflectionMethod($this, '__sleep'))->class || self::class !== (new \ReflectionMethod($this, '__serialize'))->class) {
            $this->__toString();

            return ['value' => $this->value];
        }

        trigger_deprecation('symfony/string', '7.4', 'Implementing "%s::__sleep()" is deprecated, use "__serialize()" instead.', get_debug_type($this));

        $data = [];
        foreach ($this->__sleep() as $key) {
            try {
                if (($r = new \ReflectionProperty($this, $key))->isInitialized($this)) {
                    $data[$key] = $r->getValue($this);
                }
            } catch (\ReflectionException) {
                $data[$key] = $this->$key;
            }
        }

        return $data;
    }

    /**
     * @deprecated since Symfony 7.4, will be replaced by `__serialize()` in 8.0
     */
    public function __sleep(): array
    {
        trigger_deprecation('symfony/string', '7.4', 'Calling "%s::__sleep()" is deprecated, use "__serialize()" instead.', get_debug_type($this));

        $this->__toString();

        return ['value'];
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    private function __construct()
    {
    }

    private static function getPrettyName(callable $callback): string
    {
        if (\is_string($callback)) {
            return $callback;
        }

        if (\is_array($callback)) {
            $class = \is_object($callback[0]) ? get_debug_type($callback[0]) : $callback[0];
            $method = $callback[1];
        } elseif ($callback instanceof \Closure) {
            $r = new \ReflectionFunction($callback);

            if ($r->isAnonymous() || !$class = $r->getClosureCalledClass()) {
                return $r->name;
            }

            $class = $class->name;
            $method = $r->name;
        } else {
            $class = get_debug_type($callback);
            $method = '__invoke';
        }

        return $class.'::'.$method;
    }
}
