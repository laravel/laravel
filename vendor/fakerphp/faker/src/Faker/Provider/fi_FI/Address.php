<?php

namespace Faker\Provider\fi_FI;

class Address extends \Faker\Provider\Address
{
    protected static $cityPrefix = ['Pohjois', 'Etelä', 'Itä', 'Länsi', 'Uusi', 'Uus'];
    protected static $citySuffix = ['kylä', 'niemi', 'järvi', 'joki', 'lampi', 'mäki', 'vesi', 'niemi', 'harju', 'lahti', 'harju', 'salmi', 'koski', 'pudas', 'saari'];
    protected static $buildingNumber = ['%###', '%##', '%#', '%'];
    protected static $streetSuffix = [
        'tie', 'kuja', 'polku', 'kierros', 'kulma', 'katu', 'kaarre', 'kaari', 'rinne', 'kaarto', 'haka', 'silta', 'rinne', 'töyry',
    ];
    protected static $postcode = ['#####'];
    protected static $state = [
        'Ahvenanmaa', 'Etelä-Karjala', 'Etelä-Pohjanmaa', 'Etelä-Savo', 'Kainuu', 'Kanta-Häme', 'Keski-Pohjanmaa', 'Keski-Suomi', 'Kymenlaakso', 'Lappi', 'Pirkanmaa', 'Pohjanmaa', 'Pohjois-Karjala', 'Pohjois-Pohjanmaa', 'Pohjois-Savo', 'Päijät-Häme', 'Satakunta', 'Uusimaa', 'Varsinais-Suomi',
    ];
    protected static $country = [
        'Afganistan', 'Alankomaat', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua ja Barbuda', 'Argentiina', 'Armenia', 'Australia', 'Azerbaidẑan',
        'Bahama', 'Bahrain', 'Bangladesh', 'Barbados', 'Belgia', 'Belize', 'Benin', 'Bhutan', 'Bolivia', 'Bosnia ja Hertsegovina', 'Botswana', 'Brasilia', 'Brunel', 'Bulgaria', 'Burkina Faso', 'Burundi',
        'Chile', 'Costa Rica',
        'Djibouti', 'Dominica', 'Dominikaaninen tasavalta',
        'Ecuador', 'Egypti', 'El Salvador', 'Eritrea', 'Espanja', 'Etelä-Afrikka', 'Etelä-Korea', 'Etelä-Sudan', 'Etiopia', 'Fidẑi', 'Filippiinit',
        'Gabon', 'Gambia', 'Georgia', 'Ghana', 'Grenada', 'Guatemala', 'Guinea-Bissau', 'Guinea', 'Guyana',
        'Haiti', 'Honduras',
        'Indonesia', 'Intia', 'Irak', 'Iran', 'Irlanti', 'Islanti', 'Israel', 'Italia', 'Itä-Timor', 'Itävalta',
        'Jamaika', 'Japani', 'Jemen', 'Jordania',
        'Kambodẑa', 'Kamerun', 'Kanada', 'Kap Verde', 'Kazakstan', 'Kenia', 'Keski-Afrikan tasavalta', 'Kiina', 'Kirgisia', 'Kiribati', 'Kolumbia', 'Komorit', 'Kongon demokraattinen tasavalta', 'Kongon tasavalta', 'Kosovo', 'Kreikka', 'Kroatia', 'Kuuba', 'Kuwait', 'Kypros',
        'Laos', 'Latvia', 'Lesotho', 'Libanon', 'Liberai', 'Libya', 'Lichtenstein', 'Liettua', 'Luxemburg',
        'Madagaskar', 'Makedonia', 'Malawi', 'Malediivit', 'Malesia', 'Mali', 'Malta', 'Marokko', 'Marshallinsaaret', 'Mauritania', 'Mauritius', 'Meksiko', 'Mikronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Mosambik', 'Myanmar',
        'Namibia', 'Nauru', 'Nepal', 'Nicaragua', 'Nigeria', 'Niger', 'Norja', 'Norsunluurannikko',
        'Oman',
        'Pakistan', 'Palau', 'Panama', 'Papua-Uusi-Guinea', 'Paraguay', 'Peru', 'Pohjois-Korea', 'Portugali', 'Puola', 'Päiväntasaajan Guinea',
        'Qatar',
        'Ranska', 'Romania', 'Ruanda', 'Ruotsi',
        'Saint Kitts ja Nevis', 'Saint Lucia', 'Saint Vincent ja Grenadiinit', 'Saksa', 'Salomonsaaret', 'Sambia', 'Samoa', 'San Marino', 'São Tomé ja Príncipe', 'Saudi-Arabia', 'Senegal', 'Serbia', 'Seychellit', 'Sierra Leone', 'Singapore', 'Slovakia', 'Somalia', 'Sri Lanka', 'Sudan', 'Suomi', 'Suriname', 'Swazimaa', 'Sveitsi', 'Syyria',
        'Tadẑikistan', 'Tansania', 'Tanska', 'Thaimaa', 'Togo', 'Tonga', 'Trinidad ja Tobago', 'Tšad', 'Tšekki', 'Tunisia', 'Turkki', 'Turkmenistan', 'Tuvalu',
        'Uganda', 'Ukraina', 'Unkari', 'Uruguay', 'Uusi-Seelanti', 'Uzbekistan',
        'Valko-Venäjä', 'Vanuatu', 'Vatikaanivaltio', 'Venzuela', 'Venäjä', 'Vietnam', 'Viro',
        'Yhdistyneet Arabiemiirikunnat', 'Yhdistynyt kuningaskunta', 'Yhdysvallat',
        'Zimbabwe',
    ];
    protected static $cityFormats = [
        '{{cityPrefix}}-{{firstName}}{{citySuffix}}',
        '{{cityPrefix}}-{{firstName}}',
        '{{firstName}}{{citySuffix}}',
        '{{lastName}}{{citySuffix}}',
    ];
    protected static $streetNameFormats = [
        '{{firstName}}{{streetSuffix}}',
        '{{lastName}}{{streetSuffix}}',
    ];
    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}',
        '{{streetName}} {{buildingNumber}} {{secondaryAddress}}',
    ];
    protected static $addressFormats = [
        "{{streetAddress}}\n{{postcode}} {{city}}, {{state}}",
        "{{streetAddress}}\n{{postcode}} {{city}}",
    ];
    protected static $secondaryAddressFormats = ['###'];

    /**
     * @example 'Pohjois'
     */
    public static function cityPrefix()
    {
        return static::randomElement(static::$cityPrefix);
    }

    /**
     * @example '123'
     */
    public static function secondaryAddress()
    {
        return static::numerify(static::randomElement(static::$secondaryAddressFormats));
    }

    /**
     * @example 'Pohjois-Pohjanmaa'
     */
    public static function state()
    {
        return static::randomElement(static::$state);
    }
}
