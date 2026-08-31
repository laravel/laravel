<?php

namespace Faker\Provider\th_TH;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    /**
     * @var array Thai phone number formats
     *
     * @see http://www4.sit.kmutt.ac.th/content/%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%80%E0%B8%82%E0%B8%B5%E0%B8%A2%E0%B8%99%E0%B8%AB%E0%B8%A1%E0%B8%B2%E0%B8%A2%E0%B9%80%E0%B8%A5%E0%B8%82%E0%B9%82%E0%B8%97%E0%B8%A3%E0%B8%A8%E0%B8%B1%E0%B8%9E%E0%B8%97%E0%B9%8C%E0%B9%83%E0%B8%AB%E0%B9%89%E0%B8%96%E0%B8%B9%E0%B8%81%E0%B8%95%E0%B9%89%E0%B8%AD%E0%B8%87
     */
    protected static $formats = [
        '0 #### ####',
        '+66 #### ####',
        '0########',
    ];

    /**
     * @var array Thai mobile phone number formats
     */
    protected static $mobileFormats = [
        '08# ### ####',
        '08 #### ####',
        '09# ### ####',
        '09 #### ####',
        '06# ### ####',
        '06 #### ####',
    ];

    /**
     * Returns a Thai mobile phone number
     *
     * @return string
     */
    public static function mobileNumber()
    {
        return static::numerify(static::randomElement(static::$mobileFormats));
    }
}
