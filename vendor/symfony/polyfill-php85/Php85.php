<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Php85;

/**
 * @author Pierre Ambroise <pierre27.ambroise@gmail.com>
 * @author Alexander Schranz <alexander@sulu.io>
 *
 * @internal
 */
final class Php85
{
    public static function get_error_handler(): ?callable
    {
        $handler = set_error_handler(null);
        restore_error_handler();

        return $handler;
    }

    public static function get_exception_handler(): ?callable
    {
        $handler = set_exception_handler(null);
        restore_exception_handler();

        return $handler;
    }

    public static function array_first(array $array)
    {
        foreach ($array as $value) {
            return $value;
        }

        return null;
    }

    public static function array_last(array $array)
    {
        return $array ? current(\array_slice($array, -1)) : null;
    }

    private const RTL_SCRIPTS = [
        'Adlm' => true, 'Arab' => true, 'Armi' => true, 'Hebr' => true,
        'Mand' => true, 'Mani' => true, 'Mend' => true, 'Nkoo' => true,
        'Orkh' => true, 'Phnx' => true, 'Rohg' => true, 'Samr' => true,
        'Syrc' => true, 'Thaa' => true, 'Yezi' => true,
    ];

    private const LANG_TO_SCRIPT = [
        'ar' => 'Arab',
        'ckb' => 'Arab',
        'dv' => 'Thaa',
        'fa' => 'Arab',
        'he' => 'Hebr',
        'ku' => 'Arab',
        'nqo' => 'Nkoo',
        'ps' => 'Arab',
        'sd' => 'Arab',
        'ug' => 'Arab',
        'ur' => 'Arab',
        'yi' => 'Hebr',
    ];

    public static function locale_is_right_to_left(string $locale): bool
    {
        if ('' === $locale) {
            return false;
        }

        $parts = preg_split('/[_-]/', $locale);
        $language = strtolower($parts[0]);

        foreach ($parts as $part) {
            if (4 === \strlen($part) && ctype_alpha($part)) {
                return isset(self::RTL_SCRIPTS[ucfirst(strtolower($part))]);
            }
        }

        return isset(self::LANG_TO_SCRIPT[$language]) && isset(self::RTL_SCRIPTS[self::LANG_TO_SCRIPT[$language]]);
    }

    public static function grapheme_levenshtein(string $s1, string $s2, int $insertion_cost = 1, int $replacement_cost = 1, int $deletion_cost = 1)
    {
        if (!preg_match('//u', $s1) || !preg_match('//u', $s2)) {
            return false;
        }

        if (0 > $insertion_cost || 0 > $replacement_cost || 0 > $deletion_cost) {
            throw new \ValueError('grapheme_levenshtein(): Argument #3 ($insertion_cost), #4 ($replacement_cost), and #5 ($deletion_cost) must be greater than or equal to 0');
        }

        $regex = ((float) \PCRE_VERSION >= 10.44)
            ? '\X'
            : '(?:\r\n|[\x{1F1E6}-\x{1F1FF}][\x{1F1E6}-\x{1F1FF}]?|(?:[ -~\x{200C}\x{200D}]|[ᆨ-ᇹ]+|[ᄀ-ᅟ]*(?:[가개갸걔거게겨계고과괘괴교구궈궤귀규그긔기까깨꺄꺠꺼께껴꼐꼬꽈꽤꾀꾜꾸꿔꿰뀌뀨끄끠끼나내냐냬너네녀녜노놔놰뇌뇨누눠눼뉘뉴느늬니다대댜댸더데뎌뎨도돠돼되됴두둬뒈뒤듀드듸디따때땨떄떠떼뗘뗴또똬뙈뙤뚀뚜뚸뛔뛰뜌뜨띄띠라래랴럐러레려례로롸뢔뢰료루뤄뤠뤼류르릐리마매먀먜머메며몌모뫄뫠뫼묘무뭐뭬뮈뮤므믜미바배뱌뱨버베벼볘보봐봬뵈뵤부붜붸뷔뷰브븨비빠빼뺘뺴뻐뻬뼈뼤뽀뽜뽸뾔뾰뿌뿨쀄쀠쀼쁘쁴삐사새샤섀서세셔셰소솨쇄쇠쇼수숴쉐쉬슈스싀시싸쌔쌰썌써쎄쎠쎼쏘쏴쐐쐬쑈쑤쒀쒜쒸쓔쓰씌씨아애야얘어에여예오와왜외요우워웨위유으의이자재쟈쟤저제져졔조좌좨죄죠주줘줴쥐쥬즈즤지짜째쨔쨰쩌쩨쪄쪠쪼쫘쫴쬐쬬쭈쭤쮀쮜쮸쯔쯰찌차채챠챼처체쳐쳬초촤쵀최쵸추춰췌취츄츠츼치카캐캬컈커케켜켸코콰쾌쾨쿄쿠쿼퀘퀴큐크킈키타태탸턔터테텨톄토톼퇘퇴툐투퉈퉤튀튜트틔티파패퍄퍠퍼페펴폐포퐈퐤푀표푸풔풰퓌퓨프픠피하해햐햬허헤혀혜호화홰회효후훠훼휘휴흐희히]?[ᅠ-ᆢ]+|[가-힣])[ᆨ-ᇹ]*|[ᄀ-ᅟ]+|[^\p{Cc}\p{Cf}\p{Zl}\p{Zp}])[\p{Mn}\p{Mc}\p{Me}\x{09BE}\x{09D7}\x{0B3E}\x{0B57}\x{0BBE}\x{0BD7}\x{0CC2}\x{0CD5}\x{0CD6}\x{0D3E}\x{0D57}\x{0DCF}\x{0DDF}\x{200C}\x{1D165}\x{1D16E}-\x{1D172}\x{1F3FB}-\x{1F3FF}\x{FE0E}-\x{FE0F}\x{E0020}-\x{E007F}]*(?:\x{200D}(?:[\x{1F1E6}-\x{1F1FF}]|[ -~\x{200C}\x{200D}]|[ᆨ-ᇹ]+|[ᄀ-ᅟ]*(?:[가-힣]?[ᅠ-ᆢ]+|[가-힣])[ᆨ-ᇹ]*|[ᄀ-ᅟ]+|[^\p{Cc}\p{Cf}\p{Zl}\p{Zp}])[\p{Mn}\p{Mc}\p{Me}\x{09BE}\x{09D7}\x{0B3E}\x{0B57}\x{0BBE}\x{0BD7}\x{0CC2}\x{0CD5}\x{0CD6}\x{0D3E}\x{0D57}\x{0DCF}\x{0DDF}\x{200C}\x{1D165}\x{1D16E}-\x{1D172}\x{1F3FB}-\x{1F3FF}\x{FE0E}-\x{FE0F}\x{E0020}-\x{E007F}]*)*|[\p{Cc}\p{Cf}\p{Zl}\p{Zp}])';

        preg_match_all('/'.$regex.'/u', $s1, $s1);
        preg_match_all('/'.$regex.'/u', $s2, $s2);

        $s1 = $s1[0];
        $s2 = $s2[0];
        $l1 = \count($s1);
        $l2 = \count($s2);

        if (0 === $l1) {
            return $l2 * $insertion_cost;
        }
        if (0 === $l2) {
            return $l1 * $deletion_cost;
        }

        $dp = array_fill(0, $l1 + 1, array_fill(0, $l2 + 1, 0));

        for ($i = 1; $i <= $l1; ++$i) {
            $dp[$i][0] = $dp[$i - 1][0] + $deletion_cost;
        }
        for ($j = 1; $j <= $l2; ++$j) {
            $dp[0][$j] = $dp[0][$j - 1] + $insertion_cost;
        }

        for ($i = 1; $i <= $l1; ++$i) {
            for ($j = 1; $j <= $l2; ++$j) {
                $cost = ($s1[$i - 1] === $s2[$j - 1]) ? 0 : $replacement_cost;
                $dp[$i][$j] = min($dp[$i - 1][$j] + $deletion_cost, $dp[$i][$j - 1] + $insertion_cost, $dp[$i - 1][$j - 1] + $cost);
            }
        }

        return $dp[$l1][$l2];
    }
}
