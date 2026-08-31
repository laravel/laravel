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

namespace Ramsey\Uuid\Rfc4122;

use Ramsey\Uuid\UuidInterface as BaseUuidInterface;

/**
 * A universally unique identifier (UUID), as defined in RFC 9562 (formerly RFC 4122)
 *
 * @link https://www.rfc-editor.org/rfc/rfc9562 RFC 9562
 *
 * @immutable
 */
interface UuidInterface extends BaseUuidInterface
{
}
