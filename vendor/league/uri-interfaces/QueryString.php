<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri;

use BackedEnum;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\KeyValuePair\Converter;
use ReflectionEnum;
use ReflectionException;
use SplObjectStorage;
use Stringable;
use TypeError;
use UnitEnum;
use ValueError;

use function array_is_list;
use function array_key_exists;
use function array_keys;
use function get_debug_type;
use function get_object_vars;
use function http_build_query;
use function implode;
use function is_array;
use function is_object;
use function is_resource;
use function is_scalar;
use function rawurldecode;
use function str_replace;
use function strpos;
use function substr;

use const PHP_QUERY_RFC1738;
use const PHP_QUERY_RFC3986;

/**
 * A class to parse the URI query string.
 *
 * @see https://tools.ietf.org/html/rfc3986#section-3.4
 */
final class QueryString
{
    private const PAIR_VALUE_DECODED = 1;
    private const PAIR_VALUE_PRESERVED = 2;
    private const RECURSION_MARKER = "\0__RECURSION_INTERNAL_MARKER__\0";

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Build a query string from a list of pairs.
     *
     * @see QueryString::buildFromPairs()
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-2.2
     *
     * @param iterable<array{0:string, 1:mixed}> $pairs
     * @param non-empty-string $separator
     *
     * @throws SyntaxError If the encoding type is invalid
     * @throws SyntaxError If a pair is invalid
     */
    public static function build(iterable $pairs, string $separator = '&', int $encType = PHP_QUERY_RFC3986, StringCoercionMode $coercionMode = StringCoercionMode::Native): ?string
    {
        return self::buildFromPairs($pairs, Converter::fromEncodingType($encType)->withSeparator($separator), $coercionMode);
    }

    /**
     * Build a query string from a list of pairs.
     *
     * The method expects the return value from Query::parse to build
     * a valid query string. This method differs from PHP http_build_query as
     * it does not modify parameters keys.
     *
     *  If a reserved character is found in a URI component and
     *  no delimiting role is known for that character, then it must be
     *  interpreted as representing the data octet corresponding to that
     *  character's encoding in US-ASCII.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-2.2
     *
     * @param iterable<array{0:string, 1:mixed}> $pairs
     *
     * @throws SyntaxError If the encoding type is invalid
     * @throws SyntaxError If a pair is invalid
     */
    public static function buildFromPairs(iterable $pairs, ?Converter $converter = null, StringCoercionMode $coercionMode = StringCoercionMode::Native): ?string
    {
        $keyValuePairs = [];
        foreach ($pairs as $pair) {
            if (!is_array($pair) || [0, 1] !== array_keys($pair)) {
                throw new SyntaxError('A pair must be a sequential array starting at `0` and containing two elements.');
            }

            [$key, $value] = $pair;
            $coercionMode->isCoercible($value) || throw new SyntaxError('Converting a type `'.get_debug_type($value).'` into a string is not supported by the '.(StringCoercionMode::Native === $coercionMode ? 'PHP Native' : 'Ecmascript').' coercion mode.');

            try {
                $key = $coercionMode->coerce($key);
                $value = $coercionMode->coerce($value);
            } catch (TypeError $typeError) {
                throw new SyntaxError('The pair can not be converted to build a query string.', previous: $typeError);
            }

            $keyValuePairs[] = [(string) Encoder::encodeQueryKeyValue($key), null === $value ? null : Encoder::encodeQueryKeyValue($value)];
        }

        return ($converter ?? Converter::fromRFC3986())->toValue($keyValuePairs);
    }

    /**
     * Build a query string from an object or an array like http_build_query without discarding values.
     * The method differs from http_build_query for the following behavior:
     *
     *  - if a resource is used, a TypeError is thrown.
     *  - if a recursion is detected a ValueError is thrown
     *  - the method preserves value with `null` value (http_build_query) skip the key.
     *  - the method does not handle prefix usage
     *
     * @param array<array-key, mixed> $data
     * @param non-empty-string $separator
     *
     * @throws TypeError if a resource is found it the input array
     * @throws ValueError if a recursion is detected
     */
    public static function compose(
        array|object $data,
        string $separator = '&',
        int $encType = PHP_QUERY_RFC1738,
        QueryComposeMode $composeMode = QueryComposeMode::Native
    ): ?string {
        if (QueryComposeMode::Native === $composeMode) {
            return http_build_query(data: $data, arg_separator: $separator, encoding_type: $encType);
        }

        $query = self::composeFromValue($data, Converter::fromEncodingType($encType)->withSeparator($separator), $composeMode);

        return QueryComposeMode::Safe !== $composeMode ? (string) $query : $query;
    }

    public static function composeFromValue(
        array|object $data,
        ?Converter $converter = null,
        QueryComposeMode $composeMode = QueryComposeMode::Native,
    ): ?string {
        if (QueryComposeMode::EnumLenient === $composeMode && $data instanceof UnitEnum && !$data instanceof BackedEnum) {
            return '';
        }

        QueryComposeMode::Safe !== $composeMode || is_array($data) || throw new TypeError('In safe mode only arrays are supported.');

        $converter ??= Converter::fromRFC3986();

        $pairs = QueryComposeMode::Native !== $composeMode
            ? self::composeRecursive($composeMode, $data)
            : self::parseFromValue(http_build_query(data: $data, arg_separator: '&'), Converter::fromRFC1738());

        return self::buildFromPairs($pairs, $converter);
    }

    /**
     * @param array<array-key, mixed>|object $data
     * @param SplObjectStorage<object, null> $seenObjects
     *
     * @throws TypeError if a resource is found it the input array
     * @throws ValueError if a recursion is detected
     * @throws ReflectionException if reflection is not possible on the Enum
     *
     * @return iterable<array{0: array-key, 1: string|int|float|bool|null}>
     */
    private static function composeRecursive(
        QueryComposeMode $composeMode,
        array|object $data,
        string|int $prefix = '',
        SplObjectStorage $seenObjects = new SplObjectStorage(),
    ): iterable {
        QueryComposeMode::Safe !== $composeMode || is_array($data) || throw new TypeError('In safe mode only arrays are supported.');
        in_array($composeMode, [QueryComposeMode::EnumCompatible, QueryComposeMode::EnumLenient], true) || !$data instanceof UnitEnum || throw new TypeError('Argument #1 ($data) must not be an enum, '.((new ReflectionEnum($data::class))->isBacked() ? 'Backed' : 'Pure').' given') ;

        if (is_object($data)) {
            if ($seenObjects->contains($data)) {
                QueryComposeMode::Safe !== $composeMode || throw new ValueError('composition failed; circular reference detected.');

                return;
            }

            $seenObjects->attach($data);
            $data = get_object_vars($data);
        }

        if (self::hasCircularReference($data)) {
            QueryComposeMode::Safe !== $composeMode || throw new ValueError('composition failed; circular reference detected.');

            return;
        }

        $stripIndices = QueryComposeMode::Safe === $composeMode && array_is_list($data);

        foreach ($data as $name => $value) {
            $name = $stripIndices ? '' : $name;
            if ('' !== $prefix) {
                $name = $prefix.'['.$name.']';
            }

            if (is_resource($value)) {
                QueryComposeMode::Safe !== $composeMode || throw new TypeError('composition failed; a resource has been detected and can not be converted.');
                continue;
            }

            if (is_scalar($value)) {
                yield [$name, $value];

                continue;
            }

            if (null === $value) {
                if (QueryComposeMode::Safe === $composeMode) {
                    yield [$name, $value];
                }

                continue;
            }

            if ($value instanceof BackedEnum) {
                if (QueryComposeMode::Compatible !== $composeMode) {
                    yield [$name, $value->value];

                    continue;
                }

                $value = get_object_vars($value);
            }

            if ($value instanceof UnitEnum) {
                if (QueryComposeMode::EnumLenient === $composeMode) {
                    continue;
                }

                QueryComposeMode::Compatible === $composeMode || throw new TypeError('Unbacked enum '.$value::class.' cannot be converted to a string');

                $value = get_object_vars($value);
            }

            if (QueryComposeMode::Safe === $composeMode && is_object($value)) {
                throw new ValueError('In conservative mode only arrays, scalar value or null are supported.');
            }

            yield from self::composeRecursive($composeMode, $value, $name, $seenObjects);
        }
    }

    /**
     * Array recursion detection.
     * @see https://stackoverflow.com/questions/9042142/detecting-infinite-array-recursion-in-php
     */
    private static function hasCircularReference(array &$arr): bool
    {
        if (isset($arr[self::RECURSION_MARKER])) {
            return true;
        }

        try {
            $arr[self::RECURSION_MARKER] = true;
            foreach ($arr as $key => &$value) {
                if (self::RECURSION_MARKER !== $key && is_array($value) && self::hasCircularReference($value)) {
                    return true;
                }
            }

            return false;
        } finally {
            unset($arr[self::RECURSION_MARKER]);
        }
    }

    /**
     * Parses the query string.
     *
     * The result depends on the query parsing mode
     *
     * @see QueryString::extractFromValue()
     *
     * @param non-empty-string $separator
     *
     * @throws SyntaxError
     */
    public static function extract(
        BackedEnum|Stringable|string|bool|null $query,
        string $separator = '&',
        int $encType = PHP_QUERY_RFC3986,
        QueryExtractMode $extractMode = QueryExtractMode::Unmangled,
    ): array {
        return self::extractFromValue(
            $query,
            Converter::fromEncodingType($encType)->withSeparator($separator),
            $extractMode,
        );
    }

    /**
     * Parses the query string.
     *
     * The result depends on the query parsing mode
     *
     * @throws SyntaxError
     */
    public static function extractFromValue(
        BackedEnum|Stringable|string|bool|null $query,
        ?Converter $converter = null,
        QueryExtractMode $extractMode = QueryExtractMode::Unmangled,
    ): array {
        $pairs = ($converter ?? Converter::fromRFC3986())->toPairs($query);
        if (QueryExtractMode::Native === $extractMode) {
            if ([] === $pairs) {
                return [];
            }

            $data = [];
            foreach ($pairs as [$key, $value]) {
                $key = str_replace('&', '%26', (string) $key);
                $data[] = null === $value ? $key : $key.'='.str_replace('&', '%26', $value);
            }

            parse_str(implode('&', $data), $result);

            return $result;
        }

        return self::convert(
            self::decodePairs($pairs, self::PAIR_VALUE_PRESERVED),
            $extractMode
        );
    }

    /**
     * Parses a query string into a collection of key/value pairs.
     *
     * @param non-empty-string $separator
     *
     * @throws SyntaxError
     *
     * @return array<int, array{0:string, 1:string|null}>
     */
    public static function parse(BackedEnum|Stringable|string|bool|null $query, string $separator = '&', int $encType = PHP_QUERY_RFC3986): array
    {
        return self::parseFromValue($query, Converter::fromEncodingType($encType)->withSeparator($separator));
    }

    /**
     * Parses a query string into a collection of key/value pairs.
     *
     * @throws SyntaxError
     *
     * @return array<int, array{0:string, 1:string|null}>
     */
    public static function parseFromValue(BackedEnum|Stringable|string|bool|null $query, ?Converter $converter = null): array
    {
        return self::decodePairs(
            ($converter ?? Converter::fromRFC3986())->toPairs($query),
            self::PAIR_VALUE_DECODED
        );
    }

    /**
     * @param array<non-empty-list<string|null>> $pairs
     *
     * @return array<int, array{0:string, 1:string|null}>
     */
    private static function decodePairs(array $pairs, int $pairValueState): array
    {
        $decodePair = static function (array $pair, int $pairValueState): array {
            [$key, $value] = $pair;

            return match ($pairValueState) {
                self::PAIR_VALUE_PRESERVED => [(string) Encoder::decodeAll($key), $value],
                default => [(string) Encoder::decodeAll($key), Encoder::decodeAll($value)],
            };
        };

        return array_reduce(
            $pairs,
            fn (array $carry, array $pair) => [...$carry, $decodePair($pair, $pairValueState)],
            []
        );
    }

    /**
     * Converts a collection of key/value pairs and returns
     * the store PHP variables as elements of an array.
     */
    public static function convert(iterable $pairs, QueryExtractMode $extractMode = QueryExtractMode::Unmangled): array
    {
        $returnedValue = [];
        foreach ($pairs as $pair) {
            $returnedValue = self::extractPhpVariable($returnedValue, $pair, extractMode: $extractMode);
        }

        return $returnedValue;
    }

    /**
     * Parses a query pair like parse_str without mangling the results array keys.
     *
     * <ul>
     * <li>empty name are not saved</li>
     * <li>If the value from name is duplicated its corresponding value will be overwritten</li>
     * <li>if no "[" is detected the value is added to the return array with the name as index</li>
     * <li>if no "]" is detected after detecting a "[" the value is added to the return array with the name as index</li>
     * <li>if there's a mismatch in bracket usage the remaining part is dropped</li>
     * <li>“.” and “ ” are not converted to “_”</li>
     * <li>If there is no “]”, then the first “[” is not converted to becomes an “_”</li>
     * <li>no whitespace trimming is done on the key value</li>
     * </ul>
     *
     * @see https://php.net/parse_str
     * @see https://wiki.php.net/rfc/on_demand_name_mangling
     * @see https://github.com/php/php-src/blob/master/ext/standard/tests/strings/parse_str_basic1.phpt
     * @see https://github.com/php/php-src/blob/master/ext/standard/tests/strings/parse_str_basic2.phpt
     * @see https://github.com/php/php-src/blob/master/ext/standard/tests/strings/parse_str_basic3.phpt
     * @see https://github.com/php/php-src/blob/master/ext/standard/tests/strings/parse_str_basic4.phpt
     *
     * @param array $data the submitted array
     * @param array|string $name the pair key
     * @param string $value the pair value
     */
    private static function extractPhpVariable(
        array $data,
        array|string $name,
        ?string $value = '',
        QueryExtractMode $extractMode = QueryExtractMode::Unmangled
    ): array {
        if (is_array($name)) {
            [$name, $value] = $name;
            if (null !== $value || QueryExtractMode::LossLess !== $extractMode) {
                $value = rawurldecode((string) $value);
            }
        }

        if ('' === $name) {
            return $data;
        }

        $leftBracketPosition = strpos($name, '[');
        if (false === $leftBracketPosition) {
            $data[$name] = $value;

            return $data;
        }

        $rightBracketPosition = strpos($name, ']', $leftBracketPosition);
        if (false === $rightBracketPosition) {
            $data[$name] = $value;

            return $data;
        }

        $key = substr($name, 0, $leftBracketPosition);
        if ('' === $key) {
            $key = '0';
        }

        if (!array_key_exists($key, $data) || !is_array($data[$key])) {
            $data[$key] = [];
        }

        $remaining = substr($name, $rightBracketPosition + 1);
        if (!str_starts_with($remaining, '[') || !str_contains($remaining, ']')) {
            $remaining = '';
        }

        $name = substr($name, $leftBracketPosition + 1, $rightBracketPosition - $leftBracketPosition - 1).$remaining;
        if ('' === $name) {
            $data[$key][] = $value;

            return $data;
        }

        $data[$key] = self::extractPhpVariable($data[$key], $name, $value, $extractMode);

        return $data;
    }
}
