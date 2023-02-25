<?php

namespace Faker\Provider\he_IL;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{lastName}} {{companySuffix}}',
        '{{lastName}} את {{lastName}} {{companySuffix}}',
        '{{lastName}} ו{{lastName}}',
    ];

    protected static $companySuffix = ['בע"מ', 'ובניו', 'סוכנויות', 'משווקים'];
}
