<?php

namespace Faker\Provider\fa_IR;

class Internet extends \Faker\Provider\Internet
{
    protected static $userNameFormats = [
        '{{lastNameAscii}}.{{firstNameAscii}}',
        '{{firstNameAscii}}.{{lastNameAscii}}',
        '{{firstNameAscii}}##',
        '?{{lastNameAscii}}',
    ];

    protected static $safeEmailTld = [
        'com', 'ir', 'me', 'net', 'org',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/.ir
     */
    protected static $tld = [
        'biz', 'com', 'info', 'ac.ir', 'sch.ir', 'co.ir', 'ir', 'net', 'org',
    ];

    /**
     * @see http://en.wikipedia.org/wiki/Category:Iranian-language_surnames
     */
    protected static $lastNameAscii = [
        'abdullahi', 'abbasi', 'abedini', 'ahadi', 'ahmadi', 'akbari', 'alizadeh', 'asadi',
        'baraghani', 'babaei',
        'chamran',
        'ebrahimi', 'esfahani',
        'fanaei', 'farahani', 'farsi', 'fekri',
        'ghasemi', 'ghorbani',
        'hamadani', 'hamidi', 'heidari', 'hijazi', 'husseini',
        'jahanbani', 'jalili', 'jamshidi', 'javadi',
        'kadivar', 'karimi', 'kashani', 'kazmi', 'khadem', 'khalaji', 'khomeini', 'khorsandi',
        'mahdavi', 'mahmoudi', 'mahmoudieh', 'majidi', 'mazanderani', 'mirzaei', 'mokri', 'mohammady', 'mousavi',
        'nabavi', 'naceri', 'nafisi', 'najafi', 'nalci', 'namazi', 'namdar', 'nariman', 'nazari', 'nili', 'norouzi',
        'pahlavi', 'paria', 'pashaei', 'pejman',
        'qazwini',
        'rahimi', 'rahmani', 'rajaei', 'ramadani', 'rashidi', 'rezaei',
        'salari', 'salehi', 'salemi', 'shahbazi', 'shahriari', 'shahidi', 'shirazi', 'shojaei', 'soleimani', 'soomekh', 'soroush',
        'tabatabaei', 'talebi', 'tousi',
        'yazdani', 'yazdi', 'yousefi',
        'zandi', 'zare',
    ];

    /**
     * @see http://en.wikipedia.org/wiki/Persian_name
     */
    protected static $firstNameAscii = [
        'abbas', 'afshin', 'ahmad', 'ali', 'amir', 'anahita', 'anousheh', 'arash', 'ardeshir', 'arezu', 'aria', 'arian', 'arman', 'ashraf', 'atoosa', 'azadeh', 'azar',
        'babak', 'bahar', 'bahare', 'bardia', 'behnaz', 'benyamin', 'bita',
        'dalir', 'dariush', 'davoud', 'donya',
        'ebrahim', 'ehsan', 'eskandar', 'esmaeel',
        'farangis', 'farbod', 'farhad', 'farideh', 'farnaz', 'farrokh', 'farshid', 'farzaneh', 'fateme', 'fereshteh',
        'garshasp',
        'hanie', 'hashem', 'hirbod', 'hoda', 'hormoz', 'hossein',
        'jaleh', 'jamshid', 'javad',
        'kamran', 'karim', 'kasra', 'katayoun', 'kazem', 'khorshid', 'khosrow', 'kiana', 'kiarash', 'kourosh',
        'laleh', 'leila',
        'mahdi', 'mahmoud', 'mahshid', 'majid', 'manuchehr', 'marjan', 'maryam', 'marzban', 'massoud', 'mazdak', 'maziar', 'mehran', 'mehrdad', 'meysam', 'milad', 'mina', 'mithra', 'mohammad', 'mohsen', 'musa',
        'nasrin', 'nazanin', 'niloufar',
        'omid',
        'parastoo', 'parisa', 'parsa', 'parvin', 'parviz', 'payam', 'payvand', 'pedram', 'piruz', 'pouria',
        'ramin', 'reyhan', 'reza', 'roksaneh', 'rostam', 'roxana', 'roya',
        'sahar', 'saman', 'samir', 'sara', 'sassan', 'sepehr', 'sepideh', 'shahin', 'shahryar', 'shapour', 'shervin', 'shirin', 'simin', 'soheila', 'sohrab', 'soraya', 'soroush',
        'tara', 'taraneh', 'turan',
        'vahid',
        'yaghoub', 'yahya', 'yasamin', 'yasaman', 'younes', 'yousef',
        'zahra', 'zarine', 'zeynab', 'zhila',
    ];

    public static function lastNameAscii()
    {
        return static::randomElement(static::$lastNameAscii);
    }

    public static function firstNameAscii()
    {
        return static::randomElement(static::$firstNameAscii);
    }

    /**
     * @example 'ali.rezaei'
     */
    public function userName()
    {
        $format = static::randomElement(static::$userNameFormats);

        return static::bothify($this->generator->parse($format));
    }

    /**
     * @example 'ahmad.ir'
     */
    public function domainName()
    {
        return static::randomElement(static::$lastNameAscii) . '.' . $this->tld();
    }
}
