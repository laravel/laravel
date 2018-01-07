<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted'             => ':attribute必须接受',
    'active_url'           => ':attribute不是一个有效的网址',
    'after'                => ':attribute必须要晚于 :date',
    'after_or_equal'       => ':attribute必须要等于 :date 或更晚',
    'alpha'                => ':attribute只能由字母组成',
    'alpha_dash'           => ':attribute只能由字母、数字和斜杠组成',
    'alpha_num'            => ':attribute只能由字母和数字组成',
    'array'                => ':attribute必须是一个数组',
    'before'               => ':attribute必须要早于 :date',
    'before_or_equal'      => ':attribute必须要等于 :date 或更早',
    'between'              => [
        'numeric' => ':attribute必须介于 :min - :max 之间',
        'file'    => ':attribute必须介于 :min - :max kb 之间',
        'string'  => ':attribute必须介于 :min - :max 个字符之间',
        'array'   => ':attribute必须只有 :min - :max 个单元',
    ],
    'boolean'              => ':attribute必须为布尔值',
    'confirmed'            => ':attribute两次输入不一致',
    'date'                 => ':attribute不是一个有效的日期',
    'date_format'          => ':attribute的格式必须为 :format',
    'different'            => ':attribute和 :other 必须不同',
    'digits'               => ':attribute必须是 :digits 位的数字',
    'digits_between'       => ':attribute必须是介于 :min 和 :max 位的数字',
    'dimensions'           => ':attribute图片尺寸不正确',
    'distinct'             => ':attribute已经存在',
    'email'                => ':attribute不是一个合法的邮箱',
    'exists'               => ':attribute不存在',
    'file'                 => ':attribute必须是文件',
    'filled'               => ':attribute不能为空',
    'image'                => ':attribute必须是图片',
    'in'                   => '已选的属性 :attribute 非法',
    'in_array'             => ':attribute没有在 :other 中',
    'integer'              => ':attribute必须是整数',
    'ip'                   => ':attribute必须是有效的 IP 地址',
    'ipv4'                 => ':attribute必须是有效的 IPv4 地址',
    'ipv6'                 => ':attribute必须是有效的 IPv6 地址',
    'json'                 => ':attribute必须是正确的 JSON 格式',
    'max'                  => [
        'numeric' => ':attribute不能大于 :max',
        'file'    => ':attribute不能大于 :max kb',
        'string'  => ':attribute不能大于 :max 个字符',
        'array'   => ':attribute最多只有 :max 个单元',
    ],
    'mimes'                => ':attribute必须是一个 :values 类型的文件',
    'mimetypes'            => ':attribute必须是一个 :values 类型的文件',
    'min'                  => [
        'numeric' => ':attribute必须大于等于 :min',
        'file'    => ':attribute大小不能小于 :min kb',
        'string'  => ':attribute至少为 :min 个字符',
        'array'   => ':attribute至少有 :min 个单元',
    ],
    'not_in'               => '已选的属性 :attribute非法',
    'numeric'              => ':attribute必须是一个数字',
    'present'              => ':attribute必须存在',
    'regex'                => ':attribute格式不正确',
    'required'             => '请填写:attribute',
    'required_if'          => '当 :other 为 :value 时 :attribute不能为空',
    'required_unless'      => '当 :other 不为 :value 时 :attribute不能为空',
    'required_with'        => '当 :values 存在时 :attribute不能为空',
    'required_with_all'    => '当 :values 存在时 :attribute不能为空',
    'required_without'     => '当 :values 不存在时 :attribute不能为空',
    'required_without_all' => '当 :values 都不存在时 :attribute不能为空',
    'same'                 => ':attribute和 :other 必须相同',
    'size'                 => [
        'numeric' => ':attribute大小必须为 :size',
        'file'    => ':attribute大小必须为 :size kb',
        'string'  => ':attribute必须是 :size 个字符',
        'array'   => ':attribute必须为 :size 个单元',
    ],
    'string'               => ':attribute必须是一个字符串',
    'timezone'             => ':attribute必须是一个合法的时区值',
    'unique'               => ':attribute已经存在',
    'uploaded'             => ':attribute上传失败',
    'url'                  => ':attribute格式不正确',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention 'attribute.rule' to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom'               => [
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
    | of 'email'. This simply helps us make messages a little cleaner.
    |
    */

    'attributes'           => [
        'name'                  => '名称',
        'username'              => '用户名',
        'email'                 => '邮箱',
        'first_name'            => '名',
        'last_name'             => '姓',
        'password'              => '密码',
        'password_confirmation' => '确认密码',
        'city'                  => '城市',
        'country'               => '国家',
        'address'               => '地址',
        'phone'                 => '电话',
        'mobile'                => '手机号码',
        'age'                   => '年龄',
        'sex'                   => '性别',
        'gender'                => '性别',
        'day'                   => '天',
        'month'                 => '月',
        'year'                  => '年',
        'hour'                  => '时',
        'minute'                => '分',
        'second'                => '秒',
        'title'                 => '标题',
        'content'               => '内容',
        'description'           => '描述',
        'excerpt'               => '摘要',
        'date'                  => '日期',
        'time'                  => '时间',
        'available'             => '可用的',
        'size'                  => '大小',

        'mobile_code'           => '短信验证码',
        'captcha_code'          => '图形验证码',
    ],

    'captcha_code' => ':attribute 验证失败',
    'mobile_code' => ':attribute 验证失败',
    'mobile' => ':attribute 填写错误',
];
