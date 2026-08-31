<?php

namespace Faker\Provider\hr_HR;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{lastName}} {{companySuffix}}',
        '{{companyPrefix}} {{lastName}}',
        '{{companyPrefix}} {{firstName}}',
    ];

    protected static $companySuffix = [
        'd.o.o.', 'j.d.o.o.', 'Security',
    ];

    protected static $companyPrefix = [
        'Autoškola', 'Cvjećarnica', 'Informatički obrt', 'Kamenorezački obrt', 'Kladionice', 'Market', 'Mesnica', 'Prijevoznički obrt', 'Suvenirnica', 'Turistička agencija', 'Voćarna',
    ];

    public static function companyPrefix()
    {
        return static::randomElement(static::$companyPrefix);
    }
}
