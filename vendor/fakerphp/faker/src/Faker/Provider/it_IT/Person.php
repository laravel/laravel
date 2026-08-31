<?php

namespace Faker\Provider\it_IT;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}}',
    ];

    protected static $firstNameMale = [
        'Aaron', 'Abramo', 'Adriano', 'Agostino', 'Akira', 'Alan', 'Alberto', 'Albino', 'Aldo', 'Alessandro', 'Alessio',
        'Alfonso', 'Alfredo', 'Alighieri', 'Alighiero', 'Amedeo', 'Amerigo', 'Amos', 'Anastasio', 'Andrea', 'Angelo',
        'Anselmo', 'Antimo', 'Antonino', 'Antonio', 'Arcibaldo', 'Arduino', 'Ariel', 'Armando', 'Aroldo', 'Artes', 'Arturo',
        'Audenico', 'Augusto', 'Ausonio', 'Bacchisio', 'Baldassarre', 'Battista', 'Bernardo', 'Bettino', 'Boris', 'Bortolo',
        'Bruno', 'Caio', 'Caligola', 'Carlo', 'Carmelo', 'Carmine', 'Cecco', 'Cesare', 'Cirino', 'Ciro', 'Claudio', 'Cleros',
        'Corrado', 'Cosimo', 'Costantino', 'Costanzo', 'Damiano', 'Danilo', 'Danny', 'Dante', 'Danthon', 'Dario', 'David',
        'Davide', 'Davis', 'Demian', 'Demis', 'Dimitri', 'Dindo', 'Dino', 'Domenico', 'Domingo', 'Domiziano', 'Donato', 'Dylan',
        'Edilio', 'Edipo', 'Egidio', 'Egisto', 'Elio', 'Eliziario', 'Emanuel', 'Emanuele', 'Emidio', 'Emilio', 'Enrico', 'Enzo',
        'Ercole', 'Eriberto', 'Ermes', 'Erminio', 'Ernesto', 'Ethan', 'Ettore', 'Eugenio', 'Eusebio', 'Eustachio', 'Evangelista',
        'Fabiano', 'Fabio', 'Fabrizio', 'Fausto', 'Federico', 'Felice', 'Ferdinando', 'Fernando', 'Filippo', 'Fiorentino',
        'Fiorenzo', 'Flavio', 'Folco', 'Francesco', 'Franco', 'Fulvio', 'Furio', 'Gabriele', 'Gaetano', 'Gastone', 'Gavino',
        'Gennaro', 'Gerardo', 'Gerlando', 'Germano', 'Giacinto', 'Giacobbe', 'Giacomo', 'Gian', 'Gianantonio', 'Giancarlo',
        'Gianfranco', 'Gianleonardo', 'Gianluca', 'Gianmarco', 'Gianmaria', 'Gianni', 'Gianriccardo', 'Gino', 'Gioacchino', 'Giobbe',
        'Giordano', 'Giorgio', 'Giovanni', 'Giuliano', 'Giulio', 'Giuseppe', 'Graziano', 'Gregorio', 'Guido', 'Harry', 'Hector',
        'Iacopo', 'Ian', 'Ilario', 'Ippolito', 'Isira', 'Italo', 'Ivano', 'Jack', 'Jacopo', 'Jari', 'Jarno', 'Joannes', 'Joey',
        'Joseph', 'Joshua', 'Kai', 'Karim', 'Kociss', 'Kris', 'Laerte', 'Lamberto', 'Lauro', 'Lazzaro', 'Leonardo', 'Liborio',
        'Lino', 'Lorenzo', 'Loris', 'Luca', 'Luciano', 'Ludovico', 'Luigi', 'Maggiore', 'Manfredi', 'Manuele', 'Marcello',
        'Marco', 'Mariano', 'Marino', 'Mario', 'Marvin', 'Marzio', 'Massimiliano', 'Massimo', 'Matteo', 'Mattia', 'Maurizio',
        'Mauro', 'Max', 'Michael', 'Mirco', 'Mirko', 'Modesto', 'Moreno', 'Muzio', 'Nabil', 'Nadir', 'Nathan', 'Nazzareno',
        'Nestore', 'Nick', 'Nico', 'Nicola', 'Noah', 'Noel', 'Odino', 'Odone', 'Olo', 'Omar', 'Oreste', 'Oretta', 'Orfeo',
        'Osea', 'Osvaldo', 'Pablo', 'Pacifico', 'Paolo', 'Pasquale', 'Patrizio', 'Pericle', 'Piererminio', 'Pierfrancesco',
        'Piero', 'Piersilvio', 'Pietro', 'Priamo', 'Primo', 'Quarto', 'Quasimodo', 'Quirino', 'Radames', 'Radio', 'Raffaele',
        'Raniero', 'Raoul', 'Renato', 'Renzo', 'Riccardo', 'Roberto', 'Rocco', 'Rodolfo', 'Romano', 'Romeo', 'Romolo',
        'Rosalino', 'Rosolino', 'Rudy', 'Rufo', 'Sabatino', 'Sabino', 'Salvatore', 'Samuel', 'Sandro', 'Santo', 'Sebastian',
        'Sebastiano', 'Secondo', 'Sergio', 'Serse', 'Sesto', 'Silvano', 'Silverio', 'Silvio', 'Sirio', 'Siro', 'Stefano',
        'Tancredi', 'Tazio', 'Terzo', 'Teseo', 'Timoteo', 'Timothy', 'Tolomeo', 'Tommaso', 'Trevis', 'Tristano', 'Ubaldo',
        'Ugo', 'Ulrico', 'Umberto', 'Valdo', 'Vincenzo', 'Vinicio', 'Vito', 'Vittorio', 'Walter', 'Xavier', 'Yago', 'Zaccaria',
    ];

    protected static $firstNameFemale = [
        'Adriana', 'Alessandra', 'Andrea', 'Angela', 'Angelina', 'Anna', 'Annalisa', 'Annamaria', 'Annunziata', 'Antonella',
        'Antonia', 'Antonietta', 'Antonina', 'Artemide', 'Asia', 'Assia', 'Assunta', 'Barbara', 'Benedetta', 'Bibiana', 'Brigitta',
        'Bruna', 'Carla', 'Carmela', 'Cassiopea', 'Caterina', 'Celeste', 'Cesidia', 'Chiara', 'Cinzia', 'Cira', 'Clara', 'Claudia',
        'Clea', 'Cleopatra', 'Clodovea', 'Concetta', 'Cosetta', 'Cristina', 'Cristyn', 'Damiana', 'Daniela', 'Danuta', 'Deborah',
        'Demi', 'Diamante', 'Diana', 'Domenica', 'Donatella', 'Doriana', 'Edvige', 'Elda', 'Elena', 'Eleonora', 'Elga', 'Elisa',
        'Elisabetta', 'Elsa', 'Emanuela', 'Emilia', 'Enrica', 'Erminia', 'Eufemia', 'Evita', 'Fatima', 'Federica', 'Felicia',
        'Filomena', 'Flaviana', 'Fortunata', 'Franca', 'Francesca', 'Gabriella', 'Gelsomina', 'Genziana', 'Giacinta', 'Gilda',
        'Giovanna', 'Giuliana', 'Giulietta', 'Giuseppa', 'Giuseppina', 'Grazia', 'Graziella', 'Guendalina', 'Helga', 'Ida', 'Ileana',
        'Ingrid', 'Ione', 'Irene', 'Isabel', 'Isabella', 'Ivana', 'Ivonne', 'Jelena', 'Jole', 'Kayla', 'Kristel', 'Laura', 'Leone',
        'Lia', 'Lidia', 'Liliana', 'Lina', 'Lisa', 'Loredana', 'Loretta', 'Luce', 'Lucia', 'Luciana', 'Lucrezia', 'Luisa', 'Luna',
        'Maddalena', 'Maika', 'Manuela', 'Marcella', 'Margherita', 'Maria', 'Mariagiulia', 'Marianita', 'Marianna', 'Mariapia',
        'Marieva', 'Marina', 'Marisa', 'Maristella', 'Marta', 'Maruska', 'Matilde', 'Mercedes', 'Michela', 'Michele', 'Michelle',
        'Mietta', 'Mirella', 'Miriam', 'Miriana', 'Monia', 'Monica', 'Morgana', 'Nadia', 'Naomi', 'Nayade', 'Neri', 'Nicoletta',
        'Ninfa', 'Noemi', 'Nunzia', 'Olimpia', 'Ortensia', 'Paola', 'Patrizia', 'Penelope', 'Piccarda', 'Pierina', 'Prisca',
        'Raffaella', 'Rebecca', 'Renata', 'Rita', 'Roberta', 'Rosa', 'Rosalba', 'Rosalia', 'Rosanna', 'Rosaria', 'Rosita',
        'Ruth', 'Sabrina', 'Samira', 'Sandra', 'Sara', 'Sarah', 'Sarita', 'Sasha', 'Selvaggia', 'Shaira', 'Sibilla', 'Silvana',
        'Silvia', 'Simona', 'Sonia', 'Soriana', 'Stefania', 'Stella', 'Sue ellen', 'Teresa', 'Thea', 'Tiziana', 'Tosca',
        'Ursula', 'Valentina', 'Vania', 'Vera', 'Veronica', 'Vienna', 'Vincenza', 'Violante', 'Vitalba', 'Vittoria', 'Zelida',
    ];

    protected static $lastName = [
        'Rossi', 'Russo', 'Ferrari', 'Esposito', 'Bianchi', 'Romano', 'Colombo', 'Ricci', 'Marino', 'Greco', 'Bruno', 'Gallo', 'Conti',
        'De luca', 'Mancini', 'Costa', 'Giordano', 'Rizzo', 'Lombardi', 'Moretti', 'Barbieri', 'Fontana', 'Santoro', 'Mariani',
        'Rinaldi', 'Caruso', 'Ferrara', 'Galli', 'Martini', 'Leone', 'Longo', 'Gentile', 'Martinelli', 'Vitale', 'Lombardo', 'Serra',
        'Coppola', 'De Santis', 'D\'angelo', 'Marchetti', 'Parisi', 'Villa', 'Conte', 'Ferraro', 'Ferri', 'Fabbri', 'Bianco',
        'Marini', 'Grasso', 'Valentini', 'Messina', 'Sala', 'De Angelis', 'Gatti', 'Pellegrini', 'Palumbo', 'Sanna', 'Farina',
        'Rizzi', 'Monti', 'Cattaneo', 'Morelli', 'Amato', 'Silvestri', 'Mazza', 'Testa', 'Grassi', 'Pellegrino', 'Carbone',
        'Giuliani', 'Benedetti', 'Barone', 'Rossetti', 'Caputo', 'Montanari', 'Guerra', 'Palmieri', 'Bernardi', 'Martino', 'Fiore',
        'De rosa', 'Ferretti', 'Bellini', 'Basile', 'Riva', 'Donati', 'Piras', 'Vitali', 'Battaglia', 'Sartori', 'Neri', 'Costantini',
        'Milani', 'Pagano', 'Ruggiero', 'Sorrentino', 'D\'amico', 'Orlando', 'Damico', 'Negri', 'Verdi',
    ];

    protected static $titleMale = ['Sig.', 'Dott.', 'Dr.', 'Ing.'];

    protected static $titleFemale = ['Sig.ra', 'Dott.', 'Dr.', 'Ing.'];

    private static $suffix = [];

    /**
     * @example 'PhD'
     */
    public static function suffix()
    {
        return static::randomElement(static::$suffix);
    }

    /**
     * TaxCode (CodiceFiscale)
     *
     * @see https://it.wikipedia.org/wiki/Codice_fiscale
     *
     * @return string
     */
    public static function taxId()
    {
        return strtoupper(static::bothify('??????##?##?###?'));
    }
}
