<?php

namespace Faker\Provider\en_AU;

class Internet extends \Faker\Provider\Internet
{
    protected static $freeEmailDomain = ['gmail.com', 'yahoo.com', 'hotmail.com', 'yahoo.com.au', 'hotmail.com.au'];
    protected static $tld = ['com', 'com.au', 'org', 'org.au', 'net', 'net.au', 'biz', 'info', 'edu', 'edu.au'];
}
