<?php

namespace Faker\Provider\nl_NL;

class Internet extends \Faker\Provider\Internet
{
    protected static $freeEmailDomain = ['gmail.com', 'hotmail.nl', 'live.nl', 'yahoo.nl'];
    protected static $tld = ['com', 'com', 'com', 'net', 'org', 'nl', 'nl', 'nl'];
}
