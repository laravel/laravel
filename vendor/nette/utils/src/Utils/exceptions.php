<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;


/**
 * An error occurred while working with the image.
 */
class ImageException extends \Exception
{
}


/**
 * The image file is invalid or in an unsupported format.
 */
class UnknownImageFileException extends ImageException
{
}


/**
 * JSON encoding or decoding failed.
 */
class JsonException extends \JsonException
{
}


/**
 * Regular expression pattern or execution failed.
 */
class RegexpException extends \Exception
{
}


/**
 * Type validation failed. The value doesn't match the expected type constraints.
 */
class AssertionException extends \Exception
{
}


/**
 * The process failed to run successfully.
 */
class ProcessFailedException extends \RuntimeException
{
}


/**
 * The process execution exceeded its timeout limit.
 */
class ProcessTimeoutException extends \RuntimeException
{
}
