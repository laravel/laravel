<?php

namespace Faker\Provider\lt_LT;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastNameMale}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastNameFemale}}',
    ];

    protected static $lastNameFormat = [
        '{{firstNameMale}}',
        '{{firstNameFemale}}',
    ];

    protected static $titleMale = ['p.', 'ponas'];

    protected static $titleFemale = ['p.', 'ponia', 'panelė'];

    /**
     * @see https://lt.wikipedia.org/wiki/S%C4%85ra%C5%A1as:Lietuvoje_paplit%C4%99_vardai
     */
    protected static $firstNameMale = [
        'Abramas', 'Abraomas', 'Achilas', 'Adalbertas', 'Adamas', 'Adas', 'Adolfas', 'Adolis', 'Adomas',
        'Adrijus', 'Agatas', 'Agnius', 'Aidas', 'Ainius', 'Aistis', 'Aivaras', 'Akimas', 'Akvilinas', 'Albertas', 'Albrechtas',
        'Albinas', 'Aldonas', 'Aleksandras', 'Aleksas', 'Alenas', 'Alfas', 'Alfonsas', 'Alfredas', 'Algimantas', 'Algirdas',
        'Algis', 'Alius', 'Almantas', 'Almis', 'Almonas', 'Aloyzas', 'Alpas', 'Alpis', 'Alvidas', 'Alvydas', 'Ambraziejus',
        'Anatolijus', 'Anatolis', 'Andreas', 'Andriejus', 'Andrius', 'Andžejus', 'Anicetas', 'Anisimas', 'Antanas', 'Antonas',
        'Antonijus', 'Antonis', 'Anupras', 'Anzelmas', 'Apolinaras', 'Apolonijus', 'Aras', 'Arijus', 'Arimantas', 'Aristarchas',
        'Aristidas', 'Arkadijus', 'Armantas', 'Arminas', 'Arnas', 'Arnoldas', 'Aronas', 'Arsenas', 'Arsenijus', 'Artas',
        'Artiomas', 'Artūras', 'Arūnas', 'Arvaidas', 'Arvydas', 'Astijus', 'Audrius', 'Audrys', 'Audronius', 'Augis',
        'Augustas', 'Augustinas', 'Aurelijus', 'Aurimas', 'Aušrius', 'Aušrys', 'Ąžuolas', 'Balys', 'Baltazaras', 'Baltramiejus',
        'Baltrus', 'Banguolis', 'Bartas', 'Bartvydas', 'Bazilijus', 'Benas', 'Benediktas', 'Benonas', 'Benius', 'Benjaminas',
        'Bernardas', 'Beržas', 'Bijūnas', 'Bogdanas', 'Boguslavas', 'Boleslavas', 'Boleslovas', 'Bonifacas', 'Borisas',
        'Bronislavas', 'Bronislovas', 'Bronius', 'Brunas', 'Brunonas', 'Cecilijus', 'Celestinas', 'Cezaris', 'Chaimas',
        'Charitonas', 'Ciprijonas', 'Česius', 'Česlovas', 'Čiogintas', 'Dainius', 'Daivis', 'Dalius', 'Damijonas', 'Danas',
        'Dangerutis', 'Danielius', 'Danila', 'Danius', 'Darijus', 'Darius', 'Dariušas', 'Daumantas', 'Davidas', 'Deimantas',
        'Deividas', 'Deivis', 'Demetrijus', 'Demjanas', 'Denis', 'Denisas', 'Dimitrijus', 'Diomidas', 'Dionizas', 'Dmitrijus',
        'Dobilas', 'Donatas', 'Domantas', 'Domas', 'Dominykas', 'Donaldas', 'Dovydas', 'Dovilis', 'Dovis', 'Drąsius',
        'Drąsutis', 'Džeraldas', 'Džiraldas', 'Džiugas', 'Džonis', 'Edgaras', 'Edmundas', 'Eduardas', 'Edvardas', 'Edvinas',
        'Egidijus', 'Eidimantas', 'Eidminas', 'Eidvydas', 'Eimantas', 'Eimis', 'Einius', 'Eivydas', 'Eldaras', 'Eligijus',
        'Elijus', 'Elmantas', 'Emanuelis', 'Emilis', 'Emilijonas', 'Emilijus', 'Enrikas', 'Erazmas', 'Erdvilas', 'Erichas',
        'Erikas', 'Ernestas', 'Ervinas', 'Eugenijus', 'Eugeniušas', 'Evaldas', 'Fabijus', 'Faustas', 'Fedoras', 'Felicijonas',
        'Felicijus', 'Feliksas', 'Ferdinandas', 'Filipas', 'Fiodoras', 'Foma', 'Flavijus', 'Florijonas', 'Francas',
        'Francišekas', 'Fredas', 'Fridrikas', 'Gabrielis', 'Gabrielius', 'Gailimantas', 'Gailius', 'Galmantas', 'Gasparas',
        'Gaudenis', 'Gaudrimas', 'Gaudvydas', 'Gavrila', 'Gavrilas', 'Gedas', 'Gedgaudas', 'Gediminas', 'Gedmantas', 'Gedmas',
        'Gedminas', 'Gedvaldas', 'Gedvydas', 'Gedvilas', 'Geivydas', 'Genadijus', 'Gendrius', 'Genrichas', 'Georgijus',
        'Geraldas', 'Gerardas', 'Gerdas', 'Gerimantas', 'Germanas', 'Germantas', 'Gerutis', 'Gervydas', 'Giedrius', 'Gilbertas',
        'Gintaras', 'Gintas', 'Gintis', 'Gintautas', 'Girėnas', 'Girius', 'Girmantas', 'Girvydas', 'Gitanas', 'Gytautas',
        'Gytis', 'Gordejus', 'Gotfridas', 'Gracijonas', 'Gracijus', 'Gražvydas', 'Grigalius', 'Grigas', 'Grigorijus', 'Gunaras',
        'Gustas', 'Gustavas', 'Gustis', 'Gvidas', 'Gvidonas', 'Haraldas', 'Haris', 'Haroldas', 'Hektoras', 'Helmutas',
        'Henrikas', 'Henris', 'Herbertas', 'Herkus', 'Hermanas', 'Hilarijus', 'Horacijus', 'Horstas', 'Hubertas', 'Ignacas',
        'Ignas', 'Ignotas', 'Igoris', 'Ilja', 'Imantas', 'Indrius', 'Ingvaras', 'Inocentas', 'Ipolitas', 'Irenijus', 'Irmantas',
        'Irtautas', 'Irvydas', 'Isaakas', 'Isakas', 'Ivanas', 'Izidorius', 'Izoldas', 'Jacekas', 'Jakovas', 'Jakubas', 'Janas',
        'Janis', 'Jankelis', 'Janušas', 'Jaroslavas', 'Jaunius', 'Jaunutis', 'Jegoras', 'Jemeljanas', 'Jeronimas', 'Jevgenijus',
        'Ježis', 'Joanas', 'Jogaila', 'Jogintas', 'Jogirdas', 'Jokimas', 'Jokūbas', 'Jolantas', 'Jomantas', 'Jonaras', 'Jonas',
        'Jonis', 'Joris', 'Jorūnas', 'Josifas', 'Jotautas', 'Jovaldas', 'Jovaras', 'Jovitas', 'Judrius', 'Julijonas', 'Julijus',
        'Julius', 'Juljanas', 'Juozapas', 'Juozapatas', 'Juozas', 'Juras', 'Jurgis', 'Jurijus', 'Jūras', 'Jūris', 'Justas',
        'Justinas', 'Juvencijus', 'Juzefas', 'Kajetonas', 'Kajus', 'Kalikstas', 'Kalnius', 'Kamilis', 'Kaributas', 'Karlas',
        'Karolis', 'Karpas', 'Kasparas', 'Kastantas', 'Kastytis', 'Kazimieras', 'Kazys', 'Kęstas', 'Kęstautas', 'Kęstutis',
        'Kimas', 'Kipras', 'Kiprijonas', 'Kirilas', 'Klaudas', 'Klaudijus', 'Klemas', 'Klemensas', 'Klementas', 'Kleopas',
        'Klevas', 'Klimas', 'Klimentijus', 'Kondratas', 'Konradas', 'Konstantinas', 'Kornelijus', 'Kostas', 'Kovas', 'Kozmas',
        'Krescencijus', 'Kristijonas', 'Kristinas', 'Kristoforas', 'Kristupas', 'Ksaveras', 'Kšištofas', 'Kuprijanas', 'Laimis',
        'Laimonas', 'Laimutis', 'Laisvydas', 'Laisvis', 'Laisvūnas', 'Lauras', 'Laurentijus', 'Laurynas', 'Lauris',
        'Lavrentijus', 'Leandras', 'Leonardas', 'Leonas', 'Leonidas', 'Leopoldas', 'Levas', 'Libertas', 'Linas', 'Lionginas',
        'Liubartas', 'Liubomiras', 'Liucijonas', 'Liucijus', 'Liudas', 'Liudvigas', 'Liudvikas', 'Liūtas', 'Liutauras',
        'Livijus', 'Lozorius', 'Lukas', 'Lukrecijus', 'Makaras', 'Makarijus', 'Maksas', 'Maksimas', 'Maksimilijonas',
        'Mamertas', 'Manfredas', 'Mangirdas', 'Mantas', 'Mantautas', 'Mantrimas', 'Mantvydas', 'Maratas', 'Marcelijus ',
        'Marcelinas', 'Marcelius', 'Marekas', 'Margiris', 'Marianas', 'Marijonas', 'Marijus', 'Marinas', 'Marius', 'Markas',
        'Martas', 'Martinas', 'Martynas', 'Matas', 'Mateušas', 'Matvejus', 'Mažvydas', 'Mečislavas', 'Mečislovas', 'Mečys',
        'Medardas', 'Medas', 'Mefodijus', 'Melanijus', 'Melchioras', 'Mendelis', 'Merkys', 'Merūnas', 'Michalas', 'Michailas',
        'Miglius', 'Mikalojus', 'Mikas', 'Mikolajus', 'Milanas', 'Mildas', 'Milvydas', 'Mindaugas', 'Minijus', 'Mykolas',
        'Mingaudas', 'Mintaras', 'Miroslavas', 'Modestas', 'Morkus', 'Motiejus', 'Mozė', 'Naglis', 'Napalis', 'Napalys',
        'Napoleonas', 'Napolis', 'Narcizas', 'Narimantas', 'Narsutis', 'Narvydas', 'Natanas', 'Natas', 'Naumas', 'Nauris',
        'Nazaras', 'Nazarijus', 'Nedas', 'Neimantas', 'Neivydas', 'Nemunas', 'Nerijus', 'Nerimantas', 'Nerimas', 'Neringas',
        'Nerius', 'Nidas', 'Nikandras', 'Nikas', 'Nikiforas', 'Nikita', 'Nikodemas', 'Nikola', 'Nikolajus', 'Nilas', 'Nojus',
        'Nomedas', 'Norbertas', 'Normanas', 'Normantas', 'Nortautas', 'Norvydas', 'Norvilas', 'Oktavijus', 'Olegas', 'Orestas',
        'Orintas', 'Oskaras', 'Osmundas', 'Osvaldas', 'Otas', 'Otilijus', 'Otonas', 'Ovidijus', 'Palemonas', 'Palmyras',
        'Patricijus', 'Patrikas', 'Paulis', 'Paulius', 'Petras', 'Pijus', 'Pilypas', 'Pilėnas', 'Piotras', 'Platonas',
        'Polikarpas', 'Polis', 'Povilas', 'Pranas', 'Pranciškus', 'Putinas', 'Radvila', 'Rafaelis', 'Rafailas', 'Rafalas',
        'Raigardas', 'Raimondas', 'Raimundas', 'Rainoldas', 'Ralfas', 'Ramintas', 'Ramonas', 'Ramūnas', 'Rapolas', 'Rasius',
        'Raulis', 'Redas', 'Regimantas', 'Reginaldas', 'Reinhardas', 'Remas', 'Remigijus', 'Renaldas', 'Renatas', 'Renius',
        'Richardas', 'Ričardas', 'Rikardas', 'Rimantas', 'Rimas', 'Rimgaudas', 'Rimtas', 'Rimtautas', 'Rimtis', 'Rimvydas',
        'Rinatas', 'Ryšardas', 'Rytas', 'Rytautas', 'Rytis', 'Robertas', 'Robinas', 'Rodrigas', 'Rokas', 'Rolandas', 'Rolfas',
        'Romanas', 'Romas', 'Romualdas', 'Ronaldas', 'Rostislavas', 'Rubenas', 'Rudolfas', 'Rufas', 'Rufinas', 'Rupertas',
        'Ruslanas', 'Rūtenis', 'Sabinas', 'Sakalas', 'Saliamonas', 'Salvijus', 'Samuelis', 'Samsonas', 'Samuilas', 'Sandras',
        'Santaras', 'Saulenis', 'Saulius', 'Sava', 'Sebastijonas', 'Semas', 'Semionas', 'Serafinas', 'Serapinas', 'Sergejus',
        'Sergijus', 'Seržas', 'Severas', 'Severinas', 'Sidas', 'Sidoras', 'Sigis', 'Sigitas', 'Sigizmundas', 'Sikstas',
        'Silverijus', 'Silvestras', 'Silvijus', 'Simas', 'Simeonas', 'Simonas', 'Sirvydas', 'Skaidrius', 'Skaistis',
        'Skalmantas', 'Skalvis', 'Skirgaila', 'Skirmantas', 'Skomantas', 'Sonetas', 'Stanislavas', 'Stanislovas', 'Stasys',
        'Stasius', 'Stepas', 'Stefanas', 'Stepanas', 'Steponas', 'Svajūnas', 'Svajus', 'Sviatoslavas', 'Šarūnas', 'Šiaurys',
        'Švitrigaila', 'Tadas', 'Tadeušas', 'Tamošius', 'Tarasas', 'Tauras', 'Tautginas', 'Tautrimas', 'Tautvydas', 'Tedas',
        'Telesforas', 'Teisius', 'Teisutis', 'Teodoras', 'Teofilis', 'Terentijus', 'Tiberijus', 'Timas', 'Timotiejus',
        'Timotis', 'Timūras', 'Titas', 'Tomas', 'Tomašas', 'Tonis', 'Traidenis', 'Trofimas', 'Tumas', 'Ugnius', 'Ulrikas',
        'Uosis', 'Urbonas', 'Utenis', 'Ubaldas', 'Ūdrys', 'Ūkas', 'Vacys', 'Vacius', 'Vaclovas', 'Vadimas', 'Vaidas',
        'Vaidevutis', 'Vaidila', 'Vaidis', 'Vaidotas', 'Vaidutis', 'Vaigaudas', 'Vaigirdas', 'Vainius', 'Vainoras', 'Vaitiekus',
        'Vaižgantas', 'Vakaris', 'Valdas', 'Valdemaras', 'Valdimantas', 'Valdis', 'Valentas', 'Valentinas', 'Valerijonas',
        'Valerijus', 'Valys', 'Valius', 'Valteris', 'Vasaris', 'Vasilijus', 'Venantas', 'Verneris', 'Vėjas', 'Vėjūnas',
        'Venjaminas', 'Vergilijus', 'Vestas', 'Viačeslavas', 'Vidas', 'Vydas', 'Vidimantas', 'Vydimantas', 'Vidmantas',
        'Vydmantas', 'Viesulas', 'Vygaudas', 'Vigilijus', 'Vygintas', 'Vygirdas', 'Vykantas', 'Vykintas', 'Viktas', 'Viktoras',
        'Viktorijus', 'Viktorinas', 'Vilenas', 'Vilgaudas', 'Vilhelmas', 'Vilijus', 'Vilius', 'Vylius', 'Vilmantas', 'Vilmas',
        'Vilnius', 'Viltaras', 'Viltautas', 'Viltenis', 'Vincas', 'Vincentas', 'Vingaudas', 'Virgaudas', 'Virgilijus',
        'Virginijus', 'Virgintas', 'Virgis', 'Virgius', 'Virmantas', 'Vismantas', 'Visvaldas', 'Visvaldis', 'Vitalijus',
        'Vitalis', 'Vitalius', 'Vitas', 'Vitoldas', 'Vygandas', 'Vygantas', 'Vykintas', 'Vytaras', 'Vytautas', 'Vytas',
        'Vytenis', 'Vytis', 'Vyturys', 'Vladas', 'Vladimiras', 'Vladislavas', 'Vladislovas', 'Vladlenas', 'Voicechas',
        'Voldemaras', 'Vsevolodas', 'Zacharijus', 'Zakarijus', 'Zbignevas', 'Zdislavas', 'Zenius', 'Zenonas', 'Zigfridas',
        'Zygfridas', 'Zigmantas', 'Zigmas', 'Zygmuntas', 'Zinovijus', 'Žanas', 'Žeimantas', 'Žilvinas', 'Žibartas', 'Žybartas',
        'Žydrius', 'Žydrūnas', 'Žygaudas', 'Žygimantas', 'Žygintas', 'Žygis', 'Žymantas', 'Žvaigždžius',
    ];

    /**
     * @see https://lt.wikipedia.org/wiki/S%C4%85ra%C5%A1as:Lietuvoje_paplit%C4%99_vardai
     */
    protected static $firstNameFemale = [
        'Ada', 'Adelė', 'Adelija', 'Adelina', 'Adolfa', 'Adolfina',
        'Adriana', 'Adrija', 'Adrijana', 'Agata', 'Agnė', 'Agnetė', 'Agnieška', 'Agnietė', 'Agnija',
        'Agota', 'Agripina', 'Aida', 'Aidė', 'Aimana', 'Aimantė', 'Aina', 'Ainė', 'Airė', 'Airida', 'Aistė',
        'Aistra', 'Aitra', 'Aivara', 'Akvilė', 'Akvilina', 'Alana', 'Alanta', 'Alberta', 'Albertina',
        'Albina', 'Alda', 'Aldona', 'Alė', 'Aleksandra', 'Aleksandrina', 'Aleksė', 'Aleta', 'Alfonsė',
        'Alfonsa', 'Alfreda', 'Algė', 'Algimanta', 'Algimantė', 'Algina', 'Algirdė', 'Algutė', 'Alicija',
        'Alina', 'Aliodija', 'Aliona', 'Alisa', 'Alma', 'Aloyza', 'Alona', 'Alva', 'Alvyda', 'Alvydė',
        'Alvita', 'Amalija', 'Amanda', 'Ana', 'Anastasija', 'Anastazija', 'Andrė', 'Andrėja', 'Andžela',
        'Anė', 'Anelė', 'Aneta', 'Anetė', 'Angelė', 'Angelina', 'Aniceta', 'Antanina', 'Antonida',
        'Antonija', 'Antonina', 'Anzelma', 'Apolinarija', 'Apolonija', 'Ara', 'Ariadnė', 'Arija',
        'Arimantė', 'Arina', 'Aristida', 'Armina', 'Arminta', 'Arnė', 'Arnolda', 'Arūnė', 'Arvydė', 'Asta',
        'Astija', 'Astra', 'Astrida', 'Ašara', 'Atėnė', 'Audra', 'Audrė', 'Audronė', 'Augustė', 'Augustina',
        'Augutė', 'Auksė', 'Auksuolė', 'Aura', 'Aurėja', 'Aurelija', 'Aurora', 'Austė', 'Austėja', 'Austra',
        'Aušra', 'Aušrinė', 'Banga', 'Banguolė', 'Barbara', 'Barbora', 'Bargailė', 'Bartė', 'Basia',
        'Beata', 'Beatričė', 'Benedikta', 'Benė', 'Benigna', 'Benita', 'Benjamina', 'Bernadeta', 'Bernarda',
        'Bernardina', 'Berta', 'Beta', 'Biruta', 'Birutė', 'Bytautė', 'Bitė', 'Boleslava', 'Boleslova',
        'Brigita', 'Bronė', 'Bronislava', 'Bronislova', 'Božena', 'Cecilė', 'Cecilija', 'Celestina',
        'Celina', 'Cezarija', 'Cilė', 'Cintija', 'Dagmara', 'Dagna', 'Dagnė', 'Daina', 'Dainė', 'Dainora',
        'Daiva', 'Daivita', 'Daivutė', 'Dalė', 'Dalia', 'Dalija', 'Dalytė', 'Dana', 'Danė', 'Dangė',
        'Dangerutė', 'Dangira', 'Daniela', 'Danielė', 'Danguolė', 'Danuta', 'Danutė', 'Darata', 'Daria',
        'Darija', 'Darja', 'Daugailė', 'Daumantė', 'Debora', 'Deima', 'Deimantė', 'Deivė', 'Deivilė',
        'Demetra', 'Diana', 'Dijana', 'Dina', 'Dinara', 'Dita', 'Ditė', 'Doloresa', 'Doma', 'Domantė',
        'Domicelė', 'Dominika', 'Dominyka', 'Dona', 'Donalda', 'Donata', 'Dora', 'Dorota', 'Dorotė',
        'Dorotėja', 'Dovilė', 'Džeinė', 'Džeralda', 'Džesika', 'Džilda', 'Džina', 'Džiugė', 'Džiuginta',
        'Džiulija', 'Džiuljeta', 'Džordana', 'Džulija', 'Edita', 'Eglė', 'Egida', 'Egidija', 'Eidvilė',
        'Eimantė', 'Einara', 'Eiva', 'Ela', 'Elada', 'Elė', 'Elegija', 'Elena', 'Eleonora', 'Elfrida',
        'Elija', 'Elytė', 'Eliza', 'Elma', 'Elona', 'Elvira', 'Elvyra', 'Elza', 'Elzė', 'Elžbieta', 'Ema',
        'Emanuelė', 'Emilė', 'Emilija', 'Enrika', 'Erdvilė', 'Erika', 'Ermina', 'Erna', 'Ernesta',
        'Ernestina', 'Ervina', 'Esmeralda', 'Estela', 'Estera', 'Eufrozina', 'Eugenija', 'Eulalija', 'Eva',
        'Evalda', 'Evelina', 'Fabija', 'Faina', 'Faustina', 'Felicija', 'Felicita', 'Feliksa', 'Fernanda',
        'Filomena', 'Freda', 'Frida', 'Gabeta', 'Gabija', 'Gabriela', 'Gabrielė', 'Gailė', 'Gailiūtė',
        'Gailutė', 'Gaiva', 'Gaivilė', 'Gaja', 'Galia', 'Galina', 'Gaudencija', 'Gaudrė', 'Geda',
        'Gedimina', 'Gediminė', 'Gedmantė', 'Gedmintė', 'Gedvyda', 'Geida', 'Geismantė', 'Geistė', 'Gelena',
        'Gėlė', 'Gelmė', 'Gema', 'Gena', 'Genadija', 'Gendrė', 'Genė', 'Genovaitė', 'Genovefa', 'Genutė',
        'Georgina', 'Gerarda', 'Gerda', 'Germantė', 'Gerta', 'Gertė', 'Gertruda', 'Gertrūda', 'Geta',
        'Giedra', 'Giedrė', 'Gilda', 'Gilė', 'Gilija', 'Gilma', 'Gina', 'Gintara', 'Gintarė', 'Gintautė',
        'Gintė', 'Girstautė', 'Girstė', 'Gita', 'Gitana', 'Gitė', 'Gytė', 'Gizela', 'Glorija', 'Gluosnė',
        'Goda', 'Gotautė', 'Gotė', 'Gracija', 'Grasilda', 'Gražina', 'Gražyna', 'Gražvyda', 'Greta',
        'Grėtė', 'Grita', 'Grytė', 'Gunda', 'Guoda', 'Gustė', 'Gustina', 'Halina', 'Hana', 'Helena',
        'Henrika', 'Helga', 'Henrieta', 'Henrietė', 'Herma', 'Hiacinta', 'Hilda', 'Honorata', 'Hortenzija',
        'Ida', 'Idalija', 'Ieva', 'Ievutė', 'Ignė', 'Ignota', 'Ilma', 'Ilmena', 'Ilona', 'Ilzė', 'Imantė',
        'Ina', 'Indra', 'Indraja', 'Indrė', 'Inesa', 'Ineta', 'Inga', 'Ingita', 'Ingė', 'Ingeborga',
        'Ingrida', 'Ira', 'Irena', 'Irida', 'Iridė', 'Irina', 'Irma', 'Irmanta', 'Irmantė', 'Irmina',
        'Irmutė', 'Irta', 'Irtautė', 'Irutė', 'Isabela', 'Iva', 'Ivana', 'Ivona', 'Iveta', 'Iza', 'Izabela',
        'Izabelė', 'Izidė', 'Izidora', 'Izolda', 'Jadzė', 'Jadviga', 'Jadvyga', 'Jana', 'Janė', 'Janina',
        'Januarija', 'Jaunė', 'Jaunutė', 'Jekaterina', 'Jelena', 'Jelizaveta', 'Jeronima', 'Jevdokija',
        'Jieva', 'Joana', 'Jogailė', 'Jogilė', 'Jogintė', 'Jola', 'Jolanta', 'Joleta', 'Jolita', 'Jomantė',
        'Jomilė', 'Jonė', 'Jorė', 'Jorigė', 'Jorūnė', 'Jotvingė', 'Jovilė', 'Jovita', 'Judita', 'Judra',
        'Judrė', 'Julė', 'Juliana', 'Julija', 'Julijana', 'Julijona', 'Julita', 'Julytė', 'Juozapina',
        'Juozapota', 'Juozė', 'Jura', 'Jūra', 'Jūrė', 'Jūratė', 'Jurga', 'Jurgė', 'Jurgina', 'Jurgita',
        'Justė', 'Justina', 'Juta', 'Juventa', 'Juzefa', 'Kaja', 'Kamila', 'Kamilė', 'Karina', 'Karla',
        'Karmela', 'Karolė', 'Karolina', 'Kasia', 'Kastė', 'Kastytė', 'Katarina', 'Katažina', 'Katažyna',
        'Katerina', 'Katia', 'Katrė', 'Kazė', 'Kazimiera', 'Kazimira', 'Kazytė', 'Kęstė', 'Kira', 'Klara',
        'Klarisa', 'Klaudija', 'Klema', 'Klementina', 'Kleopa', 'Kleopatra', 'Klotilda', 'Konstancija',
        'Konstantina', 'Kornelija', 'Kostė', 'Kotryna', 'Krista', 'Kristė', 'Kristijona', 'Kristina',
        'Krystyna', 'Ksavera', 'Ksaverija', 'Ksenija', 'Kunigunda', 'Lada', 'Laima', 'Laimė', 'Laimona',
        'Laimutė', 'Laisvė', 'Laisvyda', 'Laisvydė', 'Laisvūnė', 'Lana', 'Lara', 'Larisa', 'Lauma', 'Laura',
        'Laurena', 'Laurentina', 'Lauryna', 'Leandra', 'Leda', 'Leila', 'Lėja', 'Lelija', 'Lena',
        'Leokadija', 'Leona', 'Leonarda', 'Leonė', 'Leonida', 'Leonija', 'Leonila', 'Leonilė', 'Leonora',
        'Leontina', 'Leopolda', 'Leta', 'Lėta', 'Leticija', 'Leva', 'Levutė', 'Liana', 'Liauda', 'Liberta',
        'Lida', 'Lidija', 'Liepa', 'Lijana', 'Lilė', 'Liliana', 'Lilija', 'Lilijana', 'Lina', 'Linda',
        'Lingailė', 'Linė', 'Lionė', 'Liongina', 'Liuba', 'Liubarta', 'Liubovė', 'Liucė', 'Liucilė',
        'Liucina', 'Liucija', 'Liuda', 'Liudmila', 'Liudvika', 'Liūnė', 'Liutaura', 'Liva', 'Liveta',
        'Livija', 'Liza', 'Lizaveta', 'Lola', 'Lolita', 'Longina', 'Lora', 'Lorena', 'Loreta', 'Lorija',
        'Lucyna', 'Luisa', 'Luiza', 'Luknė', 'Lukrecija', 'Magda', 'Magdalena', 'Magdė', 'Maja', 'Malda',
        'Malgožata', 'Malvina', 'Mamerta', 'Mamertina', 'Mantautė', 'Mantė', 'Mantvydė', 'Manuela', 'Mara',
        'Marcė', 'Marcelė', 'Marcelija', 'Marcelina', 'Marcijona', 'Marė', 'Marilė', 'Margita', 'Margarita',
        'Mariana', 'Marija', 'Marijona', 'Marina', 'Marita', 'Marytė', 'Marta', 'Martina', 'Martyna',
        'Matilda', 'Matriona', 'Mažvydė', 'Mečislava', 'Mečislova', 'Meda', 'Medeina', 'Medėja', 'Megana',
        'Megė', 'Meilė', 'Meilutė', 'Melanija', 'Melda', 'Melisa', 'Mėnulė', 'Mėta', 'Michalina', 'Miglė',
        'Mika', 'Mikalina', 'Mykolė', 'Mila', 'Milda', 'Mildutė', 'Milena', 'Milvydė', 'Mindaugė',
        'Mingailė', 'Minija', 'Mintara', 'Mintarė', 'Mintautė', 'Mintė', 'Mira', 'Mirga', 'Modesta', 'Mona',
        'Monika', 'Morta', 'Nadė', 'Nadia', 'Nadežda', 'Nadiežda', 'Naktis', 'Narciza', 'Nastasija',
        'Nastazija', 'Nastė', 'Nastia', 'Natalija', 'Nata', 'Neda', 'Neimantė', 'Nela', 'Nelė', 'Nemira',
        'Nemunė', 'Nendrė', 'Neringa', 'Nerita', 'Nida', 'Nijolė', 'Nika', 'Nikė', 'Nila', 'Nilė', 'Nina',
        'Ninelė', 'Noja', 'Nomeda', 'Nona', 'Nora', 'Norberta', 'Norma', 'Norgailė', 'Normantė', 'Nortė',
        'Norvyda', 'Norvilė', 'Odeta', 'Ofelija', 'Oksana', 'Oktavija', 'Oktiabrina', 'Olga', 'Olimpiada',
        'Olimpija', 'Oliva', 'Olivija', 'Ona', 'Onė', 'Onorata', 'Onutė', 'Oresta', 'Orinta', 'Otilija',
        'Ovidija', 'Palma', 'Palmira', 'Palmyra', 'Pamela', 'Pasaka', 'Patricija', 'Paula', 'Paulė',
        'Paulina', 'Pelagėja', 'Pelagija', 'Petra', 'Petrė', 'Petronė', 'Petronėlė', 'Petrutė', 'Pija',
        'Polė', 'Polina', 'Povilė', 'Pranciška', 'Pranė', 'Praskovja', 'Prima', 'Pulcherija', 'Rachilė',
        'Rada', 'Radmila', 'Radvyda', 'Radvilė', 'Rafaela', 'Rafaelė', 'Raimonda', 'Raimunda', 'Raistė',
        'Rakelė', 'Ramybė', 'Raminta', 'Ramona', 'Ramunė', 'Ramūnė', 'Ramutė', 'Rasa', 'Raselė', 'Rasė',
        'Rasytė', 'Rasuolė', 'Rasvita', 'Rebeka', 'Reda', 'Rėda', 'Rega', 'Regimanta', 'Regimantė',
        'Regina', 'Rema', 'Remigija', 'Rena', 'Renalda', 'Renata', 'Renatė', 'Renė', 'Ričarda', 'Rikarda',
        'Rima', 'Rimanta', 'Rimantė', 'Rimgailė', 'Rimgaudė', 'Rimtautė', 'Rimtė', 'Rimutė', 'Rimvyda',
        'Rimvydė', 'Rimvilė', 'Rina', 'Ringa', 'Ringailė', 'Rita', 'Ryta', 'Ritė', 'Rytė', 'Roberta',
        'Robertina', 'Rolanda', 'Roma', 'Romana', 'Romė', 'Romina', 'Romualda', 'Rosita', 'Roza',
        'Rozalija', 'Rožė', 'Rufina', 'Rugilė', 'Ruslana', 'Rusnė', 'Rūstė', 'Ruta', 'Rūta', 'Rūtelė',
        'Rūtenė', 'Rūtilė', 'Sabina', 'Sabrina', 'Salė', 'Salomėja', 'Salvė', 'Salvija', 'Salvinija',
        'Samanta', 'Sandra', 'Santara', 'Sauga', 'Saulė', 'Saulena', 'Saulenė', 'Saulytė', 'Saulutė',
        'Selena', 'Selma', 'Serafina', 'Serafima', 'Serena', 'Severija', 'Severina', 'Sibilė', 'Siga',
        'Sigita', 'Sigrida', 'Sigutė', 'Silva', 'Silverija', 'Silvestra', 'Silvija', 'Sima', 'Simona',
        'Sintė', 'Sintija', 'Siuzana', 'Skaidra', 'Skaidrė', 'Skaiste', 'Skaiva', 'Skalvė', 'Skirgailė',
        'Skirma', 'Skirmanta', 'Skirmantė', 'Skolastika', 'Smilga', 'Smiltė', 'Snaigė', 'Snežana', 'Sniegė',
        'Sniegena', 'Snieguolė', 'Sofa', 'Sofija', 'Solveiga', 'Sonata', 'Soneta', 'Sonia', 'Sotera',
        'Stanislava', 'Stanislova', 'Staselė', 'Stasė', 'Stefa', 'Stefanija', 'Stela', 'Stepė', 'Sulamita',
        'Svaja', 'Svajonė', 'Sveta', 'Svetlana', 'Šalna', 'Šalnė', 'Šarlota', 'Šarūnė', 'Šatrija', 'Šilė',
        'Šviesa', 'Šviesė', 'Švitrigailė', 'Taika', 'Taisa', 'Taisija', 'Tamara', 'Tania', 'Tatjana',
        'Taura', 'Tautė', 'Tautvydė', 'Teklė', 'Teodora', 'Teofilė', 'Tera', 'Teresė', 'Tereza', 'Terezija',
        'Tesa', 'Tilija', 'Tina', 'Toma', 'Ugnė', 'Ula', 'Ulė', 'Ulijona', 'Uljana', 'Ulrika', 'Una',
        'Undinė', 'Unė', 'Uoginta', 'Ursula', 'Uršula', 'Uršulė', 'Urtė', 'Ūla', 'Upė', 'Vacė', 'Vaclava',
        'Vaclova', 'Vaida', 'Vaidota', 'Vaidotė', 'Vaidilutė', 'Vaiga', 'Vaigalė', 'Vainora', 'Vaiva',
        'Vakarė', 'Valda', 'Valdemara', 'Valdė', 'Valdonė', 'Valentina', 'Valeriana', 'Valerija',
        'Valerijona', 'Valia', 'Valė', 'Vanda', 'Varvara', 'Vasa', 'Vasara', 'Vasarė', 'Vasilisa', 'Vėja',
        'Vėjūnė', 'Venanta', 'Vena', 'Venera', 'Venta', 'Vera', 'Verena', 'Vergilija', 'Verutė', 'Veronika',
        'Vesta', 'Vėtra', 'Vida', 'Vyda', 'Vidmanta', 'Vidmantė', 'Vydmantė', 'Vidimanta', 'Vigilija',
        'Vygantė', 'Vygintė', 'Vija', 'Vijolė', 'Vika', 'Vykintė', 'Vikta', 'Viktė', 'Viktorija',
        'Viktorina', 'Vilena', 'Vilė', 'Vylė', 'Vilhelma', 'Vilhelmina', 'Vilija', 'Vilma', 'Vilmanta',
        'Vilmantė', 'Vilnė', 'Viltara', 'Viltautė', 'Viltė', 'Viltenė', 'Vilūnė', 'Vincė', 'Vincenta',
        'Vincentė', 'Viola', 'Violeta', 'Violina', 'Virdžinija', 'Virga', 'Virginija', 'Virmantė',
        'Vismantė', 'Vita', 'Vyta', 'Vitalė', 'Vitalija', 'Vytautė', 'Vitė', 'Vytė', 'Vytenė', 'Vlada',
        'Vladė', 'Vladislava', 'Vladislova', 'Vladlena', 'Vilita', 'Zabelė', 'Zanė', 'Zelma', 'Zenė',
        'Zigfrida', 'Zigmantė', 'Zylė', 'Zina', 'Zinaida', 'Zita', 'Zofija', 'Zoja', 'Zosė', 'Zuzana',
        'Žana', 'Žaneta', 'Žara', 'Žeimantė', 'Žemyna', 'Žibuoklė', 'Žibutė', 'Žiedė', 'Živilė', 'Žydrė',
        'Žydronė', 'Žydrūnė', 'Žygimantė', 'Žyginta', 'Žilvinė', 'Žymantė', 'Žoržeta',
    ];

    /**
     * @see http://www.horoskopai.lt/gaires/populiariausios-pavardes-lietuvoje/
     */
    protected static $lastNameMale = [
        'Kazlauskas', 'Jankauskas', 'Petrauskas', 'Stankevičius', 'Vasiliauskas', 'Žukauskas', 'Butkus',
        'Kateiva', 'Paulauskas', 'Urbonas', 'Kavaliauskas', 'Baranauskas', 'Pocius', 'Sakalauskas',
    ];

    /**
     * @see http://www.horoskopai.lt/gaires/populiariausios-pavardes-lietuvoje/
     */
    protected static $lastNameFemale = [
        'Kazlauskienė', 'Jankauskienė', 'Petrauskienė', 'Stankevičienė', 'Vasiliauskienė', 'Paulauskienė',
        'Žukauskienė', 'Urbonienė', 'Kavaliauskienė', 'Navickienė', 'Kazlauskaitė', 'Jankauskaitė', 'Stankevičiūtė',
        'Petrauskaitė', 'Vasiliauskaitė', 'Butkutė', 'Pociūtė', 'Lukoševičiūtė', 'Balčiūnaitė', 'Kavaliauskaitė',
    ];

    /**
     * @param string|null $gender 'male', 'female' or null for any
     *
     * @example 'Doe'
     *
     * @return string
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

    /**
     * Return male last name
     *
     * @return string
     *
     * @example 'Vasiliauskas'
     */
    public function lastNameMale()
    {
        return static::randomElement(static::$lastNameMale);
    }

    /**
     * Return female last name
     *
     * @return string
     *
     * @example 'Žukauskaitė'
     */
    public function lastNameFemale()
    {
        return static::randomElement(static::$lastNameFemale);
    }

    /**
     * Return driver license number
     *
     * @return string
     *
     * @example 12345678
     */
    public function driverLicence()
    {
        return $this->bothify('########');
    }

    /**
     * Return passport number
     *
     * @return string
     *
     * @example 12345678
     */
    public function passportNumber()
    {
        return $this->bothify('########');
    }

    /**
     * National Personal Identity number (asmens kodas)
     *
     * @see https://en.wikipedia.org/wiki/National_identification_number#Lithuania
     * @see https://lt.wikipedia.org/wiki/Asmens_kodas
     *
     * @param string $gender       [male|female]
     * @param string $randomNumber three integers
     *
     * @return string on format XXXXXXXXXXX
     */
    public function personalIdentityNumber($gender = 'male', ?\DateTime $birthdate = null, $randomNumber = '')
    {
        if (!$birthdate) {
            $birthdate = \Faker\Provider\DateTime::dateTimeThisCentury();
        }

        $genderNumber = ($gender == 'male') ? 1 : 0;
        $firstNumber = (int) floor($birthdate->format('Y') / 100) * 2 - 34 - $genderNumber;

        $datePart = $birthdate->format('ymd');
        $randomDigits = (string) (!$randomNumber || strlen($randomNumber) < 3) ? static::numerify('###') : substr($randomNumber, 0, 3);
        $partOfPerosnalCode = $firstNumber . $datePart . $randomDigits;

        $sum = self::calculateSum($partOfPerosnalCode, 1);
        $liekana = $sum % 11;

        if ($liekana !== 10) {
            $lastNumber = $liekana;

            return $firstNumber . $datePart . $randomDigits . $lastNumber;
        }

        $sum = self::calculateSum($partOfPerosnalCode, 2);
        $liekana = $sum % 11;

        $lastNumber = ($liekana !== 10) ? $liekana : 0;

        return $firstNumber . $datePart . $randomDigits . $lastNumber;
    }

    /**
     * Calculate the sum of personal code
     *
     * @see https://en.wikipedia.org/wiki/National_identification_number#Lithuania
     * @see https://lt.wikipedia.org/wiki/Asmens_kodas
     *
     * @param string $numbers
     * @param int    $time    [1|2]
     *
     * @return int
     */
    private static function calculateSum($numbers, $time = 1)
    {
        if ($time == 1) {
            $multipliers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 1];
        } else {
            $multipliers = [3, 4, 5, 6, 7, 8, 9, 1, 2, 3];
        }

        $sum = 0;

        for ($i = 1; $i <= 10; ++$i) {
            $sum += ((int) $numbers[$i - 1]) * $multipliers[$i - 1];
        }

        return (int) $sum;
    }
}
