<?php

namespace Faker\Provider\zh_TW;

class Person extends \Faker\Provider\Person
{
    /**
     * @see https://zh.wikipedia.org/wiki/%E4%B8%AD%E8%8F%AF%E6%B0%91%E5%9C%8B%E5%9C%8B%E6%B0%91%E8%BA%AB%E5%88%86%E8%AD%89
     */
    public static $idBirthplaceCode = [
        'A' => 10,
        'B' => 11,
        'C' => 12,
        'D' => 13,
        'E' => 14,
        'F' => 15,
        'G' => 16,
        'H' => 17,
        'I' => 34,
        'J' => 18,
        'K' => 19,
        'M' => 21,
        'N' => 22,
        'O' => 35,
        'P' => 23,
        'Q' => 24,
        'T' => 27,
        'U' => 28,
        'V' => 29,
        'W' => 32,
        'X' => 30,
        'Z' => 33,
    ];

    /**
     * @see https://zh.wikipedia.org/wiki/%E4%B8%AD%E8%8F%AF%E6%B0%91%E5%9C%8B%E5%9C%8B%E6%B0%91%E8%BA%AB%E5%88%86%E8%AD%89
     */
    public static $idDigitValidator = [1, 9, 8, 7, 6, 5, 4, 3, 2, 1, 1];

    protected static $maleNameFormats = [
        '{{lastName}}{{firstNameMale}}',
    ];

    protected static $femaleNameFormats = [
        '{{lastName}}{{firstNameFemale}}',
    ];

    protected static $titleMale = ['先生', '博士', '教授'];
    protected static $titleFemale = ['小姐', '太太', '博士', '教授'];

    /**
     * @see http://zh.wikipedia.org/wiki/%E7%99%BE%E5%AE%B6%E5%A7%93
     */
    protected static $lastName = [
        '趙', '錢', '孫', '李', '周', '吳', '鄭', '王', '馮',
        '陳', '褚', '衛', '蔣', '沈', '韓', '楊', '朱', '秦',
        '尤', '許', '何', '呂', '施', '張', '孔', '曹', '嚴',
        '華', '金', '魏', '陶', '姜', '戚', '謝', '鄒', '喻',
        '柏', '水', '竇', '章', '雲', '蘇', '潘', '葛',
        '奚', '范', '彭', '郎', '魯', '韋', '昌', '馬',
        '苗', '鳳', '花', '方', '俞', '任', '袁', '柳',
        '酆', '鮑', '史', '唐', '費', '廉', '岑', '薛',
        '雷', '賀', '倪', '湯', '滕', '殷', '羅', '畢',
        '郝', '鄔', '安', '常', '樂', '于', '時', '傅',
        '皮', '卞', '齊', '康', '伍', '余', '元', '卜',
        '顧', '孟', '平', '黃', '和', '穆', '蕭', '尹',
        '姚', '邵', '湛', '汪', '祁', '毛', '禹', '狄',
        '米', '貝', '明', '臧', '計', '伏', '成', '戴',
        '談', '宋', '茅', '龐', '熊', '紀', '舒', '屈',
        '項', '祝', '董', '梁', '杜', '阮', '藍', '閔',
        '席', '季', '麻', '強', '賈', '路', '婁', '危',
        '江', '童', '顏', '郭', '梅', '盛', '林', '刁',
        '鍾', '徐', '丘', '駱', '高', '夏', '蔡', '田',
        '樊', '胡', '凌', '霍', '虞', '萬', '支', '柯',
        '昝', '管', '盧', '莫', '經', '房', '裘', '繆',
        '干', '解', '應', '宗', '丁', '宣', '賁', '鄧',
        '郁', '單', '杭', '洪', '包', '諸', '左', '石',
        '崔', '吉', '鈕', '龔', '程', '嵇', '邢', '滑',
        '裴', '陸', '榮', '翁', '荀', '羊', '於', '惠',
        '甄', '麴', '家', '封', '芮', '羿', '儲', '靳',
        '汲', '邴', '糜', '松', '井', '段', '富', '巫',
        '烏', '焦', '巴', '弓', '牧', '隗', '山', '谷',
        '車', '侯', '宓', '蓬', '全', '郗', '班', '仰',
        '秋', '仲', '伊', '宮', '甯', '仇', '欒', '暴',
        '甘', '鈄', '厲', '戎', '祖', '武', '符', '劉',
        '景', '詹', '束', '龍', '葉', '幸', '司', '韶',
        '郜', '黎', '薊', '薄', '印', '宿', '白', '懷',
        '蒲', '邰', '從', '鄂', '索', '咸', '籍', '賴',
        '卓', '藺', '屠', '蒙', '池', '喬', '陰', '鬱',
        '胥', '能', '蒼', '雙', '聞', '莘', '黨', '翟',
        '譚', '貢', '勞', '逄', '姬', '申', '扶', '堵',
        '冉', '宰', '酈', '雍', '郤', '璩', '桑', '桂',
        '濮', '牛', '壽', '通', '邊', '扈', '燕', '冀',
        '郟', '浦', '尚', '農', '溫', '別', '莊', '晏',
        '柴', '瞿', '閻', '充', '慕', '連', '茹', '習',
        '宦', '艾', '魚', '容', '向', '古', '易', '慎',
        '戈', '廖', '庾', '終', '暨', '居', '衡', '步',
        '都', '耿', '滿', '弘', '匡', '國', '文', '寇',
        '廣', '祿', '闕', '東', '歐', '殳', '沃', '利',
        '蔚', '越', '夔', '隆', '師', '鞏', '厙', '聶',
        '晁', '勾', '敖', '融', '冷', '訾', '辛', '闞',
        '那', '簡', '饒', '空', '曾', '毋', '沙', '乜',
        '養', '鞠', '須', '豐', '巢', '關', '蒯', '相',
        '查', '后', '荊', '紅', '游', '竺', '權', '逯',
        '蓋', '益', '桓', '公', '万俟', '司馬', '上官',
        '歐陽', '夏侯', '諸葛', '聞人', '東方', '赫連',
        '皇甫', '尉遲', '公羊', '澹臺', '公冶', '宗政',
        '濮陽', '淳于', '單于', '太叔', '申屠', '公孫',
        '仲孫', '軒轅', '令狐', '鍾離', '宇文', '長孫',
        '慕容', '鮮于', '閭丘', '司徒', '司空', '亓官',
        '司寇', '仉', '督', '子車', '顓孫', '端木', '巫馬',
        '公西', '漆雕', '樂正', '壤駟', '公良', '拓跋',
        '夾谷', '宰父', '穀梁', '晉', '楚', '閆', '法',
        '汝', '鄢', '涂', '欽', '段干', '百里', '東郭',
        '南門', '呼延', '歸', '海', '羊舌', '微生', '岳',
        '帥', '緱', '亢', '況', '後', '有', '琴', '梁丘',
        '左丘', '東門', '西門', '商', '牟', '佘', '佴',
        '伯', '賞', '南宮', '墨', '哈', '譙', '笪', '年',
        '愛', '陽', '佟', '第五', '言', '福',
    ];

    /**
     * @see http://technology.chtsai.org/namefreq/
     */
    protected static $characterMale = [
        '佳', '俊', '信', '偉', '傑', '冠', '君', '哲',
        '嘉', '威', '宇', '安', '宏', '宗', '宜', '家',
        '庭', '廷', '建', '彥', '心', '志', '思', '承',
        '文', '柏', '樺', '瑋', '穎', '美', '翰', '華',
        '詩', '豪', '賢', '軒', '銘', '霖',
    ];

    protected static $characterFemale = [
        '伶', '佩', '佳', '依', '儀', '冠', '君', '嘉',
        '如', '娟', '婉', '婷', '安', '宜', '家', '庭',
        '心', '思', '怡', '惠', '慧', '文', '欣', '涵',
        '淑', '玲', '珊', '琪', '琬', '瑜', '穎', '筑',
        '筱', '美', '芬', '芳', '華', '萍', '萱', '蓉',
        '詩', '貞', '郁', '鈺', '雅', '雯', '靜', '馨',
    ];

    public static function randomName($pool, $n)
    {
        $name = '';

        for ($i = 0; $i < $n; ++$i) {
            $name .= static::randomElement($pool);
        }

        return $name;
    }

    public static function firstNameMale()
    {
        return static::randomName(static::$characterMale, self::numberBetween(1, 2));
    }

    public static function firstNameFemale()
    {
        return static::randomName(static::$characterFemale, self::numberBetween(1, 2));
    }

    public static function suffix()
    {
        return '';
    }

    /**
     * @param string $gender Person::GENDER_MALE || Person::GENDER_FEMALE
     *
     * @see https://en.wikipedia.org/wiki/National_Identification_Card_(Republic_of_China)
     *
     * @return string Length 10 alphanumeric characters, begins with 1 latin character (birthplace),
     *                1 number (gender) and then 8 numbers (the last one is check digit).
     */
    public function personalIdentityNumber($gender = null)
    {
        $birthPlace = self::randomKey(self::$idBirthplaceCode);
        $birthPlaceCode = self::$idBirthplaceCode[$birthPlace];

        $gender = ($gender != null) ? $gender : self::randomElement([self::GENDER_FEMALE, self::GENDER_MALE]);
        $genderCode = ($gender === self::GENDER_MALE) ? 1 : 2;

        $randomNumberCode = self::randomNumber(7, true);

        $codes = str_split($birthPlaceCode . $genderCode . $randomNumberCode);
        $total = 0;

        foreach ($codes as $key => $code) {
            $total += $code * self::$idDigitValidator[$key];
        }

        $checkSumDigit = 10 - ($total % 10);

        if ($checkSumDigit == 10) {
            $checkSumDigit = 0;
        }

        return $birthPlace . $genderCode . $randomNumberCode . $checkSumDigit;
    }
}
