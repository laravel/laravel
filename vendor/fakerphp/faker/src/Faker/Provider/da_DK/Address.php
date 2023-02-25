<?php

namespace Faker\Provider\da_DK;

class Address extends \Faker\Provider\Address
{
    /**
     * @var array Danish city suffixes.
     */
    protected static $citySuffix = [
        'sted', 'bjerg', 'borg', 'rød', 'lund', 'by',
    ];

    /**
     * @var array Danish street suffixes.
     */
    protected static $streetSuffix = [
        'vej', 'gade', 'skov', 'haven',
    ];

    /**
     * @var array Danish street word suffixes.
     */
    protected static $streetSuffixWord = [
        'Vej', 'Gade', 'Allé', 'Boulevard', 'Plads', 'Have',
    ];

    /**
     * @var array Danish building numbers.
     */
    protected static $buildingNumber = [
        '%##', '%#', '%#', '%', '%', '%', '%?', '% ?',
    ];

    /**
     * @var array Danish building level.
     */
    protected static $buildingLevel = [
        'st.', '%.', '%. sal.',
    ];

    /**
     * @var array Danish building sides.
     */
    protected static $buildingSide = [
        'tv.', 'mf.', 'th.',
    ];

    /**
     * @var array Danish zip code.
     */
    protected static $postcode = [
        '%###',
    ];

    /**
     * @var array Danish cities.
     */
    protected static $cityNames = [
        'Aabenraa', 'Aabybro', 'Aakirkeby', 'Aalborg', 'Aalestrup', 'Aars', 'Aarup', 'Agedrup', 'Agerbæk', 'Agerskov',
        'Albertslund', 'Allerød', 'Allinge', 'Allingåbro', 'Almind', 'Anholt', 'Ansager', 'Arden', 'Asaa', 'Askeby',
        'Asnæs', 'Asperup', 'Assens', 'Augustenborg', 'Aulum', 'Auning', 'Bagenkop', 'Bagsværd', 'Balle', 'Ballerup',
        'Bandholm', 'Barrit', 'Beder', 'Bedsted', 'Bevtoft', 'Billum', 'Billund', 'Bindslev', 'Birkerød', 'Bjerringbro',
        'Bjert', 'Bjæverskov', 'Blokhus', 'Blommenslyst', 'Blåvand', 'Boeslunde', 'Bogense', 'Bogø', 'Bolderslev', 'Bording',
        'Borre', 'Borup', 'Brøndby', 'Brabrand', 'Bramming', 'Brande', 'Branderup', 'Bredebro', 'Bredsten', 'Brenderup',
        'Broager', 'Broby', 'Brovst', 'Bryrup', 'Brædstrup', 'Strand', 'Brønderslev', 'Brønshøj', 'Brørup', 'Bække',
        'Bækmarksbro', 'Bælum', 'Børkop', 'Bøvlingbjerg', 'Charlottenlund', 'Christiansfeld', 'Dalby', 'Dalmose',
        'Dannemare', 'Daugård', 'Dianalund', 'Dragør', 'Dronninglund', 'Dronningmølle', 'Dybvad', 'Dyssegård', 'Ebberup',
        'Ebeltoft', 'Egernsund', 'Egtved', 'Egå', 'Ejby', 'Ejstrupholm', 'Engesvang', 'Errindlev', 'Erslev', 'Esbjerg',
        'Eskebjerg', 'Eskilstrup', 'Espergærde', 'Faaborg', 'Fanø', 'Farsø', 'Farum', 'Faxe', 'Ladeplads', 'Fejø',
        'Ferritslev', 'Fjenneslev', 'Fjerritslev', 'Flemming', 'Fredensborg', 'Fredericia', 'Frederiksberg',
        'Frederikshavn', 'Frederikssund', 'Frederiksværk', 'Frørup', 'Frøstrup', 'Fuglebjerg', 'Føllenslev', 'Føvling',
        'Fårevejle', 'Fårup', 'Fårvang', 'Gadbjerg', 'Gadstrup', 'Galten', 'Gandrup', 'Gedser', 'Gedsted', 'Gedved', 'Gelsted',
        'Gentofte', 'Gesten', 'Gilleleje', 'Gislev', 'Gislinge', 'Gistrup', 'Give', 'Gjerlev', 'Gjern', 'Glamsbjerg',
        'Glejbjerg', 'Glesborg', 'Glostrup', 'Glumsø', 'Gram', 'Gredstedbro', 'Grenaa', 'Greve', 'Grevinge', 'Grindsted',
        'Græsted', 'Gråsten', 'Gudbjerg', 'Sydfyn', 'Gudhjem', 'Gudme', 'Guldborg', 'Gørding', 'Gørlev', 'Gørløse',
        'Haderslev', 'Haderup', 'Hadsten', 'Hadsund', 'Hals', 'Hammel', 'Hampen', 'Hanstholm', 'Harboøre', 'Harlev', 'Harndrup',
        'Harpelunde', 'Hasle', 'Haslev', 'Hasselager', 'Havdrup', 'Havndal', 'Hedehusene', 'Hedensted', 'Hejls', 'Hejnsvig',
        'Hellebæk', 'Hellerup', 'Helsinge', 'Helsingør', 'Hemmet', 'Henne', 'Herfølge', 'Herlev', 'Herlufmagle', 'Herning',
        'Hesselager', 'Hillerød', 'Hinnerup', 'Hirtshals', 'Hjallerup', 'Hjerm', 'Hjortshøj', 'Hjørring', 'Hobro', 'Holbæk',
        'Holeby', 'Holmegaard', 'Holstebro', 'Holsted', 'Holte', 'Horbelev', 'Hornbæk', 'Hornslet', 'Hornsyld', 'Horsens',
        'Horslunde', 'Hovborg', 'Hovedgård', 'Humble', 'Humlebæk', 'Hundested', 'Hundslund', 'Hurup', 'Hvalsø', 'Hvide',
        'Sande', 'Hvidovre', 'Højbjerg', 'Højby', 'Højer', 'Højslev', 'Høng', 'Hørning', 'Hørsholm', 'Hørve', 'Hårlev',
        'Idestrup', 'Ikast', 'Ishøj', 'Janderup', 'Vestj', 'Jelling', 'Jerslev', 'Sjælland', 'Jerup', 'Jordrup', 'Juelsminde',
        'Jyderup', 'Jyllinge', 'Jystrup', 'Midtsj', 'Jægerspris', 'Kalundborg', 'Kalvehave', 'Karby', 'Karise', 'Karlslunde',
        'Karrebæksminde', 'Karup', 'Kastrup', 'Kerteminde', 'Kettinge', 'Kibæk', 'Kirke', 'Hyllinge', 'Såby', 'Kjellerup',
        'Klampenborg', 'Klarup', 'Klemensker', 'Klippinge', 'Klovborg', 'Knebel', 'Kokkedal', 'Kolding', 'Kolind', 'Kongens',
        'Lyngby', 'Kongerslev', 'Korsør', 'Kruså', 'Kvistgård', 'Kværndrup', 'København', 'Køge', 'Langebæk', 'Langeskov',
        'Langå', 'Lejre', 'Lemming', 'Lemvig', 'Lille', 'Skensved', 'Lintrup', 'Liseleje', 'Lundby', 'Lunderskov', 'Lynge',
        'Lystrup', 'Læsø', 'Løgstrup', 'Løgstør', 'Løgumkloster', 'Løkken', 'Løsning', 'Låsby', 'Malling', 'Mariager',
        'Maribo', 'Marslev', 'Marstal', 'Martofte', 'Melby', 'Mern', 'Mesinge', 'Middelfart', 'Millinge', 'Morud', 'Munke',
        'Bjergby', 'Munkebo', 'Møldrup', 'Mørke', 'Mørkøv', 'Måløv', 'Mårslet', 'Nakskov', 'Nexø', 'Nibe', 'Nimtofte',
        'Nordborg', 'Nyborg', 'Nykøbing', 'Nyrup', 'Nysted', 'Nærum', 'Næstved', 'Nørager', 'Nørre', 'Aaby', 'Alslev',
        'Asmindrup', 'Nebel', 'Snede', 'Nørreballe', 'Nørresundby', 'Odder', 'Odense', 'Oksbøl', 'Otterup', 'Oure', 'Outrup',
        'Padborg', 'Pandrup', 'Præstø', 'Randbøl', 'Randers', 'Ranum', 'Rask', 'Mølle', 'Redsted', 'Regstrup', 'Ribe', 'Ringe',
        'Ringkøbing', 'Ringsted', 'Risskov', 'Roskilde', 'Roslev', 'Rude', 'Rudkøbing', 'Ruds', 'Vedby', 'Rungsted', 'Kyst',
        'Rynkeby', 'Ryomgård', 'Ryslinge', 'Rødby', 'Rødding', 'Rødekro', 'Rødkærsbro', 'Rødovre', 'Rødvig', 'Stevns',
        'Rønde', 'Rønne', 'Rønnede', 'Rørvig', 'Sabro', 'Sakskøbing', 'Saltum', 'Samsø', 'Sandved', 'Sejerø', 'Silkeborg',
        'Sindal', 'Sjællands', 'Odde', 'Sjølund', 'Skagen', 'Skals', 'Skamby', 'Skanderborg', 'Skibby', 'Skive', 'Skjern',
        'Skodsborg', 'Skovlunde', 'Skælskør', 'Skærbæk', 'Skævinge', 'Skødstrup', 'Skørping', 'Skårup', 'Slagelse',
        'Slangerup', 'Smørum', 'Snedsted', 'Snekkersten', 'Snertinge', 'Solbjerg', 'Solrød', 'Sommersted', 'Sorring', 'Sorø',
        'Spentrup', 'Spjald', 'Sporup', 'Spøttrup', 'Stakroge', 'Stege', 'Stenderup', 'Stenlille', 'Stenløse', 'Stenstrup',
        'Stensved', 'Stoholm', 'Jyll', 'Stokkemarke', 'Store', 'Fuglede', 'Heddinge', 'Merløse', 'Storvorde', 'Stouby',
        'Strandby', 'Struer', 'Strøby', 'Stubbekøbing', 'Støvring', 'Suldrup', 'Sulsted', 'Sunds', 'Svaneke', 'Svebølle',
        'Svendborg', 'Svenstrup', 'Svinninge', 'Sydals', 'Sæby', 'Søborg', 'Søby', 'Ærø', 'Søllested', 'Sønder', 'Felding',
        'Sønderborg', 'Søndersø', 'Sørvad', 'Taastrup', 'Tappernøje', 'Tarm', 'Terndrup', 'Them', 'Thisted', 'Thorsø',
        'Thyborøn', 'Thyholm', 'Tikøb', 'Tilst', 'Tinglev', 'Tistrup', 'Tisvildeleje', 'Tjele', 'Tjæreborg', 'Toftlund',
        'Tommerup', 'Toreby', 'Torrig', 'Tranbjerg', 'Tranekær', 'Trige', 'Trustrup', 'Tune', 'Tureby', 'Tylstrup', 'Tølløse',
        'Tønder', 'Tørring', 'Tårs', 'Ugerløse', 'Uldum', 'Ulfborg', 'Ullerslev', 'Ulstrup', 'Vadum', 'Valby', 'Vallensbæk',
        'Vamdrup', 'Vandel', 'Vanløse', 'Varde', 'Vedbæk', 'Veflinge', 'Vejby', 'Vejen', 'Vejers', 'Vejle', 'Vejstrup',
        'Veksø', 'Vemb', 'Vemmelev', 'Vesløs', 'Vestbjerg', 'Vester', 'Skerninge', 'Vesterborg', 'Vestervig', 'Viborg', 'Viby',
        'Videbæk', 'Vildbjerg', 'Vils', 'Vinderup', 'Vipperød', 'Virum', 'Vissenbjerg', 'Viuf', 'Vodskov', 'Vojens', 'Vonge',
        'Vorbasse', 'Vordingborg', 'Væggerløse', 'Værløse', 'Ærøskøbing', 'Ølgod', 'Ølsted', 'Ølstykke', 'Ørbæk',
        'Ørnhøj', 'Ørsted', 'Djurs', 'Østbirk', 'Øster', 'Assels', 'Ulslev', 'Østermarie', 'Østervrå', 'Åbyhøj',
        'Ålbæk', 'Ålsgårde', 'Århus', 'Årre', 'Årslev', 'Haarby', 'Nivå', 'Rømø', 'Omme', 'Vrå', 'Ørum',
    ];

    /**
     * @var array Danish municipalities, called 'kommuner' in danish.
     */
    protected static $kommuneNames = [
        'København', 'Frederiksberg', 'Ballerup', 'Brøndby', 'Dragør', 'Gentofte', 'Gladsaxe', 'Glostrup', 'Herlev',
        'Albertslund', 'Hvidovre', 'Høje Taastrup', 'Lyngby-Taarbæk', 'Rødovre', 'Ishøj', 'Tårnby', 'Vallensbæk',
        'Allerød', 'Fredensborg', 'Helsingør', 'Hillerød', 'Hørsholm', 'Rudersdal', 'Egedal', 'Frederikssund', 'Greve',
        'Halsnæs', 'Roskilde', 'Solrød', 'Gribskov', 'Odsherred', 'Holbæk', 'Faxe', 'Kalundborg', 'Ringsted', 'Slagelse',
        'Stevns', 'Sorø', 'Lejre', 'Lolland', 'Næstved', 'Guldborgsund', 'Vordingborg', 'Bornholm', 'Middelfart',
        'Christiansø', 'Assens', 'Faaborg-Midtfyn', 'Kerteminde', 'Nyborg', 'Odense', 'Svendborg', 'Nordfyns', 'Langeland',
        'Ærø', 'Haderslev', 'Billund', 'Sønderborg', 'Tønder', 'Esbjerg', 'Fanø', 'Varde', 'Vejen', 'Aabenraa',
        'Fredericia', 'Horsens', 'Kolding', 'Vejle', 'Herning', 'Holstebro', 'Lemvig', 'Struer', 'Syddjurs', 'Furesø',
        'Norddjurs', 'Favrskov', 'Odder', 'Randers', 'Silkeborg', 'Samsø', 'Skanderborg', 'Aarhus', 'Ikast-Brande',
        'Ringkøbing-Skjern', 'Hedensted', 'Morsø', 'Skive', 'Thisted', 'Viborg', 'Brønderslev', 'Frederikshavn',
        'Vesthimmerlands', 'Læsø', 'Rebild', 'Mariagerfjord', 'Jammerbugt', 'Aalborg', 'Hjørring', 'Køge',
    ];

    /**
     * @var array Danish regions.
     */
    protected static $regionNames = [
        'Region Nordjylland', 'Region Midtjylland', 'Region Syddanmark', 'Region Hovedstaden', 'Region Sjælland',
    ];

    /**
     * @see https://github.com/umpirsky/country-list/blob/master/country/cldr/da_DK/country.php
     *
     * @var array Some countries in danish.
     */
    protected static $country = [
        'Andorra', 'Forenede Arabiske Emirater', 'Afghanistan', 'Antigua og Barbuda', 'Anguilla', 'Albanien', 'Armenien',
        'Hollandske Antiller', 'Angola', 'Antarktis', 'Argentina', 'Amerikansk Samoa', 'Østrig', 'Australien', 'Aruba',
        'Åland', 'Aserbajdsjan', 'Bosnien-Hercegovina', 'Barbados', 'Bangladesh', 'Belgien', 'Burkina Faso', 'Bulgarien',
        'Bahrain', 'Burundi', 'Benin', 'Saint Barthélemy', 'Bermuda', 'Brunei Darussalam', 'Bolivia', 'Brasilien', 'Bahamas',
        'Bhutan', 'Bouvetø', 'Botswana', 'Hviderusland', 'Belize', 'Canada', 'Cocosøerne', 'Congo-Kinshasa',
        'Centralafrikanske Republik', 'Congo', 'Schweiz', 'Elfenbenskysten', 'Cook-øerne', 'Chile', 'Cameroun', 'Kina',
        'Colombia', 'Costa Rica', 'Serbien og Montenegro', 'Cuba', 'Kap Verde', 'Juleøen', 'Cypern', 'Tjekkiet', 'Tyskland',
        'Djibouti', 'Danmark', 'Dominica', 'Den Dominikanske Republik', 'Algeriet', 'Ecuador', 'Estland', 'Egypten',
        'Vestsahara', 'Eritrea', 'Spanien', 'Etiopien', 'Finland', 'Fiji-øerne', 'Falklandsøerne',
        'Mikronesiens Forenede Stater', 'Færøerne', 'Frankrig', 'Gabon', 'Storbritannien', 'Grenada', 'Georgien',
        'Fransk Guyana', 'Guernsey', 'Ghana', 'Gibraltar', 'Grønland', 'Gambia', 'Guinea', 'Guadeloupe', 'Ækvatorialguinea',
        'Grækenland', 'South Georgia og De Sydlige Sandwichøer', 'Guatemala', 'Guam', 'Guinea-Bissau', 'Guyana',
        'SAR Hongkong', 'Heard- og McDonald-øerne', 'Honduras', 'Kroatien', 'Haiti', 'Ungarn', 'Indonesien', 'Irland',
        'Israel', 'Isle of Man', 'Indien', 'Det Britiske Territorium i Det Indiske Ocean', 'Irak', 'Iran', 'Island',
        'Italien', 'Jersey', 'Jamaica', 'Jordan', 'Japan', 'Kenya', 'Kirgisistan', 'Cambodja', 'Kiribati', 'Comorerne',
        'Saint Kitts og Nevis', 'Nordkorea', 'Sydkorea', 'Kuwait', 'Caymanøerne', 'Kasakhstan', 'Laos', 'Libanon',
        'Saint Lucia', 'Liechtenstein', 'Sri Lanka', 'Liberia', 'Lesotho', 'Litauen', 'Luxembourg', 'Letland', 'Libyen',
        'Marokko', 'Monaco', 'Republikken Moldova', 'Montenegro', 'Saint Martin', 'Madagaskar', 'Marshalløerne',
        'Republikken Makedonien', 'Mali', 'Myanmar', 'Mongoliet', 'SAR Macao', 'Nordmarianerne', 'Martinique',
        'Mauretanien', 'Montserrat', 'Malta', 'Mauritius', 'Maldiverne', 'Malawi', 'Mexico', 'Malaysia', 'Mozambique',
        'Namibia', 'Ny Caledonien', 'Niger', 'Norfolk Island', 'Nigeria', 'Nicaragua', 'Holland', 'Norge', 'Nepal', 'Nauru',
        'Niue', 'New Zealand', 'Oman', 'Panama', 'Peru', 'Fransk Polynesien', 'Papua Ny Guinea', 'Filippinerne', 'Pakistan',
        'Polen', 'Saint Pierre og Miquelon', 'Pitcairn', 'Puerto Rico', 'De palæstinensiske områder', 'Portugal', 'Palau',
        'Paraguay', 'Qatar', 'Reunion', 'Rumænien', 'Serbien', 'Rusland', 'Rwanda', 'Saudi-Arabien', 'Salomonøerne',
        'Seychellerne', 'Sudan', 'Sverige', 'Singapore', 'St. Helena', 'Slovenien', 'Svalbard og Jan Mayen', 'Slovakiet',
        'Sierra Leone', 'San Marino', 'Senegal', 'Somalia', 'Surinam', 'Sao Tome og Principe', 'El Salvador', 'Syrien',
        'Swaziland', 'Turks- og Caicosøerne', 'Tchad', 'Franske Besiddelser i Det Sydlige Indiske Ocean', 'Togo',
        'Thailand', 'Tadsjikistan', 'Tokelau', 'Timor-Leste', 'Turkmenistan', 'Tunesien', 'Tonga', 'Tyrkiet',
        'Trinidad og Tobago', 'Tuvalu', 'Taiwan', 'Tanzania', 'Ukraine', 'Uganda', 'De Mindre Amerikanske Oversøiske Øer',
        'USA', 'Uruguay', 'Usbekistan', 'Vatikanstaten', 'St. Vincent og Grenadinerne', 'Venezuela',
        'De britiske jomfruøer', 'De amerikanske jomfruøer', 'Vietnam', 'Vanuatu', 'Wallis og Futunaøerne', 'Samoa',
        'Yemen', 'Mayotte', 'Sydafrika', 'Zambia', 'Zimbabwe',
    ];

    /**
     * @var array Danish city format.
     */
    protected static $cityFormats = [
        '{{cityName}}',
    ];

    /**
     * @var array Danish street's name formats.
     */
    protected static $streetNameFormats = [
        '{{lastName}}{{streetSuffix}}',
        '{{middleName}}{{streetSuffix}}',
        '{{lastName}} {{streetSuffixWord}}',
        '{{middleName}} {{streetSuffixWord}}',
    ];

    /**
     * @var array Danish street's address formats.
     */
    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}',
        '{{streetName}} {{buildingNumber}}, {{buildingLevel}}',
        '{{streetName}} {{buildingNumber}}, {{buildingLevel}} {{buildingSide}}',
    ];

    /**
     * @var array Danish address format.
     */
    protected static $addressFormats = [
        "{{streetAddress}}\n{{postcode}} {{city}}",
    ];

    /**
     * Randomly return a real city name.
     *
     * @return string
     */
    public static function cityName()
    {
        return static::randomElement(static::$cityNames);
    }

    /**
     * Randomly return a suffix word.
     *
     * @return string
     */
    public static function streetSuffixWord()
    {
        return static::randomElement(static::$streetSuffixWord);
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

    /**
     * Randomly return a building level.
     *
     * @return string
     */
    public static function buildingLevel()
    {
        return static::numerify(static::randomElement(static::$buildingLevel));
    }

    /**
     * Randomly return a side of the building.
     *
     * @return string
     */
    public static function buildingSide()
    {
        return static::randomElement(static::$buildingSide);
    }

    /**
     * Randomly return a real municipality name, called 'kommune' in danish.
     *
     * @return string
     */
    public static function kommune()
    {
        return static::randomElement(static::$kommuneNames);
    }

    /**
     * Randomly return a real region name.
     *
     * @return string
     */
    public static function region()
    {
        return static::randomElement(static::$regionNames);
    }
}
