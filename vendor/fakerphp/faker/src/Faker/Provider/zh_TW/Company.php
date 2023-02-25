<?php

namespace Faker\Provider\zh_TW;

class Company extends \Faker\Provider\Company
{
    protected static $companyEnSuffix = [
        'Inc', 'and Sons', 'LLC', 'Group', 'PLC', 'Ltd',
    ];

    protected static $companyEnFormats = [
        '{{lastNameEn}} {{companyEnSuffix}}',
        '{{lastNameEn}}-{{lastNameEn}}',
        '{{lastNameEn}}, {{lastNameEn}} and {{lastNameEn}}',
    ];

    protected static $formats = [
        '{{companyPrefix}}{{companyModifier}}',
        '{{companyPrefix}}{{companySuffix}}',
        '{{companyPrefix}}{{companyModifier}}{{CompanySuffix}}',
    ];

    /**
     * @see http://zh.wikipedia.org/zh-tw/Category:%E5%8F%B0%E7%81%A3%E5%85%AC%E5%8F%B8%E5%88%97%E8%A1%A8
     */
    protected static $companyPrefix = [
        '乾坤', '亞神', '光譜', '典選', '凱基', '前衛花園',
        '創見', '友訊', '台達', '合勤', '喜瑪拉雅', '喬山',
        '多利安', '大國', '大宇', '太陽', '太陽動力', '宏碁',
        '宏達', '小白兔', '巨大', '巨室', '康師傅', '延伸',
        '弘煜', '彎的', '擎天', '新亞洲', '旺旺', '昱泉',
        '智冠', '未來', '松崗', '正新', '洧誠', '海蝶',
        '添翼創越', '滾石', '王品', '當然', '相信', '睛水',
        '研華', '福茂', '種子', '米樂士', '紅元素', '紅心辣椒',
        '統一', '美利達', '美妙', '美樂帝', '群石', '老鷹',
        '聯強', '芮河', '英特衛', '茂為', '華特', '華研',
        '華碩', '華義', '角頭', '訊連', '豐華', '豪客', '豪記',
        '貴族', '趨勢', '遊戲橘子', '野火樂集', '金革',
        '銀魚', '阿爾發', '風和日麗', '風潮', '風雲',
    ];
    protected static $companyModifier = [
        '科技', '電腦', '國際', '電子', '娛樂', '音樂', '唱片',
        '證券', '集團', '企業', '機械', '工業', '文化', '汽車',
        '百貨', '酒店', '數位',
    ];
    protected static $companySuffix = [
        '公司', '有限公司', '股份有限公司',
    ];
    /**
     * @see https://zh-tw.facebook.com/notes/%E7%8E%A9%E5%BB%A3%E5%91%8Aplaying-with-advertising/%E5%9B%9E%E9%A1%A7%E8%87%BA%E7%81%A3%E6%AD%B7%E5%B1%86%E5%BB%A3%E5%91%8A%E9%87%91%E5%8F%A5%E7%8D%8E/294008963985215
     */
    protected static $catchPhrase = [
        'Everything\'s ok',
        'Just call me be happy',
        'Keep Walking',
        'NOKIA相信科技始終來自於人性',
        'PayEasy，陪你Shopping一輩子',
        'Play不累',
        'The city never sleeps',
        'Trust me, you can make it!',
        'We are family',
        'We share',
        'You A.S.O beautiful',
        '一人吃，兩人補',
        '一定要幸福哦！',
        '一把抵兩把，何需瑪麗亞？！',
        '一次買好，就是頂好',
        '一步一腳印，大家愛台灣',
        '一段話，感動了一幅畫！',
        '一點一點 累積更好的自己',
        '三不五時，愛要及時',
        '三餐老是在外，人人叫我老外！',
        '不只辦公室，掌握更多事',
        '不在乎天長地久，只在乎曾經擁有',
        '不在辦公室，也能辦公事',
        '不平凡的平凡大眾',
        '不想分開 就永遠在一起吧',
        '不該愛的，趁早換',
        '不過～不過～，一定測不過',
        '世事難料，安泰比較好',
        '世事難料，對人要更好',
        '世界上最重要的一部車是爸爸的肩膀',
        '乎乾啦！',
        '人生30財開始',
        '什麼最青？',
        '什麼都有，什麼都賣，什麼都不奇怪！',
        '今天心情幾？',
        '他傻瓜、你聰明',
        '他捉得住我',
        '你未必出類拔萃 但肯定與眾不同',
        '你的愛，是孩子最好的示範',
        '你講台語嘛也通',
        '便宜一樣有好貨',
        '係金A！',
        '信任，帶來新幸福',
        '信義房屋，信任，帶來新幸福',
        '做你自己才叫乖，做你的乖乖！',
        '做自己 自己做',
        '傻瓜鏡片,聰明選擇',
        'お元気ですか？',
        '全國電子 足感心ㄟ',
        '全家就是你家',
        '再忙，也要和你喝杯咖啡',
        '別讓今天的應酬成為明天的負擔',
        '到服裝店培養氣質，到書店展示服裝',
        '化去心中那條線',
        '原來我們這麼近',
        '只有遠傳，沒有距離',
        '只要有夢，你會紅',
        '叫天天不印 Canon幫你印！',
        '可憐的舊情人，看不到我的新內衣',
        '台灣，加油！',
        '合味才會呷意',
        '啊！福氣啦！',
        '喜歡嗎? 爸爸買給你!',
        '嚕加嚕好呷',
        '回家的感覺真好',
        '因為你值得 L’ORE’AL',
        '夏天好熱 愛要趁熱',
        '多喝水沒事，沒事多喝水',
        '夜深了，打個電話回家',
        '天生超人氣，不該遭人棄',
        '女人說好，才算頂好',
        '好東西和好朋友分享',
        '好的開喜就是成功的一半！',
        '好身體，沒人敢惹你',
        '好險，有南山！',
        '孩子！我要你將來比我強！',
        '學琴的孩子不會變壞',
        '安全是回家唯一的路',
        '小而美、小而冷、小而省',
        '幸福怎能說不用',
        '心，是人生最大的戰場',
        '您真內行！',
        '想像力是你的超能力',
        '慈母心、豆腐心',
        '我不認識你，但是我謝謝你！',
        '我就是超愛 Send！',
        '我的八分新摺學！',
        '拍誰像誰，誰拍誰誰都得像誰',
        '挺立，不只挺阮 也挺恁',
        '捐血一袋，救人一命',
        '撼動天下的力量',
        '擋不住的感覺',
        '整個城市就是我的咖啡館',
        '最佳女主角換你做做看',
        '有心最要緊',
        '有書才會贏',
        '有青才敢大聲',
        '有點黏又不會太黏',
        '期待下一次，不如靠自己',
        '未來 就是現在',
        '正反，反正都很正！',
        '每一句話，都是思念',
        '沒說出口的 保誠也聽得懂',
        '現在的Nobody，未來的Somebody！',
        '生命就該浪費在美好的事物上',
        '用你想要的方式道別',
        '用大金，省大金',
        '用好心腸做好香腸',
        '用愛打敗不景氣',
        '用最愛 照顧最愛',
        '留一盞燈給最後回家的人',
        '百服寧、保護您',
        '真感情就是最好的服務',
        '知識使你更有魅力',
        '碼碼都有獎',
        '管他什麼垢，一瓶就夠',
        '紅利點數也能當飯吃',
        '紙有春風最溫柔',
        '紙要Double A 萬事都OK！',
        '給你好看',
        '給我小心點兒',
        '肝哪沒好，人生是黑白的！肝哪顧好，人生是彩色的！',
        '肝苦誰人知',
        '萬事皆可達，唯有情無價',
        '認真的女人最美麗',
        '贏甲嘸知人',
        '路，是ESCAPE走出來的',
        '這不是肯德基！',
        '這個月不會來，下個月也不會來了，以後都不會來了',
        '這是一定要的啦！',
        '這種時機，無閒也是一種幸福',
        '這種鬼地方都收得到',
        '通往成功的路，總是在施工中',
        '鑽石恆久遠，一顆永留傳',
        '關心自己，也關心別人',
        '關機是一種美德',
        '陪妳Shopping 一輩子',
        '雅芳比女人更瞭解女人',
        '青菜底呷啦',
        '靜得讓您耳根清靜',
        '馬上就會好',
    ];

    protected static $bsWords = [
        [
            '實現', '實作', '整合', '最佳化',
            '革命', '轉變', '提昇', '啟用', '指揮',
            '利用', '重現', '結合', '架構', '加強',
            '集中', '變形', '強化', '推廣', '延伸',
            '生產', '進化', '改善', '提高', '開發',
            '創造', '專注',
        ], [
            '加值', '縱向', '堅固', '全球', '在地', '領先',
            '虛擬', '動態', '完全', '成熟', '穩定', '穩健',
            '即時', '全年無休', '高效', '快速', '互動',
            '世界級', '下一代', '新一代', '無線', '無限',
            '豐富', '開源', '前端', '分散式', '無縫', '跨平臺',
        ],
        [
            '典範', '市場', '合作', '架構', '基礎平臺', '頻道',
            '焦點', '方案', '解決方案', '社群', '科技', '技術',
            '內容', '入口', '供應鍊', '介面', '系統', '頻寬',
            '模型', '網路', '使用經驗', '評量', '方法',
        ],
    ];

    public static function companyEnSuffix()
    {
        return static::randomElement(static::$companyEnSuffix);
    }

    public function companyEn()
    {
        $format = static::randomElement(static::$companyEnFormats);

        return $this->generator->parse($format);
    }

    public static function companyModifier()
    {
        return static::randomElement(static::$companyModifier);
    }

    public static function companyPrefix()
    {
        return static::randomElement(static::$companyPrefix);
    }

    public function catchPhrase()
    {
        return static::randomElement(static::$catchPhrase);
    }

    public function bs()
    {
        $result = '';

        foreach (static::$bsWords as &$word) {
            $result .= static::randomElement($word);
        }

        return $result;
    }

    /**
     * return standard VAT / Tax ID / Uniform Serial Number
     *
     * @example 28263822
     *
     * @return int
     */
    public function VAT()
    {
        return static::randomNumber(8, true);
    }
}
