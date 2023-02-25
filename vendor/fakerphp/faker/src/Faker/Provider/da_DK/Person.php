<?php

namespace Faker\Provider\da_DK;

use Faker\Provider\DateTime;

/**
 * @see http://www.danskernesnavne.navneforskning.ku.dk/Personnavne.asp
 */
class Person extends \Faker\Provider\Person
{
    /**
     * @var array Danish person name formats.
     */
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{middleName}} {{lastName}}',
        '{{firstNameMale}} {{middleName}} {{lastName}}',
        '{{firstNameMale}} {{middleName}}-{{middleName}} {{lastName}}',
        '{{firstNameMale}} {{middleName}} {{middleName}}-{{lastName}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{middleName}} {{lastName}}',
        '{{firstNameFemale}} {{middleName}} {{lastName}}',
        '{{firstNameFemale}} {{middleName}}-{{middleName}} {{lastName}}',
        '{{firstNameFemale}} {{middleName}} {{middleName}}-{{lastName}}',
    ];

    /**
     * @var array Danish first names.
     */
    protected static $firstNameMale = [
        'Aage', 'Adam', 'Adolf', 'Ahmad', 'Ahmed', 'Aksel', 'Albert', 'Alex', 'Alexander', 'Alf', 'Alfred', 'Ali', 'Allan',
        'Anders', 'Andreas', 'Anker', 'Anton', 'Arne', 'Arnold', 'Arthur', 'Asbjørn', 'Asger', 'August', 'Axel', 'Benjamin',
        'Benny', 'Bent', 'Bernhard', 'Birger', 'Bjarne', 'Bjørn', 'Bo', 'Brian', 'Bruno', 'Børge', 'Carl', 'Carlo',
        'Carsten', 'Casper', 'Charles', 'Chris', 'Christian', 'Christoffer', 'Christopher', 'Claus', 'Dan', 'Daniel', 'David', 'Dennis',
        'Ebbe', 'Edmund', 'Edvard', 'Egon', 'Einar', 'Ejvind', 'Elias', 'Emanuel', 'Emil', 'Erik', 'Erland', 'Erling',
        'Ernst', 'Esben', 'Ferdinand', 'Finn', 'Flemming', 'Frank', 'Freddy', 'Frederik', 'Frits', 'Fritz', 'Frode', 'Georg',
        'Gerhard', 'Gert', 'Gunnar', 'Gustav', 'Hans', 'Harald', 'Harry', 'Hassan', 'Heine', 'Heinrich', 'Helge', 'Helmer',
        'Helmuth', 'Henning', 'Henrik', 'Henry', 'Herman', 'Hermann', 'Holger', 'Hugo', 'Ib', 'Ibrahim', 'Ivan', 'Jack',
        'Jacob', 'Jakob', 'Jan', 'Janne', 'Jens', 'Jeppe', 'Jesper', 'Jimmi', 'Jimmy', 'Joachim', 'Johan', 'Johannes',
        'John', 'Johnny', 'Jon', 'Jonas', 'Jonathan', 'Josef', 'Jul', 'Julius', 'Jørgen', 'Jørn', 'Kai', 'Kaj',
        'Karl', 'Karlo', 'Karsten', 'Kasper', 'Kenneth', 'Kent', 'Kevin', 'Kjeld', 'Klaus', 'Knud', 'Kristian', 'Kristoffer',
        'Kurt', 'Lars', 'Lasse', 'Leif', 'Lennart', 'Leo', 'Leon', 'Louis', 'Lucas', 'Lukas', 'Mads', 'Magnus',
        'Malthe', 'Marc', 'Marcus', 'Marinus', 'Marius', 'Mark', 'Markus', 'Martin', 'Martinus', 'Mathias', 'Max', 'Michael',
        'Mikael', 'Mike', 'Mikkel', 'Mogens', 'Mohamad', 'Mohamed', 'Mohammad', 'Morten', 'Nick', 'Nicklas', 'Nicolai', 'Nicolaj',
        'Niels', 'Niklas', 'Nikolaj', 'Nils', 'Olaf', 'Olav', 'Ole', 'Oliver', 'Oscar', 'Oskar', 'Otto', 'Ove',
        'Palle', 'Patrick', 'Paul', 'Peder', 'Per', 'Peter', 'Philip', 'Poul', 'Preben', 'Rasmus', 'Rene', 'René',
        'Richard', 'Robert', 'Rolf', 'Rudolf', 'Rune', 'Sebastian', 'Sigurd', 'Simon', 'Simone', 'Steen', 'Stefan', 'Steffen',
        'Sten', 'Stig', 'Sune', 'Sven', 'Svend', 'Søren', 'Tage', 'Theodor', 'Thomas', 'Thor', 'Thorvald', 'Tim',
        'Tobias', 'Tom', 'Tommy', 'Tonny', 'Torben', 'Troels', 'Uffe', 'Ulrik', 'Vagn', 'Vagner', 'Valdemar', 'Vang',
        'Verner', 'Victor', 'Viktor', 'Villy', 'Walther', 'Werner', 'Wilhelm', 'William', 'Willy', 'Åge', 'Bendt', 'Bjarke',
        'Chr', 'Eigil', 'Ejgil', 'Ejler', 'Ejnar', 'Ejner', 'Evald', 'Folmer', 'Gunner', 'Gurli', 'Hartvig', 'Herluf', 'Hjalmar',
        'Ingemann', 'Ingolf', 'Ingvard', 'Keld', 'Kresten', 'Laurids', 'Laurits', 'Lauritz', 'Ludvig', 'Lynge', 'Oluf', 'Osvald',
        'Povl', 'Richardt', 'Sigfred', 'Sofus', 'Thorkild', 'Viggo', 'Vilhelm', 'Villiam',
    ];

    protected static $firstNameFemale = [
        'Aase', 'Agathe', 'Agnes', 'Alberte', 'Alexandra', 'Alice', 'Alma', 'Amalie', 'Amanda', 'Andrea', 'Ane', 'Anette', 'Anita',
        'Anja', 'Ann', 'Anna', 'Annalise', 'Anne', 'Anne-Lise', 'Anne-Marie', 'Anne-Mette', 'Annelise', 'Annette', 'Anni', 'Annie',
        'Annika', 'Anny', 'Asta', 'Astrid', 'Augusta', 'Benedikte', 'Bente', 'Berit', 'Bertha', 'Betina', 'Bettina', 'Betty',
        'Birgit', 'Birgitte', 'Birte', 'Birthe', 'Bitten', 'Bodil', 'Britt', 'Britta', 'Camilla', 'Carina', 'Carla', 'Caroline',
        'Cathrine', 'Cecilie', 'Charlotte', 'Christa', 'Christen', 'Christiane', 'Christina', 'Christine', 'Clara', 'Conni', 'Connie', 'Conny',
        'Dagmar', 'Dagny', 'Diana', 'Ditte', 'Dora', 'Doris', 'Dorte', 'Dorthe', 'Ebba', 'Edel', 'Edith', 'Eleonora',
        'Eli', 'Elin', 'Eline', 'Elinor', 'Elisa', 'Elisabeth', 'Elise', 'Ella', 'Ellen', 'Ellinor', 'Elly', 'Elna',
        'Elsa', 'Else', 'Elsebeth', 'Elvira', 'Emilie', 'Emma', 'Emmy', 'Erna', 'Ester', 'Esther', 'Eva', 'Evelyn',
        'Frede', 'Frederikke', 'Freja', 'Frida', 'Gerda', 'Gertrud', 'Gitte', 'Grete', 'Grethe', 'Gudrun', 'Hanna', 'Hanne',
        'Hardy', 'Harriet', 'Hedvig', 'Heidi', 'Helen', 'Helena', 'Helene', 'Helga', 'Helle', 'Henny', 'Henriette', 'Herdis',
        'Hilda', 'Iben', 'Ida', 'Ilse', 'Ina', 'Inga', 'Inge', 'Ingeborg', 'Ingelise', 'Inger', 'Ingrid', 'Irene',
        'Iris', 'Irma', 'Isabella', 'Jane', 'Janni', 'Jannie', 'Jeanette', 'Jeanne', 'Jenny', 'Jes', 'Jette', 'Joan',
        'Johanna', 'Johanne', 'Jonna', 'Josefine', 'Josephine', 'Juliane', 'Julie', 'Jytte', 'Kaja', 'Kamilla', 'Karen', 'Karin',
        'Karina', 'Karla', 'Karoline', 'Kate', 'Kathrine', 'Katja', 'Katrine', 'Ketty', 'Kim', 'Kirsten', 'Kirstine', 'Klara',
        'Krista', 'Kristen', 'Kristina', 'Kristine', 'Laila', 'Laura', 'Laurine', 'Lea', 'Lena', 'Lene', 'Lilian', 'Lilli',
        'Lillian', 'Lilly', 'Linda', 'Line', 'Lis', 'Lisa', 'Lisbet', 'Lisbeth', 'Lise', 'Liselotte', 'Lissi', 'Lissy',
        'Liv', 'Lizzie', 'Lone', 'Lotte', 'Louise', 'Lydia', 'Lykke', 'Lærke', 'Magda', 'Magdalene', 'Mai', 'Maiken',
        'Maj', 'Maja', 'Majbritt', 'Malene', 'Maren', 'Margit', 'Margrethe', 'Maria', 'Mariane', 'Marianne', 'Marie', 'Marlene',
        'Martha', 'Martine', 'Mary', 'Mathilde', 'Matilde', 'Merete', 'Merethe', 'Meta', 'Mette', 'Mia', 'Michelle', 'Mie',
        'Mille', 'Minna', 'Mona', 'Monica', 'Nadia', 'Nancy', 'Nanna', 'Nicoline', 'Nikoline', 'Nina', 'Ninna', 'Oda',
        'Olga', 'Olivia', 'Orla', 'Paula', 'Pauline', 'Pernille', 'Petra', 'Pia', 'Poula', 'Ragnhild', 'Randi', 'Rasmine',
        'Rebecca', 'Rebekka', 'Rigmor', 'Rikke', 'Rita', 'Rosa', 'Rose', 'Ruth', 'Sabrina', 'Sandra', 'Sanne', 'Sara',
        'Sarah', 'Selma', 'Severin', 'Sidsel', 'Signe', 'Sigrid', 'Sine', 'Sofia', 'Sofie', 'Solveig', 'Solvejg', 'Sonja',
        'Sophie', 'Stephanie', 'Stine', 'Susan', 'Susanne', 'Tanja', 'Thea', 'Theodora', 'Therese', 'Thi', 'Thyra', 'Tina',
        'Tine', 'Tove', 'Trine', 'Ulla', 'Vera', 'Vibeke', 'Victoria', 'Viktoria', 'Viola', 'Vita', 'Vivi', 'Vivian',
        'Winnie', 'Yrsa', 'Yvonne', 'Agnete', 'Agnethe', 'Alfrida', 'Alvilda', 'Anine', 'Bolette', 'Dorthea', 'Gunhild',
        'Hansine', 'Inge-Lise', 'Jensine', 'Juel', 'Jørgine', 'Kamma', 'Kristiane', 'Maj-Britt', 'Margrete', 'Metha', 'Nielsine',
        'Oline', 'Petrea', 'Petrine', 'Pouline', 'Ragna', 'Sørine', 'Thora', 'Valborg', 'Vilhelmine',
    ];

    /**
     * @var array Danish middle names.
     */
    protected static $middleName = [
        'Møller', 'Lund', 'Holm', 'Jensen', 'Juul', 'Nielsen', 'Kjær', 'Hansen', 'Skov', 'Østergaard', 'Vestergaard',
        'Nørgaard', 'Dahl', 'Bach', 'Friis', 'Søndergaard', 'Andersen', 'Bech', 'Pedersen', 'Bruun', 'Nygaard', 'Winther',
        'Bang', 'Krogh', 'Schmidt', 'Christensen', 'Hedegaard', 'Toft', 'Damgaard', 'Holst', 'Sørensen', 'Juhl', 'Munk',
        'Skovgaard', 'Søgaard', 'Aagaard', 'Berg', 'Dam', 'Petersen', 'Lind', 'Overgaard', 'Brandt', 'Larsen', 'Bak', 'Schou',
        'Vinther', 'Bjerregaard', 'Riis', 'Bundgaard', 'Kruse', 'Mølgaard', 'Hjorth', 'Ravn', 'Madsen', 'Rasmussen',
        'Jørgensen', 'Kristensen', 'Bonde', 'Bay', 'Hougaard', 'Dalsgaard', 'Kjærgaard', 'Haugaard', 'Munch', 'Bjerre', 'Due',
        'Sloth', 'Leth', 'Kofoed', 'Thomsen', 'Kragh', 'Højgaard', 'Dalgaard', 'Hjort', 'Kirkegaard', 'Bøgh', 'Beck', 'Nissen',
        'Rask', 'Høj', 'Brix', 'Storm', 'Buch', 'Bisgaard', 'Birch', 'Gade', 'Kjærsgaard', 'Hald', 'Lindberg', 'Høgh', 'Falk',
        'Koch', 'Thorup', 'Borup', 'Knudsen', 'Vedel', 'Poulsen', 'Bøgelund', 'Juel', 'Frost', 'Hvid', 'Bjerg', 'Bæk', 'Elkjær',
        'Hartmann', 'Kirk', 'Sand', 'Sommer', 'Skou', 'Nedergaard', 'Meldgaard', 'Brink', 'Lindegaard', 'Fischer', 'Rye',
        'Hoffmann', 'Daugaard', 'Gram', 'Johansen', 'Meyer', 'Schultz', 'Fogh', 'Bloch', 'Lundgaard', 'Brøndum', 'Jessen',
        'Busk', 'Holmgaard', 'Lindholm', 'Krog', 'Egelund', 'Engelbrecht', 'Buus', 'Korsgaard', 'Ellegaard', 'Tang', 'Steen',
        'Kvist', 'Olsen', 'Nørregaard', 'Fuglsang', 'Wulff', 'Damsgaard', 'Hauge', 'Sonne', 'Skytte', 'Brun', 'Kronborg',
        'Abildgaard', 'Fabricius', 'Bille', 'Skaarup', 'Rahbek', 'Borg', 'Torp', 'Klitgaard', 'Nørskov', 'Greve', 'Hviid',
        'Mørch', 'Buhl', 'Rohde', 'Mørk', 'Vendelbo', 'Bjørn', 'Laursen', 'Egede', 'Rytter', 'Lehmann', 'Guldberg', 'Rosendahl',
        'Krarup', 'Krogsgaard', 'Westergaard', 'Rosendal', 'Fisker', 'Højer', 'Rosenberg', 'Svane', 'Storgaard', 'Pihl',
        'Mohamed', 'Bülow', 'Birk', 'Hammer', 'Bro', 'Kaas', 'Clausen', 'Nymann', 'Egholm', 'Ingemann', 'Haahr', 'Olesen',
        'Nøhr', 'Brinch', 'Bjerring', 'Christiansen', 'Schrøder', 'Guldager', 'Skjødt', 'Højlund', 'Ørum', 'Weber',
        'Bødker', 'Bruhn', 'Stampe', 'Astrup', 'Schack', 'Mikkelsen', 'Høyer', 'Husted', 'Skriver', 'Lindgaard', 'Yde',
        'Sylvest', 'Lykkegaard', 'Ploug', 'Gammelgaard', 'Pilgaard', 'Brogaard', 'Degn', 'Kaae', 'Kofod', 'Grønbæk',
        'Lundsgaard', 'Bagge', 'Lyng', 'Rømer', 'Kjeldgaard', 'Hovgaard', 'Groth', 'Hyldgaard', 'Ladefoged', 'Jacobsen',
        'Linde', 'Lange', 'Stokholm', 'Bredahl', 'Hein', 'Mose', 'Bækgaard', 'Sandberg', 'Klarskov', 'Kamp', 'Green',
        'Iversen', 'Riber', 'Smedegaard', 'Nyholm', 'Vad', 'Balle', 'Kjeldsen', 'Strøm', 'Borch', 'Lerche', 'Grønlund',
        'Vestergård', 'Østergård', 'Nyborg', 'Qvist', 'Damkjær', 'Kold', 'Sønderskov', 'Bank',
    ];

    /**
     * @var array Danish last names.
     */
    protected static $lastName = [
        'Jensen', 'Nielsen', 'Hansen', 'Pedersen', 'Andersen', 'Christensen', 'Larsen', 'Sørensen', 'Rasmussen', 'Petersen',
        'Jørgensen', 'Madsen', 'Kristensen', 'Olsen', 'Christiansen', 'Thomsen', 'Poulsen', 'Johansen', 'Knudsen', 'Mortensen',
        'Møller', 'Jacobsen', 'Jakobsen', 'Olesen', 'Frederiksen', 'Mikkelsen', 'Henriksen', 'Laursen', 'Lund', 'Schmidt',
        'Eriksen', 'Holm', 'Kristiansen', 'Clausen', 'Simonsen', 'Svendsen', 'Andreasen', 'Iversen', 'Jeppesen', 'Mogensen',
        'Jespersen', 'Nissen', 'Lauridsen', 'Frandsen', 'Østergaard', 'Jepsen', 'Kjær', 'Carlsen', 'Vestergaard', 'Jessen',
        'Nørgaard', 'Dahl', 'Christoffersen', 'Skov', 'Søndergaard', 'Bertelsen', 'Bruun', 'Lassen', 'Bach', 'Gregersen',
        'Friis', 'Johnsen', 'Steffensen', 'Kjeldsen', 'Bech', 'Krogh', 'Lauritsen', 'Danielsen', 'Mathiesen', 'Andresen',
        'Brandt', 'Winther', 'Toft', 'Ravn', 'Mathiasen', 'Dam', 'Holst', 'Nilsson', 'Lind', 'Berg', 'Schou', 'Overgaard',
        'Kristoffersen', 'Schultz', 'Klausen', 'Karlsen', 'Paulsen', 'Hermansen', 'Thorsen', 'Koch', 'Thygesen', 'Bak', 'Kruse',
        'Bang', 'Juhl', 'Davidsen', 'Berthelsen', 'Nygaard', 'Lorentzen', 'Villadsen', 'Lorenzen', 'Damgaard', 'Bjerregaard',
        'Lange', 'Hedegaard', 'Bendtsen', 'Lauritzen', 'Svensson', 'Justesen', 'Juul', 'Hald', 'Beck', 'Kofoed', 'Søgaard',
        'Meyer', 'Kjærgaard', 'Riis', 'Johannsen', 'Carstensen', 'Bonde', 'Ibsen', 'Fischer', 'Andersson', 'Bundgaard',
        'Johannesen', 'Eskildsen', 'Hemmingsen', 'Andreassen', 'Thomassen', 'Schrøder', 'Persson', 'Hjorth', 'Enevoldsen',
        'Nguyen', 'Henningsen', 'Jønsson', 'Olsson', 'Asmussen', 'Michelsen', 'Vinther', 'Markussen', 'Kragh', 'Thøgersen',
        'Johansson', 'Dalsgaard', 'Gade', 'Bjerre', 'Ali', 'Laustsen', 'Buch', 'Ludvigsen', 'Hougaard', 'Kirkegaard', 'Marcussen',
        'Mølgaard', 'Ipsen', 'Sommer', 'Ottosen', 'Müller', 'Krog', 'Hoffmann', 'Clemmensen', 'Nikolajsen', 'Brodersen',
        'Therkildsen', 'Leth', 'Michaelsen', 'Graversen', 'Frost', 'Dalgaard', 'Albertsen', 'Laugesen', 'Due', 'Ebbesen',
        'Munch', 'Svenningsen', 'Ottesen', 'Fisker', 'Albrechtsen', 'Axelsen', 'Erichsen', 'Sloth', 'Bentsen', 'Westergaard',
        'Bisgaard', 'Nicolaisen', 'Magnussen', 'Thuesen', 'Povlsen', 'Thorup', 'Høj', 'Bentzen', 'Johannessen', 'Vilhelmsen',
        'Isaksen', 'Bendixen', 'Ovesen', 'Villumsen', 'Lindberg', 'Thomasen', 'Kjærsgaard', 'Buhl', 'Kofod', 'Ahmed', 'Smith',
        'Storm', 'Christophersen', 'Bruhn', 'Matthiesen', 'Wagner', 'Bjerg', 'Gram', 'Nedergaard', 'Dinesen', 'Mouritsen',
        'Boesen', 'Borup', 'Abrahamsen', 'Wulff', 'Gravesen', 'Rask', 'Pallesen', 'Greve', 'Korsgaard', 'Haugaard', 'Josefsen',
        'Bæk', 'Espersen', 'Thrane', 'Mørch', 'Frank', 'Lynge', 'Rohde', 'Larsson', 'Hammer', 'Torp', 'Sonne', 'Boysen', 'Bay',
        'Pihl', 'Fabricius', 'Høyer', 'Birch', 'Skou', 'Kirk', 'Antonsen', 'Høgh', 'Damsgaard', 'Dall', 'Truelsen', 'Daugaard',
        'Fuglsang', 'Martinsen', 'Therkelsen', 'Jansen', 'Karlsson', 'Caspersen', 'Steen', 'Callesen', 'Balle', 'Bloch', 'Smidt',
        'Rahbek', 'Hjort', 'Bjørn', 'Skaarup', 'Sand', 'Storgaard', 'Willumsen', 'Busk', 'Hartmann', 'Ladefoged', 'Skovgaard',
        'Philipsen', 'Damm', 'Haagensen', 'Hviid', 'Duus', 'Kvist', 'Adamsen', 'Mathiassen', 'Degn', 'Borg', 'Brix', 'Troelsen',
        'Ditlevsen', 'Brøndum', 'Svane', 'Mohamed', 'Birk', 'Brink', 'Hassan', 'Vester', 'Elkjær', 'Lykke', 'Nørregaard',
        'Meldgaard', 'Mørk', 'Hvid', 'Abildgaard', 'Nicolajsen', 'Bengtsson', 'Stokholm', 'Ahmad', 'Wind', 'Rømer', 'Gundersen',
        'Carlsson', 'Grøn', 'Khan', 'Skytte', 'Bagger', 'Hendriksen', 'Rosenberg', 'Jonassen', 'Severinsen', 'Jürgensen',
        'Boisen', 'Groth', 'Bager', 'Fogh', 'Hussain', 'Samuelsen', 'Pilgaard', 'Bødker', 'Dideriksen', 'Brogaard', 'Lundberg',
        'Hansson', 'Schwartz', 'Tran', 'Skriver', 'Klitgaard', 'Hauge', 'Højgaard', 'Qvist', 'Voss', 'Strøm', 'Wolff', 'Krarup',
        'Green', 'Odgaard', 'Tønnesen', 'Blom', 'Gammelgaard', 'Jæger', 'Kramer', 'Astrup', 'Würtz', 'Lehmann', 'Koefoed',
        'Skøtt', 'Lundsgaard', 'Bøgh', 'Vang', 'Martinussen', 'Sandberg', 'Weber', 'Holmgaard', 'Bidstrup', 'Meier', 'Drejer',
        'Schneider', 'Joensen', 'Dupont', 'Lorentsen', 'Bro', 'Bagge', 'Terkelsen', 'Kaspersen', 'Keller', 'Eliasen', 'Lyberth',
        'Husted', 'Mouritzen', 'Krag', 'Kragelund', 'Nørskov', 'Vad', 'Jochumsen', 'Hein', 'Krogsgaard', 'Kaas', 'Tolstrup',
        'Ernst', 'Hermann', 'Børgesen', 'Skjødt', 'Holt', 'Buus', 'Gotfredsen', 'Kjeldgaard', 'Broberg', 'Roed', 'Sivertsen',
        'Bergmann', 'Bjerrum', 'Petersson', 'Smed', 'Jeremiassen', 'Nyborg', 'Borch', 'Foged', 'Terp', 'Mark', 'Busch',
        'Lundgaard', 'Boye', 'Yde', 'Hinrichsen', 'Matzen', 'Esbensen', 'Hertz', 'Westh', 'Holmberg', 'Geertsen', 'Raun',
        'Aagaard', 'Kock', 'Falk', 'Munk',
    ];

    /**
     * Randomly return a danish name.
     *
     * @return string
     */
    public static function middleName()
    {
        return static::randomElement(static::$middleName);
    }

    /**
     * Randomly return a danish CPR number (Personnal identification number) format.
     *
     * @see http://cpr.dk/cpr/site.aspx?p=16
     * @see http://en.wikipedia.org/wiki/Personal_identification_number_%28Denmark%29
     *
     * @return string
     */
    public static function cpr()
    {
        $birthdate = DateTime::dateTimeThisCentury();

        return sprintf('%s-%s', $birthdate->format('dmy'), static::numerify('%###'));
    }
}
