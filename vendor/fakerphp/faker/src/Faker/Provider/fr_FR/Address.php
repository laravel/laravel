<?php

namespace Faker\Provider\fr_FR;

class Address extends \Faker\Provider\Address
{
    protected static $citySuffix = ['Ville', 'Bourg', '-les-Bains', '-sur-Mer', '-la-Forêt', 'boeuf', 'nec', 'dan'];
    protected static $streetPrefix = ['rue', 'rue', 'chemin', 'avenue', 'boulevard', 'place', 'impasse'];
    protected static $cityFormats = [
        '{{lastName}}',
        '{{lastName}}',
        '{{lastName}}',
        '{{lastName}}',
        '{{lastName}}{{citySuffix}}',
        '{{lastName}}{{citySuffix}}',
        '{{lastName}}{{citySuffix}}',
        '{{lastName}}-sur-{{lastName}}',
    ];
    protected static $streetNameFormats = [
        '{{streetPrefix}} {{lastName}}',
        '{{streetPrefix}} {{firstName}} {{lastName}}',
        '{{streetPrefix}} de {{lastName}}',
    ];
    protected static $streetAddressFormats = [
        '{{streetName}}',
        '{{buildingNumber}}, {{streetName}}',
        '{{buildingNumber}}, {{streetName}}',
        '{{buildingNumber}}, {{streetName}}',
        '{{buildingNumber}}, {{streetName}}',
        '{{buildingNumber}}, {{streetName}}',
    ];
    protected static $addressFormats = [
        "{{streetAddress}}\n{{postcode}} {{city}}",
    ];

    protected static $buildingNumber = ['%', '%#', '%#', '%#', '%##'];

    /**
     * @see https://en.wikipedia.org/wiki/Postal_codes_in_France
     */
    protected static $postcode = ['#####'];

    protected static $country = [
        'Afghanistan', 'Afrique du sud', 'Albanie', 'Algérie', 'Allemagne', 'Andorre', 'Angola', 'Anguilla', 'Antarctique', 'Antigua et Barbuda', 'Antilles néerlandaises', 'Arabie saoudite', 'Argentine', 'Arménie', 'Aruba', 'Australie', 'Autriche', 'Azerbaïdjan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Belgique', 'Belize', 'Benin', 'Bermudes (Les)', 'Bhoutan', 'Biélorussie', 'Bolivie', 'Bosnie-Herzégovine', 'Botswana', 'Bouvet (Îles)', 'Brunei', 'Brésil', 'Bulgarie', 'Burkina Faso', 'Burundi', 'Cambodge', 'Cameroun', 'Canada', 'Cap Vert', 'Cayman (Îles)', 'Chili', 'Chine (Rép. pop.)', 'Christmas (Île)', 'Chypre', 'Cocos (Îles)', 'Colombie', 'Comores', 'Cook (Îles)', 'Corée du Nord', 'Corée, Sud', 'Costa Rica', 'Croatie', 'Cuba', 'Côte d\'Ivoire', 'Danemark', 'Djibouti', 'Dominique', 'Égypte', 'El Salvador', 'Émirats arabes unis', 'Équateur', 'Érythrée', 'Espagne', 'Estonie', 'États-Unis', 'Ethiopie', 'Falkland (Île)', 'Fidji (République des)', 'Finlande', 'France', 'Féroé (Îles)', 'Gabon',
        'Gambie', 'Ghana', 'Gibraltar', 'Grenade', 'Groenland', 'Grèce', 'Guadeloupe', 'Guam', 'Guatemala', 'Guinée', 'Guinée Equatoriale', 'Guinée-Bissau', 'Guyane', 'Guyane française', 'Géorgie', 'Géorgie du Sud et Sandwich du Sud (Îles)', 'Haïti', 'Heard et McDonald (Îles)', 'Honduras', 'Hong Kong', 'Hongrie', 'Îles Mineures Éloignées des États-Unis', 'Inde', 'Indonésie', 'Irak', 'Iran', 'Irlande', 'Islande', 'Israël', 'Italie', 'Jamaïque', 'Japon', 'Jordanie', 'Kazakhstan', 'Kenya', 'Kirghizistan', 'Kiribati', 'Koweit', 'La Barbad', 'Laos', 'Lesotho', 'Lettonie', 'Liban', 'Libye', 'Libéria', 'Liechtenstein', 'Lithuanie', 'Luxembourg', 'Macau', 'Macédoine', 'Madagascar', 'Malaisie', 'Malawi', 'Maldives (Îles)', 'Mali', 'Malte', 'Mariannes du Nord (Îles)', 'Maroc', 'Marshall (Îles)', 'Martinique', 'Maurice', 'Mauritanie', 'Mayotte', 'Mexique', 'Micronésie (États fédérés de)', 'Moldavie', 'Monaco', 'Mongolie', 'Montserrat', 'Mozambique', 'Myanmar', 'Namibie', 'Nauru', 'Nepal',
        'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Norfolk (Îles)', 'Norvège', 'Nouvelle Calédonie', 'Nouvelle-Zélande', 'Oman', 'Ouganda', 'Ouzbékistan', 'Pakistan', 'Palau', 'Panama', 'Papouasie-Nouvelle-Guinée', 'Paraguay', 'Pays-Bas', 'Philippines', 'Pitcairn (Îles)', 'Pologne', 'Polynésie française', 'Porto Rico', 'Portugal', 'Pérou', 'Qatar', 'Roumanie', 'Royaume-Uni', 'Russie', 'Rwanda', 'Rép. Dém. du Congo', 'République centrafricaine', 'République Dominicaine', 'République tchèque', 'Réunion (La)', 'Sahara Occidental', 'Saint Pierre et Miquelon', 'Saint Vincent et les Grenadines', 'Saint-Kitts et Nevis', 'Saint-Marin (Rép. de)', 'Sainte Hélène', 'Sainte Lucie', 'Samoa', 'Samoa', 'Seychelles', 'Sierra Leone', 'Singapour', 'Slovaquie', 'Slovénie', 'Somalie', 'Soudan', 'Sri Lanka', 'Suisse', 'Suriname', 'Suède', 'Svalbard et Jan Mayen (Îles)', 'Swaziland', 'Syrie', 'São Tomé et Príncipe (Rép.)', 'Sénégal', 'Tadjikistan', 'Taiwan', 'Tanzanie', 'Tchad',
        'Territoire britannique de l\'océan Indien', 'Territoires français du sud', 'Thailande', 'Timor', 'Togo', 'Tokelau', 'Tonga', 'Trinité et Tobago', 'Tunisie', 'Turkménistan', 'Turks et Caïques (Îles)', 'Turquie', 'Tuvalu', 'Ukraine', 'Uruguay', 'Vanuatu', 'Vatican (Etat du)', 'Venezuela', 'Vierges (Îles)', 'Vierges britanniques (Îles)', 'Vietnam', 'Wallis et Futuna (Îles)', 'Yemen', 'Yougoslavie', 'Zambie', 'Zaïre', 'Zimbabwe',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/Regions_of_France
     */
    private static $regions = [
        'Auvergne-Rhône-Alpes', 'Bourgogne-Franche-Comté', 'Bretagne', 'Centre-Val de Loire', 'Corse', 'Grand Est', 'Hauts-de-France',
        'Île-de-France', 'Normandie', 'Nouvelle-Aquitaine', 'Occitanie', 'Pays de la Loire', "Provence-Alpes-Côte d'Azur",
        'Guadeloupe', 'Martinique', 'Guyane', 'La Réunion', 'Mayotte',
    ];

    private static $departments = [
        ['01' => 'Ain'], ['02' => 'Aisne'], ['03' => 'Allier'], ['04' => 'Alpes-de-Haute-Provence'], ['05' => 'Hautes-Alpes'],
        ['06' => 'Alpes-Maritimes'], ['07' => 'Ardèche'], ['08' => 'Ardennes'], ['09' => 'Ariège'], ['10' => 'Aube'],
        ['11' => 'Aude'], ['12' => 'Aveyron'], ['13' => 'Bouches-du-Rhône'], ['14' => 'Calvados'], ['15' => 'Cantal'],
        ['16' => 'Charente'], ['17' => 'Charente-Maritime'], ['18' => 'Cher'], ['19' => 'Corrèze'], ['2A' => 'Corse-du-Sud'],
        ['2B' => 'Haute-Corse'], ['21' => "Côte-d'Or"], ['22' => "Côtes-d'Armor"], ['23' => 'Creuse'], ['24' => 'Dordogne'],
        ['25' => 'Doubs'], ['26' => 'Drôme'], ['27' => 'Eure'], ['28' => 'Eure-et-Loir'], ['29' => 'Finistère'], ['30' => 'Gard'],
        ['31' => 'Haute-Garonne'], ['32' => 'Gers'], ['33' => 'Gironde'], ['34' => 'Hérault'], ['35' => 'Ille-et-Vilaine'],
        ['36' => 'Indre'], ['37' => 'Indre-et-Loire'], ['38' => 'Isère'], ['39' => 'Jura'], ['40' => 'Landes'], ['41' => 'Loir-et-Cher'],
        ['42' => 'Loire'], ['43' => 'Haute-Loire'], ['44' => 'Loire-Atlantique'], ['45' => 'Loiret'], ['46' => 'Lot'],
        ['47' => 'Lot-et-Garonne'], ['48' => 'Lozère'], ['49' => 'Maine-et-Loire'], ['50' => 'Manche'], ['51' => 'Marne'],
        ['52' => 'Haute-Marne'], ['53' => 'Mayenne'], ['54' => 'Meurthe-et-Moselle'], ['55' => 'Meuse'], ['56' => 'Morbihan'],
        ['57' => 'Moselle'], ['58' => 'Nièvre'], ['59' => 'Nord'], ['60' => 'Oise'], ['61' => 'Orne'], ['62' => 'Pas-de-Calais'],
        ['63' => 'Puy-de-Dôme'], ['64' => 'Pyrénées-Atlantiques'], ['65' => 'Hautes-Pyrénées'], ['66' => 'Pyrénées-Orientales'],
        ['67' => 'Bas-Rhin'], ['68' => 'Haut-Rhin'], ['69' => 'Rhône'], ['70' => 'Haute-Saône'], ['71' => 'Saône-et-Loire'],
        ['72' => 'Sarthe'], ['73' => 'Savoie'], ['74' => 'Haute-Savoie'], ['75' => 'Paris'], ['76' => 'Seine-Maritime'],
        ['77' => 'Seine-et-Marne'], ['78' => 'Yvelines'], ['79' => 'Deux-Sèvres'], ['80' => 'Somme'], ['81' => 'Tarn'],
        ['82' => 'Tarn-et-Garonne'], ['83' => 'Var'], ['84' => 'Vaucluse'], ['85' => 'Vendée'], ['86' => 'Vienne'],
        ['87' => 'Haute-Vienne'], ['88' => 'Vosges'], ['89' => 'Yonne'], ['90' => 'Territoire de Belfort'], ['91' => 'Essonne'],
        ['92' => 'Hauts-de-Seine'], ['93' => 'Seine-Saint-Denis'], ['94' => 'Val-de-Marne'], ['95' => "Val-d'Oise"],
        ['971' => 'Guadeloupe'], ['972' => 'Martinique'], ['973' => 'Guyane'], ['974' => 'La Réunion'], ['976' => 'Mayotte'],
    ];

    protected static $secondaryAddressFormats = ['Apt. ###', 'Suite ###', 'Étage ###', 'Bât. ###', 'Chambre ###'];

    /**
     * @example 'Appt. 350'
     */
    public static function secondaryAddress()
    {
        return static::numerify(static::randomElement(static::$secondaryAddressFormats));
    }

    /**
     * @example 'rue'
     */
    public static function streetPrefix()
    {
        return static::randomElement(static::$streetPrefix);
    }

    /**
     * Randomly returns a french region.
     *
     * @example 'Guadeloupe'
     *
     * @return string
     */
    public static function region()
    {
        return static::randomElement(static::$regions);
    }

    /**
     * Randomly returns a french department ('departmentNumber' => 'departmentName').
     *
     * @example array('2B' => 'Haute-Corse')
     *
     * @return array
     */
    public static function department()
    {
        return static::randomElement(static::$departments);
    }

    /**
     * Randomly returns a french department name.
     *
     * @example 'Ardèche'
     *
     * @return string
     */
    public static function departmentName()
    {
        $randomDepartmentName = array_values(static::department());

        return $randomDepartmentName[0];
    }

    /**
     * Randomly returns a french department number.
     *
     * @example '59'
     *
     * @return string
     */
    public static function departmentNumber()
    {
        $randomDepartmentNumber = array_keys(static::department());

        return $randomDepartmentNumber[0];
    }
}
