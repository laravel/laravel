<?php

namespace Faker\Provider\sl_SI;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{title}} {{firstNameMale}} {{lastName}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{title}} {{firstNameFemale}} {{lastName}}',
    ];

    /**
     * @see http://www.stat.si/imena_top_imena_spol.asp?r=True
     * @see http://www.stat.si/doc/vsebina/05/imena/TOPIMENA_SI.xlsx
     */
    protected static $firstNameMale = [
        'Adam', 'Adolf', 'Albert', 'Albin', 'Aleks', 'Aleksandar', 'Aleksander', 'Aleksej', 'Alen',
        'Alex', 'Aleš', 'Aljaž', 'Aljoša', 'Alojz', 'Alojzij', 'Andraž', 'Andrej', 'Anej', 'Anton',
        'Anže', 'Avgust', 'Ažbe', 'Benjamin', 'Bernard', 'Bine', 'Blaž', 'Bogdan', 'Bogomir',
        'Bojan', 'Bor', 'Boris', 'Borut', 'Boštjan', 'Božidar', 'Branko', 'Brin', 'Bruno', 'Ciril',
        'Cvetko', 'Damijan', 'Damir', 'Damjan', 'Daniel', 'Danijel', 'Danilo', 'Darko', 'David',
        'Davor', 'Davorin', 'Dejan', 'Denis', 'Domen', 'Dominik', 'Dragan', 'Drago', 'Dušan',
        'Edin', 'Edvard', 'Elvis', 'Emil', 'Enej', 'Erazem', 'Erik', 'Ernest', 'Ervin',
        'Ferdinand', 'Filip', 'Franc', 'Franci', 'Franjo', 'Frančišek', 'Gaber', 'Gabriel', 'Gal',
        'Gašper', 'Goran', 'Gorazd', 'Grega', 'Gregor', 'Hasan', 'Ian', 'Ignac', 'Igor', 'Ivan',
        'Ivo', 'Izak', 'Izidor', 'Iztok', 'Jaka', 'Jakob', 'Jan', 'Janez', 'Jani', 'Janko',
        'Jasmin', 'Jaša', 'Jernej', 'Jon', 'Josip', 'Joško', 'Jošt', 'Jože', 'Jožef', 'Jure',
        'Jurij', 'Karel', 'Karl', 'Kevin', 'Klemen', 'Kristijan', 'Kristjan', 'Ladislav', 'Lan',
        'Lenart', 'Leon', 'Leopold', 'Liam', 'Lovro', 'Ludvik', 'Luka', 'Lukas', 'Mai', 'Maj',
        'Maks', 'Maksimiljan', 'Marcel', 'Marijan', 'Mario', 'Marjan', 'Mark', 'Marko', 'Martin',
        'Matej', 'Matevž', 'Matic', 'Matija', 'Matjaž', 'Max', 'Metod', 'Miha', 'Mihael', 'Milan',
        'Miloš', 'Miran', 'Mirko', 'Miro', 'Miroslav', 'Mirsad', 'Mitja', 'Mladen', 'Nace', 'Nal',
        'Nejc', 'Nenad', 'Nik', 'Niko', 'Nikola', 'Nikolaj', 'Nino', 'Oskar', 'Ožbej', 'Patrik',
        'Pavel', 'Petar', 'Peter', 'Primož', 'Rado', 'Radovan', 'Rafael', 'Rajko', 'Renato',
        'Rene', 'Robert', 'Rok', 'Roman', 'Rudi', 'Rudolf', 'Samir', 'Samo', 'Sandi', 'Saša',
        'Sašo', 'Sebastijan', 'Sebastjan', 'Senad', 'Sergej', 'Silvester', 'Silvo', 'Simon',
        'Slavko', 'Slobodan', 'Srečko', 'Stanislav', 'Stanko', 'Staš', 'Stjepan', 'Stojan', 'Svit',
        'Tadej', 'Tai', 'Taj', 'Tarik', 'Teo', 'Tevž', 'Tian', 'Tilen', 'Tim', 'Timotej', 'Tine',
        'Tjaš', 'Tomaž', 'Tomislav', 'Tristan', 'Urban', 'Uroš', 'Val', 'Valentin', 'Valter',
        'Vid', 'Viktor', 'Viljem', 'Vincenc', 'Vinko', 'Vito', 'Vladimir', 'Vlado', 'Vojko',
        'Zdenko', 'Zdravko', 'Zlatko', 'Zoran', 'Zvonko', 'Štefan', 'Žak', 'Žan', 'Željko', 'Žiga',
    ];

    /**
     * @see http://www.stat.si/imena_top_imena_spol.asp?r=True
     * @see http://www.stat.si/doc/vsebina/05/imena/TOPIMENA_SI.xlsx
     */
    protected static $firstNameFemale = [
        'Ajda', 'Ajla', 'Albina', 'Aleksandra', 'Alenka', 'Alina', 'Alja', 'Alojzija', 'Amalija',
        'Ana Marija', 'Ana', 'Andreja', 'Andrejka', 'Aneja', 'Angela', 'Anica', 'Anika', 'Anita',
        'Anja', 'Anka', 'Antonija', 'Barbara', 'Bernarda', 'Blanka', 'Bojana', 'Branka', 'Breda',
        'Brigita', 'Brina', 'Cecilija', 'Cvetka', 'Damjana', 'Danica', 'Daniela', 'Danijela',
        'Darinka', 'Darja', 'Daša', 'Doroteja', 'Dragica', 'Dušanka', 'Ela', 'Elena', 'Elizabeta',
        'Ella', 'Ema', 'Emilija', 'Erika', 'Erna', 'Eva', 'Frančiška', 'Gabrijela', 'Gaja',
        'Gloria', 'Gordana', 'Hana', 'Hedvika', 'Helena', 'Hermina', 'Ida', 'Ines', 'Inja',
        'Irena', 'Iris', 'Irma', 'Iva', 'Ivana', 'Ivanka', 'Ivica', 'Iza', 'Izabela', 'Jana',
        'Janja', 'Jasmina', 'Jasna', 'Jelena', 'Jelka', 'Jerca', 'Jerneja', 'Jolanda', 'Jožefa',
        'Jožica', 'Julia', 'Julija', 'Julijana', 'Justina', 'Kaja', 'Karin', 'Karmen', 'Karolina',
        'Katarina', 'Katja', 'Kiara', 'Kim', 'Klara', 'Klavdija', 'Kristina', 'Ksenija', 'Lana',
        'Lara', 'Larisa', 'Laura', 'Lea', 'Leja', 'Lejla', 'Lia', 'Lidija', 'Lili', 'Lilijana',
        'Liljana', 'Lina', 'Liza', 'Ljubica', 'Ljudmila', 'Loti', 'Lucija', 'Luna', 'Magda',
        'Magdalena', 'Maja', 'Majda', 'Manca', 'Marica', 'Marija', 'Marijana', 'Marina', 'Marinka',
        'Marjana', 'Marjanca', 'Marjeta', 'Marjetka', 'Marta', 'Martina', 'Maruša', 'Mateja',
        'Matilda', 'Maša', 'Melita', 'Meta', 'Metka', 'Mia', 'Mihaela', 'Mija', 'Mila', 'Milena',
        'Milica', 'Milka', 'Mira', 'Mirjam', 'Mirjana', 'Miroslava', 'Mojca', 'Monika', 'Nada',
        'Nadja', 'Naja', 'Nastja', 'Natalija', 'Nataša', 'Neja', 'Neli', 'Nevenka', 'Neža', 'Nika',
        'Nikolina', 'Nina', 'Nives', 'Nuša', 'Olga', 'Patricija', 'Pavla', 'Petra', 'Pia', 'Pika',
        'Polona', 'Polonca', 'Rebeka', 'Renata', 'Romana', 'Rozalija', 'Sabina', 'Sandra', 'Sanja',
        'Sara', 'Saša', 'Silva', 'Simona', 'Slavica', 'Slavka', 'Sofia', 'Sofija', 'Sonja',
        'Stanislava', 'Stanka', 'Stela', 'Suzana', 'Tadeja', 'Taja', 'Tajda', 'Tamara', 'Tanja',
        'Tara', 'Tatjana', 'Tea', 'Teja', 'Terezija', 'Tia', 'Tiana', 'Tija', 'Tina', 'Tinkara',
        'Tisa', 'Tjaša', 'Ula', 'Urša', 'Urška', 'Valentina', 'Valerija', 'Vanja', 'Vera',
        'Veronika', 'Vesna', 'Vida', 'Viktorija', 'Vita', 'Vlasta', 'Zala', 'Zara', 'Zarja',
        'Zdenka', 'Zlatka', 'Zofija', 'Zoja', 'Zora', 'Zvonka', 'Špela', 'Štefanija', 'Štefka',
        'Žana', 'Živa',
    ];

    /**
     * @see http://www.stat.si/imena_top_priimki.asp?r=True
     */
    protected static $lastName = [
        'Ambrožič', 'Babič', 'Bajc', 'Bergant', 'Bevc', 'Bezjak', 'Bizjak', 'Blatnik', 'Blažič',
        'Bogataj', 'Božič', 'Bregar', 'Breznik', 'Bukovec', 'Cerar', 'Cvetko', 'Debeljak',
        'Demšar', 'Dolenc', 'Dolinar', 'Dolinšek', 'Erjavec', 'Eržen', 'Filipič', 'Fras', 'Furlan',
        'Gajšek', 'Godec', 'Golob', 'Gomboc', 'Gorenc', 'Gorjup', 'Gregorič', 'Hafner', 'Hodžić',
        'Horvat', 'Hozjan', 'Hočevar', 'Hren', 'Hribar', 'Hribernik', 'Hrovat', 'Humar', 'Ilić',
        'Ivančič', 'Jamnik', 'Janežič', 'Jarc', 'Javornik', 'Jazbec', 'Jelen', 'Jenko', 'Jereb',
        'Jerič', 'Jerman', 'Jovanović', 'Jug', 'Kalan', 'Kastelic', 'Kaučič', 'Kavčič',
        'Klemenčič', 'Knez', 'Kobal', 'Kocjančič', 'Kodrič', 'Kokalj', 'Kokol', 'Kolar', 'Kolarič',
        'Kolenc', 'Koren', 'Korošec', 'Kos', 'Kosi', 'Kotnik', 'Kovač', 'Kovačević', 'Kovačič',
        'Kočevar', 'Košir', 'Koželj', 'Krajnc', 'Kralj', 'Kramar', 'Kramberger', 'Kranjc',
        'Kranjec', 'Kristan', 'Krivec', 'Kuhar', 'Kumer', 'Lah', 'Lavrič', 'Lazar', 'Leban',
        'Lebar', 'Lesjak', 'Leskovar', 'Lešnik', 'Likar', 'Logar', 'Majcen', 'Marković',
        'Markovič', 'Marolt', 'Mavrič', 'Maček', 'Medved', 'Meglič', 'Mihelič', 'Miklavčič',
        'Mlakar', 'Mlinar', 'Mlinarič', 'Mohorič', 'Močnik', 'Mrak', 'Nemec', 'Nikolić', 'Novak',
        'Oblak', 'Pavlin', 'Pavlič', 'Perko', 'Petek', 'Petrič', 'Petrović', 'Petrovič', 'Pečnik',
        'Pintar', 'Pintarič', 'Pirc', 'Pirnat', 'Podgoršek', 'Pogačar', 'Pogačnik', 'Popović',
        'Potočnik', 'Povše', 'Primožič', 'Pušnik', 'Rajh', 'Ramšak', 'Resnik', 'Ribič', 'Rozman',
        'Rožič', 'Rožman', 'Rupnik', 'Rus', 'Rutar', 'Savić', 'Sever', 'Simonič', 'Sitar', 'Skok',
        'Smrekar', 'Stopar', 'Sušnik', 'Tavčar', 'Tomažič', 'Tomšič', 'Toplak', 'Tratnik', 'Trček',
        'Turk', 'Uršič', 'Vidic', 'Vidmar', 'Vidovič', 'Vodopivec', 'Volk', 'Vovk', 'Zadravec',
        'Zajc', 'Zakrajšek', 'Zalar', 'Zalokar', 'Založnik', 'Zemljič', 'Zorko', 'Zorman', 'Zupan',
        'Zupanc', 'Zupančič', 'Zver', 'Čeh', 'Černe', 'Čuk', 'Šinkovec', 'Škof', 'Šmid',
        'Štrukelj', 'Šuštar', 'Žagar', 'Železnik', 'Žibert', 'Žižek', 'Žnidaršič',
    ];

    protected static $title = [
        'dr.', 'mag.', 'inž.', 'univ. dipl.', 'dipl.', 'univ. dipl. inž.', 'dipl. inž.', 'prof.', 'akad.', 'dr. med.', 'spec.',
    ];

    /**
     * replaced by specific unisex slovenian title
     */
    public function title($gender = null)
    {
        return static::randomElement(static::$title);
    }

    /**
     * @param string|null $gender 'male', 'female' or null for any
     *
     * @example 'Novak'
     */
    public function lastName($gender = null)
    {
        return static::randomElement(static::$lastName);
    }

    public static function lastNameMale()
    {
        return static::lastName();
    }

    public static function lastNameFemale()
    {
        return static::lastName();
    }
}
