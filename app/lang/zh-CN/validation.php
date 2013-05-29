<?php

return array(

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

    "accepted"         => ":attribute 必须接受。",
    "active_url"       => ":attribute 不是一个有效的URL。",
    "after"            => ":attribute 必须是一个在 :date 之后的日期。",
    "alpha"            => ":attribute 只能由字母组成。",
    "alpha_dash"       => ":attribute 只能由字母、数字和斜杠组成。",
    "alpha_num"        => ":attribute 只能由字母和数字组成。",
    "before"           => ":attribute 必须是一个在 :date 之前的日期。",
    "between"          => array(
        "numeric" => ":attribute 必须介于 :min - :max 之间。",
        "file"    => ":attribute 必须介于 :min - :max 千字节之间。",
        "string"  => ":attribute 必须介于 :min - :max 个字符之间。",
    ),
    "confirmed"        => ":attribute 确认不匹配。",
    "date"             => ":attribute 不是一个有效的日期。",
    "date_format"      => ":attribute 不匹配日期格式 :format。",
    "different"        => ":attribute 和 :other 必须不同。",
    "digits"           => ":attribute 必须是 :digits 位的数字。",
    "digits_between"   => ":attribute 必须是介于 :min 和 :max 位的数字。",
    "email"            => ":attribute 电邮格式非法。",
    "exists"           => "已选的属性 :attribute 非法。",
    "image"            => ":attribute 必须是一张图片。",
    "in"               => "已选的属性 :attribute 非法。",
    "integer"          => ":attribute 必须是一个整数。",
    "ip"               => ":attribute 必须是一个有效的IP地址。",
    "max"              => array(
        "numeric" => ":attribute 必须小于 :max 。",
        "file"    => ":attribute 必须小于 :max 千字节。",
        "string"  => ":attribute 必须小于 :max 个字符。",
    ),
    "mimes"            => ":attribute 必须是一个 :values 类型的文件。",
    "min"              => array(
        "numeric" => ":attribute 必须大于 :min 。",
        "file"    => ":attribute 必须大于 :min 千字节。",
        "string"  => ":attribute 必须大于 :min 个字符。",
    ),
    "not_in"           => "已选的属性 :attribute 非法。",
    "numeric"          => ":attribute 必须是一个数字。",
    "regex"            => ":attribute 字段必填。",
    "required"         => ":attribute 属性需要填写字段。",
    "required_if"      => ":attribute 属性当 :other 为 :value时为必填项。",
    "required_with"    => ":attribute 属性当 :values 存在时为必填项。",
    "required_without" => ":attribute 属性当 :values 不存在时为必填项。",
    "same"             => ":attribute 和 :other 必须匹配。",
    "size"             => array(
        "numeric" => ":attribute 大小必须是 :size 。",
        "file"    => ":attribute 大小必须是 :size 千字节。",
        "string"  => ":attribute 必须是 :size 个字符。",
    ),
    "unique"           => ":attribute 已经有人使用。",
    "url"              => ":attribute 格式非法。",

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

    'custom' => array(),

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

    'attributes' => array(),

);
