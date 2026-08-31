<?php

namespace Faker\Provider\it_IT;

class Address extends \Faker\Provider\Address
{
    protected static $cityPrefix = ['San', 'Borgo', 'Sesto', 'Quarto', 'Settimo'];
    protected static $citySuffix = ['a mare', 'lido', 'ligure', 'del friuli', 'salentino', 'calabro', 'veneto', 'nell\'emilia', 'umbro', 'laziale', 'terme', 'sardo'];
    protected static $buildingNumber = ['%##', '%#', '%'];
    protected static $streetSuffix = [
        'Piazza', 'Strada', 'Via', 'Borgo', 'Contrada', 'Rotonda', 'Incrocio',
    ];
    protected static $postcode = ['#####'];
    protected static $state = [
        'Agrigento', 'Alessandria', 'Ancona', 'Aosta', 'Arezzo', 'Ascoli Piceno', 'Asti', 'Avellino', 'Bari', 'Barletta-Andria-Trani', 'Belluno', 'Benevento', 'Bergamo', 'Biella', 'Bologna', 'Bolzano', 'Brescia', 'Brindisi', 'Cagliari', 'Caltanissetta', 'Campobasso', 'Caserta', 'Catania', 'Catanzaro', 'Chieti', 'Como', 'Cosenza', 'Cremona', 'Crotone', 'Cuneo', 'Enna', 'Fermo', 'Ferrara', 'Firenze', 'Foggia', 'Forlì-Cesena', 'Frosinone', 'Genova', 'Gorizia', 'Grosseto', 'Imperia', 'Isernia', 'La Spezia', 'L\'Aquila', 'Latina', 'Lecce', 'Lecco', 'Livorno', 'Lodi', 'Lucca', 'Macerata', 'Mantova', 'Massa-Carrara', 'Matera', 'Messina', 'Milano', 'Modena', 'Monza e della Brianza', 'Napoli', 'Novara', 'Nuoro', 'Oristano', 'Padova', 'Palermo', 'Parma', 'Pavia', 'Perugia', 'Pesaro e Urbino', 'Pescara', 'Piacenza', 'Pisa', 'Pistoia', 'Pordenone', 'Potenza', 'Prato', 'Ragusa', 'Ravenna', 'Reggio Calabria', 'Reggio Emilia', 'Rieti', 'Rimini', 'Roma', 'Rovigo', 'Salerno', 'Sassari', 'Savona', 'Siena', 'Siracusa', 'Sondrio', 'Sud Sardegna', 'Taranto', 'Teramo', 'Terni', 'Torino', 'Trapani', 'Trento', 'Treviso', 'Trieste', 'Udine', 'Varese', 'Venezia', 'Verbano-Cusio-Ossola', 'Vercelli', 'Verona', 'Vibo Valentia', 'Vicenza', 'Viterbo',
    ];
    protected static $stateAbbr = [
        'AG', 'AL', 'AN', 'AO', 'AR', 'AP', 'AT', 'AV', 'BA', 'BT', 'BL', 'BN', 'BG', 'BI', 'BO', 'BZ', 'BS', 'BR', 'CA', 'CL', 'CB', 'CE', 'CT', 'CZ', 'CH', 'CO', 'CS', 'CR', 'KR', 'CN', 'EN', 'FM', 'FE', 'FI', 'FG', 'FC', 'FR', 'GE', 'GO', 'GR', 'IM', 'IS', 'SP', 'AQ', 'LT', 'LE', 'LC', 'LI', 'LO', 'LU', 'MC', 'MN', 'MS', 'MT', 'ME', 'MI', 'MO', 'MB', 'NA', 'NO', 'NU', 'OR', 'PD', 'PA', 'PR', 'PV', 'PG', 'PU', 'PE', 'PC', 'PI', 'PT', 'PN', 'PZ', 'PO', 'RG', 'RA', 'RC', 'RE', 'RI', 'RN', 'RM', 'RO', 'SA', 'SS', 'SV', 'SI', 'SR', 'SO', 'SU', 'TA', 'TE', 'TR', 'TO', 'TP', 'TN', 'TV', 'TS', 'UD', 'VA', 'VE', 'VB', 'VC', 'VR', 'VV', 'VI', 'VT',
    ];
    protected static $country = [
        'Afghanistan', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Anguilla', 'Antartide (territori a sud del 60° parallelo)', 'Antigua e Barbuda', 'Antille Olandesi', 'Arabia Saudita', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan',
        'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Bielorussia', 'Belgio', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnia e Herzegovina', 'Botswana', 'Isola Bouvet', 'Brasile', 'Territorio dell\'arcipelago indiano', 'Isole Vergini Britanniche', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi',
        'Cambogia', 'Cameroon', 'Canada', 'Capo Verde', 'Isole Cayman', 'Repubblica Centrale Africana', 'Chad', 'Cile', 'Cina', 'Isola di Pasqua', 'Isole Cocos', 'Colombia', 'Comore', 'Congo', 'Isole Cook', 'Costa Rica', 'Costa d\'Avorio', 'Croazia', 'Cuba', 'Cipro', 'Repubblica Ceca',
        'Danimarca', 'Repubblica Dominicana',
        'Equador', 'Egitto', 'El Salvador', 'Emirati Arabi Uniti', 'Eritrea', 'Estonia', 'Eswatini', 'Etiopia',
        'Isole Faroe', 'Isole Falkland', 'Fiji', 'Filippine', 'Finlandia', 'Francia', 'Guyana Francese', 'Polinesia Francese', 'Territori Francesi del Sud',
        'Gabon', 'Gambia', 'Georgia', 'Georgia del Sud e Isole Sandwich Australi', 'Germania', 'Ghana', 'Giamaica', 'Giappone', 'Gibilterra', 'Gibuti', 'Giordania', 'Grecia', 'Groenlandia', 'Grenada', 'Guadalupa', 'Guam', 'Guatemala', 'Guernsey', 'Guinea', 'Guinea-Bissau', 'Guinea Equatoriale', 'Guyana',
        'Haiti', 'Isole Heard e McDonald', 'Honduras', 'Hong Kong',
        'Islanda', 'India', 'Indonesia', 'Iran', 'Iraq', 'Irlanda', 'Israele', 'Italia',
        'Isola di Jersey',
        'Kazakhstan', 'Kenya', 'Kirghizistan', 'Kiribati', 'Korea', 'Kuwait',
        'Repubblica del Laos', 'Latvia', 'Lesotho', 'Libano', 'Liberia', 'Libia', 'Liechtenstein', 'Lituania', 'Lussemburgo',
        'Macao', 'Macedonia', 'Madagascar', 'Malawi', 'Malesia', 'Maldive', 'Mali', 'Malta', 'Isola di Man', 'Isole Marianne Settentrionali', 'Isole Marshall', 'Martinica', 'Mauritania', 'Mauritius', 'Mayotte', 'Messico', 'Micronesia', 'Isole Minori esterne degli Stati Uniti d\'America', 'Moldova', 'Principato di Monaco', 'Mongolia', 'Montenegro', 'Montserrat', 'Marocco', 'Mozambico', 'Myanmar',
        'Namibia', 'Nauru', 'Nepal', 'Nuova Caledonia', 'Nuova Zelanda', 'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Isole Norfolk', 'Norvegia',
        'Olanda', 'Oman',
        'Pakistan', 'Palau', 'Palestina', 'Panama', 'Papua Nuova Guinea', 'Paraguay', 'Peru', 'Isole Pitcairn', 'Polonia', 'Portogallo', 'Porto Rico',
        'Qatar',
        'Regno Unito', 'Isola della Riunione', 'Romania', 'Russia', 'Rwanda',
        'Sahara Occidentale', 'San Bartolomeo', 'Sant\'Elena', 'Saint Kitts e Nevis', 'Saint Lucia', 'Saint Martin', 'Saint-Pierre e Miquelon', 'Saint Vincent e Grenadine', 'Samoa', 'San Marino', 'Sao Tome e Principe', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovenia', 'Isole Solomon', 'Somalia', 'Spagna', 'Sri Lanka', 'Stati Uniti d\'America', 'Sud Africa', 'Sudan', 'Suriname', 'Isole Svalbard e Jan Mayen', 'Svezia', 'Svizzera', 'Siria',
        'Taiwan', 'Tajikistan', 'Tanzania', 'Tailandia', 'Timor Leste', 'Togo', 'Tokelau', 'Tonga', 'Trinidad e Tobago', 'Tunisia', 'Turchia', 'Turkmenistan', 'Isole di Turks e Caicos', 'Tuvalu',
        'Uganda', 'Ucraina', 'Uruguay', 'Uzbekistan', 'Ungheria',
        'Vanuatu', 'Vaticano', 'Venezuela', 'Isole Vergini Statunitensi', 'Vietnam',
        'Wallis e Futuna',
        'Yemen',
        'Zambia', 'Zimbabwe',
    ];
    protected static $cityFormats = [
        '{{cityPrefix}} {{firstName}} {{citySuffix}}',
        '{{cityPrefix}} {{firstName}}',
        '{{firstName}} {{citySuffix}}',
        '{{lastName}} {{citySuffix}}',
    ];
    protected static $streetNameFormats = [
        '{{streetSuffix}} {{firstName}}',
        '{{streetSuffix}} {{lastName}}',
    ];
    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}',
        '{{streetName}} {{buildingNumber}} {{secondaryAddress}}',
    ];
    protected static $addressFormats = [
        "{{streetAddress}}\n{{city}}, {{postcode}} {{state}} ({{stateAbbr}})",
    ];
    protected static $secondaryAddressFormats = ['Appartamento ##', 'Piano #'];

    /**
     * @example 'Borgo'
     */
    public static function cityPrefix()
    {
        return static::randomElement(static::$cityPrefix);
    }

    /**
     * @example 'Appartamento 350'
     */
    public static function secondaryAddress()
    {
        return static::numerify(static::randomElement(static::$secondaryAddressFormats));
    }

    /**
     * @example 'Cagliari'
     */
    public static function state()
    {
        return static::randomElement(static::$state);
    }

    /**
     * @example 'CA'
     */
    public static function stateAbbr()
    {
        return static::randomElement(static::$stateAbbr);
    }
}
