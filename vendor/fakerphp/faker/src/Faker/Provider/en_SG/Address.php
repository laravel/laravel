<?php

namespace Faker\Provider\en_SG;

class Address extends \Faker\Provider\Address
{
    // http://en.wikipedia.org/wiki/Singapore_Post#Address_format
    protected static $streetNumber = ['##', '###'];

    // http://en.wikipedia.org/wiki/Singapore_Post#Address_format
    protected static $blockNumber = [
        'Blk ##',
        'Blk ###',
        'Blk ###A',
        'Blk ###B',
        'Blk ###C',
        'Blk ###D',
        'Blk ###E',
        'Blk ###F',
        'Blk ###G',
        'Blk ###H',
    ];

    // http://www.streetdirectory.com/asia_travel/travel/street/alphabet2/
    protected static $streetSuffix = [
        'Alley', 'Avenue',
        'Bridge',
        'Crescent',
        'Drive',
        'Grove',
        'Highway', 'Hill',
        'Lane', 'Link',
        'Park', 'Place',
        'Quay',
        'Road',
        'Walk', 'Way',
    ];

    // http://www.streetdirectory.com/asia_travel/travel/street/alphabet2/
    protected static $streetPrefix = [
        'Jalan',
    ];

    // http://www.streetdirectory.com/asia_travel/travel/street/alphabet2/
    // http://remembersingapore.org/2011/04/04/old-names-of-places/
    protected static $streetName = [
        'Adam', 'Airport', 'Alexandra', 'Aljunied', 'Ampang', 'Ann Siang', 'Angus', 'Anson', 'Armenian',
        'Balmoral', 'Battery', 'Bencoolen',
        'Collyer', 'Clarke', 'Church', 'Cecil', 'Cross', 'Chulia', 'Cheang Hong Lim', 'Chin Swee', 'Choon Guan',
        'Devonshire', 'Dublin', 'Duxton', 'D\'Almeida',
        'East Coast', 'Eden', 'Edgware', 'Eunos',
        'Fifth', 'First', 'Funan', 'Fullerton',
        'George', 'Glasgow', 'Grange',
        'Havelock', 'High', 'Hylam',
        'International Business', 'International', 'Irving',
        'Jubilee',
        'Kensington Park', 'Kitchener', 'Knights',
        'Lancaster', 'Leicester', 'Lengkok Bahru', 'Lim Teck Kim',
        'Malay', 'Market', 'Middle', 'Malabar', 'Merchant', 'Mohammed Sultan',
        'Napier', 'Nathan', 'Newton',
        'Ocean', 'One Tree', 'Orchard', 'Outram', 'Ophir',
        'Pekin', 'Peng Siang', 'Prince Edward', 'Palmer',
        'Quality', 'Queen',
        'Raffles', 'Robinson', 'Rochor', 'Regent', 'Ridley', 'River Valley',
        'Sixth', 'Somerset', 'Stanley', 'Stamford', 'Shenton', 'Sultan',
        'Telok Ayer', 'Temple', 'Thomson', 'Unity', 'Victoria', 'Xilin', 'York', 'Zion',
    ];

    protected static $streetAddressFormats = [
        '{{streetPrefix}} {{streetName}}',
        '{{streetName}} {{streetSuffix}}',
    ];

    protected static $floorNumber = [
        '##', '0#',
    ];

    protected static $apartmentNumber = [
        '##', '###',
    ];

    // http://en.wikipedia.org/wiki/Singapore_Post#Address_format
    protected static $addressFormats = [
        "{{streetNumber}} {{streetAddress}}\n{{townName}} {{postcode}}",
        "{{blockNumber}} {{streetAddress}}\n{{floorNumber}} {{apartmentNumber}}\n{{townName}} {{postcode}}",
    ];

    protected static $townName = 'SINGAPORE';

    protected static $postcode = ['######'];

    protected static $country = [
        'SINGAPORE',
    ];

    public function streetPrefix()
    {
        return static::randomElement(static::$streetPrefix);
    }

    public function streetNumber()
    {
        return static::numerify(static::randomElement(static::$streetNumber));
    }

    public function blockNumber()
    {
        return static::numerify(static::randomElement(static::$blockNumber));
    }

    public function floorNumber()
    {
        return static::randomElement(static::$floorNumber);
    }

    public function apartmentNumber()
    {
        return static::randomElement(static::$apartmentNumber);
    }

    public function townName()
    {
        return static::$townName;
    }
}
