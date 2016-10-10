<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

/**
 * Formats data into an associative array of scalar values.
 * Objects and arrays will be JSON encoded.
 *
 * @author Andrew Lawson <adlawson@gmail.com>
 */
class ScalarFormatter extends NormalizerFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        foreach ($record as $key => $value) {
            $record[$key] = $this->normalizeValue($value);
        }

        return $record;
    }

    /**
     * @param  mixed $value
     * @return mixed
     */
    protected function normalizeValue($value)
    {
        $normalized = $this->normalize($value);

        if (is_array($normalized) || is_object($normalized)) {
            return $this->toJson($normalized, true);
        }

        return $normalized;
    }
}
