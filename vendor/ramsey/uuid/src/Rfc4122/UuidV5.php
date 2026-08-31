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

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Ramsey\Uuid\Uuid;

/**
 * Version 5 UUIDs are named-based, using a combination of a namespace and name that are hashed into a 128-bit unsigned
 * integer using the SHA1 hashing algorithm
 *
 * @link https://www.rfc-editor.org/rfc/rfc9562#section-5.5 RFC 9562, 5.5. UUID Version 5
 *
 * @immutable
 */
final class UuidV5 extends Uuid implements UuidInterface
{
    /**
     * Creates a version 5 (name-based, SHA1-hashed) UUID
     *
     * @param Rfc4122FieldsInterface $fields The fields from which to construct a UUID
     * @param NumberConverterInterface $numberConverter The number converter to use for converting hex values to/from integers
     * @param CodecInterface $codec The codec to use when encoding or decoding UUID strings
     * @param TimeConverterInterface $timeConverter The time converter to use for converting timestamps extracted from a
     *     UUID to unix timestamps
     */
    public function __construct(
        Rfc4122FieldsInterface $fields,
        NumberConverterInterface $numberConverter,
        CodecInterface $codec,
        TimeConverterInterface $timeConverter,
    ) {
        if ($fields->getVersion() !== Uuid::UUID_TYPE_HASH_SHA1) {
            throw new InvalidArgumentException(
                'Fields used to create a UuidV5 must represent a version 5 (named-based, SHA1-hashed) UUID',
            );
        }

        parent::__construct($fields, $numberConverter, $codec, $timeConverter);
    }
}
