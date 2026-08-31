<?php

namespace Faker\Provider\en_ZA;

class Internet extends \Faker\Provider\Internet
{
    protected static $freeEmailDomain = ['gmail.com', 'yahoo.com', 'hotmail.com', 'webmail.co.za', 'vodamail.co.za'];

    /**
     * An array of South African TLDs.
     *
     * @see https://en.wikipedia.org/wiki/.za
     * @see https://en.wikipedia.org/wiki/List_of_Internet_top-level_domains#Africa
     *
     * @var array
     */
    protected static $tld = [
        'ac.za', 'africa', 'agric.za', 'capetown', 'co.za', 'co.za', 'co.za', 'co.za', 'com', 'com',
        'durban', 'ecape.school.za', 'edu.za', 'fs.school.za', 'gov.za', 'gp.school.za', 'grondar.za',
        'joburg', 'kzn.school.za', 'law.za', 'lp.school.za', 'mil.za', 'mpm.za', 'ncape.school.za',
        'net.za', 'net', 'nis.za', 'nom.za', 'nw.school.za', 'org.za', 'school.za', 'wcape.school.za', 'web.za',
    ];
}
