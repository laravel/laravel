<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette;


/**
 * The value is outside the allowed range.
 */
class ArgumentOutOfRangeException extends \InvalidArgumentException
{
}


/**
 * The object is in a state that does not allow the requested operation.
 */
class InvalidStateException extends \RuntimeException
{
}


/**
 * The requested feature is not implemented.
 */
class NotImplementedException extends \LogicException
{
}


/**
 * The requested operation is not supported.
 */
class NotSupportedException extends \LogicException
{
}


/**
 * The requested feature is deprecated and no longer available.
 */
class DeprecatedException extends NotSupportedException
{
}


/**
 * Cannot access the requested class property or method.
 */
class MemberAccessException extends \Error
{
}


/**
 * Failed to read from or write to a file or stream.
 */
class IOException extends \RuntimeException
{
}


/**
 * The requested file does not exist.
 */
class FileNotFoundException extends IOException
{
}


/**
 * The requested directory does not exist.
 */
class DirectoryNotFoundException extends IOException
{
}


/**
 * The provided argument has invalid type or format.
 */
class InvalidArgumentException extends \InvalidArgumentException
{
}


/**
 * The requested array or collection index does not exist.
 */
class OutOfRangeException extends \OutOfRangeException
{
}


/**
 * The returned value has unexpected type or format.
 */
class UnexpectedValueException extends \UnexpectedValueException
{
}


/**
 * Houston, we have a problem.
 */
class ShouldNotHappenException extends \LogicException
{
}
