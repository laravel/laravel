<?php

namespace Illuminate\Testing\Fluent\Concerns;

use Closure;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Assert as PHPUnit;

trait Has
{
    /**
     * Assert that the prop is of the expected size.
     *
     * @param  string|int  $key
     * @param  int|null  $length
     * @return $this
     */
    public function count($key, ?int $length = null): static
    {
        if (is_null($length)) {
            $path = $this->dotPath();

            PHPUnit::assertCount(
                $key,
                $this->prop(),
                $path
                    ? sprintf('Property [%s] does not have the expected size.', $path)
                    : sprintf('Root level does not have the expected size.')
            );

            return $this;
        }

        PHPUnit::assertCount(
            $length,
            $this->prop($key),
            sprintf('Property [%s] does not have the expected size.', $this->dotPath($key))
        );

        return $this;
    }

    /**
     * Assert that the prop size is between a given minimum and maximum.
     *
     * @param  int|string  $min
     * @param  int|string  $max
     * @return $this
     */
    public function countBetween(int|string $min, int|string $max): static
    {
        $path = $this->dotPath();

        $prop = $this->prop();

        PHPUnit::assertGreaterThanOrEqual(
            $min,
            count($prop),
            $path
                ? sprintf('Property [%s] size is not greater than or equal to [%s].', $path, $min)
                : sprintf('Root level size is not greater than or equal to [%s].', $min)
        );

        PHPUnit::assertLessThanOrEqual(
            $max,
            count($prop),
            $path
                ? sprintf('Property [%s] size is not less than or equal to [%s].', $path, $max)
                : sprintf('Root level size is not less than or equal to [%s].', $max)
        );

        return $this;
    }

    /**
     * Ensure that the given prop exists.
     *
     * @param  string|int  $key
     * @param  int|\Closure|null  $length
     * @param  \Closure|null  $callback
     * @return $this
     */
    public function has($key, $length = null, ?Closure $callback = null): static
    {
        $prop = $this->prop();

        if (is_int($key) && is_null($length)) {
            return $this->count($key);
        }

        PHPUnit::assertTrue(
            Arr::has($prop, $key),
            sprintf('Property [%s] does not exist.', $this->dotPath($key))
        );

        $this->interactsWith($key);

        if (! is_null($callback)) {
            return $this->has($key, function (self $scope) use ($length, $callback) {
                return $scope
                    ->tap(function (self $scope) use ($length) {
                        if (! is_null($length)) {
                            $scope->count($length);
                        }
                    })
                    ->first($callback)
                    ->etc();
            });
        }

        if (is_callable($length)) {
            return $this->scope($key, $length);
        }

        if (! is_null($length)) {
            return $this->count($key, $length);
        }

        return $this;
    }

    /**
     * Assert that all of the given props exist.
     *
     * @param  array|string  $key
     * @return $this
     */
    public function hasAll($key): static
    {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $prop => $count) {
            if (is_int($prop)) {
                $this->has($count);
            } else {
                $this->has($prop, $count);
            }
        }

        return $this;
    }

    /**
     * Assert that at least one of the given props exists.
     *
     * @param  array|string  $key
     * @return $this
     */
    public function hasAny($key): static
    {
        $keys = is_array($key) ? $key : func_get_args();

        PHPUnit::assertTrue(
            Arr::hasAny($this->prop(), $keys),
            sprintf('None of properties [%s] exist.', implode(', ', $keys))
        );

        foreach ($keys as $key) {
            $this->interactsWith($key);
        }

        return $this;
    }

    /**
     * Assert that none of the given props exist.
     *
     * @param  array|string  $key
     * @return $this
     */
    public function missingAll($key): static
    {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $prop) {
            $this->missing($prop);
        }

        return $this;
    }

    /**
     * Assert that the given prop does not exist.
     *
     * @param  string  $key
     * @return $this
     */
    public function missing(string $key): static
    {
        PHPUnit::assertNotTrue(
            Arr::has($this->prop(), $key),
            sprintf('Property [%s] was found while it was expected to be missing.', $this->dotPath($key))
        );

        return $this;
    }

    /**
     * Compose the absolute "dot" path to the given key.
     *
     * @param  string  $key
     * @return string
     */
    abstract protected function dotPath(string $key = ''): string;

    /**
     * Marks the property as interacted.
     *
     * @param  string  $key
     * @return void
     */
    abstract protected function interactsWith(string $key): void;

    /**
     * Retrieve a prop within the current scope using "dot" notation.
     *
     * @param  string|null  $key
     * @return mixed
     */
    abstract protected function prop(?string $key = null);

    /**
     * Instantiate a new "scope" at the path of the given key.
     *
     * @param  string  $key
     * @param  \Closure  $callback
     * @return $this
     */
    abstract protected function scope(string $key, Closure $callback);

    /**
     * Disables the interaction check.
     *
     * @return $this
     */
    abstract public function etc();

    /**
     * Instantiate a new "scope" on the first element.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    abstract public function first(Closure $callback);
}
