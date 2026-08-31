<?php

namespace Faker\Provider\en_HK;

class Internet extends \Faker\Provider\Internet
{
    protected static $freeEmailDomain = [
        'gmail.com', 'yahoo.com', 'hotmail.com', 'yahoo.com.hk', 'hotmail.com.hk',
    ];
    protected static $tld = [
        'com', 'com', 'com', 'com.hk', 'com.hk', 'com', 'biz', 'info', 'net', 'org',
        'com.hk', 'edu.hk', 'org.hk', 'idv.hk',
    ];
}
