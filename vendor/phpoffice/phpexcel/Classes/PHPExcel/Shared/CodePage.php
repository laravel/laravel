<?php

/**
 * PHPExcel_Shared_CodePage
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
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Shared_CodePage
{
    /**
     * Convert Microsoft Code Page Identifier to Code Page Name which iconv
     * and mbstring understands
     *
     * @param integer $codePage Microsoft Code Page Indentifier
     * @return string Code Page Name
     * @throws PHPExcel_Exception
     */
    public static function NumberToName($codePage = 1252)
    {
        switch ($codePage) {
            case 367:
                return 'ASCII';    //    ASCII
            case 437:
                return 'CP437';    //    OEM US
            case 720:
                throw new PHPExcel_Exception('Code page 720 not supported.');    //    OEM Arabic
            case 737:
                return 'CP737';    //    OEM Greek
            case 775:
                return 'CP775';    //    OEM Baltic
            case 850:
                return 'CP850';    //    OEM Latin I
            case 852:
                return 'CP852';    //    OEM Latin II (Central European)
            case 855:
                return 'CP855';    //    OEM Cyrillic
            case 857:
                return 'CP857';    //    OEM Turkish
            case 858:
                return 'CP858';    //    OEM Multilingual Latin I with Euro
            case 860:
                return 'CP860';    //    OEM Portugese
            case 861:
                return 'CP861';    //    OEM Icelandic
            case 862:
                return 'CP862';    //    OEM Hebrew
            case 863:
                return 'CP863';    //    OEM Canadian (French)
            case 864:
                return 'CP864';    //    OEM Arabic
            case 865:
                return 'CP865';    //    OEM Nordic
            case 866:
                return 'CP866';    //    OEM Cyrillic (Russian)
            case 869:
                return 'CP869';    //    OEM Greek (Modern)
            case 874:
                return 'CP874';    //    ANSI Thai
            case 932:
                return 'CP932';    //    ANSI Japanese Shift-JIS
            case 936:
                return 'CP936';    //    ANSI Chinese Simplified GBK
            case 949:
                return 'CP949';    //    ANSI Korean (Wansung)
            case 950:
                return 'CP950';    //    ANSI Chinese Traditional BIG5
            case 1200:
                return 'UTF-16LE'; //    UTF-16 (BIFF8)
            case 1250:
                return 'CP1250';   //    ANSI Latin II (Central European)
            case 1251:
                return 'CP1251';   //    ANSI Cyrillic
            case 0:
                //    CodePage is not always correctly set when the xls file was saved by Apple's Numbers program
            case 1252:
                return 'CP1252';   //    ANSI Latin I (BIFF4-BIFF7)
            case 1253:
                return 'CP1253';   //    ANSI Greek
            case 1254:
                return 'CP1254';   //    ANSI Turkish
            case 1255:
                return 'CP1255';   //    ANSI Hebrew
            case 1256:
                return 'CP1256';   //    ANSI Arabic
            case 1257:
                return 'CP1257';   //    ANSI Baltic
            case 1258:
                return 'CP1258';   //    ANSI Vietnamese
            case 1361:
                return 'CP1361';   //    ANSI Korean (Johab)
            case 10000:
                return 'MAC';      //    Apple Roman
            case 10001:
                return 'CP932';    //    Macintosh Japanese
            case 10002:
                return 'CP950';    //    Macintosh Chinese Traditional
            case 10003:
                return 'CP1361';   //    Macintosh Korean
            case 10004:	
                return 'MACARABIC';  //	Apple Arabic
            case 10005:
                return 'MACHEBREW';		//	Apple Hebrew
            case 10006:
                return 'MACGREEK';  //    Macintosh Greek
            case 10007:
                return 'MACCYRILLIC';  //    Macintosh Cyrillic
            case 10008:
                return 'CP936';  //    Macintosh - Simplified Chinese (GB 2312)
            case 10010:
                return 'MACROMANIA';	//	Macintosh Romania
            case 10017:
                return 'MACUKRAINE';	//	Macintosh Ukraine
            case 10021:
                return 'MACTHAI';	//	Macintosh Thai
            case 10029:
                return 'MACCENTRALEUROPE';  //    Macintosh Central Europe
            case 10079:
                return 'MACICELAND';  //    Macintosh Icelandic
            case 10081:
                return 'MACTURKISH';  //    Macintosh Turkish
            case 10082:
                return 'MACCROATIAN';	//	Macintosh Croatian
            case 21010:
                return 'UTF-16LE';  //    UTF-16 (BIFF8) This isn't correct, but some Excel writer libraries erroneously use Codepage 21010 for UTF-16LE
            case 32768:
                return 'MAC';      //    Apple Roman
            case 32769:
                throw new PHPExcel_Exception('Code page 32769 not supported.');  //    ANSI Latin I (BIFF2-BIFF3)
            case 65000:
                return 'UTF-7';    //    Unicode (UTF-7)
            case 65001:
                return 'UTF-8';    //    Unicode (UTF-8)
        }
        throw new PHPExcel_Exception('Unknown codepage: ' . $codePage);
    }
}
