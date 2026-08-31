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

use Psy\Reflection\ReflectionMagicMethod;
use Psy\Reflection\ReflectionMagicProperty;

/**
 * A docblock representation.
 *
 * Based on PHP-DocBlock-Parser by Paul Scott:
 *
 * {@link http://www.github.com/icio/PHP-DocBlock-Parser}
 *
 * @author Paul Scott <paul@duedil.com>
 * @author Justin Hileman <justin@justinhileman.info>
 */
class Docblock
{
    /**
     * Tags in the docblock that have a whitespace-delimited number of parameters
     * (such as `@param type var desc` and `@return type desc`) and the names of
     * those parameters.
     *
     * @var array
     */
    public static $vectors = [
        'throws' => ['type', 'desc'],
        'param'  => ['type', 'var', 'desc'],
        'return' => ['type', 'desc'],
    ];

    /** @var array<string, ReflectionMagicMethod[]> Cache for getMagicMethods() */
    private static array $methodCache = [];

    /** @var array<string, ReflectionMagicProperty[]> Cache for getMagicProperties() */
    private static array $propertyCache = [];

    protected $reflector;

    /**
     * The description of the symbol.
     *
     * @var string
     */
    public $desc;

    /**
     * The tags defined in the docblock.
     *
     * The array has keys which are the tag names (excluding the @) and values
     * that are arrays, each of which is an entry for the tag.
     *
     * In the case where the tag name is defined in {@see DocBlock::$vectors} the
     * value within the tag-value array is an array in itself with keys as
     * described by {@see DocBlock::$vectors}.
     *
     * @var array
     */
    public $tags;

    /**
     * The entire DocBlock comment that was parsed.
     *
     * @var string
     */
    public $comment;

    /**
     * Docblock constructor.
     *
     * @param \Reflector $reflector
     */
    public function __construct(\Reflector $reflector)
    {
        $this->reflector = $reflector;

        if ($reflector instanceof \ReflectionClass || $reflector instanceof \ReflectionClassConstant || $reflector instanceof \ReflectionFunctionAbstract || $reflector instanceof \ReflectionProperty) {
            $this->setComment($reflector->getDocComment());
        }
    }

    /**
     * Set and parse the docblock comment.
     *
     * @param string $comment The docblock
     */
    protected function setComment(string $comment)
    {
        $this->desc = '';
        $this->tags = [];
        $this->comment = $comment;

        $this->parseComment($comment);
    }

    /**
     * Find the length of the docblock prefix.
     *
     * @param array $lines
     *
     * @return int Prefix length
     */
    protected static function prefixLength(array $lines): int
    {
        // find only lines with interesting things
        $lines = \array_filter($lines, fn ($line) => \substr($line, \strspn($line, "* \t\n\r\0\x0B")));

        // if we sort the lines, we only have to compare two items
        \sort($lines);

        $first = \reset($lines);
        $last = \end($lines);

        // Special case for single-line comments
        if (\count($lines) === 1) {
            return \strspn($first, "* \t\n\r\0\x0B");
        }

        // find the longest common substring, but stop before @ (tag marker)
        $count = \min(\strlen($first), \strlen($last));
        for ($i = 0; $i < $count; $i++) {
            if ($first[$i] !== $last[$i] || $first[$i] === '@') {
                return $i;
            }
        }

        return $count;
    }

    /**
     * Parse the comment into the component parts and set the state of the object.
     *
     * @param string $comment The docblock
     */
    protected function parseComment(string $comment)
    {
        // Strip the opening and closing tags of the docblock
        $comment = \substr($comment, 3, -2);

        // Split into arrays of lines
        $comment = \array_filter(\preg_split('/\r?\n\r?/', $comment));

        // Trim asterisks and whitespace from the beginning and whitespace from the end of lines
        $prefixLength = self::prefixLength($comment);
        $comment = \array_map(fn ($line) => \rtrim(\substr($line, $prefixLength)), $comment);

        // Group the lines together by @tags
        $blocks = [];
        $b = -1;
        foreach ($comment as $line) {
            if (self::isTagged($line)) {
                $b++;
                $blocks[] = [];
            } elseif ($b === -1) {
                $b = 0;
                $blocks[] = [];
            }
            $blocks[$b][] = $line;
        }

        // Parse the blocks
        foreach ($blocks as $block => $body) {
            $body = \trim(\implode("\n", $body));

            if ($block === 0 && !self::isTagged($body)) {
                // This is the description block
                $this->desc = $body;
            } else {
                // This block is tagged
                $tag = \substr(self::strTag($body), 1);
                $body = \ltrim(\substr($body, \strlen($tag) + 2));

                if (isset(self::$vectors[$tag])) {
                    // The tagged block is a vector
                    $count = \count(self::$vectors[$tag]);
                    if ($body) {
                        $parts = self::splitOnWhitespace($body, $count);
                    } else {
                        $parts = [];
                    }

                    // Default the trailing values
                    $parts = \array_pad($parts, $count, null);

                    // Store as a mapped array
                    $this->tags[$tag][] = \array_combine(self::$vectors[$tag], $parts);
                } else {
                    // The tagged block is only text
                    $this->tags[$tag][] = $body;
                }
            }
        }
    }

    /**
     * Whether or not a docblock contains a given @tag.
     *
     * @param string $tag The name of the @tag to check for
     */
    public function hasTag(string $tag): bool
    {
        return \is_array($this->tags) && \array_key_exists($tag, $this->tags);
    }

    /**
     * The value of a tag.
     *
     * @param string $tag
     *
     * @return array|null
     */
    public function tag(string $tag): ?array
    {
        return $this->hasTag($tag) ? $this->tags[$tag] : null;
    }

    /**
     * Whether or not a string begins with a @tag.
     *
     * @param string $str
     */
    public static function isTagged(string $str): bool
    {
        return isset($str[1]) && $str[0] === '@' && !\preg_match('/[^A-Za-z]/', $str[1]);
    }

    /**
     * The tag at the beginning of a string.
     *
     * @param string $str
     *
     * @return string|null
     */
    public static function strTag(string $str)
    {
        if (\preg_match('/^@[a-z0-9_]+/', $str, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * Split a string on whitespace, respecting balanced brackets.
     *
     * Like preg_split('/\s+/', $str, $limit) but treats `<...>` and `(...)` as atomic,
     * so `array<int, string> $foo` splits into ['array<int, string>', '$foo']
     * and `find(int $id)` stays as one token instead of being split.
     *
     * Uses a stack to ensure proper nesting (e.g. `<(>)` won't break).
     *
     * @param string $str   The string to split
     * @param int    $limit Maximum number of parts (0 = unlimited)
     *
     * @return string[]
     */
    private static function splitOnWhitespace(string $str, int $limit = 0): array
    {
        static $brackets = ['<' => '>', '(' => ')'];

        $parts = [];
        $current = '';
        $stack = [];
        $len = \strlen($str);
        $i = 0;

        while ($i < $len) {
            $char = $str[$i];

            if (isset($brackets[$char])) {
                // Opening bracket; push onto stack
                $stack[] = $char;
                $current .= $char;
            } elseif (!empty($stack) && $brackets[\end($stack)] === $char) {
                // Closing bracket matching the current opener; pop stack
                \array_pop($stack);
                $current .= $char;
            } elseif (empty($stack) && \ctype_space($char)) {
                // At top level, whitespace is a delimiter
                if ($current !== '') {
                    $parts[] = $current;
                    $current = '';

                    // If we've hit the limit, grab the rest as the final part
                    if ($limit > 0 && \count($parts) === $limit - 1) {
                        $rest = \ltrim(\substr($str, $i));
                        if ($rest !== '') {
                            $parts[] = $rest;
                        }

                        return $parts;
                    }
                }
                // Skip consecutive whitespace
                while ($i + 1 < $len && \ctype_space($str[$i + 1])) {
                    $i++;
                }
            } else {
                $current .= $char;
            }

            $i++;
        }

        if ($current !== '') {
            $parts[] = $current;
        }

        return $parts;
    }

    /**
     * Get magic methods declared in this docblock via @method tags.
     *
     * Only works when the docblock was created from a ReflectionClass.
     *
     * @return ReflectionMagicMethod[]
     */
    public function getMethods(): array
    {
        if (empty($this->comment) || !$this->reflector instanceof \ReflectionClass) {
            return [];
        }

        return self::parseMethodTags($this->comment, $this->reflector);
    }

    /**
     * Get magic properties declared in this docblock via @property tags.
     *
     * Includes property, property-read, and property-write tags.
     * Only works when the docblock was created from a ReflectionClass.
     *
     * @return ReflectionMagicProperty[]
     */
    public function getProperties(): array
    {
        if (empty($this->comment) || !$this->reflector instanceof \ReflectionClass) {
            return [];
        }

        return self::parsePropertyTags($this->comment, $this->reflector);
    }

    /**
     * Get all magic methods from a class and its parents, interfaces, and traits.
     *
     * Results are cached for performance.
     *
     * @return ReflectionMagicMethod[]
     */
    public static function getMagicMethods(\ReflectionClass $class): array
    {
        $cacheKey = $class->getName();

        if (isset(self::$methodCache[$cacheKey])) {
            return self::$methodCache[$cacheKey];
        }

        $methods = [];

        // Walk parent classes first (so child methods take precedence)
        $parent = $class->getParentClass();
        if ($parent !== false) {
            foreach (self::getMagicMethods($parent) as $method) {
                $methods[$method->getName()] = $method;
            }
        }

        // Walk interfaces
        foreach ($class->getInterfaces() as $interface) {
            foreach (self::getMagicMethods($interface) as $method) {
                if (!isset($methods[$method->getName()])) {
                    $methods[$method->getName()] = $method;
                }
            }
        }

        // Walk traits
        foreach ($class->getTraits() as $trait) {
            foreach (self::getMagicMethods($trait) as $method) {
                if (!isset($methods[$method->getName()])) {
                    $methods[$method->getName()] = $method;
                }
            }
        }

        // Parse this class's docblock (takes precedence over inherited)
        $docComment = $class->getDocComment();
        if ($docComment !== false) {
            foreach (self::parseMethodTags($docComment, $class) as $method) {
                $methods[$method->getName()] = $method;
            }
        }

        self::$methodCache[$cacheKey] = \array_values($methods);

        return self::$methodCache[$cacheKey];
    }

    /**
     * Get all magic properties from a class and its parents, interfaces, and traits.
     *
     * Results are cached for performance.
     *
     * @return ReflectionMagicProperty[]
     */
    public static function getMagicProperties(\ReflectionClass $class): array
    {
        $cacheKey = $class->getName();

        if (isset(self::$propertyCache[$cacheKey])) {
            return self::$propertyCache[$cacheKey];
        }

        $properties = [];

        // Walk parent classes first (so child properties take precedence)
        $parent = $class->getParentClass();
        if ($parent !== false) {
            foreach (self::getMagicProperties($parent) as $property) {
                $properties[$property->getName()] = $property;
            }
        }

        // Walk interfaces
        foreach ($class->getInterfaces() as $interface) {
            foreach (self::getMagicProperties($interface) as $property) {
                if (!isset($properties[$property->getName()])) {
                    $properties[$property->getName()] = $property;
                }
            }
        }

        // Walk traits
        foreach ($class->getTraits() as $trait) {
            foreach (self::getMagicProperties($trait) as $property) {
                if (!isset($properties[$property->getName()])) {
                    $properties[$property->getName()] = $property;
                }
            }
        }

        // Parse this class's docblock (takes precedence over inherited)
        $docComment = $class->getDocComment();
        if ($docComment !== false) {
            foreach (self::parsePropertyTags($docComment, $class) as $property) {
                $properties[$property->getName()] = $property;
            }
        }

        self::$propertyCache[$cacheKey] = \array_values($properties);

        return self::$propertyCache[$cacheKey];
    }

    /**
     * Clear the magic method and property caches.
     *
     * Useful for testing or when classes are redefined.
     */
    public static function clearMagicCache(): void
    {
        self::$methodCache = [];
        self::$propertyCache = [];
    }

    /**
     * Parse a single @method tag body.
     *
     * Handles: [static] [returnType] [&]methodName([params]) [description]
     *
     * @return array|null Parsed method data or null if invalid
     */
    private static function parseMethodTag(string $body): ?array
    {
        $body = \trim($body);
        if ($body === '') {
            return null;
        }

        $isStatic = false;
        $returnType = null;
        $returnsReference = false;
        $name = null;
        $parameters = '';
        $description = null;

        // Check for 'static' keyword at the start
        if (\strpos($body, 'static ') === 0) {
            $isStatic = true;
            $body = \ltrim(\substr($body, 7));
        }

        // Split into tokens respecting generics
        $tokens = self::splitOnWhitespace($body);
        if (empty($tokens)) {
            return null;
        }

        // Find the method name (contains parentheses or is the last non-description token)
        $methodIndex = null;
        foreach ($tokens as $i => $token) {
            if (\preg_match('/^&?[\w_]+\s*\(/', $token)) {
                $methodIndex = $i;
                break;
            }
        }

        // If no parentheses found, check for bare method name pattern
        if ($methodIndex === null) {
            foreach ($tokens as $i => $token) {
                if (\preg_match('/^&?[\w_]+$/', $token) && $i > 0) {
                    // Could be a method name if previous tokens look like types
                    $methodIndex = $i;
                    break;
                }
            }
        }

        // If still no method found, first token might be the method name
        if ($methodIndex === null) {
            if (\preg_match('/^&?[\w_]+/', $tokens[0])) {
                $methodIndex = 0;
            } else {
                return null;
            }
        }

        // Everything before methodIndex is the return type
        if ($methodIndex > 0) {
            $returnType = \implode(' ', \array_slice($tokens, 0, $methodIndex));
        }

        // Parse the method token
        $methodToken = $tokens[$methodIndex];

        // Check for returns-by-reference
        if (\strpos($methodToken, '&') === 0) {
            $returnsReference = true;
            $methodToken = \substr($methodToken, 1);
        }

        // Extract method name and parameters
        if (\preg_match('/^([\w_]+)\s*(?:\(([^\)]*)\))?/', $methodToken, $matches)) {
            $name = $matches[1];
            $parameters = $matches[2] ?? '';
        } else {
            return null;
        }

        // Everything after the method token is the description
        if ($methodIndex < \count($tokens) - 1) {
            $descParts = \array_slice($tokens, $methodIndex + 1);
            $description = \implode(' ', $descParts);
        }

        return [
            'name'             => $name,
            'static'           => $isStatic,
            'returnType'       => $returnType,
            'returnsReference' => $returnsReference,
            'parameters'       => $parameters,
            'description'      => $description !== '' ? $description : null,
        ];
    }

    /**
     * Parse a single @property tag body.
     *
     * Handles: [type] $name [description]
     *
     * @param string $tagName One of 'property', 'property-read', 'property-write'
     *
     * @return array|null Parsed property data or null if invalid
     */
    private static function parsePropertyTag(string $body, string $tagName): ?array
    {
        $body = \trim($body);
        if ($body === '') {
            return null;
        }

        $type = null;
        $name = null;
        $description = null;

        // Split into tokens respecting generics
        $tokens = self::splitOnWhitespace($body);
        if (empty($tokens)) {
            return null;
        }

        // Find the property name (starts with $ or is a bare word after a type)
        $nameIndex = null;
        foreach ($tokens as $i => $token) {
            if (\strpos($token, '$') === 0) {
                $nameIndex = $i;
                break;
            }
        }

        // If no $ found, check if second token looks like a name (first would be type)
        if ($nameIndex === null && \count($tokens) >= 2) {
            if (\preg_match('/^[\w_]+$/', $tokens[1])) {
                $nameIndex = 1;
            }
        }

        // If still no name found, first token is the name (no type)
        if ($nameIndex === null) {
            $nameIndex = 0;
        }

        // Everything before nameIndex is the type
        if ($nameIndex > 0) {
            $type = \implode(' ', \array_slice($tokens, 0, $nameIndex));
        }

        // Extract property name (strip $ if present)
        $name = \ltrim($tokens[$nameIndex], '$');

        // Everything after the name is the description
        if ($nameIndex < \count($tokens) - 1) {
            $descParts = \array_slice($tokens, $nameIndex + 1);
            $description = \implode(' ', $descParts);
        }

        return [
            'name'        => $name,
            'type'        => $type,
            'readOnly'    => $tagName === 'property-read',
            'writeOnly'   => $tagName === 'property-write',
            'description' => $description !== '' ? $description : null,
        ];
    }

    /**
     * Parse all @method tags from a raw docblock comment.
     *
     * @return ReflectionMagicMethod[]
     */
    private static function parseMethodTags(string $docComment, \ReflectionClass $class): array
    {
        $methods = [];

        if (\strpos($docComment, '@method') === false) {
            return $methods;
        }

        // Match @method tags (handles multi-line by stopping at next @ or end)
        if (\preg_match_all('/@method\s+(.+?)(?=\n\s*\*\s*@|\n\s*\*\/|\z)/s', $docComment, $matches)) {
            foreach ($matches[1] as $body) {
                // Clean up the body (remove leading asterisks and normalize whitespace)
                $body = \preg_replace('/\n\s*\*\s*/', ' ', $body);
                $body = \trim($body);

                $parsed = self::parseMethodTag($body);
                if ($parsed !== null) {
                    $methods[] = new ReflectionMagicMethod(
                        $class,
                        $parsed['name'],
                        $parsed['static'],
                        $parsed['returnType'],
                        $parsed['parameters'],
                        $parsed['description'],
                        $parsed['returnsReference']
                    );
                }
            }
        }

        return $methods;
    }

    /**
     * Parse all @property tags from a raw docblock comment.
     *
     * @return ReflectionMagicProperty[]
     */
    private static function parsePropertyTags(string $docComment, \ReflectionClass $class): array
    {
        $properties = [];

        // Match @property, @property-read, @property-write tags
        $pattern = '/@(property(?:-read|-write)?)\s+(.+?)(?=\n\s*\*\s*@|\n\s*\*\/|\z)/s';

        if (\preg_match_all($pattern, $docComment, $matches, \PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $tagName = $match[1];
                $body = \preg_replace('/\n\s*\*\s*/', ' ', $match[2]);
                $body = \trim($body);

                $parsed = self::parsePropertyTag($body, $tagName);
                if ($parsed !== null) {
                    $properties[] = new ReflectionMagicProperty(
                        $class,
                        $parsed['name'],
                        $parsed['type'],
                        $parsed['readOnly'],
                        $parsed['writeOnly'],
                        $parsed['description']
                    );
                }
            }
        }

        return $properties;
    }
}
