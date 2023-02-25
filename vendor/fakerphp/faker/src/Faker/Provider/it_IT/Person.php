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
        'Aaron', 'Abramo', 'Adriano', 'Akira', 'Alan', 'Alberto', 'Albino', 'Alessandro', 'Alessio', 'Amedeo', 'Amos', 'Anastasio', 'Anselmo',
        'Antimo', 'Antonino', 'Antonio', 'Ariel', 'Armando', 'Aroldo', 'Arturo', 'Augusto', 'Battista', 'Bernardo', 'Boris', 'Caio',
        'Carlo', 'Carmelo', 'Ciro', 'Damiano', 'Danny', 'Dante', 'Davide', 'Davis', 'Demis', 'Dimitri', 'Domingo', 'Dylan',
        'Edilio', 'Egidio', 'Elio', 'Emanuel', 'Emidio', 'Enrico', 'Enzo', 'Ercole', 'Ermes', 'Ethan', 'Ettore', 'Eusebio',
        'Fabiano', 'Fabio', 'Ferdinando', 'Fernando', 'Fiorenzo', 'Flavio', 'Folco', 'Fulvio', 'Furio', 'Gabriele', 'Gaetano', 'Gastone',
        'Gavino', 'Gerlando', 'Germano', 'Giacinto', 'Gianantonio', 'Giancarlo', 'Gianmarco', 'Gianmaria', 'Gioacchino', 'Giordano', 'Giorgio', 'Giuliano',
        'Giulio', 'Graziano', 'Gregorio', 'Guido', 'Harry', 'Hector', 'Iacopo', 'Ian', 'Ilario', 'Italo', 'Ivano', 'Jack',
        'Jacopo', 'Jari', 'Jarno', 'Joey', 'Joseph', 'Joshua', 'Kai', 'Karim', 'Kris', 'Lamberto', 'Lauro', 'Lazzaro',
        'Leonardo', 'Liborio', 'Lino', 'Lorenzo', 'Loris', 'Ludovico', 'Luigi', 'Manfredi', 'Manuele', 'Marco', 'Mariano', 'Marino',
        'Marvin', 'Marzio', 'Matteo', 'Mattia', 'Mauro', 'Max', 'Michael', 'Mirco', 'Mirko', 'Modesto', 'Moreno', 'Nabil',
        'Nadir', 'Nathan', 'Nazzareno', 'Nick', 'Nico', 'Noah', 'Noel', 'Omar', 'Oreste', 'Osvaldo', 'Pablo', 'Patrizio',
        'Pietro', 'Priamo', 'Quirino', 'Raoul', 'Renato', 'Renzo', 'Rocco', 'Rodolfo', 'Romeo', 'Romolo', 'Rudy', 'Sabatino',
        'Sabino', 'Samuel', 'Sandro', 'Santo', 'Sebastian', 'Sesto', 'Silvano', 'Silverio', 'Sirio', 'Siro', 'Timoteo', 'Timothy',
        'Tommaso', 'Ubaldo', 'Umberto', 'Vinicio', 'Walter', 'Xavier', 'Yago', 'Alighieri', 'Alighiero', 'Amerigo', 'Arcibaldo', 'Arduino',
        'Artes', 'Audenico', 'Ausonio', 'Bacchisio', 'Baldassarre', 'Bettino', 'Bortolo', 'Caligola', 'Cecco', 'Cirino', 'Cleros',
        'Costantino', 'Costanzo', 'Danthon', 'Demian', 'Domiziano', 'Edipo', 'Egisto', 'Eliziario', 'Eriberto', 'Erminio',
        'Eustachio', 'Evangelista', 'Fiorentino', 'Giacobbe', 'Gianleonardo', 'Gianriccardo', 'Giobbe', 'Ippolito',
        'Isira', 'Joannes', 'Kociss', 'Laerte', 'Maggiore', 'Muzio', 'Nestore', 'Odino', 'Odone', 'Olo', 'Oretta', 'Orfeo',
        'Osea', 'Pacifico', 'Pericle', 'Piererminio', 'Pierfrancesco', 'Piersilvio', 'Primo', 'Quarto', 'Quasimodo',
        'Radames', 'Radio', 'Raniero', 'Rosalino', 'Rosolino', 'Rufo', 'Secondo', 'Tancredi', 'Tazio', 'Terzo', 'Teseo',
        'Tolomeo',  'Trevis', 'Tristano', 'Ulrico', 'Valdo', 'Zaccaria', 'Dindo', 'Serse',
    ];

    protected static $firstNameFemale = [
        'Assia', 'Benedetta', 'Bibiana', 'Brigitta', 'Carmela', 'Celeste', 'Cira', 'Claudia', 'Concetta', 'Cristyn', 'Deborah', 'Demi', 'Diana',
        'Donatella', 'Doriana', 'Edvige', 'Elda', 'Elga', 'Elsa', 'Emilia', 'Enrica', 'Erminia', 'Evita', 'Fatima', 'Felicia',
        'Filomena', 'Fortunata', 'Gilda', 'Giovanna', 'Giulietta', 'Grazia', 'Helga', 'Ileana', 'Ingrid', 'Ione', 'Irene', 'Isabel',
        'Ivonne', 'Jelena', 'Kayla', 'Kristel', 'Laura', 'Leone', 'Lia', 'Lidia', 'Lisa', 'Loredana', 'Loretta', 'Luce',
        'Lucia', 'Lucrezia', 'Luna', 'Maika', 'Marcella', 'Maria', 'Marianita', 'Mariapia', 'Marina', 'Maristella', 'Maruska', 'Matilde',
        'Mercedes', 'Michele', 'Miriam', 'Miriana', 'Monia', 'Morgana', 'Naomi', 'Neri', 'Nicoletta', 'Ninfa', 'Noemi', 'Nunzia',
        'Olimpia', 'Ortensia', 'Penelope', 'Prisca', 'Rebecca', 'Rita', 'Rosalba', 'Rosaria', 'Rosita', 'Ruth', 'Samira', 'Sarita',
        'Sasha', 'Shaira', 'Thea', 'Ursula', 'Vania', 'Vera', 'Vienna', 'Artemide', 'Cassiopea', 'Cesidia', 'Clea', 'Cleopatra',
        'Clodovea', 'Cosetta', 'Damiana', 'Danuta', 'Diamante', 'Eufemia', 'Flaviana', 'Gelsomina', 'Genziana', 'Giacinta', 'Guendalina',
        'Jole', 'Mariagiulia', 'Marieva', 'Mietta', 'Nayade', 'Piccarda', 'Selvaggia', 'Sibilla', 'Soriana', 'Sue ellen', 'Tosca', 'Violante',
        'Vitalba', 'Zelida',
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
        'Milani', 'Pagano', 'Ruggiero', 'Sorrentino', 'D\'amico', 'Orlando', 'Damico', 'Negri',
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
