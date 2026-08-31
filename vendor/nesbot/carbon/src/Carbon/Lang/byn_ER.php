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
 * - Ge'ez Frontier Foundation    locales@geez.org
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD/MM/YYYY',
    ],
    'months' => ['ልደትሪ', 'ካብኽብቲ', 'ክብላ', 'ፋጅኺሪ', 'ክቢቅሪ', 'ምኪኤል ትጓ̅ኒሪ', 'ኰርኩ', 'ማርያም ትሪ', 'ያኸኒ መሳቅለሪ', 'መተሉ', 'ምኪኤል መሽወሪ', 'ተሕሳስሪ'],
    'months_short' => ['ልደት', 'ካብኽ', 'ክብላ', 'ፋጅኺ', 'ክቢቅ', 'ም/ት', 'ኰር', 'ማርያ', 'ያኸኒ', 'መተሉ', 'ም/ም', 'ተሕሳ'],
    'weekdays' => ['ሰንበር ቅዳዅ', 'ሰኑ', 'ሰሊጝ', 'ለጓ ወሪ ለብዋ', 'ኣምድ', 'ኣርብ', 'ሰንበር ሽጓዅ'],
    'weekdays_short' => ['ሰ/ቅ', 'ሰኑ', 'ሰሊጝ', 'ለጓ', 'ኣምድ', 'ኣርብ', 'ሰ/ሽ'],
    'weekdays_min' => ['ሰ/ቅ', 'ሰኑ', 'ሰሊጝ', 'ለጓ', 'ኣምድ', 'ኣርብ', 'ሰ/ሽ'],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['ፋዱስ ጃብ', 'ፋዱስ ደምቢ'],
]);
