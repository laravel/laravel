<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * Authors:
 * - http://www.uyip.org/ Pablo Saratxaga pablo@mandrakesoft.com
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'year' => '{1}:count יאר|{0}:count יאר|[-Inf,Inf]:count יאר',
    'a_year' => '{1}א יאר|{0}:count יאר|[-Inf,Inf]:count יאר',
    'y' => ':count יאר',
    'month' => '{1}:count חודש|{0}:count חדשים|[-Inf,Inf]:count חדשים',
    'a_month' => '{1}א חודש|{0}:count חדשים|[-Inf,Inf]:count חדשים',
    'm' => '{1}:count חודש|{0}:count חדשים|[-Inf,Inf]:count חדשים',
    'week' => '{1}:count וואך|{0}:count וואכן|[-Inf,Inf]:count וואכן',
    'a_week' => '{1}א וואך|{0}:count וואכן|[-Inf,Inf]:count וואכן',
    'w' => ':count וואך',
    'day' => '{1}:count טאג|{0}:count טעג|[-Inf,Inf]:count טעג',
    'a_day' => '{1}א טאג|{0}:count טעג|[-Inf,Inf]:count טעג',
    'd' => ':count טאג',
    'hour'   => ':count שעה',
    'a_hour' => 'א שעה',
    'h' => ':count שעה',
    'minute' => '{1}:count מינוט|{0}:count מינוט|[-Inf,Inf]:count מינוט',
    'a_minute' => '{1}א מינוט|{0}:count מינוט|[-Inf,Inf]:count מינוט',
    'min' => ':count מינוט',
    'second' => '{1}:count סעקונדע|{0}:count סעקונדעס|[-Inf,Inf]:count סעקונדעס',
    'a_second' => '{0,1}א סעקונדע|[-Inf,Inf]:count סעקונדעס',
    's' => ':count סעק',
    'millisecond' => '{1}:count מילי-סעקונדע|{0}:count מילי-סעקונדעס|[-Inf,Inf]:count מילי-סעקונדעס',
    'a_millisecond' => '{1}א מילי-סעקונדע|{0}:count מילי-סעקונדעס|[-Inf,Inf]:count מילי-סעקונדעס',
    'ms' => ':count מס',
    'microsecond' => '{1}:count מיקרא-סעקונדע|{0}:count מיקרא-סעקונדעס|[-Inf,Inf]:count מיקרא-סעקונדעס',
    'a_microsecond' => '{1}א מיקרא-סעקונדע|{0}:count מיקרא-סעקונדעס|[-Inf,Inf]:count מיקרא-סעקונדעס',
    'µs' => ':count מיקרא',
    'ago' => ':time פון יעצט',
    'from_now' => ':time ארום',
    'after' => ':time נאך',
    'before' => ':time פאר',
    'diff_now' => 'ממש יעצט',
    'diff_today' => 'היינט',
    'diff_yesterday' => 'נעכטן',
    'diff_tomorrow' => 'מארגן',
    'diff_before_yesterday' => 'אייער-נעכטן',
    'diff_after_tomorrow' => 'איבער-מארגן',
    'period_recurrences' => '{1}איין מאל|{0}:count מאל|[-Inf,Inf]:count מאל',
    'period_interval' => 'יעדע :interval',
    'period_start_date' => 'פון :date',
    'period_end_date' => 'ביז :date',
    'months' => ['יאנואר', 'פעברואר', 'מארטש', 'אפריל', 'מאי', 'יוני', 'יולי', 'אויגוסט', 'סעפטעמבער', 'אקטאבער', 'נאוועמבער', 'דעצעמבער'],
    'months_short' => ['יאנ\'', 'פעב\'', 'מאר\'', 'אפר\'', 'מאי', 'יוני', 'יולי', 'אויג\'', 'סעפ\'', 'אקט\'', 'נאו\'', 'דעצ\''],
    'weekdays' => ['זונטאג', 'מאנטאג', 'דינסטאג', 'מיטוואך', 'דאנערשטאג', 'פרייטאג', 'שבת'],
    'weekdays_short' => ['זונ\'', 'מאנ\'', 'דינ\'', 'מיט\'', 'דאנ\'', 'פריי\'', 'שבת'],
    'weekdays_min' => ['ז\'', 'מ\'', 'ד\'', 'מ\'', 'ד\'', 'ו\'', 'ש\''],
    'ordinal' => static function ($number) {
        return $number.'טע';
    },
    'list' => [', ', ' און '],
    'formats' => [
        'L' => 'DD/MM/YY',
    ],
    'first_day_of_week' => 0,
    'day_of_first_week_of_year' => 1,
]);
