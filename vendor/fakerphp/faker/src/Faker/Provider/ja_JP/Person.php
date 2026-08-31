<?php

namespace Faker\Provider\ja_JP;

class Person extends \Faker\Provider\Person
{
    protected static $maleNameFormats = [
        '{{lastName}} {{firstNameMale}}',
    ];

    protected static $femaleNameFormats = [
        '{{lastName}} {{firstNameFemale}}',
    ];

    /**
     * {@link} http://dic.nicovideo.jp/a/%E6%97%A5%E6%9C%AC%E4%BA%BA%E3%81%AE%E5%90%8D%E5%89%8D%E4%B8%80%E8%A6%A7
     * {@link} http://www.meijiyasuda.co.jp/enjoy/ranking/
     */
    protected static $firstNameMale = [
        '晃', '篤司', '治', '和也', '京助', '健一', '修平', '翔太', '淳', '聡太郎', '太一', '太郎', '拓真', '翼', '智也',
        '直樹', '直人', '英樹', '浩', '学', '充', '稔', '裕樹', '裕太', '康弘', '陽一', '洋介', '亮介', '涼平', '零',
    ];

    /**
     * {@link} http://dic.nicovideo.jp/a/%E6%97%A5%E6%9C%AC%E4%BA%BA%E3%81%AE%E5%90%8D%E5%89%8D%E4%B8%80%E8%A6%A7
     * {@link} http://www.meijiyasuda.co.jp/enjoy/ranking/
     */
    protected static $firstNameFemale = [
        '明美', 'あすか', '香織', '加奈', 'くみ子', 'さゆり', '知実', '千代',
        '直子', '七夏', '花子', '春香', '真綾', '舞', '美加子', '幹', '桃子', '結衣', '裕美子', '陽子', '里佳',
    ];

    /**
     * {@link} http://dic.nicovideo.jp/a/%E6%97%A5%E6%9C%AC%E3%81%AE%E8%8B%97%E5%AD%97%28%E5%90%8D%E5%AD%97%29%E3%81%AE%E4%B8%80%E8%A6%A7
     */
    protected static $lastName = [
        '青田', '青山', '石田', '井高', '伊藤', '井上', '宇野', '江古田', '大垣',
        '加藤', '加納', '喜嶋', '木村', '桐山', '工藤', '小泉', '小林', '近藤',
        '斉藤', '坂本', '佐々木', '佐藤', '笹田', '鈴木', '杉山',
        '高橋', '田中', '田辺', '津田',
        '中島', '中村', '渚', '中津川', '西之園', '野村',
        '原田', '浜田', '廣川', '藤本',
        '松本', '三宅', '宮沢', '村山',
        '山岸', '山口', '山田', '山本', '吉田', '吉本',
        '若松', '渡辺',
    ];

    protected static $firstKanaNameFormat = [
        '{{firstKanaNameMale}}',
        '{{firstKanaNameFemale}}',
    ];

    protected static $maleKanaNameFormats = [
        '{{lastKanaName}} {{firstKanaNameMale}}',
    ];

    protected static $femaleKanaNameFormats = [
        '{{lastKanaName}} {{firstKanaNameFemale}}',
    ];

    protected static $firstKanaNameMale = [
        'アキラ', 'アツシ', 'オサム', 'カズヤ', 'キョウスケ', 'ケンイチ', 'シュウヘイ', 'ショウタ', 'ジュン', 'ソウタロウ',
        'タイチ', 'タロウ', 'タクマ', 'ツバサ', 'トモヤ', 'ナオキ', 'ナオト', 'ヒデキ', 'ヒロシ', 'マナブ', 'ミツル', 'ミノル',
        'ユウキ', 'ユウタ', 'ヤスヒロ', 'ヨウイチ', 'ヨウスケ', 'リョウスケ', 'リョウヘイ', 'レイ',
    ];

    protected static $firstKanaNameFemale = [
        'アケミ', 'アスカ', 'カオリ', 'カナ', 'クミコ', 'サユリ', 'サトミ', 'チヨ',
        'ナオコ', 'ナナミ', 'ハナコ', 'ハルカ', 'マアヤ', 'マイ', 'ミカコ', 'ミキ', 'モモコ', 'ユイ', 'ユミコ', 'ヨウコ', 'リカ',
    ];

    protected static $lastKanaName = [
        'アオタ', 'アオヤマ', 'イシダ', 'イダカ', 'イトウ', 'ウノ', 'エコダ', 'オオガキ',
        'カノウ', 'カノウ', 'キジマ', 'キムラ', 'キリヤマ', 'クドウ', 'コイズミ', 'コバヤシ', 'コンドウ',
        'サイトウ', 'サカモト', 'ササキ', 'サトウ', 'ササダ', 'スズキ', 'スギヤマ',
        'タカハシ', 'タナカ', 'タナベ', 'ツダ',
        'ナカジマ', 'ナカムラ', 'ナギサ', 'ナカツガワ', 'ニシノソノ', 'ノムラ',
        'ハラダ', 'ハマダ', 'ヒロカワ', 'フジモト',
        'マツモト', 'ミヤケ', 'ミヤザワ', 'ムラヤマ',
        'ヤマギシ', 'ヤマグチ', 'ヤマダ', 'ヤマモト', 'ヨシダ', 'ヨシモト',
        'ワカマツ', 'ワタナベ',
    ];

    /**
     * @param string|null $gender 'male', 'female' or null for any
     *
     * @return string
     *
     * @example 'アオタ アキラ'
     */
    public function kanaName($gender = null)
    {
        if ($gender === static::GENDER_MALE) {
            $format = static::randomElement(static::$maleKanaNameFormats);
        } elseif ($gender === static::GENDER_FEMALE) {
            $format = static::randomElement(static::$femaleKanaNameFormats);
        } else {
            $format = static::randomElement(array_merge(static::$maleKanaNameFormats, static::$femaleKanaNameFormats));
        }

        return $this->generator->parse($format);
    }

    /**
     * @param string|null $gender 'male', 'female' or null for any
     *
     * @return string
     *
     * @example 'アキラ'
     */
    public function firstKanaName($gender = null)
    {
        if ($gender === static::GENDER_MALE) {
            return static::firstKanaNameMale();
        }

        if ($gender === static::GENDER_FEMALE) {
            return static::firstKanaNameFemale();
        }

        return $this->generator->parse(static::randomElement(static::$firstKanaNameFormat));
    }

    /**
     * @example 'アキラ'
     */
    public static function firstKanaNameMale()
    {
        return static::randomElement(static::$firstKanaNameMale);
    }

    /**
     * @example 'アケミ'
     */
    public static function firstKanaNameFemale()
    {
        return static::randomElement(static::$firstKanaNameFemale);
    }

    /**
     * @example 'アオタ'
     */
    public static function lastKanaName()
    {
        return static::randomElement(static::$lastKanaName);
    }
}
