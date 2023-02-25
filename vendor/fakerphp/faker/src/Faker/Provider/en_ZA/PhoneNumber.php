<?php

namespace Faker\Provider\en_ZA;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    protected static $formats = [
        '+27({{areaCode}})#######',
        '+27{{areaCode}}#######',
        '0{{areaCode}}#######',
        '0{{areaCode}} ### ####',
        '0{{areaCode}}-###-####',
    ];

    protected static $cellphoneFormats = [
        '+27{{cellphoneCode}}#######',
        '0{{cellphoneCode}}#######',
        '0{{cellphoneCode}} ### ####',
        '0{{cellphoneCode}}-###-####',
    ];

    protected static $specialFormats = [
        '{{specialCode}}#######',
        '{{specialCode}} ### ####',
        '{{specialCode}}-###-####',
        '({{specialCode}})###-####',
    ];

    protected static $tollFreeAreaCodes = [
        '0800', '0860', '0861', '0862',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/Telephone_numbers_in_South_Africa
     */
    public static function areaCode()
    {
        $digits[] = self::numberBetween(1, 5);

        switch ($digits[0]) {
            case 1:
                $digits[] = self::numberBetween(1, 8);

                break;

            case 2:
                $number = self::numberBetween(1, 8);
                $digits[] = in_array($number, [5, 6], false) ? $number + 2 : $number;

                break;

            case 3:
                $number = self::numberBetween(1, 8);
                $digits[] = in_array($number, [7, 8], false) ? $number - 2 : $number;

                break;

            case 4:
                $digits[] = self::numberBetween(1, 9);

                break;

            case 5:
                $number = self::numberBetween(1, 8);
                $digits[] = in_array($number, [2, 5], false) ? $number + 2 : $number;

                break;
        }

        return implode('', $digits);
    }

    public static function cellphoneCode()
    {
        $digits[] = self::numberBetween(6, 8);

        switch ($digits[0]) {
            case 6:
                $digits[] = self::numberBetween(0, 2);

                break;

            case 7:
                $number = self::numberBetween(1, 9);
                $digits[] = in_array($number, [5, 7], false) ? $number + 1 : $number;

                break;

            case 8:
                $digits[] = self::numberBetween(1, 9);

                break;
        }

        return implode('', $digits);
    }

    public static function specialCode()
    {
        return static::randomElement(static::$tollFreeAreaCodes);
    }

    public function mobileNumber()
    {
        $format = static::randomElement(static::$cellphoneFormats);

        return self::numerify($this->generator->parse($format));
    }

    public function tollFreeNumber()
    {
        $format = static::randomElement(static::$specialFormats);

        return self::numerify($this->generator->parse($format));
    }
}
