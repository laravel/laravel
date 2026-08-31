<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette;


/**
 * Prevents instantiation.
 */
trait StaticClass
{
	/**
	 * Class is static and cannot be instantiated.
	 */
	private function __construct()
	{
	}
}
