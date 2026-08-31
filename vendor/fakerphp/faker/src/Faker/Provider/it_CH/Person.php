<?php

namespace Faker\Provider\it_CH;

class Person extends \Faker\Provider\it_IT\Person
{
    /**
     * @see http://www.bfs.admin.ch/bfs/portal/de/index/themen/01/02/blank/dos/prenoms/02.html
     */
    protected static $firstNameMale = [
        'Aaron', 'Adriano', 'Alain', 'Alan', 'Alberto', 'Aldo', 'Alessandro', 'Alessio', 'Alex', 'Alexander', 'Alfredo', 'Andrea', 'Andreas', 'André', 'Angelo', 'Antonino', 'Antonio', 'Aris', 'Armando', 'Arturo', 'Athos', 'Attilio', 'Augusto', 'Aurelio',
        'Boris', 'Bruno',
        'Carlo', 'Carlos', 'Carmelo', 'Carmine', 'Cesare', 'Christian', 'Claudio', 'Corrado', 'Cristian', 'Cristiano',
        'Damiano', 'Daniel', 'Daniele', 'Danilo', 'Dante', 'Dario', 'David', 'Davide', 'Denis', 'Diego', 'Domenico', 'Donato',
        'Edoardo', 'Elia', 'Elio', 'Emanuele', 'Emilio', 'Enea', 'Enrico', 'Enzo', 'Eric', 'Ernesto', 'Eros', 'Ettore', 'Eugenio', 'Ezio',
        'Fabiano', 'Fabio', 'Fabrizio', 'Fausto', 'Federico', 'Felice', 'Fernando', 'Filippo', 'Fiorenzo', 'Flavio', 'Francesco', 'Franco', 'Fulvio',
        'Gabriel', 'Gabriele', 'Gaetano', 'Gerardo', 'Giacomo', 'Gian', 'Giancarlo', 'Gianfranco', 'Gianluca', 'Gianni', 'Gioele', 'Giona', 'Giordano', 'Giorgio', 'Giovanni', 'Giuliano', 'Giulio', 'Giuseppe', 'Graziano', 'Guido',
        'Hans',
        'Igor', 'Ivan', 'Ivano', 'Ivo',
        'Jacopo', 'Jean', 'Joel', 'Jonathan', 'José',
        'Kevin', 'Kurt',
        'Leandro', 'Leonardo', 'Liam', 'Livio', 'Lorenzo', 'Loris', 'Luca', 'Luciano', 'Lucio', 'Luigi', 'Luis',
        'Manuel', 'Marcello', 'Marco', 'Marino', 'Mario', 'Marko', 'Markus', 'Martin', 'Martino', 'Marzio', 'Massimiliano', 'Massimo', 'Matteo', 'Mattia', 'Maurizio', 'Mauro', 'Michael', 'Michel', 'Michele', 'Mirco', 'Mirko', 'Moreno',
        'Nathan', 'Nicola', 'Nicolas', 'Nicolò', 'Noah',
        'Oliver', 'Omar', 'Oscar',
        'Paolo', 'Pasquale', 'Patrick', 'Paul', 'Pedro', 'Peter', 'Pier', 'Pierluigi', 'Piero', 'Pietro',
        'Raffaele', 'Remo', 'Renato', 'Renzo', 'René', 'Reto', 'Riccardo', 'Robert', 'Roberto', 'Rocco', 'Roland', 'Rolf', 'Romano', 'Rosario', 'Ruben', 'Rudolf',
        'Sacha', 'Salvatore', 'Samuel', 'Samuele', 'Sandro', 'Sebastian', 'Sebastiano', 'Sergio', 'Silvano', 'Silvio', 'Simon', 'Simone', 'Stefan', 'Stefano',
        'Thomas', 'Tiziano', 'Tommaso',
        'Valentino', 'Valerio', 'Vincenzo', 'Vito', 'Vittorio',
        'Walter', 'Werner',
    ];

    /**
     * @see http://www.bfs.admin.ch/bfs/portal/de/index/themen/01/02/blank/dos/prenoms/02.html
     */
    protected static $firstNameFemale = [
        'Ada', 'Adele', 'Adriana', 'Agnese', 'Alessandra', 'Alessia', 'Alexandra', 'Alice', 'Aline', 'Ana', 'Andrea', 'Angela', 'Angelina', 'Anita', 'Anna', 'Annamaria', 'Antonella', 'Antonia', 'Antonietta', 'Arianna', 'Asia', 'Aurora',
        'Barbara', 'Beatrice', 'Bianca', 'Brigitte', 'Bruna',
        'Camilla', 'Carla', 'Carmela', 'Carmen', 'Carolina', 'Caterina', 'Cecilia', 'Chantal', 'Chiara', 'Christine', 'Cinzia', 'Clara', 'Claudia', 'Cristina',
        'Daniela', 'Debora', 'Deborah', 'Denise', 'Diana', 'Dolores', 'Donatella', 'Doris',
        'Elda', 'Elena', 'Eleonora', 'Eliana', 'Elisa', 'Elisabeth', 'Elisabetta', 'Elsa', 'Emanuela', 'Emilia', 'Emma', 'Enrica', 'Erica', 'Erika', 'Ester', 'Eva',
        'Fabiana', 'Federica', 'Fernanda', 'Filomena', 'Flavia', 'Franca', 'Francesca',
        'Gabriella', 'Gaia', 'Giada', 'Gianna', 'Giorgia', 'Giovanna', 'Giulia', 'Giuliana', 'Giuseppina', 'Gloria', 'Graziella', 'Greta',
        'Ida', 'Ilaria', 'Ines', 'Irene', 'Iris', 'Isabel', 'Isabella', 'Ivana',
        'Jacqueline', 'Jennifer', 'Jessica', 'Jolanda',
        'Karin', 'Katia',
        'Lara', 'Laura', 'Letizia', 'Lia', 'Lidia', 'Liliana', 'Lina', 'Linda', 'Lisa', 'Loredana', 'Lorena', 'Lorenza', 'Luana', 'Lucia', 'Luciana', 'Luisa',
        'Manuela', 'Mara', 'Margherita', 'Margrit', 'Maria', 'Mariangela', 'Marianne', 'Marie', 'Mariella', 'Marilena', 'Marina', 'Marisa', 'Marta', 'Martina', 'Matilde', 'Maura', 'Melissa', 'Michela', 'Michelle', 'Milena', 'Mirella', 'Miriam', 'Monica', 'Monika', 'Morena', 'Myriam',
        'Nadia', 'Nathalie', 'Nicole', 'Nicoletta', 'Nina', 'Nives', 'Noemi', 'Nora',
        'Olga', 'Ornella',
        'Pamela', 'Paola', 'Patricia', 'Patrizia', 'Pia', 'Pierina', 'Prisca',
        'Raffaella', 'Renata', 'Rita', 'Roberta', 'Romina', 'Rosa', 'Rosanna', 'Rosmarie', 'Ruth',
        'Sabina', 'Sabrina', 'Samantha', 'Sandra', 'Sara', 'Sarah', 'Serena', 'Silvana', 'Silvia', 'Simona', 'Sofia', 'Sonia', 'Sonja', 'Sophie', 'Stefania', 'Susanna', 'Susanne',
        'Tamara', 'Tania', 'Tatiana', 'Teresa', 'Tiziana',
        'Ursula',
        'Valentina', 'Valeria', 'Vanessa', 'Vera', 'Verena', 'Veronica', 'Virginia', 'Vittoria', 'Viviana',
        'Yvonne',
    ];

    /**
     * @see http://blog.tagesanzeiger.ch/datenblog/index.php/6859
     */
    protected static $lastName = [
        'Agustoni', 'Alberti', 'Albertini', 'Albisetti', 'Ambrosini', 'Antonini',
        'Balestra', 'Balmelli', 'Bassetti', 'Bassi', 'Baumann', 'Beffa', 'Belotti', 'Beretta', 'Bernasconi', 'Berta', 'Besomi', 'Bettosini', 'Bianchi', 'Bianda', 'Bizzozero', 'Bonetti', 'Botta', 'Bottinelli', 'Brunner', 'Butti',
        'Caccia', 'Campana', 'Camponovo', 'Candolfi', 'Canepa', 'Canonica', 'Capoferri', 'Carrara', 'Casanova', 'Cassina', 'Castelli', 'Cattaneo', 'Cavadini', 'Cavalli', 'Ceppi', 'Cereghetti', 'Cerutti', 'Chiesa', 'Colombo', 'Conti', 'Corti', 'Costa', 'Crivelli', 'Croci',
        'Delcò', 'Delmenico', 'Donati',
        'Esposito',
        'Ferrari', 'Ferrazzini', 'Ferretti', 'Filippini', 'Fischer', 'Foglia', 'Foletti', 'Fontana', 'Forni', 'Frei', 'Frey', 'Frigerio', 'Fumagalli',
        'Galfetti', 'Galli', 'Gamboni', 'Genini', 'Gerosa', 'Ghirlanda', 'Gianella', 'Gianinazzi', 'Gianini', 'Giannini', 'Gianola', 'Gilardi', 'Giovannini', 'Giudici', 'Gobbi', 'Grandi', 'Grassi', 'Grossi', 'Guerra', 'Guglielmetti', 'Guidotti',
        'Huber',
        'Jelmini',
        'Keller',
        'Lafranchi', 'Leonardi', 'Leoni', 'Lepori', 'Locatelli', 'Lombardi', 'Lombardo', 'Lorenzetti', 'Lucchini', 'Lupi', 'Lurati',
        'Maggetti', 'Maggi', 'Maggini', 'Martinelli', 'Martini', 'Maspoli', 'Mattei', 'Medici', 'Meier', 'Meroni', 'Meyer', 'Milani', 'Minotti', 'Molinari', 'Molteni', 'Mombelli', 'Monti', 'Morandi', 'Mordasini', 'Moresi', 'Moretti', 'Morisoli', 'Moro', 'Moser', 'Müller',
        'Nessi', 'Notari',
        'Ortelli',
        'Pagani', 'Pagnamenta', 'Papa', 'Pedrazzi', 'Pedrazzini', 'Pedrini', 'Pedroni', 'Peduzzi', 'Pellanda', 'Pellegrini', 'Pelloni', 'Pescia', 'Pesenti', 'Petrini', 'Piffaretti', 'Pini', 'Polli', 'Ponti', 'Ponzio', 'Poretti', 'Pozzi',
        'Quadri',
        'Realini', 'Regazzoni', 'Rezzonico', 'Rigamonti', 'Righetti', 'Rinaldi', 'Riva', 'Rizzi', 'Robbiani', 'Rodoni', 'Romano', 'Roncoroni', 'Rosselli', 'Rossetti', 'Rossi', 'Rossini', 'Rusca', 'Rusconi', 'Russo',
        'Sala', 'Sargenti', 'Sartori', 'Sassi', 'Schmid', 'Schneider', 'Scolari', 'Solari', 'Solcà', 'Soldati', 'Soldini', 'Steiner', 'Storni', 'Sulmoni', 'Suter',
        'Taddei', 'Tamagni', 'Tettamanti', 'Togni', 'Tognola',
        'Valsangiacomo', 'Vassalli', 'Villa', 'Vitali',
        'Weber', 'Widmer',
        'Zanetti', 'Zanini', 'Zimmermann',
    ];

    /**
     * Generates a valid random AVS13 (swiss social security) number
     *
     * This function acts as an alias for the function defined in the fr_CH provider.
     *
     * @see \Faker\Provider\fr_CH\Person::avs13()
     *
     * @return string
     */
    public static function avs13()
    {
        return \Faker\Provider\fr_CH\Person::avs13();
    }
}
