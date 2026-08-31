<?php

namespace Faker\Provider\nb_NO;

class Address extends \Faker\Provider\Address
{
    protected static $buildingNumber = ['%###', '%##', '%#', '%#?', '%', '%?'];

    protected static $streetPrefix = [
        'Øvre', 'Nedre', 'Søndre', 'Gamle', 'Østre', 'Vestre',
    ];

    protected static $streetSuffix = [
        'alléen', 'bakken', 'berget', 'bråten', 'eggen', 'engen', 'ekra', 'faret', 'flata', 'gata', 'gjerdet', 'grenda',
        'gropa', 'hagen', 'haugen', 'havna', 'holtet', 'høgda', 'jordet', 'kollen', 'kroken', 'lia', 'lunden', 'lyngen',
        'løkka', 'marka', 'moen', 'myra', 'plassen', 'ringen', 'roa', 'røa', 'skogen', 'skrenten', 'spranget', 'stien',
        'stranda', 'stubben', 'stykket', 'svingen', 'tjernet', 'toppen', 'tunet', 'vollen', 'vika', 'åsen',
    ];

    protected static $streetSuffixWord = [
        'sgate', 'svei', 's Gate', 's Vei', 'gata', 'veien',
    ];

    protected static $postcode = ['####', '####', '####', '0###'];

    /**
     * @var array Norwegian city names
     *
     * @see https://no.wikipedia.org/wiki/Liste_over_norske_byer
     */
    protected static $cityNames = [
        'Alta', 'Arendal', 'Askim', 'Bergen', 'Bodø', 'Brekstad', 'Brevik', 'Brumunddal', 'Bryne', 'Brønnøysund',
        'Drammen', 'Drøbak', 'Egersund', 'Elverum', 'Fagernes', 'Farsund', 'Fauske', 'Finnsnes', 'Flekkefjord', 'Florø',
        'Fosnavåg', 'Fredrikstad', 'Førde', 'Gjøvik', 'Grimstad', 'Halden', 'Hamar', 'Hammerfest', 'Harstad',
        'Haugesund', 'Hokksund', 'Holmestrand', 'Honningsvåg', 'Horten', 'Hønefoss', 'Jessheim', 'Jørpeland',
        'Kirkenes', 'Kolvereid', 'Kongsberg', 'Kongsvinger', 'Kopervik', 'Kragerø', 'Kristiansand', 'Kristiansund',
        'Langesund', 'Larvik', 'Leknes', 'Levanger', 'Lillehammer', 'Lillesand', 'Lillestrøm', 'Lyngdal', 'Mandal',
        'Mo i Rana',  'Moelv', 'Molde', 'Mosjøen', 'Moss', 'Mysen', 'Måløy', 'Namsos', 'Narvik', 'Notodden', 'Odda',
        'Orkanger', 'Oslo', 'Otta', 'Porsgrunn', 'Risør', 'Rjukan', 'Røros', 'Sandefjord', 'Sandnes', 'Sandnessjøen',
        'Sandvika', 'Sarpsborg', 'Sauda', 'Ski', 'Skien', 'Skudeneshavn', 'Sortland', 'Stathelle', 'Stavanger',
        'Stavern', 'Steinkjer', 'Stjørdalshalsen', 'Stokmarknes', 'Stord', 'Svelvik', 'Svolvær', 'Tromsø', 'Trondheim',
        'Tvedestrand', 'Tønsberg', 'Ulsteinvik', 'Vadsø', 'Vardø', 'Verdalsøra', 'Vinstra', 'Åkrehamn', 'Ålesund',
        'Åndalsnes', 'Åsgårdstrand',
    ];

    protected static $cityFormats = [
        '{{cityName}}',
    ];

    /**
     * @var array Norwegian municipality names
     *
     * @see https://no.wikipedia.org/wiki/Norges_kommuner
     */
    protected static $kommuneNames = [
        'Halden', 'Moss', 'Sarpsborg', 'Fredrikstad', 'Hvaler', 'Aremark', 'Marker', 'Rømskog', 'Trøgstad', 'Spydeberg',
        'Askim', 'Eidsberg', 'Skiptvet', 'Rakkestad', 'Råde', 'Rygge', 'Våler', 'Hobøl', 'Vestby', 'Ski', 'Ås', 'Frogn',
        'Nesodden', 'Oppegård', 'Bærum', 'Asker', 'Aurskog-Høland', 'Sørum', 'Fet', 'Rælingen', 'Enebakk', 'Lørenskog',
        'Skedsmo', 'Nittedal', 'Gjerdrum', 'Ullensaker', 'Nes', 'Eidsvoll', 'Nannestad', 'Hurdal', 'Oslo',
        'Kongsvinger', 'Hamar', 'Ringsaker', 'Løten', 'Stange', 'Nord-Odal', 'Sør-Odal', 'Eidskog', 'Grue', 'Åsnes',
        'Våler', 'Elverum', 'Trysil', 'Åmot', 'Stor-Elvdal', 'Rendalen', 'Engerdal', 'Tolga', 'Tynset', 'Alvdal',
        'Folldal', 'Os', 'Lillehammer', 'Gjøvik', 'Dovre', 'Lesja', 'Skjåk', 'Lom', 'Vågå', 'Nord-Fron', 'Sel',
        'Sør-Fron', 'Ringebu', 'Øyer', 'Gausdal', 'Østre Toten', 'Vestre Toten', 'Jevnaker', 'Lunner', 'Gran',
        'Søndre Land', 'Nordre Land', 'Sør-Aurdal', 'Etnedal', 'Nord-Aurdal', 'Vestre Slidre', 'Øystre Slidre', 'Vang',
        'Drammen', 'Kongsberg', 'Ringerike', 'Hole', 'Flå', 'Nes', 'Gol', 'Hemsedal', 'Ål', 'Hol', 'Sigdal',
        'Krødsherad', 'Modum', 'Øvre Eiker', 'Nedre Eiker', 'Lier', 'Røyken', 'Hurum', 'Flesberg', 'Rollag',
        'Nore og Uvdal', 'Horten', 'Holmestrand', 'Tønsberg', 'Sandefjord', 'Larvik', 'Svelvik', 'Sande', 'Hof', 'Re',
        'Andebu', 'Stokke', 'Nøtterøy', 'Tjøme', 'Lardal', 'Porsgrunn', 'Skien', 'Notodden', 'Siljan', 'Bamble',
        'Kragerø', 'Drangedal', 'Nome', 'Bø', 'Sauherad', 'Tinn', 'Hjartdal', 'Seljord', 'Kviteseid', 'Nissedal',
        'Fyresdal', 'Tokke', 'Vinje', 'Risør', 'Grimstad', 'Arendal', 'Gjerstad', 'Vegårshei', 'Tvedestrand', 'Froland',
        'Lillesand', 'Birkenes', 'Åmli', 'Iveland', 'Evje og Hornnes', 'Bygland', 'Valle', 'Bykle', 'Kristiansand',
        'Mandal', 'Farsund', 'Flekkefjord', 'Vennesla', 'Songdalen', 'Søgne', 'Marnardal', 'Åseral', 'Audnedal',
        'Lindesnes', 'Lyngdal', 'Hægebostad', 'Kvinesdal', 'Sirdal', 'Eigersund', 'Sandnes', 'Stavanger', 'Haugesund',
        'Sokndal', 'Lund', 'Bjerkreim', 'Hå', 'Klepp', 'Time', 'Gjesdal', 'Sola', 'Randaberg', 'Forsand', 'Strand',
        'Hjelmeland', 'Suldal', 'Sauda', 'Finnøy', 'Rennesøy', 'Kvitsøy', 'Bokn', 'Tysvær', 'Karmøy', 'Utsira',
        'Vindafjord', 'Bergen', 'Etne', 'Sveio', 'Bømlo', 'Stord', 'Fitjar', 'Tysnes', 'Kvinnherad', 'Jondal', 'Odda',
        'Ullensvang', 'Eidfjord', 'Ulvik', 'Granvin', 'Voss', 'Kvam', 'Fusa', 'Samnanger', 'Os', 'Austevoll', 'Sund',
        'Fjell', 'Askøy', 'Vaksdal', 'Modalen', 'Osterøy', 'Meland', 'Øygarden', 'Radøy', 'Lindås', 'Austrheim',
        'Fedje', 'Masfjorden', 'Flora', 'Gulen', 'Solund', 'Hyllestad', 'Høyanger', 'Vik', 'Balestrand', 'Leikanger',
        'Sogndal', 'Aurland', 'Lærdal', 'Årdal', 'Luster', 'Askvoll', 'Fjaler', 'Gaular', 'Jølster', 'Førde',
        'Naustdal', 'Bremanger', 'Vågsøy', 'Selje', 'Eid', 'Hornindal', 'Gloppen', 'Stryn', 'Molde', 'Ålesund',
        'Kristiansund', 'Vanylven', 'Sande', 'Herøy', 'Ulstein', 'Hareid', 'Volda', 'Ørsta', 'Ørskog', 'Norddal',
        'Stranda', 'Stordal', 'Sykkylven', 'Skodje', 'Sula', 'Giske', 'Haram', 'Vestnes', 'Rauma', 'Nesset', 'Midsund',
        'Sandøy', 'Aukra', 'Fræna', 'Eide', 'Averøy', 'Gjemnes', 'Tingvoll', 'Sunndal', 'Surnadal', 'Rindal', 'Halsa',
        'Smøla', 'Aure', 'Trondheim', 'Hemne', 'Snillfjord', 'Hitra', 'Frøya', 'Ørland', 'Agdenes', 'Rissa', 'Bjugn',
        'Åfjord', 'Roan', 'Osen', 'Oppdal', 'Rennebu', 'Meldal', 'Orkdal', 'Røros', 'Holtålen', 'Midtre Gauldal',
        'Melhus', 'Skaun', 'Klæbu', 'Malvik', 'Selbu', 'Tydal', 'Steinkjer', 'Namsos', 'Meråker', 'Stjørdal', 'Frosta',
        'Leksvik', 'Levanger', 'Verdal', 'Verran', 'Namdalseid', 'Inderøy', 'Snåsa', 'Lierne', 'Røyrvik', 'Namsskogan',
        'Grong', 'Høylandet', 'Overhalla', 'Fosnes', 'Flatanger', 'Vikna', 'Nærøy', 'Leka', 'Bodø', 'Narvik', 'Bindal',
        'Sømna', 'Brønnøy', 'Vega', 'Vevelstad', 'Herøy', 'Alstahaug', 'Leirfjord', 'Vefsn', 'Grane', 'Hattfjelldal',
        'Dønna', 'Nesna', 'Hemnes', 'Rana', 'Lurøy', 'Træna', 'Rødøy', 'Meløy', 'Gildeskål', 'Beiarn', 'Saltdal',
        'Fauske', 'Sørfold', 'Steigen', 'Hamarøy', 'Tysfjord', 'Lødingen', 'Tjeldsund', 'Evenes', 'Ballangen', 'Røst',
        'Værøy', 'Flakstad', 'Vestvågøy', 'Vågan', 'Hadsel', 'Bø', 'Øksnes', 'Sortland', 'Andøy', 'Moskenes',
        'Harstad[10]', 'Tromsø', 'Kvæfjord', 'Skånland', 'Ibestad', 'Gratangen', 'Lavangen', 'Bardu', 'Salangen',
        'Målselv', 'Sørreisa', 'Dyrøy', 'Tranøy', 'Torsken', 'Berg', 'Lenvik', 'Balsfjord', 'Karlsøy', 'Lyngen',
        'Storfjord', 'Kåfjord', 'Skjervøy', 'Nordreisa', 'Kvænangen', 'Vardø', 'Vadsø', 'Hammerfest', 'Kautokeino',
        'Alta', 'Loppa', 'Hasvik', 'Kvalsund', 'Måsøy', 'Nordkapp', 'Porsanger', 'Karasjok', 'Lebesby', 'Gamvik',
        'Berlevåg', 'Tana', 'Nesseby', 'Båtsfjord', 'Sør-Varanger',
    ];

    /**
     * @var array Norwegian county names
     *
     * @see https://no.wikipedia.org/wiki/Norges_fylker
     */
    protected static $countyNames = [
        'Østfold', 'Akershus', 'Oslo', 'Hedmark', 'Oppland', 'Buskerud', 'Vestfold', 'Telemark', 'Aust-Agder',
        'Vest-Agder', 'Rogaland', 'Hordaland', 'Sogn og Fjordane', 'Møre og Romsdal', 'Sør-Trøndelag', 'Nord-Trøndelag',
        'Nordland', 'Troms', 'Finnmark', 'Svalbard', 'Jan Mayen', 'Kontinentalsokkelen',
    ];

    protected static $country = [
        'Abkhasia', 'Afghanistan', 'Albania', 'Algerie', 'Andorra', 'Angola', 'Antigua og Barbuda', 'Argentina',
        'Armenia', 'Aserbajdsjan', 'Australia', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belgia', 'Belize',
        'Benin', 'Bhutan', 'Bolivia', 'Bosnia-Hercegovina', 'Botswana', 'Brasil', 'Brunei', 'Bulgaria', 'Burkina Faso',
        'Burundi', 'Canada', 'Chile', 'Colombia', 'Costa Rica', 'Cuba', 'Danmark', 'De forente arabiske emirater',
        'Den demokratiske republikken Kongo', 'Den dominikanske republikk', 'Den sentralafrikanske republikk',
        'Djibouti', 'Dominica', 'Ecuador', 'Egypt', 'Ekvatorial-Guinea', 'Elfenbenskysten', 'El Salvador', 'Eritrea',
        'Estland', 'Etiopia', 'Fiji', 'Filippinene', 'Finland', 'Frankrike', 'Gabon', 'Gambia', 'Georgia', 'Ghana',
        'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Hellas', 'Honduras', 'Hviterussland',
        'India', 'Indonesia', 'Irak', 'Iran', 'Irland', 'Island', 'Israel', 'Italia', 'Jamaica', 'Japan', 'Jemen',
        'Jordan', 'Kambodsja', 'Kamerun', 'Kapp Verde', 'Kasakhstan', 'Kenya', 'Folkerepublikken Kina', 'Kirgisistan',
        'Kiribati', 'Komorene', 'Republikken Kongo', 'Kosovo', 'Kroatia', 'Kuwait', 'Kypros', 'Laos', 'Latvia',
        'Lesotho', 'Libanon', 'Liberia', 'Libya', 'Liechtenstein', 'Litauen', 'Luxembourg', 'Madagaskar', 'Makedonia',
        'Malawi', 'Malaysia', 'Maldivene', 'Mali', 'Malta', 'Marokko', 'Marshalløyene', 'Mauritania', 'Mauritius',
        'Mexico', 'Mikronesiaføderasjonen', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Mosambik', 'Myanmar',
        'Namibia', 'Nauru', 'Nederland', 'Nepal', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Nord-Korea',
        'Nord-Kypros', 'Norge', 'Oman', 'Pakistan', 'Palau', 'Panama', 'Papua Ny-Guinea', 'Paraguay', 'Peru', 'Polen',
        'Portugal', 'Qatar', 'Romania', 'Russland', 'Rwanda', 'Saint Kitts og Nevis', 'Saint Lucia',
        'Saint Vincent og Grenadinene', 'Salomonøyene', 'Samoa', 'San Marino', 'São Tomé og Príncipe', 'Saudi-Arabia',
        'Senegal', 'Serbia', 'Seychellene', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Somalia', 'Spania',
        'Sri Lanka', 'Storbritannia', 'Sudan', 'Surinam', 'Sveits', 'Sverige', 'Swaziland', 'Syria', 'Sør-Afrika',
        'Sør-Korea', 'Sør-Ossetia', 'Sør-Sudan', 'Tadsjikistan', 'Taiwan', 'Tanzania', 'Thailand', 'Togo', 'Tonga',
        'Transnistria', 'Trinidad og Tobago', 'Tsjad', 'Tsjekkia', 'Tunisia', 'Turkmenistan', 'Tuvalu', 'Tyrkia',
        'Tyskland', 'Uganda', 'USA', 'Ukraina', 'Ungarn', 'Uruguay', 'Usbekistan', 'Vanuatu', 'Vatikanstaten',
        'Venezuela', 'Vietnam', 'Zambia', 'Zimbabwe', 'Østerrike', 'Øst-Timor',
    ];

    /**
     * @var array Norwegian street name formats
     */
    protected static $streetNameFormats = [
        '{{lastName}}{{streetSuffix}}',
        '{{lastName}}{{streetSuffix}}',
        '{{firstName}}{{streetSuffix}}',
        '{{firstName}}{{streetSuffix}}',
        '{{streetPrefix}}{{streetSuffix}}',
        '{{streetPrefix}}{{streetSuffix}}',
        '{{streetPrefix}}{{streetSuffix}}',
        '{{streetPrefix}}{{streetSuffix}}',
        '{{lastName}} {{streetSuffixWord}}',
    ];

    /**
     * @var array Norwegian street address formats
     */
    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}',
    ];

    /**
     * @var array Norwegian address formats
     */
    protected static $addressFormats = [
        "{{streetAddress}}\n{{postcode}} {{city}}",
    ];

    /**
     * Randomly return a real city name
     *
     * @return string
     */
    public static function cityName()
    {
        return static::randomElement(static::$cityNames);
    }

    public static function streetSuffixWord()
    {
        return static::randomElement(static::$streetSuffixWord);
    }

    public static function streetPrefix()
    {
        return static::randomElement(static::$streetPrefix);
    }

    /**
     * Randomly return a building number.
     *
     * @return string
     */
    public static function buildingNumber()
    {
        return static::toUpper(static::bothify(static::randomElement(static::$buildingNumber)));
    }
}
