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

use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Yaml offers convenience methods to load and dump YAML.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class Yaml
{
    public const DUMP_OBJECT = 1;
    public const PARSE_EXCEPTION_ON_INVALID_TYPE = 2;
    public const PARSE_OBJECT = 4;
    public const PARSE_OBJECT_FOR_MAP = 8;
    public const DUMP_EXCEPTION_ON_INVALID_TYPE = 16;
    public const PARSE_DATETIME = 32;
    public const DUMP_OBJECT_AS_MAP = 64;
    public const DUMP_MULTI_LINE_LITERAL_BLOCK = 128;
    public const PARSE_CONSTANT = 256;
    public const PARSE_CUSTOM_TAGS = 512;
    public const DUMP_EMPTY_ARRAY_AS_SEQUENCE = 1024;
    public const DUMP_NULL_AS_TILDE = 2048;
    public const DUMP_NUMERIC_KEY_AS_STRING = 4096;
    public const DUMP_NULL_AS_EMPTY = 8192;
    public const DUMP_COMPACT_NESTED_MAPPING = 16384;
    public const DUMP_FORCE_DOUBLE_QUOTES_ON_VALUES = 32768;
    public const PARSE_EXCEPTION_ON_ALIAS = 65536;

    /**
     * Parses a YAML file into a PHP value.
     *
     * Usage:
     *
     *     $array = Yaml::parseFile('config.yml');
     *     print_r($array);
     *
     * @param string                     $filename                 The path to the YAML file to be parsed
     * @param int-mask-of<self::PARSE_*> $flags                    A bit field of PARSE_* constants to customize the YAML parser behavior
     * @param int                        $maxNestingLevel          The maximum nesting depth for nested YAML blocks
     * @param int                        $maxAliasesForCollections The maximum number of collection aliases to resolve
     *
     * @throws ParseException If the file could not be read or the YAML is not valid
     */
    public static function parseFile(string $filename, int $flags = 0, int $maxNestingLevel = Parser::DEFAULT_MAX_NESTING_LEVEL, int $maxAliasesForCollections = Parser::DEFAULT_MAX_ALIASES_FOR_COLLECTIONS): mixed
    {
        $yaml = new Parser($maxNestingLevel, $maxAliasesForCollections);

        return $yaml->parseFile($filename, $flags);
    }

    /**
     * Parses YAML into a PHP value.
     *
     *  Usage:
     *  <code>
     *   $array = Yaml::parse(file_get_contents('config.yml'));
     *   print_r($array);
     *  </code>
     *
     * @param string                     $input                    A string containing YAML
     * @param int-mask-of<self::PARSE_*> $flags                    A bit field of PARSE_* constants to customize the YAML parser behavior
     * @param int                        $maxNestingLevel          The maximum nesting depth for nested YAML blocks
     * @param int                        $maxAliasesForCollections The maximum number of collection aliases to resolve
     *
     * @throws ParseException If the YAML is not valid
     */
    public static function parse(string $input, int $flags = 0, int $maxNestingLevel = Parser::DEFAULT_MAX_NESTING_LEVEL, int $maxAliasesForCollections = Parser::DEFAULT_MAX_ALIASES_FOR_COLLECTIONS): mixed
    {
        $yaml = new Parser($maxNestingLevel, $maxAliasesForCollections);

        return $yaml->parse($input, $flags);
    }

    /**
     * Dumps a PHP value to a YAML string.
     *
     * The dump method, when supplied with an array, will do its best
     * to convert the array into friendly YAML.
     *
     * @param mixed                     $input  The PHP value
     * @param int                       $inline The level where you switch to inline YAML
     * @param int                       $indent The amount of spaces to use for indentation of nested nodes
     * @param int-mask-of<self::DUMP_*> $flags  A bit field of DUMP_* constants to customize the dumped YAML string
     */
    public static function dump(mixed $input, int $inline = 2, int $indent = 4, int $flags = 0): string
    {
        $yaml = new Dumper($indent);

        return $yaml->dump($input, $inline, 0, $flags);
    }
}
