<?php

namespace Faker\Provider\de_DE;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}-{{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}} {{suffix}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}} {{suffix}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}-{{lastName}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}} {{suffix}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}} {{suffix}}',
    ];

    /**
     * Top 500 Names from a phone directory (6. January 2005)
     * {@link} From https://de.wiktionary.org/wiki/Verzeichnis:Deutsch/Namen/die_h%C3%A4ufigsten_m%C3%A4nnlichen_Vornamen_Deutschlands
     */
    protected static $firstNameMale = [
        'Achim', 'Adalbert', 'Adam', 'Adolf', 'Adrian', 'Ahmed', 'Ahmet', 'Albert', 'Albin', 'Albrecht', 'Alex', 'Alexander', 'Alfons', 'Alfred', 'Ali', 'Alois', 'Aloys', 'Alwin', 'Anatoli', 'Andre', 'Andreas', 'Andree', 'Andrej', 'Andrzej', 'André', 'Andy', 'Angelo', 'Ansgar', 'Anton', 'Antonio', 'Antonius', 'Armin', 'Arnd', 'Arndt', 'Arne', 'Arno', 'Arnold', 'Arnulf', 'Arthur', 'Artur', 'August', 'Axel',
        'Bastian', 'Benedikt', 'Benjamin', 'Benno', 'Bernard', 'Bernd', 'Berndt', 'Bernhard', 'Bert', 'Berthold', 'Bertram', 'Björn', 'Bodo', 'Bogdan', 'Boris', 'Bruno', 'Burghard', 'Burkhard',
        'Carl', 'Carlo', 'Carlos', 'Carsten', 'Christian', 'Christof', 'Christoph', 'Christopher', 'Christos', 'Claudio', 'Claus', 'Claus-Dieter', 'Claus-Peter', 'Clemens', 'Cornelius',
        'Daniel', 'Danny', 'Darius', 'David', 'Denis', 'Dennis', 'Detlef', 'Detlev', 'Dierk', 'Dieter', 'Diethard', 'Diethelm', 'Dietmar', 'Dietrich', 'Dimitri', 'Dimitrios', 'Dirk', 'Domenico', 'Dominik',
        'Eberhard', 'Eckard', 'Eckart', 'Eckehard', 'Eckhard', 'Eckhardt', 'Edgar', 'Edmund', 'Eduard', 'Edward', 'Edwin', 'Egbert', 'Egon', 'Ehrenfried', 'Ekkehard', 'Elmar', 'Emanuel', 'Emil', 'Engelbert', 'Enno', 'Enrico', 'Erhard', 'Eric', 'Erich', 'Erik', 'Ernst', 'Ernst-August', 'Erwin', 'Eugen', 'Ewald',
        'Fabian', 'Falk', 'Falko', 'Felix', 'Ferdinand', 'Florian', 'Francesco', 'Franco', 'Frank', 'Franz', 'Franz Josef', 'Franz-Josef', 'Fred', 'Fredi', 'Fridolin', 'Friedbert', 'Friedemann', 'Frieder', 'Friedhelm', 'Friedrich', 'Friedrich-Wilhelm', 'Fritz',
        'Gabriel', 'Gebhard', 'Georg', 'Georgios', 'Gerald', 'Gerd', 'Gerhard', 'Gernot', 'Gero', 'Gerold', 'Gert', 'Gilbert', 'Giovanni', 'Gisbert', 'Giuseppe', 'Gottfried', 'Gotthard', 'Gottlieb', 'Gregor', 'Guenter', 'Guido', 'Guiseppe', 'Gunnar', 'Gunter', 'Gunther', 'Gustav', 'Götz', 'Günter', 'Günther',
        'Hagen', 'Halil', 'Hannes', 'Hanni', 'Hanno', 'Hanns', 'Hans', 'Hans Dieter', 'Hans Georg', 'Hans Jürgen', 'Hans Peter', 'Hans-Christian', 'Hans-Dieter', 'Hans-Georg', 'Hans-Gerd', 'Hans-Günter', 'Hans-Günther', 'Hans-Heinrich', 'Hans-Hermann', 'Hans-J.', 'Hans-Joachim', 'Hans-Jochen', 'Hans-Josef', 'Hans-Jörg', 'Hans-Jürgen', 'Hans-Martin', 'Hans-Otto', 'Hans-Peter', 'Hans-Ulrich', 'Hans-Walter', 'Hans-Werner', 'Hans-Wilhelm', 'Hansjörg', 'Hanspeter', 'Harald', 'Hardy', 'Harri', 'Harro', 'Harry', 'Hartmut', 'Hartwig', 'Hasan', 'Heiko', 'Heiner', 'Heino', 'Heinrich', 'Heinz', 'Heinz-Dieter', 'Heinz-Georg', 'Heinz-Günter', 'Heinz-Joachim', 'Heinz-Josef', 'Heinz-Jürgen', 'Heinz-Peter', 'Heinz-Werner', 'Helfried', 'Helge', 'Hellmut', 'Hellmuth', 'Helmar', 'Helmut', 'Helmuth', 'Hendrik', 'Henning', 'Henrik', 'Henry', 'Henryk', 'Herbert', 'Heribert', 'Hermann', 'Hermann-Josef', 'Herwig', 'Hilmar', 'Hinrich', 'Holger', 'Horst', 'Horst-Dieter', 'Hubert', 'Hubertus', 'Hugo', 'Hüseyin',
        'Ibrahim', 'Ignaz', 'Igor', 'Ingo', 'Ingolf', 'Ismail', 'Ivan', 'Ivo',
        'Jakob', 'Jan', 'Janusz', 'Jens', 'Jens-Uwe', 'Joachim', 'Jochen', 'Johann', 'Johannes', 'John', 'Jonas', 'Jonas', 'Jose', 'Josef', 'Joseph', 'Josip', 'Jost', 'Juergen', 'Julian', 'Julius', 'Juri', 'Jörg', 'Jörn', 'Jürgen',
        'Kai-Uwe', 'Karl', 'Karl Heinz', 'Karl-Ernst', 'Karl-Friedrich', 'Karl-Heinrich', 'Karl-Heinz', 'Karl-Josef', 'Karl-Ludwig', 'Karl-Otto', 'Karl-Wilhelm', 'Karlheinz', 'Karsten', 'Kaspar', 'Kevin', 'Klaus', 'Klaus Dieter', 'Klaus Peter', 'Klaus-Dieter', 'Klaus-Jürgen', 'Klaus-Peter', 'Klemens', 'Knut', 'Konrad', 'Konstantin', 'Konstantinos', 'Kuno', 'Kurt',
        'Lars', 'Leo', 'Leonhard', 'Leonid', 'Leopold', 'Lorenz', 'Lothar', 'Ludger', 'Ludwig', 'Luigi', 'Lukas', 'Lutz',
        'Magnus', 'Maik', 'Malte', 'Manfred', 'Manuel', 'Marc', 'Marcel', 'Marco', 'Marcus', 'Marek', 'Marian', 'Mario', 'Marius', 'Mark', 'Marko', 'Markus', 'Martin', 'Mathias', 'Matthias', 'Max', 'Maximilian', 'Mehmet', 'Meinhard', 'Meinolf', 'Metin', 'Michael', 'Michel', 'Mike', 'Milan', 'Mirco', 'Mirko', 'Miroslav', 'Miroslaw', 'Mohamed', 'Moritz', 'Murat', 'Mustafa',
        'Nico', 'Nicolas', 'Niels', 'Nikola', 'Nikolai', 'Nikolaj', 'Nikolaos', 'Nikolaus', 'Nils', 'Norbert', 'Norman',
        'Olaf', 'Oliver', 'Ortwin', 'Oskar', 'Osman', 'Oswald', 'Otmar', 'Ottmar', 'Otto',
        'Pascal', 'Patrick', 'Paul', 'Peer', 'Peter', 'Philip', 'Philipp', 'Pierre', 'Pietro', 'Piotr',
        'Rafael', 'Raimund', 'Rainer', 'Ralf', 'Ralph', 'Ramazan', 'Raphael', 'Reimund', 'Reiner', 'Reinhard', 'Reinhardt', 'Reinhold', 'Rene', 'René', 'Richard', 'Rico', 'Robert', 'Roberto', 'Robin', 'Roger', 'Roland', 'Rolf', 'Rolf-Dieter', 'Roman', 'Ronald', 'Ronny', 'Rudi', 'Rudolf', 'Rupert', 'Rüdiger',
        'Salvatore', 'Samuel', 'Sandro', 'Sebastian', 'Sergej', 'Siegbert', 'Siegfried', 'Siegmar', 'Siegmund', 'Sigmund', 'Sigurd', 'Silvio', 'Simon', 'Stanislaw', 'Stefan', 'Steffen', 'Stephan', 'Steven', 'Sven', 'Swen', 'Sönke', 'Sören',
        'Theo', 'Theodor', 'Thilo', 'Thomas', 'Thorsten', 'Till', 'Tilo', 'Tim', 'Timo', 'Tino', 'Tobias', 'Tom', 'Toni', 'Torben', 'Torsten',
        'Udo', 'Ulf', 'Uli', 'Ullrich', 'Ulrich', 'Uwe',
        'Valentin', 'Veit', 'Victor', 'Viktor', 'Vincenzo', 'Vinzenz', 'Vitali', 'Vladimir', 'Volker', 'Volkmar',
        'Waldemar', 'Walter', 'Walther', 'Wenzel', 'Werner', 'Wieland', 'Wilfried', 'Wilhelm', 'Willi', 'William', 'Willibald', 'Willy', 'Winfried', 'Wladimir', 'Wolf', 'Wolf-Dieter', 'Wolfgang', 'Wolfram', 'Wulf',
        'Xaver',
        'Yusuf',
    ];

    /**
     * Top 500 Names from a phone directory (6. January 2005)
     * {@link} From https://de.wiktionary.org/wiki/Verzeichnis:Deutsch/Namen/die_h%C3%A4ufigsten_weiblichen_Vornamen_Deutschlands
     */
    protected static $firstNameFemale = [
        'Adele', 'Adelheid', 'Agathe', 'Agnes', 'Alexandra', 'Alice', 'Alma', 'Almut', 'Aloisia', 'Alwine', 'Amalie', 'Ana', 'Anastasia', 'Andrea', 'Anett', 'Anette', 'Angela', 'Angelika', 'Anika', 'Anita', 'Anja', 'Anke', 'Anna', 'Anna-Maria', 'Anne', 'Annegret', 'Annelie', 'Annelies', 'Anneliese', 'Annelore', 'Annemarie', 'Annerose', 'Annett', 'Annette', 'Anni', 'Annika', 'Anny', 'Antje', 'Antonia', 'Antonie', 'Ariane', 'Astrid', 'Auguste', 'Ayse',
        'Babette', 'Barbara', 'Beate', 'Beatrice', 'Beatrix', 'Bernadette', 'Berta', 'Bettina', 'Betty', 'Bianca', 'Bianka', 'Birgit', 'Birgitt', 'Birgitta', 'Birte', 'Brigitta', 'Brigitte', 'Britta', 'Brunhild', 'Brunhilde', 'Bärbel',
        'Carina', 'Carla', 'Carmen', 'Carola', 'Carolin', 'Caroline', 'Cathrin', 'Catrin', 'Centa', 'Charlotte', 'Christa', 'Christel', 'Christiane', 'Christin', 'Christina', 'Christine', 'Christl', 'Cindy', 'Claudia', 'Conny', 'Constanze', 'Cordula', 'Corina', 'Corinna', 'Cornelia', 'Cäcilia', 'Cäcilie',
        'Dagmar', 'Dana', 'Daniela', 'Danuta', 'Denise', 'Diana', 'Dietlinde', 'Dora', 'Doreen', 'Doris', 'Dorit', 'Dorothea', 'Dorothee', 'Dunja', 'Dörte',
        'Edda', 'Edelgard', 'Edeltraud', 'Edeltraut', 'Edith', 'Elena', 'Eleonore', 'Elfi', 'Elfriede', 'Elisabeth', 'Elise', 'Elke', 'Ella', 'Ellen', 'Elli', 'Elly', 'Elsa', 'Elsbeth', 'Else', 'Elvira', 'Emilia', 'Emilie', 'Emine', 'Emma', 'Emmi', 'Emmy', 'Erika', 'Erna', 'Ernestine', 'Esther', 'Eugenie', 'Eva', 'Eva-Maria', 'Evelin', 'Eveline', 'Evelyn', 'Evelyne', 'Evi', 'Ewa',
        'Fatma', 'Felicitas', 'Franziska', 'Frauke', 'Frida', 'Frieda', 'Friederike',
        'Gabi', 'Gabriela', 'Gabriele', 'Gaby', 'Galina', 'Gerda', 'Gerhild', 'Gerlinde', 'Gerta', 'Gerti', 'Gertraud', 'Gertraude', 'Gertrud', 'Gertrude', 'Gesa', 'Gesine', 'Giesela', 'Gisela', 'Gitta', 'Grete', 'Gretel', 'Grit', 'Gudrun', 'Gunda', 'Gundula',
        'Halina', 'Hanna', 'Hanne', 'Hannelore', 'Hatice', 'Hedi', 'Hedwig', 'Heide', 'Heidemarie', 'Heiderose', 'Heidi', 'Heidrun', 'Heike', 'Helen', 'Helena', 'Helene', 'Helga', 'Hella', 'Helma', 'Henny', 'Henri', 'Henriette', 'Hermine', 'Herta', 'Hertha', 'Hilda', 'Hilde', 'Hildegard', 'Hiltrud',
        'Ida', 'Ilka', 'Ilona', 'Ilse', 'Imke', 'Ina', 'Ines', 'Inga', 'Inge', 'Ingeborg', 'Ingeburg', 'Ingelore', 'Ingrid', 'Inka', 'Inna', 'Irena', 'Irene', 'Irina', 'Iris', 'Irma', 'Irmgard', 'Irmhild', 'Irmtraud', 'Irmtraut', 'Isa', 'Isabel', 'Isabell', 'Isabella', 'Isabelle', 'Isolde', 'Ivonne',
        'Jacqueline', 'Jana', 'Janet', 'Janina', 'Janine', 'Jaqueline', 'Jasmin', 'Jeanette', 'Jeannette', 'Jennifer', 'Jenny', 'Jessica', 'Joanna', 'Johanna', 'Johanne', 'Jolanta', 'Josefa', 'Josefine', 'Judith', 'Julia', 'Juliane', 'Jutta',
        'Karen', 'Karin', 'Karina', 'Karla', 'Karola', 'Karolina', 'Karoline', 'Katarina', 'Katharina', 'Kathleen', 'Kathrin', 'Kati', 'Katja', 'Katrin', 'Kerstin', 'Kirsten', 'Kirstin', 'Klara', 'Klaudia', 'Konstanze', 'Kornelia', 'Kristin', 'Kristina', 'Krystyna', 'Kunigunde', 'Käte', 'Käthe',
        'Larissa', 'Laura', 'Lena', 'Leni', 'Leonore', 'Liane', 'Lidia', 'Liesbeth', 'Liesel', 'Lieselotte', 'Lilli', 'Lilly', 'Lilo', 'Lina', 'Linda', 'Lisa', 'Lisbeth', 'Liselotte', 'Loni', 'Lore', 'Lotte', 'Lucia', 'Lucie', 'Ludmila', 'Ludmilla', 'Luise', 'Luzia', 'Luzie', 'Lydia',
        'Madeleine', 'Magda', 'Magdalena', 'Magdalene', 'Maike', 'Maja', 'Mandy', 'Manja', 'Manuela', 'Mareike', 'Maren', 'Marga', 'Margareta', 'Margarete', 'Margaretha', 'Margarethe', 'Margarita', 'Margit', 'Margitta', 'Margot', 'Margret', 'Margrit', 'Maria', 'Marianne', 'Marie', 'Marie-Luise', 'Marietta', 'Marija', 'Marika', 'Marina', 'Marion', 'Marita', 'Maritta', 'Marlen', 'Marlene', 'Marlies', 'Marliese', 'Marlis', 'Marta', 'Martha', 'Martina', 'Mathilde', 'Mechthild', 'Meike', 'Melanie', 'Melitta', 'Meta', 'Michaela', 'Mina', 'Minna', 'Miriam', 'Mirjam', 'Mona', 'Monica', 'Monika', 'Monique',
        'Nadine', 'Nadja', 'Nancy', 'Natalia', 'Natalie', 'Natalja', 'Natascha', 'Nathalie', 'Nelli', 'Nicole', 'Nina', 'Nora',
        'Olga', 'Ortrud', 'Ottilie',
        'Pamela', 'Patricia', 'Patrizia', 'Paula', 'Pauline', 'Peggy', 'Petra', 'Pia',
        'Ramona', 'Rebecca', 'Regina', 'Regine', 'Reinhild', 'Reinhilde', 'Renata', 'Renate', 'Resi', 'Ria', 'Ricarda', 'Rita', 'Romy', 'Rosa', 'Rosalinde', 'Rose', 'Rosel', 'Rosemarie', 'Rosi', 'Rosina', 'Rosita', 'Rosmarie', 'Roswitha', 'Ruth',
        'Sabina', 'Sabine', 'Sabrina', 'Sandra', 'Sandy', 'Sara', 'Sarah', 'Saskia', 'Selma', 'Sibylle', 'Sieglinde', 'Siegrid', 'Siglinde', 'Sigrid', 'Sigrun', 'Silke', 'Silvana', 'Silvia', 'Simona', 'Simone', 'Sina', 'Sofia', 'Sofie', 'Sonja', 'Sophia', 'Sophie', 'Stefanie', 'Steffi', 'Stephanie', 'Susan', 'Susann', 'Susanna', 'Susanne', 'Svenja', 'Svetlana', 'Swetlana', 'Sybille', 'Sylke', 'Sylvia',
        'Tamara', 'Tanja', 'Tatjana', 'Teresa', 'Thea', 'Thekla', 'Theresa', 'Therese', 'Theresia', 'Tina', 'Traudel', 'Traute', 'Trude',
        'Ulla', 'Ulrike', 'Ursel', 'Ursula', 'Uschi', 'Uta', 'Ute',
        'Valentina', 'Valeri', 'Valerie', 'Vanessa', 'Vera', 'Verena', 'Veronika', 'Viktoria', 'Viola',
        'Walburga', 'Wally', 'Waltraud', 'Waltraut', 'Wanda', 'Wendelin', 'Wera', 'Wiebke', 'Wilhelmine', 'Wilma', 'Wiltrud',
        'Yvonne',
        'Änne',
    ];

    /**
     * Top 500 Names from a phone directory (6. January 2005)
     * {@link} https://de.wiktionary.org/wiki/Verzeichnis:Deutsch/Namen/die_h%C3%A4ufigsten_Nachnamen_Deutschlands
     */
    protected static $lastName = [
        'Ackermann', 'Adam', 'Adler', 'Ahrens', 'Albers', 'Albert', 'Albrecht', 'Altmann', 'Anders', 'Appel', 'Arndt', 'Arnold', 'Auer',
        'Bach', 'Bachmann', 'Bader', 'Baier', 'Bartels', 'Barth', 'Barthel', 'Bartsch', 'Bauer', 'Baum', 'Baumann', 'Baumgartner', 'Baur', 'Bayer', 'Beck', 'Becker', 'Beckmann', 'Beer', 'Behrendt', 'Behrens', 'Beier', 'Bender', 'Benz', 'Berg', 'Berger', 'Bergmann', 'Berndt', 'Bernhardt', 'Bertram', 'Betz', 'Beyer', 'Binder', 'Bischoff', 'Bittner', 'Blank', 'Block', 'Blum', 'Bock', 'Bode', 'Born', 'Brand', 'Brandl', 'Brandt', 'Braun', 'Brenner', 'Breuer', 'Brinkmann', 'Brunner', 'Bruns', 'Brückner', 'Buchholz', 'Buck', 'Burger', 'Burkhardt', 'Busch', 'Busse', 'Bär', 'Böhm', 'Böhme', 'Böttcher', 'Bühler', 'Büttner',
        'Christ', 'Conrad',
        'Decker', 'Diehl', 'Dietrich', 'Dietz', 'Dittrich', 'Dorn', 'Döring', 'Dörr',
        'Eberhardt', 'Ebert', 'Eckert', 'Eder', 'Ehlers', 'Eichhorn', 'Engel', 'Engelhardt', 'Engelmann', 'Erdmann', 'Ernst', 'Esser',
        'Falk', 'Feldmann', 'Fiedler', 'Fink', 'Fischer', 'Fleischer', 'Fleischmann', 'Forster', 'Frank', 'Franke', 'Franz', 'Freitag', 'Freund', 'Frey', 'Fricke', 'Friedrich', 'Fritsch', 'Fritz', 'Fröhlich', 'Fuchs', 'Fuhrmann', 'Funk', 'Funke', 'Förster',
        'Gabriel', 'Gebhardt', 'Geiger', 'Geisler', 'Geißler', 'Gerber', 'Gerlach', 'Geyer', 'Giese', 'Glaser', 'Gottschalk', 'Graf', 'Greiner', 'Grimm', 'Gross', 'Groß', 'Großmann', 'Gruber', 'Gärtner', 'Göbel', 'Götz', 'Günther',
        'Haag', 'Haas', 'Haase', 'Hagen', 'Hahn', 'Hamann', 'Hammer', 'Hanke', 'Hansen', 'Harms', 'Hartmann', 'Hartung', 'Hartwig', 'Haupt', 'Hauser', 'Hecht', 'Heck', 'Heil', 'Heim', 'Hein', 'Heine', 'Heinemann', 'Heinrich', 'Heinz', 'Heinze', 'Held', 'Heller', 'Hempel', 'Henke', 'Henkel', 'Hennig', 'Henning', 'Hentschel', 'Herbst', 'Hermann', 'Herold', 'Herrmann', 'Herzog', 'Hess', 'Hesse', 'Heuer', 'Heß', 'Hildebrandt', 'Hiller', 'Hinz', 'Hirsch', 'Hoffmann', 'Hofmann', 'Hohmann', 'Holz', 'Hoppe', 'Horn', 'Huber', 'Hummel', 'Hübner',
        'Jacob', 'Jacobs', 'Jahn', 'Jakob', 'Jansen', 'Janssen', 'Janßen', 'John', 'Jordan', 'Jost', 'Jung', 'Jäger', 'Jürgens',
        'Kaiser', 'Karl', 'Kaufmann', 'Keil', 'Keller', 'Kellner', 'Kern', 'Kessler', 'Keßler', 'Kiefer', 'Kirchner', 'Kirsch', 'Klaus', 'Klein', 'Klemm', 'Klose', 'Kluge', 'Knoll', 'Koch', 'Kohl', 'Kolb', 'Konrad', 'Kopp', 'Kraft', 'Kramer', 'Kraus', 'Krause', 'Krauß', 'Krebs', 'Kremer', 'Kretschmer', 'Krieger', 'Kroll', 'Krug', 'Kruse', 'Krämer', 'Kröger', 'Krüger', 'Kuhlmann', 'Kuhn', 'Kunz', 'Kunze', 'Kurz', 'Köhler', 'König', 'Körner', 'Köster', 'Kühn', 'Kühne',
        'Lang', 'Lange', 'Langer', 'Lauer', 'Lechner', 'Lehmann', 'Lemke', 'Lenz', 'Lindemann', 'Lindner', 'Link', 'Linke', 'Lohmann', 'Lorenz', 'Ludwig', 'Lutz', 'Löffler',
        'Mack', 'Mai', 'Maier', 'Mann', 'Marquardt', 'Martens', 'Martin', 'Marx', 'Maurer', 'May', 'Mayer', 'Mayr', 'Meier', 'Meister', 'Meißner', 'Menzel', 'Merkel', 'Mertens', 'Merz', 'Metz', 'Metzger', 'Meyer', 'Michel', 'Michels', 'Miller', 'Mohr', 'Moll', 'Moritz', 'Moser', 'Möller', 'Müller', 'Münch',
        'Nagel', 'Naumann', 'Neubauer', 'Neubert', 'Neuhaus', 'Neumann', 'Nickel', 'Niemann', 'Noack', 'Noll', 'Nolte', 'Nowak',
        'Opitz', 'Oswald', 'Ott', 'Otto',
        'Pape', 'Paul', 'Peter', 'Peters', 'Petersen', 'Pfeifer', 'Pfeiffer', 'Philipp', 'Pieper', 'Pietsch', 'Pohl', 'Popp', 'Preuß', 'Probst',
        'Raab', 'Rapp', 'Rau', 'Rauch', 'Rausch', 'Reich', 'Reichel', 'Reichert', 'Reimann', 'Reimer', 'Reinhardt', 'Reiter', 'Renner', 'Reuter', 'Richter', 'Riedel', 'Riedl', 'Rieger', 'Ritter', 'Rohde', 'Rose', 'Roth', 'Rothe', 'Rudolph', 'Ruf', 'Runge', 'Rupp', 'Röder', 'Römer',
        'Sander', 'Sauer', 'Sauter', 'Schade', 'Schaller', 'Scharf', 'Scheffler', 'Schenk', 'Scherer', 'Schiller', 'Schilling', 'Schindler', 'Schlegel', 'Schlüter', 'Schmid', 'Schmidt', 'Schmitt', 'Schmitz', 'Schneider', 'Scholz', 'Schott', 'Schrader', 'Schramm', 'Schreiber', 'Schreiner', 'Schröder', 'Schröter', 'Schubert', 'Schuler', 'Schulte', 'Schultz', 'Schulz', 'Schulze', 'Schumacher', 'Schumann', 'Schuster', 'Schwab', 'Schwarz', 'Schweizer', 'Schäfer', 'Schön', 'Schüler', 'Schütte', 'Schütz', 'Schütze', 'Seeger', 'Seidel', 'Seidl', 'Seifert', 'Seiler', 'Seitz', 'Siebert', 'Simon', 'Singer', 'Sommer', 'Sonntag', 'Springer', 'Stadler', 'Stahl', 'Stark', 'Steffen', 'Steffens', 'Stein', 'Steinbach', 'Steiner', 'Stephan', 'Stock', 'Stoll', 'Straub', 'Strauß', 'Strobel', 'Stumpf', 'Sturm',
        'Thiel', 'Thiele', 'Thomas',
        'Ullrich', 'Ulrich', 'Unger', 'Urban',
        'Vetter', 'Vogel', 'Vogt', 'Voigt', 'Vollmer', 'Voss', 'Voß', 'Völker',
        'Wagner', 'Wahl', 'Walter', 'Walther', 'Weber', 'Wegener', 'Wegner', 'Weidner', 'Weigel', 'Weis', 'Weise', 'Weiss', 'Weiß', 'Wendt', 'Wenzel', 'Werner', 'Westphal', 'Wetzel', 'Wiedemann', 'Wiegand', 'Wieland', 'Wiese', 'Wiesner', 'Wild', 'Wilhelm', 'Wilke', 'Will', 'Wimmer', 'Winkler', 'Winter', 'Wirth', 'Witt', 'Witte', 'Wittmann', 'Wolf', 'Wolff', 'Wolter', 'Wulf', 'Wunderlich',
        'Zander', 'Zeller', 'Ziegler', 'Zimmer', 'Zimmermann',
    ];

    protected static $titleMale = ['Herr', 'Herr Dr.', 'Herr Prof.', 'Herr Prof. Dr.'];
    protected static $titleFemale = ['Frau', 'Frau Dr.', 'Frau Prof.', 'Frau Prof. Dr.'];

    protected static $suffix = ['B.Sc.', 'B.A.', 'B.Eng.', 'MBA.'];

    /**
     * @example 'PhD'
     */
    public static function suffix()
    {
        return static::randomElement(static::$suffix);
    }
}
