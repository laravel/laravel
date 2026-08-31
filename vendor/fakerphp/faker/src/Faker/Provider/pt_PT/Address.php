<?php

namespace Faker\Provider\pt_PT;

class Address extends \Faker\Provider\Address
{
    protected static $streetPrefix = ['Av.', 'Avenida', 'R.', 'Rua', 'Tv.', 'Travessa', 'Lg.', 'Largo'];

    protected static $streetNameFormats = [
        '{{streetPrefix}} {{lastName}}',
        '{{streetPrefix}} {{firstName}} {{lastName}}',
        '{{streetPrefix}} St. {{firstName}} {{lastName}}',
        '{{streetPrefix}} São. {{firstName}}',
        '{{streetPrefix}} de {{lastName}}',
    ];

    protected static $streetAddressFormats = [
        '{{streetName}}',
        '{{streetName}}, {{buildingNumber}}',
        '{{streetName}}, {{buildingNumber}}, {{secondaryAddress}}',
    ];

    /**
     * @see http://www.univ-ab.pt/PINTAC/carta_normas.htm address example in letters *
     */
    protected static $addressFormats = [
        '{{streetAddress}} {{postcode}} {{city}}',
    ];

    /**
     * @see http://www.mapadeportugal.net/indicecidades.asp *
     */
    private static $cities = [
        'Abrantes', 'Agualva-Cacém', 'Águeda', 'Albufeira', 'Alcácer do Sal', 'Alcobaça', 'Almada', 'Almeirim', 'Alverca do Ribatejo', 'Amadora', 'Amarante', 'Amora', 'Anadia', 'Angra do Heroísmo', 'Aveiro', 'Barcelos', 'Barreiro',
        'Beja', 'Braga', 'Bragança', 'Caldas da Rainha', 'Camara de Lobos', 'Cantanhede', 'Cartaxo', 'Castelo Branco', 'Chaves', 'Coimbra', 'Covilhã', 'Elvas', 'Entroncamento', 'Ermesinde', 'Esmoriz', 'Espinho', 'Esposende', 'Estarreja',
        'Estremoz', 'Évora', 'Fafe', 'Faro', 'Fátima', 'Felgueiras', 'Fiães', 'Figueira da Foz', 'Freamunde', 'Funchal', 'Fundão', 'Gafanha da Nazaré', 'Gondomar', 'Gouveia', 'Guarda', 'Guimarães', 'Horta', 'Ílhavo', 'Lagoa', 'Lagos', 'Lamego',
        'Leiria', 'Lisboa', 'Lixa', 'Loulé', 'Loures', 'Lourosa', 'Macedo de Cavaleiros', 'Machico', 'Maia', 'Mangualde', 'Marco de Canaveses', 'Marinha Grande',
        'Matosinhos', 'Mealhada', 'Miranda do Douro', 'Mirandela', 'Montemor-o-Novo', 'Montijo', 'Moura', 'Odivelas', 'Olhão da Restauração', 'Oliveira de Azeméis', 'Oliveira do Hospital', 'Ourém', 'Ovar', 'Paços de Ferreira',
        'Paredes', 'Penafiel', 'Peniche', 'Peso da Régua', 'Pinhel', 'Pombal', 'Ponta Delgada', 'Ponte de Sor', 'Portalegre', 'Portimão', 'Porto', 'Porto Santo', 'Póvoa de Santa Iria', 'Póvoa de Varzim', 'Quarteira', 'Queluz', 'Ribeira Grande', 'Rio Maior',
        'Rio Tinto', 'Sacavém', 'Santa Comba Dão', 'Santa Cruz', 'Santa Maria da Feira', 'Santana', 'Santarém', 'Santiago do Cacém', 'Santo Tirso',
        'São João da Madeira', 'São Mamede de Infesta', 'Seia', 'Seixal', 'Setúbal', 'Silves', 'Sines', 'Tarouca', 'Tavira', 'Tomar', 'Tondela', 'Torres Novas', 'Torres Vedras', 'Valbom', 'Vale de Cambra', 'Valongo', 'Valpaços', 'Vendas Novas',
        'Viana do Castelo', 'Vila do Conde', 'Vila Franca de Xira', 'Vila Nova de Famalicão', 'Vila Nova de Foz Côa', 'Vila Nova de Gaia', 'Vila Praia da Vitória', 'Vila Real', 'Vila Real de Santo António', 'Viseu', 'Vizela',
    ];

    protected static $postcode = [
        '%##0-###', '%##0',
        '%##1-###', '%##1',
        '%##4-###', '%##4',
        '%##5-###', '%##5',
        '%##9-###', '%##9',
    ];

    protected static $buildingNumber = ['nº %', 'nº %%', 'nº %%%', '%', '%#', '%##'];

    protected static $secondaryAddressFormats = [
        'Bloco %', 'Bl. %',
        '%º Dir.', '%#º Dir.', '%º Dr.', '%#º Dr.',
        '%º Esq.', '%#º Esq.', '%º Eq.', '%#º Eq.',
    ];

    /**
     * @example '6º Dir.'
     */
    public static function secondaryAddress()
    {
        return static::numerify(static::randomElement(static::$secondaryAddressFormats));
    }

    /**
     * @see http://www.indexmundi.com/pt/ *
     */
    protected static $country = [
        'Afeganistão', 'África do Sul', 'Albânia', 'Alemanha', 'Andorra',
        'Angola', 'Antigua e Barbuda', 'Arabia Saudita', 'Argélia',
        'Argentina', 'Armênia', 'Austrália', 'Áustria', 'Azerbaijão',
        'Bahamas', 'Bangladesh', 'Barbados', 'Barein', 'Belize', 'Benin',
        'Bielorrússia', 'Birmânia', 'Bolívia', 'Bósnia e Herzegovina',
        'Botsuana', 'Brasil', 'Brunei', 'Bulgária', 'Burkina Faso',
        'Burundi', 'Butão', 'Bélgica', 'Cabo Verde', 'Camboja', 'Camarões',
        'Canadá', 'Cazaquistão', 'Chad', 'Chile', 'China', 'Chipre',
        'Colômbia', 'Comoras', 'Congo', 'Coréia do Norte', 'Coréia do Sul',
        'Costa Rica', 'Costa do Marfim', 'Croácia', 'Cuba', 'Dinamarca',
        'Djibouti', 'Domênica', 'Equador', 'Egito', 'El Salvador',
        'Emirados Árabes Unidos', 'Eritrea', 'Eslováquia', 'Eslovênia',
        'Espanha', 'Estados Unidos da América', 'Estónia', 'Etiópia',
        'Filipinas', 'Finlândia', 'Fiji', 'França', 'Gabão', 'Gâmbia',
        'Georgia', 'Gana', 'Granada', 'Grécia', 'Guatemala',
        'Guiné Equatorial', 'Guiné Bissau', 'Guiana', 'Haiti', 'Honduras',
        'Hungria', 'Índia', 'Indonésia', 'Iraque', 'Irlanda', 'Irã',
        'Islândia', 'Ilhas Marshall', 'Ilhas Maurício', 'Ilhas Salomão',
        'Ilhas Samoa', 'Israel', 'Itália', 'Jamaica', 'Japão', 'Jordânia',
        'Kiribati', 'Kwait', 'Laos', 'Lesoto', 'Letónia', 'Libéria', 'Líbia',
        'Liechtenstein', 'Lituânia', 'Luxemburgo', 'Líbano', 'Macedónia',
        'Madagascar', 'Malásia', 'Malauí', 'Maldivas', 'Mali', 'Malta',
        'Marrocos', 'Mauritânia', 'Micronésia', 'Moldávia', 'Mongólia',
        'Montenegro', 'Moçambique', 'México', 'Mónaco', 'Namíbia', 'Nauru',
        'Nepal', 'Nicarágua', 'Nigéria', 'Noruega', 'Nova Guiné',
        'Nova Zelândia', 'Níger', 'Omã', 'Qatar', 'Quênia', 'Quirguistão',
        'Paquistão', 'Palaos', 'Panamá', 'Papua Nova Guiné', 'Paraguai',
        'Países Baixos', 'Peru', 'Polónia', 'Portugal', 'Reino Unido',
        'Reino Unido da Grã Bretanha e Irlanda do Norte',
        'República Centroafricana', 'República Checa',
        'República Democrática do Congo', 'República Dominicana', 'Ruanda',
        'Romênia', 'Rússia', 'São Cristovão e Neves', 'San Marino',
        'São Vicente e as Granadinas', 'Santa Luzia', 'São Tomé e Príncipe',
        'Senegal', 'Sérvia', 'Seychelles', 'Serra Leoa', 'Singapura', 'Síria',
        'Somália', 'Sri Lanka', 'Suazilândia', 'Sudão', 'Suécia', 'Suiça',
        'Suriname', 'Tailândia', 'Tanzânia', 'Tajiquistão', 'Timor Leste',
        'Togo', 'Tonga', 'Trinidad e Tobago', 'Turcomenistão', 'Turquia',
        'Tuvalu', 'Tunísia', 'Ucrânia', 'Uganda', 'Uruguai', 'Uzbequistão',
        'Vaticano', 'Vanuatu', 'Venezuela', 'Vietnã', 'Yemen', 'Zâmbia',
        'Zimbábue',
    ];

    /**
     * @example 'Avenida' ' Rua'
     */
    public static function streetPrefix()
    {
        return static::randomElement(static::$streetPrefix);
    }

    /**
     * @example 'Aveiro' 'Oliveira de Azeméis'
     */
    public function city()
    {
        return static::randomElement(static::$cities);
    }
}
