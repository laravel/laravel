<?php

namespace Faker\Provider\pt_BR;

require_once 'check_digit.php';

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}} {{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{firstNameMale}} {{lastName}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}} {{lastName}}',
        '{{firstNameMale}} {{lastName}} {{suffix}}',
        '{{firstNameMale}} {{firstNameMale}} {{lastName}} {{suffix}}',
        '{{firstNameMale}} {{lastName}} {{lastName}} {{suffix}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}} {{suffix}}',
        '{{titleMale}} {{firstNameMale}} {{firstNameMale}} {{lastName}} {{suffix}}',
        '{{titleMale}} {{firstNameMale}} {{lastName}} {{lastName}} {{suffix}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}} {{lastName}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}}',
        '{{titleFemale}} {{firstNameFemale}} {{firstNameFemale}} {{lastName}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}} {{suffix}}',
        '{{firstNameFemale}} {{firstNameFemale}} {{lastName}} {{suffix}}',
        '{{firstNameFemale}} {{lastName}} {{lastName}} {{suffix}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}} {{suffix}}',
        '{{titleFemale}} {{firstNameFemale}} {{firstNameFemale}} {{lastName}} {{suffix}}',
        '{{titleFemale}} {{firstNameFemale}} {{lastName}} {{lastName}} {{suffix}}',
    ];

    protected static $firstNameMale = [
        'Aaron', 'Adriano', 'Adriel', 'Afonso', 'Agostinho', 'Alan', 'Alessandro', 'Alexandre', 'Allan', 'Alonso',
        'Anderson', 'Andres', 'André', 'Antônio', 'Arthur', 'Artur', 'Augusto', 'Benedito', 'Benjamin', 'Benício',
        'Bernardo', 'Breno', 'Bruno', 'Caio', 'Camilo', 'Carlos', 'Cauan', 'Cezar', 'Christian', 'Christopher',
        'Cláudio', 'Cléber', 'Cristian', 'Cristiano', 'Cristóvão', 'César', 'Daniel', 'Danilo', 'Dante', 'Davi',
        'David', 'Deivid', 'Demian', 'Dener', 'Denis', 'Diego', 'Diogo', 'Edilson', 'Edson', 'Eduardo', 'Elias',
        'Emanuel', 'Emerson', 'Emiliano', 'Emílio', 'Enzo', 'Eric', 'Erik', 'Estêvão', 'Evandro', 'Everton', 'Fabiano',
        'Fabrício', 'Felipe', 'Fernando', 'Filipe', 'Flávio', 'Francisco', 'Franco', 'Fábio', 'Gabriel', 'Gael', 'Gean',
        'George', 'Gian', 'Gilberto', 'Giovane', 'Guilherme', 'Gustavo', 'Heitor', 'Henrique', 'Hernani', 'Horácio',
        'Hugo', 'Ian', 'Igor', 'Inácio', 'Isaac', 'Ivan', 'James', 'Jean', 'Jefferson', 'Jerônimo', 'Joaquim',
        'Joaquin', 'Jonas', 'Jorge', 'Josué', 'José', 'João', 'Juan', 'Juliano', 'Jácomo', 'Júlio', 'Kauan', 'Kevin',
        'Kléber', 'Leandro', 'Leo', 'Leonardo', 'Lorenzo', 'Luan', 'Lucas', 'Luciano', 'Lucio', 'Luis', 'Luiz',
        'Maicon', 'Manuel', 'Marcelo', 'Marco', 'Marcos', 'Martinho', 'Mateus', 'Matheus', 'Matias', 'Mauro',
        'Maurício', 'Maximiano', 'Michael', 'Miguel', 'Moisés', 'Murilo', 'Márcio', 'Mário', 'Máximo', 'Natal', 'Natan',
        'Nelson', 'Nero', 'Nicolas', 'Noel', 'Otávio', 'Pablo', 'Paulo', 'Pedro', 'Rafael', 'Raphael', 'Reinaldo',
        'Renan', 'Renato', 'Ricardo', 'Richard', 'Roberto', 'Robson', 'Rodolfo', 'Rodrigo', 'Rogério', 'Ronaldo',
        'Samuel', 'Sandro', 'Santiago', 'Saulo', 'Sebastião', 'Sergio', 'Simon', 'Simão', 'Sérgio', 'Teobaldo',
        'Thales', 'Thiago', 'Thomas', 'Théo', 'Tiago', 'Tomás', 'Téo', 'Valentin', 'Vicente', 'Victor', 'Vinícius',
        'Vitor', 'Wagner', 'Walter', 'Wellington', 'Wesley', 'William', 'Willian', 'Wilson', 'Yuri', 'Ziraldo', 'Ícaro',
        'Ítalo',
    ];

    protected static $firstNameFemale = [
        'Abgail', 'Adriana', 'Adriele', 'Agatha', 'Agustina', 'Alana', 'Alessandra', 'Alexa', 'Alice', 'Aline',
        'Allison', 'Alma', 'Alícia', 'Amanda', 'Amélia', 'Ana', 'Analu', 'Andressa', 'Andréa', 'Andréia', 'Angélica',
        'Anita', 'Antonella', 'Antonieta', 'Aparecida', 'Ariana', 'Ariane', 'Aurora', 'Ayla', 'Beatriz', 'Bella',
        'Betina', 'Bia', 'Bianca', 'Bruna', 'Bárbara', 'Camila', 'Carla', 'Carol', 'Carolina', 'Caroline', 'Catarina',
        'Cecília', 'Clara', 'Clarice', 'Cláudia', 'Constância', 'Cristiana', 'Cristina', 'Cynthia', 'Cíntia', 'Daiana',
        'Daiane', 'Daniela', 'Daniele', 'Daniella', 'Danielle', 'Dayana', 'Dayane', 'Denise', 'Diana', 'Dirce',
        'Débora', 'Eduarda', 'Elaine', 'Eliane', 'Elis', 'Elisa', 'Elizabeth', 'Ellen', 'Eloah', 'Eloá', 'Emanuelly',
        'Emilly', 'Emily', 'Emília', 'Estela', 'Ester', 'Esther', 'Eunice', 'Eva', 'Fabiana', 'Fernanda', 'Flor',
        'Flávia', 'Franciele', 'Fátima', 'Gabi', 'Gabriela', 'Gabrielle', 'Gabrielly', 'Giovana', 'Giovanna', 'Gisela',
        'Gisele', 'Graziela', 'Helena', 'Heloise', 'Heloísa', 'Hortência', 'Hosana', 'Iasmin', 'Ingrid', 'Irene',
        'Isabel', 'Isabella', 'Isabelly', 'Isadora', 'Isis', 'Ivana', 'Janaina', 'Jaqueline', 'Jasmin', 'Jennifer',
        'Joana', 'Josefina', 'Joyce', 'Juliana', 'Juliane', 'Julieta', 'Jéssica', 'Júlia', 'Kamila', 'Karen', 'Karina',
        'Karine', 'Katherine', 'Kelly', 'Ketlin', 'Késia', 'Laiane', 'Lara', 'Larissa', 'Laura', 'Lavínia', 'Laís',
        'Letícia', 'Lia', 'Lidiane', 'Lilian', 'Liz', 'Lorena', 'Louise', 'Luana', 'Luara', 'Luciana', 'Luiza', 'Luna',
        'Luzia', 'Luísa', 'Léia', 'Lívia', 'Lúcia', 'Madalena', 'Maiara', 'Maitê', 'Malena', 'Malu', 'Manoela',
        'Manuela', 'Maraisa', 'Mari', 'Maria', 'Mariah', 'Mariana', 'Marina', 'Marisa', 'Marta', 'Mary', 'Marília',
        'Maya', 'Mayara', 'Maísa', 'Mel', 'Melina', 'Melinda', 'Melissa', 'Mia', 'Micaela', 'Michele', 'Michelle',
        'Mila', 'Milena', 'Milene', 'Miranda', 'Mirela', 'Mirella', 'Miriam', 'Márcia', 'Mônica', 'Naiara', 'Naomi',
        'Nathalia', 'Natália', 'Nayara', 'Nicole', 'Noa', 'Noelí', 'Noemi', 'Norma', 'Nádia', 'Ohana', 'Olga', 'Olívia',
        'Ornela', 'Paloma', 'Paola', 'Patrícia', 'Paula', 'Paulina', 'Pietra', 'Poliana', 'Priscila', 'Pâmela',
        'Pérola', 'Rafaela', 'Raissa', 'Raquel', 'Rayane', 'Raysa', 'Rebeca', 'Regiane', 'Regina', 'Renata', 'Roberta',
        'Rosana', 'Ruth', 'Sabrina', 'Samanta', 'Samara', 'Sandra', 'Sara', 'Sarah', 'Sheila', 'Silvana', 'Simone',
        'Sofia', 'Sophia', 'Sophie', 'Stefany', 'Stella', 'Stephanie', 'Stephany', 'Suelen', 'Sueli', 'Suellen',
        'Suzana', 'Sônia', 'Tainara', 'Talita', 'Tatiana', 'Tatiane', 'Taís', 'Tessália', 'Thalia', 'Thalissa',
        'Thalita', 'Thaís', 'Tábata', 'Tâmara', 'Valentina', 'Valéria', 'Vanessa', 'Verônica', 'Violeta', 'Vitória',
        'Viviane', 'Yasmin', 'Yohanna',
    ];

    protected static $lastName = [
        'Abreu', 'Aguiar', 'Alcantara', 'Alves', 'Amaral', 'Aragão', 'Aranda', 'Arruda', 'Ávila', 'Assunção', 'Azevedo',
        'Balestero', 'Barreto', 'Barros', 'Batista', 'Beltrão', 'Benez', 'Benites', 'Bezerra', 'Bittencourt', 'Bonilha',
        'Branco', 'Brito', 'Burgos', 'Caldeira', 'Camacho', 'Campos', 'Carmona', 'Carrara', 'Carvalho', 'Casanova',
        'Cervantes', 'Chaves', 'Colaço', 'Cordeiro', 'Corona', 'Correia', 'Cortês', 'Cruz', 'D\'ávila', 'Delatorre',
        'Delgado', 'Delvalle', 'Deverso', 'Dias', 'Dominato', 'Domingues', 'Duarte', 'Escobar', 'Espinoza', 'Esteves',
        'Estrada', 'Faria', 'Faro', 'Feliciano', 'Ferminiano', 'Fernandes', 'Ferraz', 'Ferreira', 'Ferreira', 'Fidalgo',
        'Flores', 'Fonseca', 'Fontes', 'Franco', 'Furtado', 'Galhardo', 'Galindo', 'Galvão', 'Garcia', 'Gil', 'Godói',
        'Gomes', 'Gonçalves', 'Grego', 'Guerra', 'Gusmão', 'Jimenes', 'Leal', 'Leon', 'Lira', 'Lourenço', 'Lovato',
        'Lozano', 'Lutero', 'Madeira', 'Maia', 'Maldonado', 'Marin', 'Marinho', 'Marques', 'Martines', 'Marés',
        'Mascarenhas', 'Matias', 'Matos', 'Medina', 'Meireles', 'Mendes', 'Mendonça', 'Molina', 'Montenegro', 'Neves',
        'Oliveira', 'Ortega', 'Ortiz', 'Pacheco', 'Padilha', 'Padrão', 'Paes', 'Paz', 'Pedrosa', 'Pena', 'Pereira',
        'Perez', 'Pontes', 'Prado', 'Queirós', 'Queirós', 'Quintana', 'Quintana', 'Ramires', 'Ramos', 'Rangel', 'Reis',
        'Rezende', 'Rico', 'Rios', 'Rivera', 'Rocha', 'Rodrigues', 'Romero', 'Roque', 'Rosa', 'Saito', 'Salas',
        'Salazar', 'Sales', 'Salgado', 'Sanches', 'Sandoval', 'Santacruz', 'Santana', 'Santiago', 'Santos', 'Saraiva',
        'Sepúlveda', 'Serna', 'Serra', 'Serrano', 'Soares', 'Solano', 'Soto', 'Souza', 'Tamoio', 'Teles', 'Toledo',
        'Torres', 'Uchoa', 'Urias', 'Valdez', 'Vale', 'Valente', 'Valentin', 'Valência', 'Vasques', 'Vega', 'Velasques',
        'Verdara', 'Verdugo', 'Vieira', 'Vila', 'Zamana', 'Zambrano', 'Zaragoça', 'da Cruz', 'da Rosa', 'da Silva',
        'das Dores', 'das Neves', 'de Aguiar', 'de Arruda', 'de Freitas', 'de Oliveira', 'de Souza',
    ];

    protected static $titleMale = ['Sr.', 'Dr.'];

    protected static $titleFemale = ['Sra.', 'Srta.', 'Dr.'];

    protected static $suffix = ['Filho', 'Neto', 'Sobrinho', 'Jr.'];

    /**
     * @example 'Jr.'
     */
    public static function suffix()
    {
        return static::randomElement(static::$suffix);
    }

    /**
     * A random CPF number.
     *
     * @see http://en.wikipedia.org/wiki/Cadastro_de_Pessoas_F%C3%ADsicas
     *
     * @param bool $formatted If the number should have dots/dashes or not.
     *
     * @return string
     */
    public function cpf($formatted = true)
    {
        $n = $this->generator->numerify('#########');
        $n .= check_digit($n);
        $n .= check_digit($n);

        return $formatted ? vsprintf('%d%d%d.%d%d%d.%d%d%d-%d%d', str_split($n)) : $n;
    }

    /**
     * A random RG number, following Sao Paulo state's rules.
     *
     * @see http://pt.wikipedia.org/wiki/C%C3%A9dula_de_identidade
     *
     * @param bool $formatted If the number should have dots/dashes or not.
     *
     * @return string
     */
    public function rg($formatted = true)
    {
        $n = $this->generator->numerify('########');
        $n .= check_digit($n);

        return $formatted ? vsprintf('%d%d.%d%d%d.%d%d%d-%s', str_split($n)) : $n;
    }
}
