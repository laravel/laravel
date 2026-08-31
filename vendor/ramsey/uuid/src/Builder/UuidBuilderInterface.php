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

namespace Ramsey\Uuid\Builder;

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * A UUID builder builds instances of UuidInterface
 *
 * @immutable
 */
interface UuidBuilderInterface
{
    /**
     * Builds and returns a UuidInterface
     *
     * @param CodecInterface $codec The codec to use for building this UuidInterface instance
     * @param string $bytes The byte string from which to construct a UUID
     *
     * @return UuidInterface Implementations may choose to return more specific instances of UUIDs that implement UuidInterface
     *
     * @pure
     */
    public function build(CodecInterface $codec, string $bytes): UuidInterface;
}
