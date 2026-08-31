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

namespace Ramsey\Uuid;

/**
 * @deprecated DegradedUuid is no longer necessary to represent UUIDs on 32-bit systems.
 *     Transition any type declarations using this class to {@see UuidInterface}.
 *
 * @immutable
 */
class DegradedUuid extends Uuid
{
}
