<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Exception;

/**
 * Thrown to indicate that the string received is not a valid UUID
 *
 * The InvalidArgumentException that this extends is the ramsey/uuid version of this exception. It exists in the same
 * namespace as this class.
 */
class InvalidUuidStringException extends InvalidArgumentException implements UuidExceptionInterface
{
}
