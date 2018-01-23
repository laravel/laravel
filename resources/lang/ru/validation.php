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

    'accepted'             => 'Поле :attribute должно быть подтверждено.',
    'active_url'           => 'Поле :attribute не является корректным URL.',
    'after'                => 'Поле :attribute должно быть датой старше :date.',
    'after_or_equal'       => 'Поле :attribute должно быть датой старше или равной :date.',
    'alpha'                => 'Поле :attribute может содержать только буквы.',
    'alpha_dash'           => 'Поле :attribute может содержать только буквы, цифры и дефис.',
    'alpha_num'            => 'Поле :attribute может содержать только буквы и цифры.',
    'array'                => 'Поле :attribute должно быть массивом.',
    'before'               => 'Поле :attribute должно быть датой раньше :date.',
    'before_or_equal'      => 'Поле :attribute должно быть датой раньше или равной :date.',
    'between'              => [
        'numeric' => 'Поле :attribute должно быть числом между :min и :max.',
        'file'    => 'Поле :attribute должно иметь размер между :min и :max килобайт.',
        'string'  => 'Поле :attribute должно быть строкой длиной не меньше :min и не больше :max символов.',
        'array'   => 'Поле :attribute должно быть массивом, содержащим не меньше :min и не больше :max символов.',
    ],
    'boolean'              => 'Значение поля :attribute должно быть булевым и содержать TRUE или FALSE.',
    'confirmed'            => 'Значение поля :attribute не совпадает с подтверждаемым полем.',
    'date'                 => 'Значение поля :attribute не является коректной датой.',
    'date_format'          => 'Значение поля :attribute не является датой в формате :format.',
    'different'            => 'Значение поля :attribute и :other должны быть разными.',
    'digits'               => 'Значение поля :attribute должно быть :digits цифрами.',
    'digits_between'       => 'Значение поля :attribute должно быть между :min и :max цифр.',
    'dimensions'           => 'Изображение, указанное в поле :attribute, имеет недопустимые размеры.',
    'distinct'             => 'Значение поля :attribute имеет повторяющееся значение.',
    'email'                => 'Значение поля :attribute должно быть корректным email-адресом.',
    'exists'               => 'Выбранное значение поля :attribute некорректно.',
    'file'                 => 'Значение поля :attribute должно быть файлом.',
    'filled'               => 'Значение поля :attribute должно быть заполнено.',
    'image'                => 'Значение поля :attribute должно быть изображением.',
    'in'                   => 'Выбранное значение поля :attribute некорректно.',
    'in_array'             => 'Значение поля :attribute не существует в :other.',
    'integer'              => 'Значение поля :attribute должно быть целым числом.',
    'ip'                   => 'Значение поля :attribute должно быть корректным IP-адресом.',
    'ipv4'                 => 'Значение поля :attribute должно быть корректным IPv4-адресом.',
    'ipv6'                 => 'Значение поля :attribute должно быть корректным IPv6-адресом.',
    'json'                 => 'Значение поля :attribute должно быть корректной JSON-строкой.',
    'max'                  => [
        'numeric' => 'Значение поля :attribute не может быть больше :max.',
        'file'    => 'Указанный в поле :attribute файл не может быть больше :max килобайт.',
        'string'  => 'Значение поля :attribute не может быть больше :max символов.',
        'array'   => 'Значение поля :attribute не может быть больше :max элементов.',
    ],
    'mimes'                => 'Поле :attribute должно быть файлом типа :values.',
    'mimetypes'            => 'Поле :attribute должно быть файлом типа :values.',
    'min'                  => [
        'numeric' => 'Значение поля :attribute должно быть больше :min.',
        'file'    => 'Указанный в поле :attribute файл должен быть больше :min килобайт.',
        'string'  => 'Длина значения поля :attribute должна быть больше :min символов.',
        'array'   => 'Значение поля :attribute должно быть больше :min items.',
    ],
    'not_in'               => 'Выбранное значение поля :attribute некорректно.',
    'numeric'              => 'Поле :attribute должно быть числом.',
    'present'              => 'Поле :attribute должно присутствовать.',
    'regex'                => 'Значение поля :attribute имеет некорректный формат.',
    'required'             => 'Значение поля :attribute обязательно.',
    'required_if'          => 'Значение поля :attribute обязательно, если значение поля :other равно :value.',
    'required_unless'      => 'Значение поля :attribute обязательно, если значение поля :other присутствует в :values.',
    'required_with'        => 'Значение поля :attribute обязательно, если присутствует одно из значений :values.',
    'required_with_all'    => 'Значение поля :attribute обязательно, если присутствует все поля из :values.',
    'required_without'     => 'Значение поля :attribute обязательно, если значения :values не заполнены.',
    'required_without_all' => 'Значение поля :attribute обязательно, если все значения из :values не заполнены.',
    'same'                 => 'Значение поля :attribute и :other должны совпадать.',
    'size'                 => [
        'numeric' => 'Поле :attribute должно содержать :size символов.',
        'file'    => 'Поле :attribute должно быть файлом размером :size килобайт.',
        'string'  => 'Поле :attribute должно быть строкой длиной :size символов.',
        'array'   => 'Массив :attribute должен содержать :size элементов.',
    ],
    'string'               => 'Значение поля :attribute должно быть строкой.',
    'timezone'             => 'Значение поля :attribute должно быть валидной таймзоной.',
    'unique'               => 'Значение поля :attribute уже существует.',
    'uploaded'             => 'Файл из поля :attribute не удалось загрузить.',
    'url'                  => 'Значение поля :attribute имеет некорректный формат URL.',

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
