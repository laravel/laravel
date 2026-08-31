<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Schema;


interface Schema
{
	/**
	 * Normalization.
	 * @return mixed
	 */
	function normalize(mixed $value, Context $context);

	/**
	 * Merging.
	 * @return mixed
	 */
	function merge(mixed $value, mixed $base);

	/**
	 * Validation and finalization.
	 * @return mixed
	 */
	function complete(mixed $value, Context $context);

	/**
	 * @return mixed
	 */
	function completeDefault(Context $context);
}
