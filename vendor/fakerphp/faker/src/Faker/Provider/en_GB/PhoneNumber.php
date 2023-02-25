<?php

namespace Faker\Provider\en_GB;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    protected static $formats = [
        '+44(0)##########',
        '+44(0)#### ######',
        '+44(0)#########',
        '+44(0)#### #####',
        '0##########',
        '0#########',
        '0#### ######',
        '0#### #####',
        '0### ### ####',
        '0### #######',
        '(0####) ######',
        '(0####) #####',
        '(0###) ### ####',
        '(0###) #######',
    ];

    /**
     * An array of en_GB mobile (cell) phone number formats
     *
     * @var array
     */
    protected static $mobileFormats = [
        // Local
        '07#########',
        '07### ######',
        '07### ### ###',
    ];

    protected static $e164Formats = [
        '+44##########',
    ];

    /**
     * Return a en_GB mobile phone number
     *
     * @return string
     */
    public static function mobileNumber()
    {
        return static::numerify(static::randomElement(static::$mobileFormats));
    }
}
