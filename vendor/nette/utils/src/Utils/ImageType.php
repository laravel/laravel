<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use const IMAGETYPE_BMP, IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP;


/**
 * Type of image file.
 */
/*enum*/ final class ImageType
{
	public const
		JPEG = IMAGETYPE_JPEG,
		PNG = IMAGETYPE_PNG,
		GIF = IMAGETYPE_GIF,
		WEBP = IMAGETYPE_WEBP,
		AVIF = 19, // IMAGETYPE_AVIF,
		BMP = IMAGETYPE_BMP;
}
