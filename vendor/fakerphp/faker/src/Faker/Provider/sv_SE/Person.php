<?php

namespace Faker\Provider\sv_SE;

use Faker\Calculator\Luhn;

class Person extends \Faker\Provider\Person
{
    protected static $formats = [
        '{{firstName}} {{lastName}}',
        '{{firstName}} {{lastName}}',
        '{{firstName}} {{lastName}}',
        '{{firstName}} {{lastName}}',
        '{{firstName}} {{lastName}}',
        '{{firstName}} {{firstName}} {{lastName}}',
        '{{firstName}} {{firstName}} {{lastName}}',
        '{{firstName}} {{firstName}} {{lastName}}',
        '{{firstName}} {{lastName}} {{lastName}}',
        '{{firstName}} {{lastName}}-{{lastName}}',
        '{{firstName}} {{firstName}} {{lastName}}-{{lastName}}',
    ];

    /**
     * @var array Swedish female first names
     *
     * @see http://spraakbanken.gu.se/statistik/lbfnamnalf.phtml
     */
    protected static $firstNameFemale = [

        'Ada', 'Adela', 'Adele', 'Adéle', 'Adelia', 'Adina', 'Adolfina', 'Agda', 'Agnes', 'Agneta', 'Aina', 'Aino', 'Albertina', 'Alexandra', 'Alfhild', 'Alfrida', 'Alice', 'Alida', 'Ally', 'Alma', 'Alva', 'Amalia', 'Amanda', 'Andrea', 'Anette', 'Angela', 'Anita', 'Anja', 'Ann', 'Anna', 'Anna-Carin', 'Anna-Greta', 'Anna-Karin', 'Anna-Lena', 'Anna-Lisa', 'Anna-Maria', 'Anna-Stina', 'Anne', 'Anneli', 'Annelie', 'Annette', 'Anne-Charlotte', 'Anne-Marie', 'Anni', 'Annica', 'Annie', 'Annika', 'Annikki', 'Anny', 'Ann-Britt', 'Ann-Charlott', 'Ann-Charlotte', 'Ann-Christin', 'Ann-Christine', 'Ann-Katrin', 'Ann-Kristin', 'Ann-Louise', 'Ann-Margret', 'Ann-Mari', 'Ann-Marie', 'Ann-Sofi', 'Ann-Sofie', 'Antonia', 'Arvida', 'Asta', 'Astrid', 'Augusta', 'Aurora', 'Axelia', 'Axelina',
        'Barbro', 'Beata', 'Beatrice', 'Beda', 'Berit', 'Bernhardina', 'Berta', 'Betty', 'Birgit', 'Birgitta', 'Blenda', 'Bodil', 'Boel', 'Borghild', 'Brita', 'Britt', 'Britta', 'Britt-Inger', 'Britt-Louise', 'Britt-Mari', 'Britt-Marie',
        'Camilla', 'Carin', 'Carina', 'Carita', 'Carola', 'Carolina', 'Caroline', 'Catarina', 'Catharina', 'Cathrine', 'Catrin', 'Cecilia', 'Charlott', 'Charlotta', 'Charlotte', 'Christel', 'Christin', 'Christina', 'Christine', 'Clara', 'Clary', 'Constance', 'Cristina',
        'Daga', 'Dagmar', 'Dagny', 'Daisy', 'Davida', 'Desideria', 'Desirée', 'Diana', 'Disa', 'Dora', 'Doris', 'Dorotea',
        'Ebba', 'Edit', 'Edith', 'Edla', 'Eira', 'Eivor', 'Ejvor', 'Elaine', 'Eleonor', 'Eleonora', 'Elfrida', 'Elida', 'Elin', 'Elina', 'Elinor', 'Elisabet', 'Elisabeth', 'Elise', 'Ella', 'Ellen', 'Ellinor', 'Elly', 'Elma', 'Elna', 'Elsa', 'Else', 'Else-Marie', 'Elsi', 'Elsie', 'Elsy', 'Elvi', 'Elvira', 'Elvy', 'Emelia', 'Emerentia', 'Emilia', 'Emma', 'Emmy', 'Erika', 'Erna', 'Ester', 'Estrid', 'Ethel', 'Eufemia', 'Eugenia', 'Eva', 'Eva-Britt', 'Eva-Lena', 'Eva-Lotta', 'Eva-Marie', 'Evelina', 'Evelyn', 'Evy', 'Ewa',
        'Fanny', 'Florence', 'Fredrika', 'Frida', 'Frideborg',
        'Gabriella', 'Gerd', 'Gerda', 'Gertie', 'Gertrud', 'Gisela', 'Greta', 'Gudrun', 'Gull', 'Gullan', 'Gullbritt', 'Gulli', 'Gullvi', 'Gully', 'Gull-Britt', 'Gun', 'Gunborg', 'Gunbritt', 'Gunda', 'Gunhild', 'Gunilla', 'Gunn', 'Gunnel', 'Gunni', 'Gunvor', 'Gun-Britt', 'Gurli', 'Gustava', 'Gärd', 'Görel', 'Göta',
        'Hanna', 'Harriet', 'Hedvig', 'Helen', 'Helén', 'Helena', 'Helene', 'Heléne', 'Helfrid', 'Helga', 'Helmi', 'Helny', 'Henny', 'Henrietta', 'Henriette', 'Herta', 'Hilda', 'Hildegard', 'Hildur', 'Hillevi', 'Hilma', 'Hjördis', 'Hulda',
        'Ida', 'Ines', 'Inez', 'Inga', 'Ingalill', 'Inga-Britt', 'Inga-Lena', 'Inga-Lill', 'Inga-Lisa', 'Inga-Maj', 'Ingbritt', 'Ingeborg', 'Ingegerd', 'Ingegärd', 'Ingela', 'Inger', 'Ingrid', 'Ingvor', 'Ing-Britt', 'Ing-Mari', 'Ing-Marie', 'Iréne', 'Irene', 'Iris', 'Irma', 'Isabella',
        'Jane', 'Janet', 'Jeanette', 'Jenny', 'Jessica', 'Johanna', 'Josefina', 'Judit', 'Judith', 'Julia', 'Juliana', 'Justina',
        'Kaarina', 'Kajsa', 'Karin', 'Karina', 'Karla', 'Karola', 'Karolina', 'Katarina', 'Katharina', 'Katrin', 'Katrina', 'Kersti', 'Kerstin', 'Klara', 'Konstantia', 'Kornelia', 'Kristin', 'Kristina', 'Kristine',
        'Laila', 'Laura', 'Leila', 'Lena', 'Leontina', 'Liisa', 'Lilian', 'Lill', 'Lillemor', 'Lillian', 'Lilly', 'Linda', 'Linnéa', 'Linnea', 'Lisa', 'Lisbet', 'Lisbeth', 'Liselott', 'Liselotte', 'Lise-Lott', 'Lise-Lotte', 'Lizzie', 'Lola', 'Louise', 'Lovisa', 'Lucia', 'Lydia',
        'Madeleine', 'Madelene', 'Magda', 'Magdalena', 'Magnhild', 'Maj', 'Maja', 'Majbritt', 'Majken', 'Majlis', 'Majvor', 'Maj-Britt', 'Maj-Lis', 'Malin', 'Malvina', 'Margaret', 'Margareta', 'Margareth', 'Margaretha', 'Margit', 'Margita', 'Margot', 'Margret', 'Margreta', 'Mari', 'Maria', 'Mariana', 'Mariann', 'Marianne', 'Marie', 'Mariette', 'Marie-Louise', 'Marika', 'Marina', 'Marion', 'Marit', 'Marita', 'Mari-Ann', 'Marja', 'Marjatta', 'Marlene', 'Marta', 'Martha', 'Martina', 'Mary', 'Mathilda', 'Matilda', 'Maud', 'May', 'Mia', 'Mildred', 'Mimmi', 'Mirjam', 'Mona', 'Monica', 'Monika', 'Märit', 'Märta', 'Märtha',
        'Naemi', 'Naima', 'Nancy', 'Nanna', 'Nanny', 'Natalia', 'Nelly', 'Nina', 'Nora',
        'Olga', 'Olivia', 'Ottilia',
        'Paula', 'Paulina', 'Pauline', 'Pernilla', 'Petra', 'Petronella', 'Pia',
        'Ragna', 'Ragnhild', 'Rakel', 'Rebecka', 'Regina', 'Renée', 'Rigmor', 'Rita', 'Rosa', 'Rose', 'Rose-Marie', 'Rosita', 'Ros-Mari', 'Ros-Marie', 'Runa', 'Rut', 'Ruth',
        'Sabina', 'Saga', 'Sally', 'Sara', 'Selma', 'Serafia', 'Sibylla', 'Sigbritt', 'Signe', 'Signhild', 'Sigrid', 'Siri', 'Siv', 'Sofi', 'Sofia', 'Sofie', 'Solbritt', 'Solveig', 'Solvig', 'Sonja', 'Stina', 'Susann', 'Susanna', 'Susanne', 'Suzanne', 'Svea', 'Sylvia', 'Synnöve', 'Syster',
        'Tea', 'Tekla', 'Terese', 'Teresia', 'Therése', 'Therese', 'Theresia', 'Thyra', 'Tina', 'Tora', 'Torborg', 'Tove', 'Tyra',
        'Ulla', 'Ulla-Britt', 'Ulla-Britta', 'Ulrica', 'Ulrika', 'Ursula',
        'Valborg', 'Vanja', 'Vega', 'Vendela', 'Vendla', 'Vera', 'Veronica', 'Veronika', 'Victoria', 'Viktoria', 'Vilhelmina', 'Vilma', 'Viola', 'Virginia', 'Vivan', 'Viveca', 'Viveka', 'Vivi', 'Vivian', 'Viviann', 'Vivianne', 'Vivi-Ann', 'Vivi-Anne',
        'Wilhelmina',
        'Ylva', 'Yvonne',
        'Åsa', 'Åse',
    ];

    /**
     * @var array Swedish male first names
     *
     * @see http://spraakbanken.gu.se/statistik/lbfnamnalf.phtml
     */
    protected static $firstNameMale = [
        'Abraham', 'Adam', 'Adolf', 'Adrian', 'Agaton', 'Agne', 'Albert', 'Albin', 'Aldor', 'Alex', 'Alexander', 'Alexis', 'Alexius', 'Alf', 'Alfons', 'Alfred', 'Algot', 'Allan', 'Alrik', 'Alvar', 'Alve', 'Amandus', 'Anders', 'André', 'Andreas', 'Anselm', 'Anshelm', 'Antero', 'Anton', 'Antonius', 'Arne', 'Arnold', 'Aron', 'Arthur', 'Artur', 'Arvid', 'Assar', 'Astor', 'August', 'Augustin', 'Axel',
        'Bengt', 'Bengt-Göran', 'Bengt-Olof', 'Bengt-Åke', 'Benny', 'Berndt', 'Berne', 'Bernhard', 'Bernt', 'Bert', 'Berth', 'Berthold', 'Bertil', 'Bill', 'Billy', 'Birger', 'Bjarne', 'Björn', 'Bo', 'Boris', 'Bror', 'Bruno', 'Brynolf', 'Börje',
        'Carl', 'Carl-Axel', 'Carl-Erik', 'Carl-Gustaf', 'Carl-Gustav', 'Carl-Johan', 'Charles', 'Christer', 'Christian', 'Claes', 'Claes-Göran', 'Clarence', 'Clas', 'Conny', 'Crister', 'Curt',
        'Dag', 'Dan', 'Daniel', 'David', 'Dennis', 'Dick', 'Donald', 'Douglas',
        'Ebbe', 'Eddie', 'Eddy', 'Edgar', 'Edmund', 'Edvard', 'Edvin', 'Efraim', 'Egon', 'Eilert', 'Einar', 'Eje', 'Ejnar', 'Elias', 'Elis', 'Ellert', 'Elmer', 'Elof', 'Elon', 'Elov', 'Elving', 'Elvir', 'Emanuel', 'Emil', 'Enar', 'Engelbert', 'Engelbrekt', 'Enok', 'Erhard', 'Eric', 'Erik', 'Erland', 'Erling', 'Ernfrid', 'Ernst', 'Esbjörn', 'Eskil', 'Eugén', 'Eugen', 'Evald', 'Eve', 'Evert',
        'Fabian', 'Felix', 'Ferdinand', 'Filip', 'Fingal', 'Finn', 'Folke', 'Frank', 'Frans', 'Franz', 'Fred', 'Fredrik', 'Fridolf', 'Friedrich', 'Fritiof', 'Fritjof', 'Frits', 'Fritz',
        'Gabriel', 'Georg', 'George', 'Gerhard', 'Gert', 'Gideon', 'Gilbert', 'Gillis', 'Glenn', 'Gottfrid', 'Gotthard', 'Greger', 'Gudmund', 'Gunder', 'Gunnar', 'Gustaf', 'Gustav', 'Göran', 'Görgen', 'Gösta', 'Göte',
        'Hadar', 'Halvar', 'Halvard', 'Hans', 'Hans-Erik', 'Hans-Olof', 'Hans-Åke', 'Harald', 'Hardy', 'Harry', 'Hartvig', 'Hasse', 'Heinrich', 'Heinz', 'Helge', 'Helmer', 'Henning', 'Henric', 'Henrik', 'Henry', 'Herbert', 'Heribert', 'Herman', 'Hilbert', 'Hilding', 'Hilmer', 'Hjalmar', 'Holger', 'Holmfrid', 'Hubert', 'Hugo', 'Håkan',
        'Inge', 'Ingemar', 'Ingmar', 'Ingvald', 'Ingvar', 'Isak', 'Isidor', 'Ivan', 'Ivar',
        'Jack', 'Jacob', 'Jakob', 'James', 'Jan', 'Janne', 'Jan-Eric', 'Jan-Erik', 'Jan-Olof', 'Jan-Olov', 'Jan-Ove', 'Jan-Åke', 'Jarl', 'Jean', 'Jens', 'Jerker', 'Jerry', 'Jesper', 'Jim', 'Jimmy', 'Joachim', 'Joacim', 'Joakim', 'Joel', 'Johan', 'Johannes', 'John', 'Johnny', 'Johny', 'Jon', 'Jonas', 'Jonny', 'Josef', 'Juhani', 'Julius', 'Justus', 'Jöns', 'Jörgen',
        'Kai', 'Kaj', 'Kalevi', 'Karl', 'Karl-Axel', 'Karl-Erik', 'Karl-Gunnar', 'Karl-Gustaf', 'Karl-Gustav', 'Karl-Johan', 'Kennert', 'Kennet', 'Kenneth', 'Kenny', 'Kent', 'Kenth', 'Kjell', 'Kjell-Åke', 'Klas', 'Knut', 'Konrad', 'Konstantin', 'Krister', 'Kristian', 'Kristoffer', 'Kurt', 'Kåre',
        'Lage', 'Lambert', 'Lars', 'Lars-Eric', 'Lars-Erik', 'Lars-Gunnar', 'Lars-Göran', 'Lars-Olof', 'Lars-Olov', 'Lars-Ove', 'Lars-Åke', 'Laurentius', 'Leander', 'Leif', 'Lennart', 'Leo', 'Leon', 'Leonard', 'Leopold', 'Levi', 'Levin', 'Linné', 'Linus', 'Lorentz', 'Louis', 'Ludvig',
        'Magni', 'Magnus', 'Malkolm', 'Malte', 'Manfred', 'Manne', 'Marcus', 'Markus', 'Martin', 'Mathias', 'Mats', 'Matti', 'Mattias', 'Matts', 'Maurits', 'Mauritz', 'Max', 'Melker', 'Micael', 'Michael', 'Mickael', 'Mikael', 'Morgan', 'Måns', 'Mårten',
        'Napoleon', 'Natanael', 'Nicklas', 'Niclas', 'Niklas', 'Nikolaus', 'Nils', 'Nils-Erik', 'Nore',
        'Odd', 'Ola', 'Olaus', 'Olav', 'Olavi', 'Ole', 'Oliver', 'Olle', 'Olof', 'Olov', 'Orvar', 'Oscar', 'Oskar', 'Ossian', 'Osvald', 'Otto', 'Ove', 'Owe',
        'Patric', 'Patrick', 'Patrik', 'Paul', 'Peder', 'Per', 'Percy', 'Per-Anders', 'Per-Arne', 'Per-Erik', 'Per-Ola', 'Per-Olof', 'Per-Olov', 'Per-Åke', 'Peter', 'Petrus', 'Petter', 'Pierre', 'Pontus', 'Pär',
        'Ragnar', 'Ragnvald', 'Ralf', 'Ralph', 'Raymond', 'Reidar', 'Reine', 'Reinhold', 'Reino', 'Richard', 'Rickard', 'Rikard', 'Robert', 'Roger', 'Roine', 'Roland', 'Rolf', 'Ronald', 'Ronnie', 'Ronny', 'Roy', 'Ruben', 'Rudolf', 'Runar', 'Rune', 'Runo', 'Rutger',
        'Salomon', 'Sam', 'Samuel', 'Sanfrid', 'Sebastian', 'Set', 'Seth', 'Seved', 'Severin', 'Sigfrid', 'Sigmund', 'Signar', 'Sigurd', 'Sigvard', 'Simon', 'Sivert', 'Sixten', 'Sonny', 'Staffan', 'Stanley', 'Stefan', 'Stellan', 'Sten', 'Stephan', 'Steve', 'Stig', 'Sture', 'Sune', 'Svante', 'Sven', 'Sven-Erik', 'Sven-Olof', 'Sven-Olov', 'Sven-Åke', 'Sverker', 'Sölve', 'Sören',
        'Tage', 'Ted', 'Teodor', 'Theodor', 'Thomas', 'Thor', 'Thorbjörn', 'Thord', 'Thore', 'Thorsten', 'Thorvald', 'Thure', 'Tobias', 'Toivo', 'Tom', 'Tomas', 'Tommy', 'Tonny', 'Tony', 'Tor', 'Torbjörn', 'Tord', 'Tore', 'Torgny', 'Torkel', 'Torsten', 'Torvald', 'Tryggve', 'Ture', 'Tyko',
        'Ulf', 'Ulrik', 'Uno', 'Urban',
        'Valdemar', 'Valentin', 'Valfrid', 'Vallentin', 'Valter', 'Veine', 'Verner', 'Victor', 'Vidar', 'Viggo', 'Viking', 'Viktor', 'Vilgot', 'Vilhelm', 'Villiam', 'Villy', 'Vincent', 'Vitalis',
        'Waldemar', 'Walter', 'Werner', 'Wilhelm', 'William', 'Willy',
        'Yngve',
        'Åke',
        'Örjan', 'Östen',
    ];

    /**
     * @var array Swedish common last names
     *
     * @see http://www.scb.se/sv_/Hitta-statistik/Statistik-efter-amne/Befolkning/Amnesovergripande-statistik/Namnstatistik/30898/2012A01x/Samtliga-folkbokforda--Efternamn-topplistor/Efternamn-topp-100/
     */
    protected static $lastName = [

        'Abrahamsson', 'Andersson', 'Andreasson', 'Arvidsson', 'Axelsson',
        'Bengtsson', 'Berg', 'Berggren', 'Berglund', 'Bergman', 'Bergqvist', 'Bergström', 'Björk', 'Björklund', 'Blom', 'Blomqvist',
        'Claesson',
        'Dahlberg', 'Danielsson',
        'Engström', 'Ek', 'Eklund', 'Ekström', 'Eliasson', 'Eriksson',
        'Falk', 'Forsberg', 'Fransson', 'Fredriksson',
        'Gunnarsson', 'Gustafsson',
        'Hansen', 'Hansson', 'Hedlund', 'Hellström', 'Henriksson', 'Hermansson', 'Holm', 'Holmberg', 'Holmgren', 'Holmqvist', 'Håkansson',
        'Isaksson', 'Ivarsson',
        'Jakobsson', 'Jansson', 'Johansson', 'Jonasson', 'Jonsson', 'Jönsson',
        'Karlsson',
        'Larsson', 'Lind', 'Lindberg', 'Lindgren', 'Lindholm', 'Lindqvist', 'Lindström', 'Lund', 'Lundberg', 'Lundgren', 'Lundin', 'Lundqvist', 'Lundström', 'Löfgren',
        'Magnusson', 'Martinsson', 'Mattsson', 'Månsson', 'Mårtensson',
        'Nilsson', 'Norberg', 'Nordin', 'Nordström', 'Nyberg', 'Nyström',
        'Olofsson', 'Olsson',
        'Persson', 'Pettersson', 'Pålsson',
        'Samuelsson', 'Sandberg', 'Sandström', 'Sjöberg', 'Sjögren', 'Ström', 'Strömberg', 'Sundberg', 'Sundqvist', 'Sundström', 'Svensson', 'Söderberg',
        'Viklund',
        'Wallin', 'Wikström',
        'Åberg', 'Åkesson', 'Åström',
        'Öberg',
    ];

    /**
     * National Personal Identity number (personnummer)
     *
     * @see http://en.wikipedia.org/wiki/Personal_identity_number_(Sweden)
     *
     * @param \DateTime $birthdate
     * @param string    $gender    Person::GENDER_MALE || Person::GENDER_FEMALE
     *
     * @return string on format XXXXXX-XXXX
     */
    public function personalIdentityNumber(\DateTime $birthdate = null, $gender = null)
    {
        if (!$birthdate) {
            $birthdate = \Faker\Provider\DateTime::dateTimeThisCentury();
        }
        $datePart = $birthdate->format('ymd');
        $randomDigits = $this->getBirthNumber($gender);

        $checksum = Luhn::computeCheckDigit($datePart . $randomDigits);

        return $datePart . '-' . $randomDigits . $checksum;
    }

    /**
     * @param string $gender Person::GENDER_MALE || Person::GENDER_FEMALE
     *
     * @return string of three digits
     */
    protected function getBirthNumber($gender = null)
    {
        if ($gender && $gender === static::GENDER_MALE) {
            return (string) static::numerify('##') . static::randomElement([1, 3, 5, 7, 9]);
        }

        $zeroCheck = static function ($callback) {
            do {
                $randomDigits = $callback();
            } while ($randomDigits === '000');

            return $randomDigits;
        };

        if ($gender && $gender === static::GENDER_FEMALE) {
            return $zeroCheck(static function () {
                return (string) static::numerify('##') . static::randomElement([0, 2, 4, 6, 8]);
            });
        }

        return  $zeroCheck(static function () {
            return (string) static::numerify('###');
        });
    }
}
