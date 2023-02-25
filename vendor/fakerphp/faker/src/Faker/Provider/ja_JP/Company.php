<?php

namespace Faker\Provider\ja_JP;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{companyPrefix}} {{lastName}}',
    ];

    protected static $companyPrefix = ['株式会社', '有限会社'];

    public static function companyPrefix()
    {
        return static::randomElement(static::$companyPrefix);
    }
}
