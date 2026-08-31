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
 * - Danish Standards Association    bug-glibc-locales@gnu.org
 * - John Eyðstein Johannesen (mashema)
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'LT' => 'HH:mm',
        'LTS' => 'HH:mm:ss',
        'L' => 'DD.MM.YYYY',
        'LL' => 'D. MMMM YYYY',
        'LLL' => 'D. MMMM YYYY HH:mm',
        'LLLL' => 'dddd [d.] D. MMMM YYYY [kl.] HH:mm',
    ],
    'months' => ['januaarip', 'februaarip', 'marsip', 'apriilip', 'maajip', 'juunip', 'juulip', 'aggustip', 'septembarip', 'oktobarip', 'novembarip', 'decembarip'],
    'months_short' => ['jan', 'feb', 'mar', 'apr', 'maj', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'],
    'weekdays' => ['sapaat', 'ataasinngorneq', 'marlunngorneq', 'pingasunngorneq', 'sisamanngorneq', 'tallimanngorneq', 'arfininngorneq'],
    'weekdays_short' => ['sap', 'ata', 'mar', 'pin', 'sis', 'tal', 'arf'],
    'weekdays_min' => ['sap', 'ata', 'mar', 'pin', 'sis', 'tal', 'arf'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,

    'year' => '{1}ukioq :count|{0}:count ukiut|[-Inf,Inf]ukiut :count',
    'a_year' => '{1}ukioq|{0}:count ukiut|[-Inf,Inf]ukiut :count',
    'y' => '{1}:countyr|{0}:countyrs|[-Inf,Inf]:countyrs',

    'month' => '{1}qaammat :count|{0}:count qaammatit|[-Inf,Inf]qaammatit :count',
    'a_month' => '{1}qaammat|{0}:count qaammatit|[-Inf,Inf]qaammatit :count',
    'm' => '{1}:countmo|{0}:countmos|[-Inf,Inf]:countmos',

    'week' => '{1}:count sap. ak.|{0}:count sap. ak.|[-Inf,Inf]:count sap. ak.',
    'a_week' => '{1}a sap. ak.|{0}:count sap. ak.|[-Inf,Inf]:count sap. ak.',
    'w' => ':countw',

    'day' => '{1}:count ulloq|{0}:count ullut|[-Inf,Inf]:count ullut',
    'a_day' => '{1}a ulloq|{0}:count ullut|[-Inf,Inf]:count ullut',
    'd' => ':countd',

    'hour' => '{1}:count tiimi|{0}:count tiimit|[-Inf,Inf]:count tiimit',
    'a_hour' => '{1}tiimi|{0}:count tiimit|[-Inf,Inf]:count tiimit',
    'h' => ':counth',

    'minute' => '{1}:count minutsi|{0}:count minutsit|[-Inf,Inf]:count minutsit',
    'a_minute' => '{1}a minutsi|{0}:count minutsit|[-Inf,Inf]:count minutsit',
    'min' => ':countm',

    'second' => '{1}:count sikunti|{0}:count sikuntit|[-Inf,Inf]:count sikuntit',
    'a_second' => '{1}sikunti|{0}:count sikuntit|[-Inf,Inf]:count sikuntit',
    's' => ':counts',

    'ago' => ':time matuma siorna',

]);
