<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use const SORT_STRING;
use function is_object;
use function is_scalar;
use function json_decode;
use function json_encode;
use function json_last_error;
use function ksort;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Json
{
    /**
     * @throws InvalidJsonException
     */
    public static function prettify(string $json): string
    {
        $decodedJson = json_decode($json, false);

        if (json_last_error()) {
            throw new InvalidJsonException;
        }

        return json_encode($decodedJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Element 0 is true and element 1 is null when JSON decoding did not work.
     * * Element 0 is false and element 1 has the decoded value when JSON decoding did work.
     * * This is used to avoid ambiguity with JSON strings consisting entirely of 'null' or 'false'.
     *
     * @return array{0: false, 1: mixed}|array{0: true, 1: null}
     */
    public static function canonicalize(string $json): array
    {
        $decodedJson = json_decode($json);

        if (json_last_error()) {
            return [true, null];
        }

        self::recursiveSort($decodedJson);

        $reencodedJson = json_encode($decodedJson);

        return [false, $reencodedJson];
    }

    /**
     * JSON object keys are unordered while PHP array keys are ordered.
     *
     * Sort all array keys to ensure both the expected and actual values have
     * their keys in the same order.
     */
    private static function recursiveSort(mixed &$json): void
    {
        // Nulls, empty arrays, and scalars need no further handling.
        if (!$json || is_scalar($json)) {
            return;
        }

        $isObject = is_object($json);

        if ($isObject) {
            // Objects need to be sorted during canonicalization to ensure
            // correct comparsion since JSON objects are unordered. It must be
            // kept as an object so that the value correctly stays as a JSON
            // object instead of potentially being converted to an array. This
            // approach ensures that numeric string JSON keys are preserved and
            // don't risk being flattened due to PHP's array semantics.
            // See #2919, #4584, #4674
            $json = (array) $json;
            ksort($json, SORT_STRING);
        }

        foreach ($json as &$value) {
            self::recursiveSort($value);
        }

        if ($isObject) {
            $json = (object) $json;
        }
    }
}
