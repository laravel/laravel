<?php

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array_replace_recursive(require __DIR__.'/az.php', [
    'weekdays' => ['базар', 'базар ертәси', 'чәршәнбә ахшамы', 'чәршәнбә', 'ҹүмә ахшамы', 'ҹүмә', 'шәнбә'],
    'weekdays_short' => ['Б.', 'Б.Е.', 'Ч.А.', 'Ч.', 'Ҹ.А.', 'Ҹ.', 'Ш.'],
    'weekdays_min' => ['Б.', 'Б.Е.', 'Ч.А.', 'Ч.', 'Ҹ.А.', 'Ҹ.', 'Ш.'],
    'months' => ['јанвар', 'феврал', 'март', 'апрел', 'май', 'ијун', 'ијул', 'август', 'сентјабр', 'октјабр', 'нојабр', 'декабр'],
    'months_short' => ['јан', 'фев', 'мар', 'апр', 'май', 'ијн', 'ијл', 'авг', 'сен', 'окт', 'ној', 'дек'],
    'meridiem' => ['а', 'п'],
]);
