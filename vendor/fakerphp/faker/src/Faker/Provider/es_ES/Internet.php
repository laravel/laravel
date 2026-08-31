<?php

namespace Faker\Provider\es_ES;

class Internet extends \Faker\Provider\Internet
{
    protected static $freeEmailDomain = ['gmail.com', 'hotmail.com', 'hotmail.es', 'yahoo.com', 'yahoo.es', 'live.com', 'hispavista.com', 'latinmail.com', 'terra.com'];
    protected static $tld = ['com', 'com', 'com', 'com', 'net', 'org', 'org', 'es', 'es', 'es', 'com.es'];
}
