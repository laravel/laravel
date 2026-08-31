<?php

namespace Faker\Provider\fa_IR;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{companyPrefix}} {{companyField}} {{firstName}}',
        '{{companyPrefix}} {{companyField}} {{firstName}}',
        '{{companyPrefix}} {{companyField}} {{firstName}}',
        '{{companyPrefix}} {{companyField}} {{firstName}}',
        '{{companyPrefix}} {{companyField}} {{lastName}}',
        '{{companyField}} {{firstName}}',
        '{{companyField}} {{firstName}}',
        '{{companyField}} {{lastName}}',
    ];

    protected static $companyPrefix = [
        'شرکت', 'موسسه', 'سازمان', 'بنیاد',
    ];

    protected static $companyField = [
        'فناوری اطلاعات', 'راه و ساختمان', 'توسعه معادن', 'استخراج و اکتشاف',
        'سرمایه گذاری', 'نساجی', 'کاریابی', 'تجهیزات اداری', 'تولیدی', 'فولاد',
    ];

    protected static $contract = [
        'رسمی', 'پیمانی', 'تمام وقت', 'پاره وقت', 'پروژه ای', 'ساعتی',
    ];

    /**
     * @example 'شرکت'
     *
     * @return string
     */
    public static function companyPrefix()
    {
        return static::randomElement(static::$companyPrefix);
    }

    /**
     * @example 'سرمایه گذاری'
     *
     * @return string
     */
    public static function companyField()
    {
        return static::randomElement(static::$companyField);
    }

    /**
     * @example 'تمام وقت'
     *
     * @return string
     */
    public function contract()
    {
        return static::randomElement(static::$contract);
    }
}
