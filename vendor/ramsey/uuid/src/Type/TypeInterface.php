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

namespace Ramsey\Uuid\Type;

use JsonSerializable;
use Serializable;

/**
 * TypeInterface ensures consistency in typed values returned by ramsey/uuid
 *
 * @immutable
 */
interface TypeInterface extends JsonSerializable, Serializable
{
    /**
     * @pure
     */
    public function toString(): string;

    /**
     * @pure
     */
    public function __toString(): string;
}
