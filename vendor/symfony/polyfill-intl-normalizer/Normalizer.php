<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Intl\Normalizer;

/**
 * Normalizer is a PHP fallback implementation of the Normalizer class provided by the intl extension.
 *
 * It has been validated with Unicode 6.3 Normalization Conformance Test.
 * See http://www.unicode.org/reports/tr15/ for detailed info about Unicode normalizations.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
class Normalizer
{
    public const FORM_D = \Normalizer::FORM_D;
    public const FORM_KD = \Normalizer::FORM_KD;
    public const FORM_C = \Normalizer::FORM_C;
    public const FORM_KC = \Normalizer::FORM_KC;
    public const NFD = \Normalizer::NFD;
    public const NFKD = \Normalizer::NFKD;
    public const NFC = \Normalizer::NFC;
    public const NFKC = \Normalizer::NFKC;

    private static $C;
    private static $D;
    private static $KD;
    private static $cC;
    private static $rawD;
    private static $rawKD;
    private static $ulenMask = ["\xC0" => 2, "\xD0" => 2, "\xE0" => 3, "\xF0" => 4];
    private static $ASCII = "\x20\x65\x69\x61\x73\x6E\x74\x72\x6F\x6C\x75\x64\x5D\x5B\x63\x6D\x70\x27\x0A\x67\x7C\x68\x76\x2E\x66\x62\x2C\x3A\x3D\x2D\x71\x31\x30\x43\x32\x2A\x79\x78\x29\x28\x4C\x39\x41\x53\x2F\x50\x22\x45\x6A\x4D\x49\x6B\x33\x3E\x35\x54\x3C\x44\x34\x7D\x42\x7B\x38\x46\x77\x52\x36\x37\x55\x47\x4E\x3B\x4A\x7A\x56\x23\x48\x4F\x57\x5F\x26\x21\x4B\x3F\x58\x51\x25\x59\x5C\x09\x5A\x2B\x7E\x5E\x24\x40\x60\x7F\x00\x01\x02\x03\x04\x05\x06\x07\x08\x0B\x0C\x0D\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F";

    public static function isNormalized(string $s, int $form = self::FORM_C)
    {
        if (!\in_array($form, [self::NFD, self::NFKD, self::NFC, self::NFKC])) {
            return false;
        }
        if (!isset($s[strspn($s, self::$ASCII)])) {
            return true;
        }
        if (self::NFC == $form && preg_match('//u', $s) && !preg_match('/[^\x00-\x{2FF}]/u', $s)) {
            return true;
        }

        return self::normalize($s, $form) === $s;
    }

    public static function getRawDecomposition(string $s, int $form = self::FORM_C)
    {
        if ('' === $s || !preg_match('//u', $s)) {
            return null;
        }

        $ulen = $s[0] < "\x80" ? 1 : (self::$ulenMask[$s[0] & "\xF0"] ?? 0);
        if (!$ulen || \strlen($s) !== $ulen) {
            return null;
        }

        if (self::NFC !== $form && self::NFD !== $form && self::NFKC !== $form && self::NFKD !== $form) {
            return '';
        }

        if ($s >= "\xEA\xB0\x80" && $s <= "\xED\x9E\xA3") {
            $u = unpack('C*', $s);
            $j = (($u[1] - 224) << 12) + (($u[2] - 128) << 6) + $u[3] - 0xAC80;

            if ($t = $j % 28) {
                $j -= $t;
                $lv = 0xAC00 + $j;
                $r = \chr(0xE0 | $lv >> 12).\chr(0x80 | $lv >> 6 & 0x3F).\chr(0x80 | $lv & 0x3F);
                $r .= $t < 25
                    ? ("\xE1\x86".\chr(0xA7 + $t))
                    : ("\xE1\x87".\chr(0x67 + $t));

                return $r;
            }

            return "\xE1\x84".\chr(0x80 + (int) ($j / 588))
                  ."\xE1\x85".\chr(0xA1 + (int) (($j % 588) / 28));
        }

        if (null === self::$rawD) {
            self::$rawD = self::getData('rawCanonicalDecomposition');
        }

        if (isset(self::$rawD[$s])) {
            return self::$rawD[$s];
        }

        if (self::NFKC === $form || self::NFKD === $form) {
            if (null === self::$rawKD) {
                self::$rawKD = self::getData('rawCompatibilityDecomposition');
            }

            return self::$rawKD[$s] ?? null;
        }

        return null;
    }

    public static function normalize(string $s, int $form = self::FORM_C)
    {
        if (!preg_match('//u', $s)) {
            return false;
        }

        switch ($form) {
            case self::NFC: $C = true; $K = false; break;
            case self::NFD: $C = false; $K = false; break;
            case self::NFKC: $C = true; $K = true; break;
            case self::NFKD: $C = false; $K = true; break;
            default:
                if (\defined('Normalizer::NONE') && \Normalizer::NONE == $form) {
                    return $s;
                }

                if (80000 > \PHP_VERSION_ID) {
                    return false;
                }

                throw new \ValueError('normalizer_normalize(): Argument #2 ($form) must be a a valid normalization form');
        }

        if ('' === $s) {
            return '';
        }

        if ($K && null === self::$KD) {
            self::$KD = self::getData('compatibilityDecomposition');
        }

        if (null === self::$D) {
            self::$D = self::getData('canonicalDecomposition');
            self::$cC = self::getData('combiningClass');
        }

        if (null !== $mbEncoding = (2 /* MB_OVERLOAD_STRING */ & (int) \ini_get('mbstring.func_overload')) ? mb_internal_encoding() : null) {
            mb_internal_encoding('8bit');
        }

        $r = self::decompose($s, $K);

        if ($C) {
            if (null === self::$C) {
                self::$C = self::getData('canonicalComposition');
            }

            $r = self::recompose($r);
        }
        if (null !== $mbEncoding) {
            mb_internal_encoding($mbEncoding);
        }

        return $r;
    }

    private static function recompose($s)
    {
        $ASCII = self::$ASCII;
        $compMap = self::$C;
        $combClass = self::$cC;
        $ulenMask = self::$ulenMask;

        $result = $tail = '';

        $i = $s[0] < "\x80" ? 1 : $ulenMask[$s[0] & "\xF0"];
        $len = \strlen($s);

        $lastUchr = substr($s, 0, $i);
        $lastUcls = isset($combClass[$lastUchr]) ? 256 : 0;

        while ($i < $len) {
            if ($s[$i] < "\x80") {
                // ASCII chars

                if ($tail) {
                    $lastUchr .= $tail;
                    $tail = '';
                }

                if ($j = strspn($s, $ASCII, $i + 1)) {
                    $lastUchr .= substr($s, $i, $j);
                    $i += $j;
                }

                $result .= $lastUchr;
                $lastUchr = $s[$i];
                $lastUcls = 0;
                ++$i;
                continue;
            }

            $ulen = $ulenMask[$s[$i] & "\xF0"];
            $uchr = substr($s, $i, $ulen);

            if ($lastUchr < "\xE1\x84\x80" || "\xE1\x84\x92" < $lastUchr
                || $uchr < "\xE1\x85\xA1" || "\xE1\x85\xB5" < $uchr
                || $lastUcls) {
                // Table lookup and combining chars composition

                $ucls = $combClass[$uchr] ?? 0;

                if (isset($compMap[$lastUchr.$uchr]) && (!$lastUcls || $lastUcls < $ucls)) {
                    $lastUchr = $compMap[$lastUchr.$uchr];
                } elseif ($lastUcls = $ucls) {
                    $tail .= $uchr;
                } else {
                    if ($tail) {
                        $lastUchr .= $tail;
                        $tail = '';
                    }

                    $result .= $lastUchr;
                    $lastUchr = $uchr;
                }
            } else {
                // Hangul chars

                $L = \ord($lastUchr[2]) - 0x80;
                $V = \ord($uchr[2]) - 0xA1;
                $T = 0;

                $uchr = substr($s, $i + $ulen, 3);

                if ("\xE1\x86\xA7" <= $uchr && $uchr <= "\xE1\x87\x82") {
                    $T = \ord($uchr[2]) - 0xA7;
                    0 > $T && $T += 0x40;
                    $ulen += 3;
                }

                $L = 0xAC00 + ($L * 21 + $V) * 28 + $T;
                $lastUchr = \chr(0xE0 | $L >> 12).\chr(0x80 | $L >> 6 & 0x3F).\chr(0x80 | $L & 0x3F);
            }

            $i += $ulen;
        }

        return $result.$lastUchr.$tail;
    }

    private static function decompose($s, $c)
    {
        $result = '';

        $ASCII = self::$ASCII;
        $decompMap = self::$D;
        $combClass = self::$cC;
        $ulenMask = self::$ulenMask;
        if ($c) {
            $compatMap = self::$KD;
        }

        $c = [];
        $i = 0;
        $len = \strlen($s);

        while ($i < $len) {
            if ($s[$i] < "\x80") {
                // ASCII chars

                if ($c) {
                    ksort($c);
                    $result .= implode('', $c);
                    $c = [];
                }

                $j = 1 + strspn($s, $ASCII, $i + 1);
                $result .= substr($s, $i, $j);
                $i += $j;
                continue;
            }

            $ulen = $ulenMask[$s[$i] & "\xF0"];
            $uchr = substr($s, $i, $ulen);
            $i += $ulen;

            if ($uchr < "\xEA\xB0\x80" || "\xED\x9E\xA3" < $uchr) {
                // Table lookup

                if ($uchr !== $j = $compatMap[$uchr] ?? ($decompMap[$uchr] ?? $uchr)) {
                    $uchr = $j;

                    $j = \strlen($uchr);
                    $ulen = $uchr[0] < "\x80" ? 1 : $ulenMask[$uchr[0] & "\xF0"];

                    if ($ulen != $j) {
                        // Put trailing chars in $s

                        $j -= $ulen;
                        $i -= $j;

                        if (0 > $i) {
                            $s = str_repeat(' ', -$i).$s;
                            $len -= $i;
                            $i = 0;
                        }

                        while ($j--) {
                            $s[$i + $j] = $uchr[$ulen + $j];
                        }

                        $uchr = substr($uchr, 0, $ulen);
                    }
                }
                if (isset($combClass[$uchr])) {
                    // Combining chars, for sorting

                    if (!isset($c[$combClass[$uchr]])) {
                        $c[$combClass[$uchr]] = '';
                    }
                    $c[$combClass[$uchr]] .= $uchr;
                    continue;
                }
            } else {
                // Hangul chars

                $uchr = unpack('C*', $uchr);
                $j = (($uchr[1] - 224) << 12) + (($uchr[2] - 128) << 6) + $uchr[3] - 0xAC80;

                $uchr = "\xE1\x84".\chr(0x80 + (int) ($j / 588))
                       ."\xE1\x85".\chr(0xA1 + (int) (($j % 588) / 28));

                if ($j %= 28) {
                    $uchr .= $j < 25
                        ? ("\xE1\x86".\chr(0xA7 + $j))
                        : ("\xE1\x87".\chr(0x67 + $j));
                }
            }
            if ($c) {
                ksort($c);
                $result .= implode('', $c);
                $c = [];
            }

            $result .= $uchr;
        }

        if ($c) {
            ksort($c);
            $result .= implode('', $c);
        }

        return $result;
    }

    private static function getData($file)
    {
        if (file_exists($file = __DIR__.'/Resources/unidata/'.$file.'.php')) {
            return require $file;
        }

        return false;
    }
}
