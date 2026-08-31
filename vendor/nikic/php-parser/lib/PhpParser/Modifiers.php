<?php declare(strict_types=1);

namespace PhpParser;

/**
 * Modifiers used (as a bit mask) by various flags subnodes, for example on classes, functions,
 * properties and constants.
 */
final class Modifiers {
    public const PUBLIC    =  1;
    public const PROTECTED =  2;
    public const PRIVATE   =  4;
    public const STATIC    =  8;
    public const ABSTRACT  = 16;
    public const FINAL     = 32;
    public const READONLY  = 64;
    public const PUBLIC_SET = 128;
    public const PROTECTED_SET = 256;
    public const PRIVATE_SET = 512;

    public const VISIBILITY_MASK = self::PUBLIC | self::PROTECTED | self::PRIVATE;

    public const VISIBILITY_SET_MASK = self::PUBLIC_SET | self::PROTECTED_SET | self::PRIVATE_SET;

    private const TO_STRING_MAP = [
        self::PUBLIC  => 'public',
        self::PROTECTED => 'protected',
        self::PRIVATE => 'private',
        self::STATIC  => 'static',
        self::ABSTRACT => 'abstract',
        self::FINAL  => 'final',
        self::READONLY  => 'readonly',
        self::PUBLIC_SET => 'public(set)',
        self::PROTECTED_SET => 'protected(set)',
        self::PRIVATE_SET => 'private(set)',
    ];

    public static function toString(int $modifier): string {
        if (!isset(self::TO_STRING_MAP[$modifier])) {
            throw new \InvalidArgumentException("Unknown modifier $modifier");
        }
        return self::TO_STRING_MAP[$modifier];
    }

    private static function isValidModifier(int $modifier): bool {
        $isPow2 = ($modifier & ($modifier - 1)) == 0 && $modifier != 0;
        return $isPow2 && $modifier <= self::PRIVATE_SET;
    }

    /**
     * @internal
     */
    public static function verifyClassModifier(int $a, int $b): void {
        assert(self::isValidModifier($b));
        if (($a & $b) != 0) {
            throw new Error(
                'Multiple ' . self::toString($b) . ' modifiers are not allowed');
        }

        if ($a & 48 && $b & 48) {
            throw new Error('Cannot use the final modifier on an abstract class');
        }
    }

    /**
     * @internal
     */
    public static function verifyModifier(int $a, int $b): void {
        assert(self::isValidModifier($b));
        if (($a & Modifiers::VISIBILITY_MASK && $b & Modifiers::VISIBILITY_MASK) ||
            ($a & Modifiers::VISIBILITY_SET_MASK && $b & Modifiers::VISIBILITY_SET_MASK)
        ) {
            throw new Error('Multiple access type modifiers are not allowed');
        }

        if (($a & $b) != 0) {
            throw new Error(
                'Multiple ' . self::toString($b) . ' modifiers are not allowed');
        }

        if ($a & 48 && $b & 48) {
            throw new Error('Cannot use the final modifier on an abstract class member');
        }
    }
}
