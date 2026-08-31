<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Completion;

/**
 * Completion kind bitmask constants.
 *
 * Defines the syntactic kinds where completion is being requested.
 * Multiple kinds can be combined using bitwise OR to represent
 * positions where multiple types of completions are valid.
 *
 * Example:
 *   $kinds = CompletionKind::CLASS_NAME | CompletionKind::FUNCTION_NAME;
 */
class CompletionKind
{
    // Special
    public const NONE = 0;
    public const UNKNOWN = 1 << 0;

    // Variables
    public const VARIABLE = 1 << 1;  // $foo|

    // Object members (instance)
    public const OBJECT_METHOD = 1 << 2;    // $foo->method()|
    public const OBJECT_PROPERTY = 1 << 3;  // $foo->property|

    // Static members (class)
    public const STATIC_METHOD = 1 << 4;    // Foo::method()|
    public const STATIC_PROPERTY = 1 << 5;  // Foo::$property|
    public const CLASS_CONSTANT = 1 << 6;   // Foo::CONSTANT|

    // Type names
    public const CLASS_NAME = 1 << 7;      // new Foo|, extends Foo|
    public const INTERFACE_NAME = 1 << 8;  // implements Bar|
    public const TRAIT_NAME = 1 << 9;      // use SomeTrait| (inside class)
    public const ATTRIBUTE_NAME = 1 << 10; // #[Deprecated|] (PHP 8+)

    // Global symbols
    public const FUNCTION_NAME = 1 << 11;  // foo|()
    public const CONSTANT = 1 << 12;       // CONST|
    public const NAMESPACE = 1 << 13;      // namespace Foo\|, use Foo\|

    // PsySH-specific
    public const COMMAND = 1 << 14;        // ls|, doc|
    public const COMMAND_OPTION = 1 << 15; // ls --option|, ls -a|

    // PHP keywords
    public const KEYWORD = 1 << 16;  // echo|, isset|

    // Advanced (future)
    public const NAMED_PARAMETER = 1 << 17;  // foo(name: |) (PHP 8+)
    public const ARRAY_KEY = 1 << 18;        // $array['key'|]
    public const COMMAND_ARGUMENT = 1 << 19; // config set verbosity|

    // Common combinations for ambiguous contexts
    public const OBJECT_MEMBER = self::OBJECT_METHOD | self::OBJECT_PROPERTY;                          // $foo->|
    public const STATIC_MEMBER = self::STATIC_METHOD | self::STATIC_PROPERTY | self::CLASS_CONSTANT;  // Foo::|
    public const TYPE_NAME = self::CLASS_NAME | self::INTERFACE_NAME;                                  // Type hints, return types
    public const CLASS_LIKE = self::CLASS_NAME | self::INTERFACE_NAME | self::TRAIT_NAME;              // Any class-like structure
    public const CALLABLE = self::FUNCTION_NAME | self::CLASS_NAME;
    public const SYMBOL = self::FUNCTION_NAME | self::CLASS_LIKE | self::CONSTANT;

    // Contexts where the input might be a shell command rather than PHP code
    public const COMMAND_ELIGIBLE = self::UNKNOWN | self::SYMBOL | self::KEYWORD;
}
