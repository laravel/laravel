<?php

namespace Faker\Provider\en_GB;

class Address extends \Faker\Provider\Address
{
    protected static $cityPrefix = ['North', 'East', 'West', 'South', 'New', 'Lake', 'Port'];
    protected static $citySuffix = [
        'berg', 'borough', 'burgh', 'bury',
        'chester',
        'fort', 'furt',
        'haven',
        'land',
        'mouth', 'mouth',
        'port',
        'shire', 'side', 'stad',
        'ton', 'town',
        'view', 'ville',
    ];
    protected static $buildingNumber = ['%##', '%#', '%'];
    protected static $streetSuffix = [
        'Alley', 'Avenue',
        'Branch', 'Bridge', 'Brook', 'Brooks', 'Burg', 'Burgs', 'Bypass',
        'Camp', 'Canyon', 'Cape', 'Causeway', 'Center', 'Centers', 'Circle', 'Circles', 'Cliff', 'Cliffs', 'Club', 'Common', 'Corner', 'Corners', 'Course', 'Court', 'Courts', 'Cove', 'Coves', 'Creek', 'Crescent', 'Crest', 'Crossing', 'Crossroad', 'Curve',
        'Dale', 'Dam', 'Divide', 'Drive', 'Drive', 'Drives',
        'Estate', 'Estates', 'Expressway', 'Extension', 'Extensions',
        'Fall', 'Falls', 'Ferry', 'Field', 'Fields', 'Flat', 'Flats', 'Ford', 'Fords', 'Forest', 'Forge', 'Forges', 'Fork', 'Forks', 'Fort',
        'Garden', 'Gardens', 'Gateway', 'Glen', 'Glens', 'Green', 'Greens', 'Grove', 'Groves',
        'Harbour', 'Harbours', 'Haven', 'Heights', 'Highway', 'Hill', 'Hills', 'Hollow',
        'Inlet', 'Island', 'Islands', 'Isle',
        'Junction', 'Junctions',
        'Key', 'Keys', 'Knoll', 'Knolls',
        'Lake', 'Lakes', 'Land', 'Landing', 'Lane', 'Light', 'Lights', 'Loaf', 'Lock', 'Locks', 'Locks', 'Lodge', 'Lodge', 'Loop',
        'Manor', 'Manors', 'Meadow', 'Meadows', 'Mews', 'Mill', 'Mills', 'Motorway', 'Mount', 'Mountain', 'Mountains',
        'Neck',
        'Orchard', 'Oval', 'Overpass',
        'Park', 'Parks', 'Parkway', 'Parkways', 'Pass', 'Passage', 'Path', 'Pike', 'Pine', 'Pines', 'Place', 'Plain', 'Plains', 'Plaza', 'Point', 'Points', 'Port', 'Ports',
        'Radial', 'Ramp', 'Ranch', 'Rapid', 'Rapids', 'Rest', 'Ridge', 'Ridges', 'River', 'Road', 'Road', 'Roads', 'Roads', 'Route', 'Row', 'Rue', 'Run',
        'Shoal', 'Shoals', 'Shore', 'Shores', 'Spring', 'Springs', 'Springs', 'Spur', 'Spurs', 'Square', 'Square', 'Squares', 'Squares', 'Station', 'Station', 'Stream', 'Stream', 'Street', 'Streets', 'Summit',
        'Terrace', 'Throughway', 'Trace', 'Track', 'Trafficway', 'Trail', 'Tunnel', 'Turnpike',
        'Underpass', 'Union', 'Unions',
        'Valley', 'Valleys', 'Via', 'Viaduct', 'View', 'Views', 'Village', 'Villages', 'Ville', 'Vista', 'Vista',
        'Walk', 'Walks', 'Wall', 'Way', 'Ways', 'Well', 'Wells',
    ];

    protected static $postcode = [
        'DD8 1LR', 'HU7 4FE', 'SG6 1PR', 'DN40 3PT', 'PO21 3JR', 'BN13 3DN', 'B23 5RS', 'W5 5PA', 'ML12 6XR', 'WR2 4HQ', 'TR16 6EU', 'TF4 2NU',
        'HU8 8SH', 'TS24 7QA', 'SE11 5SD', 'WV99 1TL', 'TR16 5TN', 'NP16 6QT', 'BT60 3QU', 'TA24 6LB', 'TS23 1AD', 'W1G 9PN', 'WA1 4PJ', 'CM22 6DR',
        'BL7 8BW', 'FK2 8DB', 'LL65 4LN', 'LL21 0RG', 'SP6 1NH', 'EN1 4AE', 'WR5 1DG', 'NW6 9FA', 'NG18 3AZ', 'N14 5HW', 'CO9 3JZ', 'CW12 1BP',
        'BT41 2RX', 'CT6 9AR', 'ST10 4JQ', 'HD9 7ED', 'SW6 1LG', 'KY10 3RL', 'LA8 9RA', 'RH20 2LH', 'WF9 2JY', 'N11 1RH', 'BT78 2JU', 'ME6 5PD',
        'CB22 7PT', 'BS4 2BH', 'NR10 3QE', 'ST3 5SH', 'WA5 1QF', 'B17 0LW', 'WA7 1EN', 'SP3 6WD', 'LL48 6SE', 'MK3 7SA', 'CV34 4DF', 'CW1 5PG',
        'BN41 1PT', 'TS22 5AN', 'ST3 2HB', 'L25 8SJ', 'ST14 5JJ', 'L36 3RN', 'S2 3BQ', 'HR7 4AT', 'PH6 2HW', 'RH10 7RT', 'RM12 5EJ', 'IV2 5EG',
        'RH19 9GG', 'BD4 7HP', 'GL3 4TA', 'BS23 3YE', 'SN2 7TE', 'CB2 3EN', 'B27 6SF', 'AB53 4RJ', 'W4 3JU', 'SK14 1SB', 'PO14 4NH', 'CB9 9EE',
        'WF1 5HR', 'LL55 4TE', 'BH12 2EN', 'LE2 9HY', 'EX32 2BA', 'BS31 3HT', 'PE25 1AA', 'G61 3HA', 'EX12 2WH', 'OX4 1GF', 'DY2 9EU', 'CO1 1QR',
        'CH45 6UP', 'ME14 4NL', 'SE1P 5NT', 'LE3 9LB', 'BT66 7RR', 'BT5 5ED', 'NG17 1BH', 'GU1 2SB', 'SW15 4AF', 'AL5 1SZ', 'B63 4JQ', 'OL12 6RA',
        'RH12 1AS', 'TN6 2QU', 'CO12 3SQ', 'E1 4QJ', 'TW5 0XT', 'ST10 1JW', 'BL9 8LE', 'CH1 4EZ', 'SA71 5BP', 'HR9 9AJ', 'SA6 6DH', 'ML9 3BS',
        'TW12 2RA', 'EH26 0LE', 'E12 5QJ', 'M46 9XG', 'CO6 3EG', 'ST16 3AP', 'WN6 8BX', 'WR14 2YU', 'DG11 3JQ', 'TN14 5GD', 'TW15 3EQ', 'SW8 4TE',
        'DE55 5SF', 'DT6 9BF', 'TN31 7BY', 'SA19 9BR', 'HD9 4DH', 'IP21 4TN', 'CT14 7EW', 'DE65 5JX', 'B10 9JS', 'AL1 1SZ', 'CF39 0LH', 'SW20 8JY',
        'HP1 9HT', 'M44 6ZR', 'SW19 1BB', 'HP13 7TG', 'IP16 4UL', 'SE1 7DB', 'BN12 6HW', 'WF10 2AL', 'AL3 8RN', 'RG14 2EL', 'ME8 6QQ', 'W14 8AZ',
        'BT49 0NJ', 'WS11 1ZY', 'CR5 2DP', 'LS17 8LP', 'DL15 8GH', 'W1G 9PZ', 'CA6 5YS', 'WN2 3RS', 'L39 3LJ', 'BT47 2QQ', 'CT13 0PW', 'BL8 2ND',
        'RM9 4UT', 'GU7 9SD', 'WN3 6DQ', 'AB22 8ZW', 'LE11 9DA', 'EX16 6BS', 'DE65 6JG', 'GL55 6HT', 'BS24 7AH', 'LS26 8UF', 'PO6 4FH', 'CT5 3HQ',
        'CW11 5SY', 'MK16 0FL', 'RG19 8JZ', 'SP1 1NE', 'SG18 0HL', 'HA7 1HB', 'TW4 7JP', 'BT15 3FB', 'LN5 9WR', 'SK2 5XT', 'NP18 3TF', 'NE33 5SQ',
        'L40 4LA', 'LU7 4SW', 'WV99 1RG', 'EC3P 3AY', 'CW5 6DY', 'CR2 8EN', 'PO11 0JY', 'IP33 9GD', 'WA3 3UR', 'WD3 3LY', 'CT6 7HL', 'TN15 8JE',
        'L35 5JA', 'CF23 0EL', 'TR13 0DP', 'GL14 2NW', 'W1D 4PR', 'SY5 0AR', 'NP4 8LA', 'CH45 7RH', 'S35 4FX', 'PL20 6JB', 'NW1 6AB', 'AB41 7HB',
        'S72 7HG', 'RG27 8PG', 'TA1 3TF', 'FK3 8EP', 'MK43 7LX', 'BT79 7AQ', 'L9 9BL', 'PE28 5US', 'PO4 8NU', 'WF4 3QZ', 'SE23 3RG', 'NN5 7AR',
        'L15 6UE', 'CA4 9QG', 'RH9 8DR', 'AB11 5QE', 'L2 2TX', 'NE20 0RB', 'TF3 2BG', 'NW2 2SH', 'IG10 3JT', 'HR9 7GB', 'N10 3DS',
        'PA3 4NH', 'W8 7EY', 'HP19 9BW', 'KA1 3TU', 'SE26 6JG', 'SL3 9LU', 'L38 9EB', 'M15 6QL', 'BN6 8DA', 'PE27 5PP', 'LS16 8EE',
        'CM0 7HA', 'SY11 4LB', 'IG1 3TR', 'NE63 8EL', 'CR5 3DN', 'NW4 4XL', 'BL9 6QT', 'KT24 6NU', 'EH37 5TF', 'SO16 9RJ', 'PL28 8QJ',
        'E9 5LR', 'BR6 9XJ', 'M25 3BY', 'M20 1BT', 'SE18 7QX', 'DD1 2NF', 'NR31 8NS', 'BH31 6AF', 'TN23 5PR', 'TN12 9PU', 'HR8 2JJ', 'KT6 5DX',
        'HX3 0NS', 'SN7 8NR', 'SY7 8AQ', 'CV8 1LS', 'NR34 9ET', 'BD23 3EU', 'YO11 3JN', 'BH11 9NE', 'CM3 3AE', 'KA3 7PR', 'DE15 9DU', 'PR8 9LB',
        'GL53 7EN', 'OX15 4HW', 'TS19 9ES', 'G65 9BG', 'SE15 6FE', 'B37 7RA', 'BT51 3NQ', 'YO32 9SX', 'M50 3TU', 'LL14 5NR', 'PO35 5XS', 'W5 9TG',
        'BD24 0LF', 'KT22 7UE', 'GL1 2SZ', 'HP5 2ED', 'TN11 8HT', 'LA12 0HX', 'N5 1WP', 'TS10 3NS', 'B98 7JU', 'SY23 4LA', 'PR7 5PY', 'YO7 1SP',
        'HR9 7XU', 'ST3 1EQ', 'AL9 5DL', 'DT11 0JD', 'KT17 1DJ', 'HP6 5AY', 'NR8 5BD', 'PO37 6NN', 'YO31 8WU', 'CF48 2SR', 'BD23 1UY', 'HU12 8HG',
        'ML3 8PH', 'CO5 9BJ', 'BD9 4EF', 'G71 7PA', 'TF1 1HU', 'G74 3LB', 'CM16 6TT', 'BS8 4UR', 'B92 8HS', 'EH4 5LQ', 'GU1 2PB', 'FK8 1LD', 'S70 4DN',
        'BT93 6AB', 'RM15 4AP', 'HU14 3EB', 'CH63 4JT', 'M34 2JG', 'LU3 1HQ', 'TD1 2BX', 'PE23 4LZ', 'S66 8JW', 'RG8 0UN', 'YO31 0UQ', 'OX11 8JJ',
        'TS18 1NS', 'ME19 6QD', 'PL15 8US', 'SG5 3JJ', 'TN34 9GL', 'LL18 3RP', 'SK13 1LP', 'KA7 1TH', 'NG8 4EN', 'B68 8PE', 'EX14 1QF', 'RG14 2NL',
        'NG9 3FL', 'KA13 7NR', 'PR8 4RH', 'BB4 5TZ', 'SA5 4EA', 'TD8 6PQ', 'B44 8SR', 'GU16 6EL', 'AB31 5ZD', 'TA4 2EY', 'WR2 4RX', 'TF4 2JW',
        'RM14 3PA', 'DD3 8ES', 'CA28 6HA', 'IP22 1JW', 'S70 5RT', 'RM16 3EW', 'G77 6DL', 'TR15 1PH', 'DN2 5AU', 'ML11 9BQ', 'PE16 6RY', 'SW6 2WG',
        'PL7 4AJ', 'MK17 9QX', 'SL7 3PB', 'BL6 6YT', 'NG24 2PB', 'RG30 4AJ', 'DT7 3SY', 'YO21 1HX', 'BH8 8BP', 'DE11 0SD', 'S81 0HT', 'WD6 5QD',
        'BT25 2HD', 'CW5 6QF', 'S6 1WR', 'RM20 1WN', 'CF62 3HX', 'CB2 8HH', 'NE10 8JX', 'SL4 1YB', 'WV5 9BS', 'G83 0SH', 'M45 8AF', 'WR1 2HZ',
        'LU2 7LJ', 'SK6 7QN', 'TR10 8QN', 'HA3 7SF', 'LL12 9NR', 'G69 7EA', 'L25 2NW', 'PL2 2DD', 'DN15 9AU', 'HA1 2AG', 'LS2 9ND', 'HD9 6PH',
        'DH3 2NB', 'OL12 7TX', 'NG5 1HT', 'S64 0BU', 'EX20 1ER', 'RG40 4RX', 'B47 5EE', 'NE29 7BG', 'SM3 9QR', 'NG17 4JY', 'CF23 7EW', 'GU10 4HE',
        'NP44 4PE', 'MK9 2AD', 'S49 1YZ', 'PO8 0LJ', 'BD18 4LP', 'SW2 3DJ', 'SP4 6JS', 'OL16 3NA', 'IP7 5SS', 'PO17 5HZ', 'RH15 9TE', 'ME15 0JU',
        'LS25 2AY', 'BT46 5NR', 'YO19 6BL', 'M28 7XP', 'W6 7PR', 'NE29 0AT', 'TR19 6DX', 'LE3 0BF', 'BS6 9HE', 'SA18 1HW', 'DD8 4EX', 'BT35 7PB',
        'PE29 2HJ', 'LS1 9QA', 'BN14 7AR', 'BS5 8PJ', 'OX3 7PJ', 'W6 7AN', 'S60 2PT', 'G12 9BH', 'IP5 1LR', 'B26 3SX', 'PE21 8PT', 'RM14 2LD',
        'PL9 9NN', 'NG20 0JZ', 'W1K 7AF', 'AB31 4DX', 'PL1 4EH', 'IP19 0NS', 'LS28 9NF', 'CH64 3TH', 'G13 1XN', 'NG5 1JR', 'W10 6DY', 'BS27 3XD',
        'ST21 6SR', 'PL4 9RB', 'BA15 2QE', 'TR20 9RG', 'NG34 9HJ', 'BD22 9DN', 'LE4 4JR', 'KA9 2RN', 'W1G 6JQ', 'B14 5TF', 'SA16 0HY', 'N8 7AU',
        'LE17 5PD', 'PE25 2RA', 'SE19 2BG', 'OX12 8PJ', 'DY5 3EH', 'NG19 7QJ', 'G43 1HQ',
    ];

    protected static $county = [
        'Aberdeenshire', 'Anglesey', 'Angus', 'Argyll', 'Ayrshire', 'Banffshire', 'Bedfordshire', 'Berwickshire', 'Breconshire', 'Buckinghamshire', 'Bute', 'Caernarvonshire', 'Caithness', 'Cambridgeshire', 'Cardiganshire', 'Carmarthenshire', 'Cheshire', 'Clackmannanshire', 'Cornwall', 'Isles of Scilly', 'Cumbria', 'Denbighshire', 'Derbyshire', 'Devon', 'Dorset', 'Dumbartonshire', 'Dumfriesshire', 'Durham', 'East Lothian', 'East Sussex', 'Essex', 'Fife', 'Flintshire', 'Glamorgan', 'Gloucestershire', 'Greater London', 'Greater Manchester', 'Hampshire', 'Hertfordshire', 'Inverness', 'Kent', 'Kincardineshire', 'Kinross-shire', 'Kirkcudbrightshire', 'Lanarkshire', 'Lancashire', 'Leicestershire', 'Lincolnshire', 'London', 'Merionethshire', 'Merseyside', 'Midlothian', 'Monmouthshire', 'Montgomeryshire', 'Moray', 'Nairnshire', 'Norfolk', 'North Yorkshire', 'Northamptonshire', 'Northumberland', 'Nottinghamshire', 'Orkney', 'Oxfordshire', 'Peebleshire', 'Pembrokeshire', 'Perthshire', 'Radnorshire', 'Renfrewshire', 'Ross & Cromarty', 'Roxburghshire', 'Selkirkshire', 'Shetland', 'Shropshire', 'Somerset', 'South Yorkshire', 'Staffordshire', 'Stirlingshire', 'Suffolk', 'Surrey', 'Sutherland', 'Tyne and Wear', 'Warwickshire', 'West Lothian', 'West Midlands', 'West Sussex', 'West Yorkshire', 'Wigtownshire', 'Wiltshire', 'Worcestershire',
    ];

    protected static $country = [
        'Afghanistan', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Anguilla', 'Antarctica (the territory South of 60 deg S)', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan',
        'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Bouvet Island (Bouvetoya)', 'Brazil', 'British Indian Ocean Territory (Chagos Archipelago)', 'British Virgin Islands', 'Brunei Darussalam', 'Bulgaria', 'Burkina Faso', 'Burundi',
        'Cambodia', 'Cameroon', 'Canada', 'Cape Verde', 'Cayman Islands', 'Central African Republic', 'Chad', 'Chile', 'China', 'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Congo', 'Congo', 'Cook Islands', 'Costa Rica', 'Cote d\'Ivoire', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic',
        'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic',
        'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia',
        'Faroe Islands', 'Falkland Islands (Malvinas)', 'Fiji', 'Finland', 'France', 'French Guiana', 'French Polynesia', 'French Southern Territories',
        'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guadeloupe', 'Guam', 'Guatemala', 'Guernsey', 'Guinea', 'Guinea-Bissau', 'Guyana',
        'Haiti', 'Heard Island and McDonald Islands', 'Holy See (Vatican City State)', 'Honduras', 'Hong Kong', 'Hungary',
        'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Isle of Man', 'Israel', 'Italy',
        'Jamaica', 'Japan', 'Jersey', 'Jordan',
        'Kazakhstan', 'Kenya', 'Kiribati', 'Korea', 'Korea', 'Kuwait', 'Kyrgyz Republic',
        'Lao People\'s Democratic Republic', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libyan Arab Jamahiriya', 'Liechtenstein', 'Lithuania', 'Luxembourg',
        'Macao', 'Macedonia', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Martinique', 'Mauritania', 'Mauritius', 'Mayotte', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar',
        'Namibia', 'Nauru', 'Nepal', 'Netherlands Antilles', 'Netherlands', 'New Caledonia', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Norfolk Island', 'Northern Mariana Islands', 'Norway',
        'Oman',
        'Pakistan', 'Palau', 'Palestinian Territories', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Pitcairn Islands', 'Poland', 'Portugal', 'Puerto Rico',
        'Qatar',
        'Reunion', 'Romania', 'Russian Federation', 'Rwanda',
        'Saint Barthelemy', 'Saint Helena', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Martin', 'Saint Pierre and Miquelon', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia (Slovak Republic)', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Georgia and the South Sandwich Islands', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Svalbard & Jan Mayen Islands', 'Swaziland', 'Sweden', 'Switzerland', 'Syrian Arab Republic',
        'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tokelau', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks and Caicos Islands', 'Tuvalu',
        'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States of America', 'United States Minor Outlying Islands', 'United States Virgin Islands', 'Uruguay', 'Uzbekistan',
        'Vanuatu', 'Venezuela', 'Vietnam',
        'Wallis and Futuna', 'Western Sahara',
        'Yemen',
        'Zambia', 'Zimbabwe',
    ];

    protected static $cityFormats = [
        '{{cityPrefix}} {{firstName}}{{citySuffix}}',
        '{{cityPrefix}} {{firstName}}',
        '{{firstName}}{{citySuffix}}',
        '{{lastName}}{{citySuffix}}',
    ];
    protected static $streetNameFormats = [
        '{{firstName}} {{streetSuffix}}',
        '{{lastName}} {{streetSuffix}}',
    ];
    protected static $streetAddressFormats = [
        '{{buildingNumber}} {{streetName}}',
        '{{buildingNumber}} {{streetName}}',
        "{{secondaryAddress}}\n{{streetName}}",
    ];
    protected static $addressFormats = [
        "{{streetAddress}}\n{{city}}\n{{postcode}}",
    ];
    protected static $secondaryAddressFormats = ['Flat ##', 'Flat ##?', 'Studio ##', 'Studio ##?'];

    /**
     * @example 'East'
     */
    public static function cityPrefix()
    {
        return static::randomElement(static::$cityPrefix);
    }

    /**
     * @example 'Flat 350'
     */
    public static function secondaryAddress()
    {
        return static::bothify(static::randomElement(static::$secondaryAddressFormats));
    }

    /**
     * @example 'Hampshire'
     */
    public static function county()
    {
        return static::randomElement(static::$county);
    }

    /**
     * @example 'N6 5AA'
     */
    public static function postcode()
    {
        return static::randomElement(static::$postcode);
    }
}
