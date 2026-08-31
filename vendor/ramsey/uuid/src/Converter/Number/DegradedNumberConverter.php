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

namespace Ramsey\Uuid\Converter\Number;

/**
 * @deprecated DegradedNumberConverter is no longer necessary for converting numbers on 32-bit systems. Please
 *     transition to {@see GenericNumberConverter}.
 *
 * @immutable
 */
class DegradedNumberConverter extends BigNumberConverter
{
}
