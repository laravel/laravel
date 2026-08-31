<?php

namespace Faker\Provider\fr_BE;

class Person extends \Faker\Provider\Person
{
    protected static $firstNameMale = [
        'Aaron', 'Adam', 'Adrien', 'Alessio', 'Alexander', 'Alexandre', 'Antoine', 'Arne', 'Arthur', 'Axel', 'Ayoub', 'Baptiste', 'Benjamin',
        'Bo', 'Clément', 'Daan', 'David', 'Diego', 'Dylan', 'Elias', 'Emiel', 'Emile', 'Enzo', 'Ethan', 'Finn',
        'Florian', 'Gabriel', 'Gilles', 'Guillaume', 'Hamza', 'Hugo', 'Ilias', 'Janne', 'Jasper', 'Jelle', 'Jonas', 'Jules',
        'Julien', 'Kato', 'Lars', 'Leon', 'Liam', 'Louis', 'Loïc', 'Luca', 'Lucas', 'Lukas', 'Martin', 'Mathias',
        'Mathis', 'Mathéo', 'Mats', 'Matteo', 'Mauro', 'Maxim', 'Maxime', 'Mehdi', 'Milan', 'Mohamed', 'Mohammed', 'Nathan',
        'Nicolas', 'Niels', 'Noah', 'Nolan', 'Quinten', 'Raphaël', 'Rayan', 'Robbe', 'Romain', 'Ruben', 'Rune', 'Sacha',
        'Sam', 'Samuel', 'Sander', 'Simon', 'Stan', 'Thomas', 'Théo', 'Tibo', 'Tom', 'Tristan', 'Vic', 'Victor',
        'Vince', 'Wout', 'Xander', 'Yanis', 'Jarne', 'Lowie', 'Mathys', 'Senne', 'Seppe', 'Siebe', 'Tuur', 'Warre',
    ];

    protected static $firstNameFemale = [
        'Alexia', 'Alexis', 'Alice', 'Alicia', 'Alyssa', 'Amber', 'Amy', 'Amélie', 'Anaïs', 'Anna', 'Anouk', 'Axelle', 'Aya',
        'Camille', 'Charlotte', 'Chiara', 'Chloé', 'Clara', 'Clémence', 'Célia', 'Elena', 'Eline', 'Elisa', 'Elise', 'Ella',
        'Eloïse', 'Emilie', 'Emma', 'Estelle', 'Eva', 'Febe', 'Femke', 'Fien', 'Fleur', 'Giulia', 'Hajar', 'Hanne',
        'Helena', 'Ines', 'Inès', 'Jade', 'Jana', 'Jeanne', 'Julia', 'Julie', 'Juliette', 'Kaat', 'Kobe', 'Lana',
        'Lander', 'Lara', 'Laura', 'Laure', 'Lena', 'Lien', 'Lilou', 'Lily', 'Lina', 'Linde', 'Lisa', 'Lise',
        'Lola', 'Lore', 'Lotte', 'Louise', 'Lucie', 'Luna', 'Léa', 'Malak', 'Manon', 'Margaux', 'Margot', 'Marie',
        'Marion', 'Maya', 'Maëlle', 'Merel', 'Mila', 'Nina', 'Noa', 'Noor', 'Nora', 'Nore', 'Noé', 'Noémie',
        'Océane', 'Olivia', 'Pauline', 'Rania', 'Robin', 'Romane', 'Salma', 'Sara', 'Sarah', 'Sofia', 'Tess', 'Victoria',
        'Yana', 'Yasmine', 'Zoé', 'Zoë', 'Ferre', 'Roos',
    ];

    protected static $lastName = [
        'Adam', 'Aerts', 'Amrani', 'André', 'Antoine', 'Baert', 'Bah', 'Barry', 'Bastin', 'Bauwens', 'Benali', 'Bernard', 'Bertrand', 'Bodart', 'Bogaert', 'Bogaerts', 'Borremans', 'Bosmans',
        'Boulanger', 'Bourgeois', 'Brasseur', 'Carlier', 'Celik', 'Ceulemans', 'Charlier', 'Christiaens', 'Claes', 'Claessens', 'Claeys', 'Collard', 'Collignon', 'Collin', 'Cools', 'Coppens',
        'Cornelis', 'Cornet', 'Cuvelier', 'Daems', 'De Backer', 'De Clercq', 'De Cock', 'De Coninck', 'De Coster', 'De Greef', 'De Groote', 'De Meyer', 'De Pauw', 'De Ridder', 'De Smedt',
        'De Smet', 'De Vos', 'De Wilde', 'De Winter', 'Declercq', 'Delfosse', 'Delhaye', 'Delvaux', 'Demir', 'Denis', 'Deprez', 'Descamps', 'Desmedt', 'Desmet', 'Dethier', 'Devos', 'Diallo',
        'Dierckx', 'Dogan', 'Dubois', 'Dumont', 'Dupont', 'El Amrani', 'Etienne', 'Evrard', 'Fontaine', 'François', 'Geerts', 'Georges', 'Gérard', 'Gielen', 'Gilles', 'Gillet', 'Gilson',
        'Goethals', 'Goffin', 'Goossens', 'Grégoire', 'Guillaume', 'Hajji', 'Hardy', 'Hendrickx', 'Henry', 'Herman', 'Hermans', 'Heylen', 'Heymans', 'Hubert', 'Jacob', 'Jacobs', 'Jacques',
        'Jacquet', 'Jansen', 'Janssen', 'Janssens', 'Kaya', 'Lacroix', 'Lambert', 'Lambrechts', 'Laurent', 'Lauwers', 'Lebrun', 'Leclercq', 'Lecocq', 'Lecomte', 'Lefebvre', 'Lefèvre', 'Legrand',
        'Lejeune', 'Lemaire', 'Lemmens', 'Lenaerts', 'Léonard', 'Leroy', 'Libert', 'Lievens', 'Louis', 'Luyten', 'Maes', 'Mahieu', 'Marchal', 'Maréchal', 'Martens', 'Martin', 'Massart', 'Masson',
        'Mathieu', 'Meert', 'Mertens', 'Messaoudi', 'Meunier', 'Michaux', 'Michel', 'Michiels', 'Moens', 'Moreau', 'Nguyen', 'Nicolas', 'Nijs', 'Noël', 'Parmentier', 'Pauwels', 'Peeters', 'Petit',
        'Pierre', 'Pieters', 'Piette', 'Piron', 'Pirotte', 'Poncelet', 'Raes', 'Remy', 'Renard', 'Robert', 'Roels', 'Roland', 'Rousseau', 'Sahin', 'Saidi', 'Schmitz', 'Segers', 'Servais', 'Simon',
        'Simons', 'Smet', 'Smets', 'Somers', 'Stevens', 'Thijs', 'Thiry', 'Thomas', 'Thys', 'Timmermans', 'Toussaint', 'Tran', 'Urbain', 'Van Acker', 'Van Damme', 'Van de Velde', 'Van den Bossche',
        'Van den Broeck', 'Van Dyck', 'Van Hecke', 'Van Hoof', 'Vandamme', 'Vandenberghe', 'Verbeeck', 'Verbeke', 'Verbruggen', 'Vercammen', 'Verhaegen', 'Verhaeghe', 'Verhelst', 'Verheyen',
        'Verhoeven', 'Verlinden', 'Vermeersch', 'Vermeiren', 'Vermeulen', 'Verschueren', 'Verstraete', 'Verstraeten', 'Vervoort', 'Wauters', 'Willems', 'Wouters', 'Wuyts', 'Yildirim', 'Yilmaz',
    ];

    protected static $titleMale = ['M.', 'Dr.', 'Pr.', 'Me.', 'Mgr'];

    protected static $titleFemale = ['Mme.', 'Mlle', 'Dr.', 'Pr.', 'Me.'];
}
