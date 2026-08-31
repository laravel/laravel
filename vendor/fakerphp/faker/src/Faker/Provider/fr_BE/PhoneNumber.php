<?php

namespace Faker\Provider\fr_BE;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    protected static $formats = [
        '+32(0)########',
        '+32(0)### ######',
        '+32(0)# #######',
        '0#########',
        '0### ######',
        '0### ### ###',
        '0### ## ## ##',
        '0## ######',
        '0## ## ## ##',
        '0# #######',
        '0# ### ## ##',
    ];
}
