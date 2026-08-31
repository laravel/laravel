<?php

namespace Faker\Provider\fa_IR;

class Address extends \Faker\Provider\Address
{
    protected static $cityPrefix = ['استان'];
    protected static $streetPrefix = ['خیابان'];
    protected static $buildingNamePrefix = ['ساختمان'];
    protected static $buildingNumberPrefix = ['پلاک', 'قطعه'];
    protected static $postcodePrefix = ['کد پستی'];

    protected static $cityName = [
        'آذربایجان شرقی', 'آذربایجان غربی', 'اردبیل', 'اصفهان', 'البرز', 'ایلام', 'بوشهر',
        'تهران', 'خراسان جنوبی', 'خراسان رضوی', 'خراسان شمالی', 'خوزستان', 'زنجان', 'سمنان',
        'سیستان و بلوچستان', 'فارس', 'قزوین', 'قم', 'لرستان', 'مازندران', 'مرکزی', 'هرمزگان',
        'همدان', 'چهارمحال و بختیاری', 'کردستان', 'کرمان', 'کرمانشاه', 'کهگیلویه و بویراحمد',
        'گلستان', 'گیلان', 'یزد',
    ];

    protected static $cityFormats = [
        '{{cityName}}',
        '{{cityPrefix}} {{cityName}}',
    ];
    protected static $streetNameFormats = [
        '{{streetPrefix}} {{lastName}}',
    ];
    protected static $streetAddressFormats = [
        '{{streetName}} {{building}}',
    ];
    protected static $addressFormats = [
        '{{city}} {{streetAddress}} {{postcodePrefix}} {{postcode}}',
        '{{city}} {{streetAddress}}',
    ];
    protected static $buildingFormat = [
        '{{buildingNamePrefix}} {{firstName}} {{buildingNumberPrefix}} {{buildingNumber}}',
        '{{buildingNamePrefix}} {{firstName}}',
    ];

    protected static $postcode = ['##########'];
    protected static $country = ['ایران'];

    /**
     * @example 'استان'
     */
    public static function cityPrefix()
    {
        return static::randomElement(static::$cityPrefix);
    }

    /**
     * @example 'زنجان'
     */
    public static function cityName()
    {
        return static::randomElement(static::$cityName);
    }

    /**
     * @example 'خیابان'
     */
    public static function streetPrefix()
    {
        return static::randomElement(static::$streetPrefix);
    }

    /**
     * @example 'ساختمان'
     */
    public static function buildingNamePrefix()
    {
        return static::randomElement(static::$buildingNamePrefix);
    }

    /**
     * @example 'پلاک'
     */
    public static function buildingNumberPrefix()
    {
        return static::randomElement(static::$buildingNumberPrefix);
    }

    /**
     * @example 'ساختمان آفتاب پلاک 24'
     */
    public function building()
    {
        $format = static::randomElement(static::$buildingFormat);

        return $this->generator->parse($format);
    }

    /**
     * @example 'کد پستی'
     */
    public static function postcodePrefix()
    {
        return static::randomElement(static::$postcodePrefix);
    }
}
