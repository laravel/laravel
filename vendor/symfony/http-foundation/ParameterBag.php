<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Exception\UnexpectedValueException;

/**
 * ParameterBag is a container for key/value pairs.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @implements \IteratorAggregate<string, mixed>
 */
class ParameterBag implements \IteratorAggregate, \Countable
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        protected array $parameters = [],
    ) {
    }

    /**
     * Returns the parameters.
     *
     * @template TKey of string|null
     *
     * @param TKey $key The name of the parameter to return or null to get them all
     *
     * @return (TKey is null ? array<string, mixed> : array<mixed>)
     *
     * @throws BadRequestException if the value is not an array
     */
    public function all(?string $key = null): array
    {
        if (null === $key) {
            return $this->parameters;
        }

        if (!\is_array($value = $this->parameters[$key] ?? [])) {
            throw new BadRequestException(\sprintf('Unexpected value for parameter "%s": expecting "array", got "%s".', $key, get_debug_type($value)));
        }

        return $value;
    }

    /**
     * Returns the parameter keys.
     *
     * @return list<string>
     */
    public function keys(): array
    {
        return array_keys($this->parameters);
    }

    /**
     * Replaces the current parameters by a new set.
     *
     * @param array<string, mixed> $parameters
     */
    public function replace(array $parameters = []): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Adds parameters.
     *
     * @param array<string, mixed> $parameters
     */
    public function add(array $parameters = []): void
    {
        $this->parameters = array_replace($this->parameters, $parameters);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return \array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Returns true if the parameter is defined.
     */
    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->parameters);
    }

    /**
     * Removes a parameter.
     */
    public function remove(string $key): void
    {
        unset($this->parameters[$key]);
    }

    /**
     * Returns the alphabetic characters of the parameter value.
     *
     * @throws UnexpectedValueException if the value cannot be converted to string
     */
    public function getAlpha(string $key, string $default = ''): string
    {
        return preg_replace('/[^[:alpha:]]/', '', $this->getString($key, $default));
    }

    /**
     * Returns the alphabetic characters and digits of the parameter value.
     *
     * @throws UnexpectedValueException if the value cannot be converted to string
     */
    public function getAlnum(string $key, string $default = ''): string
    {
        return preg_replace('/[^[:alnum:]]/', '', $this->getString($key, $default));
    }

    /**
     * Returns the digits of the parameter value.
     *
     * @throws UnexpectedValueException if the value cannot be converted to string
     */
    public function getDigits(string $key, string $default = ''): string
    {
        return preg_replace('/[^[:digit:]]/', '', $this->getString($key, $default));
    }

    /**
     * Returns the parameter as string.
     *
     * @throws UnexpectedValueException if the value cannot be converted to string
     */
    public function getString(string $key, string $default = ''): string
    {
        $value = $this->get($key, $default);
        if (!\is_scalar($value) && !$value instanceof \Stringable) {
            throw new UnexpectedValueException(\sprintf('Parameter value "%s" cannot be converted to "string".', $key));
        }

        return (string) $value;
    }

    /**
     * Returns the parameter value converted to integer.
     *
     * @throws UnexpectedValueException if the value cannot be converted to integer
     */
    public function getInt(string $key, int $default = 0): int
    {
        return $this->filter($key, $default, \FILTER_VALIDATE_INT, ['flags' => \FILTER_REQUIRE_SCALAR]);
    }

    /**
     * Returns the parameter value converted to boolean.
     *
     * @throws UnexpectedValueException if the value cannot be converted to a boolean
     */
    public function getBoolean(string $key, bool $default = false): bool
    {
        return $this->filter($key, $default, \FILTER_VALIDATE_BOOL, ['flags' => \FILTER_REQUIRE_SCALAR]);
    }

    /**
     * Returns the parameter value converted to an enum.
     *
     * @template T of \BackedEnum
     *
     * @param class-string<T> $class
     * @param ?T              $default
     *
     * @return ?T
     *
     * @psalm-return ($default is null ? T|null : T)
     *
     * @throws UnexpectedValueException if the parameter value cannot be converted to an enum
     */
    public function getEnum(string $key, string $class, ?\BackedEnum $default = null): ?\BackedEnum
    {
        $value = $this->get($key);

        if (null === $value) {
            return $default;
        }

        try {
            return $class::from($value);
        } catch (\ValueError|\TypeError $e) {
            throw new UnexpectedValueException(\sprintf('Parameter "%s" cannot be converted to enum: ', $key).$e->getMessage().'.', $e->getCode(), $e);
        }
    }

    /**
     * Filter key.
     *
     * @param int                                     $filter  FILTER_* constant
     * @param int|array{flags?: int, options?: array} $options Flags from FILTER_* constants
     *
     * @see https://php.net/filter-var
     *
     * @throws UnexpectedValueException if the parameter value is a non-stringable object
     * @throws UnexpectedValueException if the parameter value is invalid and \FILTER_NULL_ON_FAILURE is not set
     */
    public function filter(string $key, mixed $default = null, int $filter = \FILTER_DEFAULT, mixed $options = []): mixed
    {
        $value = $this->get($key, $default);

        // Always turn $options into an array - this allows filter_var option shortcuts.
        if (!\is_array($options) && $options) {
            $options = ['flags' => $options];
        }

        // Add a convenience check for arrays.
        if (\is_array($value) && !isset($options['flags'])) {
            $options['flags'] = \FILTER_REQUIRE_ARRAY;
        }

        if (\is_object($value) && !$value instanceof \Stringable) {
            throw new UnexpectedValueException(\sprintf('Parameter value "%s" cannot be filtered.', $key));
        }

        if ((\FILTER_CALLBACK & $filter) && !(($options['options'] ?? null) instanceof \Closure)) {
            throw new \InvalidArgumentException(\sprintf('A Closure must be passed to "%s()" when FILTER_CALLBACK is used, "%s" given.', __METHOD__, get_debug_type($options['options'] ?? null)));
        }

        $options['flags'] ??= 0;
        $nullOnFailure = $options['flags'] & \FILTER_NULL_ON_FAILURE;
        $options['flags'] |= \FILTER_NULL_ON_FAILURE;

        $value = filter_var($value, $filter, $options);

        if (null !== $value || $nullOnFailure) {
            return $value;
        }

        throw new \UnexpectedValueException(\sprintf('Parameter value "%s" is invalid and flag "FILTER_NULL_ON_FAILURE" was not set.', $key));
    }

    /**
     * Returns an iterator for parameters.
     *
     * @return \ArrayIterator<string, mixed>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->parameters);
    }

    /**
     * Returns the number of parameters.
     */
    public function count(): int
    {
        return \count($this->parameters);
    }
}
