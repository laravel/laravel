<?php

namespace Faker\Provider\ja_JP;

class Address extends \Faker\Provider\Address
{
    protected static $country = [
        'アフガニスタン', 'アルバニア', 'アルジェリア', 'アメリカ領サモア', 'アンドラ', 'アンゴラ', 'アンギラ', '南極大陸', 'アンティグアバーブーダ', 'アルゼンチン', 'アルメニア', 'アルバ', 'オーストラリア', 'オーストリア', 'アゼルバイジャン',
        'バハマ', 'バーレーン', 'バングラデシュ', 'バルバドス', 'ベラルーシ', 'ベルギー', 'ベリーズ', 'ベナン', 'バミューダ島', 'ブータン', 'ボリビア', 'ボスニア・ヘルツェゴビナ', 'ボツワナ', 'ブーベ島', 'ブラジル', 'イギリス領インド洋地域', 'イギリス領ヴァージン諸島', 'ブルネイ', 'ブルガリア', 'ブルキナファソ', 'ブルンジ',
        'カンボジア', 'カメルーン', 'カナダ', 'カーボベルデ', 'ケイマン諸島', '中央アフリカ共和国', 'チャド', 'チリ', '中国', 'クリスマス島', 'ココス諸島', 'コロンビア', 'コモロ', 'コンゴ共和国', 'クック諸島', 'コスタリカ', 'コートジボワール', 'クロアチア', 'キューバ', 'キプロス共和国', 'チェコ共和国',
        'デンマーク', 'ジブチ共和国', 'ドミニカ国', 'ドミニカ共和国',
        'エクアドル', 'エジプト', 'エルサルバドル', '赤道ギニア共和国', 'エリトリア', 'エストニア', 'エチオピア',
        'フェロー諸島', 'フォークランド諸島', 'フィジー共和国', 'フィンランド', 'フランス', 'フランス領ギアナ', 'フランス領ポリネシア', 'フランス領極南諸島',
        'ガボン', 'ガンビア', 'ジョージア', 'ドイツ', 'ガーナ', 'ジブラルタル', 'ギリシャ', 'グリーンランド', 'グレナダ', 'グアドループ', 'グアム', 'グアテマラ', 'ガーンジー', 'ギニア', 'ギニアビサウ', 'ガイアナ',
        'ハイチ', 'ハード島とマクドナルド諸島', 'バチカン市国', 'ホンジュラス', '香港', 'ハンガリー',
        'アイスランド', 'インド', 'インドネシア', 'イラン', 'イラク', 'アイルランド共和国', 'マン島', 'イスラエル', 'イタリア',
        'ジャマイカ', '日本', 'ジャージー島', 'ヨルダン',
        'カザフスタン', 'ケニア', 'キリバス', '朝鮮', '韓国', 'クウェート', 'キルギス共和国',
        'ラオス人民民主共和国', 'ラトビア', 'レバノン', 'レソト', 'リベリア', 'リビア国', 'リヒテンシュタイン', 'リトアニア', 'ルクセンブルク',
        'マカオ', 'マケドニア共和国', 'マダガスカル', 'マラウィ', 'マレーシア', 'モルディブ', 'マリ', 'マルタ共和国', 'マーシャル諸島', 'マルティニーク', 'モーリタニア・イスラム共和国', 'モーリシャス', 'マヨット', 'メキシコ', 'ミクロネシア連邦', 'モルドバ共和国', 'モナコ公国', 'モンゴル', 'モンテネグロ共和国', 'モントセラト', 'モロッコ', 'モザンビーク', 'ミャンマー',
        'ナミビア', 'ナウル', 'ネパール', 'オランダ領アンティル', 'オランダ', 'ニューカレドニア', 'ニュージーランド', 'ニカラグア', 'ニジェール', 'ナイジェリア', 'ニース', 'ノーフォーク島', '北マリアナ諸島', 'ノルウェー',
        'オマーン',
        'パキスタン', 'パラオ', 'パレスチナ自治区', 'パナマ', 'パプアニューギニア', 'パラグアイ', 'ペルー', 'フィリピン', 'ピトケアン諸島', 'ポーランド', 'ポルトガル', 'プエルトリコ',
        'カタール',
        'レユニオン', 'ルーマニア', 'ロシア', 'ルワンダ',
        'サン・バルテルミー島', 'セントヘレナ', 'セントクリストファー・ネイビス連邦', 'セントルシア', 'セント・マーチン島', 'サンピエール島・ミクロン島', 'セントビンセント・グレナディーン', 'サモア', 'サンマリノ', 'サントメプリンシペ', 'サウジアラビア', 'セネガル', 'セルビア', 'セイシェル', 'シエラレオネ', 'シンガポール', 'スロバキア', 'スロベニア', 'ソロモン諸島', 'ソマリア', '南アフリカ共和国', 'サウスジョージア・サウスサンドウィッチ諸島', 'スペイン', 'スリランカ', 'スーダン', 'スリナム', 'スヴァールバル諸島およびヤンマイエン島', 'スワジランド王国', 'スウェーデン', 'スイス', 'シリア',
        '台湾', 'タジキスタン共和国', 'タンザニア', 'タイ', '東ティモール', 'トーゴ', 'トケラウ', 'トンガ', 'トリニダード・トバゴ', 'チュニジア', 'トルコ', 'トルクメニスタン', 'タークス・カイコス諸島', 'ツバル',
        'ウガンダ', 'ウクライナ', 'アラブ首長国連邦', 'イギリス', 'アメリカ合衆国', '合衆国領有小離島', 'アメリカ領ヴァージン諸島', 'ウルグアイ', 'ウズベキスタン',
        'バヌアツ', 'ベネズエラ', 'ベトナム',
        'ウォリス・フツナ', '西サハラ',
        'イエメン',
        'ザンビア', 'ジンバブエ',
    ];
    protected static $prefecture = [
        '北海道',
        '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
        '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
        '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県',
        '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県',
        '鳥取県', '島根県', '岡山県', '広島県', '山口県',
        '徳島県', '香川県', '愛媛県', '高知県',
        '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県',
        '沖縄県',
    ];
    protected static $ward = ['中央', '北', '東', '南', '西'];

    protected static $citySuffix = ['市'];
    protected static $wardSuffix = ['区'];
    protected static $streetSuffix = ['町'];

    protected static $postcodeFormats = ['{{postcode1}}{{postcode2}}'];
    protected static $cityFormats = [
        '{{lastName}}{{citySuffix}}',
    ];
    protected static $streetNameFormats = [
        '{{lastName}}{{streetSuffix}}',
    ];
    protected static $streetAddressFormats = [
        '{{streetName}}{{lastName}}{{areaNumber}}-{{areaNumber}}-{{areaNumber}}',
    ];
    protected static $addressFormats = [
        '{{postcode}}  {{prefecture}}{{city}}{{ward}}{{streetAddress}}',
        '{{postcode}}  {{prefecture}}{{city}}{{ward}}{{streetAddress}} {{secondaryAddress}}',
    ];
    protected static $secondaryAddressFormats = [
        'ハイツ{{lastName}}{{buildingNumber}}号',
        'コーポ{{lastName}}{{buildingNumber}}号',
    ];

    /**
     * @example 111
     */
    public static function postcode1()
    {
        return self::numberBetween(100, 999);
    }

    /**
     * @example 2222
     */
    public static function postcode2()
    {
        return self::numberBetween(1000, 9999);
    }

    /**
     * @example 1112222
     */
    public static function postcode()
    {
        $postcode1 = static::postcode1();
        $postcode2 = static::postcode2();

        return $postcode1 . $postcode2;
    }

    /**
     * @example '東京都'
     */
    public static function prefecture()
    {
        return static::randomElement(static::$prefecture);
    }

    /**
     * @example '北区'
     */
    public static function ward()
    {
        $ward = static::randomElement(static::$ward);
        $suffix = static::randomElement(static::$wardSuffix);

        return $ward . $suffix;
    }

    /**
     * 丁、番地、号
     *
     * @return int
     */
    public static function areaNumber()
    {
        return self::numberBetween(1, 10);
    }

    public static function buildingNumber()
    {
        return (string) self::numberBetween(101, 110);
    }

    public function secondaryAddress()
    {
        $format = static::randomElement(static::$secondaryAddressFormats);

        return $this->generator->parse($format);
    }
}
