<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Exception;

/**
 * Thrown by Request::toArray() when the content cannot be JSON-decoded.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class JsonException extends UnexpectedValueException implements RequestExceptionInterface
{
}
