<?php

namespace Faker\Provider\en_SG;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    protected static $internationalCodePrefix = [
        '+65',
        '65',
    ];

    protected static $zeroToEight = [0, 1, 2, 3, 4, 5, 6, 7, 8];

    protected static $oneToEight = [1, 2, 3, 4, 5, 6, 7, 8];

    protected static $mobileNumberFormats = [
        '{{internationalCodePrefix}}9{{zeroToEight}}## ####',
        '{{internationalCodePrefix}} 9{{zeroToEight}}## ####',
        '9{{zeroToEight}}## ####',
        '{{internationalCodePrefix}}8{{oneToEight}}## ####',
        '{{internationalCodePrefix}} 8{{oneToEight}}## ####',
        '8{{oneToEight}}## ####',
    ];

    protected static $fixedLineNumberFormats = [
        '{{internationalCodePrefix}}6### ####',
        '{{internationalCodePrefix}} 6### ####',
        '6### ####',
    ];

    // http://en.wikipedia.org/wiki/Telephone_numbers_in_Singapore#Numbering_plan
    protected static $formats = [
        '{{mobileNumber}}',
        '{{fixedLineNumber}}',
    ];

    protected static $voipNumber = [
        '{{internationalCodePrefix}}3### ####',
        '{{internationalCodePrefix}} 3### ####',
        '3### ####',
    ];

    protected static $tollFreeInternationalNumber = [
        '800 ### ####',
    ];

    protected static $tollFreeLineNumber = [
        '1800 ### ####',
    ];

    protected static $premiumPhoneNumber = [
        '1900 ### ####',
    ];

    public function tollFreeInternationalNumber()
    {
        return static::numerify(static::randomElement(static::$tollFreeInternationalNumber));
    }

    public function tollFreeLineNumber()
    {
        return static::numerify(static::randomElement(static::$tollFreeLineNumber));
    }

    public function premiumPhoneNumber()
    {
        return static::numerify(static::randomElement(static::$premiumPhoneNumber));
    }

    public function mobileNumber()
    {
        $format = static::randomElement(static::$mobileNumberFormats);

        return static::numerify($this->generator->parse($format));
    }

    public function fixedLineNumber()
    {
        $format = static::randomElement(static::$fixedLineNumberFormats);

        return static::numerify($this->generator->parse($format));
    }

    public function voipNumber()
    {
        $format = static::randomElement(static::$voipNumber);

        return static::numerify($this->generator->parse($format));
    }

    public function internationalCodePrefix()
    {
        return static::randomElement(static::$internationalCodePrefix);
    }

    public function zeroToEight()
    {
        return static::randomElement(static::$zeroToEight);
    }

    public function oneToEight()
    {
        return static::randomElement(static::$oneToEight);
    }
}
