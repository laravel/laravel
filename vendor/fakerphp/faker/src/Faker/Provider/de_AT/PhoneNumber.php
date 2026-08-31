<?php

namespace Faker\Provider\de_AT;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    protected static $formats = [
        '0650 #######',
        '0660 #######',
        '0664 #######',
        '0676 #######',
        '0677 #######',
        '0678 #######',
        '0699 #######',
        '0680 #######',
        '+43 #### ####',
        '+43 #### ####-##',
    ];

    protected static $e164Formats = [
        '+43##########',
    ];
}
