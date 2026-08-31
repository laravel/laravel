<?php

namespace Faker\Provider\en_AU;

/**
 * @see http://www.ipaustralia.gov.au/about-us/corporate/address-standards/
 */
class Address extends \Faker\Provider\en_US\Address
{
    protected static $cityPrefix = ['North', 'East', 'West', 'South', 'New', 'Lake', 'Port', 'St.'];

    protected static $buildingNumber = ['%##', '%#', '%'];

    protected static $buildingLetters = ['A', 'B', 'C', 'D'];

    protected static $streetSuffix = [
        'Access', 'Alley', 'Alleyway', 'Amble', 'Anchorage', 'Approach', 'Arcade', 'Artery', 'Avenue', 'Basin', 'Beach', 'Bend', 'Block', 'Boulevard', 'Brace', 'Brae', 'Break', 'Bridge', 'Broadway', 'Brow', 'Bypass', 'Byway', 'Causeway', 'Centre', 'Centreway', 'Chase', 'Circle', 'Circlet', 'Circuit', 'Circus', 'Close', 'Colonnade', 'Common', 'Concourse', 'Copse', 'Corner', 'Corso', 'Court', 'Courtyard', 'Cove', 'Crescent', 'Crest', 'Cross', 'Crossing', 'Crossroad', 'Crossway', 'Cruiseway', 'Cul-de-sac', 'Cutting', 'Dale', 'Dell', 'Deviation', 'Dip', 'Distributor', 'Drive', 'Driveway', 'Edge', 'Elbow', 'End', 'Entrance', 'Esplanade', 'Estate', 'Expressway', 'Extension', 'Fairway', 'Fire Track', 'Firetrail', 'Flat', 'Follow', 'Footway', 'Foreshore', 'Formation', 'Freeway', 'Front', 'Frontage', 'Gap', 'Garden', 'Gardens', 'Gate', 'Gates', 'Glade', 'Glen', 'Grange', 'Green', 'Ground', 'Grove', 'Gully', 'Heights', 'Highroad', 'Highway', 'Hill', 'Interchange', 'Intersection', 'Junction', 'Key', 'Landing', 'Lane', 'Laneway', 'Lees', 'Line', 'Link', 'Little', 'Lookout', 'Loop', 'Lower', 'Mall', 'Meander', 'Mew', 'Mews', 'Motorway', 'Mount', 'Nook', 'Outlook', 'Parade', 'Park', 'Parklands', 'Parkway', 'Part', 'Pass', 'Path', 'Pathway', 'Piazza', 'Place', 'Plateau', 'Plaza', 'Pocket', 'Point', 'Port', 'Promenade', 'Quad', 'Quadrangle', 'Quadrant', 'Quay', 'Quays', 'Ramble', 'Ramp', 'Range', 'Reach', 'Reserve', 'Rest', 'Retreat', 'Ride', 'Ridge', 'Ridgeway', 'Right Of Way', 'Ring', 'Rise', 'River', 'Riverway', 'Riviera', 'Road', 'Roads', 'Roadside', 'Roadway', 'Ronde', 'Rosebowl', 'Rotary', 'Round', 'Route', 'Row', 'Rue', 'Run', 'Service Way', 'Siding', 'Slope', 'Sound', 'Spur', 'Square', 'Stairs', 'State Highway', 'Steps', 'Strand', 'Street', 'Strip', 'Subway', 'Tarn', 'Terrace', 'Thoroughfare', 'Tollway', 'Top', 'Tor', 'Towers', 'Track', 'Trail', 'Trailer', 'Triangle', 'Trunkway', 'Turn', 'Underpass', 'Upper', 'Vale', 'Viaduct', 'View', 'Villas', 'Vista', 'Wade', 'Walk', 'Walkway', 'Way', 'Wynd',
    ];

    protected static $postcode = [
        // as per https://en.wikipedia.org/wiki/Postcodes_in_Australia
        // NSW
        '1###',
        '20##', '21##', '22##', '23##', '24##', '25##',
        '2619', '262#', '263#', '264#', '265#', '266#', '267#', '268#', '269#', '27##', '28##',
        '292#', '293#', '294#', '295#', '296#', '297#', '298#', '299#',
        // ACT
        '02##',
        '260#', '261#',
        '290#', '291#', '2920',
        // VIC
        '3###',
        '8###',
        // QLD
        '4###',
        '9###',
        // SA
        '5###',
        // WA
        '6###',
        // TAS
        '7###',
        // NT
        '08##',
        '09##',
    ];

    protected static $state = [
        'Australian Capital Territory', 'New South Wales', 'Northern Territory', 'Queensland', 'South Australia', 'Tasmania', 'Victoria', 'Western Australia',
    ];

    protected static $stateAbbr = [
        'ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA',
    ];

    protected static $streetAddressFormats = [
        '{{buildingNumber}} {{streetName}}',
        '{{buildingNumber}}{{buildingLetter}} {{streetName}}',
        '{{secondaryAddress}} {{buildingNumber}} {{streetName}}',
    ];

    protected static $secondaryAddressFormats = [
        'Apt. ###',
        'Flat ##',
        'Suite ###',
        'Unit ##',
        'Level #',
        '### /',
        '## /',
        '# /',
    ];

    /**
     * Returns a sane building letter
     *
     * @example B
     */
    public static function buildingLetter()
    {
        return static::toUpper(static::randomElement(static::$buildingLetters));
    }

    /**
     * Returns a sane city prefix
     *
     * @example West
     */
    public static function cityPrefix()
    {
        return static::randomElement(static::$cityPrefix);
    }

    /**
     * Returns a sane street suffix
     *
     * @example Beach
     */
    public static function streetSuffix()
    {
        return static::randomElement(static::$streetSuffix);
    }

    /**
     * Returns a sane state
     *
     * @example New South Wales
     */
    public static function state()
    {
        return static::randomElement(static::$state);
    }
}
