<?php

namespace Illuminate\Testing\Fluent\Concerns;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert as PHPUnit;

use function Illuminate\Support\enum_value;

trait Matching
{
    /**
     * Asserts that the property matches the expected value.
     *
     * @param  string  $key
     * @param  mixed|\Closure  $expected
     * @return $this
     */
    public function where(string $key, $expected): static
    {
        $this->has($key);

        $actual = $this->prop($key);

        if ($expected instanceof Closure) {
            PHPUnit::assertTrue(
                $expected(is_array($actual) ? new Collection($actual) : $actual),
                sprintf('Property [%s] was marked as invalid using a closure.', $this->dotPath($key))
            );

            return $this;
        }

        $expected = $expected instanceof Arrayable
            ? $expected->toArray()
            : enum_value($expected);

        $this->ensureSorted($expected);
        $this->ensureSorted($actual);

        PHPUnit::assertSame(
            $expected,
            $actual,
            sprintf('Property [%s] does not match the expected value.', $this->dotPath($key))
        );

        return $this;
    }

    /**
     * Asserts that the property does not match the expected value.
     *
     * @param  string  $key
     * @param  mixed|\Closure  $expected
     * @return $this
     */
    public function whereNot(string $key, $expected): static
    {
        $this->has($key);

        $actual = $this->prop($key);

        if ($expected instanceof Closure) {
            PHPUnit::assertFalse(
                $expected(is_array($actual) ? new Collection($actual) : $actual),
                sprintf('Property [%s] was marked as invalid using a closure.', $this->dotPath($key))
            );

            return $this;
        }

        $expected = $expected instanceof Arrayable
            ? $expected->toArray()
            : enum_value($expected);

        $this->ensureSorted($expected);
        $this->ensureSorted($actual);

        PHPUnit::assertNotSame(
            $expected,
            $actual,
            sprintf(
                'Property [%s] contains a value that should be missing: [%s, %s]',
                $this->dotPath($key),
                $key,
                $expected
            )
        );

        return $this;
    }

    /**
     * Asserts that the property is null.
     *
     * @param  string  $key
     * @return $this
     */
    public function whereNull(string $key): static
    {
        $this->has($key);

        $actual = $this->prop($key);

        PHPUnit::assertNull(
            $actual,
            sprintf(
                'Property [%s] should be null.',
                $this->dotPath($key),
            )
        );

        return $this;
    }

    /**
     * Asserts that the property is not null.
     *
     * @param  string  $key
     * @return $this
     */
    public function whereNotNull(string $key): static
    {
        $this->has($key);

        $actual = $this->prop($key);

        PHPUnit::assertNotNull(
            $actual,
            sprintf(
                'Property [%s] should not be null.',
                $this->dotPath($key),
            )
        );

        return $this;
    }

    /**
     * Asserts that all properties match their expected values.
     *
     * @param  array  $bindings
     * @return $this
     */
    public function whereAll(array $bindings): static
    {
        foreach ($bindings as $key => $value) {
            $this->where($key, $value);
        }

        return $this;
    }

    /**
     * Asserts that the property is of the expected type.
     *
     * @param  string  $key
     * @param  string|array  $expected
     * @return $this
     */
    public function whereType(string $key, $expected): static
    {
        $this->has($key);

        $actual = $this->prop($key);

        if (! is_array($expected)) {
            $expected = explode('|', $expected);
        }

        PHPUnit::assertContains(
            strtolower(gettype($actual)),
            $expected,
            sprintf('Property [%s] is not of expected type [%s].', $this->dotPath($key), implode('|', $expected))
        );

        return $this;
    }

    /**
     * Asserts that all properties are of their expected types.
     *
     * @param  array  $bindings
     * @return $this
     */
    public function whereAllType(array $bindings): static
    {
        foreach ($bindings as $key => $value) {
            $this->whereType($key, $value);
        }

        return $this;
    }

    /**
     * Asserts that the property contains the expected values.
     *
     * @param  string  $key
     * @param  mixed  $expected
     * @return $this
     */
    public function whereContains(string $key, $expected)
    {
        $actual = new Collection(
            $this->prop($key) ?? $this->prop()
        );

        $missing = (new Collection($expected))
            ->map(fn ($search) => enum_value($search))
            ->reject(function ($search) use ($key, $actual) {
                if ($actual->containsStrict($key, $search)) {
                    return true;
                }

                return $actual->containsStrict($search);
            });

        if ($missing->whereInstanceOf('Closure')->isNotEmpty()) {
            PHPUnit::assertEmpty(
                $missing->toArray(),
                sprintf(
                    'Property [%s] does not contain a value that passes the truth test within the given closure.',
                    $key,
                )
            );
        } else {
            PHPUnit::assertEmpty(
                $missing->toArray(),
                sprintf(
                    'Property [%s] does not contain [%s].',
                    $key,
                    implode(', ', array_values($missing->toArray()))
                )
            );
        }

        return $this;
    }

    /**
     * Ensures that all properties are sorted the same way, recursively.
     *
     * @param  mixed  $value
     * @return void
     */
    protected function ensureSorted(&$value): void
    {
        if (! is_array($value)) {
            return;
        }

        foreach ($value as &$arg) {
            $this->ensureSorted($arg);
        }

        ksort($value);
    }

    /**
     * Compose the absolute "dot" path to the given key.
     *
     * @param  string  $key
     * @return string
     */
    abstract protected function dotPath(string $key = ''): string;

    /**
     * Ensure that the given prop exists.
     *
     * @param  string  $key
     * @param  null  $value
     * @param  \Closure|null  $scope
     * @return $this
     */
    abstract public function has(string $key, $value = null, ?Closure $scope = null);

    /**
     * Retrieve a prop within the current scope using "dot" notation.
     *
     * @param  string|null  $key
     * @return mixed
     */
    abstract protected function prop(?string $key = null);
}
