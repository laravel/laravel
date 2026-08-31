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
 * InputBag is a container for user input values such as $_GET, $_POST, $_REQUEST, and $_COOKIE.
 *
 * @template TInput of string|int|float|bool|null
 *
 * @author Saif Eddin Gmati <azjezz@protonmail.com>
 */
final class InputBag extends ParameterBag
{
    /**
     * Returns a scalar input value by name.
     *
     * @template TDefault of string|int|float|bool|null
     *
     * @param TDefault $default The default value if the input key does not exist
     *
     * @return TDefault|TInput
     *
     * @throws BadRequestException if the input contains a non-scalar value
     */
    public function get(string $key, mixed $default = null): string|int|float|bool|null
    {
        if (null !== $default && !\is_scalar($default) && !$default instanceof \Stringable) {
            throw new \InvalidArgumentException(\sprintf('Expected a scalar value as a 2nd argument to "%s()", "%s" given.', __METHOD__, get_debug_type($default)));
        }

        $value = parent::get($key, $this);

        if (null !== $value && $this !== $value && !\is_scalar($value) && !$value instanceof \Stringable) {
            throw new BadRequestException(\sprintf('Input value "%s" contains a non-scalar value.', $key));
        }

        return $this === $value ? $default : $value;
    }

    /**
     * Replaces the current input values by a new set.
     */
    public function replace(array $inputs = []): void
    {
        $this->parameters = [];
        $this->add($inputs);
    }

    /**
     * Adds input values.
     */
    public function add(array $inputs = []): void
    {
        foreach ($inputs as $input => $value) {
            $this->set($input, $value);
        }
    }

    /**
     * Sets an input by name.
     *
     * @param string|int|float|bool|array|null $value
     */
    public function set(string $key, mixed $value): void
    {
        if (null !== $value && !\is_scalar($value) && !\is_array($value) && !$value instanceof \Stringable) {
            throw new \InvalidArgumentException(\sprintf('Expected a scalar, or an array as a 2nd argument to "%s()", "%s" given.', __METHOD__, get_debug_type($value)));
        }

        $this->parameters[$key] = $value;
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
     * @throws BadRequestException if the input cannot be converted to an enum
     */
    public function getEnum(string $key, string $class, ?\BackedEnum $default = null): ?\BackedEnum
    {
        try {
            return parent::getEnum($key, $class, $default);
        } catch (UnexpectedValueException $e) {
            throw new BadRequestException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Returns the parameter value converted to string.
     *
     * @throws BadRequestException if the input contains a non-scalar value
     */
    public function getString(string $key, string $default = ''): string
    {
        // Shortcuts the parent method because the validation on scalar is already done in get().
        return (string) $this->get($key, $default);
    }

    /**
     * @throws BadRequestException if the input value is an array and \FILTER_REQUIRE_ARRAY or \FILTER_FORCE_ARRAY is not set
     * @throws BadRequestException if the input value is invalid and \FILTER_NULL_ON_FAILURE is not set
     */
    public function filter(string $key, mixed $default = null, int $filter = \FILTER_DEFAULT, mixed $options = []): mixed
    {
        $value = $this->has($key) ? $this->all()[$key] : $default;

        // Always turn $options into an array - this allows filter_var option shortcuts.
        if (!\is_array($options) && $options) {
            $options = ['flags' => $options];
        }

        if (\is_array($value) && !(($options['flags'] ?? 0) & (\FILTER_REQUIRE_ARRAY | \FILTER_FORCE_ARRAY))) {
            throw new BadRequestException(\sprintf('Input value "%s" contains an array, but "FILTER_REQUIRE_ARRAY" or "FILTER_FORCE_ARRAY" flags were not set.', $key));
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

        throw new BadRequestException(\sprintf('Input value "%s" is invalid and flag "FILTER_NULL_ON_FAILURE" was not set.', $key));
    }
}
