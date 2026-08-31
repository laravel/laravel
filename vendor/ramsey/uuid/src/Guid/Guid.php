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

namespace Ramsey\Uuid\Guid;

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Uuid;

/**
 * Guid represents a UUID with "native" (little-endian) byte order
 *
 * From Wikipedia:
 *
 * > The first three fields are unsigned 32- and 16-bit integers and are subject to swapping, while the last two fields
 * > consist of uninterpreted bytes, not subject to swapping. This byte swapping applies even for versions 3, 4, and 5,
 * > where the canonical fields do not correspond to the content of the UUID.
 *
 * The first three fields of a GUID are encoded in little-endian byte order, while the last three fields are in network
 * (big-endian) byte order. This is according to the history of the Microsoft GUID definition.
 *
 * According to the .NET Guid.ToByteArray method documentation:
 *
 * > Note that the order of bytes in the returned byte array is different from the string representation of a Guid value.
 * > The order of the beginning four-byte group and the next two two-byte groups is reversed, whereas the order of the
 * > last two-byte group and the closing six-byte group is the same.
 *
 * @link https://en.wikipedia.org/wiki/Universally_unique_identifier#Variants UUID Variants on Wikipedia
 * @link https://docs.microsoft.com/en-us/windows/win32/api/guiddef/ns-guiddef-guid Windows GUID structure
 * @link https://docs.microsoft.com/en-us/dotnet/api/system.guid .NET Guid Struct
 * @link https://docs.microsoft.com/en-us/dotnet/api/system.guid.tobytearray .NET Guid.ToByteArray Method
 *
 * @immutable
 */
final class Guid extends Uuid
{
    public function __construct(
        Fields $fields,
        NumberConverterInterface $numberConverter,
        CodecInterface $codec,
        TimeConverterInterface $timeConverter,
    ) {
        parent::__construct($fields, $numberConverter, $codec, $timeConverter);
    }
}
