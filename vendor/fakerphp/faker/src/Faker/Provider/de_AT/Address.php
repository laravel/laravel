<?php

namespace Faker\Provider\de_AT;

class Address extends \Faker\Provider\Address
{
    protected static $buildingNumber = ['%##', '%#', '%', '##[abc]', '#[abc]'];

    protected static $streetSuffixLong = [
        'Gasse', 'Platz', 'Ring', 'Straße', 'Weg',
    ];
    protected static $streetSuffixShort = [
        'gasse', 'platz', 'ring', 'straße', 'weg',
    ];

    /**
     * @var string[]
     *
     * @see http://www.statistik.at/verzeichnis/reglisten/gemliste_knz.xls - postal codes of all Austrian cities with the status 'Statutarstadt (SR)' or 'Stadtgemeinde (ST)'
     */
    protected static $postcode = [
        '1010', '1020', '1030', '1040', '1050', '1060', '1070', '1080', '1090', '1100', '1110', '1120', '1130', '1140',
        '1150', '1160', '1170', '1180', '1190', '1200', '1210', '1220', '1230', '2000', '2020', '2070', '2073', '2083',
        '2093', '2095', '2100', '2120', '2130', '2136', '2170', '2201', '2225', '2230', '2232', '2293', '2301', '2320',
        '2340', '2401', '2410', '2452', '2460', '2483', '2490', '2491', '2500', '2514', '2540', '2560', '2620', '2630',
        '2640', '2700', '2860', '3002', '3021', '3040', '3100', '3130', '3133', '3150', '3170', '3180', '3240', '3250',
        '3270', '3300', '3340', '3350', '3370', '3380', '3390', '3400', '3430', '3500', '3512', '3542', '3550', '3580',
        '3601', '3712', '3730', '3741', '3804', '3812', '3820', '3830', '3860', '3874', '3910', '3920', '3943', '3950',
        '3970', '4020', '4050', '4053', '4060', '4070', '4150', '4190', '4210', '4221', '4230', '4240', '4300', '4320',
        '4360', '4400', '4470', '4540', '4560', '4600', '4614', '4663', '4690', '4710', '4722', '4780', '4800', '4810',
        '4820', '4840', '4910', '4950', '5020', '5110', '5201', '5202', '5230', '5280', '5400', '5500', '5550', '5600',
        '5700', '5730', '5760', '6020', '6060', '6130', '6240', '6300', '6330', '6370', '6460', '6500', '6682', '6700',
        '6800', '6845', '6850', '6900', '7000', '7071', '7083', '7100', '7132', '7210', '7350', '7400', '7423', '7461',
        '7540', '8010', '8130', '8160', '8200', '8230', '8240', '8280', '8330', '8350', '8380', '8430', '8480', '8490',
        '8530', '8570', '8572', '8580', '8600', '8605', '8630', '8650', '8680', '8700', '8720', '8724', '8740', '8750',
        '8784', '8786', '8790', '8793', '8832', '8850', '8940', '8970', '8990', '9020', '9100', '9150', '9170', '9300',
        '9330', '9341', '9360', '9400', '9433', '9462', '9500', '9545', '9560', '9620', '9800', '9853', '9900',
    ];

    /**
     * @var array
     *
     * @see https://de.wikipedia.org/wiki/Liste_der_St%C3%A4dte_in_%C3%96sterreich
     */
    protected static $cityNames = [
        'Allentsteig', 'Altheim', 'Althofen', 'Amstetten', 'Ansfelden', 'Attnang-Puchheim',
        'Bad Aussee', 'Bad Hall', 'Bad Ischl', 'Bad Leonfelden', 'Bad Radkersburg', 'Bad St. Leonhard im Lavanttal', 'Bad Vöslau', 'Baden', 'Bärnbach', 'Berndorf', 'Bischofshofen', 'Bleiburg', 'Bludenz', 'Braunau am Inn', 'Bregenz', 'Bruck an der Leitha', 'Bruck an der Mur',
        'Deutsch-Wagram', 'Deutschlandsberg', 'Dornbirn', 'Drosendorf-Zissersdorf', 'Dürnstein',
        'Ebenfurth', 'Ebreichsdorf', 'Eferding', 'Eggenburg', 'Eisenerz', 'Eisenstadt', 'Enns',
        'Fehring', 'Feldbach', 'Feldkirch', 'Feldkirchen in Kärnten', 'Ferlach', 'Fischamend', 'Frauenkirchen', 'Freistadt', 'Friedberg', 'Friesach', 'Frohnleiten', 'Fürstenfeld',
        'Gallneukirchen', 'Gänserndorf', 'Geras', 'Gerasdorf bei Wien', 'Gföhl', 'Gleisdorf', 'Gloggnitz', 'Gmünd', 'Gmünd in Kärnten', 'Gmunden', 'Graz', 'Grein', 'Grieskirchen', 'Groß-Enzersdorf', 'Groß Gerungs', 'Groß-Siegharts', 'Güssing',
        'Haag', 'Hainburg an der Donau', 'Hainfeld', 'Hall in Tirol', 'Hallein', 'Hardegg', 'Hartberg', 'Heidenreichstein', 'Hermagor-Pressegger See', 'Herzogenburg', 'Hohenems', 'Hollabrunn', 'Horn',
        'Imst', 'Innsbruck',
        'Jennersdorf', 'Judenburg',
        'Kapfenberg', 'Kindberg', 'Kirchdorf an der Krems', 'Kirchschlag in der Buckligen Welt', 'Kitzbühel', 'Klagenfurt am Wörthersee', 'Klosterneuburg', 'Knittelfeld', 'Köflach', 'Korneuburg', 'Krems an der Donau', 'Kufstein',
        'Laa an der Thaya', 'Laakirchen', 'Landeck', 'Langenlois', 'Leibnitz', 'Leoben', 'Leonding', 'Lienz', 'Liezen', 'Lilienfeld', 'Linz', 'Litschau',
        'Maissau', 'Mank', 'Mannersdorf am Leithagebirge', 'Marchegg', 'Marchtrenk', 'Mariazell', 'Mattersburg', 'Mattighofen', 'Mautern an der Donau', 'Melk', 'Mittersill', 'Mistelbach', 'Mödling', 'Murau', 'Mureck', 'Mürzzuschlag',
        'Neufeld an der Leitha', 'Neulengbach', 'Neumarkt am Wallersee', 'Neunkirchen', 'Neusiedl am See',
        'Oberndorf bei Salzburg', 'Oberpullendorf', 'Oberwart', 'Oberwölz',
        'Perg', 'Peuerbach', 'Pinkafeld', 'Pöchlarn', 'Poysdorf', 'Pregarten', 'Pressbaum', 'Pulkau', 'Purbach am Neusiedler See', 'Purkersdorf',
        'Raabs an der Thaya', 'Radenthein', 'Radstadt', 'Rattenberg', 'Retz', 'Ried im Innkreis', 'Rohrbach-Berg', 'Rottenmann', 'Rust',
        'Saalfelden am Steinernen Meer', 'Salzburg', 'Sankt Andrä', 'St. Johann im Pongau', 'St. Pölten', 'St. Valentin', 'Sankt Veit an der Glan', 'Schärding', 'Scheibbs', 'Schladming', 'Schrattenthal', 'Schrems', 'Schwanenstadt', 'Schwaz', 'Schwechat', 'Seekirchen am Wallersee', 'Spielberg', 'Spittal an der Drau', 'Stadtschlaining', 'Steyr', 'Steyregg', 'Stockerau', 'Straßburg',
        'Ternitz', 'Traiskirchen', 'Traismauer', 'Traun', 'Trieben', 'Trofaiach', 'Tulln an der Donau',
        'Villach', 'Vils', 'Vöcklabruck', 'Voitsberg', 'Völkermarkt',
        'Waidhofen an der Thaya', 'Waidhofen an der Ybbs', 'Weitra', 'Weiz', 'Wels', 'Wien', 'Wiener Neustadt', 'Wieselburg', 'Wilhelmsburg', 'Wolfsberg', 'Wolkersdorf im Weinviertel', 'Wörgl',
        'Ybbs an der Donau',
        'Zell am See', 'Zeltweg', 'Zistersdorf', 'Zwettl',
    ];

    protected static $state = [
        'Burgenland', 'Kärnten', 'Niederösterreich', 'Oberösterreich', 'Salzburg', 'Steiermark', 'Tirol', 'Vorarlberg', 'Wien',
    ];

    protected static $country = [
        'Afghanistan', 'Alandinseln', 'Albanien', 'Algerien', 'Amerikanisch-Ozeanien', 'Amerikanisch-Samoa', 'Amerikanische Jungferninseln', 'Andorra', 'Angola', 'Anguilla', 'Antarktis', 'Antigua und Barbuda', 'Argentinien', 'Armenien', 'Aruba', 'Aserbaidschan', 'Australien', 'Ägypten', 'Äquatorialguinea', 'Äthiopien', 'Äußeres Ozeanien',
        'Bahamas', 'Bahrain', 'Bangladesch', 'Barbados', 'Belarus', 'Belgien', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivien', 'Bosnien und Herzegowina', 'Botsuana', 'Bouvetinsel', 'Brasilien', 'Britische Jungferninseln', 'Britisches Territorium im Indischen Ozean', 'Brunei Darussalam', 'Bulgarien', 'Burkina Faso', 'Burundi',
        'Chile', 'China', 'Cookinseln', 'Costa Rica', 'Côte d’Ivoire',
        'Demokratische Republik Kongo', 'Demokratische Volksrepublik Korea', 'Deutschland', 'Dominica', 'Dominikanische Republik', 'Dschibuti', 'Dänemark',
        'Ecuador', 'El Salvador', 'Eritrea', 'Estland', 'Europäische Union',
        'Falklandinseln', 'Fidschi', 'Finnland', 'Frankreich', 'Französisch-Guayana', 'Französisch-Polynesien', 'Französische Süd- und Antarktisgebiete', 'Färöer',
        'Gabun', 'Gambia', 'Georgien', 'Ghana', 'Gibraltar', 'Grenada', 'Griechenland', 'Grönland', 'Guadeloupe', 'Guam', 'Guatemala', 'Guernsey', 'Guinea', 'Guinea-Bissau', 'Guyana',
        'Haiti', 'Heard- und McDonald-Inseln', 'Honduras',
        'Indien', 'Indonesien', 'Irak', 'Iran', 'Irland', 'Island', 'Isle of Man', 'Israel', 'Italien',
        'Jamaika', 'Japan', 'Jemen', 'Jersey', 'Jordanien',
        'Kaimaninseln', 'Kambodscha', 'Kamerun', 'Kanada', 'Kap Verde', 'Kasachstan', 'Katar', 'Kenia', 'Kirgisistan', 'Kiribati', 'Kokosinseln', 'Kolumbien', 'Komoren', 'Kongo', 'Kroatien', 'Kuba', 'Kuwait',
        'Laos', 'Lesotho', 'Lettland', 'Libanon', 'Liberia', 'Libyen', 'Liechtenstein', 'Litauen', 'Luxemburg',
        'Madagaskar', 'Malawi', 'Malaysia', 'Malediven', 'Mali', 'Malta', 'Marokko', 'Marshallinseln', 'Martinique', 'Mauretanien', 'Mauritius', 'Mayotte', 'Mazedonien', 'Mexiko', 'Mikronesien', 'Monaco', 'Mongolei', 'Montenegro', 'Montserrat', 'Mosambik', 'Myanmar',
        'Namibia', 'Nauru', 'Nepal', 'Neukaledonien', 'Neuseeland', 'Nicaragua', 'Niederlande', 'Niederländische Antillen', 'Niger', 'Nigeria', 'Niue', 'Norfolkinsel', 'Norwegen', 'Nördliche Marianen',
        'Oman', 'Osttimor', 'Österreich',
        'Pakistan', 'Palau', 'Palästinensische Gebiete', 'Panama', 'Papua-Neuguinea', 'Paraguay', 'Peru', 'Philippinen', 'Pitcairn', 'Polen', 'Portugal', 'Puerto Rico',
        'Republik Korea', 'Republik Moldau', 'Ruanda', 'Rumänien', 'Russische Föderation', 'Réunion',
        'Salomonen', 'Sambia', 'Samoa', 'San Marino', 'Saudi-Arabien', 'Schweden', 'Schweiz', 'Senegal', 'Serbien', 'Serbien und Montenegro', 'Seychellen', 'Sierra Leone', 'Simbabwe', 'Singapur', 'Slowakei', 'Slowenien', 'Somalia', 'Sonderverwaltungszone Hongkong', 'Sonderverwaltungszone Macao', 'Spanien', 'Sri Lanka', 'St. Barthélemy', 'St. Helena', 'St. Kitts und Nevis', 'St. Lucia', 'St. Martin', 'St. Pierre und Miquelon', 'St. Vincent und die Grenadinen', 'Sudan', 'Suriname', 'Svalbard und Jan Mayen', 'Swasiland', 'Syrien', 'São Tomé und Príncipe', 'Südafrika', 'Südgeorgien und die Südlichen Sandwichinseln',
        'Tadschikistan', 'Taiwan', 'Tansania', 'Thailand', 'Togo', 'Tokelau', 'Tonga', 'Trinidad und Tobago', 'Tschad', 'Tschechische Republik', 'Tunesien', 'Turkmenistan', 'Turks- und Caicosinseln', 'Tuvalu', 'Türkei',
        'Uganda', 'Ukraine', 'Unbekannte oder ungültige Region', 'Ungarn', 'Uruguay', 'Usbekistan',
        'Vanuatu', 'Vatikanstadt', 'Venezuela', 'Vereinigte Arabische Emirate', 'Vereinigte Staaten', 'Vereinigtes Königreich', 'Vietnam',
        'Wallis und Futuna', 'Weihnachtsinsel', 'Westsahara',
        'Zentralafrikanische Republik', 'Zypern',
    ];

    protected static $cityFormats = [
        '{{cityName}}',
    ];

    protected static $streetNameFormats = [
        '{{lastName}}{{streetSuffixShort}}',
        '{{firstName}}-{{lastName}}-{{streetSuffixLong}}',
    ];

    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}',
    ];
    protected static $addressFormats = [
        "{{streetAddress}}\n{{postcode}} {{city}}",
    ];

    public function cityName()
    {
        return static::randomElement(static::$cityNames);
    }

    public function streetSuffixShort()
    {
        return static::randomElement(static::$streetSuffixShort);
    }

    public function streetSuffixLong()
    {
        return static::randomElement(static::$streetSuffixLong);
    }

    /**
     * @example 'Wien'
     */
    public static function state()
    {
        return static::randomElement(static::$state);
    }

    public static function buildingNumber()
    {
        return static::regexify(self::numerify(static::randomElement(static::$buildingNumber)));
    }
}
