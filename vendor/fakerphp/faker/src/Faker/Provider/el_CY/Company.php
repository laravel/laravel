<?php

namespace Faker\Provider\el_CY;

class Company extends \Faker\Provider\Company
{
    protected static $companySuffix = [
        'ΛΤΔ',
        'Δημόσια εταιρεία',
        '& Υιοι',
        '& ΣΙΑ',
    ];

    protected static $formats = [
        '{{lastName}} {{companySuffix}}',
        '{{lastName}}-{{lastName}}',
    ];
}
