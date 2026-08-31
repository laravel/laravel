<?php

namespace Faker\Provider\bn_BD;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{companyName}} {{companyType}}',
    ];

    protected static $names = [
        'রহিম', 'করিম', 'বাবলু',
    ];

    protected static $types = [
        'সিমেন্ট', 'সার', 'ঢেউটিন',
    ];

    public static function companyType()
    {
        return static::randomElement(static::$types);
    }

    public static function companyName()
    {
        return static::randomElement(static::$names);
    }
}
