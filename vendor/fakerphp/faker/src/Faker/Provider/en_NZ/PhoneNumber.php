<?php

namespace Faker\Provider\en_NZ;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    /**
     * An array of en_NZ landline phone number formats
     *
     * @var array
     */
    protected static $formats = [
        // National Calls
        '{{areaCode}}{{beginningNumber}}######',
        '{{areaCode}} {{beginningNumber}}## ####',
    ];

    /**
     * An array of en_NZ mobile (cell) phone number formats
     *
     * @var array
     */
    protected static $mobileFormats = [
        // Local
        '02########',
        '02#########',
        '02# ### ####',
        '02# #### ####',
    ];

    /**
     * An array of toll free phone number formats
     *
     * @var array
     */
    protected static $tollFreeFormats = [
        '0508######',
        '0508 ######',
        '0508 ### ###',
        '0800######',
        '0800 ######',
        '0800 ### ###',
    ];

    /**
     * An array of en_NZ landline area codes
     *
     * @var array
     */
    protected static $areaCodes = [
        '02', '03', '04', '06', '07', '09',
    ];

    /**
     * An array of en_NZ landline beginning numbers
     *
     * @var array
     */
    protected static $beginningNumbers = [
        '2', '3', '4', '5', '6', '7', '8', '9',
    ];

    /**
     * Return a en_NZ mobile phone number
     *
     * @return string
     */
    public static function mobileNumber()
    {
        return static::numerify(static::randomElement(static::$mobileFormats));
    }

    /**
     * Return a en_NZ toll free phone number
     *
     * @return string
     */
    public static function tollFreeNumber()
    {
        return static::numerify(static::randomElement(static::$tollFreeFormats));
    }

    /**
     * Return a en_NZ landline area code
     *
     * @return string
     */
    public static function areaCode()
    {
        return static::numerify(static::randomElement(static::$areaCodes));
    }

    /**
     * Return a en_NZ landline beginning number
     *
     * @return string
     */
    public static function beginningNumber()
    {
        return static::numerify(static::randomElement(static::$beginningNumbers));
    }
}
