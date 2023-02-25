<?php

namespace Faker\Provider\me_ME;

class Address extends \Faker\Provider\Address
{
    protected static $postcode = ['#####'];

    protected static $streetPrefix = [
        '',
    ];

    /**
     * @see http://podgorica.mapa.in.rs/
     */
    protected static $street = [
        '1. crnogorske brigade narodne odbrane', '1. maja', '1. proleterske brigade', '10. crnogorske brigade', '13. jula', '18. februara', '18. jula', '19. decembra', '2. crnogorskog bataljona', '2. proleterske dalmatinske brigade', '27. marta', '3. sandžačke proleterske brigade', '4. jula', '4. proleterske brigade', '5. proleterske brigade', '6. crnogorske udarne brigade', '7. omladinske brigade', '8. crnogorske udarne brigade', '8. jula', '8. marta', '9. crnogorske brigade',
        'Admirala Zmajevića', 'Aerodromska', 'Aleksandra Ace Prijića', 'Aleksandra Lesa Ivanovića', 'Aleksandra Puškina', 'Alekse Šantića', 'Alfreda Tenisona', 'Andrije Paltašića', 'Andrijevička', 'Antona Čehova', 'Arhitekte Milana Popovića', 'Arsenija Čarnojevića', 'Atinska', 'AVNOJ-a',
        'Balkanska', 'Balšića', 'Barska', 'Belvederska', 'Beogradska', 'Berska', 'Bjelasička', 'Bjelopoljska', 'Blaža Jovanovića', 'Bohinjska', 'Bokeljske mornarice', 'Bokeška', 'Bore i Ramiza', 'Borisa Kidriča', 'Boška Buhe', 'Botunska', 'Bracana Bracanovića', 'Braće Ribar', 'Branislava Lekića', 'Branka Ćopića', 'Branka Deletića', 'Branka Radičevića', 'Bratonožićka', 'Bratstva i jedinstva', 'Bregalnička', 'Buda Tomovića', 'Budvanska', 'Bulevar Džordža Vašingtona', 'Bulevar Ivana Crnojevića', 'Bulevar Mihaila Lalića', 'Bulevar revolucije', 'Bulevar Save Kovačevića',
        'Cara Lazara', 'Carev laz', 'Ceklinska', 'Cetinjski put', 'Crnogorskih serdara', 'Crnojevića', 'Cvijetna',
        'Dajbabska', 'Dalmatinska', 'Danilovgradska', 'Desanke Maksimović', 'Dositeja Obradovića', 'Dr Blaža Raičevića', 'Dr Filipa Šoća', 'Dr Milutina Kažića', 'Dr Nika Miljanića', 'Dr Saše Božovića', 'Drvarska', 'Dukljanska', 'Dunavska', 'Durmitorska', 'Dušana Duće Mugoše', 'Dušana Milutinovića', 'Dušana Vukotića', 'Džan', 'Đečevića', 'Đoka Miraševića', 'Đuje Jovanovića', 'Đure Daničića',
        'Emila Zole', 'Franca Prešerna', 'Franca Rozmana', 'Fruškogorska', 'Fundinske bitke',
        'Gavra Vukovića', 'Gavrila Principa', 'Generala Sava Orlovića', 'Georgi Dimitrova', 'Geteova', 'Goce Delčeva', 'Gojka Radonjića', 'Goranska', 'Gorička', 'Grahovačka',
        'Hajduk Veljkova', 'Hercegnovska', 'Hercegovačka', 'Husinskih rudara',
        'Igmanska', 'Ilije Milačića', 'Isidore Sekulić', 'Ivana Cankara', 'Ivana Gorana Kovačića', 'Ivana Milutinovića', 'Ivana Vujoševića', 'Ivangradska', 'Ive Andrića', 'Iveze Vukova',
        'Jadranska', 'Janka Đanovića', 'Janka Vukotića', 'Jaroslava Čermaka', 'Jelene Balšić', 'Jerevanska', 'Jezerska', 'Josipa Broza Tita', 'Jovana Cvijića', 'Jovana Ćetkovića', 'Jovana Tomaševića',
        'Kadinjača', 'Karađorđeva', 'Kninska', 'KNOJ-a', 'Kolašinska', 'Komska', 'Kosmajska', 'Kosovska', 'Kosovskih junaka', 'Koste Racina', 'Kotorska', 'Kozaračka', 'Kragujevačka', 'Kralja Nikole', 'Kraljevačka',
        'Lamela', 'Lazara Sočice', 'Lička', 'Lovćenska', 'Ludviga Kube', 'Luke Boljevića', 'Lutovačkih barjaktara', 'Ljesanska', 'Ljeskopoljska', 'Ljube Čupića', 'Ljube Nenadovića', 'Ljubljanska', 'Ljubostinjskih junaka', 'Ljubovićka',
        'Majevička', 'Manastirska', 'Marka Mašanovića', 'Marka Miljanova', 'Matije Gupca', 'Mediteranska', 'Medunska', 'Meše Selimovića', 'Mila Milunovića', 'Mila Peruničića', 'Mila Radunovića', 'Miladina Popovića', 'Milana Kuča', 'Milana Raičkovića', 'Miloja Pavlovića', 'Miloša Obilića', 'Miljana Vukova', 'Miodraga Bulatovića', 'Mirka Banjevića', 'Mirka Vešovića', 'Mitra Bakića', 'Mojkovačka', 'Mojsija Zečevića', 'Moračka', 'Moskovska', 'Moskovski most', 'Mosorska', 'Most Milenijum', 'Most žrtava 5. maja 1944.', 'Mušikića',
        'Neznanih junaka', 'Nikca od Rovina', 'Nikole Đurkovića', 'Nikole Lopičića', 'Nikole Tesle', 'Nikšićka', 'Novaka Miloševa', 'Novaka Ramova', 'Novosadska', 'Njegoševa',
        'Obala Ribnice', 'Obodska', 'Ohridska', 'Oktobarske revolucije', 'Omera Abdovića', 'Omladinskih brigada', 'Orijenska',
        'Pariske komune', 'Partizanski put', 'Pera Počeka', 'Perojska', 'Petra Kočića', 'Petra Lubarde', 'Petra Prlje', 'Pilota Cvetkovića i Milojevića', 'Piperska', 'Pivska', 'Plavska', 'Plitvička', 'Plužinska', 'Pljevaljska', 'Pohorska', 'Polimska', 'Popa Boška Popovića', 'Predraga Golubovića', 'Princa Mihaila Petrovića', 'Prištinska', 'Prolaz Generala Dožića',
        'Radnička', 'Radoja Jovanovića', 'Radomira Ivanovića', 'Radosava Burića', 'Radosava Popovića', 'Radovana Petrovića', 'Radovana Vukanovića', 'Radovana Zogovića', 'Radula Rusa Radulovića', 'Rista Stijovića', 'Rogamska', 'Rovačka', 'Ruža',
        'Sarajevska', 'Sava Lubarde', 'Sava Nikolića', 'Savska', 'Serdara Jola Piletića', 'Sergeja Jesenjina', 'Sime Matavulja', 'Simona Ivanova', 'Sitnička', 'Skadarska', 'SKOJ-a', 'Skopska', 'Slavonska', 'Slobodana Škerovića', 'Slobode', 'Sloge', 'Spasa Nikolića', 'Spasoja Raspopovića', 'Srednjoškolska', 'Stanka Dragojevića', 'Stefana Mitrova Ljubiše', 'Steva Boljevića', 'Steva Kraljevića', 'Studentska', 'Svetog Petra Cetinjskog', 'Svetozara Markovića', 'Šarkića', 'Šavnička', 'Španskih boraca', 'Špira Mugoše',
        'Tivatska', 'Trebinjska', 'Trg Božane Vučinić', 'Trg golootočkih žrtava', 'Trg Nikole Kovačevića', 'Trg republike', 'Trifuna Đukića', 'Triglavska', 'Tripa Kukolja', 'Tuška',
        'Ulcinjska', 'Užička',
        'Valtazara Bogišića', 'Vardarska', 'Vasa Raičkovića', 'Velimira Stojanovića', 'Velimira Terzića', 'Veljka Jankovića', 'Vezirov most', 'Vinogradska', 'Vitomira Vita Nikolića', 'Vlada Ćetkovića', 'Vlada Martinovića', 'Vladike Danila', 'Vladike Petra I', 'Vladike Vasilija Petrovića', 'Vojisavljevića', 'Vojislava Grujića', 'Vojvode Ilije Plamenca', 'Vojvode Mijajla Nišina', 'Vojvode Mirka Petrovića', 'Vojvode Raduna', 'Vojvode Vase Bracanova', 'Vojvođanska', 'Vrela 2.', 'Vrela 3.', 'Vrela 4.', 'Vrela 5.', 'Vrela 6.', 'Vučedolska', 'Vuka Đurovića', 'Vuka Karadžića', 'Vuka Mandušića', 'Vuka Mićunovića', 'Vukice Mitrović', 'Vukosava Božovića',
        'Zagrebačka', 'Zetskih vladara', 'Zetskog odreda', 'Zmaj Jovina', 'Žabljačka', 'Žarka Zrenjanina', 'Žikice Jovanovića Španca', 'Žrtava fašizma',
    ];

    protected static $streetNameFormats = [
        '{{street}}',
        '{{streetPrefix}} {{street}}',
    ];

    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}',
    ];

    protected static $cityFormats = [
        '{{cityName}}',
    ];

    /**
     * @see http://sh.wikipedia.org/wiki/Popis_gradova_u_Crnoj_Gori
     */
    protected static $cityNames = [
        'Bar', 'Budva', 'Herceg Novi',
        'Kotor', 'Tivat', 'Ulcinj', 'Podgorica',
        'Cetinje', 'Nikšić', 'Danilovgrad', 'Žabljak',
        'Kolašin', 'Andrijevica', 'Berane', 'Bijelo Polje',
        'Mojkovac', 'Plav', 'Plužine', 'Pljevlja', 'Rožaje',
        'Šavnik', 'Petnjica', 'Gusinje', 'Petrovac', 'Sutomore',
    ];

    /**
     * @see https://github.com/umpirsky/country-list/blob/master/country/cldr/sr_Latn/country.php
     */
    protected static $country = [
        'Alandska ostrva', 'Albanija', 'Alžir', 'Američka Samoa', 'Andora', 'Angola', 'Angvila', 'Antarktika', 'Antigva i Barbuda', 'Argentina', 'Armenija', 'Aruba', 'Australija', 'Austrija', 'Avganistan', 'Azerbejdžan',
        'Bahami', 'Bahrein', 'Bangladeš', 'Barbados', 'Belgija', 'Belise', 'Belorusija', 'Benin', 'Bermuda', 'Bocvana', 'Bolivija', 'Bosna i Hercegovina', 'Božićna Ostrva', 'Brazil', 'Britanska Devičanska Ostrva', 'Britansko Indijska Okeanska Teritorija', 'Brunej', 'Bugarska', 'Burkina Faso', 'Burundi', 'Butan', 'Buve Ostrva',
        'Čad', 'Centralno Afrička Republika', 'Češka', 'Čile',
        'Crna Gora',
        'Danska', 'Demokratska Republika Kongo', 'Dijego Garsija', 'Dominika', 'Dominikanska Republika',
        'Džersi', 'Džibuti',
        'Egipat', 'Ekvador', 'Ekvatorijalna Gvineja', 'Eritreja', 'Estonija', 'Etiopija', 'Evropska unija',
        'Farska Ostrva', 'Fidži', 'Filipini', 'Finska', 'Folklandska Ostrva', 'Francuska', 'Francuska Gvajana', 'Francuska Polinezija', 'Francuske Južne Teritorije',
        'Gabon', 'Gambija', 'Gana', 'Gibraltar', 'Grčka', 'Grenada', 'Grenland', 'Gruzija', 'Guam', 'Gurnsi', 'Gvadelupe', 'Gvajana', 'Gvatemala', 'Gvineja', 'Gvineja-Bisao',
        'Haiti', 'Herd i Mekdonald Ostrva', 'Holandija', 'Holandski Antili', 'Honduras', 'Hong Kong (S. A. R. Kina)', 'Hrvatska',
        'Indija', 'Indonezija', 'Irak', 'Iran', 'Irska', 'Island', 'Istočni Timor', 'Italija', 'Izrael',
        'Jamajka', 'Japan', 'Jemen', 'Jordan', 'Južna Džordžija i Južna Sendvič Ostrva', 'Južna Koreja', 'Južnoafrička Republika',
        'Kajmanska Ostrva', 'Kambodža', 'Kamerun', 'Kanada', 'Kanarska ostrva', 'Kape Verde', 'Katar', 'Kazahstan', 'Kenija', 'Kina', 'Kipar', 'Kirgizstan', 'Kiribati', 'Kokos (Keling) Ostrva', 'Kolumbija', 'Komorska Ostrva', 'Kongo', 'Kostarika', 'Kuba', 'Kukova Ostrva', 'Kuvajt',
        'Laos', 'Lesoto', 'Letonija', 'Liban', 'Liberija', 'Libija', 'Lihtenštajn', 'Litvanija', 'Luksemburg',
        'Madagaskar', 'Mađarska', 'Majote', 'Makao (S. A. R. Kina)', 'Makedonija', 'Malavi', 'Maldivi', 'Malezija', 'Mali', 'Malta', 'Manja Udaljena Ostrva SAD', 'Maroko', 'Maršalska Ostrva', 'Martinik', 'Mauricius', 'Mauritanija', 'Meksiko', 'Mijanmar', 'Mikronezija', 'Moldavija', 'Monako', 'Mongolija', 'Monserat', 'Mozambik',
        'Namibija', 'Nauru', 'Nemačka', 'Nepal', 'Niger', 'Nigerija', 'Nikaragva', 'Niue', 'Norfolk Ostrvo', 'Norveška', 'Nova Kaledonija', 'Novi Zeland',
        'Obala Slonovače', 'Oman', 'Ostala okeanija', 'Ostrvo Asension', 'Ostrvo Kliperton', 'Ostrvo Man',
        'Pakistan', 'Palau', 'Palestinska Teritorija', 'Panama', 'Papua Nova Gvineja', 'Paragvaj', 'Peru', 'Pitcairn', 'Poljska', 'Porto Riko', 'Portugal',
        'Rejunion', 'Ruanda', 'Rumunija', 'Rusija',
        'S.A.D. Devičanska Ostrva', 'Salvador', 'Samoa', 'San Marino', 'Sao Tome i Principe', 'Saudijska Arabija', 'Sejšeli', 'Sen Pjer i Mikelon', 'Senegal', 'Sent Kits i Nevis', 'Sent Lucija', 'Sent Vinsent i Grenadini', 'Seuta i Melilja', 'Severna Koreja', 'Severna Marijanska Ostrva', 'Sijera Leone', 'Singapur', 'Sirija', 'Sjedinjene Američke Države', 'Slovačka', 'Slovenija', 'Solomonska Ostrva', 'Somalija',
        'Španija', 'Srbija', 'Šri Lanka', 'Sudan', 'Surinam', 'Sv. Bartolomej', 'Sv. Martin', 'Švajcarska', 'Svalbard i Janmajen Ostrva', 'Svazilend', 'Švedska', 'Sveta Jelena',
        'Tadžikistan', 'Tajland', 'Tajvan', 'Tanzanija', 'Togo', 'Tokelau', 'Tonga', 'Trinidad i Tobago', 'Tristan da Kunja', 'Tunis', 'Turkmenistan', 'Turks i Kajkos Ostrva', 'Turska', 'Tuvalu',
        'Uganda', 'Ujedinjeni Arapski Emirati', 'Ukrajina', 'Urugvaj', 'Uzbekistan',
        'Valis i Futuna Ostrva', 'Vanuatu', 'Vatikan', 'Velika Britanija', 'Venecuela', 'Vijetnam',
        'Zambija', 'Zapadna Sahara', 'Zimbabve',
    ];

    public static function streetPrefix()
    {
        return static::randomElement(static::$streetPrefix);
    }

    public static function street()
    {
        return static::randomElement(static::$street);
    }

    public function cityName()
    {
        return static::randomElement(static::$cityNames);
    }

    public static function localCoordinates()
    {
        return [
            'latitude' => static::latitude(42.43, 42.45),
            'longitude' => static::longitude(19.16, 19.27),
        ];
    }
}
