<?php

/**
 * PHPExcel_Reader_Excel5_MD5
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader_Excel5
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt        LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Reader_Excel5_MD5
{
    // Context
    private $a;
    private $b;
    private $c;
    private $d;

    /**
     * MD5 stream constructor
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Reset the MD5 stream context
     */
    public function reset()
    {
        $this->a = 0x67452301;
        $this->b = 0xEFCDAB89;
        $this->c = 0x98BADCFE;
        $this->d = 0x10325476;
    }

    /**
     * Get MD5 stream context
     *
     * @return string
     */
    public function getContext()
    {
        $s = '';
        foreach (array('a', 'b', 'c', 'd') as $i) {
            $v = $this->{$i};
            $s .= chr($v & 0xff);
            $s .= chr(($v >> 8) & 0xff);
            $s .= chr(($v >> 16) & 0xff);
            $s .= chr(($v >> 24) & 0xff);
        }

        return $s;
    }

    /**
     * Add data to context
     *
     * @param string $data Data to add
     */
    public function add($data)
    {
        $words = array_values(unpack('V16', $data));

        $A = $this->a;
        $B = $this->b;
        $C = $this->c;
        $D = $this->d;

        $F = array('PHPExcel_Reader_Excel5_MD5','f');
        $G = array('PHPExcel_Reader_Excel5_MD5','g');
        $H = array('PHPExcel_Reader_Excel5_MD5','h');
        $I = array('PHPExcel_Reader_Excel5_MD5','i');

        /* ROUND 1 */
        self::step($F, $A, $B, $C, $D, $words[0], 7, 0xd76aa478);
        self::step($F, $D, $A, $B, $C, $words[1], 12, 0xe8c7b756);
        self::step($F, $C, $D, $A, $B, $words[2], 17, 0x242070db);
        self::step($F, $B, $C, $D, $A, $words[3], 22, 0xc1bdceee);
        self::step($F, $A, $B, $C, $D, $words[4], 7, 0xf57c0faf);
        self::step($F, $D, $A, $B, $C, $words[5], 12, 0x4787c62a);
        self::step($F, $C, $D, $A, $B, $words[6], 17, 0xa8304613);
        self::step($F, $B, $C, $D, $A, $words[7], 22, 0xfd469501);
        self::step($F, $A, $B, $C, $D, $words[8], 7, 0x698098d8);
        self::step($F, $D, $A, $B, $C, $words[9], 12, 0x8b44f7af);
        self::step($F, $C, $D, $A, $B, $words[10], 17, 0xffff5bb1);
        self::step($F, $B, $C, $D, $A, $words[11], 22, 0x895cd7be);
        self::step($F, $A, $B, $C, $D, $words[12], 7, 0x6b901122);
        self::step($F, $D, $A, $B, $C, $words[13], 12, 0xfd987193);
        self::step($F, $C, $D, $A, $B, $words[14], 17, 0xa679438e);
        self::step($F, $B, $C, $D, $A, $words[15], 22, 0x49b40821);

        /* ROUND 2 */
        self::step($G, $A, $B, $C, $D, $words[1], 5, 0xf61e2562);
        self::step($G, $D, $A, $B, $C, $words[6], 9, 0xc040b340);
        self::step($G, $C, $D, $A, $B, $words[11], 14, 0x265e5a51);
        self::step($G, $B, $C, $D, $A, $words[0], 20, 0xe9b6c7aa);
        self::step($G, $A, $B, $C, $D, $words[5], 5, 0xd62f105d);
        self::step($G, $D, $A, $B, $C, $words[10], 9, 0x02441453);
        self::step($G, $C, $D, $A, $B, $words[15], 14, 0xd8a1e681);
        self::step($G, $B, $C, $D, $A, $words[4], 20, 0xe7d3fbc8);
        self::step($G, $A, $B, $C, $D, $words[9], 5, 0x21e1cde6);
        self::step($G, $D, $A, $B, $C, $words[14], 9, 0xc33707d6);
        self::step($G, $C, $D, $A, $B, $words[3], 14, 0xf4d50d87);
        self::step($G, $B, $C, $D, $A, $words[8], 20, 0x455a14ed);
        self::step($G, $A, $B, $C, $D, $words[13], 5, 0xa9e3e905);
        self::step($G, $D, $A, $B, $C, $words[2], 9, 0xfcefa3f8);
        self::step($G, $C, $D, $A, $B, $words[7], 14, 0x676f02d9);
        self::step($G, $B, $C, $D, $A, $words[12], 20, 0x8d2a4c8a);

        /* ROUND 3 */
        self::step($H, $A, $B, $C, $D, $words[5], 4, 0xfffa3942);
        self::step($H, $D, $A, $B, $C, $words[8], 11, 0x8771f681);
        self::step($H, $C, $D, $A, $B, $words[11], 16, 0x6d9d6122);
        self::step($H, $B, $C, $D, $A, $words[14], 23, 0xfde5380c);
        self::step($H, $A, $B, $C, $D, $words[1], 4, 0xa4beea44);
        self::step($H, $D, $A, $B, $C, $words[4], 11, 0x4bdecfa9);
        self::step($H, $C, $D, $A, $B, $words[7], 16, 0xf6bb4b60);
        self::step($H, $B, $C, $D, $A, $words[10], 23, 0xbebfbc70);
        self::step($H, $A, $B, $C, $D, $words[13], 4, 0x289b7ec6);
        self::step($H, $D, $A, $B, $C, $words[0], 11, 0xeaa127fa);
        self::step($H, $C, $D, $A, $B, $words[3], 16, 0xd4ef3085);
        self::step($H, $B, $C, $D, $A, $words[6], 23, 0x04881d05);
        self::step($H, $A, $B, $C, $D, $words[9], 4, 0xd9d4d039);
        self::step($H, $D, $A, $B, $C, $words[12], 11, 0xe6db99e5);
        self::step($H, $C, $D, $A, $B, $words[15], 16, 0x1fa27cf8);
        self::step($H, $B, $C, $D, $A, $words[2], 23, 0xc4ac5665);

        /* ROUND 4 */
        self::step($I, $A, $B, $C, $D, $words[0], 6, 0xf4292244);
        self::step($I, $D, $A, $B, $C, $words[7], 10, 0x432aff97);
        self::step($I, $C, $D, $A, $B, $words[14], 15, 0xab9423a7);
        self::step($I, $B, $C, $D, $A, $words[5], 21, 0xfc93a039);
        self::step($I, $A, $B, $C, $D, $words[12], 6, 0x655b59c3);
        self::step($I, $D, $A, $B, $C, $words[3], 10, 0x8f0ccc92);
        self::step($I, $C, $D, $A, $B, $words[10], 15, 0xffeff47d);
        self::step($I, $B, $C, $D, $A, $words[1], 21, 0x85845dd1);
        self::step($I, $A, $B, $C, $D, $words[8], 6, 0x6fa87e4f);
        self::step($I, $D, $A, $B, $C, $words[15], 10, 0xfe2ce6e0);
        self::step($I, $C, $D, $A, $B, $words[6], 15, 0xa3014314);
        self::step($I, $B, $C, $D, $A, $words[13], 21, 0x4e0811a1);
        self::step($I, $A, $B, $C, $D, $words[4], 6, 0xf7537e82);
        self::step($I, $D, $A, $B, $C, $words[11], 10, 0xbd3af235);
        self::step($I, $C, $D, $A, $B, $words[2], 15, 0x2ad7d2bb);
        self::step($I, $B, $C, $D, $A, $words[9], 21, 0xeb86d391);

        $this->a = ($this->a + $A) & 0xffffffff;
        $this->b = ($this->b + $B) & 0xffffffff;
        $this->c = ($this->c + $C) & 0xffffffff;
        $this->d = ($this->d + $D) & 0xffffffff;
    }

    private static function f($X, $Y, $Z)
    {
        return (($X & $Y) | ((~ $X) & $Z)); // X AND Y OR NOT X AND Z
    }

    private static function g($X, $Y, $Z)
    {
        return (($X & $Z) | ($Y & (~ $Z))); // X AND Z OR Y AND NOT Z
    }

    private static function h($X, $Y, $Z)
    {
        return ($X ^ $Y ^ $Z); // X XOR Y XOR Z
    }

    private static function i($X, $Y, $Z)
    {
        return ($Y ^ ($X | (~ $Z))) ; // Y XOR (X OR NOT Z)
    }

    private static function step($func, &$A, $B, $C, $D, $M, $s, $t)
    {
        $A = ($A + call_user_func($func, $B, $C, $D) + $M + $t) & 0xffffffff;
        $A = self::rotate($A, $s);
        $A = ($B + $A) & 0xffffffff;
    }

    private static function rotate($decimal, $bits)
    {
        $binary = str_pad(decbin($decimal), 32, "0", STR_PAD_LEFT);
        return bindec(substr($binary, $bits).substr($binary, 0, $bits));
    }
}
