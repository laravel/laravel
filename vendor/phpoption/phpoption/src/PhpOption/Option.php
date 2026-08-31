<?php

/*
 * Copyright 2012 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PhpOption;

use ArrayAccess;
use IteratorAggregate;

/**
 * @template T
 *
 * @implements IteratorAggregate<T>
 */
abstract class Option implements IteratorAggregate
{
    /**
     * Creates an option given a return value.
     *
     * This is intended for consuming existing APIs and allows you to easily
     * convert them to an option. By default, we treat ``null`` as the None
     * case, and everything else as Some.
     *
     * @template S
     *
     * @param S $value     The actual return value.
     * @param S $noneValue The value which should be considered "None"; null by
     *                     default.
     *
     * @return Option<S>
     */
    public static function fromValue($value, $noneValue = null)
    {
        if ($value === $noneValue) {
            return None::create();
        }

        return new Some($value);
    }

    /**
     * Creates an option from an array's value.
     *
     * If the key does not exist in the array, the array is not actually an
     * array, or the array's value at the given key is null, None is returned.
     * Otherwise, Some is returned wrapping the value at the given key.
     *
     * @template S
     *
     * @param array<string|int,S>|ArrayAccess<string|int,S>|null $array A potential array or \ArrayAccess value.
     * @param string|int|null                                    $key   The key to check.
     *
     * @return Option<S>
     */
    public static function fromArraysValue($array, $key)
    {
        if ($key === null || !(is_array($array) || $array instanceof ArrayAccess) || !isset($array[$key])) {
            return None::create();
        }

        return new Some($array[$key]);
    }

    /**
     * Creates a lazy-option with the given callback.
     *
     * This is also a helper constructor for lazy-consuming existing APIs where
     * the return value is not yet an option. By default, we treat ``null`` as
     * None case, and everything else as Some.
     *
     * @template S
     *
     * @param callable $callback  The callback to evaluate.
     * @param array    $arguments The arguments for the callback.
     * @param S        $noneValue The value which should be considered "None";
    *                             null by default.
     *
     * @return LazyOption<S>
     */
    public static function fromReturn($callback, array $arguments = [], $noneValue = null)
    {
        return new LazyOption(static function () use ($callback, $arguments, $noneValue) {
            /** @var mixed */
            $return = call_user_func_array($callback, $arguments);

            if ($return === $noneValue) {
                return None::create();
            }

            return new Some($return);
        });
    }

    /**
     * Option factory, which creates new option based on passed value.
     *
     * If value is already an option, it simply returns. If value is callable,
     * LazyOption with passed callback created and returned. If Option
     * returned from callback, it returns directly. On other case value passed
     * to Option::fromValue() method.
     *
     * @template S
     *
     * @param Option<S>|callable|S $value
     * @param S                    $noneValue Used when $value is mixed or
     *                                        callable, for None-check.
     *
     * @return Option<S>|LazyOption<S>
     */
    public static function ensure($value, $noneValue = null)
    {
        if ($value instanceof self) {
            return $value;
        } elseif (is_callable($value)) {
            return new LazyOption(static function () use ($value, $noneValue) {
                /** @var mixed */
                $return = $value();

                if ($return instanceof self) {
                    return $return;
                } else {
                    return self::fromValue($return, $noneValue);
                }
            });
        } else {
            return self::fromValue($value, $noneValue);
        }
    }

    /**
     * Lift a function so that it accepts Option as parameters.
     *
     * We return a new closure that wraps the original callback. If any of the
     * parameters passed to the lifted function is empty, the function will
     * return a value of None. Otherwise, we will pass all parameters to the
     * original callback and return the value inside a new Option, unless an
     * Option is returned from the function, in which case, we use that.
     *
     * @template S
     *
     * @param callable $callback
     * @param mixed    $noneValue
     *
     * @return callable
     */
    public static function lift($callback, $noneValue = null)
    {
        return static function () use ($callback, $noneValue) {
            /** @var array<int, mixed> */
            $args = func_get_args();

            $reduced_args = array_reduce(
                $args,
                /** @param bool $status */
                static function ($status, self $o) {
                    return $o->isEmpty() ? true : $status;
                },
                false
            );
            // if at least one parameter is empty, return None
            if ($reduced_args) {
                return None::create();
            }

            $args = array_map(
                /** @return T */
                static function (self $o) {
                    // it is safe to do so because the fold above checked
                    // that all arguments are of type Some
                    /** @var T */
                    return $o->get();
                },
                $args
            );

            return self::ensure(call_user_func_array($callback, $args), $noneValue);
        };
    }

    /**
     * Returns the value if available, or throws an exception otherwise.
     *
     * @throws \RuntimeException If value is not available.
     *
     * @return T
     */
    abstract public function get();

    /**
     * Returns the value if available, or the default value if not.
     *
     * @template S
     *
     * @param S $default
     *
     * @return T|S
     */
    abstract public function getOrElse($default);

    /**
     * Returns the value if available, or the results of the callable.
     *
     * This is preferable over ``getOrElse`` if the computation of the default
     * value is expensive.
     *
     * @template S
     *
     * @param callable():S $callable
     *
     * @return T|S
     */
    abstract public function getOrCall($callable);

    /**
     * Returns the value if available, or throws the passed exception.
     *
     * @param \Exception $ex
     *
     * @return T
     */
    abstract public function getOrThrow(\Exception $ex);

    /**
     * Returns true if no value is available, false otherwise.
     *
     * @return bool
     */
    abstract public function isEmpty();

    /**
     * Returns true if a value is available, false otherwise.
     *
     * @return bool
     */
    abstract public function isDefined();

    /**
     * Returns this option if non-empty, or the passed option otherwise.
     *
     * This can be used to try multiple alternatives, and is especially useful
     * with lazy evaluating options:
     *
     * ```php
     *     $repo->findSomething()
     *         ->orElse(new LazyOption(array($repo, 'findSomethingElse')))
     *         ->orElse(new LazyOption(array($repo, 'createSomething')));
     * ```
     *
     * @param Option<T> $else
     *
     * @return Option<T>
     */
    abstract public function orElse(self $else);

    /**
     * This is similar to map() below except that the return value has no meaning;
     * the passed callable is simply executed if the option is non-empty, and
     * ignored if the option is empty.
     *
     * In all cases, the return value of the callable is discarded.
     *
     * ```php
     *     $comment->getMaybeFile()->ifDefined(function($file) {
     *         // Do something with $file here.
     *     });
     * ```
     *
     * If you're looking for something like ``ifEmpty``, you can use ``getOrCall``
     * and ``getOrElse`` in these cases.
     *
     * @deprecated Use forAll() instead.
     *
     * @param callable(T):mixed $callable
     *
     * @return void
     */
    abstract public function ifDefined($callable);

    /**
     * This is similar to map() except that the return value of the callable has no meaning.
     *
     * The passed callable is simply executed if the option is non-empty, and ignored if the
     * option is empty. This method is preferred for callables with side-effects, while map()
     * is intended for callables without side-effects.
     *
     * @param callable(T):mixed $callable
     *
     * @return Option<T>
     */
    abstract public function forAll($callable);

    /**
     * Applies the callable to the value of the option if it is non-empty,
     * and returns the return value of the callable wrapped in Some().
     *
     * If the option is empty, then the callable is not applied.
     *
     * ```php
     *     (new Some("foo"))->map('strtoupper')->get(); // "FOO"
     * ```
     *
     * @template S
     *
     * @param callable(T):S $callable
     *
     * @return Option<S>
     */
    abstract public function map($callable);

    /**
     * Applies the callable to the value of the option if it is non-empty, and
     * returns the return value of the callable directly.
     *
     * In contrast to ``map``, the return value of the callable is expected to
     * be an Option itself; it is not automatically wrapped in Some().
     *
     * @template S
     *
     * @param callable(T):Option<S> $callable must return an Option
     *
     * @return Option<S>
     */
    abstract public function flatMap($callable);

    /**
     * If the option is empty, it is returned immediately without applying the callable.
     *
     * If the option is non-empty, the callable is applied, and if it returns true,
     * the option itself is returned; otherwise, None is returned.
     *
     * @param callable(T):bool $callable
     *
     * @return Option<T>
     */
    abstract public function filter($callable);

    /**
     * If the option is empty, it is returned immediately without applying the callable.
     *
     * If the option is non-empty, the callable is applied, and if it returns false,
     * the option itself is returned; otherwise, None is returned.
     *
     * @param callable(T):bool $callable
     *
     * @return Option<T>
     */
    abstract public function filterNot($callable);

    /**
     * If the option is empty, it is returned immediately.
     *
     * If the option is non-empty, and its value does not equal the passed value
     * (via a shallow comparison ===), then None is returned. Otherwise, the
     * Option is returned.
     *
     * In other words, this will filter all but the passed value.
     *
     * @param T $value
     *
     * @return Option<T>
     */
    abstract public function select($value);

    /**
     * If the option is empty, it is returned immediately.
     *
     * If the option is non-empty, and its value does equal the passed value (via
     * a shallow comparison ===), then None is returned; otherwise, the Option is
     * returned.
     *
     * In other words, this will let all values through except the passed value.
     *
     * @param T $value
     *
     * @return Option<T>
     */
    abstract public function reject($value);

    /**
     * Binary operator for the initial value and the option's value.
     *
     * If empty, the initial value is returned. If non-empty, the callable
     * receives the initial value and the option's value as arguments.
     *
     * ```php
     *
     *     $some = new Some(5);
     *     $none = None::create();
     *     $result = $some->foldLeft(1, function($a, $b) { return $a + $b; }); // int(6)
     *     $result = $none->foldLeft(1, function($a, $b) { return $a + $b; }); // int(1)
     *
     *     // This can be used instead of something like the following:
     *     $option = Option::fromValue($integerOrNull);
     *     $result = 1;
     *     if ( ! $option->isEmpty()) {
     *         $result += $option->get();
     *     }
     * ```
     *
     * @template S
     *
     * @param S                $initialValue
     * @param callable(S, T):S $callable
     *
     * @return S
     */
    abstract public function foldLeft($initialValue, $callable);

    /**
     * foldLeft() but with reversed arguments for the callable.
     *
     * @template S
     *
     * @param S                $initialValue
     * @param callable(T, S):S $callable
     *
     * @return S
     */
    abstract public function foldRight($initialValue, $callable);
}
