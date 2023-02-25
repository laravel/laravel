<?php

namespace Faker\Provider\ro_MD;

class Person extends \Faker\Provider\Person
{
    // http://en.wikipedia.org/wiki/Romanian_name, prefixes are for more formal purposes
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}}',
    ];

    //http://ro.wikipedia.org/wiki/List%C4%83_de_prenume_rom%C3%A2ne%C8%99ti#Feminine
    protected static $firstNameFemale = [
        'Ada', 'Adela', 'Adelaida', 'Adelina', 'Adina', 'Adriana', 'Agata', 'Aglaia', 'Agripina', 'Aida', 'Alberta', 'Albertina', 'Alexandra', 'Alexandrina', 'Alida', 'Alina', 'Alice', 'Alis', 'Alma',
        'Amalia', 'Amelia', 'Amanda', 'Ana', 'Anabela', 'Anaida', 'Anamaria', 'Anastasia', 'Anca', 'Ancuța', 'Anda', 'Andra', 'Andrada', 'Andreea', 'Anemona', 'Aneta', 'Angela', 'Anghelina', 'Anica',
        'Anișoara', 'Antoaneta', 'Antonia', 'Antonela', 'Anuța', 'Ariadna', 'Ariana', 'Arina', 'Aristița', 'Artemisa', 'Astrid', 'Atena', 'Augustina', 'Aura', 'Aurelia', 'Aureliana', 'Aurica', 'Aurora',
        'Beatrice', 'Betina', 'Bianca', 'Blanduzia', 'Bogdana', 'Brândușa', 'Camelia', 'Carina', 'Carla', 'Carmen', 'Carmina', 'Carolina', 'Casandra', 'Casiana', 'Caterina', 'Catinca', 'Catrina', 'Catrinel',
        'Cătălina', 'Cecilia', 'Celia', 'Cerasela', 'Cezara', 'Cipriana', 'Clara', 'Clarisa', 'Claudia', 'Clementina', 'Cleopatra', 'Codrina', 'Codruța', 'Constantina', 'Constanța', 'Consuela', 'Coralia',
        'Corina', 'Cornelia', 'Cosmina', 'Crenguța', 'Crina', 'Cristina', 'Daciana', 'Dafina', 'Daiana', 'Dalia', 'Dana', 'Daniela', 'Daria', 'Dariana', 'Delia', 'Demetra', 'Denisa', 'Despina', 'Diana',
        'Dida', 'Didina', 'Dimitrina', 'Dina', 'Dochia', 'Doina', 'Domnica', 'Dora', 'Doriana', 'Dorina', 'Dorli', 'Draga', 'Dumitra', 'Dumitrana', 'Ecaterina', 'Eftimia', 'Elena', 'Eleonora', 'Eliana',
        'Elisabeta', 'Elisaveta', 'Eliza', 'Elodia', 'Elvira', 'Emilia', 'Emanuela', 'Erica', 'Estera', 'Eufrosina', 'Eugenia', 'Eusebia', 'Eva', 'Evanghelina', 'Evelina', 'Fabia', 'Fabiana', 'Felicia',
        'Filofteia', 'Fiona', 'Flavia', 'Floare', 'Floarea', 'Flora', 'Floriana', 'Florica', 'Florina', 'Florentina', 'Florența', 'Francesca', 'Frusina', 'Gabriela', 'Geanina', 'Gențiana', 'Georgeta',
        'Georgia', 'Georgiana', 'Geta', 'Gherghina', 'Gianina', 'Gina', 'Giorgiana', 'Grațiana', 'Grațiela', 'Hortensia', 'Henrieta', 'Heracleea', 'Iasmina', 'Ica', 'Ileana', 'Ilinca', 'Ilona', 'Ina',
        'Ioana', 'Ioanina', 'Iolanda', 'Ionela', 'Ionelia', 'Iosefina', 'Irina', 'Iridenta', 'Iris', 'Isabela', 'Iulia', 'Iuliana', 'Iustina', 'Ivona', 'Izabela', 'Jana', 'Janeta', 'Janina', 'Jasmina',
        'Jeana', 'Julia', 'Julieta', 'Larisa', 'Laura', 'Laurenția', 'Lavinia', 'Lăcrămioara', 'Leana', 'Lelia', 'Leontina', 'Leopoldina', 'Letiția', 'Lia', 'Liana', 'Lidia', 'Ligia', 'Lili', 'Liliana',
        'Lioara', 'Livia', 'Loredana', 'Lorelei', 'Lorena', 'Luana', 'Lucia', 'Luciana', 'Lucreția', 'Ludovica', 'Ludmila', 'Luiza', 'Luminița', 'Magdalena', 'Maia', 'Manuela', 'Mara', 'Marcela', 'Marga',
        'Margareta', 'Marcheta', 'Maria', 'Mariana', 'Maricica', 'Marilena', 'Marina', 'Marinela', 'Marioara', 'Marta', 'Matilda', 'Malvina', 'Mădălina', 'Mălina', 'Mărioara', 'Măriuca', 'Melania', 'Melina',
        'Mihaela', 'Milena', 'Mina', 'Minodora', 'Mioara', 'Mirabela', 'Mirela', 'Mirona', 'Miruna', 'Mona', 'Monalisa', 'Monica', 'Nadia', 'Narcisa', 'Natalia', 'Natașa', 'Noemi', 'Nicoleta', 'Niculina',
        'Nidia', 'Nora', 'Norica', 'Oana', 'Octavia', 'Octaviana', 'Ofelia', 'Olga', 'Olimpia', 'Olivia', 'Ortansa', 'Otilia', 'Ozana', 'Pamela', 'Paraschiva', 'Paula', 'Paulica', 'Paulina', 'Patricia',
        'Petronela', 'Petruța', 'Pompilia', 'Profira', 'Rada', 'Rafila', 'Raluca', 'Ramona', 'Rebeca', 'Renata', 'Rica', 'Roberta', 'Robertina', 'Rodica', 'Romanița', 'Romina', 'Roza', 'Rozalia', 'Roxana',
        'Roxelana', 'Ruxanda', 'Ruxandra', 'Sabina', 'Sabrina', 'Safta', 'Salomea', 'Sanda', 'Saveta', 'Savina', 'Sânziana', 'Semenica', 'Severina', 'Sidonia', 'Silvia', 'Silvana', 'Silviana',
        'Simina', 'Simona', 'Smaranda', 'Sofia', 'Sonia', 'Sorana', 'Sorina', 'Speranța', 'Stana', 'Stanca', 'Stela', 'Steliana', 'Steluța', 'Suzana', 'Svetlana', 'Ștefana', 'Ștefania', 'Tamara', 'Tania',
        'Tatiana', 'Teea', 'Teodora', 'Teodosia', 'Teona', 'Tiberia', 'Timea', 'Tinca', 'Tincuța', 'Tudora', 'Tudorița', 'Tudosia', 'Valentina', 'Valeria', 'Vanesa', 'Varvara', 'Vasilica', 'Venera', 'Vera',
        'Veronica', 'Veta', 'Vicenția', 'Victoria', 'Violeta', 'Viorela', 'Viorica', 'Virginia', 'Viviana', 'Voichița', 'Xenia', 'Zaharia', 'Zamfira', 'Zaraza', 'Zenobia', 'Zenovia', 'Zina', 'Zoe',
    ];

    //http://ro.wikipedia.org/wiki/List%C4%83_de_prenume_rom%C3%A2ne%C8%99ti#Feminine
    protected static $firstNameMale = [
        'Achim', 'Adam', 'Adelin', 'Adonis', 'Adrian', 'Adi', 'Agnos', 'Albert', 'Alex', 'Alexandru', 'Alexe', 'Aleodor', 'Alin', 'Alistar', 'Amedeu', 'Amza', 'Anatolie', 'Andrei', 'Angel', 'Anghel', 'Antim',
        'Anton', 'Antonie', 'Antoniu', 'Arian', 'Aristide', 'Arsenie', 'Augustin', 'Aurel', 'Aurelian', 'Aurică', 'Avram', 'Axinte', 'Barbu', 'Bartolomeu', 'Basarab', 'Bănel', 'Bebe', 'Beniamin', 'Benone',
        'Bernard', 'Bogdan', 'Brăduț', 'Bucur', 'Caius', 'Camil', 'Cantemir', 'Carol', 'Casian', 'Cazimir', 'Călin', 'Cătălin', 'Cedrin', 'Cezar', 'Ciprian', 'Claudiu', 'Codin', 'Codrin', 'Codruț', 'Cornel',
        'Corneliu', 'Corvin', 'Constantin', 'Cosmin', 'Costache', 'Costel', 'Costin', 'Crin', 'Cristea', 'Cristian', 'Cristobal', 'Cristofor', 'Dacian', 'Damian', 'Dan', 'Daniel', 'Darius', 'David',
        'Decebal', 'Denis', 'Dinu', 'Dominic', 'Dorel', 'Dorian', 'Dorin', 'Dorinel', 'Doru', 'Dragoș', 'Ducu', 'Dumitru', 'Edgar', 'Edmond', 'Eduard', 'Eftimie', 'Emil', 'Emilian', 'Emanoil', 'Emanuel',
        'Emanuil', 'Eremia', 'Eric', 'Ernest', 'Eugen', 'Eusebiu', 'Eustațiu', 'Fabian', 'Felix', 'Filip', 'Fiodor', 'Flaviu', 'Florea', 'Florentin', 'Florian', 'Florin', 'Francisc', 'Frederic',
        'Gabi', 'Gabriel', 'Gelu', 'George', 'Georgel', 'Georgian', 'Ghenadie', 'Gheorghe', 'Gheorghiță', 'Ghiță', 'Gică', 'Gicu', 'Giorgian', 'Grațian', 'Gregorian', 'Grigore',
        'Haralamb', 'Haralambie', 'Horațiu', 'Horea', 'Horia', 'Iacob', 'Iancu', 'Ianis', 'Ieremia', 'Ilarie', 'Ilarion', 'Ilie', 'Inocențiu', 'Ioan', 'Ion', 'Ionel', 'Ionică', 'Ionuț', 'Iosif', 'Irinel',
        'Iulian', 'Iuliu', 'Iurie', 'Iustin', 'Iustinian', 'Ivan', 'Jan', 'Jean', 'Jenel', 'Ladislau', 'Lascăr', 'Laurențiu', 'Laurian', 'Lazăr', 'Leonard', 'Leontin', 'Lică', 'Liviu', 'Lorin', 'Luca',
        'Lucențiu', 'Lucian', 'Lucrețiu', 'Ludovic', 'Manole', 'Marcel', 'Marcu', 'Marian', 'Marin', 'Marius', 'Martin', 'Matei', 'Maxim', 'Maximilian', 'Mădălin', 'Mihai', 'Mihail', 'Mihnea', 'Mircea',
        'Miron', 'Mitică', 'Mitruț', 'Mugur', 'Mugurel', 'Nae', 'Narcis', 'Nechifor', 'Nelu', 'Nichifor', 'Nicoară', 'Nicodim', 'Nicolae', 'Nicolaie', 'Nicu', 'Nicuță', 'Niculiță', 'Nicușor', 'Norbert',
        'Norman', 'Octav', 'Octavian', 'Octaviu', 'Olimpian', 'Olimpiu', 'Oliviu', 'Ovidiu', 'Pamfil', 'Panait', 'Panagachie', 'Paul', 'Pavel', 'Pătru', 'Petre', 'Petrică', 'Petrișor', 'Petru', 'Petruț',
        'Pompiliu', 'Radu', 'Rafael', 'Rareș', 'Raul', 'Răducu', 'Răzvan', 'Relu', 'Remus', 'Robert', 'Romeo', 'Romulus', 'Sabin', 'Sandu', 'Sava', 'Sebastian', 'Sergiu', 'Sever', 'Severin', 'Silvian',
        'Silviu', 'Simi', 'Simion', 'Sinică', 'Sorin', 'Stan', 'Stancu', 'Stelian', 'Sandu', 'Șerban', 'Ștefan', 'Teodor', 'Teofil', 'Teohari', 'Theodor', 'Tiberiu', 'Timotei', 'Titus', 'Todor', 'Toma',
        'Traian', 'Tudor', 'Valentin', 'Valeriu', 'Valter', 'Vasile', 'Vasilică', 'Veniamin', 'Vicențiu', 'Victor', 'Vincențiu', 'Viorel', 'Visarion', 'Vlad', 'Vladimir', 'Vlaicu', 'Voicu', 'Zamfir', 'Zeno',
    ];

    //courtesy of Florin LIPAN, at nume.ottomotor.ro
    protected static $lastName = [
        'Achim', 'Adam', 'Albu', 'Aldea', 'Alexa', 'Alexandrescu', 'Alexandru', 'Alexe', 'Andrei', 'Anghel', 'Antal', 'Anton', 'Apostol', 'Ardelean', 'Ardeleanu', 'Avram',
        'Baciu', 'Badea', 'Balan', 'Balint', 'Banica', 'Banu', 'Barbu', 'Barbulescu', 'Bejan', 'Biro', 'Blaga', 'Boboc', 'Bodea', 'Bogdan', 'Bota', 'Botezatu', 'Bratu', 'Bucur', 'Buda', 'Bunea', 'Burlacu',
        'Calin', 'Catana', 'Cazacu', 'Chiriac', 'Chirila', 'Chirita', 'Chis', 'Chivu', 'Ciobanu', 'Ciocan', 'Cojocaru', 'Coman', 'Constantin', 'Constantinescu', 'Cornea', 'Cosma', 'Costache',
        'Costea', 'Costin', 'Covaci', 'Cozma', 'Craciun', 'Cretu', 'Crisan', 'Cristea', 'Cristescu', 'Croitoru', 'Cucu',
        'Damian', 'Dan', 'Danciu', 'Danila', 'Dascalu', 'David', 'Diaconescu', 'Diaconu', 'Dima', 'Dinca', 'Dinu', 'Dobre', 'Dobrescu', 'Dogaru', 'Dragan', 'Draghici',
        'Dragoi', 'Dragomir', 'Dumitrache', 'Dumitrascu', 'Dumitrescu', 'Dumitriu', 'Dumitru', 'Duta',
        'Enache', 'Ene', 'Farcas', 'Filimon', 'Filip', 'Florea', 'Florescu', 'Fodor', 'Fratila',
        'Gabor', 'Gal', 'Ganea', 'Gavrila', 'Georgescu', 'Gheorghe', 'Gheorghita', 'Gheorghiu', 'Gherman', 'Ghita', 'Giurgiu', 'Grecu', 'Grigoras', 'Grigore', 'Grigorescu', 'Grosu', 'Groza',
        'Horvath', 'Iacob', 'Iancu', 'Ichim', 'Ignat', 'Ilie', 'Iliescu', 'Ion', 'Ionescu', 'Ionita', 'Iordache', 'Iorga', 'Iosif', 'Irimia', 'Ispas', 'Istrate', 'Ivan', 'Ivascu',
        'Kiss', 'Kovacs', 'Lazar', 'Luca', 'Lungu', 'Lupu', 'Macovei', 'Maftei', 'Man', 'Manea', 'Manolache', 'Manole', 'Marcu', 'Marginean', 'Marian', 'Marin', 'Marinescu', 'Martin', 'Mateescu',
        'Matei', 'Maxim', 'Mazilu', 'Micu', 'Mihai', 'Mihaila', 'Mihailescu', 'Mihalache', 'Mihalcea', 'Milea', 'Militaru', 'Mircea', 'Mirea', 'Miron', 'Miu', 'Mocanu', 'Moga', 'Moise', 'Moldovan',
        'Moldoveanu', 'Molnar', 'Morar', 'Moraru', 'Muntean', 'Munteanu', 'Muresan', 'Musat', 'Nagy', 'Nastase', 'Neacsu', 'Neagoe', 'Neagu', 'Neamtu', 'Nechita', 'Necula', 'Nedelcu',
        'Negoita', 'Negrea', 'Negru', 'Nemes', 'Nica', 'Nicoara', 'Nicolae', 'Nicolescu', 'Niculae', 'Niculescu', 'Nistor', 'Nita', 'Nitu',
        'Oancea', 'Olariu', 'Olaru', 'Oltean', 'Olteanu', 'Oprea', 'Opris', 'Paduraru', 'Pana', 'Panait', 'Paraschiv', 'Parvu', 'Pasca', 'Pascu', 'Patrascu', 'Paun', 'Pavel', 'Petcu', 'Peter',
        'Petre', 'Petrea', 'Petrescu', 'Pintea', 'Pintilie', 'Pirvu', 'Pop', 'Popa', 'Popescu', 'Popovici', 'Preda', 'Prodan', 'Puiu', 'Radoi', 'Radu', 'Radulescu', 'Roman', 'Rosca', 'Rosu',
        'Rotaru', 'Rus', 'Rusu', 'Sabau', 'Sandor', 'Sandu', 'Sarbu', 'Sava', 'Savu', 'Serban', 'Sima', 'Simion', 'Simionescu', 'Simon', 'Sirbu', 'Soare', 'Solomon', 'Staicu', 'Stan',
        'Stanciu', 'Stancu', 'Stanescu', 'Stefan', 'Stefanescu', 'Stoian', 'Stoica', 'Stroe', 'Suciu', 'Szabo', 'Szasz', 'Szekely', 'Tamas', 'Tanase', 'Tataru', 'Teodorescu', 'Toader',
        'Toma', 'Tomescu', 'Toth', 'Trandafir', 'Trif', 'Trifan', 'Tudor', 'Tudorache', 'Tudose', 'Turcu', 'Ungureanu', 'Ursu', 'Vaduva', 'Varga', 'Vasile', 'Vasilescu', 'Vasiliu', 'Veres',
        'Vintila', 'Visan', 'Vlad', 'Voicu', 'Voinea', 'Zaharia', 'Zamfir',
    ];

    protected static $titleMale = ['dl.', 'ing.', 'dr.'];
    protected static $titleFemale = ['d-na.', 'd-șoara', 'ing.', 'dr.'];
}
