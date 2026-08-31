<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use Nette;
use function is_array;


/**
 * Utilities for iterables.
 */
final class Iterables
{
	use Nette\StaticClass;

	/**
	 * Tests for the presence of value.
	 * @param  iterable<mixed>  $iterable
	 */
	public static function contains(iterable $iterable, mixed $value): bool
	{
		foreach ($iterable as $v) {
			if ($v === $value) {
				return true;
			}
		}
		return false;
	}


	/**
	 * Tests for the presence of key.
	 * @param  iterable<mixed>  $iterable
	 */
	public static function containsKey(iterable $iterable, mixed $key): bool
	{
		foreach ($iterable as $k => $v) {
			if ($k === $key) {
				return true;
			}
		}
		return false;
	}


	/**
	 * Returns the first item (matching the specified predicate if given). If there is no such item, it returns result of invoking $else or null.
	 * @template K
	 * @template V
	 * @template E
	 * @param  iterable<K, V>  $iterable
	 * @param  ?callable(V, K, iterable<K, V>): bool  $predicate
	 * @param  ?callable(): E  $else
	 * @return ($else is null ? ?V : V|E)
	 */
	public static function first(iterable $iterable, ?callable $predicate = null, ?callable $else = null): mixed
	{
		foreach ($iterable as $k => $v) {
			if (!$predicate || $predicate($v, $k, $iterable)) {
				return $v;
			}
		}
		return $else ? $else() : null;
	}


	/**
	 * Returns the key of first item (matching the specified predicate if given). If there is no such item, it returns result of invoking $else or null.
	 * @template K
	 * @template V
	 * @template E
	 * @param  iterable<K, V>  $iterable
	 * @param  ?callable(V, K, iterable<K, V>): bool  $predicate
	 * @param  ?callable(): E  $else
	 * @return ($else is null ? ?K : K|E)
	 */
	public static function firstKey(iterable $iterable, ?callable $predicate = null, ?callable $else = null): mixed
	{
		foreach ($iterable as $k => $v) {
			if (!$predicate || $predicate($v, $k, $iterable)) {
				return $k;
			}
		}
		return $else ? $else() : null;
	}


	/**
	 * Tests whether at least one element in the iterable passes the test implemented by the provided function.
	 * @template K
	 * @template V
	 * @param  iterable<K, V>  $iterable
	 * @param  callable(V, K, iterable<K, V>): bool  $predicate
	 */
	public static function some(iterable $iterable, callable $predicate): bool
	{
		foreach ($iterable as $k => $v) {
			if ($predicate($v, $k, $iterable)) {
				return true;
			}
		}
		return false;
	}


	/**
	 * Tests whether all elements in the iterable pass the test implemented by the provided function.
	 * @template K
	 * @template V
	 * @param  iterable<K, V>  $iterable
	 * @param  callable(V, K, iterable<K, V>): bool  $predicate
	 */
	public static function every(iterable $iterable, callable $predicate): bool
	{
		foreach ($iterable as $k => $v) {
			if (!$predicate($v, $k, $iterable)) {
				return false;
			}
		}
		return true;
	}


	/**
	 * Returns a generator that yields only elements matching the given $predicate. Maintains original keys.
	 * @template K
	 * @template V
	 * @param  iterable<K, V>  $iterable
	 * @param  callable(V, K, iterable<K, V>): bool  $predicate
	 * @return \Generator<K, V>
	 */
	public static function filter(iterable $iterable, callable $predicate): \Generator
	{
		foreach ($iterable as $k => $v) {
			if ($predicate($v, $k, $iterable)) {
				yield $k => $v;
			}
		}
	}


	/**
	 * Returns a generator that transforms values by calling $transformer. Maintains original keys.
	 * @template K
	 * @template V
	 * @template R
	 * @param  iterable<K, V>  $iterable
	 * @param  callable(V, K, iterable<K, V>): R  $transformer
	 * @return \Generator<K, R>
	 */
	public static function map(iterable $iterable, callable $transformer): \Generator
	{
		foreach ($iterable as $k => $v) {
			yield $k => $transformer($v, $k, $iterable);
		}
	}


	/**
	 * Returns a generator that transforms keys and values by calling $transformer. If it returns null, the element is skipped.
	 * @template K
	 * @template V
	 * @template ResK
	 * @template ResV
	 * @param  iterable<K, V>  $iterable
	 * @param  callable(V, K, iterable<K, V>): ?array{ResK, ResV}  $transformer
	 * @return \Generator<ResK, ResV>
	 */
	public static function mapWithKeys(iterable $iterable, callable $transformer): \Generator
	{
		foreach ($iterable as $k => $v) {
			$pair = $transformer($v, $k, $iterable);
			if ($pair) {
				yield $pair[0] => $pair[1];
			}
		}
	}


	/**
	 * Creates a repeatable iterator from a factory function.
	 * The factory is called every time the iterator is iterated.
	 * @template K
	 * @template V
	 * @param  callable(): iterable<K, V>  $factory
	 * @return \IteratorAggregate<K, V>
	 */
	public static function repeatable(callable $factory): \IteratorAggregate
	{
		return new class ($factory(...)) implements \IteratorAggregate {
			public function __construct(
				/** @var \Closure(): iterable<mixed, mixed> */
				private readonly \Closure $factory,
			) {
			}


			public function getIterator(): \Iterator
			{
				return Iterables::toIterator(($this->factory)());
			}
		};
	}


	/**
	 * Wraps around iterator and caches its keys and values during iteration.
	 * This allows the data to be re-iterated multiple times.
	 * @template K
	 * @template V
	 * @param  iterable<K, V>  $iterable
	 * @return \IteratorAggregate<K, V>
	 */
	public static function memoize(iterable $iterable): \IteratorAggregate
	{
		return new class (self::toIterator($iterable)) implements \IteratorAggregate {
			public function __construct(
				private readonly \Iterator $iterator,
				/** @var array<array{mixed, mixed}> */
				private array $cache = [],
			) {
			}


			public function getIterator(): \Generator
			{
				if (!$this->cache) {
					$this->iterator->rewind();
				}
				$i = 0;
				while (true) {
					if (isset($this->cache[$i])) {
						[$k, $v] = $this->cache[$i];
					} elseif ($this->iterator->valid()) {
						$k = $this->iterator->key();
						$v = $this->iterator->current();
						$this->iterator->next();
						$this->cache[$i] = [$k, $v];
					} else {
						break;
					}
					yield $k => $v;
					$i++;
				}
			}
		};
	}


	/**
	 * Creates an iterator from anything that is iterable.
	 * @template K
	 * @template V
	 * @param  iterable<K, V>  $iterable
	 * @return \Iterator<K, V>
	 */
	public static function toIterator(iterable $iterable): \Iterator
	{
		return match (true) {
			$iterable instanceof \Iterator => $iterable,
			$iterable instanceof \IteratorAggregate => self::toIterator($iterable->getIterator()),
			is_array($iterable) => new \ArrayIterator($iterable),
			default => throw new Nette\ShouldNotHappenException,
		};
	}
}
