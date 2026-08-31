<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog;

class_alias(JsonSerializableDateTimeImmutable::class, 'Monolog\DateTimeImmutable');

// @phpstan-ignore-next-line
if (false) {
    /**
     * @deprecated Use \Monolog\JsonSerializableDateTimeImmutable instead.
     */
    class DateTimeImmutable extends JsonSerializableDateTimeImmutable
    {
    }
}
