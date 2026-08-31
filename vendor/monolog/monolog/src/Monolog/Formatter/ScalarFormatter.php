<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

use Monolog\LogRecord;

/**
 * Formats data into an associative array of scalar (+ null) values.
 * Objects and arrays will be JSON encoded.
 *
 * @author Andrew Lawson <adlawson@gmail.com>
 */
class ScalarFormatter extends NormalizerFormatter
{
    /**
     * @inheritDoc
     *
     * @phpstan-return array<string, scalar|null> $record
     */
    public function format(LogRecord $record): array
    {
        $result = [];
        foreach ($record->toArray() as $key => $value) {
            $result[$key] = $this->toScalar($value);
        }

        return $result;
    }

    protected function toScalar(mixed $value): string|int|float|bool|null
    {
        $normalized = $this->normalize($value);

        if (\is_array($normalized)) {
            return $this->toJson($normalized, true);
        }

        return $normalized;
    }
}
