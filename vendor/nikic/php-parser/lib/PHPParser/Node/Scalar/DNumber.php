<?php

/**
 * @property float $value Number value
 */
class PHPParser_Node_Scalar_DNumber extends PHPParser_Node_Scalar
{
    /**
     * Constructs a float number scalar node.
     *
     * @param float $value      Value of the number
     * @param array $attributes Additional attributes
     */
    public function __construct($value = 0.0, array $attributes = array()) {
        parent::__construct(
            array(
                'value' => $value
            ),
            $attributes
        );
    }

    /**
     * Parses a DNUMBER token like PHP would.
     *
     * @param string $str A string number
     *
     * @return float The parsed number
     */
    public static function parse($str) {
        // if string contains any of .eE just cast it to float
        if (false !== strpbrk($str, '.eE')) {
            return (float) $str;
        }

        // otherwise it's an integer notation that overflowed into a float
        // if it starts with 0 it's one of the special integer notations
        if ('0' === $str[0]) {
            // hex
            if ('x' === $str[1] || 'X' === $str[1]) {
                return hexdec($str);
            }

            // bin
            if ('b' === $str[1] || 'B' === $str[1]) {
                return bindec($str);
            }

            // oct
            // substr($str, 0, strcspn($str, '89')) cuts the string at the first invalid digit (8 or 9)
            // so that only the digits before that are used
            return octdec(substr($str, 0, strcspn($str, '89')));
        }

        // dec
        return (float) $str;
    }
}