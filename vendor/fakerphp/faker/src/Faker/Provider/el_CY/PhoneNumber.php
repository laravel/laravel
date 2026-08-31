<?php

namespace Faker\Provider\el_CY;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    protected static $formats = [
        '+3572#######',
        '+3579#######',
        '2#######',
        '9#######',
    ];

    /**
     * An array of el_CY mobile (cell) phone number formats.
     *
     * @var array
     */
    protected static $mobileFormats = [
        '9#######',
    ];

    /**
     * Return a el_CY mobile phone number.
     *
     * @return string
     */
    public static function mobileNumber()
    {
        return static::numerify(static::randomElement(static::$mobileFormats));
    }
}
