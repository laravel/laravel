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

    'accepted'             => ':attribute debe ser aceptado.',
    'active_url'           => ':attribute no es una URL válida.',
    'after'                => ':attribute debe ser una fecha posterior a :date.',
    'after_or_equal'       => ':attribute debe ser una fecha igual o posterior a :date.',
    'alpha'                => ':attribute solo puede contener letras.',
    'alpha_dash'           => ':attribute solo puede contener letras, número y guiones.',
    'alpha_num'            => ':attribute solo puede contener letras y numeros.',
    'array'                => ':attribute debe ser un array.',
    'before'               => ':attribute debe ser una fecha anterior a :date.',
    'before_or_equal'      => ':attribute debe ser una fecha anterior o igual a :date.',
    'between'              => [
        'numeric' => ':attribute debe ser un valor entre :min y :max.',
        'file'    => 'El tamaño de :attribute debe estar entre :min y :max kilobytes.',
        'string'  => ':attribute debe tener entre :min y :max caracteres.',
        'array'   => ':attribute debe tener entre :min y :max items.',
    ],
    'boolean'              => ':attribute debe ser verdadero o falso.',
    'confirmed'            => 'La confirmación de :attribute no coincide.',
    'date'                 => ':attribute no es una fecha válida.',
    'date_format'          => ':attribute no coincide con el formato :format.',
    'different'            => ':attribute y :other deben ser diferentes.',
    'digits'               => ':attribute debe ser de :digits digits.',
    'digits_between'       => ':attribute debe estar entre :min y :max digits.',
    'dimensions'           => 'Las dimensiones de :attribute son inválidas.',
    'distinct'             => ':attribute tiene valores duplicados.',
    'email'                => ':attribute debe ser una dirección de email válida.',
    'exists'               => 'El atributo seleccionado :attribute es inválido.',
    'file'                 => ':attribute debe ser un archivo.',
    'filled'               => ':attribute es requerido.',
    'image'                => ':attribute debe ser una imagen.',
    'in'                   => 'El atributo seleccionado :attribute en inválido.',
    'in_array'             => ':attribute no existe en :other.',
    'integer'              => ':attribute debe ser un número entero.',
    'ip'                   => ':attribute debe ser una dirección IP válida.',
    'ipv4'                 => ':attribute debe ser una dirección IPv4 válida.',
    'ipv6'                 => ':attribute debe ser una dirección IPv6 válida.',
    'json'                 => ':attribute debe ser un string JSON válido.',
    'max'                  => [
        'numeric' => ':attribute no puede ser mayor a :max.',
        'file'    => ':attribute no puede tener un tamaño mayor a :max kilobytes.',
        'string'  => ':attribute no puede ser mas largo que :max caracteres.',
        'array'   => ':attribute no puede tener mas de :max items.',
    ],
    'mimes'                => ':attribute debe ser un archivo de tipo: :values.',
    'mimetypes'            => ':attribute debe ser un archivo de tipo: :values.',
    'min'                  => [
        'numeric' => ':attribute debe ser, al menos, :min.',
        'file'    => ':attribute debe tener un tamaño mínimo de :min kilobytes.',
        'string'  => ':attribute debe tener, al menos, :min caracteres.',
        'array'   => ':attribute debe tener, al menos, :min items.',
    ],
    'not_in'               => ':attribute es inválido.',
    'numeric'              => ':attribute debe ser un número.',
    'present'              => ':attribute debe estar seleccionado.',
    'regex'                => 'El formato de :attribute es inválido.',
    'required'             => ':attribute es requerido.',
    'required_if'          => ':attribute es requerido cuando :other es :value.',
    'required_unless'      => ':attribute es requerido, a menos que :other sea :values.',
    'required_with'        => ':attribute es requerido cuando :values esta seleccionado.',
    'required_with_all'    => ':attribute es requerido cuando :values están seleccionados.',
    'required_without'     => ':attribute es requerido cuando :values no esta seleccionado.',
    'required_without_all' => ':attribute es requerido cuando ninguno de :values esta seleccionado.',
    'same'                 => ':attribute y :other deben coincidir.',
    'size'                 => [
        'numeric' => ':attribute debe ser exactamente :size.',
        'file'    => ':attribute debe tener un tamaño de :size kilobytes.',
        'string'  => ':attribute debe tener un largo de :size caracteres.',
        'array'   => ':attribute debe contener :size items.',
    ],
    'string'               => ':attribute debe ser una cadena de caracteres.',
    'timezone'             => ':attribute debe ser un huso horario válido.',
    'unique'               => ':attribute ya esta siendo utilizado.',
    'uploaded'             => ':attribute no puse ser transferido.',
    'url'                  => 'El formato de :attribute es inválido.',

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
