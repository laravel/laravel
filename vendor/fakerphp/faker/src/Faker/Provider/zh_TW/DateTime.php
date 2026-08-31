<?php

namespace Faker\Provider\zh_TW;

class DateTime extends \Faker\Provider\DateTime
{
    public static function amPm($max = 'now')
    {
        return static::dateTime($max)->format('a') === 'am' ? '上午' : '下午';
    }

    public static function dayOfWeek($max = 'now')
    {
        $map = [
            'Sunday' => '星期日',
            'Monday' => '星期一',
            'Tuesday' => '星期二',
            'Wednesday' => '星期三',
            'Thursday' => '星期四',
            'Friday' => '星期五',
            'Saturday' => '星期六',
        ];
        $week = static::dateTime($max)->format('l');

        return $map[$week] ?? $week;
    }

    public static function monthName($max = 'now')
    {
        $map = [
            'January' => '一月',
            'February' => '二月',
            'March' => '三月',
            'April' => '四月',
            'May' => '五月',
            'June' => '六月',
            'July' => '七月',
            'August' => '八月',
            'September' => '九月',
            'October' => '十月',
            'November' => '十一月',
            'December' => '十二月',
        ];
        $month = static::dateTime($max)->format('F');

        return $map[$month] ?? $month;
    }
}
