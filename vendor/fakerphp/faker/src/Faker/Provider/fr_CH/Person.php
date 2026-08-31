<?php

namespace Faker\Provider\fr_CH;

class Person extends \Faker\Provider\fr_FR\Person
{
    /**
     * @see http://www.bfs.admin.ch/bfs/portal/de/index/themen/01/02/blank/dos/prenoms/02.html
     */
    protected static $firstNameMale = [
        'Adrian', 'Adrien', 'Alain', 'Albert', 'Alberto', 'Alessandro', 'Alex', 'Alexander', 'Alexandre', 'Alexis', 'Alfred', 'Ali', 'Andrea', 'André', 'Angelo', 'Anthony', 'Antoine', 'Antonio', 'António', 'Arnaud', 'Arthur', 'Aurélien', 'Axel',
        'Baptiste', 'Bastien', 'Benjamin', 'Benoît', 'Bernard', 'Bertrand', 'Bruno', 'Bryan',
        'Carlos', 'Charles', 'Christian', 'Christophe', 'Christopher', 'Claude', 'Claudio', 'Cyril', 'Cédric',
        'Damien', 'Daniel', 'David', 'Denis', 'Didier', 'Diego', 'Diogo', 'Dominique', 'Dylan',
        'Emmanuel', 'Enzo', 'Eric', 'Etienne',
        'Fabien', 'Fabio', 'Fabrice', 'Fernando', 'Filipe', 'Florian', 'Francesco', 'Francis', 'Francisco', 'François', 'Frédéric',
        'Gabriel', 'Georges', 'Gilbert', 'Gilles', 'Giovanni', 'Giuseppe', 'Gregory', 'Grégoire', 'Grégory', 'Guillaume', 'Guy', 'Gérald', 'Gérard',
        'Hans', 'Henri', 'Hervé', 'Hugo',
        'Jacques', 'Jean', 'Jean-Claude', 'Jean-Daniel', 'Jean-François', 'Jean-Jacques', 'Jean-Louis', 'Jean-Luc', 'Jean-Marc', 'Jean-Marie', 'Jean-Michel', 'Jean-Paul', 'Jean-Pierre', 'Joao', 'Joaquim', 'John', 'Jonas', 'Jonathan', 'Jorge', 'Jose', 'Joseph', 'José', 'João', 'Joël', 'Juan', 'Julien', 'Jérémie', 'Jérémy', 'Jérôme',
        'Kevin',
        'Laurent', 'Lionel', 'Loris', 'Louis', 'Loïc', 'Luc', 'Luca', 'Lucas', 'Lucien', 'Ludovic', 'Luis', 'Léo',
        'Manuel', 'Marc', 'Marcel', 'Marco', 'Mario', 'Martin', 'Mathias', 'Mathieu', 'Matteo', 'Matthieu', 'Maurice', 'Max', 'Maxime', 'Michael', 'Michaël', 'Michel', 'Miguel', 'Mohamed',
        'Nathan', 'Nicolas', 'Noah', 'Nolan', 'Nuno',
        'Olivier',
        'Pascal', 'Patrice', 'Patrick', 'Paul', 'Paulo', 'Pedro', 'Peter', 'Philippe', 'Pierre', 'Pierre-Alain', 'Pierre-André',
        'Quentin',
        'Rafael', 'Raphaël', 'Raymond', 'René', 'Ricardo', 'Richard', 'Robert', 'Roberto', 'Robin', 'Roger', 'Roland', 'Romain', 'Rui', 'Rémy',
        'Sacha', 'Salvatore', 'Samuel', 'Serge', 'Sergio', 'Simon', 'Steve', 'Stéphane', 'Sylvain', 'Sébastien',
        'Thierry', 'Thomas', 'Théo', 'Tiago',
        'Valentin', 'Victor', 'Vincent', 'Vitor',
        'Walter', 'William', 'Willy',
        'Xavier',
        'Yann', 'Yannick', 'Yvan', 'Yves',
    ];

    /**
     * @see http://www.bfs.admin.ch/bfs/portal/de/index/themen/01/02/blank/dos/prenoms/02.html
     */
    protected static $firstNameFemale = [
        'Agnès', 'Alexandra', 'Alice', 'Alicia', 'Aline', 'Amélie', 'Ana', 'Anaïs', 'Andrea', 'Andrée', 'Angela', 'Anita', 'Anna', 'Anne', 'Anne-Marie', 'Antoinette', 'Ariane', 'Arlette', 'Audrey', 'Aurélie',
        'Barbara', 'Bernadette', 'Brigitte', 'Béatrice',
        'Camille', 'Carine', 'Carla', 'Carmen', 'Carole', 'Caroline', 'Catherine', 'Chantal', 'Charlotte', 'Chloé', 'Christelle', 'Christiane', 'Christine', 'Cindy', 'Claire', 'Clara', 'Claudia', 'Claudine', 'Colette', 'Coralie', 'Corinne', 'Cristina', 'Cécile', 'Célia', 'Céline',
        'Daniela', 'Danielle', 'Danièle', 'Delphine', 'Denise', 'Diana', 'Dominique',
        'Edith', 'Elena', 'Eliane', 'Elisa', 'Elisabeth', 'Elodie', 'Elsa', 'Emilie', 'Emma', 'Erika', 'Estelle', 'Esther', 'Eva', 'Evelyne',
        'Fabienne', 'Fanny', 'Florence', 'Francine', 'Françoise',
        'Gabrielle', 'Geneviève', 'Georgette', 'Ginette', 'Gisèle', 'Géraldine',
        'Huguette', 'Hélène',
        'Inès', 'Irène', 'Isabel', 'Isabelle',
        'Jacqueline', 'Janine', 'Jeanne', 'Jeannine', 'Jennifer', 'Jessica', 'Joana', 'Jocelyne', 'Josette', 'Josiane', 'Joëlle', 'Julia', 'Julie', 'Juliette', 'Justine',
        'Karin', 'Karine', 'Katia',
        'Laetitia', 'Lara', 'Laura', 'Laure', 'Laurence', 'Liliane', 'Lisa', 'Louise', 'Lucia', 'Lucie', 'Léa',
        'Madeleine', 'Magali', 'Manon', 'Manuela', 'Marguerite', 'Maria', 'Marianne', 'Marie', 'Marie-Thérèse', 'Marina', 'Marine', 'Marion', 'Marlyse', 'Marlène', 'Martine', 'Mathilde', 'Melissa', 'Micheline', 'Michelle', 'Michèle', 'Mireille', 'Monica', 'Monique', 'Morgane', 'Muriel', 'Myriam', 'Mélanie',
        'Nadia', 'Nadine', 'Natacha', 'Nathalie', 'Nelly', 'Nicole', 'Nina', 'Noémie',
        'Océane', 'Olga', 'Olivia',
        'Pascale', 'Patricia', 'Paula', 'Pauline', 'Pierrette',
        'Rachel', 'Raymonde', 'Renée', 'Rita', 'Rosa', 'Rose', 'Rose-Marie', 'Ruth',
        'Sabine', 'Sabrina', 'Sandra', 'Sandrine', 'Sara', 'Sarah', 'Silvia', 'Simone', 'Sofia', 'Sonia', 'Sophie', 'Stéphanie', 'Suzanne', 'Sylvia', 'Sylviane', 'Sylvie', 'Séverine',
        'Tania', 'Tatiana', 'Teresa', 'Thérèse',
        'Valentine', 'Valérie', 'Vanessa', 'Victoria', 'Virginie', 'Viviane', 'Véronique',
        'Yolande', 'Yvette', 'Yvonne',
        'Zoé',
    ];

    /**
     * @see http://blog.tagesanzeiger.ch/datenblog/index.php/6859
     */
    protected static $lastName = [
        'Aebischer', 'Aeby', 'Andrey', 'Aubert', 'Aubry',
        'Bachmann', 'Baechler', 'Baeriswyl', 'Barbey', 'Barras', 'Baumann', 'Baumgartner', 'Berger', 'Bernard', 'Berset', 'Bersier', 'Berthoud', 'Besson', 'Blanc', 'Blaser', 'Boillat', 'Bonvin', 'Bourquin', 'Bruchez', 'Brunner', 'Brügger', 'Buchs', 'Bugnon', 'Burri', 'Bühler',
        'Castella', 'Cattin', 'Chappuis', 'Chapuis', 'Chassot', 'Chatelain', 'Chevalley', 'Chollet', 'Christen', 'Clerc', 'Clément', 'Constantin', 'Crausaz',
        'Da Silva', 'Darbellay', 'Demierre', 'dos Santos', 'Droz', 'Dubois', 'Dubuis', 'Duc', 'Dévaud',
        'Egger', 'Emery',
        'Fasel', 'Favre', 'Fellay', 'Fernandes', 'Fernandez', 'Ferreira', 'Fischer', 'Fleury', 'Flückiger', 'Fournier', 'Fragnière', 'Froidevaux',
        'Gaillard', 'Garcia', 'Gasser', 'Gay', 'Geiser', 'Genoud', 'Gerber', 'Gilliéron', 'Girard', 'Girardin', 'Giroud', 'Glauser', 'Golay', 'Gonzalez', 'Graf', 'Grand', 'Grandjean', 'Gremaud', 'Grosjean', 'Gross', 'Guex', 'Guignard',
        'Hofer', 'Hofmann', 'Huber', 'Huguenin', 'Héritier',
        'Jaccard', 'Jacot', 'Jaquet', 'Jaquier', 'Jeanneret', 'Jordan', 'Jungo', 'Junod',
        'Kaufmann', 'Keller', 'Kohler', 'Kolly', 'Kunz',
        'Lachat', 'Lambert', 'Lehmann', 'Leuba', 'Leuenberger', 'Liechti', 'Lopez', 'Lüthi',
        'Maeder', 'Magnin', 'Maillard', 'Maret', 'Marti', 'Martin', 'Martinez', 'Matthey', 'Maurer', 'Mauron', 'Mayor', 'Meier', 'Meyer', 'Meylan', 'Michaud', 'Michel', 'Monnet', 'Monney', 'Monnier', 'Morand', 'Morard', 'Morel', 'Moret', 'Moser', 'Muller', 'Müller',
        'Neuhaus', 'Nguyen', 'Nicolet',
        'Oberson',
        'Pache', 'Pasche', 'Pasquier', 'Pereira', 'Perez', 'Perrenoud', 'Perret', 'Perrin', 'Perroud', 'Pfister', 'Piguet', 'Piller', 'Pilloud', 'Pittet', 'Pochon',
        'Racine', 'Rey', 'Reymond', 'Richard', 'Robert', 'Rochat', 'Rodrigues', 'Rodriguez', 'Roduit', 'Rosset', 'Rossier', 'Roth', 'Rouiller', 'Roulin', 'Roy', 'Ruffieux',
        'Savary', 'Schaller', 'Schmid', 'Schmidt', 'Schmutz', 'Schneider', 'Schwab', 'Seydoux', 'Simon', 'Stalder', 'Stauffer', 'Steiner', 'Studer', 'Suter',
        'Tissot',
        'Vaucher', 'Vonlanthen', 'Vuilleumier',
        'Waeber', 'Weber', 'Wenger', 'Widmer', 'Wyss',
        'Zbinden', 'Zimmermann',
    ];

    /**
     * Generates a valid random AVS13 (swiss social security) number
     *
     * This function will generate a valid random AVS13 number and return it
     * as a formatted string.
     *
     * @see https://www.zas.admin.ch/zas/fr/home/partenaires-et-institutions-/navs13.html
     *
     * @return string
     */
    public static function avs13()
    {
        $p = [
            756,
            self::numerify('####'),
            self::numerify('####'),
            self::numerify('#'),
        ];

        $checksum = \Faker\Calculator\Ean::checksum(implode('', $p));

        return sprintf('%s.%s.%s.%s%s', $p[0], $p[1], $p[2], $p[3], $checksum);
    }
}
