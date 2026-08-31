<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use function explode, is_string, str_contains;


/**
 * ReflectionMethod preserving the original class name.
 * @internal
 */
final class ReflectionMethod extends \ReflectionMethod
{
	/** @var \ReflectionClass<object> */
	private readonly \ReflectionClass $originalClass;


	/** @param  class-string|object  $objectOrMethod */
	public function __construct(object|string $objectOrMethod, ?string $method = null)
	{
		if (is_string($objectOrMethod) && str_contains($objectOrMethod, '::')) {
			[$objectOrMethod, $method] = explode('::', $objectOrMethod, 2);
		}
		parent::__construct($objectOrMethod, $method);
		$this->originalClass = new \ReflectionClass($objectOrMethod);
	}


	/** @return \ReflectionClass<object> */
	public function getOriginalClass(): \ReflectionClass
	{
		return $this->originalClass;
	}
}
