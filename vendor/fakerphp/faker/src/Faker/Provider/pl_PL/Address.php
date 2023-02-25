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
        '1 Maja', '3 Maja', '11 Listopada', 'Agrestowa', 'Akacjowa', 'Andersa Władysława', 'Armii Krajowej',
        'Asnyka Adama', 'Astrów', 'Azaliowa', 'Baczyńskiego Krzysztofa Kamila', 'Bałtycka',
        'Barlickiego Norberta', 'Batalionów Chłopskich', 'Batorego Stefana', 'Bema Józefa',
        'Bema Józefa', 'Beskidzka', 'Białostocka', 'Bielska', 'Bieszczadzka', 'Błękitna',
        'Boczna', 'Bogusławskiego Wojciecha', 'Bohaterów Westerplatte', 'Bolesława Chrobrego',
        'Bolesława Krzywoustego', 'Borowa', 'Botaniczna', 'Bracka', 'Bratków', 'Broniewskiego Władysława',
        'Brzechwy Jana', 'Brzoskwiniowa', 'Brzozowa', 'Budowlanych', 'Bukowa', 'Bursztynowa',
        'Bydgoska', 'Bytomska', 'Cedrowa', 'Cegielniana', 'Ceglana', 'Chabrowa', 'Chełmońskiego Józefa',
        'Chłodna', 'Chłopska', 'Chmielna', 'Chopina Fryderyka', 'Chorzowska', 'Chrobrego Bolesława',
        'Ciasna', 'Cicha', 'Cieszyńska', 'Cisowa', 'Cmentarna', 'Curie-Skłodowskiej Marii',
        'Czarnieckiego Stefana', 'Czereśniowa', 'Częstochowska', 'Czwartaków', 'Daleka', 'Daszyńskiego Ignacego',
        'Dąbrowskiego Jana Henryka', 'Dąbrowskiego Jarosława', 'Dąbrowskiego Jarosława',
        'Dąbrowskiej Marii', 'Dąbrowszczaków', 'Dąbrówki', 'Dębowa', 'Diamentowa', 'Długa',
        'Długosza Jana', 'Dmowskiego Romana', 'Dobra', 'Dolna', 'Dożynkowa', 'Drzymały Michała',
        'Dubois Stanisława', 'Dworcowa', 'Dworska', 'Działkowa', 'Energetyków', 'Fabryczna',
        'Fałata Juliana', 'Fiołkowa', 'Folwarczna', 'Franciszkańska', 'Francuska', 'Fredry Aleksandra',
        'Gagarina Jurija', 'Gajowa', 'Gałczyńskiego Konstantego Ildefonsa', 'Gdańska', 'Gdyńska',
        'Gliwicka', 'Głogowa', 'Głogowska', 'Głowackiego Bartosza', 'Główna', 'Gminna', 'Gnieźnieńska',
        'Gojawiczyńskiej Poli', 'Gołębia', 'Gościnna', 'Górna', 'Górnicza', 'Górnośląska',
        'Grabowa', 'Graniczna', 'Granitowa', 'Grochowska', 'Grodzka', 'Grota-Roweckiego Stefana',
        'Grottgera Artura', 'Grójecka', 'Grunwaldzka', 'Grzybowa', 'Hallera Józefa', 'Handlowa',
        'Harcerska', 'Hetmańska', 'Hoża', 'Husarska', 'Hutnicza', 'Inżynierska', 'Iwaszkiewicza Jarosława',
        'Jagiellońska', 'Jagiellońskie Os.', 'Jagiełły Władysława', 'Jagodowa', 'Jałowcowa',
        'Jana Pawła II', 'Jana Pawła II Al.', 'Jaracza Stefana', 'Jarzębinowa', 'Jaskółcza',
        'Jasna', 'Jastrzębia', 'Jaśminowa', 'Jaworowa', 'Jerozolimskie Al.', 'Jesienna', 'Jesionowa',
        'Jeżynowa', 'Jodłowa', 'Kalinowa', 'Kaliska', 'Kamienna', 'Karłowicza Mieczysława',
        'Karpacka', 'Kartuska', 'Kasprowicza Jana', 'Kasprzaka Marcina', 'Kasztanowa', 'Kaszubska',
        'Katowicka', 'Kazimierza Wielkiego', 'Kielecka', 'Kilińskiego Jana', 'Kleeberga Franciszka',
        'Klonowa', 'Kłosowa', 'Kochanowskiego Jana', 'Kolberga Oskara', 'Kolejowa', 'Kolorowa',
        'Kołłątaja Hugo', 'Kołłątaja Hugona', 'Kołobrzeska', 'Konarskiego Stanisława',
        'Konopnickiej Marii', 'Konstytucji 3 Maja', 'Konwaliowa', 'Kopalniana', 'Kopernika Mikołaja',
        'Koralowa', 'Korczaka Janusza', 'Korfantego Wojciecha', 'Kosmonautów', 'Kossaka Juliusza',
        'Kosynierów', 'Koszalińska', 'Koszykowa', 'Kościelna', 'Kościuszki Tadeusza', 'Kościuszki Tadeusza Pl.',
        'Kowalska', 'Krakowska', 'Krańcowa', 'Krasickiego Ignacego', 'Krasińskiego Zygmunta',
        'Kraszewskiego Józefa Ignacego', 'Kresowa', 'Kręta', 'Królewska', 'Królowej Jadwigi',
        'Krótka', 'Krucza', 'Kruczkowskiego Leona', 'Krzywa', 'Księżycowa', 'Kujawska', 'Kusocińskiego Janusza',
        'Kwiatkowskiego Eugeniusza', 'Kwiatowa', 'Lawendowa', 'Lazurowa', 'Lechicka', 'Legionów',
        'Legnicka', 'Lelewela Joachima', 'Leszczynowa', 'Leśmiana Bolesława', 'Leśna', 'Letnia',
        'Ligonia Juliusza', 'Liliowa', 'Limanowskiego Bolesława', 'Lipowa', 'Lisia', 'Litewska',
        'Lompy Józefa', 'Lotnicza', 'Lotników', 'Lubelska', 'Ludowa', 'Lwowska', 'Łabędzia',
        'Łagiewnicka', 'Łanowa', 'Łączna', 'Łąkowa', 'Łokietka Władysława', 'Łomżyńska',
        'Łowicka', 'Łódzka', 'Łukasiewicza Ignacego', 'Łużycka', 'Maczka Stanisława',
        'Magazynowa', 'Majowa', 'Makowa', 'Makuszyńskiego Kornela', 'Malczewskiego Jacka', 'Malinowa',
        'Mała', 'Małachowskiego Stanisława', 'Małopolska', 'Marszałkowska', 'Matejki Jana',
        'Mazowiecka', 'Mazurska', 'Miarki Karola', 'Mickiewicza Adama', 'Miedziana', 'Mieszka I',
        'Miła', 'Miodowa', 'Młynarska', 'Młyńska', 'Modlińska', 'Modra', 'Modrzejewskiej Heleny',
        'Modrzewiowa', 'Mokra', 'Moniuszki Stanisława', 'Morcinka Gustawa', 'Morelowa', 'Morska',
        'Mostowa', 'Myśliwska', 'Nadbrzeżna', 'Nadrzeczna', 'Nałkowskiej Zofii', 'Narutowicza Gabriela',
        'Niecała', 'Niedziałkowskiego Mieczysława', 'Niemcewicza Juliana Ursyna', 'Niepodległości',
        'Niepodległości Al.', 'Niska', 'Norwida Cypriana Kamila', 'Nowa', 'Nowowiejska', 'Nowowiejskiego Feliksa',
        'Nowy Świat', 'Obrońców Westerplatte', 'Odrodzenia', 'Odrzańska', 'Ogrodowa', 'Okopowa',
        'Okólna', 'Okrężna', 'Okrzei Stefana', 'Okulickiego Leopolda', 'Olchowa', 'Olimpijska',
        'Olsztyńska', 'Opolska', 'Orkana Władysława', 'Orla', 'Orzechowa', 'Orzeszkowej Elizy',
        'Osiedlowa', 'Oświęcimska', 'Owocowa', 'Paderewskiego Ignacego', 'Parkowa', 'Partyzantów',
        'Patriotów', 'Pawia', 'Perłowa', 'Piaskowa', 'Piastowska', 'Piastowskie Os.', 'Piekarska',
        'Piękna', 'Piłsudskiego Józefa', 'Piłsudskiego Józefa', 'Piłsudskiego Józefa Al.',
        'Piotrkowska', 'Piwna', 'Plater Emilii', 'Plebiscytowa', 'Płocka', 'Pocztowa', 'Podchorążych',
        'Podgórna', 'Podhalańska', 'Podleśna', 'Podmiejska', 'Podwale', 'Pogodna', 'Pokoju',
        'Pola Wincentego', 'Polna', 'Południowa', 'Pomorska', 'Poniatowskiego Józefa', 'Poniatowskiego Józefa',
        'Popiełuszki Jerzego', 'Poprzeczna', 'Portowa', 'Porzeczkowa', 'Powstańców', 'Powstańców Śląskich',
        'Powstańców Wielkopolskich', 'Poziomkowa', 'Poznańska', 'Północna', 'Promienna',
        'Prosta', 'Prusa Bolesława', 'Przechodnia', 'Przemysłowa', 'Przybyszewskiego Stanisława',
        'Przyjaźni', 'Pszenna', 'Ptasia', 'Pułaskiego Kazimierza', 'Pułaskiego Kazimierza',
        'Puławska', 'Puszkina Aleksandra', 'Racławicka', 'Radomska', 'Radosna', 'Rataja Macieja',
        'Reja Mikołaja', 'Rejtana Tadeusza', 'Reymonta Władysława', 'Reymonta Władysława Stanisława',
        'Robotnicza', 'Rodzinna', 'Rolna', 'Rolnicza', 'Równa', 'Różana', 'Rubinowa', 'Rumiankowa',
        'Rybacka', 'Rybna', 'Rybnicka', 'Rycerska', 'Rynek', 'Rynek Rynek', 'Rzeczna', 'Rzemieślnicza',
        'Sadowa', 'Sandomierska', 'Saperów', 'Sawickiej Hanki', 'Sądowa', 'Sąsiedzka', 'Senatorska',
        'Siemiradzkiego Henryka', 'Sienkiewicza Henryka', 'Sienna', 'Siewna', 'Sikorskiego Władysława',
        'Sikorskiego Władysława', 'Skargi Piotra', 'Skargi Piotra', 'Składowa', 'Skłodowskiej-Curie Marii',
        'Skośna', 'Skrajna', 'Słoneczna', 'Słonecznikowa', 'Słowackiego Juliusza', 'Słowiańska',
        'Słowicza', 'Sobieskiego Jana', 'Sobieskiego Jana III', 'Sokola', 'Solidarności Al.',
        'Solna', 'Solskiego Ludwika', 'Sosnowa', 'Sowia', 'Sowińskiego Józefa', 'Spacerowa',
        'Spokojna', 'Sportowa', 'Spółdzielcza', 'Srebrna', 'Staffa Leopolda', 'Stalowa', 'Staromiejska',
        'Starowiejska', 'Staszica Stanisława', 'Stawowa', 'Stolarska', 'Strażacka', 'Stroma',
        'Struga Andrzeja', 'Strumykowa', 'Strzelecka', 'Studzienna', 'Stwosza Wita', 'Sucha',
        'Sucharskiego Henryka', 'Szafirowa', 'Szarych Szeregów', 'Szczecińska', 'Szczęśliwa',
        'Szeroka', 'Szewska', 'Szkolna', 'Szmaragdowa', 'Szpitalna', 'Szymanowskiego Karola',
        'Ściegiennego Piotra', 'Śląska', 'Średnia', 'Środkowa', 'Świdnicka', 'Świerkowa',
        'Świętojańska', 'Świętokrzyska', 'Targowa', 'Tatrzańska', 'Tęczowa', 'Topolowa',
        'Torowa', 'Toruńska', 'Towarowa', 'Traugutta Romualda', 'Truskawkowa', 'Tulipanowa',
        'Tulipanów', 'Turkusowa', 'Turystyczna', 'Tuwima Juliana', 'Tylna', 'Tysiąclecia', 'Ułańska',
        'Urocza', 'Wałowa', 'Wandy', 'Wańkowicza Melchiora', 'Wapienna', 'Warmińska', 'Warszawska',
        'Waryńskiego Ludwika', 'Wąska', 'Wczasowa', 'Wesoła', 'Węglowa', 'Widok', 'Wiejska',
        'Wielkopolska', 'Wieniawskiego Henryka', 'Wierzbowa', 'Wilcza', 'Wileńska', 'Willowa',
        'Wiosenna', 'Wiśniowa', 'Witosa Wincentego', 'Władysława IV', 'Wodna', 'Wojska Polskiego',
        'Wojska Polskiego Al.', 'Wolności', 'Wolności Pl.', 'Wolska', 'Wołodyjowskiego Michała',
        'Wrocławska', 'Wronia', 'Wróblewskiego Walerego', 'Wrzosowa', 'Wschodnia', 'Wspólna',
        'Wybickiego Józefa', 'Wysoka', 'Wyspiańskiego Stanisława', 'Wyszyńskiego Stefana',
        'Wyzwolenia', 'Wyzwolenia Al.', 'Zachodnia', 'Zacisze', 'Zajęcza', 'Zakątek', 'Zakopiańska',
        'Zamenhofa Ludwika', 'Zamkowa', 'Zapolskiej Gabrieli', 'Zbożowa', 'Zdrojowa', 'Zgierska',
        'Zielna', 'Zielona', 'Złota', 'Zwierzyniecka', 'Zwycięstwa', 'Źródlana', 'Żabia',
        'Żeglarska', 'Żelazna', 'Żeromskiego Stefana', 'Żniwna', 'Żołnierska', 'Żółkiewskiego Stanisława',
        'Żurawia', 'Żwirki Franciszka i Wigury Stanisława', 'Żwirki i Wigury', 'Żwirowa',
        'Żytnia',
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
