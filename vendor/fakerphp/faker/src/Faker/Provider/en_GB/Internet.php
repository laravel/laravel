<?php

namespace Faker\Provider\en_GB;

class Internet extends \Faker\Provider\Internet
{
    protected static $freeEmailDomain = ['gmail.com', 'yahoo.com', 'hotmail.com', 'gmail.co.uk', 'yahoo.co.uk', 'hotmail.co.uk'];
    protected static $tld = ['com', 'com', 'com', 'com', 'com', 'com', 'biz', 'info', 'net', 'org', 'co.uk'];
}
