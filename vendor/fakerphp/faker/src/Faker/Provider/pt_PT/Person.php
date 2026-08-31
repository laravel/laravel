<?php

namespace Faker\Provider\pt_PT;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{lastName}} {{lastName}}',
        '{{firstNameMale}} {{lastName}} de {{lastName}}',
        '{{firstNameMale}} {{firstNameMale}} {{lastName}}',
        '{{firstNameMale}} {{firstNameMale}} de {{lastName}}',
        '{{firstNameMale}} {{firstNameMale}} {{lastName}} {{lastName}}',
        '{{firstNameMale}} {{firstNameMale}} {{lastName}} de {{lastName}}',
        '{{firstNameMale}} {{firstNameMale}} {{lastName}} {{lastName}} {{lastName}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}} {{lastName}}',
        '{{firstNameFemale}} {{lastName}} de {{lastName}}',
        '{{firstNameFemale}} {{firstNameFemale}} {{lastName}}',
        '{{firstNameFemale}} {{firstNameFemale}} de {{lastName}}',
        '{{firstNameFemale}} {{firstNameFemale}} {{lastName}} {{lastName}}',
        '{{firstNameFemale}} {{firstNameFemale}} {{lastName}} de {{lastName}}',
        '{{firstNameFemale}} {{firstNameFemale}} {{lastName}} {{lastName}} {{lastName}}',
    ];

    /**
     * @see http://goo.gl/v6bScG document with all pt abreviations *
     */
    protected static $titleMale = ['Sr.', 'Dr.', 'Exmo.', 'Eng.', 'Eng.º', 'Ex.', 'Exº'];
    protected static $titleFemale = ['Sra.', 'Dra.', 'Exma', 'Eng.ª', 'Exª'];

    protected static $firstEightDigitsFormat = [
        '1#######',
        '2#######',
        '3#######',
        '45######',
        '5#######',
        '6#######',
        '70######',
        '71######',
        '72######',
        '77######',
        '79######',
        '8#######',
        '90######',
        '98######',
        '99######',
    ];

    /**
     * Taxpayer Identification Number (NIF in Portugal)
     *
     * @see http://pt.wikipedia.org/wiki/N%C3%BAmero_de_identifica%C3%A7%C3%A3o_fiscal
     *
     * @return string 9 digit number
     */
    public static function taxpayerIdentificationNumber()
    {
        $firstEightDigits = static::numerify(static::randomElement(static::$firstEightDigitsFormat));
        $lastDigit = static::dvCalcMod11($firstEightDigits);

        return $firstEightDigits . $lastDigit;
    }

    /**
     * Generate module
     *
     * @see http://pt.wikipedia.org/wiki/D%C3%ADgito_verificador
     *
     * @param string $number number
     *
     * @return int
     */
    public static function dvCalcMod11($number)
    {
        $base = 9;
        $sum = 0;
        $factor = 2;

        for ($i = strlen($number); $i > 0; --$i) {
            $numbers[$i] = substr($number, $i - 1, 1);
            $partial[$i] = $numbers[$i] * $factor;
            $sum += $partial[$i];

            if ($factor == $base) {
                $factor = 1;
            }
            ++$factor;
        }
        $res = $sum % 11;

        if ($res == 0 || $res == 1) {
            $digit = 0;
        } else {
            $digit = 11 - $res;
        }

        return $digit;
    }

    /**
     * @see http://nomesportugueses.blogspot.pt/2012/01/lista-dos-cem-nomes-mais-usados-em.html
     */
    protected static $firstNameMale = [
        'Rodrigo', 'João', 'Martim', 'Afonso', 'Tomás', 'Gonçalo', 'Francisco', 'Tiago',
        'Diogo', 'Guilherme', 'Pedro', 'Miguel', 'Rafael', 'Gabriel', 'Santiago', 'Dinis',
        'David', 'Duarte', 'José', 'Simão', 'Daniel', 'Lucas', 'Gustavo', 'André', 'Denis',
        'Salvador', 'António', 'Vasco', 'Henrique', 'Lourenço', 'Manuel', 'Eduardo', 'Bernardo',
        'Leandro', 'Luís', 'Diego', 'Leonardo', 'Alexandre', 'Rúben', 'Mateus', 'Ricardo',
        'Vicente', 'Filipe', 'Bruno', 'Nuno', 'Carlos', 'Rui', 'Hugo', 'Samuel', 'Álvaro',
        'Matias', 'Fábio', 'Ivo', 'Paulo', 'Jorge', 'Xavier', 'Marco', 'Isaac', 'Raúl', 'Benjamim',
        'Renato', 'Artur', 'Mário', 'Frederico', 'Cristiano', 'Ivan', 'Sérgio', 'Micael',
        'Vítor', 'Edgar', 'Kevin', 'Joaquim', 'Igor', 'Ângelo', 'Enzo', 'Valentim', 'Flávio',
        'Joel', 'Fernando', 'Sebastião', 'Tomé', 'César', 'Cláudio', 'Nelson', 'Lisandro', 'Jaime',
        'Gil', 'Mauro', 'Sandro', 'Hélder', 'Matheus', 'William', 'Gaspar', 'Márcio',
        'Martinho', 'Emanuel', 'Marcos', 'Telmo', 'Davi', 'Wilson',
    ];

    protected static $firstNameFemale = [
        'Maria', 'Leonor', 'Matilde', 'Mariana', 'Ana', 'Beatriz', 'Inês', 'Lara', 'Carolina', 'Margarida',
        'Joana', 'Sofia', 'Diana', 'Francisca', 'Laura', 'Sara', 'Madalena', 'Rita', 'Mafalda', 'Catarina',
        'Luana', 'Marta', 'Íris', 'Alice', 'Bianca', 'Constança', 'Gabriela', 'Eva', 'Clara', 'Bruna', 'Daniela',
        'Iara', 'Filipa', 'Vitória', 'Ariana', 'Letícia', 'Bárbara', 'Camila', 'Rafaela', 'Carlota', 'Yara',
        'Núria', 'Raquel', 'Ema', 'Helena', 'Benedita', 'Érica', 'Isabel', 'Nicole', 'Lia', 'Alícia', 'Mara',
        'Jéssica', 'Soraia', 'Júlia', 'Luna', 'Victória', 'Luísa', 'Teresa', 'Miriam', 'Adriana', 'Melissa',
        'Andreia', 'Juliana', 'Alexandra', 'Yasmin', 'Tatiana', 'Leticia', 'Luciana', 'Eduarda', 'Cláudia',
        'Débora', 'Fabiana', 'Renata', 'Kyara', 'Kelly', 'Irina', 'Mélanie', 'Nádia', 'Cristiana', 'Liliana',
        'Patrícia', 'Vera', 'Doriana', 'Ângela', 'Mia', 'Erica', 'Mónica', 'Isabela', 'Salomé', 'Cátia',
        'Verónica', 'Violeta', 'Lorena', 'Érika', 'Vanessa', 'Iris', 'Anna', 'Viviane', 'Rebeca', 'Neuza',
    ];

    protected static $lastName = [
        'Abreu',  'Almeida',  'Alves', 'Amaral', 'Amorim', 'Andrade', 'Anjos', 'Antunes', 'Araújo', 'Assunção',
        'Azevedo', 'Baptista', 'Barbosa', 'Barros', 'Batista', 'Borges', 'Branco', 'Brito', 'Campos', 'Cardoso',
        'Carneiro', 'Carvalho', 'Castro', 'Coelho', 'Correia', 'Costa', 'Cruz', 'Cunha', 'Domingues', 'Esteves',
        'Faria', 'Fernandes', 'Ferreira', 'Figueiredo', 'Fonseca', 'Freitas', 'Garcia', 'Gaspar', 'Gomes',
        'Gonçalves', 'Guerreiro', 'Henriques', 'Jesus', 'Leal', 'Leite', 'Lima', 'Lopes', 'Loureiro', 'Lourenço',
        'Macedo', 'Machado', 'Magalhães', 'Maia', 'Marques', 'Martins', 'Matias', 'Matos', 'Melo', 'Mendes',
        'Miranda', 'Monteiro', 'Morais', 'Moreira', 'Mota', 'Moura', 'Nascimento', 'Neto', 'Neves', 'Nogueira',
        'Nunes', 'Oliveira', 'Pacheco', 'Paiva', 'Pereira', 'Pinheiro', 'Pinho', 'Pinto', 'Pires', 'Ramos',
        'Reis', 'Ribeiro', 'Rocha', 'Rodrigues', 'Santos', 'Silva', 'Simões', 'Soares', 'Sousa',
        'Sá', 'Tavares', 'Teixeira', 'Torres', 'Valente', 'Vaz', 'Vicente', 'Vieira',
    ];
}
