<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Reflection;

/**
 * A fake ReflectionFunction but for language constructs.
 */
class ReflectionLanguageConstruct extends \ReflectionFunctionAbstract
{
    public $keyword;

    /**
     * Language construct parameter definitions.
     */
    private const LANGUAGE_CONSTRUCTS = [
        'isset' => [
            'var'  => [],
            'vars' => [
                'isVariadic' => true,
            ],
        ],

        'unset' => [
            'var'  => [],
            'vars' => [
                'isVariadic' => true,
            ],
        ],

        'empty' => [
            'var' => [],
        ],

        'echo' => [
            'arg1' => [],
            'args' => [
                'isVariadic' => true,
            ],
        ],

        'print' => [
            'arg' => [],
        ],

        'array' => [
            'values' => [
                'isVariadic' => true,
            ],
        ],

        'list' => [
            'var'  => [],
            'vars' => [
                'isVariadic' => true,
            ],
        ],

        'die' => [
            'status' => [
                'isOptional'   => true,
                'defaultValue' => 0,
            ],
        ],

        'exit' => [
            'status' => [
                'isOptional'   => true,
                'defaultValue' => 0,
            ],
        ],
    ];

    /**
     * Construct a ReflectionLanguageConstruct object.
     *
     * @param string $keyword
     */
    public function __construct(string $keyword)
    {
        if (!self::isLanguageConstruct($keyword)) {
            throw new \InvalidArgumentException('Unknown language construct: '.$keyword);
        }

        $this->keyword = $keyword;
    }

    /**
     * This can't (and shouldn't) do anything :).
     *
     * @throws \RuntimeException
     */
    public static function export($name)
    {
        throw new \RuntimeException('Not yet implemented because it\'s unclear what I should do here :)');
    }

    /**
     * Get language construct name.
     */
    public function getName(): string
    {
        return $this->keyword;
    }

    /**
     * None of these return references.
     */
    public function returnsReference(): bool
    {
        return false;
    }

    public function hasReturnType(): bool
    {
        return false;
    }

    public function getReturnType(): ?\ReflectionType
    {
        return null;
    }

    /**
     * Get language construct params.
     *
     * @return array
     */
    public function getParameters(): array
    {
        $params = [];
        foreach (self::LANGUAGE_CONSTRUCTS[$this->keyword] as $parameter => $opts) {
            $params[] = new ReflectionLanguageConstructParameter($this->keyword, $parameter, $opts);
        }

        return $params;
    }

    /**
     * Gets the file name from a language construct.
     *
     * (Hint: it always returns false)
     *
     * @todo remove \ReturnTypeWillChange attribute after dropping support for PHP 7.x (when we can use union types)
     *
     * @return string|false (false)
     */
    #[\ReturnTypeWillChange]
    public function getFileName()
    {
        return false;
    }

    /**
     * To string.
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * Check whether keyword is a (known) language construct.
     *
     * @param string $keyword
     */
    public static function isLanguageConstruct(string $keyword): bool
    {
        return \array_key_exists($keyword, self::LANGUAGE_CONSTRUCTS);
    }

    /**
     * Get known language construct names.
     *
     * @return string[]
     */
    public static function getNames(): array
    {
        return \array_keys(self::LANGUAGE_CONSTRUCTS);
    }
}
