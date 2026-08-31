<?php

namespace Faker\Provider\bn_BD;

class Utils
{
    public static function getBanglaNumber($number)
    {
        $english = range(0, 10);
        $bangla = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

        return str_replace($english, $bangla, $number);
    }
}
