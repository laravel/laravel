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

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;

use function bin2hex;
use function explode;
use function hex2bin;
use function microtime;
use function str_pad;
use function substr;

use const STR_PAD_LEFT;

/**
 * CombGenerator generates COMBs (combined UUID/timestamp)
 *
 * The CombGenerator, when used with the StringCodec (and, by proxy, the TimestampLastCombCodec) or the
 * TimestampFirstCombCodec, combines the current timestamp with a UUID (hence the name "COMB"). The timestamp either
 * appears as the first or last 48 bits of the COMB, depending on the codec used.
 *
 * By default, COMBs will have the timestamp set as the last 48 bits of the identifier.
 *
 * ```
 * $factory = new UuidFactory();
 *
 * $factory->setRandomGenerator(new CombGenerator(
 *     $factory->getRandomGenerator(),
 *     $factory->getNumberConverter(),
 * ));
 *
 * $comb = $factory->uuid4();
 * ```
 *
 * To generate a COMB with the timestamp as the first 48 bits, set the TimestampFirstCombCodec as the codec.
 *
 * ```
 * $factory->setCodec(new TimestampFirstCombCodec($factory->getUuidBuilder()));
 * ```
 *
 * @deprecated Please migrate to {@link https://uuid.ramsey.dev/en/stable/rfc4122/version7.html Version 7, Unix Epoch Time UUIDs}.
 *
 * @link https://web.archive.org/web/20240118030355/https://www.informit.com/articles/printerfriendly/25862 The Cost of GUIDs as Primary Keys
 */
class CombGenerator implements RandomGeneratorInterface
{
    public const TIMESTAMP_BYTES = 6;

    public function __construct(
        private RandomGeneratorInterface $generator,
        private NumberConverterInterface $numberConverter
    ) {
    }

    /**
     * @throws InvalidArgumentException if $length is not a positive integer greater than or equal to CombGenerator::TIMESTAMP_BYTES
     *
     * @inheritDoc
     */
    public function generate(int $length): string
    {
        if ($length < self::TIMESTAMP_BYTES) {
            throw new InvalidArgumentException(
                'Length must be a positive integer greater than or equal to ' . self::TIMESTAMP_BYTES
            );
        }

        if ($length % 2 !== 0) {
            throw new InvalidArgumentException('Length must be an even number');
        }

        $hash = '';

        /** @phpstan-ignore greater.alwaysTrue (TIMESTAMP_BYTES constant could change in child classes) */
        if (self::TIMESTAMP_BYTES > 0 && $length > self::TIMESTAMP_BYTES) {
            $hash = $this->generator->generate($length - self::TIMESTAMP_BYTES);
        }

        $lsbTime = str_pad(
            $this->numberConverter->toHex($this->timestamp()),
            self::TIMESTAMP_BYTES * 2,
            '0',
            STR_PAD_LEFT,
        );

        return (string) hex2bin(str_pad(bin2hex($hash), $length - self::TIMESTAMP_BYTES, '0') . $lsbTime);
    }

    /**
     * Returns the current timestamp as a string integer, precise to 0.00001 seconds
     */
    private function timestamp(): string
    {
        $time = explode(' ', microtime(false));

        return $time[1] . substr($time[0], 2, 5);
    }
}
