<?php

namespace Faker\Provider\da_DK;

class Internet extends \Faker\Provider\Internet
{
    /**
     * @var array Some safe email TLD.
     */
    protected static $safeEmailTld = [
        'org', 'com', 'net', 'dk', 'dk', 'dk',
    ];

    /**
     * @var array Some email domains in Denmark.
     */
    protected static $freeEmailDomain = [
        'gmail.com', 'yahoo.com', 'yahoo.dk', 'hotmail.com', 'hotmail.dk', 'mail.dk', 'live.dk',
    ];

    /**
     * @var array Some TLD.
     */
    protected static $tld = [
        'com', 'com', 'com', 'biz', 'info', 'net', 'org', 'dk', 'dk', 'dk',
    ];
}
