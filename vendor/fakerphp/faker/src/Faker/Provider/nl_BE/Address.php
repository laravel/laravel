<?php

namespace Faker\Provider\nl_BE;

class Address extends \Faker\Provider\Address
{
    protected static $postcodes = [
        '2970', '3700', '7510', '9420', '8511', '3800', '9300', '9880', '3200', '8700', '8211', '2630', '4557',
        '4280', '3930', '5590', '5362', '4219', '6280', '9991', '8660', '1790', '9051', '5544', '4317', '5310',
        '6250', '5070', '3570', '5550', '4432', '1652', '8690', '4540', '6680', '6953', '4770', '6997', '7750',
        '5300', '1070', '6150', '4821', '4031', '7387', '5537', '6721', '6890', '4430', '5500', '5520', '4520',
        '4160', '7640', '2000', '2018', '2020', '2030', '2040', '2050', '2060', '2099', '7910', '8570', '9200',
        '9400', '5170', '7811', '4990', '1390', '8850', '2370', '4601', '6700', '7181', '5060', '6870', '3665',
        '9404', '9890', '7040', '1730', '8310', '1007', '9960', '6860', '3460', '5330', '9800', '7800', '6791',
        '3404', '3384', '6717', '7941', '6790', '7972', '4880', '5660', '6880', '7382', '6706', '1367', '5580',
        '8630', '8580', '4260', '3271', '4340', '4400', '6900', '4630', '4920', '3128', '9310', '2387', '4837',
        '6464', '6460', '5555', '7730', '5377', '7380', '1470', '5190', '9860', '2490', '6951', '6500', '4671',
        '5570', '7534', '5370', '6940', '7971', '4983', '4690', '9968', '7830', '6600', '3870', '4651', '7130',
        '7331', '7870', '7604', '1401', '9520', '8531', '9150', '4052', '6980', '1320', '6594', '7532', '3960',
    ];

    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}',
    ];

    protected static $streetNameFormats = ['{{lastName}}{{streetSuffix}}'];

    protected static $cityFormats = ['{{cityName}}'];

    protected static $addressFormats = [
        "{{streetAddress}}\n {{postcode}} {{city}}",
    ];

    protected static $streetSuffix = [
        'baan', 'boulevard', 'dreef', 'hof', 'laan', 'pad', 'ring', 'singel', 'steeg', 'straat', 'weg',
    ];

    /**
     * Export of BAG (http://bag.vrom.nl/)
     * last updated 2012/11/09
     *
     * @var array
     */
    protected static $cityNames = [
        'Aalst', 'Aarlen', 'Aarschot', 'Aat', 'Andenne', 'Antoing', 'Antwerpen', 'Bastenaken', 'Beringen',
        'Beaumont', 'Beauraing', 'Bergen', 'Bilzen', 'Binche', 'Blankenberge', 'Borgloon', 'Borgworm', 'Bouillon',
        'Bree', 'Brugge', 'Brussel', 'Charleroi', 'Châtelet', 'Chièvres', 'Chimay', 'Chiny', 'Ciney', 'Couvin',
        'Damme', 'Deinze', 'Dendermonde', 'Diest', 'Diksmuide', 'Dilsen-Stokkem', 'Dinant', 'Doornik', 'Durbuy',
        'Edingen', 'Eeklo', 'Eupen', 'Fleurus', 'Florenville', 'Fontaine-l\'Evêque', 'Fosses-la-Ville', 'Geel',
        'Geldenaken', 'Gembloers', 'Genepiën', 'Genk', 'Gent', 'Geraardsbergen', 'Gistel', '\'s-Gravenbrakel',
        'Halen', 'Halle', 'Hamont-Achel', 'Hannuit', 'Harelbeke', 'Hasselt', 'Herentals', 'Herk-de-Stad', 'Herstal',
        'Herve', 'Hoei', 'Hoogstraten', 'Houffalize', 'Ieper', 'Izegem', 'Komen-Waasten', 'Kortrijk', 'La Louvière',
        'La Roche-en-Ardenne', 'Landen', 'Le Rœulx', 'Lessen', 'Leuze-en-Hainaut', 'Leuven', 'Lier', 'Limburg',
        'Lo-Reninge', 'Lokeren', 'Lommel', 'Luik', 'Maaseik', 'Malmedy', 'Marche-en-Famenne', 'Mechelen', 'Menen',
        'Mesen', 'Moeskroen', 'Mortsel', 'Namen', 'Neufchâteau', 'Nieuwpoort', 'Nijvel', 'Ninove', 'Oostende',
        'Ottignies', 'Oudenaarde', 'Oudenburg', 'Peer', 'Péruwelz', 'Philippeville', 'Poperinge', 'Rochefort',
        'Roeselare', 'Ronse', 'Saint-Ghislain', 'Saint-Hubert', 'Sankt Vith', 'Scherpenheuvel-Zichem', 'Seraing',
        'Sint-Niklaas', 'Sint-Truiden', 'Spa', 'Stavelot', 'Thuin', 'Tielt', 'Tienen', 'Tongeren', 'Torhout',
        'Turnhout', 'Verviers', 'Veurne', 'Vilvoorde', 'Virton', 'Walcourt', 'Waregem', 'Waver', 'Wervik', 'Wezet',
        'Zinnik', 'Zottegem', 'Zoutleeuw',
    ];

    protected static $state = [
        'Antwerpen', 'Limburg', 'Oost-Vlaanderen', 'Vlaams-Brabant', 'West-Vlaanderen',
        'Henegouwen', 'Luik', 'Luxemburg', 'Namen', 'Waals-Brabant',
    ];

    protected static $country = [
        'Afghanistan', 'Albanië', 'Algerije', 'Amerikaans-Samoa', 'Andorra', 'Angola', 'Amerikaanse Virgineilanden',
        'Anguilla', 'Antartica', 'Antigua en Barbuda', 'Argentinië', 'Armenië', 'Aruba', 'Australië', 'Azerbeidzjan',
        'Bahamas', 'Bahrein', 'Bangladesh', 'Barbados', 'België', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia',
        'Bosnië-Herzegovina', 'Botswana', 'Bouvet Eiland (Bouvetoya)', 'Brazilië', 'Britse Maagdeneilanden',
        'Brunei Darussalam', 'Bulgarije', 'Burkina Faso', 'Burundi', 'Cambodja', 'Canada',
        'Centraal-Afrikaanse Republiek', 'Chili', 'China', 'Christmaseiland', 'Cocoseilanden', 'Colombia', 'Comoren',
        'Congo', 'Cookeilanden', 'Costa Rica', 'Cuba', 'Cyprus', 'Denemarken', 'Djibouti', 'Dominica',
        'Dominicaanse Republiek', 'Duitsland', 'Ecuador', 'Egypte', 'El salvador', 'Equatoriaal-Guinea', 'Eritrea',
        'Estland', 'Ethiopië', 'Faroe Eilanden', 'Falklandeilanden', 'Fiji', 'Finland', 'Frankrijk', 'Frans-Guyana',
        'Frans-Polynesië', 'Franse Zuidelijke en Antarctische Gebieden', 'Gabon', 'Gambia', 'Georgië', 'Ghana',
        'Gibraltar', 'Griekenland', 'Groenland', 'Grenada', 'Guadeloupe', 'Guam', 'Guatemala', 'Guernsey', 'Guinee',
        'Guinee-Bissau', 'Guyana', 'Haïti', 'Heard en McDonaldeilanden', 'Honduras', 'Hong Kong', 'Hongarije',
        'IJsland', 'India', 'Indonesië', 'Iran', 'Irak', 'Ierland', 'Man', 'Israel', 'Ivoorkust', 'Italië', 'Jamaica',
        'Japan', 'Jersey', 'Jordanië', 'Jemen', 'Kazachstan', 'Kenia',
        'Kleinere afgelegen eilanden van de Verenigde staten', 'Kiribati', 'Korea', 'Koeweit', 'Kirgizië', 'Kameroen',
        'Kaapverdië', 'Kaaimaneilanden', 'Kroatië', 'Laos', 'Letland', 'Libanon', 'Lesotho', 'Liberia', 'Libië',
        'Liechtenstein', 'Litouwen', 'Luxemburg', 'Macau', 'Macedonië', 'Madagascar', 'Malawi', 'Maleisië', 'Maldiven',
        'Mali', 'Malta', 'Marshalleilanden', 'Martinique', 'Mauritus', 'Mauritania', 'Mayotte', 'Mexico', 'Micronesië',
        'Moldavië', 'Monaco', 'Mongolië', 'Montenegro', 'Monsterrat', 'Marokko', 'Mozambique', 'Myanmar', 'Namibië',
        'Nauru', 'Nepal', 'Nederlandse Antillen', 'Nederland', 'Nieuw-Caledonië', 'Nieuw-Zeeland', 'Nicaragua',
        'Niger', 'Nigeria', 'Niue', 'Norfolk', 'Noordelijke Marianen', 'Noorwegen', 'Oman', 'Oostenrijk', 'Oeganda',
        'Oekraïne', 'Oezbakistan', 'Pakistan', 'Palau', 'Palestina', 'Panama', 'Papoea-Nieuw-Guinea', 'Paraguay',
        'Peru', 'Filipijnen', 'Pitcairneilanden', 'Polen', 'Portugal', 'Puerto Rico', 'Qatar', 'Réunion', 'Roemenië',
        'Rusland', 'Rwanda', 'Rwanda', 'Sint-Bartholomeus', 'Sint-Helena', 'Saint Kitts en Nevis', 'Saint Lucia',
        'Sint Maarten', 'Saint-Pierre en Miquelon', 'Saint Vincent en de Grenadines', 'Samoa', 'San Marino',
        'Sao Toma en Principe', 'Saoedi-Arabië', 'Senegal', 'Servië', 'Seychellen', 'Sierra Leone', 'Singapore',
        'Slovenië', 'Salomonseilanden', 'Somalië', 'Spanje', 'Sri Lanka', 'Soedan', 'Suriname',
        'Spitsbergen en Jan Mayen', 'Swaziland', 'Zweden', 'Zwitserland', 'Syrië', 'Taiwan', 'Tadzjikistan',
        'Tanzania', 'Thailand', 'Tsjaad', 'Timor-Leste', 'Togo', 'Tokelau-eilanden', 'Tonga', 'Trinidad en Trobago',
        'Tunesië', 'Turkije', 'Turkmenistan', 'Turks- en Caicoseilanden', 'Tuvalu', 'Tsjechische Republiek',
        'Uruguay', 'Vanuatu', 'Venezuela', 'Verenigde Arabische Emiraten', 'Verenigd Koninkrijk',
        'Verenigde Staten van Amerika', 'Vaticaanstad', 'Vietnam', 'Wallis en Futuna', 'Westerlijke Shara',
        'Wit-Rusland', 'Zambia', 'Zuid-Afrika', 'Zuid-Georgia en de Zuidelijke Sandwicheilanden', 'Zimbabwe',
    ];

    public static function postcode()
    {
        return static::randomElement(static::$postcodes);
    }

    /**
     * @example 'Gelderland'
     */
    public static function state()
    {
        return static::randomElement(static::$state);
    }

    /**
     * @see parent
     */
    public function cityName()
    {
        return static::randomElement(static::$cityNames);
    }
}
