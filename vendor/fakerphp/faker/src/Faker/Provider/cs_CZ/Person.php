<?php

namespace Faker\Provider\cs_CZ;

class Person extends \Faker\Provider\Person
{
    protected static $lastNameFormat = [
        '{{lastNameMale}}',
        '{{lastNameFemale}}',
    ];

    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastNameMale}}',
        '{{firstNameMale}} {{lastNameMale}}',
        '{{firstNameMale}} {{lastNameMale}}',
        '{{firstNameMale}} {{lastNameMale}}',
        '{{titleMale}} {{firstNameMale}} {{lastNameMale}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastNameFemale}}',
        '{{firstNameFemale}} {{lastNameFemale}}',
        '{{firstNameFemale}} {{lastNameFemale}}',
        '{{firstNameFemale}} {{lastNameFemale}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastNameFemale}}',
    ];

    protected static $firstNameMale = [
        'Adam', 'Aleš', 'Alois', 'Antonín', 'Bohumil', 'Bohuslav', 'Dagmar',
        'Dalibor', 'Daniel', 'David', 'Dominik', 'Dušan', 'Eduard', 'Emil',
        'Filip', 'František', 'Igor', 'Ivan', 'Ivo', 'Jakub', 'Jan', 'Ján',
        'Jaromír', 'Jaroslav', 'Jindřich', 'Jiří', 'Josef', 'Jozef', 'Kamil',
        'Karel', 'Kryštof', 'Ladislav', 'Libor', 'Lubomír', 'Luboš', 'Luděk',
        'Ludvík', 'Lukáš', 'Marcel', 'Marek', 'Martin', 'Matěj', 'Matyáš',
        'Michael', 'Michal', 'Milan', 'Miloslav', 'Miloš', 'Miroslav',
        'Oldřich', 'Ondřej', 'Patrik', 'Pavel', 'Peter', 'Petr', 'Radek',
        'Radim', 'Radomír', 'René', 'Richard', 'Robert', 'Roman', 'Rostislav',
        'Rudolf', 'Stanislav', 'Šimon', 'Štefan', 'Štěpán', 'Tomáš',
        'Václav', 'Vasyl', 'Viktor', 'Vít', 'Vítězslav', 'Vladimír',
        'Vladislav', 'Vlastimil', 'Vojtěch', 'Zbyněk', 'Zdeněk',
    ];

    protected static $firstNameFemale = [
        'Adéla', 'Alena', 'Alžběta', 'Andrea', 'Aneta', 'Anežka', 'Anna',
        'Barbora', 'Blanka', 'Božena', 'Dana', 'Daniela', 'Denisa', 'Dominika',
        'Eliška', 'Emilie', 'Eva', 'Františka', 'Gabriela', 'Hana', 'Helena',
        'Irena', 'Iva', 'Ivana', 'Iveta', 'Jana', 'Jarmila', 'Jaroslava',
        'Jindřiška', 'Jiřina', 'Jitka', 'Kamila', 'Karolína', 'Kateřina',
        'Klára', 'Kristýna', 'Lenka', 'Libuše', 'Lucie', 'Ludmila', 'Marcela',
        'Mária', 'Marie', 'Markéta', 'Marta', 'Martina', 'Michaela', 'Milada',
        'Milena', 'Miloslava', 'Miluše', 'Miroslava', 'Monika', 'Naděžda',
        'Natálie', 'Nela', 'Nikola', 'Olga', 'Pavla', 'Pavlína', 'Petra',
        'Radka', 'Renata', 'Renáta', 'Romana', 'Růžena', 'Simona', 'Soňa',
        'Stanislava', 'Šárka', 'Štěpánka', 'Tereza', 'Vendula', 'Věra',
        'Veronika', 'Vladimíra', 'Vlasta', 'Zdenka', 'Zdeňka', 'Zdeňka',
        'Zuzana',
    ];

    protected static $lastNameMale = [
        'Adam', 'Adamec', 'Adámek', 'Albrecht', 'Ambrož', 'Anděl', 'Andrle',
        'Antoš', 'Bajer', 'Baláž', 'Balcar', 'Balog', 'Baloun', 'Barák',
        'Baran', 'Bareš', 'Bárta', 'Barták', 'Bartoň', 'Bartoš',
        'Bartošek', 'Bartůněk', 'Bašta', 'Bauer', 'Bayer', 'Bažant',
        'Bečka', 'Bečvář', 'Bednář', 'Bednařík', 'Bělohlávek',
        'Benda', 'Beneš', 'Beran', 'Beránek', 'Berger', 'Berka', 'Berky',
        'Bernard', 'Bezděk', 'Bílek', 'Bílý', 'Bína', 'Bittner',
        'Blaha', 'Bláha', 'Blažek', 'Blecha', 'Bobek', 'Boček', 'Boháč',
        'Boháček', 'Böhm', 'Borovička', 'Bouček', 'Bouda', 'Bouška',
        'Brabec', 'Brabenec', 'Brada', 'Bradáč', 'Braun', 'Brázda',
        'Brázdil', 'Brejcha', 'Brož', 'Brožek', 'Brychta', 'Březina',
        'Bříza', 'Bubeník', 'Buček', 'Buchta', 'Burda', 'Bureš', 'Burian',
        'Buriánek', 'Byrtus', 'Caha', 'Cibulka', 'Cihlář', 'Císař', 'Coufal',
        'Čada', 'Čáp', 'Čapek', 'Čech', 'Čejka', 'Čermák', 'Černík',
        'Černohorský', 'Černoch', 'Černý', 'Červeňák', 'Červenka',
        'Červený', 'Červinka', 'Čihák', 'Čížek', 'Čonka', 'Čurda',
        'Daněk', 'Daniel', 'Daniš', 'David', 'Dědek', 'Dittrich', 'Diviš',
        'Dlouhý', 'Dobeš', 'Dobiáš', 'Dobrovolný', 'Dočekal', 'Dočkal',
        'Dohnal', 'Dokoupil', 'Doleček', 'Dolejš', 'Dolejší', 'Doležal',
        'Doležel', 'Doskočil', 'Dostál', 'Doubek', 'Doubrava', 'Douša',
        'Drábek', 'Drozd', 'Dubský', 'Duda', 'Dudek', 'Dufek', 'Duchoň',
        'Dunka', 'Dušek', 'Dvorský', 'Dvořáček', 'Dvořák', 'Eliáš',
        'Erben', 'Fabián', 'Fanta', 'Farkaš', 'Fejfar', 'Fencl', 'Ferenc',
        'Fiala', 'Fiedler', 'Filip', 'Fischer', 'Fišer', 'Florián', 'Fojtík',
        'Foltýn', 'Formánek', 'Forman', 'Fořt', 'Fousek', 'Franc', 'Franěk',
        'Frank', 'Fridrich', 'Frydrych', 'Fučík', 'Fuchs', 'Fuksa', 'Gábor',
        'Gabriel', 'Gajdoš', 'Gregor', 'Gruber', 'Grundza', 'Grygar', 'Hájek',
        'Hajný', 'Hála', 'Hampl', 'Hanáček', 'Hána', 'Hanák', 'Hanousek',
        'Hanus', 'Hanuš', 'Hanzal', 'Hanzl', 'Hanzlík', 'Hartman', 'Hašek',
        'Havel', 'Havelka', 'Havlíček', 'Havlík', 'Havránek', 'Heczko',
        'Heger', 'Hejda', 'Hejduk', 'Hejl', 'Hejna', 'Hendrych', 'Herman',
        'Heřmánek', 'Heřman', 'Hladík', 'Hladký', 'Hlaváček', 'Hlaváč',
        'Hlavatý', 'Hlávka', 'Hloušek', 'Hoffmann', 'Hofman', 'Holan',
        'Holas', 'Holec', 'Holeček', 'Holík', 'Holoubek', 'Holub', 'Holý',
        'Homola', 'Homolka', 'Horáček', 'Hora', 'Horák', 'Horký', 'Horňák',
        'Horníček', 'Horník', 'Horský', 'Horváth', 'Horvát', 'Hořejší',
        'Hošek', 'Houdek', 'Houška', 'Hovorka', 'Hrabal', 'Hrabovský',
        'Hradecký', 'Hradil', 'Hrbáček', 'Hrbek', 'Hrdina', 'Hrdlička',
        'Hrdý', 'Hrnčíř', 'Hroch', 'Hromádka', 'Hron', 'Hrubeš', 'Hrubý',
        'Hruška', 'Hrůza', 'Hubáček', 'Hudec', 'Hudeček', 'Hůlka', 'Huml',
        'Husák', 'Hušek', 'Hýbl', 'Hynek', 'Chaloupka', 'Chalupa', 'Charvát',
        'Chládek', 'Chlup', 'Chmelař', 'Chmelík', 'Chovanec', 'Chromý',
        'Chudoba', 'Chvátal', 'Chvojka', 'Chytil', 'Jahoda', 'Jakeš',
        'Jakl', 'Jakoubek', 'Jakubec', 'Janáček', 'Janák', 'Janata',
        'Janča', 'Jančík', 'Janda', 'Janeček', 'Janečka', 'Janíček',
        'Janík', 'Janků', 'Janota', 'Janoušek', 'Janovský', 'Jansa',
        'Jánský', 'Jareš', 'Jaroš', 'Jašek', 'Javůrek', 'Jedlička',
        'Jech', 'Jelen', 'Jelínek', 'Jeníček', 'Jeřábek', 'Ježek', 'Jež',
        'Jílek', 'Jindra', 'Jíra', 'Jirák', 'Jiránek', 'Jirásek', 'Jirka',
        'Jirků', 'Jiroušek', 'Jirsa', 'Jiřík', 'John', 'Jonáš', 'Junek',
        'Jurčík', 'Jurečka', 'Juřica', 'Juřík', 'Kabát', 'Kačírek',
        'Kadeřábek', 'Kadlec', 'Kafka', 'Kaiser', 'Kaláb', 'Kala', 'Kalaš',
        'Kalina', 'Kalivoda', 'Kalousek', 'Kalous', 'Kameník', 'Kaňa',
        'Kaňka', 'Kantor', 'Kaplan', 'Karásek', 'Karas', 'Karban', 'Karel',
        'Karlík', 'Kasal', 'Kašík', 'Kašpárek', 'Kašpar', 'Kavka', 'Kazda',
        'Kindl', 'Klečka', 'Klein', 'Klement', 'Klíma', 'Kliment', 'Klimeš',
        'Klouček', 'Klouda', 'Knap', 'Knotek', 'Kocián', 'Kocman', 'Kocourek',
        'Kohoutek', 'Kohout', 'Koch', 'Koláček', 'Kolařík', 'Kolář',
        'Kolek', 'Kolman', 'Komárek', 'Komínek', 'Konečný', 'Koníček',
        'Kopal', 'Kopecký', 'Kopeček', 'Kopečný', 'Kopřiva', 'Korbel',
        'Kořínek', 'Kosík', 'Kosina', 'Kos', 'Kostka', 'Košťál', 'Kotas',
        'Kotek', 'Kotlár', 'Kotrba', 'Kouba', 'Koubek', 'Koudela', 'Koudelka',
        'Koukal', 'Kouřil', 'Koutný', 'Kováč', 'Kovařík', 'Kovářík',
        'Kovář', 'Kozák', 'Kozel', 'Krajíček', 'Králíček', 'Králík',
        'Král', 'Krátký', 'Kratochvíl', 'Kraus', 'Krčmář', 'Krejčík',
        'Krejčí', 'Krejčíř', 'Krištof', 'Kropáček', 'Kroupa', 'Krupa',
        'Krupička', 'Krupka', 'Křeček', 'Křenek', 'Křivánek', 'Křížek',
        'Kříž', 'Kuba', 'Kubálek', 'Kubánek', 'Kubát', 'Kubec', 'Kubelka',
        'Kubeš', 'Kubica', 'Kubíček', 'Kubík', 'Kubín', 'Kubiš', 'Kuča',
        'Kučera', 'Kudláček', 'Kudrna', 'Kuchař', 'Kuchta', 'Kukla',
        'Kulhánek', 'Kulhavý', 'Kunc', 'Kuneš', 'Kupec', 'Kupka', 'Kurka',
        'Kužel', 'Kvapil', 'Kvasnička', 'Kyncl', 'Kysela', 'Lacina', 'Lacko',
        'Lakatoš', 'Landa', 'Langer', 'Lang', 'Langr', 'Látal', 'Lavička',
        'Lebeda', 'Levý', 'Líbal', 'Linhart', 'Liška', 'Lorenc', 'Louda',
        'Ludvík', 'Lukáč', 'Lukášek', 'Lukáš', 'Lukeš', 'Macák', 'Macek',
        'Macura', 'Macháček', 'Machač', 'Macháč', 'Machala', 'Machálek',
        'Mácha', 'Mach', 'Majer', 'Maleček', 'Málek', 'Malík', 'Malina',
        'Malý', 'Maňák', 'Mareček', 'Marek', 'Mareš', 'Maršálek',
        'Maršík', 'Martinec', 'Martinek', 'Martínek', 'Mařík', 'Masopust',
        'Mašek', 'Matějíček', 'Matějka', 'Matoušek', 'Matouš', 'Matula',
        'Matuška', 'Matyáš', 'Matys', 'Maxa', 'Mayer', 'Mazánek', 'Medek',
        'Melichar', 'Mencl', 'Menšík', 'Merta', 'Mička', 'Michalec',
        'Michálek', 'Michalík', 'Michal', 'Michna', 'Mika', 'Míka', 'Mikeš',
        'Miko', 'Mikula', 'Mikulášek', 'Minařík', 'Minář', 'Mirga',
        'Mládek', 'Mlčoch', 'Mlejnek', 'Mojžíš', 'Mokrý', 'Molnár',
        'Moravec', 'Morávek', 'Motl', 'Motyčka', 'Moučka', 'Moudrý',
        'Mráček', 'Mrázek', 'Mráz', 'Mrkvička', 'Mucha', 'Müller',
        'Műller', 'Musil', 'Mužík', 'Myška', 'Nagy', 'Najman', 'Navrátil',
        'Nečas', 'Nedbal', 'Nedoma', 'Nedvěd', 'Nejedlý', 'Němec',
        'Němeček', 'Nesvadba', 'Nešpor', 'Neubauer', 'Neuman', 'Neumann',
        'Nguyen', 'Nguyen', 'Nosek', 'Nováček', 'Novák', 'Novosad', 'Novotný',
        'Nový', 'Odehnal', 'Oláh', 'Oliva', 'Ondráček', 'Ondra', 'Orság',
        'Otáhal', 'Paleček', 'Pánek', 'Papež', 'Pařízek', 'Pašek',
        'Pátek', 'Patočka', 'Paul', 'Pavelek', 'Pavelka', 'Pavel', 'Pavlas',
        'Pavlica', 'Pavlíček', 'Pavlík', 'Pavlů', 'Pazdera', 'Pecka',
        'Pecháček', 'Pecha', 'Pech', 'Pekárek', 'Pekař', 'Pelc', 'Pelikán',
        'Pernica', 'Peroutka', 'Peřina', 'Pešek', 'Peška', 'Pešta',
        'Peterka', 'Petrák', 'Petráš', 'Petr', 'Petrů', 'Petříček',
        'Petřík', 'Pham', 'Pícha', 'Pilař', 'Pilát', 'Píša', 'Pivoňka',
        'Plaček', 'Plachý', 'Plšek', 'Pluhař', 'Podzimek', 'Pohl', 'Pokorný',
        'Poláček', 'Polách', 'Polák', 'Polanský', 'Polášek', 'Polívka',
        'Popelka', 'Pospíchal', 'Pospíšil', 'Potůček', 'Pour', 'Prachař',
        'Prášek', 'Pražák', 'Prchal', 'Procházka', 'Prokeš', 'Prokop',
        'Prošek', 'Provazník', 'Průcha', 'Průša', 'Přibyl', 'Příhoda',
        'Přikryl', 'Pšenička', 'Ptáček', 'Rác', 'Rada', 'Rak', 'Rambousek',
        'Raška', 'Rataj', 'Remeš', 'Rezek', 'Richter', 'Richtr', 'Roubal',
        'Rous', 'Rozsypal', 'Rudolf', 'Růžek', 'Růžička', 'Ryba', 'Rybář',
        'Rýdl', 'Ryšavý', 'Řeháček', 'Řehák', 'Řehoř', 'Řezáč',
        'Řezníček', 'Říha', 'Sadílek', 'Samek', 'Sedláček', 'Sedlák',
        'Sedlář', 'Sehnal', 'Seidl', 'Seifert', 'Sekanina', 'Semerád',
        'Severa', 'Schejbal', 'Schmidt', 'Schneider', 'Schwarz', 'Sikora',
        'Sivák', 'Skácel', 'Skala', 'Skála', 'Skalický', 'Sklenář',
        'Skopal', 'Skořepa', 'Skřivánek', 'Slabý', 'Sládek', 'Sladký',
        'Sláma', 'Slanina', 'Slavíček', 'Slavík', 'Slezák', 'Slováček',
        'Slovák', 'Sluka', 'Smejkal', 'Smékal', 'Smetana', 'Smola', 'Smolík',
        'Smolka', 'Smrčka', 'Smrž', 'Smutný', 'Sobek', 'Sobotka', 'Sochor',
        'Sojka', 'Sokol', 'Sommer', 'Souček', 'Soukup', 'Sova', 'Spáčil',
        'Spurný', 'Srb', 'Staněk', 'Stárek', 'Starý', 'Stehlík', 'Steiner',
        'Stejskal', 'Stibor', 'Stoklasa', 'Straka', 'Stránský', 'Strejček',
        'Strnad', 'Strouhal', 'Studený', 'Studnička', 'Stuchlík',
        'Stupka', 'Suchánek', 'Suchomel', 'Suchý', 'Suk', 'Svačina',
        'Svatoň', 'Svatoš', 'Světlík', 'Sviták', 'Svoboda', 'Svozil',
        'Sýkora', 'Synek', 'Syrový', 'Šafařík', 'Šafář', 'Šafránek',
        'Šálek', 'Šanda', 'Šašek', 'Šebek', 'Šebela', 'Šebesta', 'Šeda',
        'Šedivý', 'Šenk', 'Šesták', 'Ševčík', 'Šilhavý', 'Šimáček',
        'Šimák', 'Šimánek', 'Šíma', 'Šimčík', 'Šimeček', 'Šimek',
        'Šimon', 'Šimůnek', 'Šindelář', 'Šindler', 'Šípek', 'Šíp',
        'Široký', 'Šír', 'Šiška', 'Škoda', 'Škrabal', 'Šlechta',
        'Šmejkal', 'Šmerda', 'Šmíd', 'Šnajdr', 'Šolc', 'Špaček',
        'Špička', 'Šplíchal', 'Šrámek', 'Šťastný', 'Štefan',
        'Štefek', 'Štefl', 'Štěpánek', 'Štěpán', 'Štěrba', 'Šubrt',
        'Šulc', 'Šustr', 'Šváb', 'Švanda', 'Švarc', 'Švec', 'Švehla',
        'Švejda', 'Švestka', 'Táborský', 'Tancoš', 'Teplý', 'Tesař',
        'Tichý', 'Tománek', 'Toman', 'Tomášek', 'Tomáš', 'Tomeček',
        'Tomek', 'Tomeš', 'Tóth', 'Tran', 'Trávníček', 'Trčka', 'Trnka',
        'Trojan', 'Truhlář', 'Tříska', 'Tuček', 'Tůma', 'Tureček', 'Turek',
        'Tvrdík', 'Tvrdý', 'Uher', 'Uhlíř', 'Ulrich', 'Urbanec', 'Urbánek',
        'Urban', 'Vacek', 'Václavek', 'Václavík', 'Vaculík', 'Vágner',
        'Vácha', 'Valášek', 'Vala', 'Válek', 'Valenta', 'Valeš', 'Váňa',
        'Vančura', 'Vaněček', 'Vaněk', 'Vaníček', 'Varga', 'Vašák',
        'Vašek', 'Vašíček', 'Vávra', 'Vavřík', 'Večeřa', 'Vejvoda',
        'Verner', 'Veselý', 'Veverka', 'Vícha', 'Vilímek', 'Vinš', 'Víšek',
        'Vitásek', 'Vítek', 'Vít', 'Vlach', 'Vlasák', 'Vlček', 'Vlk',
        'Vobořil', 'Vodák', 'Vodička', 'Vodrážka', 'Vojáček', 'Vojta',
        'Vojtěch', 'Vojtek', 'Vojtíšek', 'Vokoun', 'Volek', 'Volf', 'Volný',
        'Vondráček', 'Vondrák', 'Vondra', 'Voráček', 'Vorel', 'Vorlíček',
        'Voříšek', 'Votava', 'Votruba', 'Vrabec', 'Vrána', 'Vrba', 'Vrzal',
        'Vybíral', 'Vydra', 'Vymazal', 'Vyskočil', 'Vysloužil', 'Wagner',
        'Walter', 'Weber', 'Weiss', 'Winkler', 'Wolf', 'Zábranský', 'Zahrádka',
        'Zahradník', 'Zach', 'Zajíc', 'Zajíček', 'Zálešák', 'Zámečník',
        'Zapletal', 'Záruba', 'Zatloukal', 'Zavadil', 'Zavřel', 'Zbořil',
        'Zdražil', 'Zedník', 'Zelenka', 'Zelený', 'Zelinka', 'Zemánek',
        'Zeman', 'Zezula', 'Zíka', 'Zikmund', 'Zima', 'Zlámal', 'Zoubek',
        'Zouhar', 'Zvěřina', 'Žáček', 'Žák', 'Žďárský', 'Žemlička',
        'Žídek', 'Žižka', 'Žůrek',
    ];

    protected static $lastNameFemale = [
        'Adamová', 'Adamcová', 'Adámková', 'Albrechtová', 'Ambrožová',
        'Andělová', 'Andrlová', 'Antošová', 'Bajerová', 'Balážová',
        'Balcarová', 'Balogová', 'Balounová', 'Baráková', 'Baranová',
        'Barešová', 'Bártová', 'Bartáková', 'Bartoňová', 'Bartošová',
        'Bartošková', 'Bartůňková', 'Baštová', 'Bauerová', 'Bayerová',
        'Bažantová', 'Bečková', 'Bečvářová', 'Bednářová',
        'Bednaříková', 'Bělohlávková', 'Bendová', 'Benešová',
        'Beranová', 'Beránková', 'Bergerová', 'Berková', 'Berkyová',
        'Bernardová', 'Bezděková', 'Bílková', 'Bílová', 'Bínová',
        'Bittnerová', 'Blahová', 'Bláhová', 'Blažková', 'Blechová',
        'Bobková', 'Bočková', 'Boháčová', 'Boháčková', 'Böhmová',
        'Borovičková', 'Boučková', 'Boudová', 'Boušková', 'Brabcová',
        'Brabencová', 'Bradová', 'Bradáčová', 'Braunová', 'Brázdová',
        'Brázdilová', 'Brejchová', 'Brožová', 'Brožková', 'Brychtová',
        'Březinová', 'Břízová', 'Bubeníková', 'Bučková', 'Buchtová',
        'Burdová', 'Burešová', 'Burianová', 'Buriánková', 'Byrtusová',
        'Cahová', 'Cibulková', 'Cihlářová', 'Císařová', 'Coufalová',
        'Čadová', 'Čápová', 'Čapková', 'Čechová', 'Čejková',
        'Čermáková', 'Černíková', 'Černohorská', 'Černochová',
        'Černá', 'Červeňáková', 'Červenková', 'Červená', 'Červinková',
        'Čiháková', 'Čížková', 'Čonková', 'Čurdová', 'Daňková',
        'Danielová', 'Danišová', 'Davidová', 'Dědková', 'Dittrichová',
        'Divišová', 'Dlouhá', 'Dobešová', 'Dobiášová', 'Dobrovolná',
        'Dočekalová', 'Dočkalová', 'Dohnalová', 'Dokoupilová',
        'Dolečková', 'Dolejšová', 'Dolejší', 'Doležalová', 'Doleželová',
        'Doskočilová', 'Dostálová', 'Doubková', 'Doubravová', 'Doušová',
        'Drábková', 'Drozdová', 'Dubská', 'Dudová', 'Dudková', 'Dufková',
        'Duchoňová', 'Dunková', 'Dušková', 'Dvorská', 'Dvořáčková',
        'Dvořáková', 'Eliášová', 'Erbenová', 'Fabiánová', 'Fantová',
        'Farkašová', 'Fejfarová', 'Fenclová', 'Ferencová', 'Fialová',
        'Fiedlerová', 'Filipová', 'Fischerová', 'Fišerová', 'Floriánová',
        'Fojtíková', 'Foltýnová', 'Formánková', 'Formanová', 'Fořtová',
        'Fousková', 'Francová', 'Fraňková', 'Franková', 'Fridrichová',
        'Frydrychová', 'Fučíková', 'Fuchsová', 'Fuksová', 'Gáborová',
        'Gabrielová', 'Gajdošová', 'Gregorová', 'Gruberová', 'Grundzová',
        'Grygarová', 'Hájková', 'Hajná', 'Hálová', 'Hamplová',
        'Hanáčková', 'Hánová', 'Hanáková', 'Hanousková', 'Hanusová',
        'Hanušová', 'Hanzalová', 'Hanzlová', 'Hanzlíková', 'Hartmanová',
        'Hašková', 'Havelová', 'Havelková', 'Havlíčková', 'Havlíková',
        'Havránková', 'Heczková', 'Hegerová', 'Hejdová', 'Hejduková',
        'Hejlová', 'Hejnová', 'Hendrychová', 'Hermanová', 'Heřmánková',
        'Heřmanová', 'Hladíková', 'Hladká', 'Hlaváčková', 'Hlaváčová',
        'Hlavatá', 'Hlávková', 'Hloušková', 'Hoffmannová', 'Hofmanová',
        'Holanová', 'Holasová', 'Holcová', 'Holečková', 'Holíková',
        'Holoubková', 'Holubová', 'Holá', 'Homolová', 'Homolková',
        'Horáčková', 'Horová', 'Horáková', 'Horká', 'Horňáková',
        'Horníčková', 'Horníková', 'Horská', 'Horváthová', 'Horvátová',
        'Hořejšíová', 'Hošková', 'Houdková', 'Houšková', 'Hovorková',
        'Hrabalová', 'Hrabovská', 'Hradecká', 'Hradilová', 'Hrbáčková',
        'Hrbková', 'Hrdinová', 'Hrdličková', 'Hrdá', 'Hrnčířová',
        'Hrochová', 'Hromádková', 'Hronová', 'Hrubešová', 'Hrubá',
        'Hrušková', 'Hrůzová', 'Hubáčková', 'Hudcová', 'Hudečková',
        'Hůlková', 'Humlová', 'Husáková', 'Hušková', 'Hýblová',
        'Hynková', 'Chaloupková', 'Chalupová', 'Charvátová', 'Chládková',
        'Chlupová', 'Chmelařová', 'Chmelíková', 'Chovancová', 'Chromá',
        'Chudobová', 'Chvátalová', 'Chvojková', 'Chytilová', 'Jahodová',
        'Jakešová', 'Jaklová', 'Jakoubková', 'Jakubcová', 'Janáčková',
        'Janáková', 'Janatová', 'Jančová', 'Jančíková', 'Jandová',
        'Janečková', 'Janečková', 'Janíčková', 'Janíková', 'Janková',
        'Janotová', 'Janoušková', 'Janovská', 'Jansová', 'Jánská',
        'Jarešová', 'Jarošová', 'Jašková', 'Javůrková', 'Jedličková',
        'Jechová', 'Jelenová', 'Jelínková', 'Jeníčková', 'Jeřábková',
        'Ježková', 'Ježová', 'Jílková', 'Jindrová', 'Jírová',
        'Jiráková', 'Jiránková', 'Jirásková', 'Jirková', 'Jirková',
        'Jiroušková', 'Jirsová', 'Jiříková', 'Johnová', 'Jonášová',
        'Junková', 'Jurčíková', 'Jurečková', 'Juřicová', 'Juříková',
        'Kabátová', 'Kačírková', 'Kadeřábková', 'Kadlcová', 'Kafková',
        'Kaiserová', 'Kalábová', 'Kalová', 'Kalašová', 'Kalinová',
        'Kalivodová', 'Kalousková', 'Kalousová', 'Kameníková', 'Kaňová',
        'Kaňková', 'Kantorová', 'Kaplanová', 'Karásková', 'Karasová',
        'Karbanová', 'Karelová', 'Karlíková', 'Kasalová', 'Kašíková',
        'Kašpárková', 'Kašparová', 'Kavková', 'Kazdová', 'Kindlová',
        'Klečková', 'Kleinová', 'Klementová', 'Klímová', 'Klimentová',
        'Klimešová', 'Kloučková', 'Kloudová', 'Knapová', 'Knotková',
        'Kociánová', 'Kocmanová', 'Kocourková', 'Kohoutková', 'Kohoutová',
        'Kochová', 'Koláčková', 'Kolaříková', 'Kolářová', 'Kolková',
        'Kolmanová', 'Komárková', 'Komínková', 'Konečná', 'Koníčková',
        'Kopalová', 'Kopecká', 'Kopečková', 'Kopečná', 'Kopřivová',
        'Korbelová', 'Kořínková', 'Kosíková', 'Kosinová', 'Kosová',
        'Kostková', 'Košťálová', 'Kotasová', 'Kotková', 'Kotlárová',
        'Kotrbová', 'Koubová', 'Koubková', 'Koudelová', 'Koudelková',
        'Koukalová', 'Kouřilová', 'Koutná', 'Kováčová', 'Kovaříková',
        'Kováříková', 'Kovářová', 'Kozáková', 'Kozelová',
        'Krajíčková', 'Králíčková', 'Králíková', 'Králová',
        'Krátká', 'Kratochvílová', 'Krausová', 'Krčmářová',
        'Krejčíková', 'Krejčová', 'Krejčířová', 'Krištofová',
        'Kropáčková', 'Kroupová', 'Krupová', 'Krupičková', 'Krupková',
        'Křečková', 'Křenková', 'Křivánková', 'Křížková',
        'Křížová', 'Kubová', 'Kubálková', 'Kubánková', 'Kubátová',
        'Kubcová', 'Kubelková', 'Kubešová', 'Kubicová', 'Kubíčková',
        'Kubíková', 'Kubínová', 'Kubišová', 'Kučová', 'Kučerová',
        'Kudláčková', 'Kudrnová', 'Kuchařová', 'Kuchtová', 'Kuklová',
        'Kulhánková', 'Kulhavá', 'Kuncová', 'Kunešová', 'Kupcová',
        'Kupková', 'Kurková', 'Kuželová', 'Kvapilová', 'Kvasničková',
        'Kynclová', 'Kyselová', 'Lacinová', 'Lacková', 'Lakatošová',
        'Landová', 'Langerová', 'Langová', 'Langrová', 'Látalová',
        'Lavičková', 'Lebedová', 'Levá', 'Líbalová', 'Linhartová',
        'Lišková', 'Lorencová', 'Loudová', 'Ludvíková', 'Lukáčová',
        'Lukášková', 'Lukášová', 'Lukešová', 'Macáková', 'Macková',
        'Macurová', 'Macháčková', 'Machačová', 'Macháčová', 'Machalová',
        'Machálková', 'Máchová', 'Machová', 'Majerová', 'Malečková',
        'Málková', 'Malíková', 'Malinová', 'Malá', 'Maňáková',
        'Marečková', 'Marková', 'Marešová', 'Maršálková',
        'Maršíková', 'Martincová', 'Martinková', 'Martínková',
        'Maříková', 'Masopustová', 'Mašková', 'Matějíčková',
        'Matějková', 'Matoušková', 'Matoušová', 'Matulová', 'Matušková',
        'Matyášová', 'Matysová', 'Maxová', 'Mayerová', 'Mazánková',
        'Medková', 'Melicharová', 'Menclová', 'Menšíková', 'Mertová',
        'Mičková', 'Michalcová', 'Michálková', 'Michalíková',
        'Michalová', 'Michnová', 'Miková', 'Míková', 'Mikešová',
        'Miková', 'Mikulová', 'Mikulášková', 'Minaříková', 'Minářová',
        'Mirgová', 'Mládková', 'Mlčochová', 'Mlejnková', 'Mojžíšová',
        'Mokrá', 'Molnárová', 'Moravcová', 'Morávková', 'Motlová',
        'Motyčková', 'Moučková', 'Moudrá', 'Mráčková', 'Mrázková',
        'Mrázová', 'Mrkvičková', 'Muchová', 'Müllerová', 'Műllerová',
        'Musilová', 'Mužíková', 'Myšková', 'Nagyová', 'Najmanová',
        'Navrátilová', 'Nečasová', 'Nedbalová', 'Nedomová', 'Nedvědová',
        'Nejedlá', 'Němcová', 'Němečková', 'Nesvadbová', 'Nešporová',
        'Neubauerová', 'Neumanová', 'Neumannová', 'Nguyenová', 'Vanová',
        'Nosková', 'Nováčková', 'Nováková', 'Novosadová', 'Novotná',
        'Nová', 'Odehnalová', 'Oláhová', 'Olivová', 'Ondráčková',
        'Ondrová', 'Orságová', 'Otáhalová', 'Palečková', 'Pánková',
        'Papežová', 'Pařízková', 'Pašková', 'Pátková', 'Patočková',
        'Paulová', 'Pavelková', 'Pavelková', 'Pavelová', 'Pavlasová',
        'Pavlicová', 'Pavlíčková', 'Pavlíková', 'Pavlová', 'Pazderová',
        'Pecková', 'Pecháčková', 'Pechová', 'Pechová', 'Pekárková',
        'Pekařová', 'Pelcová', 'Pelikánová', 'Pernicová', 'Peroutková',
        'Peřinová', 'Pešková', 'Pešková', 'Peštová', 'Peterková',
        'Petráková', 'Petrášová', 'Petrová', 'Petrová', 'Petříčková',
        'Petříková', 'Phamová', 'Píchová', 'Pilařová', 'Pilátová',
        'Píšová', 'Pivoňková', 'Plačková', 'Plachá', 'Plšková',
        'Pluhařová', 'Podzimková', 'Pohlová', 'Pokorná', 'Poláčková',
        'Poláchová', 'Poláková', 'Polanská', 'Polášková', 'Polívková',
        'Popelková', 'Pospíchalová', 'Pospíšilová', 'Potůčková',
        'Pourová', 'Prachařová', 'Prášková', 'Pražáková',
        'Prchalová', 'Procházková', 'Prokešová', 'Prokopová',
        'Prošková', 'Provazníková', 'Průchová', 'Průšová',
        'Přibylová', 'Příhodová', 'Přikrylová', 'Pšeničková',
        'Ptáčková', 'Rácová', 'Radová', 'Raková', 'Rambousková',
        'Rašková', 'Ratajová', 'Remešová', 'Rezková', 'Richterová',
        'Richtrová', 'Roubalová', 'Rousová', 'Rozsypalová', 'Rudolfová',
        'Růžková', 'Růžičková', 'Rybová', 'Rybářová', 'Rýdlová',
        'Ryšavá', 'Řeháčková', 'Řeháková', 'Řehořová', 'Řezáčová',
        'Řezníčková', 'Říhová', 'Sadílková', 'Samková', 'Sedláčková',
        'Sedláková', 'Sedlářová', 'Sehnalová', 'Seidlová', 'Seifertová',
        'Sekaninová', 'Semerádová', 'Severová', 'Schejbalová', 'Schmidtová',
        'Schneiderová', 'Schwarzová', 'Sikorová', 'Siváková', 'Skácelová',
        'Skalová', 'Skálová', 'Skalická', 'Sklenářová', 'Skopalová',
        'Skořepová', 'Skřivánková', 'Slabá', 'Sládková', 'Sladká',
        'Slámová', 'Slaninová', 'Slavíčková', 'Slavíková', 'Slezáková',
        'Slováčková', 'Slováková', 'Sluková', 'Smejkalová', 'Smékalová',
        'Smetanová', 'Smolová', 'Smolíková', 'Smolková', 'Smrčková',
        'Smržová', 'Smutná', 'Sobková', 'Sobotková', 'Sochorová',
        'Sojková', 'Sokolová', 'Sommerová', 'Součková', 'Soukupová',
        'Sovová', 'Spáčilová', 'Spurná', 'Srbová', 'Staňková',
        'Stárková', 'Stará', 'Stehlíková', 'Steinerová', 'Stejskalová',
        'Stiborová', 'Stoklasová', 'Straková', 'Stránská', 'Strejčková',
        'Strnadová', 'Strouhalová', 'Studená', 'Studničková',
        'Stuchlíková', 'Stupková', 'Suchánková', 'Suchomelová', 'Suchá',
        'Suková', 'Svačinová', 'Svatoňová', 'Svatošová', 'Světlíková',
        'Svitáková', 'Svobodová', 'Svozilová', 'Sýkorová', 'Synková',
        'Syrová', 'Šafaříková', 'Šafářová', 'Šafránková',
        'Šálková', 'Šandová', 'Šašková', 'Šebková', 'Šebelová',
        'Šebestová', 'Šedová', 'Šedivá', 'Šenková', 'Šestáková',
        'Ševčíková', 'Šilhavá', 'Šimáčková', 'Šimáková',
        'Šimánková', 'Šímová', 'Šimčíková', 'Šimečková', 'Šimková',
        'Šimonová', 'Šimůnková', 'Šindelářová', 'Šindlerová',
        'Šípková', 'Šípová', 'Široká', 'Šírová', 'Šišková',
        'Škodová', 'Škrabalová', 'Šlechtová', 'Šmejkalová', 'Šmerdová',
        'Šmídová', 'Šnajdrová', 'Šolcová', 'Špačková', 'Špičková',
        'Šplíchalová', 'Šrámková', 'Šťastná', 'Štefanová',
        'Štefková', 'Šteflová', 'Štěpánková', 'Štěpánová',
        'Štěrbová', 'Šubrtová', 'Šulcová', 'Šustrová', 'Švábová',
        'Švandová', 'Švarcová', 'Švecová', 'Švehlová', 'Švejdová',
        'Švestková', 'Táborská', 'Tancošová', 'Teplá', 'Tesařová',
        'Tichá', 'Tománková', 'Tomanová', 'Tomášková', 'Tomášová',
        'Tomečková', 'Tomková', 'Tomešová', 'Tóthová', 'Tranová',
        'Trávníčková', 'Trčková', 'Trnková', 'Trojanová', 'Truhlářová',
        'Třísková', 'Tučková', 'Tůmová', 'Turečková', 'Turková',
        'Tvrdíková', 'Tvrdá', 'Uherová', 'Uhlířová', 'Ulrichová',
        'Urbancová', 'Urbánková', 'Urbanová', 'Vacková', 'Václavková',
        'Václavíková', 'Vaculíková', 'Vágnerová', 'Váchová',
        'Valášková', 'Valová', 'Válková', 'Valentová', 'Valešová',
        'Váňová', 'Vančurová', 'Vaněčková', 'Vaňková', 'Vaníčková',
        'Vargová', 'Vašáková', 'Vašková', 'Vašíčková', 'Vávrová',
        'Vavříková', 'Večeřová', 'Vejvodová', 'Vernerová', 'Veselá',
        'Veverková', 'Víchová', 'Vilímková', 'Vinšová', 'Víšková',
        'Vitásková', 'Vítková', 'Vítová', 'Vlachová', 'Vlasáková',
        'Vlčková', 'Vlková', 'Vobořilová', 'Vodáková', 'Vodičková',
        'Vodrážková', 'Vojáčková', 'Vojtová', 'Vojtěchová',
        'Vojtková', 'Vojtíšková', 'Vokounová', 'Volková', 'Volfová',
        'Volná', 'Vondráčková', 'Vondráková', 'Vondrová', 'Voráčková',
        'Vorlová', 'Vorlíčková', 'Voříšková', 'Votavová', 'Votrubová',
        'Vrabcová', 'Vránová', 'Vrbová', 'Vrzalová', 'Vybíralová',
        'Vydrová', 'Vymazalová', 'Vyskočilová', 'Vysloužilová',
        'Wagnerová', 'Walterová', 'Weberová', 'Weissová', 'Winklerová',
        'Wolfová', 'Zábranská', 'Zahrádková', 'Zahradníková', 'Zachová',
        'Zajícová', 'Zajíčková', 'Zálešáková', 'Zámečníková',
        'Zapletalová', 'Zárubová', 'Zatloukalová', 'Zavadilová',
        'Zavřelová', 'Zbořilová', 'Zdražilová', 'Zedníková', 'Zelenková',
        'Zelená', 'Zelinková', 'Zemánková', 'Zemanová', 'Zezulová',
        'Zíková', 'Zikmundová', 'Zimová', 'Zlámalová', 'Zoubková',
        'Zouharová', 'Zvěřinová', 'Žáčková', 'Žáková', 'Žďárská',
        'Žemličková', 'Žídková', 'Žižková', 'Žůrková',
    ];

    protected static $title = [
        'Bc.', 'Ing.', 'MUDr.', 'MVDr.', 'Mgr.', 'JUDr.', 'PhDr.', 'RNDr.', 'doc.', 'Dr.',
    ];

    /**
     * @param string|null $gender 'male', 'female' or null for any
     * @param int         $minAge minimal age of "generated person" in years
     * @param int         $maxAge maximal age of "generated person" in years
     *
     * @return string czech birth number
     */
    public function birthNumber($gender = null, $minAge = 0, $maxAge = 100, $slashProbability = 50)
    {
        if ($gender === null) {
            $gender = $this->generator->boolean() ? static::GENDER_MALE : static::GENDER_FEMALE;
        }

        $startTimestamp = strtotime(sprintf('-%d year', $maxAge));
        $endTimestamp = strtotime(sprintf('-%d year', $minAge));
        $randTimestamp = self::numberBetween($startTimestamp, $endTimestamp);

        $year = (int) (date('Y', $randTimestamp));
        $month = (int) (date('n', $randTimestamp));
        $day = (int) (date('j', $randTimestamp));
        $suffix = self::numberBetween(0, 999);

        // women has +50 to month
        if ($gender == static::GENDER_FEMALE) {
            $month += 50;
        }

        // from year 2004 everyone has +20 to month when birth numbers in one day are exhausted
        if ($year >= 2004 && $this->generator->boolean(10)) {
            $month += 20;
        }

        $birthNumber = sprintf('%02d%02d%02d%03d', $year % 100, $month, $day, $suffix);

        // from year 1954 birth number includes CRC
        if ($year >= 1954) {
            $crc = intval($birthNumber, 10) % 11;

            if ($crc == 10) {
                $crc = 0;
            }
            $birthNumber .= sprintf('%d', $crc);
        }

        // add slash
        if ($this->generator->boolean($slashProbability)) {
            $birthNumber = substr($birthNumber, 0, 6) . '/' . substr($birthNumber, 6);
        }

        return $birthNumber;
    }

    public static function birthNumberMale()
    {
        return static::birthNumber(static::GENDER_MALE);
    }

    public static function birthNumberFemale()
    {
        return static::birthNumber(static::GENDER_FEMALE);
    }

    public function title($gender = null)
    {
        return static::titleMale();
    }

    /**
     * replaced by specific unisex Czech title
     */
    public static function titleMale()
    {
        return static::randomElement(static::$title);
    }

    /**
     * replaced by specific unisex Czech title
     */
    public static function titleFemale()
    {
        return static::titleMale();
    }

    /**
     * @param string|null $gender 'male', 'female' or null for any
     *
     * @example 'Albrecht'
     */
    public function lastName($gender = null)
    {
        if ($gender === static::GENDER_MALE) {
            return static::lastNameMale();
        }

        if ($gender === static::GENDER_FEMALE) {
            return static::lastNameFemale();
        }

        return $this->generator->parse(static::randomElement(static::$lastNameFormat));
    }

    public static function lastNameMale()
    {
        return static::randomElement(static::$lastNameMale);
    }

    public static function lastNameFemale()
    {
        return static::randomElement(static::$lastNameFemale);
    }
}
