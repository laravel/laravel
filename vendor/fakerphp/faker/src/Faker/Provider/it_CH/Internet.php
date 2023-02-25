<?php

namespace Faker\Provider\it_CH;

class Internet extends \Faker\Provider\Internet
{
    protected static $freeEmailDomain = ['gmail.com', 'hotmail.com', 'yahoo.com', 'googlemail.com', 'gmx.ch', 'bluewin.ch', 'swissonline.ch'];
    protected static $tld = ['com', 'com', 'com', 'net', 'org', 'li', 'ch', 'ch'];
}
