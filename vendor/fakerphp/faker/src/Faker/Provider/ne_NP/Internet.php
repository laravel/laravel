<?php

namespace Faker\Provider\ne_NP;

class Internet extends \Faker\Provider\Internet
{
    protected static $freeEmailDomain = ['gmail.com', 'yahoo.com', 'hotmail.com'];
    protected static $tld = ['com', 'com', 'com', 'net', 'org'];

    protected static $emailFormats = [
        '{{userName}}@{{domainName}}',
        '{{userName}}@{{domainName}}',
        '{{userName}}@{{freeEmailDomain}}',
        '{{userName}}@{{domainName}}.np',
        '{{userName}}@{{domainName}}.np',
        '{{userName}}@{{domainName}}.np',
    ];

    protected static $urlFormats = [
        'http://www.{{domainName}}.np/',
        'http://www.{{domainName}}.np/',
        'http://{{domainName}}.np/',
        'http://{{domainName}}.np/',
        'http://www.{{domainName}}.np/{{slug}}',
        'http://www.{{domainName}}.np/{{slug}}.html',
        'http://{{domainName}}.np/{{slug}}',
        'http://{{domainName}}.np/{{slug}}',
        'http://{{domainName}}/{{slug}}.html',
        'http://www.{{domainName}}/',
        'http://{{domainName}}/',
    ];
}
