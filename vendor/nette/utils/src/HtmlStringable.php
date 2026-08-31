<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette;


/**
 * Represents object convertible to HTML string.
 */
interface HtmlStringable
{
	/**
	 * Returns string in HTML format.
	 */
	function __toString(): string;
}


interface_exists(Utils\IHtmlString::class);
