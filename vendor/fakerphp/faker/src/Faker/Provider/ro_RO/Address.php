<?php

namespace Faker\Provider\ro_RO;

class Address extends \Faker\Provider\Address
{
    protected static $buildingNumber = ['%##', '%#', '%', '%/#', '#A', '#B'];
    protected static $apartmentNumber = ['#', '##'];
    protected static $floor = ['#', '##'];
    protected static $block = ['#', '##', 'A', 'B', 'C', 'D'];
    protected static $blockSegment = ['A', 'B', 'C', 'D'];

    protected static $streetPrefix = [
        'Str.', 'B-dul.', 'Aleea', 'Splaiul', 'Calea', 'P-ța',
    ];

    // random selection of seemingly frequently used streets and naming categories
    protected static $streetPlainName = [
        // historical events
        'Eroilor', 'Independenței', 'Memorandumului', 'Unirii', '1 Decembrie',
        // historical people
        'Mihai Viteazul', 'Mircea cel Bătrân', 'Vlad Țepeș', 'Traian', 'Decebal', 'Horea', 'Cloșca', 'Crișan',
        // national and international people names
        'Louis Pasteur', 'Albert Einstein', 'Franklin Delano Rosevelt', 'J.J Rousseau', 'Petrache Poenaru', 'Henri Coandă', 'Constantin Brâncuși', 'Aurel Vlaicu', 'Ion Creangă', 'Mihai Eminescu',
        // nature-related
        'Cireșilor', 'Frasinului', 'Salcâmilor', 'Brăduțului', 'Frunzișului', 'Castanilor', 'Mesteacănului', 'Florilor', 'Pădurii', 'Piersicului',
        // work-related
        'Croitorilor', 'Meșterilor', 'Zidarilor', 'Păcurari', 'Muncii', 'Învățătorului',
        // geography related
        'Jiului', 'Bega', 'Someș', 'Făget', 'Sinaia', 'Herculane', 'Padiș',
    ];

    protected static $postcode = ['######'];

    // from http://ro.wikipedia.org/wiki/Lista_ora%C8%99elor_din_Rom%C3%A2nia#Lista_alfabetic.C4.83_a_ora.C8.99elor_din_Rom.C3.A2nia_.28inclusiv_municipii.29
    protected static $cityNames = [
        'Abrud', 'Adjud', 'Agnita', 'Aiud', 'Alba Iulia', 'Aleșd', 'Alexandria', 'Amara', 'Anina', 'Aninoasa', 'Arad', 'Ardud', 'Avrig', 'Azuga', 'Babadag', 'Băbeni', 'Bacău', 'Baia de Aramă',
        'Baia de Arieș', 'Baia Mare', 'Baia Sprie', 'Băicoi', 'Băile Govora', 'Băile Herculane', 'Băile Olănești', 'Băile Tușnad', 'Băilești', 'Bălan', 'Bălcești', 'Balș', 'Băneasa', 'Baraolt',
        'Bârlad', 'Bechet', 'Beclean', 'Beiuș', 'Berbești', 'Berești', 'Bicaz', 'Bistrița', 'Blaj', 'Bocșa', 'Boldești-Scăeni', 'Bolintin-Vale', 'Borșa', 'Borsec', 'Botoșani', 'Brad', 'Bragadiru',
        'Brăila', 'Brașov', 'Breaza', 'Brezoi', 'Broșteni', 'Bucecea', 'București', 'Budești', 'Buftea', 'Buhuși', 'Bumbești-Jiu', 'Bușteni', 'Buzău', 'Buziaș', 'Cajvana', 'Calafat', 'Călan',
        'Călărași', 'Călimănești', 'Câmpeni', 'Câmpia Turzii', 'Câmpina', 'Câmpulung Moldovenesc', 'Câmpulung', 'Caracal', 'Caransebeș', 'Carei', 'Cavnic', 'Căzănești', 'Cehu Silvaniei',
        'Cernavodă', 'Chișineu-Criș', 'Chitila', 'Ciacova', 'Cisnădie', 'Cluj-Napoca', 'Codlea', 'Comănești', 'Comarnic', 'Constanța', 'Copșa Mică', 'Corabia', 'Costești', 'Covasna', 'Craiova',
        'Cristuru Secuiesc', 'Cugir', 'Curtea de Argeș', 'Curtici', 'Dăbuleni', 'Darabani', 'Dărmănești', 'Dej', 'Deta', 'Deva', 'Dolhasca', 'Dorohoi', 'Drăgănești-Olt', 'Drăgășani', 'Dragomirești',
        'Drobeta-Turnu Severin', 'Dumbrăveni', 'Eforie', 'Făgăraș', 'Făget', 'Fălticeni', 'Făurei', 'Fetești', 'Fieni', 'Fierbinți-Târg', 'Filiași', 'Flămânzi', 'Focșani', 'Frasin', 'Fundulea',
        'Găești', 'Galați', 'Gătaia', 'Geoagiu', 'Gheorgheni', 'Gherla', 'Ghimbav', 'Giurgiu', 'Gura Humorului', 'Hârlău', 'Hârșova', 'Hațeg', 'Horezu', 'Huedin', 'Hunedoara', 'Huși', 'Ianca',
        'Iași', 'Iernut', 'Ineu', 'Însurăței', 'Întorsura Buzăului', 'Isaccea', 'Jibou', 'Jimbolia', 'Lehliu Gară', 'Lipova', 'Liteni', 'Livada', 'Luduș', 'Lugoj', 'Lupeni', 'Măcin', 'Măgurele',
        'Mangalia', 'Mărășești', 'Marghita', 'Medgidia', 'Mediaș', 'Miercurea Ciuc', 'Miercurea Nirajului', 'Miercurea Sibiului', 'Mihăilești', 'Milișăuți', 'Mioveni', 'Mizil', 'Moinești',
        'Moldova Nouă', 'Moreni', 'Motru', 'Murfatlar', 'Murgeni', 'Nădlac', 'Năsăud', 'Năvodari', 'Negrești', 'Negrești-Oaș', 'Negru Vodă', 'Nehoiu', 'Novaci', 'Nucet', 'Ocna Mureș',
        'Ocna Sibiului', 'Ocnele Mari', 'Odobești', 'Odorheiu Secuiesc', 'Oltenița', 'Onești', 'Oradea', 'Orăștie', 'Oravița', 'Orșova', 'Oțelu Roșu', 'Otopeni', 'Ovidiu', 'Panciu', 'Pâncota',
        'Pantelimon', 'Pașcani', 'Pătârlagele', 'Pecica', 'Petrila', 'Petroșani', 'Piatra Neamț', 'Piatra-Olt', 'Pitești', 'Ploiești', 'Plopeni', 'Podu Iloaiei', 'Pogoanele', 'Popești-Leordeni',
        'Potcoava', 'Predeal', 'Pucioasa', 'Răcari', 'Rădăuți', 'Râmnicu Sărat', 'Râșnov', 'Recaș', 'Reghin', 'Reșița', 'Roman', 'Roșiorii de Vede', 'Rovinari', 'Roznov', 'Rupea', 'Săcele',
        'Săcueni', 'Salcea', 'Săliște', 'Săliștea de Sus', 'Salonta', 'Sângeorgiu de Pădure', 'Sângeorz-Băi', 'Sânnicolau Mare', 'Sântana', 'Sărmașu', 'Satu Mare', 'Săveni', 'Scornicești',
        'Sebeș', 'Sebiș', 'Segarcea', 'Seini', 'Sfântu Gheorghe', 'Sibiu', 'Sighetu Marmației', 'Sighișoara', 'Simeria', 'Șimleu Silvaniei', 'Sinaia', 'Siret', 'Slănic', 'Slănic-Moldova',
        'Slatina', 'Slobozia', 'Solca', 'Șomcuta Mare', 'Sovata', 'Ștefănești, Argeș', 'Ștefănești, Botoșani', 'Ștei', 'Strehaia', 'Suceava', 'Sulina', 'Tălmaciu', 'Țăndărei', 'Târgoviște',
        'Târgu Bujor', 'Târgu Cărbunești', 'Târgu Frumos', 'Târgu Jiu', 'Târgu Lăpuș', 'Târgu Mureș', 'Târgu Neamț', 'Târgu Ocna', 'Târgu Secuiesc', 'Târnăveni', 'Tășnad', 'Tăuții-Măgherăuș',
        'Techirghiol', 'Tecuci', 'Teiuș', 'Țicleni', 'Timișoara', 'Tismana', 'Titu', 'Toplița', 'Topoloveni', 'Tulcea', 'Turceni', 'Turda', 'Turnu Măgurele', 'Ulmeni', 'Ungheni', 'Uricani',
        'Urlați', 'Urziceni', 'Valea lui Mihai', 'Vălenii de Munte', 'Vânju Mare', 'Vașcău', 'Vaslui', 'Vatra Dornei', 'Vicovu de Sus', 'Victoria', 'Videle', 'Vișeu de Sus', 'Vlăhița', 'Voluntari',
        'Vulcan', 'Zalău', 'Zărnești', 'Zimnicea', 'Zlatna',
    ];

    // http://en.wikipedia.org/wiki/Counties_of_Romania#Current_list
    protected static $counties = [
        'Alba', 'Arad', 'Argeș', 'Bacău', 'Bihor', 'Bistrița Năsăud', 'Botoșani', 'Brăila', 'Brașov', 'București', 'Buzău', 'Călărași', 'Caraș-Severin', 'Cluj', 'Constanța', 'Covasna', 'Dâmbovița',
        'Dolj', 'Galați', 'Giurgiu', 'Gorj', 'Harghita', 'Hunedoara', 'Ialomița', 'Iași', 'Ilfov', 'Maramureț', 'Mehedinți', 'Mureș', 'Neamț', 'Olt', 'Prahova', 'Sălaj', 'Satu Mare', 'Sibiu',
        'Suceava', 'Teleorman', 'Timiș', 'Tulcea', 'Vâlcea', 'Vaslui', 'Vrancea',
    ];

    // http://ro.wikipedia.org/wiki/Lista_statelor_lumii#Lista_statelor_lumii
    protected static $country = [
        'Afganistan', 'Africa de Sud', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua și Barbuda', 'Arabia Saudită', 'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaidjan',
        'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgia', 'Belize', 'Benin', 'Bhutan', 'Birmania', 'Bolivia', 'Bosnia și Herțegovina', 'Botswana', 'Brazilia', 'Brunei', 'Bulgaria',
        'Burkina Faso', 'Burundi', 'Cambodgia', 'Camerun', 'Canada', 'Capul Verde', 'Cehia', 'Republica Centrafricană', 'Chile', 'Republica Populară Chineză', 'Ciad', 'Cipru', 'Columbia', 'Comore',
        'Republica Democrată Congo', 'Republica Congo', 'Coreea de Nord', 'Coreea de Sud', 'Costa Rica', 'Coasta de Fildeș', 'Croația', 'Cuba', 'Danemarca', 'Djibouti', 'Dominica',
        'Republica Dominicană', 'Ecuador', 'Egipt', 'El Salvador', 'Elveția', 'Emiratele Arabe Unite', 'Eritreea', 'Estonia', 'Etiopia', 'Fiji', 'Filipine', 'Finlanda', 'Franța', 'Gabon', 'Gambia',
        'Georgia', 'Germania', 'Ghana', 'Grecia', 'Grenada', 'Guatemala', 'Guineea', 'Guineea-Bissau', 'Guineea Ecuatorială', 'Guyana', 'Haiti', 'Honduras',
        'India', 'Indonezia', 'Iordania', 'Irak', 'Iran', 'Republica Irlanda', 'Islanda', 'Israel', 'Italia', 'Jamaica', 'Japonia', 'Kazahstan', 'Kenya', 'Kirghizstan', 'Kiribati', 'Kuweit',
        'Laos', 'Lesotho', 'Letonia', 'Liban', 'Liberia', 'Libia', 'Liechtenstein', 'Lituania', 'Luxemburg', 'Republica Macedonia', 'Madagascar', 'Malawi', 'Malaezia', 'Maldive', 'Mali', 'Malta',
        'Maroc', 'Insulele Marshall', 'Mauritania', 'Mauritius', 'Mexic', 'Statele Federate ale Microneziei', 'Republica Moldova', 'Monaco', 'Mongolia', 'Mozambic', 'Muntenegru', 'Namibia', 'Nauru',
        'Nepal', 'Nicaragua', 'Niger', 'Nigeria', 'Norvegia', 'Noua Zeelandă', 'Olanda', 'Oman', 'Pakistan', 'Palau', 'Panama', 'Papua Noua Guinee', 'Paraguay', 'Peru', 'Polonia', 'Portugalia',
        'Qatar', 'Regatul Unit', 'România', 'Rusia', 'Rwanda', 'Samoa', 'San Marino', 'São Tomé și Príncipe', 'São Tomé e Príncipe', 'Senegal', 'Serbia', 'Seychelles', 'Sfânta Lucia',
        'Sfântul Cristofor și Nevis', 'Saint Vincent and the Grenadines', 'Sierra Leone', 'Singapore', 'Siria', 'Slovacia', 'Slovenia', 'Insulele Solomon', 'Somalia', 'Spania', 'Sri Lanka',
        'Statele Unite ale Americii', 'Sudan', 'Sudanul de Sud', 'Suedia', 'Surinam', 'Swaziland', 'Tadjikistan', 'Tanzania', 'Thailanda', 'Timorul de Est', 'Togo', 'Tonga', 'Trinidad-Tobago',
        'Tunisia', 'Turcia', 'Turkmenistan', 'Tuvalu', 'Ucraina', 'Uganda', 'Ungaria', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican', 'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe',
    ];

    protected static $cityFormats = [
        '{{cityName}}',
        'Mun. {{cityName}}',
    ];

    protected static $streetNameFormats = [
        '{{streetPrefix}} {{streetPlainName}}',
    ];

    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}',
        '{{streetName}} {{buildingNumber}}',
        '{{streetName}} {{buildingNumber}}',
        '{{streetName}} nr. {{buildingNumber}}, bl. {{block}}, ap. {{apartmentNumber}}',
        '{{streetName}} nr. {{buildingNumber}}, bl. {{block}}, et. {{floor}}, ap. {{apartmentNumber}}',
        '{{streetName}} nr. {{buildingNumber}}, bl. {{block}}, sc. {{blockSegment}}, et. {{floor}}, ap. {{apartmentNumber}}',
    ];

    protected static $addressFormats = [
        '{{streetAddress}}, {{city}}, {{county}}, CP {{postcode}}',
    ];

    public function cityName()
    {
        return static::randomElement(static::$cityNames);
    }

    public static function block()
    {
        return static::numerify(static::randomElement(static::$block));
    }

    public function blockSegment()
    {
        return static::randomElement(static::$blockSegment);
    }

    public static function floor()
    {
        return static::numerify(static::randomElement(static::$floor));
    }

    public static function apartmentNumber()
    {
        return static::numerify(static::randomElement(static::$apartmentNumber));
    }

    public function streetPrefix()
    {
        return static::randomElement(static::$streetPrefix);
    }

    /**
     * @example 'Independenței'
     */
    public function streetPlainName()
    {
        return static::randomElement(static::$streetPlainName);
    }

    /**
     * @example 'Cluj'
     */
    public function county()
    {
        return static::randomElement(static::$counties);
    }
}
