<?php

namespace Faker\Provider\lt_LT;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{companySuffix}} {{lastNameMale}}',
        '{{companySuffix}} {{lastNameMale}} ir {{lastNameMale}}',
        '{{companySuffix}} "{{lastNameMale}} ir {{lastNameMale}}"',
        '{{companySuffix}} "{{lastNameMale}}"',
    ];

    protected static $companySuffix = ['UAB', 'AB', 'IĮ', 'MB', 'VŠĮ'];
}
