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

use Traversable;

/**
 * @template T
 *
 * @extends Option<T>
 */
final class LazyOption extends Option
{
    /** @var callable(mixed...):(Option<T>) */
    private $callback;

    /** @var array<int, mixed> */
    private $arguments;

    /** @var Option<T>|null */
    private $option;

    /**
     * @template S
     * @param callable(mixed...):(Option<S>) $callback
     * @param array<int, mixed>              $arguments
     *
     * @return LazyOption<S>
     */
    public static function create($callback, array $arguments = []): self
    {
        return new self($callback, $arguments);
    }

    /**
     * @param callable(mixed...):(Option<T>) $callback
     * @param array<int, mixed>              $arguments
     */
    public function __construct($callback, array $arguments = [])
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Invalid callback given');
        }

        $this->callback = $callback;
        $this->arguments = $arguments;
    }

    public function isDefined(): bool
    {
        return $this->option()->isDefined();
    }

    public function isEmpty(): bool
    {
        return $this->option()->isEmpty();
    }

    public function get()
    {
        return $this->option()->get();
    }

    public function getOrElse($default)
    {
        return $this->option()->getOrElse($default);
    }

    public function getOrCall($callable)
    {
        return $this->option()->getOrCall($callable);
    }

    public function getOrThrow(\Exception $ex)
    {
        return $this->option()->getOrThrow($ex);
    }

    public function orElse(Option $else)
    {
        return $this->option()->orElse($else);
    }

    public function ifDefined($callable)
    {
        $this->option()->forAll($callable);
    }

    public function forAll($callable)
    {
        return $this->option()->forAll($callable);
    }

    public function map($callable)
    {
        return $this->option()->map($callable);
    }

    public function flatMap($callable)
    {
        return $this->option()->flatMap($callable);
    }

    public function filter($callable)
    {
        return $this->option()->filter($callable);
    }

    public function filterNot($callable)
    {
        return $this->option()->filterNot($callable);
    }

    public function select($value)
    {
        return $this->option()->select($value);
    }

    public function reject($value)
    {
        return $this->option()->reject($value);
    }

    /**
     * @return Traversable<T>
     */
    public function getIterator(): Traversable
    {
        return $this->option()->getIterator();
    }

    public function foldLeft($initialValue, $callable)
    {
        return $this->option()->foldLeft($initialValue, $callable);
    }

    public function foldRight($initialValue, $callable)
    {
        return $this->option()->foldRight($initialValue, $callable);
    }

    /**
     * @return Option<T>
     */
    private function option(): Option
    {
        if (null === $this->option) {
            /** @var mixed */
            $option = call_user_func_array($this->callback, $this->arguments);
            if ($option instanceof Option) {
                $this->option = $option;
            } else {
                throw new \RuntimeException(sprintf('Expected instance of %s', Option::class));
            }
        }

        return $this->option;
    }
}
