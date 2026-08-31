<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Formatter;

use Psy\Manual\ManualInterface;
use Psy\Reflection\ReflectionConstant;
use Psy\Reflection\ReflectionLanguageConstruct;
use Psy\Reflection\ReflectionMagicMethod;
use Psy\Reflection\ReflectionMagicProperty;
use Psy\Util\Json;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * An abstract representation of a function, class or property signature.
 */
class SignatureFormatter implements ReflectorFormatter
{
    private static ?ManualInterface $manual = null;

    /**
     * Set the manual interface for generating hyperlinks.
     */
    public static function setManual(?ManualInterface $manual): void
    {
        self::$manual = $manual;
    }

    /**
     * Set styles for formatting hyperlinks.
     *
     * @deprecated Use LinkFormatter::setStyles() instead
     *
     * @param array $styles Map of style name to inline style string
     */
    public static function setStyles(array $styles): void
    {
        // Delegate to LinkFormatter which now handles this
        LinkFormatter::setStyles($styles);
    }

    /**
     * Set the manual database for generating hyperlinks.
     *
     * @deprecated Manual database is now set via Configuration::setManual()
     *
     * @param \PDO|null $db
     */
    public static function setManualDb(?\PDO $db): void
    {
        // The functionality is now handled through setManual()
    }

    /**
     * Format a signature for the given reflector.
     *
     * Defers to subclasses to do the actual formatting.
     * Automatically generates hyperlinks if manual database is set.
     *
     * @param \Reflector $reflector
     *
     * @return string Formatted signature
     */
    public static function format(\Reflector $reflector): string
    {
        switch (true) {
            case $reflector instanceof \ReflectionFunction:
            case $reflector instanceof ReflectionLanguageConstruct:
                return self::formatFunction($reflector);

            case $reflector instanceof \ReflectionClass:
                // this case also covers \ReflectionObject
                return self::formatClass($reflector);

            case $reflector instanceof \ReflectionClassConstant:
                return self::formatClassConstant($reflector);

            case $reflector instanceof ReflectionMagicMethod:
                return self::formatMagicMethod($reflector);

            case $reflector instanceof \ReflectionMethod:
                return self::formatMethod($reflector);

            case $reflector instanceof ReflectionMagicProperty:
                return self::formatMagicProperty($reflector);

            case $reflector instanceof \ReflectionProperty:
                return self::formatProperty($reflector);

            case $reflector instanceof ReflectionConstant:
                return self::formatConstant($reflector);

            default:
                throw new \InvalidArgumentException('Unexpected Reflector class: '.\get_class($reflector));
        }
    }

    /**
     * Print the signature name.
     *
     * @param \ReflectionClass|\ReflectionClassConstant|\ReflectionFunctionAbstract $reflector
     *
     * @return string Formatted name
     */
    public static function formatName(\Reflector $reflector): string
    {
        return self::normalizeName($reflector->getName());
    }

    /**
     * Print the method, property or class modifiers.
     *
     * @param \ReflectionMethod|\ReflectionProperty|\ReflectionClass $reflector
     *
     * @return string Formatted modifiers
     */
    private static function formatModifiers(\Reflector $reflector): string
    {
        $modifiers = \array_map(
            fn ($modifier) => \sprintf('<keyword>%s</keyword>', $modifier),
            \Reflection::getModifierNames($reflector->getModifiers())
        );

        return \implode(' ', $modifiers);
    }

    /**
     * Format a class signature.
     *
     * @param \ReflectionClass $reflector
     *
     * @return string Formatted signature
     */
    private static function formatClass(\ReflectionClass $reflector): string
    {
        $chunks = [];

        if ($modifiers = self::formatModifiers($reflector)) {
            $chunks[] = $modifiers;
        }

        if ($reflector->isTrait()) {
            $chunks[] = 'trait';
        } else {
            $chunks[] = $reflector->isInterface() ? 'interface' : 'class';
        }

        $chunks[] = LinkFormatter::styleWithHref('class', self::formatName($reflector), self::getManualHref($reflector));

        if ($parent = $reflector->getParentClass()) {
            $chunks[] = 'extends';
            $parentHref = self::getManualHref($parent);
            $chunks[] = LinkFormatter::styleWithHref('class', self::normalizeName($parent->getName()), $parentHref);
        }

        $interfaces = $reflector->getInterfaceNames();
        if (!empty($interfaces)) {
            \sort($interfaces);

            $chunks[] = $reflector->isInterface() ? 'extends' : 'implements';
            $chunks[] = \implode(', ', \array_map(function ($name) {
                try {
                    $interfaceHref = self::getManualHref(new \ReflectionClass($name));
                } catch (\ReflectionException $e) {
                    $interfaceHref = null;
                }

                return LinkFormatter::styleWithHref('class', self::normalizeName($name), $interfaceHref);
            }, $interfaces));
        }

        return \implode(' ', $chunks);
    }

    /**
     * Format a constant signature.
     *
     * @param \ReflectionClassConstant $reflector
     *
     * @return string Formatted signature
     */
    private static function formatClassConstant($reflector): string
    {
        $value = $reflector->getValue();
        $style = self::getTypeStyle($value);

        return \sprintf(
            '<keyword>const</keyword> %s = <%s>%s</%s>',
            LinkFormatter::styleWithHref('const', self::formatName($reflector), self::getManualHref($reflector)),
            $style,
            OutputFormatter::escape(Json::encode($value)),
            $style
        );
    }

    /**
     * Format a constant signature.
     *
     * @param ReflectionConstant $reflector
     *
     * @return string Formatted signature
     */
    private static function formatConstant(ReflectionConstant $reflector): string
    {
        $value = $reflector->getValue();
        $style = self::getTypeStyle($value);

        return \sprintf(
            '<keyword>define</keyword>(<string>%s</string>, <%s>%s</%s>)',
            OutputFormatter::escape(Json::encode($reflector->getName())),
            $style,
            OutputFormatter::escape(Json::encode($value)),
            $style
        );
    }

    /**
     * Helper for getting output style for a given value's type.
     *
     * @param mixed $value
     */
    private static function getTypeStyle($value): string
    {
        if (\is_int($value) || \is_float($value)) {
            return 'number';
        } elseif (\is_string($value)) {
            return 'string';
        } elseif (\is_bool($value) || $value === null) {
            return 'bool';
        } else {
            return 'strong'; // @codeCoverageIgnore
        }
    }

    /**
     * Format a property signature.
     *
     * @param \ReflectionProperty $reflector
     *
     * @return string Formatted signature
     */
    private static function formatProperty(\ReflectionProperty $reflector): string
    {
        return \sprintf(
            '%s <strong>$%s</strong>',
            self::formatModifiers($reflector),
            $reflector->getName()
        );
    }

    /**
     * Format a function signature.
     *
     * @param \ReflectionFunction $reflector
     *
     * @return string Formatted signature
     */
    private static function formatFunction(\ReflectionFunctionAbstract $reflector): string
    {
        return \sprintf(
            '<keyword>function</keyword> %s%s(%s)%s',
            $reflector->returnsReference() ? '&' : '',
            LinkFormatter::styleWithHref('function', self::formatName($reflector), self::getManualHref($reflector)),
            \implode(', ', self::formatFunctionParams($reflector)),
            self::formatFunctionReturnType($reflector)
        );
    }

    /**
     * Format a function signature's return type (if available).
     *
     * @param \ReflectionFunctionAbstract $reflector
     *
     * @return string Formatted return type
     */
    private static function formatFunctionReturnType(\ReflectionFunctionAbstract $reflector): string
    {
        if (!\method_exists($reflector, 'hasReturnType') || !$reflector->hasReturnType()) {
            return '';
        }

        return \sprintf(': %s', self::formatReflectionType($reflector->getReturnType(), true));
    }

    /**
     * Format a method signature.
     *
     * @param \ReflectionMethod $reflector
     *
     * @return string Formatted signature
     */
    private static function formatMethod(\ReflectionMethod $reflector): string
    {
        return \sprintf(
            '%s %s',
            self::formatModifiers($reflector),
            self::formatFunction($reflector)
        );
    }

    /**
     * Format a magic method signature.
     *
     * @param ReflectionMagicMethod $reflector
     *
     * @return string Formatted signature
     */
    private static function formatMagicMethod(ReflectionMagicMethod $reflector): string
    {
        $parts = [];

        $parts[] = self::formatModifiers($reflector);
        $parts[] = '<keyword>function</keyword>';

        $ref = $reflector->returnsReference() ? '&' : '';

        $signature = \sprintf(
            '%s<function>%s</function>(%s)',
            $ref,
            $reflector->getName(),
            OutputFormatter::escape($reflector->getParameterString())
        );

        $returnType = $reflector->getDocblockReturnType();
        if ($returnType !== null) {
            $signature .= \sprintf(': <keyword>%s</keyword>', OutputFormatter::escape($returnType));
        }

        $parts[] = $signature;

        return \implode(' ', \array_filter($parts));
    }

    /**
     * Format a magic property signature.
     *
     * @param ReflectionMagicProperty $reflector
     *
     * @return string Formatted signature
     */
    private static function formatMagicProperty(ReflectionMagicProperty $reflector): string
    {
        $parts = [];

        $parts[] = self::formatModifiers($reflector);

        $type = $reflector->getDocblockType();
        if ($type !== null) {
            $parts[] = \sprintf('<keyword>%s</keyword>', OutputFormatter::escape($type));
        }

        $parts[] = \sprintf('<strong>$%s</strong>', $reflector->getName());

        if ($reflector->isReadOnly()) {
            $parts[] = '<aside>(read-only)</aside>';
        } elseif ($reflector->isWriteOnly()) {
            $parts[] = '<aside>(write-only)</aside>';
        }

        return \implode(' ', \array_filter($parts));
    }

    /**
     * Print the function params.
     *
     * @param \ReflectionFunctionAbstract $reflector
     *
     * @return array
     */
    private static function formatFunctionParams(\ReflectionFunctionAbstract $reflector): array
    {
        $params = [];
        foreach ($reflector->getParameters() as $param) {
            $hint = '';
            try {
                if (\method_exists($param, 'getType')) {
                    // Only include the inquisitive nullable type iff param default value is not null.
                    $defaultIsNull = $param->isOptional() && $param->isDefaultValueAvailable() && @$param->getDefaultValue() === null;
                    $hint = self::formatReflectionType($param->getType(), !$defaultIsNull);
                } else {
                    if ($param->isArray()) {
                        $hint = '<keyword>array</keyword>';
                    } elseif ($class = $param->getClass()) {
                        $hint = LinkFormatter::styleWithHref('class', self::normalizeName($class->getName()), self::getManualHref($class));
                    }
                }
            } catch (\Throwable $e) {
                // sometimes we just don't know...
                // bad class names, or autoloaded classes that haven't been loaded yet, or whathaveyou.
                // come to think of it, the only time I've seen this is with the intl extension.

                // Hax: we'll try to extract it :P

                // @codeCoverageIgnoreStart
                $chunks = \explode('$'.$param->getName(), (string) $param);
                $chunks = \explode(' ', \trim($chunks[0]));
                $guess = \end($chunks);

                $hint = \sprintf('<urgent>%s</urgent>', OutputFormatter::escape($guess));
                // @codeCoverageIgnoreEnd
            }

            $isVariadic = $param->isVariadic();
            if ($param->isOptional() && !$isVariadic) {
                if (!$param->isDefaultValueAvailable()) {
                    $value = 'unknown';
                    $typeStyle = 'urgent';
                } else {
                    $value = @$param->getDefaultValue();
                    $typeStyle = self::getTypeStyle($value);
                    $value = \is_array($value) ? '[]' : ($value === null ? 'null' : \var_export($value, true));
                }
                $default = \sprintf(' = <%s>%s</%s>', $typeStyle, OutputFormatter::escape($value), $typeStyle);
            } else {
                $default = '';
            }

            $params[] = \sprintf(
                '%s%s%s%s<strong>$%s</strong>%s',
                $param->isPassedByReference() ? '&' : '',
                $isVariadic ? '...' : '',
                $hint,
                $hint !== '' ? ' ' : '',
                $param->getName(),
                $default
            );
        }

        return $params;
    }

    /**
     * Print function param or return type(s).
     *
     * @param \ReflectionType|null $type
     */
    private static function formatReflectionType(?\ReflectionType $type, bool $indicateNullable): string
    {
        if ($type === null) {
            return '';
        }

        if ($type instanceof \ReflectionUnionType) {
            $delimeter = '|';
        } elseif ($type instanceof \ReflectionIntersectionType) {
            $delimeter = '&';
        } else {
            return self::formatReflectionNamedType($type, $indicateNullable);
        }

        $formattedTypes = [];
        foreach ($type->getTypes() as $namedType) {
            $formattedTypes[] = self::formatReflectionNamedType($namedType, $indicateNullable);
        }

        return \implode($delimeter, $formattedTypes);
    }

    /**
     * Print a single named type.
     */
    private static function formatReflectionNamedType(\ReflectionNamedType $type, bool $indicateNullable): string
    {
        $nullable = $indicateNullable && $type->allowsNull() ? '?' : '';
        $typeName = self::normalizeName($type->getName());

        if ($type->isBuiltin()) {
            return \sprintf('<keyword>%s%s</keyword>', $nullable, OutputFormatter::escape($typeName));
        }

        // Non-builtin type is a class - try to get href for it
        $href = null;
        try {
            $classReflector = new \ReflectionClass($typeName);
            $href = self::getManualHref($classReflector);
        } catch (\ReflectionException $e) {
            // Class doesn't exist or can't be reflected, no href
        }

        return $nullable.LinkFormatter::styleWithHref('class', $typeName, $href);
    }

    /**
     * Wrap text in a style tag, optionally including an href.
     *
     * @deprecated use LinkFormatter::styleWithHref directly
     *
     * @param string      $style The style name (e.g., 'class', 'function')
     * @param string      $text  The text to wrap
     * @param string|null $href  Optional hyperlink URL
     *
     * @return string Formatted text with style and optional href
     */
    private static function styleWithHref(string $style, string $text, ?string $href = null): string
    {
        return LinkFormatter::styleWithHref($style, $text, $href);
    }

    /**
     * Get a hyperlink URL for a reflector if it's in the PHP manual.
     *
     * @param \Reflector $reflector
     *
     * @return string|null URL to php.net or null if not in manual
     */
    private static function getManualHref(\Reflector $reflector): ?string
    {
        // If it's not in the manual, assume it's not on php.net
        if (!self::getManualDoc($reflector)) {
            return null;
        }

        switch (\get_class($reflector)) {
            case \ReflectionClass::class:
            case \ReflectionObject::class:
            case \ReflectionFunction::class:
                $query = self::normalizeName($reflector->name);
                break;

            case \ReflectionMethod::class:
                $query = self::normalizeName($reflector->class).'.'.self::normalizeName($reflector->name);
                break;

            case \ReflectionProperty::class:
            case \ReflectionClassConstant::class:
                // No simple redirect URLs for properties/constants, link to class instead
                $query = self::normalizeName($reflector->class);
                break;

            default:
                return null;
        }

        return \sprintf('https://php.net/%s', $query);
    }

    /**
     * Get manual documentation for a reflector.
     *
     * @param \Reflector $reflector
     *
     * @return string|array|false Documentation string or structured data, or false if not found
     */
    private static function getManualDoc(\Reflector $reflector)
    {
        if (!self::$manual) {
            return false;
        }

        switch (\get_class($reflector)) {
            case \ReflectionClass::class:
            case \ReflectionObject::class:
            case \ReflectionFunction::class:
                $id = self::normalizeName($reflector->name);
                break;

            case \ReflectionMethod::class:
            case \ReflectionClassConstant::class:
                $id = self::normalizeName($reflector->class).'::'.self::normalizeName($reflector->name);
                break;

            case \ReflectionProperty::class:
                $id = self::normalizeName($reflector->class).'::$'.self::normalizeName($reflector->name);
                break;

            case ReflectionConstant::class:
                $id = self::normalizeName($reflector->name);
                break;

            default:
                return false;
        }

        return self::$manual->get($id) ?? false;
    }

    /**
     * Strip scoped PHAR namespace prefixes from a name.
     */
    private static function normalizeName(string $name): string
    {
        return (string) \preg_replace('/^_Psy[a-f0-9]+\\\\/i', '', $name);
    }
}
