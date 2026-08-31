<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Yaml;

use Symfony\Component\Yaml\Exception\DumpException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Tag\TaggedValue;

/**
 * Inline implements a YAML parser/dumper for the YAML inline syntax.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
class Inline
{
    public const REGEX_QUOTED_STRING = '(?:"([^"\\\\]*+(?:\\\\.[^"\\\\]*+)*+)"|\'([^\']*+(?:\'\'[^\']*+)*+)\')';

    public static int $parsedLineNumber = -1;
    public static ?string $parsedFilename = null;

    private static bool $exceptionOnInvalidType = false;
    private static bool $objectSupport = false;
    private static bool $objectForMap = false;
    private static bool $constantSupport = false;

    public static function initialize(int $flags, ?int $parsedLineNumber = null, ?string $parsedFilename = null): void
    {
        self::$exceptionOnInvalidType = (bool) (Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE & $flags);
        self::$objectSupport = (bool) (Yaml::PARSE_OBJECT & $flags);
        self::$objectForMap = (bool) (Yaml::PARSE_OBJECT_FOR_MAP & $flags);
        self::$constantSupport = (bool) (Yaml::PARSE_CONSTANT & $flags);
        self::$parsedFilename = $parsedFilename;

        if (null !== $parsedLineNumber) {
            self::$parsedLineNumber = $parsedLineNumber;
        }
    }

    /**
     * Converts a YAML string to a PHP value.
     *
     * @param int   $flags      A bit field of Yaml::PARSE_* constants to customize the YAML parser behavior
     * @param array $references Mapping of variable names to values
     *
     * @throws ParseException
     */
    public static function parse(string $value, int $flags = 0, array &$references = [], ?ParserState $state = null): mixed
    {
        self::initialize($flags);
        $state ??= new ParserState();

        $value = trim($value);

        if ('' === $value) {
            return '';
        }

        $i = 0;
        $isQuoted = null;
        $tag = self::parseTag($value, $i, $flags);
        switch ($value[$i]) {
            case '[':
                $result = self::parseSequence($state, $value, $flags, $i, $references);
                ++$i;
                break;
            case '{':
                $result = self::parseMapping($state, $value, $flags, $i, $references);
                ++$i;
                break;
            default:
                $result = self::parseScalar($value, $flags, null, $i, true, $references, $isQuoted, $state);
        }

        // some comments are allowed at the end
        if (preg_replace('/\s*#.*$/A', '', substr($value, $i))) {
            throw new ParseException(\sprintf('Unexpected characters near "%s".', substr($value, $i)), self::$parsedLineNumber + 1, $value, self::$parsedFilename);
        }

        if (null !== $tag && '' !== $tag) {
            return new TaggedValue($tag, $result);
        }

        return $result;
    }

    /**
     * Dumps a given PHP variable to a YAML string.
     *
     * @param mixed $value The PHP variable to convert
     * @param int   $flags A bit field of Yaml::DUMP_* constants to customize the dumped YAML string
     *
     * @throws DumpException When trying to dump PHP resource
     */
    public static function dump(mixed $value, int $flags = 0, bool $rootLevel = false): string
    {
        switch (true) {
            case \is_resource($value):
                if (Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE & $flags) {
                    throw new DumpException(\sprintf('Unable to dump PHP resources in a YAML file ("%s").', get_resource_type($value)));
                }

                return self::dumpNull($flags);
            case $value instanceof \DateTimeInterface:
                return $value->format(match (true) {
                    !$length = \strlen(rtrim($value->format('u'), '0')) => 'c',
                    $length < 4 => 'Y-m-d\TH:i:s.vP',
                    default => 'Y-m-d\TH:i:s.uP',
                });
            case $value instanceof \UnitEnum:
                return \sprintf('!php/enum %s::%s', $value::class, $value->name);
            case \is_object($value):
                if ($value instanceof TaggedValue) {
                    return '!'.$value->getTag().' '.self::dump($value->getValue(), $flags);
                }

                if (Yaml::DUMP_OBJECT & $flags) {
                    return '!php/object '.self::dump(serialize($value));
                }

                if (Yaml::DUMP_OBJECT_AS_MAP & $flags && ($value instanceof \stdClass || $value instanceof \ArrayObject)) {
                    return self::dumpHashArray($value, $flags);
                }

                if (Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE & $flags) {
                    throw new DumpException('Object support when dumping a YAML file has been disabled.');
                }

                return self::dumpNull($flags);
            case \is_array($value):
                return self::dumpArray($value, $flags);
            case null === $value:
                return self::dumpNull($flags, $rootLevel);
            case true === $value:
                return 'true';
            case false === $value:
                return 'false';
            case \is_int($value):
                return $value;
            case is_numeric($value) && false === strpbrk($value, "\f\n\r\t\v"):
                $locale = setlocale(\LC_NUMERIC, 0);
                if (false !== $locale) {
                    setlocale(\LC_NUMERIC, 'C');
                }
                if (\is_float($value)) {
                    $repr = (string) $value;
                    if (is_infinite($value)) {
                        $repr = str_ireplace('INF', '.Inf', $repr);
                    } elseif (floor($value) == $value && $repr == $value) {
                        // Preserve float data type since storing a whole number will result in integer value.
                        if (!str_contains($repr, 'E')) {
                            $repr .= '.0';
                        }
                    }
                } else {
                    $repr = \is_string($value) ? "'$value'" : (string) $value;
                }
                if (false !== $locale) {
                    setlocale(\LC_NUMERIC, $locale);
                }

                return $repr;
            case '' == $value:
                return "''";
            case self::isBinaryString($value):
                return '!!binary '.base64_encode($value);
            case Escaper::requiresDoubleQuoting($value):
            case Yaml::DUMP_FORCE_DOUBLE_QUOTES_ON_VALUES & $flags:
                return Escaper::escapeWithDoubleQuotes($value);
            case Escaper::requiresSingleQuoting($value):
                $singleQuoted = Escaper::escapeWithSingleQuotes($value);
                if (!str_contains($value, "'")) {
                    return $singleQuoted;
                }
                // Attempt double-quoting the string instead to see if it's more efficient.
                $doubleQuoted = Escaper::escapeWithDoubleQuotes($value);

                return \strlen($doubleQuoted) < \strlen($singleQuoted) ? $doubleQuoted : $singleQuoted;
            case Parser::preg_match('{^[0-9]+[_0-9]*$}', $value):
            case Parser::preg_match(self::getHexRegex(), $value):
            case Parser::preg_match(self::getTimestampRegex(), $value):
                return Escaper::escapeWithSingleQuotes($value);
            default:
                return $value;
        }
    }

    /**
     * Check if given array is hash or just normal indexed array.
     */
    public static function isHash(array|\ArrayObject|\stdClass $value): bool
    {
        if ($value instanceof \stdClass || $value instanceof \ArrayObject) {
            return true;
        }

        $expectedKey = 0;

        foreach ($value as $key => $val) {
            if ($key !== $expectedKey++) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dumps a PHP array to a YAML string.
     *
     * @param array $value The PHP array to dump
     * @param int   $flags A bit field of Yaml::DUMP_* constants to customize the dumped YAML string
     */
    private static function dumpArray(array $value, int $flags): string
    {
        // array
        if (($value || Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE & $flags) && !self::isHash($value)) {
            $output = [];
            foreach ($value as $val) {
                $output[] = self::dump($val, $flags);
            }

            return \sprintf('[%s]', implode(', ', $output));
        }

        return self::dumpHashArray($value, $flags);
    }

    /**
     * Dumps hash array to a YAML string.
     *
     * @param array|\ArrayObject|\stdClass $value The hash array to dump
     * @param int                          $flags A bit field of Yaml::DUMP_* constants to customize the dumped YAML string
     */
    private static function dumpHashArray(array|\ArrayObject|\stdClass $value, int $flags): string
    {
        $output = [];
        $keyFlags = $flags & ~Yaml::DUMP_FORCE_DOUBLE_QUOTES_ON_VALUES;
        foreach ($value as $key => $val) {
            if (\is_int($key) && Yaml::DUMP_NUMERIC_KEY_AS_STRING & $flags) {
                $key = (string) $key;
            }

            $output[] = \sprintf('%s: %s', self::dump($key, $keyFlags), self::dump($val, $flags));
        }

        return \sprintf('{ %s }', implode(', ', $output));
    }

    private static function dumpNull(int $flags, bool $rootLevel = false): string
    {
        if (Yaml::DUMP_NULL_AS_TILDE & $flags) {
            return '~';
        }

        if (Yaml::DUMP_NULL_AS_EMPTY & $flags && !$rootLevel) {
            return '';
        }

        return 'null';
    }

    /**
     * Parses a YAML scalar.
     *
     * @throws ParseException When malformed inline YAML string is parsed
     */
    public static function parseScalar(string $scalar, int $flags = 0, ?array $delimiters = null, int &$i = 0, bool $evaluate = true, array &$references = [], ?bool &$isQuoted = null, ?ParserState $state = null): mixed
    {
        if (\in_array($scalar[$i], ['"', "'"], true)) {
            // quoted scalar
            $isQuoted = true;
            $output = self::parseQuotedScalar($scalar, $i);

            if (null !== $delimiters) {
                $tmp = ltrim(substr($scalar, $i), " \n");
                if ('' === $tmp) {
                    throw new ParseException(\sprintf('Unexpected end of line, expected one of "%s".', implode('', $delimiters)), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
                }
                if (!\in_array($tmp[0], $delimiters)) {
                    throw new ParseException(\sprintf('Unexpected characters (%s).', substr($scalar, $i)), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
                }
            }
        } else {
            // "normal" string
            $isQuoted = false;

            if (!$delimiters) {
                $output = substr($scalar, $i);
                $i += \strlen($output);

                // remove comments
                if (Parser::preg_match('/[ \t]+#/', $output, $match, \PREG_OFFSET_CAPTURE)) {
                    $output = substr($output, 0, $match[0][1]);
                }
            } elseif (Parser::preg_match('/^(.*?)('.implode('|', $delimiters).')/', substr($scalar, $i), $match)) {
                $output = $match[1];
                $i += \strlen($output);
                $output = trim($output);
            } else {
                throw new ParseException(\sprintf('Malformed inline YAML string: "%s".', $scalar), self::$parsedLineNumber + 1, null, self::$parsedFilename);
            }

            // a non-quoted string cannot start with @ or ` (reserved) nor with a scalar indicator (| or >)
            if ($output && ('@' === $output[0] || '`' === $output[0] || '|' === $output[0] || '>' === $output[0] || '%' === $output[0])) {
                throw new ParseException(\sprintf('The reserved indicator "%s" cannot start a plain scalar; you need to quote the scalar.', $output[0]), self::$parsedLineNumber + 1, $output, self::$parsedFilename);
            }

            if ($evaluate) {
                $state ??= new ParserState();
                $output = self::evaluateScalar($state, $output, $flags, $references, $isQuoted);
            }
        }

        return $output;
    }

    /**
     * Parses a YAML quoted scalar.
     *
     * @throws ParseException When malformed inline YAML string is parsed
     */
    private static function parseQuotedScalar(string $scalar, int &$i = 0): string
    {
        if (!Parser::preg_match('/'.self::REGEX_QUOTED_STRING.'/Au', substr($scalar, $i), $match)) {
            throw new ParseException(\sprintf('Malformed inline YAML string: "%s".', substr($scalar, $i)), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
        }

        $output = substr($match[0], 1, -1);

        $unescaper = new Unescaper();
        if ('"' == $scalar[$i]) {
            $output = $unescaper->unescapeDoubleQuotedString($output);
        } else {
            $output = $unescaper->unescapeSingleQuotedString($output);
        }

        $i += \strlen($match[0]);

        return $output;
    }

    /**
     * Parses a YAML sequence.
     *
     * @throws ParseException When malformed inline YAML string is parsed
     */
    private static function parseSequence(ParserState $state, string $sequence, int $flags, int &$i = 0, array &$references = []): array
    {
        $state->enterNestingLevel(self::$parsedLineNumber + 1, null, self::$parsedFilename);

        $output = [];
        $len = \strlen($sequence);
        ++$i;

        try {
            // [foo, bar, ...]
            $lastToken = null;
            while ($i < $len) {
                if (']' === $sequence[$i]) {
                    return $output;
                }
                if (',' === $sequence[$i] || ' ' === $sequence[$i]) {
                    if (',' === $sequence[$i] && (null === $lastToken || 'separator' === $lastToken)) {
                        $output[] = null;
                    } elseif (',' === $sequence[$i]) {
                        $lastToken = 'separator';
                    }

                    ++$i;

                    continue;
                }

                $tag = self::parseTag($sequence, $i, $flags);
                $anchorRef = self::parseAnchor($sequence, $i);
                if (null !== $anchorRef && null === $tag) {
                    $tag = self::parseTag($sequence, $i, $flags);
                }

                if (null !== $anchorRef && (!isset($sequence[$i]) || ',' === $sequence[$i] || ']' === $sequence[$i])) {
                    $value = null;
                    $references[$anchorRef] = $value;
                    if (null !== $tag && '' !== $tag) {
                        $value = new TaggedValue($tag, $value);
                    }
                    $output[] = $value;
                    $lastToken = 'value';
                    continue;
                }

                switch ($sequence[$i]) {
                    case '[':
                        // nested sequence
                        $value = self::parseSequence($state, $sequence, $flags, $i, $references);
                        break;
                    case '{':
                        // nested mapping
                        $value = self::parseMapping($state, $sequence, $flags, $i, $references);
                        break;
                    default:
                        $value = self::parseScalar($sequence, $flags, [',', ']'], $i, null === $tag, $references, $isQuoted, $state);

                        // the value can be an array if a reference has been resolved to an array var
                        if (\is_string($value) && !$isQuoted && str_contains($value, ': ')) {
                            // embedded mapping?
                            $j = $i;
                            $mappingValue = $value;
                            $mappingException = null;
                            do {
                                try {
                                    $pos = 0;
                                    $value = self::parseMapping($state, '{'.$mappingValue.'}', $flags, $pos, $references);
                                    $i = $j;
                                    $mappingException = null;
                                    break;
                                } catch (ParseException $exception) {
                                    $mappingException = $exception;
                                    if ($j >= $len) {
                                        break;
                                    }

                                    $mappingValue .= $sequence[$j++];
                                    if ($j >= $len) {
                                        break;
                                    }

                                    $mappingValue .= self::parseScalar($sequence, $flags, [',', ']'], $j, null === $tag, $references);
                                }
                            } while ($j < $len);

                            if ($mappingException) {
                                throw $mappingException;
                            }
                        }

                        --$i;
                }

                if (null !== $anchorRef) {
                    $references[$anchorRef] = $value;
                }

                if (null !== $tag && '' !== $tag) {
                    $value = new TaggedValue($tag, $value);
                }

                $output[] = $value;

                $lastToken = 'value';
                ++$i;
            }

            throw new ParseException(\sprintf('Malformed inline YAML string: "%s".', $sequence), self::$parsedLineNumber + 1, null, self::$parsedFilename);
        } finally {
            $state->leaveNestingLevel();
        }
    }

    /**
     * Parses a YAML mapping.
     *
     * @throws ParseException When malformed inline YAML string is parsed
     */
    private static function parseMapping(ParserState $state, string $mapping, int $flags, int &$i = 0, array &$references = []): array|\stdClass
    {
        $state->enterNestingLevel(self::$parsedLineNumber + 1, null, self::$parsedFilename);

        $output = [];
        $len = \strlen($mapping);
        ++$i;
        $allowOverwrite = false;

        try {
            // {foo: bar, bar:foo, ...}
            while ($i < $len) {
                switch ($mapping[$i]) {
                    case ' ':
                    case ',':
                    case "\n":
                        ++$i;
                        continue 2;
                    case '}':
                        if (self::$objectForMap) {
                            return (object) $output;
                        }

                        return $output;
                }

                // key
                $offsetBeforeKeyParsing = $i;
                $isKeyQuoted = \in_array($mapping[$i], ['"', "'"], true);
                $key = self::parseScalar($mapping, $flags, [':', ' '], $i, false);

                if ($offsetBeforeKeyParsing === $i) {
                    throw new ParseException('Missing mapping key.', self::$parsedLineNumber + 1, $mapping);
                }

                if ('!php/const' === $key || '!php/enum' === $key) {
                    $key .= ' '.self::parseScalar($mapping, $flags, ['(?<!:):(?!:)'], $i, false);
                    $key = self::evaluateScalar($state, $key, $flags);
                }

                if (false === $i = strpos($mapping, ':', $i)) {
                    break;
                }

                if (!$isKeyQuoted) {
                    $evaluatedKey = self::evaluateScalar($state, $key, $flags, $references);

                    if ('' !== $key && $evaluatedKey !== $key && !\is_string($evaluatedKey) && !\is_int($evaluatedKey)) {
                        throw new ParseException('Implicit casting of incompatible mapping keys to strings is not supported. Quote your evaluable mapping keys instead.', self::$parsedLineNumber + 1, $mapping);
                    }
                }

                if (!$isKeyQuoted && (!isset($mapping[$i + 1]) || !\in_array($mapping[$i + 1], [' ', ',', '[', ']', '{', '}', "\n"], true))) {
                    throw new ParseException('Colons must be followed by a space or an indication character (i.e. " ", ",", "[", "]", "{", "}").', self::$parsedLineNumber + 1, $mapping);
                }

                if ('<<' === $key) {
                    $allowOverwrite = true;
                }

                while ($i < $len) {
                    if (':' === $mapping[$i] || ' ' === $mapping[$i] || "\n" === $mapping[$i]) {
                        ++$i;

                        continue;
                    }

                    $tag = self::parseTag($mapping, $i, $flags);
                    $anchorRef = self::parseAnchor($mapping, $i);
                    if (null !== $anchorRef && null === $tag) {
                        $tag = self::parseTag($mapping, $i, $flags);
                    }

                    if (null !== $anchorRef && (!isset($mapping[$i]) || \in_array($mapping[$i], [',', '}', "\n"], true))) {
                        $value = null;
                        $references[$anchorRef] = $value;
                        if ('<<' !== $key) {
                            if ($allowOverwrite || !isset($output[$key])) {
                                $output[$key] = null !== $tag ? new TaggedValue($tag, $value) : $value;
                            } elseif (isset($output[$key])) {
                                throw new ParseException(\sprintf('Duplicate key "%s" detected.', $key), self::$parsedLineNumber + 1, $mapping);
                            }
                        }
                        continue 2;
                    }

                    switch ($mapping[$i]) {
                        case '[':
                            // nested sequence
                            $value = self::parseSequence($state, $mapping, $flags, $i, $references);
                            if (null !== $anchorRef) {
                                $references[$anchorRef] = $value;
                            }
                            // Spec: Keys MUST be unique; first one wins.
                            // Parser cannot abort this mapping earlier, since lines
                            // are processed sequentially.
                            // But overwriting is allowed when a merge node is used in current block.
                            if ('<<' === $key) {
                                foreach ($value as $parsedValue) {
                                    $output += $parsedValue;
                                }
                            } elseif ($allowOverwrite || !isset($output[$key])) {
                                if (null !== $tag) {
                                    $output[$key] = new TaggedValue($tag, $value);
                                } else {
                                    $output[$key] = $value;
                                }
                            } elseif (isset($output[$key])) {
                                throw new ParseException(\sprintf('Duplicate key "%s" detected.', $key), self::$parsedLineNumber + 1, $mapping);
                            }
                            break;
                        case '{':
                            // nested mapping
                            $value = self::parseMapping($state, $mapping, $flags, $i, $references);
                            if (null !== $anchorRef) {
                                $references[$anchorRef] = $value;
                            }
                            // Spec: Keys MUST be unique; first one wins.
                            // Parser cannot abort this mapping earlier, since lines
                            // are processed sequentially.
                            // But overwriting is allowed when a merge node is used in current block.
                            if ('<<' === $key) {
                                $output += $value;
                            } elseif ($allowOverwrite || !isset($output[$key])) {
                                if (null !== $tag) {
                                    $output[$key] = new TaggedValue($tag, $value);
                                } else {
                                    $output[$key] = $value;
                                }
                            } elseif (isset($output[$key])) {
                                throw new ParseException(\sprintf('Duplicate key "%s" detected.', $key), self::$parsedLineNumber + 1, $mapping);
                            }
                            break;
                        default:
                            $value = self::parseScalar($mapping, $flags, [',', '}', "\n"], $i, null === $tag, $references, $isValueQuoted, $state);
                            if (null !== $anchorRef) {
                                $references[$anchorRef] = $value;
                            }
                            // Spec: Keys MUST be unique; first one wins.
                            // Parser cannot abort this mapping earlier, since lines
                            // are processed sequentially.
                            // But overwriting is allowed when a merge node is used in current block.
                            if ('<<' === $key) {
                                $output += $value;
                            } elseif ($allowOverwrite || !isset($output[$key])) {
                                if (null !== $tag) {
                                    $output[$key] = new TaggedValue($tag, $value);
                                } else {
                                    $output[$key] = $value;
                                }
                            } elseif (isset($output[$key])) {
                                throw new ParseException(\sprintf('Duplicate key "%s" detected.', $key), self::$parsedLineNumber + 1, $mapping);
                            }
                            --$i;
                    }
                    ++$i;

                    continue 2;
                }
            }

            throw new ParseException(\sprintf('Malformed inline YAML string: "%s".', $mapping), self::$parsedLineNumber + 1, null, self::$parsedFilename);
        } finally {
            $state->leaveNestingLevel();
        }
    }

    /**
     * Evaluates scalars and replaces magic values.
     *
     * @throws ParseException when object parsing support was disabled and the parser detected a PHP object or when a reference could not be resolved
     */
    private static function evaluateScalar(ParserState $state, string $scalar, int $flags, array &$references = [], ?bool &$isQuotedString = null): mixed
    {
        $isQuotedString = false;
        $scalar = trim($scalar);

        if (str_starts_with($scalar, '*')) {
            if (false !== $pos = strpos($scalar, '#')) {
                $value = substr($scalar, 1, $pos - 2);
            } else {
                $value = substr($scalar, 1);
            }

            // an unquoted *
            if ('' === $value) {
                throw new ParseException('A reference must contain at least one character.', self::$parsedLineNumber + 1, $value, self::$parsedFilename);
            }

            if (!\array_key_exists($value, $references)) {
                throw new ParseException(\sprintf('Reference "%s" does not exist.', $value), self::$parsedLineNumber + 1, $value, self::$parsedFilename);
            }

            $state->countAlias($references[$value], self::$parsedLineNumber + 1, null, self::$parsedFilename);

            return $references[$value];
        }

        $scalarLower = strtolower($scalar);

        switch (true) {
            case 'null' === $scalarLower:
            case '' === $scalar:
            case '~' === $scalar:
                return null;
            case 'true' === $scalarLower:
                return true;
            case 'false' === $scalarLower:
                return false;
            case '!' === $scalar[0]:
                switch (true) {
                    case str_starts_with($scalar, '!!str '):
                        $s = substr($scalar, 6);

                        if (\in_array($s[0] ?? '', ['"', "'"], true)) {
                            $isQuotedString = true;
                            $s = self::parseQuotedScalar($s);
                        }

                        return $s;
                    case str_starts_with($scalar, '! '):
                        return substr($scalar, 2);
                    case str_starts_with($scalar, '!php/object'):
                        if (self::$objectSupport) {
                            if (!isset($scalar[12])) {
                                throw new ParseException('Missing value for tag "!php/object".', self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
                            }

                            return unserialize(self::parseScalar(substr($scalar, 12)), ['allowed_classes' => true]);
                        }

                        if (self::$exceptionOnInvalidType) {
                            throw new ParseException('Object support when parsing a YAML file has been disabled.', self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
                        }

                        return null;
                    case str_starts_with($scalar, '!php/const'):
                        if (self::$constantSupport) {
                            if (!isset($scalar[11])) {
                                throw new ParseException('Missing value for tag "!php/const".', self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
                            }

                            $i = 0;
                            if (\defined($const = self::parseScalar(substr($scalar, 11), 0, null, $i, false))) {
                                return \constant($const);
                            }

                            throw new ParseException(\sprintf('The constant "%s" is not defined.', $const), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
                        }
                        if (self::$exceptionOnInvalidType) {
                            throw new ParseException(\sprintf('The string "%s" could not be parsed as a constant. Did you forget to pass the "Yaml::PARSE_CONSTANT" flag to the parser?', $scalar), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
                        }

                        return null;
                    case str_starts_with($scalar, '!php/enum'):
                        if (self::$constantSupport) {
                            if (!isset($scalar[11])) {
                                throw new ParseException('Missing value for tag "!php/enum".', self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
                            }

                            $i = 0;
                            $enumName = self::parseScalar(substr($scalar, 10), 0, null, $i, false);
                            $useName = str_contains($enumName, '::');
                            $enum = $useName ? strstr($enumName, '::', true) : $enumName;

                            if (!enum_exists($enum)) {
                                throw new ParseException(\sprintf('The enum "%s" is not defined.', $enum), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
                            }
                            if (!$useName) {
                                return $enum::cases();
                            }
                            if ($useValue = str_ends_with($enumName, '->value')) {
                                $enumName = substr($enumName, 0, -7);
                            }

                            if (!\defined($enumName)) {
                                throw new ParseException(\sprintf('The string "%s" is not the name of a valid enum.', $enumName), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
                            }

                            $value = \constant($enumName);

                            if (!$useValue) {
                                return $value;
                            }
                            if (!$value instanceof \BackedEnum) {
                                throw new ParseException(\sprintf('The enum "%s" defines no value next to its name.', $enumName), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
                            }

                            return $value->value;
                        }
                        if (self::$exceptionOnInvalidType) {
                            throw new ParseException(\sprintf('The string "%s" could not be parsed as an enum. Did you forget to pass the "Yaml::PARSE_CONSTANT" flag to the parser?', $scalar), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
                        }

                        return null;
                    case str_starts_with($scalar, '!!float '):
                        return (float) substr($scalar, 8);
                    case str_starts_with($scalar, '!!binary '):
                        return self::evaluateBinaryScalar(substr($scalar, 9));
                }

                throw new ParseException(\sprintf('The string "%s" could not be parsed as it uses an unsupported built-in tag.', $scalar), self::$parsedLineNumber, $scalar, self::$parsedFilename);
            case preg_match('/^(?:\+|-)?0o(?P<value>[0-7_]++)$/', $scalar, $matches):
                $value = str_replace('_', '', $matches['value']);

                if ('-' === $scalar[0]) {
                    return -octdec($value);
                }

                return octdec($value);
            case \in_array($scalar[0], ['+', '-', '.'], true) || is_numeric($scalar[0]):
                if (Parser::preg_match('{^[+-]?[0-9][0-9_]*$}', $scalar)) {
                    $scalar = str_replace('_', '', $scalar);
                }

                switch (true) {
                    case ctype_digit($scalar):
                    case '-' === $scalar[0] && ctype_digit(substr($scalar, 1)):
                        if ($scalar < \PHP_INT_MIN || \PHP_INT_MAX < $scalar) {
                            return $scalar;
                        }

                        $cast = (int) $scalar;

                        return ($scalar === (string) $cast) ? $cast : $scalar;
                    case is_numeric($scalar):
                    case Parser::preg_match(self::getHexRegex(), $scalar):
                        $scalar = str_replace('_', '', $scalar);

                        return '0x' === $scalar[0].$scalar[1] ? hexdec($scalar) : (float) $scalar;
                    case '.inf' === $scalarLower:
                    case '.nan' === $scalarLower:
                        return -log(0);
                    case '-.inf' === $scalarLower:
                        return log(0);
                    case Parser::preg_match('/^(-|\+)?[0-9][0-9_]*(\.[0-9_]+)?$/', $scalar):
                        return (float) str_replace('_', '', $scalar);
                    case Parser::preg_match(self::getTimestampRegex(), $scalar):
                        try {
                            // When no timezone is provided in the parsed date, YAML spec says we must assume UTC.
                            $time = new \DateTimeImmutable($scalar, new \DateTimeZone('UTC'));
                        } catch (\Exception $e) {
                            // Some dates accepted by the regex are not valid dates.
                            throw new ParseException(\sprintf('The date "%s" could not be parsed as it is an invalid date.', $scalar), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename, $e);
                        }

                        if (Yaml::PARSE_DATETIME & $flags) {
                            return $time;
                        }

                        if ('' !== rtrim($time->format('u'), '0')) {
                            return (float) $time->format('U.u');
                        }

                        try {
                            if (false !== $scalar = $time->getTimestamp()) {
                                return $scalar;
                            }
                        } catch (\DateRangeError|\ValueError) {
                            // no-op
                        }

                        return $time->format('U');
                }
        }

        return (string) $scalar;
    }

    private static function parseAnchor(string $value, int &$i): ?string
    {
        if (!isset($value[$i]) || '&' !== $value[$i]) {
            return null;
        }

        if (!Parser::preg_match('/^&(?P<ref>[^ \t,\[\]\{\}\n]++)\s*+/A', substr($value, $i), $matches)) {
            return null;
        }

        $i += \strlen($matches[0]);

        return $matches['ref'];
    }

    private static function parseTag(string $value, int &$i, int $flags): ?string
    {
        if ('!' !== $value[$i]) {
            return null;
        }

        $tagLength = strcspn($value, " \t\n[]{},", $i + 1);
        $tag = substr($value, $i + 1, $tagLength);

        $nextOffset = $i + $tagLength + 1;
        $nextOffset += strspn($value, ' ', $nextOffset);

        if ('' === $tag && (!isset($value[$nextOffset]) || \in_array($value[$nextOffset], [']', '}', ','], true))) {
            throw new ParseException('Using the unquoted scalar value "!" is not supported. You must quote it.', self::$parsedLineNumber + 1, $value, self::$parsedFilename);
        }

        // Is followed by a scalar and is a built-in tag
        if ('' !== $tag && (!isset($value[$nextOffset]) || !\in_array($value[$nextOffset], ['[', '{'], true)) && ('!' === $tag[0] || \in_array($tag, ['str', 'php/const', 'php/enum', 'php/object'], true))) {
            // Manage in {@link self::evaluateScalar()}
            return null;
        }

        $i = $nextOffset;

        // Built-in tags
        if ('' !== $tag && '!' === $tag[0]) {
            throw new ParseException(\sprintf('The built-in tag "!%s" is not implemented.', $tag), self::$parsedLineNumber + 1, $value, self::$parsedFilename);
        }

        if ('' !== $tag && !isset($value[$i])) {
            throw new ParseException(\sprintf('Missing value for tag "%s".', $tag), self::$parsedLineNumber + 1, $value, self::$parsedFilename);
        }

        if ('' === $tag || Yaml::PARSE_CUSTOM_TAGS & $flags) {
            return $tag;
        }

        throw new ParseException(\sprintf('Tags support is not enabled. Enable the "Yaml::PARSE_CUSTOM_TAGS" flag to use "!%s".', $tag), self::$parsedLineNumber + 1, $value, self::$parsedFilename);
    }

    public static function evaluateBinaryScalar(string $scalar): string
    {
        $parsedBinaryData = self::parseScalar(preg_replace('/\s/', '', $scalar));

        if (!\is_scalar($parsedBinaryData ?? '') && !$parsedBinaryData instanceof \Stringable) {
            throw new ParseException(\sprintf('The "!!binary" tag only supports a base64 encoded string, got "%s".', get_debug_type($parsedBinaryData)), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
        }

        $parsedBinaryData = (string) $parsedBinaryData;

        if (0 !== (\strlen($parsedBinaryData) % 4)) {
            throw new ParseException(\sprintf('The normalized base64 encoded data (data without whitespace characters) length must be a multiple of four (%d bytes given).', \strlen($parsedBinaryData)), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
        }

        if (!Parser::preg_match('#^[A-Z0-9+/]+={0,2}$#i', $parsedBinaryData)) {
            throw new ParseException(\sprintf('The base64 encoded data (%s) contains invalid characters.', $parsedBinaryData), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
        }

        return base64_decode($parsedBinaryData, true);
    }

    private static function isBinaryString(string $value): bool
    {
        return !preg_match('//u', $value) || preg_match('/[^\x00\x07-\x0d\x1B\x20-\xff]/', $value);
    }

    /**
     * Gets a regex that matches a YAML date.
     *
     * @see http://www.yaml.org/spec/1.2/spec.html#id2761573
     */
    private static function getTimestampRegex(): string
    {
        return <<<EOF
                    ~^
                    (?P<year>[0-9][0-9][0-9][0-9])
                    -(?P<month>[0-9][0-9]?)
                    -(?P<day>[0-9][0-9]?)
                    (?:(?:[Tt]|[ \t]+)
                    (?P<hour>[0-9][0-9]?)
                    :(?P<minute>[0-9][0-9])
                    :(?P<second>[0-9][0-9])
                    (?:\.(?P<fraction>[0-9]*))?
                    (?:[ \t]*(?P<tz>Z|(?P<tz_sign>[-+])(?P<tz_hour>[0-9][0-9]?)
                    (?::(?P<tz_minute>[0-9][0-9]))?))?)?
                    $~x
            EOF;
    }

    /**
     * Gets a regex that matches a YAML number in hexadecimal notation.
     */
    private static function getHexRegex(): string
    {
        return '~^0x[0-9a-f_]++$~i';
    }
}
