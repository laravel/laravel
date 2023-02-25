<?php

namespace Faker\Provider\pt_PT;

class Internet extends \Faker\Provider\Internet
{
    protected static $freeEmailDomain = ['gmail.com', 'yahoo.com', 'hotmail.com', 'sapo.pt', 'clix.pt', 'mail.pt'];
    protected static $tld = ['com', 'com', 'pt', 'pt', 'net', 'org', 'eu'];
}
