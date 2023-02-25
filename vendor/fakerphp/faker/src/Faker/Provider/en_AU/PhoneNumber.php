<?php

namespace Faker\Provider\en_AU;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    protected static $formats = [
        // Local calls
        '#### ####',
        '####-####',
        '####.####',
        '########',

        // National dialing
        '0{{areaCode}} #### ####',
        '0{{areaCode}}-####-####',
        '0{{areaCode}}.####.####',
        '0{{areaCode}}########',

        // Optional parenthesis
        '(0{{areaCode}}) #### ####',
        '(0{{areaCode}})-####-####',
        '(0{{areaCode}}).####.####',
        '(0{{areaCode}})########',

        // International drops the 0
        '+61 {{areaCode}} #### ####',
        '+61-{{areaCode}}-####-####',
        '+61.{{areaCode}}.####.####',
        '+61{{areaCode}}########',
    ];

    // 04 Mobile telephones (Australia-wide) mostly commonly written 4 - 3 - 3 instead of 2 - 4 - 4
    protected static $mobileFormats = [
        '04## ### ###',
        '04##-###-###',
        '04##.###.###',
        '+61 4## ### ###',
        '+61-4##-###-###',
        '+61.4##.###.###',
    ];

    protected static $areacodes = [
        '2', '3', '7', '8',
    ];

    public static function mobileNumber()
    {
        return static::numerify(static::randomElement(static::$mobileFormats));
    }

    public static function areaCode()
    {
        return static::numerify(static::randomElement(static::$areacodes));
    }
}
