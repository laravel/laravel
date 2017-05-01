<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute muss akzeptiert werden.',
    'active_url'           => ':attribute ist keine valide URL.',
    'after'                => ':attribute muss ein Datum nach :date sein.',
    'alpha'                => ':attribute sollte nur Buchstaben enthalten.',
    'alpha_dash'           => ':attribute sollte nur Buchstaben, Zahlen und Striche enthalten.',
    'alpha_num'            => ':attribute sollte nur Buchstaben und Zahlen enthalten.',
    'array'                => ':attribute muss ein Array sein.',
    'before'               => ':attribute muss ein Datum vor :date sein.',
    'between'              => [
        'numeric' => ':attribute muss zwischen :min und :max liegen.',
        'file'    => ':attribute muss zwischen :min und :max Kilobyte haben.',
        'string'  => ':attribute muss zwischen :min und :max Buchstaben haben.',
        'array'   => ':attribute muss zwischen :min und :max Einträge haben.',
    ],
    'boolean'              => ':attribute Feld muss wahr oder falsch sein.',
    'confirmed'            => ':attribute Bestätigung ist falsch.',
    'date'                 => ':attribute ist kein valides Datum.',
    'date_format'          => ':attribute stimmt nicht mit dem Format :format überein.',
    'different'            => ':attribute und :other müssen sich unterscheiden.',
    'digits'               => ':attribute muss :digits Stellen haben.',
    'digits_between'       => ':attribute muss zwischen :min und :max Stellen haben.',
    'email'                => ':attribute muss eine valide E-Mail Adresse sein.',
    'exists'               => 'Das ausgewählte :attribute ist unzulässig.',
    'filled'               => 'Das :attribute Feld ist ein Pflichtfeld.',
    'image'                => ':attribute muss ein Foto sein.',
    'in'                   => 'Das ausgewählte :attribute ist unzulässig.',
    'integer'              => ':attribute muss eine Ganzzahl sein.',
    'ip'                   => ':attribute muss eine gültige IP-Adresse sein.',
    'json'                 => ':attribute muss ein gültiger JSON-String sein.',
    'max'                  => [
        'numeric' => ':attribute darf nicht größer sein als :max.',
        'file'    => ':attribute darf nicht größer sein als :max Kilobyte.',
        'string'  => ':attribute darf nicht mehr als :max Buchstaben beinhalten.',
        'array'   => ':attribute darf nicht mehr als :max Einträge haben.',
    ],
    'mimes'                => ':attribute muss eine Datei vom Typ: :values sein.',
    'min'                  => [
        'numeric' => ':attribute hat einen Mindestwert von :min.',
        'file'    => ':attribute muss mindestens :min Kilobyte groß sein.',
        'string'  => ':attribute muss mindestens :min Buchstaben lang sein.',
        'array'   => ':attribute muss mindestens :min Einträge beinhalten.',
    ],
    'not_in'               => 'Das ausgewählte :attribute ist ungültig.',
    'numeric'              => ':attribute muss eine Nummer sein.',
    'regex'                => 'Das :attribute Format ist ungültig.',
    'required'             => ':attribute ist ein Pflichtfeld.',
    'required_if'          => ':attribute ist ein Pflichtfeld wenn :other ist :value.',
    'required_with'        => ':attribute ist ein Pflichtfeld wenn :values vorhanden ist.',
    'required_with_all'    => ':attribute ist ein Pflichtfeld wenn :values sind vorhanden.',
    'required_without'     => ':attribute ist ein Pflichtfeld wenn :values nicht vorhanden ist.',
    'required_without_all' => ':attribute ist ein Pflichtfeld wenn keiner von :values vorhanden ist.',
    'same'                 => ':attribute und :other müssen gleich sein.',
    'size'                 => [
        'numeric' => ':attribute muss :size groß sein.',
        'file'    => ':attribute muss :size Kilobyte groß sein.',
        'string'  => ':attribute muss :size Buchstaben beinhalten.',
        'array'   => ':attribute muss :size Einträge haben.',
    ],
    'string'               => ':attribute muss ein String sein.',
    'timezone'             => ':attribute muss eine gültige Zeitzone sein.',
    'unique'               => ':attribute wurde bereits verwendet.',
    'url'                  => 'Das :attribute Format ist ungültig.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
