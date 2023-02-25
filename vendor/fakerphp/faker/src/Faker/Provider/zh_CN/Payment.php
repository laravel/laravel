<?php

namespace Faker\Provider\zh_CN;

class Payment extends \Faker\Provider\Payment
{
    /**
     * @see https://zh.wikipedia.org/wiki/%E4%B8%AD%E5%9B%BD%E9%93%B6%E8%A1%8C%E5%88%97%E8%A1%A8
     *
     * @var array
     */
    protected static $banks = [
        '渤海银行',
        '广发银行',
        '国家开发银行',
        '恒丰银行',
        '华夏银行',
        '交通银行',
        '平安银行',
        '上海浦东发展银行',
        '兴业银行',
        '招商银行',
        '浙商银行',
        '中国工商银行',
        '中国光大银行',
        '中国建设银行',
        '中国民生银行',
        '中国农业银行',
        '中国银行',
        '中国邮政储蓄银行',
        '中信银行',
    ];

    /**
     * @example '中国建设银行'
     *
     * @return string
     */
    public static function bank()
    {
        return static::randomElement(static::$banks);
    }
}
