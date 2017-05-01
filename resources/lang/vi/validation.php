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


    'accepted'             => ':attribute phải được chấp nhận.',
    'active_url'           => ':attribute không phải là một URL hợp lệ.',
    'after'                => ':attribute phải sau ngày :date.',
    'alpha'                => ':attribute chỉ có thể chứa ký tự chữ',
    'alpha_dash'           => ':attribute chỉ có thể chứa ký tự chữ, số và dấu gạch ngang (-)',
    'alpha_num'            => ':attribute chỉ có thể chứa ký tự chữ và số',
    'before'               => ':attribute phải trước ngày :date.',
    'array'                => ':attribute phải là một mảng.',
    'before'               => ':attribute phải trước ngày :date.',
    'between'              => [
        'numeric' => ':attribute phải có giá trị trong khoản :min - :max.',
        'file'    => ':attribute phải có kích thước trong khoản :min - :max kilobytes.',
        'string'  => ':attribute phải có từ :min đến :max ký tự.',
        'array'   => ':attribute phải có từ :min đến :max phần tử.',
    ],
    'boolean'              => ':attribute mang giá trị True hoặc False.',
    'confirmed'            => 'Giá trị xác nhận :attribute không trùng khớp.',
    'date'                 => ':attribute không phải là một ngày hợp lệ.',
    'date_format'          => ':attribute không phù hợp với định dạng :format.',
    'different'            => ':attribute và :other phải khác nhau.',
    'digits'               => ':attribute phải có :digits chữ số.',
    'digits_between'       => ':attribute phải nằm trong khoản :min và :max chữ số.',
    'email'                => 'Định dạng :attribute không hợp lệ.',
    'exists'               => ':attribute đã chọn không hợp lệ.',
    'filled'               => ':attribute trường này là bắt buộc.',
    'image'                => ':attribute phải là một tập tin ảnh.',
    'in'                   => ':attribute đã chọn không hợp lệ.',
    'integer'              => ':attribute phải là một số nguyên.',
    'ip'                   => ':attribute phải là một địa chỉ IP.',
    'json'                 => ':attribute phải là một đoạn JSON.',
    'max'                  => [
        'numeric' => ':attribute không được lớn hơn :max.',
        'file'    => ':attribute không được lớn hơn :max kilobytes.',
        'string'  => ':attribute không được dài hơn :max ký tự.',
        'array'   => ':attribute không được quá :max phần tử.',
    ],
    'mimes'                => ':attribute phải là một tập tin có định dạng: :values.',
    'min'                  => [
        'numeric' => ':attribute nhỏ nhất là :min.',
        'file'    => ':attribute nhỏ nhất là :min kilobytes.',
        'string'  => ':attribute ngắn nhất là :min ký tự.',
        'array'   => ':attribute phải có ít nhất :min phần tử.',
    ],
    'not_in'               => 'Giá trị :attribute đã chọn không hợp lệ.',
    'numeric'              => ':attribute phải là một giá trị số.',
    'regex'                => ':attribute không hợp lệ.',
    'required'             => ':attribute bắt buộc phải có giá trị.',
    'same'                 => ':attribute và :other phải có giá trị giống nhau.',
    'required_if'          => ':attribute bắt buộc phải nhập khi :other có giá trị :value.',
    'required_unless'      => ':attribute bắt buộc phải nhập khi :other không phải :values.',
    'required_with'        => ':attribute bắt buộc phải nhập khi :values có giá trị.',
    'required_without'     => ':attribute bắt buộc phải nhập khi :values không có giá trị.',
    'required_without_all' => ':attribute bắt buộc phải nhập khi :values không có giá trị.',
    'size'                 => [
        'numeric' => ':attribute phải bằng :size.',
        'file'    => ':attribute phải bằng :size kilobytes.',
        'string'  => ':attribute phải dài :size ký tự.',
        'array'   => ':attribute phải có :size phần tử.',
    ],
    'string'               => ':attribute phải là một chuỗi.',
    'timezone'             => ':attribute phải là một múi giờ.',
    'unique'               => ':attribute đã có sẳn.',
    'url'                  => ':attribute không hợp lệ.',
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
