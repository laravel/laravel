<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Util;

/**
 * A static class to wrap JSON encoding/decoding with PsySH's default options.
 */
class Json
{
    /**
     * Encode a value as JSON.
     *
     * @param mixed $val
     * @param int   $opt
     */
    public static function encode($val, int $opt = 0): string
    {
        $opt |= \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE;

        return \json_encode($val, $opt);
    }
}
