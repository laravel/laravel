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

namespace Ramsey\Uuid\Codec;

/**
 * TimestampLastCombCodec encodes and decodes COMBs, with the timestamp as the last 48 bits
 *
 * The CombGenerator when used with the StringCodec (and, by proxy, the TimestampLastCombCodec) adds the timestamp to
 * the last 48 bits of the COMB. The TimestampLastCombCodec is provided for the sake of consistency. In practice, it is
 * identical to the standard StringCodec, but it may be used with the CombGenerator for additional context when reading
 * code.
 *
 * Consider the following code. By default, the codec used by UuidFactory is the StringCodec, but here, we explicitly
 * set the TimestampLastCombCodec. It is redundant, but it is clear that we intend this COMB to be generated with the
 * timestamp appearing at the end.
 *
 * ```
 * $factory = new UuidFactory();
 *
 * $factory->setCodec(new TimestampLastCombCodec($factory->getUuidBuilder()));
 *
 * $factory->setRandomGenerator(new CombGenerator(
 *     $factory->getRandomGenerator(),
 *     $factory->getNumberConverter(),
 * ));
 *
 * $timestampLastComb = $factory->uuid4();
 * ```
 *
 * @deprecated Please use {@see StringCodec} instead.
 *
 * @immutable
 */
class TimestampLastCombCodec extends StringCodec
{
}
