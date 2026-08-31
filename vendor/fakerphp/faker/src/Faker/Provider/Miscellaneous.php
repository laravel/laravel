<?php

namespace Faker\Provider;

class Miscellaneous extends Base
{
    /**
     * @see https://en.wikipedia.org/wiki/Emoji#Unicode_blocks
     * On date of 2017-03-26
     *
     * U+1F600 - U+1F637 in Unicode Codepoint Escape Syntax
     */
    protected static $emoji = [
        "\u{1F600}", "\u{1F601}", "\u{1F602}", "\u{1F603}",
        "\u{1F604}", "\u{1F605}", "\u{1F606}", "\u{1F607}",
        "\u{1F608}", "\u{1F609}", "\u{1F60A}", "\u{1F60B}",
        "\u{1F60C}", "\u{1F60D}", "\u{1F60E}", "\u{1F60F}",
        "\u{1F610}", "\u{1F611}", "\u{1F612}", "\u{1F613}",
        "\u{1F614}", "\u{1F615}", "\u{1F616}", "\u{1F617}",
        "\u{1F618}", "\u{1F619}", "\u{1F61A}", "\u{1F61B}",
        "\u{1F61C}", "\u{1F61D}", "\u{1F61E}", "\u{1F61F}",
        "\u{1F620}", "\u{1F621}", "\u{1F622}", "\u{1F623}",
        "\u{1F624}", "\u{1F625}", "\u{1F626}", "\u{1F627}",
        "\u{1F628}", "\u{1F629}", "\u{1F62A}", "\u{1F62B}",
        "\u{1F62C}", "\u{1F62D}", "\u{1F62E}", "\u{1F62F}",
        "\u{1F630}", "\u{1F631}", "\u{1F632}", "\u{1F633}",
        "\u{1F634}", "\u{1F635}", "\u{1F636}", "\u{1F637}",
    ];

    /**
     * @see https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
     * On date of 2016-04-22
     */
    protected static $languageCode = [
        'aa', 'ab', 'ae', 'af', 'ak', 'am', 'an', 'ar', 'as', 'av',
        'ay', 'az', 'ba', 'be', 'bg', 'bh', 'bi', 'bm', 'bn', 'bo',
        'br', 'bs', 'ca', 'ce', 'ch', 'co', 'cr', 'cs', 'cu', 'cv',
        'cy', 'da', 'de', 'dv', 'dz', 'ee', 'el', 'en', 'eo', 'es',
        'et', 'eu', 'fa', 'ff', 'fi', 'fj', 'fo', 'fr', 'fy', 'ga',
        'gd', 'gl', 'gn', 'gu', 'gv', 'ha', 'he', 'hi', 'ho', 'hr',
        'ht', 'hu', 'hy', 'hz', 'ia', 'id', 'ie', 'ig', 'ii', 'ik',
        'io', 'is', 'it', 'iu', 'ja', 'jv', 'ka', 'kg', 'ki', 'kj',
        'kk', 'kl', 'km', 'kn', 'ko', 'kr', 'ks', 'ku', 'kv', 'kw',
        'ky', 'la', 'lb', 'lg', 'li', 'ln', 'lo', 'lt', 'lu', 'lv',
        'mg', 'mh', 'mi', 'mk', 'ml', 'mn', 'mr', 'ms', 'mt', 'my',
        'na', 'nb', 'nd', 'ne', 'ng', 'nl', 'nn', 'no', 'nr', 'nv',
        'ny', 'oc', 'oj', 'om', 'or', 'os', 'pa', 'pi', 'pl', 'ps',
        'pt', 'qu', 'rm', 'rn', 'ro', 'ru', 'rw', 'sa', 'sc', 'sd',
        'se', 'sg', 'si', 'sk', 'sl', 'sm', 'sn', 'so', 'sq', 'sr',
        'ss', 'st', 'su', 'sv', 'sw', 'ta', 'te', 'tg', 'th', 'ti',
        'tk', 'tl', 'tn', 'to', 'tr', 'ts', 'tt', 'tw', 'ty', 'ug',
        'uk', 'ur', 'uz', 've', 'vi', 'vo', 'wa', 'wo', 'xh', 'yi',
        'yo', 'za', 'zh', 'zu',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
     * On date of 2014-10-19
     */
    protected static $countryCode = [
        'AD', 'AE', 'AF', 'AG', 'AI', 'AL', 'AM', 'AO', 'AQ', 'AR',
        'AS', 'AT', 'AU', 'AW', 'AX', 'AZ', 'BA', 'BB', 'BD', 'BE',
        'BF', 'BG', 'BH', 'BI', 'BJ', 'BL', 'BM', 'BN', 'BO', 'BQ',
        'BR', 'BS', 'BT', 'BW', 'BY', 'BZ', 'CA', 'CC', 'CD', 'CF',
        'CG', 'CH', 'CI', 'CK', 'CL', 'CM', 'CN', 'CO', 'CR', 'CU',
        'CV', 'CW', 'CX', 'CY', 'CZ', 'DE', 'DJ', 'DK', 'DM', 'DO',
        'DZ', 'EC', 'EE', 'EG', 'EH', 'ER', 'ES', 'ET', 'FI', 'FJ',
        'FK', 'FM', 'FO', 'FR', 'GA', 'GB', 'GD', 'GE', 'GF', 'GG',
        'GH', 'GI', 'GL', 'GM', 'GN', 'GP', 'GQ', 'GR', 'GS', 'GT',
        'GU', 'GW', 'GY', 'HK', 'HN', 'HR', 'HT', 'HU', 'ID', 'IE',
        'IL', 'IM', 'IN', 'IO', 'IQ', 'IR', 'IS', 'IT', 'JE', 'JM',
        'JO', 'JP', 'KE', 'KG', 'KH', 'KI', 'KM', 'KN', 'KP', 'KR',
        'KW', 'KY', 'KZ', 'LA', 'LB', 'LC', 'LI', 'LK', 'LR', 'LS',
        'LT', 'LU', 'LV', 'LY', 'MA', 'MC', 'MD', 'ME', 'MF', 'MG',
        'MH', 'MK', 'ML', 'MM', 'MN', 'MO', 'MP', 'MQ', 'MR', 'MS',
        'MT', 'MU', 'MV', 'MW', 'MX', 'MY', 'MZ', 'NA', 'NC', 'NE',
        'NF', 'NG', 'NI', 'NL', 'NO', 'NP', 'NR', 'NU', 'NZ', 'OM',
        'PA', 'PE', 'PF', 'PG', 'PH', 'PK', 'PL', 'PM', 'PN', 'PR',
        'PS', 'PT', 'PW', 'PY', 'QA', 'RE', 'RO', 'RS', 'RU', 'RW',
        'SA', 'SB', 'SC', 'SD', 'SE', 'SG', 'SH', 'SI', 'SJ', 'SK',
        'SL', 'SM', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV', 'SX', 'SY',
        'SZ', 'TC', 'TD', 'TF', 'TG', 'TH', 'TJ', 'TK', 'TL', 'TM',
        'TN', 'TO', 'TR', 'TT', 'TV', 'TW', 'TZ', 'UA', 'UG', 'UM',
        'US', 'UY', 'UZ', 'VA', 'VC', 'VE', 'VG', 'VI', 'VN', 'VU',
        'WF', 'WS', 'YE', 'YT', 'ZA', 'ZM', 'ZW',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/ISO_3166-1_alpha-3
     * On date of 2014-10-19
     */
    protected static $countryISOAlpha3 = [
        'ABW', 'AFG', 'AGO', 'AIA', 'ALA', 'ALB', 'AND', 'ARE', 'ARG', 'ARM',
        'ASM', 'ATA', 'ATF', 'ATG', 'AUS', 'AUT', 'AZE', 'BDI', 'BEL', 'BEN',
        'BES', 'BFA', 'BGD', 'BGR', 'BHR', 'BHS', 'BIH', 'BLM', 'BLR', 'BLZ',
        'BMU', 'BOL', 'BRA', 'BRB', 'BRN', 'BTN', 'BVT', 'BWA', 'CAF', 'CAN',
        'CCK', 'CHE', 'CHL', 'CHN', 'CIV', 'CMR', 'COD', 'COG', 'COK', 'COL',
        'COM', 'CPV', 'CRI', 'CUB', 'CUW', 'CXR', 'CYM', 'CYP', 'CZE', 'DEU',
        'DJI', 'DMA', 'DNK', 'DOM', 'DZA', 'ECU', 'EGY', 'ERI', 'ESH', 'ESP',
        'EST', 'ETH', 'FIN', 'FJI', 'FLK', 'FRA', 'FRO', 'FSM', 'GAB', 'GBR',
        'GEO', 'GGY', 'GHA', 'GIB', 'GIN', 'GLP', 'GMB', 'GNB', 'GNQ', 'GRC',
        'GRD', 'GRL', 'GTM', 'GUF', 'GUM', 'GUY', 'HKG', 'HMD', 'HND', 'HRV',
        'HTI', 'HUN', 'IDN', 'IMN', 'IND', 'IOT', 'IRL', 'IRN', 'IRQ', 'ISL',
        'ISR', 'ITA', 'JAM', 'JEY', 'JOR', 'JPN', 'KAZ', 'KEN', 'KGZ', 'KHM',
        'KIR', 'KNA', 'KOR', 'KWT', 'LAO', 'LBN', 'LBR', 'LBY', 'LCA', 'LIE',
        'LKA', 'LSO', 'LTU', 'LUX', 'LVA', 'MAC', 'MAF', 'MAR', 'MCO', 'MDA',
        'MDG', 'MDV', 'MEX', 'MHL', 'MKD', 'MLI', 'MLT', 'MMR', 'MNE', 'MNG',
        'MNP', 'MOZ', 'MRT', 'MSR', 'MTQ', 'MUS', 'MWI', 'MYS', 'MYT', 'NAM',
        'NCL', 'NER', 'NFK', 'NGA', 'NIC', 'NIU', 'NLD', 'NOR', 'NPL', 'NRU',
        'NZL', 'OMN', 'PAK', 'PAN', 'PCN', 'PER', 'PHL', 'PLW', 'PNG', 'POL',
        'PRI', 'PRK', 'PRT', 'PRY', 'PSE', 'PYF', 'QAT', 'REU', 'ROU', 'RUS',
        'RWA', 'SAU', 'SDN', 'SEN', 'SGP', 'SGS', 'SHN', 'SJM', 'SLB', 'SLE',
        'SLV', 'SMR', 'SOM', 'SPM', 'SRB', 'SSD', 'STP', 'SUR', 'SVK', 'SVN',
        'SWE', 'SWZ', 'SXM', 'SYC', 'SYR', 'TCA', 'TCD', 'TGO', 'THA', 'TJK',
        'TKL', 'TKM', 'TLS', 'TON', 'TTO', 'TUN', 'TUR', 'TUV', 'TWN', 'TZA',
        'UGA', 'UKR', 'UMI', 'URY', 'USA', 'UZB', 'VAT', 'VCT', 'VEN', 'VGB',
        'VIR', 'VNM', 'VUT', 'WLF', 'WSM', 'YEM', 'ZAF', 'ZMB', 'ZWE',
    ];

    protected static $localeData = [
        'aa_DJ', 'aa_ER', 'aa_ET',
        'af_NA', 'af_ZA', 'ak_GH',
        'am_ET', 'ar_AE', 'ar_BH', 'ar_DZ',
        'ar_EG', 'ar_IQ', 'ar_JO', 'ar_KW', 'ar_LB',
        'ar_LY', 'ar_MA', 'ar_OM', 'ar_QA', 'ar_SA',
        'ar_SD', 'ar_SY', 'ar_TN', 'ar_YE',
        'as_IN', 'az_AZ', 'be_BY',
        'bg_BG', 'bn_BD', 'bn_IN',
        'bo_CN', 'bo_IN', 'bs_BA',
        'byn_ER', 'ca_ES',
        'cch_NG', 'cs_CZ',
        'cy_GB', 'da_DK', 'de_AT',
        'de_BE', 'de_CH', 'de_DE', 'de_LI', 'de_LU',
        'dv_MV', 'dz_BT',
        'ee_GH', 'ee_TG', 'el_CY', 'el_GR',
        'en_AS', 'en_AU', 'en_BE', 'en_BW',
        'en_BZ', 'en_CA', 'en_GB', 'en_GU', 'en_HK',
        'en_IE', 'en_IN', 'en_JM', 'en_MH', 'en_MP',
        'en_MT', 'en_NA', 'en_NZ', 'en_PH', 'en_PK',
        'en_SG', 'en_TT', 'en_UM', 'en_US', 'en_VI',
        'en_ZA', 'en_ZW', 'es_AR',
        'es_BO', 'es_CL', 'es_CO', 'es_CR', 'es_DO',
        'es_EC', 'es_ES', 'es_GT', 'es_HN', 'es_MX',
        'es_NI', 'es_PA', 'es_PE', 'es_PR', 'es_PY',
        'es_SV', 'es_US', 'es_UY', 'es_VE',
        'et_EE', 'eu_ES', 'fa_AF',
        'fa_IR', 'fi_FI', 'fil_PH',
        'fo_FO', 'fr_BE', 'fr_CA',
        'fr_CH', 'fr_FR', 'fr_LU', 'fr_MC', 'fr_SN',
        'fur_IT', 'ga_IE',
        'gaa_GH', 'gez_ER', 'gez_ET',
        'gl_ES', 'gsw_CH', 'gu_IN',
        'gv_GB', 'ha_GH', 'ha_NE',
        'ha_NG', 'ha_SD', 'haw_US',
        'he_IL', 'hi_IN', 'hr_HR',
        'hu_HU', 'hy_AM',
        'id_ID', 'ig_NG',
        'ii_CN', 'is_IS',
        'it_CH', 'it_IT',
        'ja_JP', 'ka_GE', 'kaj_NG',
        'kam_KE', 'kcg_NG',
        'kfo_CI', 'kk_KZ', 'kl_GL',
        'km_KH', 'kn_IN',
        'ko_KR', 'kok_IN', 'kpe_GN',
        'kpe_LR', 'ku_IQ', 'ku_IR', 'ku_SY',
        'ku_TR', 'kw_GB', 'ky_KG',
        'ln_CD', 'ln_CG', 'lo_LA',
        'lt_LT', 'lv_LV',
        'mk_MK', 'ml_IN', 'mn_CN',
        'mn_MN', 'mr_IN',
        'ms_BN', 'ms_MY', 'mt_MT',
        'my_MM', 'nb_NO', 'nds_DE',
        'ne_IN', 'ne_NP', 'nl_BE',
        'nl_NL', 'nn_NO',
        'nr_ZA', 'nso_ZA', 'ny_MW',
        'oc_FR', 'om_ET', 'om_KE',
        'or_IN', 'pa_IN', 'pa_PK',
        'pl_PL', 'ps_AF',
        'pt_BR', 'pt_PT', 'ro_MD', 'ro_RO',
        'ru_RU', 'ru_UA', 'rw_RW',
        'sa_IN', 'se_FI', 'se_NO',
        'sh_BA', 'sh_CS', 'sh_YU',
        'si_LK', 'sid_ET', 'sk_SK',
        'sl_SI', 'so_DJ', 'so_ET',
        'so_KE', 'so_SO', 'sq_AL',
        'sr_BA', 'sr_CS', 'sr_ME', 'sr_RS', 'sr_YU',
        'ss_SZ', 'ss_ZA', 'st_LS',
        'st_ZA', 'sv_FI', 'sv_SE',
        'sw_KE', 'sw_TZ', 'syr_SY',
        'ta_IN', 'te_IN', 'tg_TJ',
        'th_TH', 'ti_ER', 'ti_ET',
        'tig_ER', 'tn_ZA',
        'to_TO', 'tr_TR',
        'trv_TW', 'ts_ZA', 'tt_RU',
        'ug_CN', 'uk_UA',
        'ur_IN', 'ur_PK', 'uz_AF', 'uz_UZ',
        've_ZA', 'vi_VN',
        'wal_ET', 'wo_SN', 'xh_ZA',
        'yo_NG', 'zh_CN', 'zh_HK',
        'zh_MO', 'zh_SG', 'zh_TW', 'zu_ZA',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/ISO_4217
     * On date of 2023-01-01
     *
     * With the following exceptions:
     * SVC has been replaced by the USD in 2001: https://en.wikipedia.org/wiki/Salvadoran_col%C3%B3n
     * ZWL has been suspended since 2009: https://en.wikipedia.org/wiki/Zimbabwean_dollar
     * HRK has been replaced by EUR since 2023: https://en.wikipedia.org/wiki/Croatian_kuna
     */
    protected static $currencyCode = [
        'AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG', 'AZN',
        'BAM', 'BBD', 'BDT', 'BGN', 'BHD', 'BIF', 'BMD', 'BND', 'BOB', 'BRL',
        'BSD', 'BTN', 'BWP', 'BYN', 'BZD', 'CAD', 'CDF', 'CHF', 'CLP', 'CNY',
        'COP', 'CRC', 'CUC', 'CUP', 'CVE', 'CZK', 'DJF', 'DKK', 'DOP', 'DZD',
        'EGP', 'ERN', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP', 'GEL', 'GHS', 'GIP',
        'GMD', 'GNF', 'GTQ', 'GYD', 'HKD', 'HNL', 'HTG', 'HUF', 'IDR', 'ILS',
        'INR', 'IQD', 'IRR', 'ISK', 'JMD', 'JOD', 'JPY', 'KES', 'KGS', 'KHR',
        'KMF', 'KPW', 'KRW', 'KWD', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LRD',
        'LSL', 'LYD', 'MAD', 'MDL', 'MGA', 'MKD', 'MMK', 'MNT', 'MOP', 'MRU',
        'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN', 'NAD', 'NGN', 'NIO', 'NOK',
        'NPR', 'NZD', 'OMR', 'PAB', 'PEN', 'PGK', 'PHP', 'PKR', 'PLN', 'PYG',
        'QAR', 'RON', 'RSD', 'RUB', 'RWF', 'SAR', 'SBD', 'SCR', 'SDG', 'SEK',
        'SGD', 'SHP', 'SLL', 'SOS', 'SRD', 'SSP', 'STN', 'SYP', 'SZL', 'THB',
        'TJS', 'TMT', 'TND', 'TOP', 'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 'UGX',
        'USD', 'UYU', 'UZS', 'VES', 'VND', 'VUV', 'WST', 'XAF', 'XCD', 'XOF',
        'XPF', 'YER', 'ZAR', 'ZMW',
    ];

    /**
     * Return a boolean, true or false.
     *
     * @param int $chanceOfGettingTrue Between 0 (always get false) and 100 (always get true)
     *
     * @return bool
     *
     * @example true
     */
    public static function boolean($chanceOfGettingTrue = 50)
    {
        return self::numberBetween(1, 100) <= $chanceOfGettingTrue;
    }

    /**
     * @example 'cfcd208495d565ef66e7dff9f98764da'
     *
     * @return string
     */
    public static function md5()
    {
        return md5(self::numberBetween());
    }

    /**
     * @example 'b5d86317c2a144cd04d0d7c03b2b02666fafadf2'
     *
     * @return string
     */
    public static function sha1()
    {
        return sha1(self::numberBetween());
    }

    /**
     * @example '85086017559ccc40638fcde2fecaf295e0de7ca51b7517b6aebeaaf75b4d4654'
     *
     * @return string
     */
    public static function sha256()
    {
        return hash('sha256', self::numberBetween());
    }

    /**
     * @example 'fr_FR'
     *
     * @return string
     */
    public static function locale()
    {
        return static::randomElement(static::$localeData);
    }

    /**
     * @example 'FR'
     *
     * @see https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
     *
     * @return string
     */
    public static function countryCode()
    {
        return static::randomElement(static::$countryCode);
    }

    /**
     * @example 'FRA'
     *
     * @see https://en.wikipedia.org/wiki/ISO_3166-1_alpha-3
     *
     * @return string
     */
    public static function countryISOAlpha3()
    {
        return static::randomElement(static::$countryISOAlpha3);
    }

    /**
     * @example 'fr'
     *
     * @return string
     */
    public static function languageCode()
    {
        return static::randomElement(static::$languageCode);
    }

    /**
     * @example 'EUR'
     *
     * @see https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
     *
     * @return string
     */
    public static function currencyCode()
    {
        return static::randomElement(static::$currencyCode);
    }

    /**
     * Returns an Emoji (Unicode character between U+1F600 and U+1F637).
     *
     * @see https://en.wikipedia.org/wiki/Emoji#Unicode_blocks
     *
     * @return string
     */
    public static function emoji()
    {
        return static::randomElement(static::$emoji);
    }
}
