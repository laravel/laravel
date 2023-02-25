<?php

namespace Faker\Provider\hy_AM;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    protected static $codes = [91, 96, 99, 43, 77, 93, 94, 98, 97, 77, 55, 95, 41, 49];

    protected static $numberFormats = [
        '######',
        '##-##-##',
        '###-###',
    ];

    protected static $formats = [
        '0{{code}} {{numberFormat}}',
        '(0{{code}}) {{numberFormat}}',
        '+374{{code}} {{numberFormat}}',
        '+374 {{code}} {{numberFormat}}',
    ];

    public function phoneNumber()
    {
        return static::numerify($this->generator->parse(static::randomElement(static::$formats)));
    }

    public function code()
    {
        return static::randomElement(static::$codes);
    }

    public function numberFormat()
    {
        return static::randomElement(static::$numberFormats);
    }
}
