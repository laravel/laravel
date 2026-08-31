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

namespace Ramsey\Uuid\Nonstandard;

use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\UuidInterface;
use Throwable;

/**
 * Nonstandard\UuidBuilder builds instances of Nonstandard\Uuid
 *
 * @immutable
 */
class UuidBuilder implements UuidBuilderInterface
{
    /**
     * @param NumberConverterInterface $numberConverter The number converter to use when constructing the Nonstandard\Uuid
     * @param TimeConverterInterface $timeConverter The time converter to use for converting timestamps extracted from a
     *     UUID to Unix timestamps
     */
    public function __construct(
        private NumberConverterInterface $numberConverter,
        private TimeConverterInterface $timeConverter,
    ) {
    }

    /**
     * Builds and returns a Nonstandard\Uuid
     *
     * @param CodecInterface $codec The codec to use for building this instance
     * @param string $bytes The byte string from which to construct a UUID
     *
     * @return Uuid The Nonstandard\UuidBuilder returns an instance of Nonstandard\Uuid
     *
     * @pure
     */
    public function build(CodecInterface $codec, string $bytes): UuidInterface
    {
        try {
            /** @phpstan-ignore possiblyImpure.new */
            return new Uuid($this->buildFields($bytes), $this->numberConverter, $codec, $this->timeConverter);
        } catch (Throwable $e) {
            /** @phpstan-ignore possiblyImpure.methodCall, possiblyImpure.methodCall */
            throw new UnableToBuildUuidException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Proxy method to allow injecting a mock for testing
     *
     * @pure
     */
    protected function buildFields(string $bytes): Fields
    {
        /** @phpstan-ignore possiblyImpure.new */
        return new Fields($bytes);
    }
}
