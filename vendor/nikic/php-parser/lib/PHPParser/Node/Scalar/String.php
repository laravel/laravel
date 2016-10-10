<?php

/**
 * @property string $value String value
 */
class PHPParser_Node_Scalar_String extends PHPParser_Node_Scalar
{
    protected static $replacements = array(
        '\\' => '\\',
        '$'  =>  '$',
        'n'  => "\n",
        'r'  => "\r",
        't'  => "\t",
        'f'  => "\f",
        'v'  => "\v",
        'e'  => "\x1B",
    );

    /**
     * Constructs a string scalar node.
     *
     * @param string $value      Value of the string
     * @param array  $attributes Additional attributes
     */
    public function __construct($value = '', array $attributes = array()) {
        parent::__construct(
            array(
                'value' => $value
            ),
            $attributes
        );
    }

    /**
     * Parses a string token.
     *
     * @param string $str String token content
     *
     * @return string The parsed string
     */
    public static function parse($str) {
        $bLength = 0;
        if ('b' === $str[0]) {
            $bLength = 1;
        }

        if ('\'' === $str[$bLength]) {
            return str_replace(
                array('\\\\', '\\\''),
                array(  '\\',   '\''),
                substr($str, $bLength + 1, -1)
            );
        } else {
            return self::parseEscapeSequences(substr($str, $bLength + 1, -1), '"');
        }
    }

    /**
     * Parses escape sequences in strings (all string types apart from single quoted).
     *
     * @param string      $str   String without quotes
     * @param null|string $quote Quote type
     *
     * @return string String with escape sequences parsed
     */
    public static function parseEscapeSequences($str, $quote) {
        if (null !== $quote) {
            $str = str_replace('\\' . $quote, $quote, $str);
        }

        return preg_replace_callback(
            '~\\\\([\\\\$nrtfve]|[xX][0-9a-fA-F]{1,2}|[0-7]{1,3})~',
            array(__CLASS__, 'parseCallback'),
            $str
        );
    }

    public static function parseCallback($matches) {
        $str = $matches[1];

        if (isset(self::$replacements[$str])) {
            return self::$replacements[$str];
        } elseif ('x' === $str[0] || 'X' === $str[0]) {
            return chr(hexdec($str));
        } else {
            return chr(octdec($str));
        }
    }

    /**
     * Parses a constant doc string.
     *
     * @param string $startToken Doc string start token content (<<<SMTHG)
     * @param string $str        String token content
     *
     * @return string Parsed string
     */
    public static function parseDocString($startToken, $str) {
        // strip last newline (thanks tokenizer for sticking it into the string!)
        $str = preg_replace('~(\r\n|\n|\r)$~', '', $str);

        // nowdoc string
        if (false !== strpos($startToken, '\'')) {
            return $str;
        }

        return self::parseEscapeSequences($str, null);
    }
}