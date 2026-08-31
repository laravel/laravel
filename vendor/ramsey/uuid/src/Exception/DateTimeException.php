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

use RuntimeException as PhpRuntimeException;

/**
 * Thrown to indicate that the PHP DateTime extension encountered an exception/error
 */
class DateTimeException extends PhpRuntimeException implements UuidExceptionInterface
{
}
