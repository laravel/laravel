<?php

namespace Faker\Provider\en_NZ;

class Address extends \Faker\Provider\en_US\Address
{
    /**
     * An array of en_NZ (New Zealand) building number formats
     *
     * @var array
     */
    protected static $buildingNumber = ['%', '%#', '%##'];

    /**
     * An array of en_NZ (New Zealand) street suffixes
     *
     * @var array
     */
    protected static $streetSuffix = [
        'Avenue', 'Close', 'Court', 'Crescent', 'Drive', 'Esplanade', 'Grove', 'Heights', 'Highway', 'Hill', 'Lane', 'Line', 'Mall', 'Parade', 'Place', 'Quay', 'Rise', 'Road', 'Square', 'Street', 'Terrace', 'Way',
    ];

    /**
     * City suffixes
     *
     * @var array
     */
    protected static $citySuffix = ['ville', 'ston'];

    /**
     * City formats
     *
     * @var array
     */
    protected static $cityFormats = ['{{firstName}}{{citySuffix}}'];

    /**
     * An array of en_NZ (New Zealand) regions
     *
     * @see http://en.wikipedia.org/wiki/Regions_of_New_Zealand
     *
     * @var array
     */
    protected static $region = [
        'Auckland', 'Bay of Plenty', 'Canterbury', 'Gisborne', 'Hawkes Bay', 'Manawatu-Whanganui', 'Marlborough', 'Nelson', 'Northland', 'Otago', 'Southland', 'Taranaki', 'Tasman', 'Waikato', 'Wellington', 'West Coast',
    ];

    /**
     * An array of en_NZ (New Zealand) poscode formats
     *
     * @var array
     */
    protected static $postcode = ['####'];

    /**
     * An array of en_NZ (New Zealand) address formats
     *
     * @var array
     */
    protected static $addressFormats = ['{{buildingNumber}} {{streetName}}, {{city}}, {{region}}, {{postcode}}'];

    /**
     * An array of en_NZ (New Zealand) street address formats
     *
     * @var array
     */
    protected static $streetAddressFormats = ['{{buildingNumber}} {{streetName}}'];

    /**
     * Return a en_NZ (New Zealand) postcode
     *
     * @return string
     */
    public static function postcode()
    {
        return static::numerify(static::randomElement(static::$postcode));
    }

    /**
     * Return a en_NZ (New Zealand) region
     *
     * @return string
     */
    public static function region()
    {
        return static::randomElement(static::$region);
    }
}
