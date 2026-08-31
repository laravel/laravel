<?php

namespace Faker\Provider\de_DE;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    /**
     * @var array<int, string>
     */
    protected static $areaCodeRegexes = [
        2 => '(0[0-389]|0[4-6][1-68]|1[124]|1[0-9][0-9]|2[18]|2[0-9][1-9]|3[14]|3[0-35-9][0-9]|4[1]|4[02-8][0-9]|5[1]|5[02-9][0-9]|6[1]|6[02-9][0-9]|7[1]|7[2-7][0-9]|8[1]|8[02-7][0-9]|9[1]|9[02-9][0-9])',
        3 => '(0|3[15]|3[02-46-9][1-9]|3[02-46-9][02-9][0-9]|4[015]|4[2-4679][1-8]|4[2-4679][02-9][0-9]|5[15]|5[02-46-9][1-9]|5[02-46-9][02-9][0-9]|6[15]|6[02-46-9][1-9]|6[02-46-9][02-9][0-9]|7[15]|7[2-467][1-7]|7[2-467][02-689][0-9]|8[15]|8[2-46-8][013-9]|8[2-46-8][02-9][0-9]|9[15]|9[02-46-9][1-9]|9[02-46-9][02-9][0-9])',
        4 => '(0|1[02-9][0-9]|2[1]|2[02-9][0-9]|3[1]|3[02-9][0-9]|4[1]|4[0-9][0-9]|5[1]|5[02-6][0-9]|6[1]|6[02-8][0-9]|7[1]|7[02-79][0-9]|8[1]|8[02-9][0-9]|9[1]|9[02-7][0-9])',
        5 => '(0[2-8][0-9]|1[1]|1[02-9][0-9]|2[1]|2[02-9][1-9]|3[1]|3[02-8][0-9]|4[1]|4[02-9][1-9]|5[1]|5[02-9][0-9]|6[1]|6[02-9][0-9]|7[1]|7[02-7][1-9]|8[1]|8[02-8][0-9]|9[1]|9[0-7][1-9])',
        6 => '(0[02-9][0-9]|1[1]|1[02-9][0-9]|2[1]|2[02-9][0-9]|3[1]|3[02-9][0-9]|4[1]|4[0-8][0-9]|5[1]|5[02-9][0-9]|6[1]|6[2-9][0-9]|7[1]|7[02-8][1-9]|8[1]|8[02-9][1-9]|9)',
        7 => '(0[2-8][1-6]|1[1]|1[2-9][0-9]|2[1]|2[0-7][0-9]|3[1]|3[02-9][0-9]|4[1]|4[0-8][0-9]|5[1]|5[02-8][0-9]|6[1]|6[02-8][0-9]|7[1]|7[02-7][0-9]|8[1]|8[02-5][1-9]|9[1]|9[03-7][0-9])',
        8 => '(0[2-9][0-9]|1[1]|1[02-79][0-9]|2[1]|2[02-9][0-9]|3[1]|3[02-9][0-9]|4[1]|4[02-6][0-9]|5[1]|5[02-9][0-9]|6[1]|6[2-8][0-9]|7[1]|7[02-8][1-9]|8[1]|8[02-6][0-9]|9)',
        9 => '(0[6]|0[07-9][0-9]|1[1]|1[02-9][0-9]|2[1]|2[02-9][0-9]|3[1]|3[02-9][0-9]|4[1]|4[02-9][0-9]|5[1]|5[02-7][0-9]|6[1]|6[02-8][1-9]|7[1]|7[02-467][0-9]|8[1]|8[02-7][0-9]|9[1]|9[02-7][0-9])',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/National_conventions_for_writing_telephone_numbers#Germany
     * @see https://www.itu.int/oth/T0202000051/en
     * @see https://en.wikipedia.org/wiki/Telephone_numbers_in_Germany
     */
    protected static $formats = [
        // International format
        '+49 {{areaCode}} #######',
        '+49 {{areaCode}} ### ####',
        '+49 (0{{areaCode}}) #######',
        '+49 (0{{areaCode}}) ### ####',
        '+49{{areaCode}}#######',
        '+49{{areaCode}}### ####',

        // Standard formats
        '0{{areaCode}} ### ####',
        '0{{areaCode}} #######',
        '(0{{areaCode}}) ### ####',
        '(0{{areaCode}}) #######',
    ];

    protected static $e164Formats = [
        '+49{{areaCode}}#######',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/Toll-free_telephone_number
     */
    protected static $tollFreeAreaCodes = [
        800,
    ];

    protected static $tollFreeFormats = [
        // Standard formats
        '0{{tollFreeAreaCode}} ### ####',
        '(0{{tollFreeAreaCode}}) ### ####',
        '+49{{tollFreeAreaCode}} ### ####',
    ];

    public function tollFreeAreaCode()
    {
        return self::randomElement(static::$tollFreeAreaCodes);
    }

    public function tollFreePhoneNumber()
    {
        $format = self::randomElement(static::$tollFreeFormats);

        return self::numerify($this->generator->parse($format));
    }

    protected static $mobileCodes = [
        1511, 1512, 1514, 1515, 1516, 1517,
        1520, 1521, 1522, 1523, 1525, 1526, 1529,
        1570, 1573, 1575, 1577, 1578, 1579,
        1590,
    ];

    protected static $mobileFormats = [
        '+49{{mobileCode}}#######',
        '+49 {{mobileCode}} ### ####',
        '0{{mobileCode}}#######',
        '0{{mobileCode}} ### ####',
        '0 {{mobileCode}} ### ####',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/List_of_dialling_codes_in_Germany
     *
     * @return string
     */
    public static function areaCode()
    {
        $firstDigit = self::numberBetween(2, 9);

        return $firstDigit . self::regexify(self::$areaCodeRegexes[$firstDigit]);
    }

    /**
     * Generate a code for a mobile number.
     *
     * @internal Used to generate mobile numbers.
     *
     * @return string
     */
    public static function mobileCode()
    {
        return static::randomElement(static::$mobileCodes);
    }

    /**
     * Generate a mobile number.
     *
     * @example A mobile number: '015111234567'
     * @example A mobile number with spaces: '01511 123 4567'
     * @example A mobile number with international code prefix: '+4915111234567'
     * @example A mobile number with international code prefix and spaces: '+49 1511 123 4567'
     *
     * @return string
     */
    public function mobileNumber()
    {
        return ltrim(static::numerify($this->generator->parse(
            static::randomElement(static::$mobileFormats),
        )));
    }
}
