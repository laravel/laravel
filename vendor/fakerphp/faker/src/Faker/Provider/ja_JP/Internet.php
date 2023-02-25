<?php

namespace Faker\Provider\ja_JP;

class Internet extends \Faker\Provider\Internet
{
    protected static $userNameFormats = [
        '{{lastNameAscii}}.{{firstNameAscii}}',
        '{{firstNameAscii}}.{{lastNameAscii}}',
        '{{firstNameAscii}}##',
        '?{{lastNameAscii}}',
    ];

    protected static $safeEmailTld = [
        'org', 'com', 'net', 'jp', 'jp', 'jp',
    ];

    protected static $freeEmailDomain = [
        'gmail.com', 'yahoo.co.jp', 'hotmail.co.jp', 'mail.goo.ne.jp',
    ];

    protected static $tld = [
        'com', 'com', 'com', 'biz', 'info', 'net', 'org', 'jp', 'jp', 'jp',
    ];

    /**
     * {@link} http://dic.nicovideo.jp/a/%E6%97%A5%E6%9C%AC%E3%81%AE%E8%8B%97%E5%AD%97%28%E5%90%8D%E5%AD%97%29%E3%81%AE%E4%B8%80%E8%A6%A7
     */
    protected static $lastNameAscii = [
        'aota', 'aoyama', 'ishida', 'idaka', 'ito', 'uno', 'ekoda', 'ogaki',
        'kato', 'kanou', 'kijima', 'kimura', 'kiriyama', 'kudo', 'koizumi', 'kobayashi', 'kondo',
        'saito', 'sakamoto', 'sasaki', 'sato', 'sasada', 'suzuki', 'sugiyama',
        'takahashi', 'tanaka', 'tanabe', 'tsuda',
        'nakajima', 'nakamura', 'nagisa', 'nakatsugawa', 'nishinosono', 'nomura',
        'harada', 'hamada', 'hirokawa', 'fujimoto',
        'matsumoto', 'miyake', 'miyazawa', 'murayama',
        'yamagishi', 'yamaguchi', 'yamada', 'yamamoto', 'yoshida', 'yoshimoto',
        'wakamatsu', 'watanabe',
    ];

    /**
     * {@link} http://dic.nicovideo.jp/a/%E6%97%A5%E6%9C%AC%E4%BA%BA%E3%81%AE%E5%90%8D%E5%89%8D%E4%B8%80%E8%A6%A7
     * {@link} http://www.meijiyasuda.co.jp/enjoy/ranking/
     */
    protected static $firstNameAscii = [
        'akira', 'atsushi', 'osamu',
        'akemi', 'asuka',
        'kazuya', 'kyosuke', 'kenichi',
        'kaori', 'kana', 'kumiko',
        'shuhei', 'shota', 'jun', 'soutaro',
        'sayuri', 'satomi',
        'taichi', 'taro', 'takuma', 'tsubasa', 'tomoya',
        'chiyo',
        'naoki', 'naoto',
        'naoko', 'nanami',
        'hideki', 'hiroshi',
        'hanako', 'haruka',
        'manabu', 'mitsuru', 'minoru',
        'maaya', 'mai', 'mikako', 'miki', 'momoko',
        'yuki', 'yuta', 'yasuhiro', 'youichi', 'yosuke',
        'yui', 'yumiko', 'yoko',
        'ryosuke', 'ryohei', 'rei',
        'rika',
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
     * @example 'suzuki.taro'
     */
    public function userName()
    {
        $format = static::randomElement(static::$userNameFormats);

        return static::bothify($this->generator->parse($format));
    }

    /**
     * @example 'yamada.jp'
     */
    public function domainName()
    {
        return static::randomElement(static::$lastNameAscii) . '.' . $this->tld();
    }
}
