<?php

namespace Faker\Provider\en_CA;

/**
 * Extend US class since most fields share the same format
 */
class Address extends \Faker\Provider\en_US\Address
{
    protected static $postcode = ['?#? #?#', '?#?-#?#', '?#?#?#'];

    protected static $postcodeLetters = ['A', 'B', 'C', 'E', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'R', 'S', 'T', 'V', 'X', 'Y'];

    protected static $province = [
        'Alberta',
        'British Columbia',
        'Manitoba',
        'New Brunswick', 'Newfoundland and Labrador', 'Northwest Territories', 'Nova Scotia', 'Nunavut',
        'Ontario',
        'Prince Edward Island',
        'Quebec',
        'Saskatchewan',
        'Yukon Territory',
    ];

    protected static $provinceAbbr = [
        'AB', 'BC', 'MB', 'NB', 'NL', 'NT', 'NS', 'NU', 'ON', 'PE', 'QC', 'SK', 'YT',
    ];

    protected static $addressFormats = [
        "{{streetAddress}}\n{{city}}, {{provinceAbbr}}  {{postcode}}",
    ];

    /**
     * @example 'Ontario'
     */
    public static function province()
    {
        return static::randomElement(static::$province);
    }

    /**
     * @example 'ON'
     */
    public static function provinceAbbr()
    {
        return static::randomElement(static::$provinceAbbr);
    }

    /**
     * Returns a postalcode-safe letter
     *
     * @example A1B 2C3
     */
    public static function randomPostcodeLetter()
    {
        return static::randomElement(static::$postcodeLetters);
    }

    /**
     * @example A1B 2C3
     */
    public static function postcode()
    {
        $string = static::randomElement(static::$postcode);

        $string = preg_replace_callback('/\#/u', [static::class,  'randomDigit'], $string);
        $string = preg_replace_callback('/\?/u', [static::class, 'randomPostcodeLetter'], $string);

        return static::toUpper($string);
    }
}
