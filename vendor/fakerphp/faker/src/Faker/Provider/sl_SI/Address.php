<?php

namespace Faker\Provider\sl_SI;

class Address extends \Faker\Provider\Address
{
    /**
     * @see http://www.rtvslo.si/strani/abecedni-seznam-obcin/3103
     */
    protected static $city = [
        'Ajdovščina', 'Apače', 'Beltinci', 'Benedikt', 'Bistrica ob Sotli', 'Bled', 'Bloke', 'Bohinj', 'Borovnica', 'Bovec',
        'Braslovče', 'Brda', 'Brezovica', 'Brežice', 'Cankova', 'Celje', 'Cerklje na Gorenjskem', 'Cerknica', 'Cerkno',
        'Cerkvenjak', 'Cirkulane', 'Destrnik', 'Divača', 'Dobje', 'Dobrepolje', 'Dobrna', 'Dobrova - Polhov Gradec', 'Dobrovnik',
        'Dol pri Ljubljani', 'Dolenjske Toplice', 'Domžale', 'Dornava', 'Dravograd', 'Duplek', 'Gorenja vas - Poljane',
        'Gorišnica', 'Gorje', 'Gornja Radgona', 'Gornji Grad', 'Gornji Petrovci', 'Grad', 'Grosuplje', 'Hajdina', 'Hodoš',
        'Horjul', 'Hoče - Slivnica', 'Hrastnik', 'Hrpelje - Kozina', 'Idrija', 'Ig', 'Ilirska Bistrica', 'Ivančna Gorica',
        'Izola', 'Jesenice', 'Jezersko', 'Juršinci', 'Kamnik', 'Kanal ob Soči', 'Kidričevo', 'Kobarid', 'Kobilje', 'Komen',
        'Komenda', 'Koper', 'Kostanjevica na Krki', 'Kostel', 'Kozje', 'Kočevje', 'Kranj', 'Kranjska Gora', 'Križevci', 'Krško',
        'Kungota', 'Kuzma', 'Laško', 'Lenart', 'Lendava', 'Litija', 'Ljubljana', 'Ljubno', 'Ljutomer', 'Log - Dragomer', 'Logatec',
        'Lovrenc na Pohorju', 'Loška Dolina', 'Loški Potok', 'Lukovica', 'Luče', 'Majšperk', 'Makole', 'Maribor', 'Markovci',
        'Medvode', 'Mengeš', 'Metlika', 'Mežica', 'Miklavž na Dravskem polju', 'Miren - Kostanjevica', 'Mirna Peč', 'Mislinja',
        'Mokronog - Trebelno', 'Moravske Toplice', 'Moravče', 'Mozirje', 'Murska Sobota', 'Muta', 'Naklo', 'Nazarje', 'Nova Gorica',
        'Novo mesto', 'Odranci', 'Oplotnica', 'Ormož', 'Osilnica', 'Pesnica', 'Piran', 'Pivka', 'Podlehnik', 'Podvelka',
        'Podčetrtek', 'Poljčane', 'Polzela', 'Postojna', 'Prebold', 'Preddvor', 'Prevalje', 'Ptuj', 'Puconci', 'Radenci', 'Radeče',
        'Radlje ob Dravi', 'Radovljica', 'Ravne na Koroškem', 'Razkrižje', 'Rače - Fram', 'Renče - Vogrsko', 'Rečica ob Savinji',
        'Ribnica na Pohorju', 'Ribnica', 'Rogatec', 'Rogaška Slatina', 'Rogašovci', 'Ruše', 'Selnica ob Dravi', 'Semič', 'Sevnica',
        'Sežana', 'Slovenj Gradec', 'Slovenska Bistrica', 'Slovenske Konjice', 'Sodražica', 'Solčava', 'Središče ob Dravi', 'Starše',
        'Straža', 'Sveta Ana', 'Sveta Trojica v Slovenskih goricah', 'Sveti Andraž v Slovenskih goricah', 'Sveti Jurij ob Ščavnici',
        'Sveti Jurij v Slovenskih goricah', 'Sveti Tomaž', 'Tabor', 'Tišina', 'Tolmin', 'Trbovlje', 'Trebnje', 'Trnovska vas',
        'Trzin', 'Tržič', 'Turnišče', 'Velenje', 'Velika Polana', 'Velike Lašče', 'Veržej', 'Videm', 'Vipava', 'Vitanje', 'Vodice',
        'Vojnik', 'Vransko', 'Vrhnika', 'Vuzenica', 'Zagorje ob Savi', 'Zavrč', 'Zreče', 'Črenšovci', 'Črna na Koroškem', 'Črnomelj',
        'Šalovci', 'Šempeter - Vrtojba', 'Šentilj', 'Šentjernej', 'Šentjur', 'Šentrupert', 'Šenčur', 'Škocjan', 'Škofja Loka',
        'Škofljica', 'Šmarje pri Jelšah', 'Šmarješke Toplice', 'Šmartno ob Paki', 'Šmartno pri Litiji', 'Šoštanj', 'Štore', 'Žalec',
        'Železniki', 'Žetale', 'Žiri', 'Žirovnica', 'Žužemberk',
    ];

    protected static $buildingNumber = ['%##', '%#', '%#', '%#', '%#', '%'];

    protected static $postcode = ['###0'];

    /**
     * Most common street names in Slovenia
     *
     * @see http://www.stat.si/krajevnaimena/pregledi_ulice_najpogostejse.asp
     * @see http://www.stat.si/KrajevnaImena/pregledi_naselja_najpogostejsa.asp
     */
    protected static $street = [
        'Šolska ulica', 'Prešernova ulica', 'Cankarjeva ulica', 'Vrtna ulica', 'Gregorčičeva ulica', 'Kajuhova ulica', 'Prečna ulica',
        'Levstikova ulica', 'Trubarjeva ulica', 'Mladinska ulica', 'Gubčeva ulica', 'Ljubljanska cesta', 'Partizanska ulica', 'Maistrova ulica',
        'Rožna ulica', 'Bevkova ulica', 'Jurčičeva ulica', 'Župančičeva ulica', 'Kolodvorska ulica', 'Partizanska cesta', 'Gasilska ulica',
        'Kidričeva ulica', 'Aškerčeva ulica', 'Kratka ulica', 'Nova ulica', 'Obrtniška ulica', 'Tomšičeva ulica', 'Cvetlična ulica',
        'Mariborska cesta', 'Ob potoku', 'Trg svobode', 'Ulica talcev', 'Kettejeva ulica', 'Kosovelova ulica', 'Finžgarjeva ulica', 'Ob gozdu',
        'Stara cesta', 'Vegova ulica', 'Prežihova ulica', 'Sončna ulica',

        'Gradišče', 'Pristava', 'Brezje', 'Dolenja vas', 'Potok', 'Ravne',
        'Brdo', 'Dobrava', 'Draga', 'Javorje', 'Kal', 'Laze', 'Log', 'Planina', 'Podkraj', 'Selce', 'Trnovec', 'Bistrica', 'Gorenja vas',
        'Gorica', 'Lipa', 'Nova vas', 'Podgora', 'Podgorje', 'Podgrad', 'Ponikve', 'Sela', 'Selo', 'Škocjan', 'Vrh',
    ];

    /**
     * @see http://sl.wikipedia.org/wiki/Seznam_suverenih_držav
     */
    protected static $country = [
        'Afganistan', 'Albanija', 'Alžirija', 'Andora', 'Angola', 'Antigva in Barbuda', 'Argentina', 'Armenija', 'Avstralija', 'Avstrija',
        'Azerbajdžan', 'Bahami', 'Bahrajn', 'Bangladeš', 'Barbados', 'Belgija', 'Belize', 'Belorusija', 'Benin', 'Bocvana', 'Bolgarija',
        'Bolivija', 'Bosna in Hercegovina', 'Brazilija', 'Brunej', 'Burkina Faso', 'Burundi', 'Butan', 'Ciper', 'Čad', 'Češka', 'Čile',
        'Črna gora', 'Danska', 'Dominika', 'Dominikanska republika', 'Džibuti', 'Egipt', 'Ekvador', 'Ekvatorialna Gvineja', 'Eritreja',
        'Estonija', 'Etiopija', 'Fidži', 'Filipini', 'Finska', 'Francija', 'Gabon', 'Gambija', 'Gana', 'Grčija', 'Grenada', 'Gruzija',
        'Gvajana', 'Gvatemala', 'Gvineja', 'Gvineja Bissau', 'Haiti', 'Honduras', 'Hrvaška', 'Indija', 'Indonezija', 'Irak', 'Iran', 'Irska',
        'Islandija', 'Italija', 'Izrael', 'Jamajka', 'Japonska', 'Jemen', 'Jordanija', 'Južna Afrika', 'Južna Koreja', 'Kambodža', 'Kamerun',
        'Kanada', 'Katar', 'Kazahstan', 'Kenija', 'Kirgizistan', 'Kiribati', 'Kitajska', 'Kolumbija', 'Komori', 'Kongo', 'Demokratična republika Kongo',
        'Kostarika', 'Kuba', 'Kuvajt', 'Laos', 'Latvija', 'Lesoto', 'Libanon', 'Liberija', 'Libija', 'Lihtenštajn', 'Litva', 'Luksemburg', 'Madagaskar',
        'Madžarska', 'Makedonija', 'Malavi', 'Maldivi', 'Malezija', 'Mali', 'Malta', 'Maroko', 'Marshallovi otoki', 'Mauritius', 'Mavretanija', 'Mehika',
        'Mikronezija', 'Mjanmar', 'Moldavija', 'Monako', 'Mongolija', 'Mozambik', 'Namibija', 'Nauru', 'Nemčija', 'Nepal', 'Niger', 'Nigerija',
        'Nikaragva', 'Nizozemska', 'Norveška', 'Nova Zelandija', 'Oman', 'Pakistan', 'Palau', 'Panama', 'Papua Nova Gvineja', 'Paragvaj', 'Peru',
        'Poljska', 'Portugalska', 'Romunija', 'Ruanda', 'Rusija', 'Saint Kitts in Nevis', 'Saint Lucia', 'Saint Vincent in Grenadine',
        'Salomonovi otoki', 'Salvador', 'San Marino', 'Sao Tome in Principe', 'Saudova Arabija', 'Sejšeli', 'Senegal', 'Severna Koreja', 'Sierra Leone',
        'Singapur', 'Sirija', 'Slonokoščena obala', 'Slovaška', 'Slovenija', 'Somalija', 'Srbija', 'Srednjeafriška republika', 'Sudan', 'Surinam',
        'Svazi', 'Španija', 'Šrilanka', 'Švedska', 'Švica', 'Tadžikistan', 'Tajska', 'Tajvan', 'Tanzanija', 'Togo', 'Tonga', 'Trinidad in Tobago',
        'Tunizija', 'Turčija', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Ukrajina', 'Urugvaj', 'Uzbekistan', 'Vanuatu', 'Vatikan', 'Velika Britanija',
        'Venezuela', 'Vietnam', 'Vzhodni Timor', 'Zahodna Samoa', 'Zambija', 'Združene države Amerike', 'Združeni arabski emirati',
        'Zelenortski otoki', 'Zimbabve',
    ];

    protected static $cityFormats = [
        '{{cityName}}',
    ];

    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}',
    ];

    protected static $addressFormats = [
        '{{streetAddress}}\n {{postcode}}\n {{cityName}}',
    ];

    public static function cityName()
    {
        return static::randomElement(static::$city);
    }

    public function streetName()
    {
        return static::randomElement(static::$street);
    }
}
