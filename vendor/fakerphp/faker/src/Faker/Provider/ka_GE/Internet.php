<?php

namespace Faker\Provider\ka_GE;

class Internet extends \Faker\Provider\Internet
{
    protected static $freeEmailDomain = [
        'posta.ge', 'boom.ge', 'hotmail.com', 'gmail.com', 'yahoo.com', 'mail.ru', 'avoe.ge',
    ];

    protected static $tld = [
        'ge', 'ge', 'ge', 'ge', 'ge', 'com.ge', 'edu.ge', 'net.ge', 'org.ge',
        'pvt.ge', 'gov.ge', 'mil.ge', 'com', 'biz', 'info', 'net', 'org',
    ];
}
