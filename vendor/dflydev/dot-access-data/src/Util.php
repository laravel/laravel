<?php

declare(strict_types=1);

/*
 * This file is a part of dflydev/dot-access-data.
 *
 * (c) Dragonfly Development Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dflydev\DotAccessData;

class Util
{
    /**
     * Test if array is an associative array
     *
     * Note that this function will return true if an array is empty. Meaning
     * empty arrays will be treated as if they are associative arrays.
     *
     * @param array<mixed> $arr
     *
     * @return bool
     *
     * @psalm-pure
     */
    public static function isAssoc(array $arr): bool
    {
        return !count($arr) || count(array_filter(array_keys($arr), 'is_string')) == count($arr);
    }

    /**
     * Merge contents from one associtative array to another
     *
     * @param mixed $to
     * @param mixed $from
     * @param DataInterface::PRESERVE|DataInterface::REPLACE|DataInterface::MERGE $mode
     *
     * @return mixed
     *
     * @psalm-pure
     */
    public static function mergeAssocArray($to, $from, int $mode = DataInterface::REPLACE)
    {
        if ($mode === DataInterface::MERGE && self::isList($to) && self::isList($from)) {
            return array_merge($to, $from);
        }

        if (is_array($from) && is_array($to)) {
            foreach ($from as $k => $v) {
                if (!isset($to[$k])) {
                    $to[$k] = $v;
                } else {
                    $to[$k] = self::mergeAssocArray($to[$k], $v, $mode);
                }
            }

            return $to;
        }

        return $mode === DataInterface::PRESERVE ? $to : $from;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     *
     * @psalm-pure
     */
    private static function isList($value): bool
    {
        return is_array($value) && array_values($value) === $value;
    }
}
