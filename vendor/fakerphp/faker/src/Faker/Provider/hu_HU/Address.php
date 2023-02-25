<?php

namespace Faker\Provider\hu_HU;

class Address extends \Faker\Provider\Address
{
    protected static $cityFormats = [
        '{{capital}}',
        '{{capital}}',
        '{{capital}}',
        '{{bigCity}}',
        '{{bigCity}}',
        '{{smallerCity}}',
    ];
    protected static $streetNameFormats = [
        '{{firstName}} {{streetSuffix}}',
        '{{lastName}} {{streetSuffix}}',
    ];
    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}.',
        '{{streetName}} {{buildingNumber}}. {{secondaryAddress}}',
    ];
    protected static $addressFormats = [
        '{{postcode}} {{city}}, {{streetAddress}}',
    ];
    protected static $secondaryAddressFormats = ['##. emelet', '##. ajtó'];

    /**
     * @example '10. emelet'
     */
    public static function secondaryAddress()
    {
        return static::numerify(static::randomElement(static::$secondaryAddressFormats));
    }

    /**
     * @example 'Pest'
     */
    public static function state()
    {
        return static::randomElement(static::$state);
    }

    /**
     * @example 'Budapest'
     */
    public static function capital()
    {
        return static::randomElement(static::$capitals);
    }

    /**
     * @example 'Pécs'
     */
    public static function bigCity()
    {
        return static::randomElement(static::$bigCities);
    }

    /**
     * @example 'Várpalota'
     */
    public static function smallerCity()
    {
        return static::randomElement(static::$smallerCities);
    }

    protected static $buildingNumber = ['%##', '%#', '%#', '%'];

    /**
     * Coordinates inside the border of Hungary
     *
     * @example array('47.049242', '18.355119')
     *
     * @return array | latitude, longitude
     */
    public static function localCoordinates()
    {
        return [
            'latitude' => static::latitude(46.262740, 47.564721),
            'longitude' => static::longitude(17.077949, 20.604560),
        ];
    }

    protected static $streetSuffix = [
        'árok', 'átjáró', 'dűlősor', 'dűlőút', 'erdősor', 'fasor', 'forduló', 'gát', 'határsor', 'határút', 'híd', 'játszótér', 'kert', 'körönd', 'körtér', 'körút', 'köz', 'lakótelep', 'lejáró', 'lejtő', 'lépcső', 'liget', 'mélyút', 'orom', 'országút', 'ösvény', 'park', 'part', 'pincesor', 'rakpart', 'sétány', 'sétaút', 'sor', 'sugárút', 'tér', 'tere', 'turistaút', 'udvar', 'út', 'útja', 'utca', 'üdülőpart',
    ];
    protected static $postcode = ['####'];
    protected static $state = [
        'Budapest', 'Bács-Kiskun', 'Baranya', 'Békés', 'Borsod-Abaúj-Zemplén', 'Csongrád', 'Fejér', 'Győr-Moson-Sopron', 'Hajdú-Bihar', 'Heves', 'Jász-Nagykun-Szolnok', 'Komárom-Esztergom', 'Nógrád', 'Pest', 'Somogy', 'Szabolcs-Szatmár-Bereg', 'Tolna', 'Vas', 'Veszprém', 'Zala',
    ];
    protected static $country = [
        'Afganisztán', 'Albánia', 'Algéria', 'Amerikai Egyesült Államok', 'Andorra', 'Angola', 'Antigua és Barbuda', 'Argentína', 'Ausztria', 'Ausztrália', 'Azerbajdzsán',
        'Bahama-szigetek', 'Bahrein', 'Banglades', 'Barbados', 'Belgium', 'Belize', 'Benin', 'Bhután', 'Bolívia', 'Bosznia-Hercegovina', 'Botswana', 'Brazília', 'Brunei', 'Bulgária', 'Burkina Faso', 'Burma', 'Burundi',
        'Chile', 'Ciprus', 'Costa Rica', 'Csehország', 'Csád',
        'Dominikai Köztársaság', 'Dominikai Közösség', 'Dzsibuti', 'Dánia', 'Dél-Afrika', 'Dél-Korea', 'Dél-Szudán',
        'Ecuador', 'Egyenlítői-Guinea', 'Egyesült Arab Emírségek', 'Egyesült Királyság', 'Egyiptom', 'Elefántcsontpart', 'Eritrea', 'Etiópia',
        'Fehéroroszország', 'Fidzsi-szigetek', 'Finnország', 'Franciaország', 'Fülöp-szigetek',
        'Gabon', 'Gambia', 'Ghána', 'Grenada', 'Grúzia', 'Guatemala', 'Guinea', 'Guyana', 'Görögország',
        'Haiti', 'Hollandia', 'Horvátország',
        'India', 'Indonézia', 'Irak', 'Irán', 'Izland', 'Izrael',
        'Japán', 'Jemen', 'Jordánia',
        'Kambodzsa', 'Kamerun', 'Kanada', 'Katar', 'Kazahsztán', 'Kelet-Timor', 'Kenya', 'Kirgizisztán', 'Kiribati', 'Kolumbia', 'Kongói Demokratikus Köztársaság', 'Kongói Köztársaság', 'Kuba', 'Kuvait', 'Kína', 'Közép-Afrika',
        'Laosz', 'Lengyelország', 'Lesotho', 'Lettország', 'Libanon', 'Libéria', 'Liechtenstein', 'Litvánia', 'Luxemburg', 'Líbia',
        'Macedónia', 'Madagaszkár', 'Magyarország', 'Malawi', 'Maldív-szigetek', 'Mali', 'Malájzia', 'Marokkó', 'Marshall-szigetek', 'Mauritánia', 'Mexikó', 'Mikronézia', 'Moldova', 'Monaco', 'Mongólia', 'Montenegró', 'Mozambik', 'Málta',
        'Namíbia', 'Nauru', 'Nepál', 'Nicaragua', 'Niger', 'Nigéria', 'Norvégia', 'Németország',
        'Olaszország', 'Omán', 'Oroszország',
        'Pakisztán', 'Palau', 'Panama', 'Paraguay', 'Peru', 'Portugália', 'Pápua Új-Guinea',
        'Románia', 'Ruanda',
        'Saint Kitts és Nevis', 'Saint Vincent', 'Salamon-szigetek', 'Salvador', 'San Marino', 'Seychelle-szigetek', 'Spanyolország', 'Srí Lanka', 'Suriname', 'Svájc', 'Svédország', 'Szamoa', 'Szaúd-Arábia', 'Szenegál', 'Szerbia', 'Szingapúr', 'Szlovákia', 'Szlovénia', 'Szomália', 'Szudán', 'Szváziföld', 'Szíria', 'São Tomé és Príncipe',
        'Tadzsikisztán', 'Tanzánia', 'Thaiföld', 'Togo', 'Tonga', 'Trinidad és Tobago', 'Tunézia', 'Tuvalu', 'Törökország', 'Türkmenisztán',
        'Uganda', 'Ukrajna', 'Uruguay',
        'Vanuatu', 'Venezuela', 'Vietnám',
        'Zambia', 'Zimbabwe', 'Zöld-foki-szigetek',
        'Észak-Korea', 'Észtország', 'Írország', 'Örményország', 'Új-Zéland', 'Üzbegisztán',
    ];

    /**
     * Source: https://hu.wikipedia.org/wiki/Magyarorsz%C3%A1g_v%C3%A1rosainak_list%C3%A1ja
     */
    protected static $capitals = ['Budapest'];
    protected static $bigCities = [
        'Békéscsaba', 'Debrecen', 'Dunaújváros', 'Eger', 'Érd', 'Győr', 'Hódmezővásárhely', 'Kaposvár', 'Kecskemét', 'Miskolc', 'Nagykanizsa', 'Nyíregyháza', 'Pécs', 'Salgótarján', 'Sopron', 'Szeged', 'Székesfehérvár', 'Szekszárd', 'Szolnok', 'Szombathely', 'Tatabánya', 'Veszprém', 'Zalaegerszeg',
    ];
    protected static $smallerCities = [
        'Ajka', 'Aszód', 'Bácsalmás',
        'Baja', 'Baktalórántháza', 'Balassagyarmat', 'Balatonalmádi', 'Balatonfüred', 'Balmazújváros', 'Barcs', 'Bátonyterenye', 'Békés', 'Bélapátfalva', 'Berettyóújfalu', 'Bicske', 'Bóly', 'Bonyhád', 'Budakeszi',
        'Cegléd', 'Celldömölk', 'Cigánd', 'Csenger', 'Csongrád', 'Csorna', 'Csurgó',
        'Dabas', 'Derecske', 'Devecser', 'Dombóvár', 'Dunakeszi',
        'Edelény', 'Encs', 'Enying', 'Esztergom',
        'Fehérgyarmat', 'Fonyód', 'Füzesabony',
        'Gárdony', 'Gödöllő', 'Gönc', 'Gyál', 'Gyomaendrőd', 'Gyöngyös', 'Gyula',
        'Hajdúböszörmény', 'Hajdúhadház', 'Hajdúnánás', 'Hajdúszoboszló', 'Hatvan', 'Heves',
        'Ibrány',
        'Jánoshalma', 'Jászapáti', 'Jászberény',
        'Kalocsa', 'Kapuvár', 'Karcag', 'Kazincbarcika', 'Kemecse', 'Keszthely', 'Kisbér', 'Kiskőrös', 'Kiskunfélegyháza', 'Kiskunhalas', 'Kiskunmajsa', 'Kistelek', 'Kisvárda', 'Komárom', 'Komló', 'Körmend', 'Kőszeg', 'Kunhegyes', 'Kunszentmárton', 'Kunszentmiklós',
        'Lenti', 'Letenye',
        'Makó', 'Marcali', 'Martonvásár', 'Mátészalka', 'Mezőcsát', 'Mezőkovácsháza', 'Mezőkövesd', 'Mezőtúr', 'Mohács', 'Monor', 'Mór', 'Mórahalom', 'Mosonmagyaróvár',
        'Nagyatád', 'Nagykálló', 'Nagykáta', 'Nagykőrös', 'Nyíradony', 'Nyírbátor',
        'Orosháza', 'Oroszlány', 'Ózd',
        'Paks', 'Pannonhalma', 'Pápa', 'Pásztó', 'Pécsvárad', 'Pétervására', 'Pilisvörösvár', 'Polgárdi', 'Püspökladány', 'Putnok',
        'Ráckeve', 'Rétság',
        'Sárbogárd', 'Sarkad', 'Sárospatak', 'Sárvár', 'Sásd', 'Sátoraljaújhely', 'Sellye', 'Siklós', 'Siófok', 'Sümeg', 'Szarvas', 'Szécsény', 'Szeghalom', 'Szentendre', 'Szentes', 'Szentgotthárd', 'Szentlőrinc', 'Szerencs', 'Szigetszentmiklós', 'Szigetvár', 'Szikszó', 'Szob',
        'Tab', 'Tamási', 'Tapolca', 'Tata', 'Tét', 'Tiszafüred', 'Tiszakécske', 'Tiszaújváros', 'Tiszavasvári', 'Tokaj', 'Tolna', 'Törökszentmiklós',
        'Vác', 'Várpalota', 'Vásárosnamény', 'Vasvár', 'Vecsés',
        'Záhony', 'Zalaszentgrót', 'Zirc',
    ];
}
