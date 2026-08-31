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
 * - ubuntu Myanmar LoCo Team https://ubuntu-mm.net Bone Pyae Sone bone.burma@mail.com
 */
return array_replace_recursive(require __DIR__.'/en.php', [
    'first_day_of_week' => 0,
    'formats' => [
        'L' => 'OY MMM OD dddd',
    ],
    'months' => ['လိူၼ်ၵမ်', 'လိူၼ်သၢမ်', 'လိူၼ်သီ', 'လိူၼ်ႁႃႈ', 'လိူၼ်ႁူၵ်း', 'လိူၼ်ၸဵတ်း', 'လိူၼ်ပႅတ်ႇ', 'လိူၼ်ၵဝ်ႈ', 'လိူၼ်သိပ်း', 'လိူၼ်သိပ်းဢိတ်း', 'လိူၼ်သိပ်းဢိတ်းသွင်', 'လိူၼ်ၸဵင်'],
    'months_short' => ['လိူၼ်ၵမ်', 'လိူၼ်သၢမ်', 'လိူၼ်သီ', 'လိူၼ်ႁႃႈ', 'လိူၼ်ႁူၵ်း', 'လိူၼ်ၸဵတ်း', 'လိူၼ်ပႅတ်ႇ', 'လိူၼ်ၵဝ်ႈ', 'လိူၼ်သိပ်း', 'လိူၼ်သိပ်းဢိတ်း', 'လိူၼ်သိပ်းဢိတ်းသွင်', 'လိူၼ်ၸဵင်'],
    'weekdays' => ['ဝၼ်းဢႃးတိတ်ႉ', 'ဝၼ်းၸၼ်', 'ဝၼ်း​ဢၢင်း​ၵၢၼ်း', 'ဝၼ်းပူတ်ႉ', 'ဝၼ်းၽတ်း', 'ဝၼ်းသုၵ်း', 'ဝၼ်းသဝ်'],
    'weekdays_short' => ['တိတ့်', 'ၸၼ်', 'ၵၢၼ်း', 'ပုတ့်', 'ၽတ်း', 'သုၵ်း', 'သဝ်'],
    'weekdays_min' => ['တိတ့်', 'ၸၼ်', 'ၵၢၼ်း', 'ပုတ့်', 'ၽတ်း', 'သုၵ်း', 'သဝ်'],
    'alt_numbers' => ['႐႐', '႐႑', '႐႒', '႐႓', '႐႔', '႐႕', '႐႖', '႐႗', '႐႘', '႐႙', '႑႐', '႑႑', '႑႒', '႑႓', '႑႔', '႑႕', '႑႖', '႑႗', '႑႘', '႑႙', '႒႐', '႒႑', '႒႒', '႒႓', '႒႔', '႒႕', '႒႖', '႒႗', '႒႘', '႒႙', '႓႐', '႓႑', '႓႒', '႓႓', '႓႔', '႓႕', '႓႖', '႓႗', '႓႘', '႓႙', '႔႐', '႔႑', '႔႒', '႔႓', '႔႔', '႔႕', '႔႖', '႔႗', '႔႘', '႔႙', '႕႐', '႕႑', '႕႒', '႕႓', '႕႔', '႕႕', '႕႖', '႕႗', '႕႘', '႕႙', '႖႐', '႖႑', '႖႒', '႖႓', '႖႔', '႖႕', '႖႖', '႖႗', '႖႘', '႖႙', '႗႐', '႗႑', '႗႒', '႗႓', '႗႔', '႗႕', '႗႖', '႗႗', '႗႘', '႗႙', '႘႐', '႘႑', '႘႒', '႘႓', '႘႔', '႘႕', '႘႖', '႘႗', '႘႘', '႘႙', '႙႐', '႙႑', '႙႒', '႙႓', '႙႔', '႙႕', '႙႖', '႙႗', '႙႘', '႙႙'],
    'meridiem' => ['ၵၢင်ၼႂ်', 'တၢမ်းၶမ်ႈ'],

    'month' => ':count လိူၼ်', // less reliable
    'm' => ':count လိူၼ်', // less reliable
    'a_month' => ':count လိူၼ်', // less reliable

    'week' => ':count ဝၼ်း', // less reliable
    'w' => ':count ဝၼ်း', // less reliable
    'a_week' => ':count ဝၼ်း', // less reliable

    'hour' => ':count ຕີ', // less reliable
    'h' => ':count ຕີ', // less reliable
    'a_hour' => ':count ຕີ', // less reliable

    'minute' => ':count ເດັກ', // less reliable
    'min' => ':count ເດັກ', // less reliable
    'a_minute' => ':count ເດັກ', // less reliable

    'second' => ':count ဢိုၼ်ႇ', // less reliable
    's' => ':count ဢိုၼ်ႇ', // less reliable
    'a_second' => ':count ဢိုၼ်ႇ', // less reliable

    'year' => ':count ပီ',
    'y' => ':count ပီ',
    'a_year' => ':count ပီ',

    'day' => ':count ກາງວັນ',
    'd' => ':count ກາງວັນ',
    'a_day' => ':count ກາງວັນ',
]);
