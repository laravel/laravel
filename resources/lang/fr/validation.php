<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lignes de traduction de "Validation"
    |--------------------------------------------------------------------------
    |
    | Les lignes suivantes contiennent les messages d'erreur par défaut
    | utilisés par la classe de validation. Certaines de ces règles ont
    | plusieurs versions, comme la règle "size".
    | N'hésitez pas à personnaliser chacun des messages ci-dessous.
    |
    */

    'accepted'             => 'L\'attribut ":attribute" doit être accepté.',
    'active_url'           => 'L\'attribut ":attribute" n\'est pas une URL valide.',
    'after'                => 'L\'attribut ":attribute" doit être une date après le :date.',
    'after_or_equal'       => 'L\'attribut ":attribute" doit être le :date ou après.',
    'alpha'                => 'L\'attribut ":attribute" ne doit contenir que des lettres.',
    'alpha_dash'           => 'L\'attribut ":attribute" ne doit contenir que lettres, nombres, et tirets (-).',
    'alpha_num'            => 'L\'attribut ":attribute" ne doit contenir que lettres et nombres.',
    'array'                => 'L\'attribut ":attribute" doit être un tableau.',
    'before'               => 'L\'attribut ":attribute" doit être une date avant le :date.',
    'before_or_equal'      => 'L\'attribut ":attribute" doit être le :date ou avant.',
    'between'              => [
        'numeric' => 'L\'attribut ":attribute" doit être entre :min et :max.',
        'file'    => 'L\'attribut ":attribute" doit peser entre :min et :max kilo-octets.',
        'string'  => 'L\'attribut ":attribute" doit avoir :min et :max caractères.',
        'array'   => 'L\'attribut ":attribute" doit avoir entre :min et :max éléments.',
    ],
    'boolean'              => 'L\'attribut ":attribute" doit valoir "true" ou "false".',
    'confirmed'            => 'La confirmation de l\'attribut ":attribute" ne correspond pas.',
    'date'                 => 'L\'attribut ":attribute" n\'est pas une date valide.',
    'date_format'          => 'L\'attribut ":attribute" ne correspond pas au format :format.',
    'different'            => 'Les attributs ":attribute" et ":other" doivent être differents.',
    'digits'               => 'L\'attribut ":attribute" doit avoir :digits chiffre(s).',
    'digits_between'       => 'L\'attribut ":attribute" doit avoir entre :min et :max chiffres.',
    'dimensions'           => 'L\'image ":attribute" n\'est pas aux dimensions valides.',
    'distinct'             => 'L\'attribut ":attribute" existe déjà.',
    'email'                => 'L\'attribut ":attribute" doit être une adresse e-mail valide.',
    'exists'               => 'L\'attribut ":attribute" n\'est pas valide.',
    'file'                 => 'L\'attribut ":attribute" doit être un fichier.',
    'filled'               => 'L\'attribut ":attribute" est requis.',
    'gt'                   => [
        'numeric' => 'L\'attribut ":attribute" doit être supérieur à :value.',
        'file'    => 'L\'attribut ":attribute" doit peser plus de :value kilo-octets.',
        'string'  => 'L\'attribut ":attribute" doit faire plus que :value caractères.',
        'array'   => 'L\'attribut ":attribute" doit avoir plus de :value éléments.',
    ],
    'gte'                  => [
        'numeric' => 'L\'attribut ":attribute" doit être supérieur ou égal à :value.',
        'file'    => 'L\'attribut ":attribute" doit être peser au moins :value kilo-octets.',
        'string'  => 'L\'attribut ":attribute" doit faire au moins :value caractères.',
        'array'   => 'L\'attribut ":attribute" doit avoir au moins :value éléments.',
    ],
    'image'                => 'L\'attribut ":attribute" doit être une image.',
    'in'                   => 'L\'attribut :attribute n\'est pas valide.',
    'in_array'             => 'L\'attribut ":attribute" n\'existe pas dans ":other".',
    'integer'              => 'L\'attribut ":attribute" doit être un entier.',
    'ip'                   => 'L\'attribut ":attribute" doit être une adresse IP valide.',
    'ipv4'                 => 'L\'attribut ":attribute" doit être une adresse IPv4 valide.',
    'ipv6'                 => 'L\'attribut ":attribute" doit être une adresse IPv6 valide.',
    'json'                 => 'L\'attribut ":attribute" doit être une chaine JSON valide.',
    'lt'                   => [
        'numeric' => 'L\'attribut ":attribute" doit être inférieur à :value.',
        'file'    => 'L\'attribut ":attribute" doit peser moins de :value kilo-octets.',
        'string'  => 'L\'attribut ":attribute" doit faire moins que :value caractères.',
        'array'   => 'L\'attribut ":attribute" doit avoir moins de :value éléments.',
    ],
    'lte'                  => [
        'numeric' => 'L\'attribut ":attribute" doit être inférieur ou égal à :value.',
        'file'    => 'L\'attribut ":attribute" doit être peser au plus :value kilo-octets.',
        'string'  => 'L\'attribut ":attribute" doit faire au plus :value caractères.',
        'array'   => 'L\'attribut ":attribute" doit avoir au plus :value éléments.',
    ],
    'max'                  => [
        'numeric' => 'L\'attribut ":attribute" ne doit pas être plus grand que :max.',
        'file'    => 'L\'attribut ":attribute" ne doit pas peser plus que :max kilo-octets.',
        'string'  => 'L\'attribut ":attribute" ne doit pas dépasser :max caractères.',
        'array'   => 'L\'attribut ":attribute" ne doit pas contenir plus de :max éléments.',
    ],
    'mimes'                => 'L\'attribut ":attribute" doit être un fichier de type: :values.',
    'mimetypes'            => 'L\'attribut ":attribute" doit être un fichier de type: :values.',
    'min'                  => [
        'numeric' => 'L\'attribut ":attribute" doit être plus petit que :min.',
        'file'    => 'L\'attribut ":attribute" doit peser au moins :min kilo-octets.',
        'string'  => 'L\'attribut ":attribute" doit faire au moins :min caractères.',
        'array'   => 'L\'attribut ":attribute" doit avoir au moins :min éléments.',
    ],
    'not_in'               => 'L\'attribut ":attribute" n\'est pas valide.',
    'not_regex'            => 'Le format de l\'attribut ":attribute" n\'est pas valide.',
    'numeric'              => 'L\'attribut ":attribute" doit être un nombre.',
    'present'              => 'L\'attribut ":attribute" doit être present.',
    'regex'                => 'Le format de l\'attribut ":attribute" n\'est pas valide.',
    'required'             => 'L\'attribut ":attribute" est requis.',
    'required_if'          => 'L\'attribut ":attribute" est requis si ":other" vaut ":value".',
    'required_unless'      => 'L\'attribut ":attribute" est requis sauf si ":other" vaut :values.',
    'required_with'        => 'L\'attribut ":attribute" est requis si ":values" est présent.',
    'required_with_all'    => 'L\'attribut ":attribute" est requis si ":values" est présent.',
    'required_without'     => 'L\'attribut ":attribute" est requis si ":values" n\'est pas présent.',
    'required_without_all' => 'L\'attribut ":attribute" est requis si aucun de ":values" n\'est présent.',
    'same'                 => 'Les attributs ":attribute" et ":other" doivent correspondre.',
    'size'                 => [
        'numeric' => 'L\'attribut ":attribute" doit être :size.',
        'file'    => 'L\'attribut ":attribute" doit peser :size kilo-octets.',
        'string'  => 'L\'attribut ":attribute" doit faire :size caractères.',
        'array'   => 'L\'attribut ":attribute" doit contenir :size éléments.',
    ],
    'string'               => 'L\'attribut ":attribute" doit être une chaine de caractères.',
    'timezone'             => 'L\'attribut ":attribute" doit être un fuseau horaire valide.',
    'unique'               => 'L\'attribut ":attribute" existe déjà.',
    'uploaded'             => 'Le fichier ":attribute" n\'a pas pu être téléversé.',
    'url'                  => 'Le format de l\'attribut ":attribute" n\'est pas valide.',

    /*
    |--------------------------------------------------------------------------
    | Lignes de traduction personnalisées de "Validation"
    |--------------------------------------------------------------------------
    |
    | Ici, vous pouvez spécifier des messages de validation personnalisés
    | utilisant la convention "attribute.rule" pour nommer la ligne.
    | Cela permet d'indiquer rapidement une ligne spécifique pour
    | une règle de validation donnée.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Attributs personnalisés de "Validation"
    |--------------------------------------------------------------------------
    |
    | Les lignes suivantes sont utilisées pour remplacer les textes d'exemple
    | par quelque chose de plus compréhensible, comme "Adresse e-mail" au lieu
    | de "email". Ça nous aide à rendre les messages plus clairs.
    |
    */

    'attributes' => [],

];
