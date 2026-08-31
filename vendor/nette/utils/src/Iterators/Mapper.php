<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Iterators;


/**
 * @deprecated use Nette\Utils\Iterables::map()
 */
class Mapper extends \IteratorIterator
{
	private \Closure $callback;


	public function __construct(\Traversable $iterator, callable $callback)
	{
		parent::__construct($iterator);
		$this->callback = $callback(...);
	}


	public function current(): mixed
	{
		return ($this->callback)(parent::current(), parent::key());
	}
}
