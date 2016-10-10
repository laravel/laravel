<?php

/**
 * @property int $value Number value
 */
class PHPParser_Node_Scalar_LNumber extends PHPParser_Node_Scalar
{
    /**
     * Constructs an integer number scalar node.
     *
     * @param int   $value      Value of the number
     * @param array $attributes Additional attributes
     */
    public function __construct($value = 0, array $attributes = array()) {
        parent::__construct(
            array(
                'value' => $value
            ),
            $attributes
        );
    }

    /**
     * Parses an LNUMBER token (dec, hex, oct and bin notations) like PHP would.
     *
     * @param string $str A string number
     *
     * @return int The parsed number
     */
    public static function parse($str) {
        // handle plain 0 specially
        if ('0' === $str) {
            return 0;
        }

        // if first char is 0 (and number isn't 0) it's a special syntax
        if ('0' === $str[0]) {
            // hex
            if ('x' === $str[1] || 'X' === $str[1]) {
                return hexdec($str);
            }

            // bin
            if ('b' === $str[1] || 'B' === $str[1]) {
                return bindec($str);
            }

            // oct (intval instead of octdec to get proper cutting behavior with malformed numbers)
            return intval($str, 8);
        }

        // dec
        return (int) $str;
    }
}