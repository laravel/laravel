<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use Nette;
use function count, is_array, is_scalar, sprintf;


/**
 * Array-like object with property access.
 * @template T
 * @implements \IteratorAggregate<array-key, T>
 * @implements \ArrayAccess<array-key, T>
 */
class ArrayHash extends \stdClass implements \ArrayAccess, \Countable, \IteratorAggregate
{
	/**
	 * Transforms array to ArrayHash.
	 * @param  array<T>  $array
	 */
	public static function from(array $array, bool $recursive = true): static
	{
		$obj = new static;
		foreach ($array as $key => $value) {
			$obj->$key = $recursive && is_array($value)
				? static::from($value)
				: $value;
		}

		return $obj;
	}


	/**
	 * @return \Iterator<array-key, T>
	 */
	public function &getIterator(): \Iterator
	{
		foreach ((array) $this as $key => $foo) {
			yield $key => $this->$key;
		}
	}


	public function count(): int
	{
		return count((array) $this);
	}


	/**
	 * Replaces or appends an item.
	 * @param  array-key  $key
	 * @param  T  $value
	 */
	public function offsetSet($key, $value): void
	{
		if (!is_scalar($key)) { // prevents null
			throw new Nette\InvalidArgumentException(sprintf('Key must be either a string or an integer, %s given.', get_debug_type($key)));
		}

		$this->$key = $value;
	}


	/**
	 * Returns an item.
	 * @param  array-key  $key
	 * @return T
	 */
	public function offsetGet($key): mixed
	{
		return $this->$key;
	}


	/**
	 * Determines whether an item exists.
	 * @param  array-key  $key
	 */
	public function offsetExists($key): bool
	{
		return isset($this->$key);
	}


	/**
	 * Removes the element from this list.
	 * @param  array-key  $key
	 */
	public function offsetUnset($key): void
	{
		unset($this->$key);
	}
}
