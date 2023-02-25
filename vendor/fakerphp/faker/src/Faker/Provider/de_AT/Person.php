<?php

namespace Faker\Provider\de_AT;

use Faker\Provider\DateTime;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}} {{suffix}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}} {{suffix}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}} {{suffix}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}} {{suffix}}',
    ];

    /**
     * 60 most popular names in 1985, 1995, 2005 and 2015
     * {@link} https://www.statistik.at/wcm/idc/idcplg?IdcService=GET_NATIVE_FILE&RevisionSelectionMethod=LatestReleased&dDocName=115199
     */
    protected static $firstNameMale = [
        'Adrian', 'Alexander', 'Andreas', 'Anton',
        'Ben', 'Benedikt', 'Benjamin', 'Bernd', 'Bernhard',
        'Christian', 'Christoph', 'Christopher', 'Clemens',
        'Daniel', 'David', 'Dominik',
        'Elias', 'Emil', 'Erik',
        'Fabian', 'Fabio', 'Felix', 'Finn', 'Florian', 'Franz',
        'Gabriel', 'Georg', 'Gerald', 'Gerhard', 'Gernot', 'Gregor', 'Günther',
        'Hannes', 'Harald', 'Helmut', 'Herbert',
        'Jakob', 'Jan', 'Johann', 'Johannes', 'Jonas', 'Jonathan', 'Josef', 'Joseph', 'Julian', 'Justin', 'Jürgen',
        'Karl', 'Kevin', 'Kilian', 'Klaus', 'Konstantin',
        'Leo', 'Leon', 'Lorenz', 'Luca', 'Luis', 'Lukas',
        'Manfred', 'Manuel', 'Marc', 'Marcel', 'Marco', 'Mario', 'Markus', 'Martin', 'Marvin', 'Matteo', 'Matthias', 'Max', 'Maximilian', 'Michael', 'Moritz',
        'Nico', 'Nicolas', 'Niklas', 'Noah',
        'Oliver', 'Oskar',
        'Pascal', 'Patrick', 'Patrik', 'Paul', 'Peter', 'Philipp',
        'Ralph', 'Raphael', 'Reinhard', 'René', 'Richard', 'Robert', 'Roland', 'Roman',
        'Samuel', 'Sandro', 'Sascha', 'Sebastian', 'Simon', 'Stefan',
        'Theo', 'Theodor', 'Thomas', 'Tim', 'Tobias',
        'Valentin', 'Vincent',
        'Werner', 'Wolfgang',
    ];

    /**
     * 60 most popular names in 1985, 1995, 2005 and 2015
     * {@link} https://www.statistik.at/wcm/idc/idcplg?IdcService=GET_NATIVE_FILE&RevisionSelectionMethod=LatestReleased&dDocName=115199
     */
    protected static $firstNameFemale = [
        'Alexandra', 'Alexandrea', 'Algelika', 'Alina', 'Amelie', 'Andrea', 'Angelina', 'Anita', 'Anja', 'Anna', 'Anna-Lena', 'Annika', 'Astrid',
        'Barbara', 'Bettina', 'Bianca', 'Birgit',
        'Carina', 'Caroline', 'Celina', 'Chiara', 'Christina', 'Christine', 'Clara', 'Claudia', 'Cornelia',
        'Daniela', 'Denise', 'Doris',
        'Elena', 'Elisa', 'Elisabeth', 'Ella', 'Emely', 'Emilia', 'Emily', 'Emma', 'Eva', 'Eva-Maria',
        'Franziska',
        'Hanna', 'Hannah', 'Helena',
        'Ines', 'Iris', 'Isabel', 'Isabella',
        'Jacqueline', 'Jacquline', 'Jana', 'Janine', 'Jasmin', 'Jennifer', 'Jessica', 'Johanna', 'Julia',
        'Karin', 'Katharina', 'Katrin', 'Kerstin',
        'Lara', 'Larissa', 'Laura', 'Lea', 'Lena', 'Leonie', 'Lilly', 'Lina', 'Lisa', 'Livia', 'Luisa',
        'Magdalena', 'Maja', 'Manuela', 'Maria', 'Marie', 'Marion', 'Marlene', 'Martina', 'Melanie', 'Melina', 'Mia', 'Michaela', 'Michelle', 'Miriam', 'Mona', 'Monika',
        'Nadine', 'Natalie', 'Nicole', 'Nina', 'Nora',
        'Patricia', 'Paula', 'Petra', 'Pia',
        'Rebecca', 'Rosa',
        'Sabine', 'Sabrina', 'Sandra', 'Sarah', 'Selina', 'Silvia', 'Simone', 'Sonja', 'Sophia', 'Sophie', 'Stefanie', 'Susanne',
        'Tamara', 'Tanja', 'Theresa',
        'Valentina', 'Valerie', 'Vanessa', 'Verena', 'Viktoria',
        'Yvonne',
    ];

    /**
     * Top 500 Names from a phone directory (February 2004)
     * {@link} https://de.wiktionary.org/w/index.php?title=Verzeichnis:Deutsch/Namen/die_h%C3%A4ufigsten_Nachnamen_%C3%96sterreichs
     */
    protected static $lastName = [
        'Abraham', 'Achleitner', 'Adam', 'Aichinger', 'Aigner', 'Albrecht', 'Altmann', 'Amann', 'Amon', 'Angerer', 'Arnold', 'Artner', 'Aschauer', 'Auer', 'Augustin', 'Auinger',
        'Bacher', 'Bachler', 'Bachmann', 'Bader', 'Baier', 'Barth', 'Bartl', 'Bauer', 'Baumann', 'Baumgartner', 'Bayer', 'Beck', 'Beer', 'Berger', 'Bergmann', 'Bernhard', 'Bichler', 'Binder', 'Bischof', 'Bock', 'Bogner', 'Brandl', 'Brandner', 'Brandstetter', 'Brandstätter', 'Braun', 'Brenner', 'Bruckner', 'Brugger', 'Brunner', 'Buchberger', 'Buchegger', 'Bucher', 'Buchinger', 'Buchner', 'Burger', 'Burgstaller', 'Burtscher', 'Böck', 'Böhm', 'Bösch',
        'Danner', 'Denk', 'Deutsch', 'Dietrich', 'Dobler', 'Doppler', 'Dorner', 'Draxler',
        'Eberharter', 'Eberl', 'Ebner', 'Ecker', 'Eder', 'Edlinger', 'Egger', 'Eibl', 'Eichberger', 'Eichinger', 'Eigner', 'Erhart', 'Ernst', 'Ertl',
        'Falkner', 'Fasching', 'Feichtinger', 'Fellner', 'Fiala', 'Fichtinger', 'Fiedler', 'Fink', 'Fischer', 'Fleischhacker', 'Forster', 'Forstner', 'Frank', 'Franz', 'Friedl', 'Friedrich', 'Fritsch', 'Fritz', 'Fröhlich', 'Frühwirth', 'Fuchs', 'Führer', 'Fürst',
        'Gabriel', 'Gangl', 'Gartner', 'Gasser', 'Gassner', 'Geiger', 'Geisler', 'Geyer', 'Glaser', 'Glatz', 'Gmeiner', 'Grabner', 'Graf', 'Gratzer', 'Greiner', 'Grill', 'Gritsch', 'Gross', 'Groß', 'Gruber', 'Grünwald', 'Gschwandtner', 'Gutmann',
        'Haas', 'Haberl', 'Hackl', 'Hafner', 'Hagen', 'Hager', 'Hahn', 'Haider', 'Haidinger', 'Haller', 'Hammer', 'Hammerl', 'Handl', 'Handler', 'Harrer', 'Hartl', 'Hartmann', 'Haslinger', 'Hauer', 'Hauser', 'Heindl', 'Heinrich', 'Hemetsberger', 'Herbst', 'Hermann', 'Herzog', 'Hinterberger', 'Hinteregger', 'Hinterleitner', 'Hirsch', 'Hochreiter', 'Hofbauer', 'Hofer', 'Hoffmann', 'Hofmann', 'Hofstätter', 'Holzer', 'Holzinger', 'Holzmann', 'Horvath', 'Huber', 'Huemer', 'Hufnagl', 'Humer', 'Hummer', 'Hutter', 'Hämmerle', 'Hödl', 'Höfler', 'Höller', 'Hölzl', 'Hörmann', 'Hütter',
        'Jahn', 'Jandl', 'Janisch', 'Jovanovic', 'Jung', 'Jungwirth', 'Jäger',
        'Kainz', 'Kaiser', 'Kaltenbrunner', 'Kapeller', 'Kargl', 'Karl', 'Karner', 'Kastner', 'Kaufmann', 'Kellner', 'Kern', 'Kerschbaumer', 'Kirchmair', 'Kirchner', 'Klammer', 'Klein', 'Klinger', 'Klug', 'Knapp', 'Knoll', 'Koch', 'Kofler', 'Kogler', 'Kohl', 'Koller', 'Kollmann', 'Konrad', 'Kopp', 'Kovacs', 'Kraft', 'Krainer', 'Kramer', 'Krammer', 'Kraus', 'Kremser', 'Krenn', 'Kreuzer', 'Kronberger', 'Kröll', 'Kugler', 'Kummer', 'Kurz', 'Köberl', 'Köck', 'Köhler', 'König',
        'Lackner', 'Lamprecht', 'Lang', 'Langer', 'Lechner', 'Lederer', 'Leeb', 'Lehner', 'Leitgeb', 'Leitner', 'Lengauer', 'Lenz', 'Lettner', 'Lindner', 'List', 'Loidl', 'Lorenz', 'Ludwig', 'Luger', 'Lukas', 'Lutz', 'Löffler',
        'Mader', 'Maier', 'Maierhofer', 'Mair', 'Mairhofer', 'Mandl', 'Markovic', 'Martin', 'Maurer', 'Mayer', 'Mayerhofer', 'Mayr', 'Mayrhofer', 'Meier', 'Meixner', 'Messner', 'Meyer', 'Mitterer', 'Moosbrugger', 'Moser', 'Muhr', 'Mühlbacher', 'Müller', 'Müllner',
        'Nagl', 'Nemeth', 'Neubauer', 'Neuhauser', 'Neuhold', 'Neumann', 'Neumayer', 'Neuner', 'Neuwirth', 'Nikolic', 'Novak', 'Nowak', 'Nussbaumer', 'Nußbaumer',
        'Ofner', 'Ortner', 'Oswald', 'Ott',
        'Paar', 'Pacher', 'Pammer', 'Paul', 'Payer', 'Peer', 'Penz', 'Peter', 'Petrovic', 'Petz', 'Pfeffer', 'Pfeifer', 'Pfeiffer', 'Pfister', 'Pfleger', 'Pichler', 'Pilz', 'Pinter', 'Pirker', 'Plank', 'Plattner', 'Platzer', 'Pointner', 'Pokorny', 'Pollak', 'Posch', 'Prem', 'Prinz', 'Probst', 'Pucher', 'Putz', 'Pöll', 'Pölzl', 'Pöschl', 'Pühringer',
        'Raab', 'Rabl', 'Rainer', 'Rath', 'Rauch', 'Rausch', 'Rauscher', 'Rauter', 'Rechberger', 'Redl', 'Reich', 'Reichl', 'Reindl', 'Reiner', 'Reinisch', 'Reischl', 'Reisinger', 'Reiter', 'Reiterer', 'Renner', 'Resch', 'Richter', 'Rieder', 'Riedl', 'Riedler', 'Rieger', 'Riegler', 'Rieser', 'Ritter', 'Rosenberger', 'Roth', 'Rupp',
        'Sailer', 'Sattler', 'Sauer', 'Schachinger', 'Schachner', 'Schaffer', 'Schaller', 'Scharf', 'Schatz', 'Schauer', 'Scheiber', 'Schenk', 'Scheucher', 'Schiefer', 'Schiller', 'Schindler', 'Schlager', 'Schlögl', 'Schmid', 'Schmidt', 'Schmied', 'Schnabl', 'Schneeberger', 'Schneider', 'Schober', 'Scholz', 'Schranz', 'Schreiber', 'Schreiner', 'Schubert', 'Schuh', 'Schuller', 'Schulz', 'Schuster', 'Schwab', 'Schwaiger', 'Schwaighofer', 'Schwarz', 'Schweiger', 'Schweighofer', 'Schön', 'Schöpf', 'Schütz', 'Seebacher', 'Seidl', 'Siegl', 'Simon', 'Singer', 'Sommer', 'Sonnleitner', 'Spitzer', 'Springer', 'Stadler', 'Stangl', 'Stark', 'Staudinger', 'Steger', 'Steinbauer', 'Steinberger', 'Steindl', 'Steiner', 'Steininger', 'Steinkellner', 'Steinlechner', 'Steinwender', 'Stelzer', 'Stern', 'Steurer', 'Stocker', 'Stockinger', 'Strasser', 'Strauss', 'Strauß', 'Strobl', 'Stummer', 'Sturm', 'Stöckl', 'Stöger', 'Suppan', 'Swoboda', 'Szabo',
        'Thaler', 'Thaller', 'Thurner', 'Tischler', 'Toth', 'Traxler', 'Trimmel', 'Trummer',
        'Ulrich', 'Unger', 'Unterberger', 'Unterweger', 'Urban',
        'Varga', 'Vogel', 'Vogl',
        'Wachter', 'Wagner', 'Walch', 'Walcher', 'Wallner', 'Walter', 'Weber', 'Wechselberger', 'Wegscheider', 'Weidinger', 'Weigl', 'Weinberger', 'Weiss', 'Weiß', 'Weninger', 'Werner', 'Wieland', 'Wieser', 'Wiesinger', 'Wild', 'Wilhelm', 'Wimmer', 'Windisch', 'Winkler', 'Winter', 'Wirth', 'Wittmann', 'Wolf', 'Wurm', 'Wurzer',
        'Zach', 'Zangerl', 'Zauner', 'Zechner', 'Zehetner', 'Zeilinger', 'Zeller', 'Zenz', 'Ziegler', 'Zimmermann', 'Zöhrer',
    ];

    protected static $titleMale = ['Herr', 'Dr.', 'Mag.', 'Ing.', 'Dipl.-Ing.', 'Prof.', 'Univ.Prof.'];
    protected static $titleFemale = ['Frau', 'Dr.', 'Maga.', 'Ing.', 'Dipl.-Ing.', 'Prof.', 'Univ.Prof.'];

    protected static $suffix = ['B.Sc.', 'B.A.', 'B.Eng.', 'MBA.'];

    /**
     * @example 'PhD'
     */
    public static function suffix()
    {
        return static::randomElement(static::$suffix);
    }

    /**
     * Generates a random Austrian Social Security number.
     *
     * @see https://de.wikipedia.org/wiki/Sozialversicherungsnummer#.C3.96sterreich
     *
     * @return string
     */
    public static function ssn(\DateTime $birthdate = null)
    {
        $birthdate = $birthdate ?? DateTime::dateTimeThisCentury();

        $birthDateString = $birthdate->format('dmy');

        do {
            $consecutiveNumber = (string) self::numberBetween(100, 999);

            $verificationNumber = (
                (int) $consecutiveNumber[0] * 3
                    + (int) $consecutiveNumber[1] * 7
                    + (int) $consecutiveNumber[2] * 9
                    + (int) $birthDateString[0] * 5
                    + (int) $birthDateString[1] * 8
                    + (int) $birthDateString[2] * 4
                    + (int) $birthDateString[3] * 2
                    + (int) $birthDateString[4] * 1
                    + (int) $birthDateString[5] * 6
            ) % 11;
        } while ($verificationNumber == 10);

        return sprintf('%s%s%s', $consecutiveNumber, $verificationNumber, $birthDateString);
    }
}
