<?php

namespace Faker\Provider;

class Color extends Base
{
    protected static $safeColorNames = [
        'black', 'maroon', 'green', 'navy', 'olive',
        'purple', 'teal', 'lime', 'blue', 'silver',
        'gray', 'yellow', 'fuchsia', 'aqua', 'white',
    ];

    protected static $allColorNames = [
        'AliceBlue', 'AntiqueWhite', 'Aqua', 'Aquamarine',
        'Azure', 'Beige', 'Bisque', 'Black', 'BlanchedAlmond',
        'Blue', 'BlueViolet', 'Brown', 'BurlyWood', 'CadetBlue',
        'Chartreuse', 'Chocolate', 'Coral', 'CornflowerBlue',
        'Cornsilk', 'Crimson', 'Cyan', 'DarkBlue', 'DarkCyan',
        'DarkGoldenRod', 'DarkGray', 'DarkGreen', 'DarkKhaki',
        'DarkMagenta', 'DarkOliveGreen', 'Darkorange', 'DarkOrchid',
        'DarkRed', 'DarkSalmon', 'DarkSeaGreen', 'DarkSlateBlue',
        'DarkSlateGray', 'DarkTurquoise', 'DarkViolet', 'DeepPink',
        'DeepSkyBlue', 'DimGray', 'DimGrey', 'DodgerBlue', 'FireBrick',
        'FloralWhite', 'ForestGreen', 'Fuchsia', 'Gainsboro', 'GhostWhite',
        'Gold', 'GoldenRod', 'Gray', 'Green', 'GreenYellow', 'HoneyDew',
        'HotPink', 'IndianRed', 'Indigo', 'Ivory', 'Khaki', 'Lavender',
        'LavenderBlush', 'LawnGreen', 'LemonChiffon', 'LightBlue', 'LightCoral',
        'LightCyan', 'LightGoldenRodYellow', 'LightGray', 'LightGreen', 'LightPink',
        'LightSalmon', 'LightSeaGreen', 'LightSkyBlue', 'LightSlateGray', 'LightSteelBlue',
        'LightYellow', 'Lime', 'LimeGreen', 'Linen', 'Magenta', 'Maroon', 'MediumAquaMarine',
        'MediumBlue', 'MediumOrchid', 'MediumPurple', 'MediumSeaGreen', 'MediumSlateBlue',
        'MediumSpringGreen', 'MediumTurquoise', 'MediumVioletRed', 'MidnightBlue',
        'MintCream', 'MistyRose', 'Moccasin', 'NavajoWhite', 'Navy', 'OldLace', 'Olive',
        'OliveDrab', 'Orange', 'OrangeRed', 'Orchid', 'PaleGoldenRod', 'PaleGreen',
        'PaleTurquoise', 'PaleVioletRed', 'PapayaWhip', 'PeachPuff', 'Peru', 'Pink', 'Plum',
        'PowderBlue', 'Purple', 'Red', 'RosyBrown', 'RoyalBlue', 'SaddleBrown', 'Salmon',
        'SandyBrown', 'SeaGreen', 'SeaShell', 'Sienna', 'Silver', 'SkyBlue', 'SlateBlue',
        'SlateGray', 'Snow', 'SpringGreen', 'SteelBlue', 'Tan', 'Teal', 'Thistle', 'Tomato',
        'Turquoise', 'Violet', 'Wheat', 'White', 'WhiteSmoke', 'Yellow', 'YellowGreen',
    ];

    /**
     * @example '#fa3cc2'
     *
     * @return string
     */
    public static function hexColor()
    {
        return '#' . str_pad(dechex(self::numberBetween(1, 16777215)), 6, '0', STR_PAD_LEFT);
    }

    /**
     * @example '#ff0044'
     *
     * @return string
     */
    public static function safeHexColor()
    {
        $color = str_pad(dechex(self::numberBetween(0, 255)), 3, '0', STR_PAD_LEFT);

        return '#' . $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
    }

    /**
     * @example 'array(0,255,122)'
     *
     * @return array
     */
    public static function rgbColorAsArray()
    {
        $color = static::hexColor();

        return [
            hexdec(substr($color, 1, 2)),
            hexdec(substr($color, 3, 2)),
            hexdec(substr($color, 5, 2)),
        ];
    }

    /**
     * @example '0,255,122'
     *
     * @return string
     */
    public static function rgbColor()
    {
        return implode(',', static::rgbColorAsArray());
    }

    /**
     * @example 'rgb(0,255,122)'
     *
     * @return string
     */
    public static function rgbCssColor()
    {
        return 'rgb(' . static::rgbColor() . ')';
    }

    /**
     * @example 'rgba(0,255,122,0.8)'
     *
     * @return string
     */
    public static function rgbaCssColor()
    {
        return 'rgba(' . static::rgbColor() . ',' . static::randomFloat(1, 0, 1) . ')';
    }

    /**
     * @example 'blue'
     *
     * @return string
     */
    public static function safeColorName()
    {
        return static::randomElement(static::$safeColorNames);
    }

    /**
     * @example 'NavajoWhite'
     *
     * @return string
     */
    public static function colorName()
    {
        return static::randomElement(static::$allColorNames);
    }

    /**
     * @example '340,50,20'
     *
     * @return string
     */
    public static function hslColor()
    {
        return sprintf(
            '%s,%s,%s',
            self::numberBetween(0, 360),
            self::numberBetween(0, 100),
            self::numberBetween(0, 100)
        );
    }

    /**
     * @example array(340, 50, 20)
     *
     * @return array
     */
    public static function hslColorAsArray()
    {
        return [
            self::numberBetween(0, 360),
            self::numberBetween(0, 100),
            self::numberBetween(0, 100),
        ];
    }
}
