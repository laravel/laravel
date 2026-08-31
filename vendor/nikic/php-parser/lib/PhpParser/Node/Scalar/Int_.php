<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Error;
use PhpParser\Node\Scalar;

class Int_ extends Scalar {
    /* For use in "kind" attribute */
    public const KIND_BIN = 2;
    public const KIND_OCT = 8;
    public const KIND_DEC = 10;
    public const KIND_HEX = 16;

    /** @var int Number value */
    public int $value;

    /**
     * Constructs an integer number scalar node.
     *
     * @param int $value Value of the number
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(int $value, array $attributes = []) {
        $this->attributes = $attributes;
        $this->value = $value;
    }

    public function getSubNodeNames(): array {
        return ['value'];
    }

    /**
     * Constructs an Int node from a string number literal.
     *
     * @param string $str String number literal (decimal, octal, hex or binary)
     * @param array<string, mixed> $attributes Additional attributes
     * @param bool $allowInvalidOctal Whether to allow invalid octal numbers (PHP 5)
     *
     * @return Int_ The constructed LNumber, including kind attribute
     */
    public static function fromString(string $str, array $attributes = [], bool $allowInvalidOctal = false): Int_ {
        $attributes['rawValue'] = $str;

        $str = str_replace('_', '', $str);

        if ('0' !== $str[0] || '0' === $str) {
            $attributes['kind'] = Int_::KIND_DEC;
            return new Int_((int) $str, $attributes);
        }

        if ('x' === $str[1] || 'X' === $str[1]) {
            $attributes['kind'] = Int_::KIND_HEX;
            return new Int_(hexdec($str), $attributes);
        }

        if ('b' === $str[1] || 'B' === $str[1]) {
            $attributes['kind'] = Int_::KIND_BIN;
            return new Int_(bindec($str), $attributes);
        }

        if (!$allowInvalidOctal && strpbrk($str, '89')) {
            throw new Error('Invalid numeric literal', $attributes);
        }

        // Strip optional explicit octal prefix.
        if ('o' === $str[1] || 'O' === $str[1]) {
            $str = substr($str, 2);
        }

        // use intval instead of octdec to get proper cutting behavior with malformed numbers
        $attributes['kind'] = Int_::KIND_OCT;
        return new Int_(intval($str, 8), $attributes);
    }

    public function getType(): string {
        return 'Scalar_Int';
    }
}

// @deprecated compatibility alias
class_alias(Int_::class, LNumber::class);
