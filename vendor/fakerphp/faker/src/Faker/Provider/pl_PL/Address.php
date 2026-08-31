<?php

namespace Faker\Provider\pl_PL;

class Address extends \Faker\Provider\Address
{
    protected static $streetAddressFormats = [
        '{{streetName}} {{buildingNumber}}',
    ];
    protected static $addressFormats = [
        '{{streetAddress}}, {{postcode}} {{city}}',
    ];

    protected static $buildingNumber = ['##A', '%#', '##A/%#', '%#/%#'];
    protected static $postcode = ['##-###'];
    /**
     * @var array full list of Polish voivodeship
     */
    protected static $state = [
        'dolnośląskie', 'kujawsko-pomorskie', 'lubelskie', 'lubuskie', 'łódzkie', 'małopolskie', 'mazowieckie',
        'opolskie', 'podkarpackie', 'podlaskie', 'pomorskie', 'śląskie', 'świętokrzyskie', 'warmińsko-mazurskie',
        'wielkopolskie', 'zachodniopomorskie',
    ];
    /**
     * @var array Countries in Polish
     *
     * @see http://ksng.gugik.gov.pl/english/files/dictionary.pdf
     */
    protected static $country = [
        'Afganistan', 'Albania', 'Algieria', 'Andora', 'Angola', 'Antigua i Barbuda', 'Arabia Saudyjska', 'Argentyna',
        'Armenia', 'Australia', 'Austria', 'Azerbejdżan', 'Bahamy', 'Bahrajn', 'Bangladesz', 'Barbados', 'Belgia',
        'Belize', 'Benin', 'Bhutan', 'Białoruś', 'Birma', 'Boliwia', 'Bośnia i Hercegowina', 'Botswana', 'Brazylia',
        'Brunei', 'Bułgaria', 'Burkina Faso', 'Burundi', 'Chile', 'Chiny', 'Chorwacja', 'Cypr', 'Czad', 'Czarnogóra',
        'Czechy', 'Dania', 'Demokratyczna Republika Konga', 'Dominika', 'Dominikana', 'Dżibuti', 'Egipt', 'Ekwador',
        'Erytrea', 'Estonia', 'Etiopia', 'Fidżi', 'Filipiny', 'Finlandia', 'Francja', 'Gabon', 'Gambia', 'Ghana',
        'Grecja', 'Grenada', 'Gruzja', 'Gujana', 'Gwatemala', 'Gwinea', 'Gwinea Bissau', 'Gwinea Równikowa', 'Haiti',
        'Hiszpania', 'Holandia', 'Honduras', 'Indie', 'Indonezja', 'Irak', 'Iran', 'Irlandia', 'Islandia', 'Izrael',
        'Jamajka', 'Japonia', 'Jemen', 'Jordania', 'Kambodża', 'Kamerun', 'Kanada', 'Katar', 'Kazachstan', 'Kenia',
        'Kirgistan', 'Kiribati', 'Kolumbia', 'Komory', 'Kongo', 'Korea Południowa', 'Korea Północna', 'Kostaryka',
        'Kuba', 'Kuwejt', 'Laos', 'Lesotho', 'Liban', 'Liberia', 'Libia', 'Liechtenstein', 'Litwa', 'Luksemburg',
        'Łotwa', 'Macedonia Północna', 'Madagaskar', 'Malawi', 'Malediwy', 'Malezja', 'Mali', 'Malta', 'Maroko', 'Mauretania',
        'Mauritius', 'Meksyk', 'Mikronezja', 'Mołdawia', 'Monako', 'Mongolia', 'Mozambik', 'Namibia', 'Nauru', 'Nepal',
        'Niemcy', 'Niger', 'Nigeria', 'Nikaragua', 'Norwegia', 'Nowa Zelandia', 'Oman', 'Pakistan', 'Palau', 'Panama',
        'Papua-Nowa Gwinea', 'Paragwaj', 'Peru', 'Polska', 'Portugalia', 'Republika Południowej Afryki',
        'Republika Środkowoafrykańska', 'Republika Zielonego Przylądka', 'Rosja', 'Rumunia', 'Rwanda',
        'Saint Kitts i Nevis', 'Saint Lucia', 'Saint Vincent i Grenadyny', 'Salwador', 'Samoa', 'San Marino', 'Senegal',
        'Serbia', 'Seszele', 'Sierra Leone', 'Singapur', 'Słowacja', 'Słowenia', 'Somalia', 'Sri Lanka',
        'Stany Zjednoczone', 'Suazi', 'Sudan', 'Surinam', 'Syria', 'Szwajcaria', 'Szwecja', 'Tadżykistan', 'Tajlandia',
        'Tanzania', 'Timor Wschodni', 'Togo', 'Tonga', 'Trynidad i Tobago', 'Tunezja', 'Turcja', 'Turkmenistan',
        'Tuvalu', 'Uganda', 'Ukraina', 'Urugwaj', 'Uzbekistan', 'Vanuatu', 'Watykan', 'Wenezuela', 'Węgry',
        'Wielka Brytania', 'Wietnam', 'Włochy', 'Wybrzeże Kości Słoniowej', 'Wyspy Marshalla', 'Wyspy Salomona',
        'Wyspy Świętego Tomasza i Książęca', 'Zambia', 'Zimbabwe', 'Zjednoczone Emiraty Arabskie',
    ];
    /**
     * @var array 250 Polish cities with biggest number of streets. Extracted from data issued by the official
     *            public postal service of Poland.
     *
     * @see http://www.poczta-polska.pl/
     */
    protected static $city = [
        'Babienica', 'Bartoszyce', 'Bełchatów', 'Bezrzecze', 'Będzin', 'Biała Podlaska', 'Białystok',
        'Bielawa', 'Bielsko-Biała', 'Bieruń', 'Bochnia', 'Bogaczów', 'Bogatynia', 'Boguszów-Gorce', 'Bolesławiec',
        'Braniewo', 'Brodnica', 'Brzeg', 'Busko-Zdrój', 'Bydgoszcz', 'Bytom', 'Chełm', 'Chojnice', 'Chorzów',
        'Chrzanów', 'Ciechanów', 'Cieszyn', 'Czaplinek', 'Czarna Woda', 'Czechowice-Dziedzice', 'Czeladź',
        'Czerwionka-Leszczyny', 'Częstochowa', 'Darłowo', 'Dąbrowa Górnicza', 'Dębica', 'Dębogórze',
        'Dzierżoniów', 'Elbląg', 'Ełk', 'Franciszków', 'Gdańsk', 'Gdynia', 'Giżycko', 'Gliwice', 'Głogów',
        'Gniezno', 'Gołubie', 'Gorlice', 'Gorzów Wielkopolski', 'Grodzisk Mazowiecki', 'Grudziądz', 'Ilkowice',
        'Iława', 'Inowrocław', 'Jadowniki', 'Jarosław', 'Jaroszowa Wola', 'Jasło', 'Jastarnia', 'Jastrzębie',
        'Jastrzębie-Zdrój', 'Jawor', 'Jaworzno', 'Jelcz-Laskowice', 'Jelenia Góra', 'Jemielnica', 'Jeziorna',
        'Józefów', 'Kalisz', 'Kamienica Królewska', 'Kamieniec Ząbkowicki', 'Kamień', 'Katowice', 'Kędzierzyn-Koźle',
        'Kętrzyn', 'Kielce', 'Kluczbork', 'Kłobuck', 'Kłodzko', 'Knurów', 'Kolonowskie', 'Koło', 'Kołobrzeg',
        'Konin', 'Konstancin-Jeziorna', 'Koszalin', 'Koszwały', 'Kościan', 'Kościerzyna', 'Kozienice',
        'Kraków', 'Krapkowice', 'Kraśnik', 'Krępiec', 'Krosno', 'Krotoszyn', 'Kutno', 'Kuźnica Masłońska',
        'Kwidzyn', 'Legionowo', 'Legnica', 'Leszno', 'Lębork', 'Lędziny', 'Lidzbark Warmiński', 'Lubartów',
        'Lubin', 'Lublin', 'Lubliniec', 'Lubojenka', 'Luboń', 'Ławy', 'Łaziska Górne', 'Łęczna', 'Łomianki',
        'Łomża', 'Łoś', 'Łowicz', 'Łódź', 'Magdalenka', 'Malbork', 'Marylka', 'Mielec', 'Mikołów',
        'Mokrzyska', 'Mysłowice', 'Myszków', 'Nowa Ruda', 'Nowa Sól', 'Nowe Kramsko', 'Nowy Dwór Mazowiecki',
        'Nowy Sącz', 'Nowy Targ', 'Nysa', 'Olkusz', 'Olsztyn', 'Opole', 'Orzesze', 'Osówiec', 'Ostrołęka',
        'Ostrowiec Świętokrzyski', 'Ostróda', 'Ostrów Mazowiecka', 'Ostrów Wielkopolski', 'Ostrzeszów',
        'Oświęcim', 'Otwock', 'Pabianice', 'Pawłowice', 'Pęcice', 'Piaseczno', 'Piekary Śląskie', 'Pieszyce',
        'Pilchowo', 'Piła', 'Piotrków Trybunalski', 'Pisz', 'Płazów', 'Płock', 'Police', 'Postęp', 'Poznań',
        'Pruszcz Gdański', 'Pruszków', 'Przemyśl', 'Przędzel', 'Pszczyna', 'Puławy', 'Pułtusk', 'Racibórz',
        'Radom', 'Radomsko', 'Ruda Śląska', 'Rumia', 'Rybnik', 'Rynarzewo', 'Rzeszów', 'Sandomierz', 'Sanok',
        'Siedlce', 'Siemianowice Śląskie', 'Sieradz', 'Skalbmierz', 'Skarżysko-Kamienna', 'Skierniewice',
        'Słupsk', 'Sochaczew', 'Sopot', 'Sosnowiec', 'Stalowa Wola', 'Starachowice', 'Stargard',
        'Starogard Gdański', 'Studzienice', 'Sulejówek', 'Suwałki', 'Swarzędz', 'Szczawin', 'Szczecin',
        'Szczecinek', 'Szczytno', 'Szówsko', 'Szteklin', 'Szwecja', 'Śrem', 'Świdnica', 'Świdnik', 'Świdwin',
        'Świebodzice', 'Świebodzin', 'Świecie', 'Świętochłowice', 'Świnoujście', 'Tarnobrzeg', 'Tarnowskie Góry',
        'Tarnów', 'Tczew', 'Tomaszów Mazowiecki', 'Toruń', 'Trzebiatów', 'Turek', 'Tychy', 'Ustka', 'Wałbrzych',
        'Warszawa', 'Wągrowiec', 'Wejherowo', 'Wilkowice', 'Władysławowo', 'Włocławek', 'Wodzisław Śląski',
        'Wola Kiedrzyńska', 'Wrocław', 'Września', 'Wyszków', 'Zabrze', 'Zakopane', 'Zamość', 'Zawiercie',
        'Ząbki', 'Zborowskie', 'Zduńska Wola', 'Zgierz', 'Zgorzelec', 'Zielona Góra', 'Żary', 'Żory',
        'Żyrardów', 'Żywiec',
    ];
    /**
     * @var array 549 most common Polish street names. Extracted from data issued by the official public
     *            postal service of Poland.
     *
     * @see http://www.poczta-polska.pl/
     */
    protected static $street = [
        '1 Maja', '3 Maja', '11 Listopada', 'Agrestowa', 'Akacjowa', 'Andersa', 'Armii Krajowej',
        'Asnyka', 'Astrów', 'Azaliowa', 'Baczyńskiego', 'Bałtycka', 'Barlickiego', 'Batalionów Chłopskich',
        'Batorego', 'Bema', 'Beskidzka', 'Białostocka', 'Bielska', 'Bieszczadzka', 'Błękitna',
        'Boczna', 'Bogusławskiego', 'Bohaterów Westerplatte', 'Bolesława Chrobrego',
        'Bolesława Krzywoustego', 'Borowa', 'Botaniczna', 'Bracka', 'Bratków', 'Broniewskiego',
        'Brzechwy', 'Brzoskwiniowa', 'Brzozowa', 'Budowlanych', 'Bukowa', 'Bursztynowa',
        'Bydgoska', 'Bytomska', 'Cedrowa', 'Cegielniana', 'Ceglana', 'Chabrowa', 'Chełmońskiego',
        'Chłodna', 'Chłopska', 'Chmielna', 'Chopina Fryderyka', 'Chorzowska',
        'Ciasna', 'Cicha', 'Cieszyńska', 'Cisowa', 'Cmentarna', 'Curie-Skłodowskiej',
        'Czarnieckiego', 'Czereśniowa', 'Częstochowska', 'Czwartaków', 'Daleka', 'Daszyńskiego',
        'Dąbrowskiego', 'Dąbrowskiej', 'Dąbrowszczaków', 'Dąbrówki', 'Dębowa', 'Diamentowa', 'Długa',
        'Długosza', 'Dmowskiego', 'Dobra', 'Dolna', 'Dożynkowa', 'Drzymały',
        'Dworcowa', 'Dworska', 'Działkowa', 'Energetyków', 'Fabryczna',
        'Fałata', 'Fiołkowa', 'Folwarczna', 'Franciszkańska', 'Francuska', 'Fredry',
        'Gagarina', 'Gajowa', 'Gałczyńskiego', 'Gdańska', 'Gdyńska',
        'Gliwicka', 'Głogowa', 'Głogowska', 'Głowackiego', 'Główna', 'Gminna', 'Gnieźnieńska',
        'Gojawiczyńskiej', 'Gołębia', 'Gościnna', 'Górna', 'Górnicza', 'Górnośląska',
        'Grabowa', 'Graniczna', 'Granitowa', 'Grochowska', 'Grodzka', 'Grota-Roweckiego',
        'Grottgera', 'Grójecka', 'Grunwaldzka', 'Grzybowa', 'Hallera', 'Handlowa',
        'Harcerska', 'Hetmańska', 'Hoża', 'Husarska', 'Hutnicza', 'Inżynierska', 'Iwaszkiewicza',
        'Jagiellońska', 'Os. Jagiellońskie', 'Jagiełły', 'Jagodowa', 'Jałowcowa',
        'Jana Pawła II', 'Al. Jana Pawła II', 'Jaracza', 'Jarzębinowa', 'Jaskółcza',
        'Jasna', 'Jastrzębia', 'Jaśminowa', 'Jaworowa', 'Al. Jerozolimskie', 'Jesienna', 'Jesionowa',
        'Jeżynowa', 'Jodłowa', 'Kalinowa', 'Kaliska', 'Kamienna', 'Karłowicza',
        'Karpacka', 'Kartuska', 'Kasprowicza', 'Kasprzaka Marcina', 'Kasztanowa', 'Kaszubska',
        'Katowicka', 'Kazimierza Wielkiego', 'Kielecka', 'Kilińskiego', 'Kleeberga',
        'Klonowa', 'Kłosowa', 'Kochanowskiego', 'Kolberga', 'Kolejowa', 'Kolorowa',
        'Kołłątaja', 'Kołobrzeska', 'Konarskiego',
        'Konopnickiej', 'Konstytucji 3 Maja', 'Konwaliowa', 'Kopalniana', 'Kopernika',
        'Koralowa', 'Korczaka', 'Korfantego', 'Kosmonautów', 'Kossaka',
        'Kosynierów', 'Koszalińska', 'Koszykowa', 'Kościelna', 'Kościuszki', 'Pl. Kościuszki',
        'Kowalska', 'Krakowska', 'Krańcowa', 'Krasickiego', 'Krasińskiego',
        'Kraszewskiego', 'Kresowa', 'Kręta', 'Królewska', 'Królowej Jadwigi',
        'Krótka', 'Krucza', 'Kruczkowskiego', 'Krzywa', 'Księżycowa', 'Kujawska', 'Kusocińskiego',
        'Kwiatkowskiego', 'Kwiatowa', 'Lawendowa', 'Lazurowa', 'Lechicka', 'Legionów',
        'Legnicka', 'Lelewela', 'Leszczynowa', 'Leśmiana', 'Leśna', 'Letnia',
        'Ligonia', 'Liliowa', 'Limanowskiego', 'Lipowa', 'Lisia', 'Litewska',
        'Lompy', 'Lotnicza', 'Lotników', 'Lubelska', 'Ludowa', 'Lwowska', 'Łabędzia',
        'Łagiewnicka', 'Łanowa', 'Łączna', 'Łąkowa', 'Łokietka', 'Łomżyńska',
        'Łowicka', 'Łódzka', 'Łukasiewicza', 'Łużycka', 'Maczka',
        'Magazynowa', 'Majowa', 'Makowa', 'Makuszyńskiego', 'Malczewskiego', 'Malinowa',
        'Mała', 'Małachowskiego', 'Małopolska', 'Marszałkowska', 'Matejki',
        'Mazowiecka', 'Mazurska', 'Miarki', 'Mickiewicza', 'Miedziana', 'Mieszka I',
        'Miła', 'Miodowa', 'Młynarska', 'Młyńska', 'Modlińska', 'Modra', 'Modrzejewskiej',
        'Modrzewiowa', 'Mokra', 'Moniuszki', 'Morcinka', 'Morelowa', 'Morska',
        'Mostowa', 'Myśliwska', 'Nadbrzeżna', 'Nadrzeczna', 'Nałkowskiej', 'Narutowicza',
        'Niecała', 'Niedziałkowskiego', 'Niemcewicza', 'Niepodległości',
        'Al. Niepodległości', 'Niska', 'Norwida', 'Nowa', 'Nowowiejska', 'Nowowiejskiego',
        'Nowy Świat', 'Obrońców Westerplatte', 'Odrodzenia', 'Odrzańska', 'Ogrodowa', 'Okopowa',
        'Okólna', 'Okrężna', 'Okrzei', 'Okulickiego', 'Olchowa', 'Olimpijska',
        'Olsztyńska', 'Opolska', 'Orkana', 'Orla', 'Orzechowa', 'Orzeszkowej',
        'Osiedlowa', 'Oświęcimska', 'Owocowa', 'Paderewskiego', 'Parkowa', 'Partyzantów',
        'Patriotów', 'Pawia', 'Perłowa', 'Piaskowa', 'Piastowska', 'Os. Piastowskie', 'Piekarska',
        'Piękna', 'Piłsudskiego', 'Al. Piłsudskiego',
        'Piotrkowska', 'Piwna', 'Emilii Plater', 'Plebiscytowa', 'Płocka', 'Pocztowa', 'Podchorążych',
        'Podgórna', 'Podhalańska', 'Podleśna', 'Podmiejska', 'Podwale', 'Pogodna', 'Pokoju',
        'Wincentego Pola', 'Polna', 'Południowa', 'Pomorska', 'Poniatowskiego',
        'Popiełuszki', 'Poprzeczna', 'Portowa', 'Porzeczkowa', 'Powstańców', 'Powstańców Śląskich',
        'Powstańców Wielkopolskich', 'Poziomkowa', 'Poznańska', 'Północna', 'Promienna',
        'Prosta', 'Bolesława Prusa', 'Przechodnia', 'Przemysłowa', 'Przybyszewskiego',
        'Przyjaźni', 'Pszenna', 'Ptasia', 'Pułaskiego', 'Puławska', 'Puszkina', 'Racławicka',
        'Radomska', 'Radosna', 'Rataja', 'Reja', 'Rejtana', 'Reymonta',
        'Robotnicza', 'Rodzinna', 'Rolna', 'Rolnicza', 'Równa', 'Różana', 'Rubinowa', 'Rumiankowa',
        'Rybacka', 'Rybna', 'Rybnicka', 'Rycerska', 'Rynek', 'Rzeczna', 'Rzemieślnicza',
        'Sadowa', 'Sandomierska', 'Saperów', 'Sawickiej', 'Sądowa', 'Sąsiedzka', 'Senatorska',
        'Siemiradzkiego', 'Sienkiewicza', 'Sienna', 'Siewna',
        'Sikorskiego', 'Piotra Skargi', 'Składowa', 'Skłodowskiej-Curie',
        'Skośna', 'Skrajna', 'Słoneczna', 'Słonecznikowa', 'Słowackiego', 'Słowiańska',
        'Słowicza', 'Sobieskiego', 'Jana III Sobieskiego', 'Sokola', 'Al. Solidarności',
        'Solna', 'Solskiego', 'Sosnowa', 'Sowia', 'Sowińskiego', 'Spacerowa',
        'Spokojna', 'Sportowa', 'Spółdzielcza', 'Srebrna', 'Staffa ', 'Stalowa', 'Staromiejska',
        'Starowiejska', 'Staszica', 'Stawowa', 'Stolarska', 'Strażacka', 'Stroma',
        'Struga', 'Strumykowa', 'Strzelecka', 'Studzienna', 'Wita Stwosza', 'Sucha',
        'Sucharskiego', 'Szafirowa', 'Szarych Szeregów', 'Szczecińska', 'Szczęśliwa',
        'Szeroka', 'Szewska', 'Szkolna', 'Szmaragdowa', 'Szpitalna', 'Szymanowskiego',
        'Ściegiennego', 'Śląska', 'Średnia', 'Środkowa', 'Świdnicka', 'Świerkowa',
        'Świętojańska', 'Świętokrzyska', 'Targowa', 'Tatrzańska', 'Tęczowa', 'Topolowa',
        'Torowa', 'Toruńska', 'Towarowa', 'Traugutta', 'Truskawkowa', 'Tulipanowa',
        'Tulipanów', 'Turkusowa', 'Turystyczna', 'Tuwima', 'Tylna', 'Tysiąclecia', 'Ułańska',
        'Urocza', 'Wałowa', 'Wandy', 'Wańkowicza', 'Wapienna', 'Warmińska', 'Warszawska',
        'Waryńskiego', 'Wąska', 'Wczasowa', 'Wesoła', 'Węglowa', 'Widok', 'Wiejska',
        'Wielkopolska', 'Wieniawskiego', 'Wierzbowa', 'Wilcza', 'Wileńska', 'Willowa',
        'Wiosenna', 'Wiśniowa', 'Witosa', 'Władysława IV', 'Wodna', 'Wojska Polskiego',
        'Al. Wojska Polskiego', 'Wolności', 'Pl. Wolności', 'Wolska', 'Wołodyjowskiego',
        'Wrocławska', 'Wronia', 'Wróblewskiego', 'Wrzosowa', 'Wschodnia', 'Wspólna',
        'Wybickiego', 'Wysoka', 'Wyspiańskiego', 'Wyszyńskiego',
        'Wyzwolenia', 'Al. Wyzwolenia', 'Zachodnia', 'Zacisze', 'Zajęcza', 'Zakątek', 'Zakopiańska',
        'Zamenhofa', 'Zamkowa', 'Zapolskiej', 'Zbożowa', 'Zdrojowa', 'Zgierska',
        'Zielna', 'Zielona', 'Złota', 'Zwierzyniecka', 'Zwycięstwa', 'Źródlana', 'Żabia',
        'Żeglarska', 'Żelazna', 'Żeromskiego', 'Żniwna', 'Żołnierska', 'Żółkiewskiego',
        'Żurawia', 'Żwirki i Wigury', 'Żwirowa', 'Żytnia',
    ];

    public function city()
    {
        return static::randomElement(static::$city);
    }

    public function streetName()
    {
        return static::randomElement(static::$street);
    }

    public function state()
    {
        return static::randomElement(static::$state);
    }
}
