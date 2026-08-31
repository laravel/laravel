<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Localization;


/**
 * Translation provider.
 */
interface Translator
{
	/**
	 * Translates the given string.
	 */
	function translate(string|\Stringable $message, mixed ...$parameters): string|\Stringable;
}


interface_exists(ITranslator::class);
