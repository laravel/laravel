<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2014 PHPExcel
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
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */


/**
 * PHPExcel_Shared_CodePage
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
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
			case 367:	return 'ASCII';				break;	//	ASCII
			case 437:	return 'CP437';				break;	//	OEM US
			case 720:	throw new PHPExcel_Exception('Code page 720 not supported.');
													break;	//	OEM Arabic
			case 737:	return 'CP737';				break;	//	OEM Greek
			case 775:	return 'CP775';				break;	//	OEM Baltic
			case 850:	return 'CP850';				break;	//	OEM Latin I
			case 852:	return 'CP852';				break;	//	OEM Latin II (Central European)
			case 855:	return 'CP855';				break;	//	OEM Cyrillic
			case 857:	return 'CP857';				break;	//	OEM Turkish
			case 858:	return 'CP858';				break;	//	OEM Multilingual Latin I with Euro
			case 860:	return 'CP860';				break;	//	OEM Portugese
			case 861:	return 'CP861';				break;	//	OEM Icelandic
			case 862:	return 'CP862';				break;	//	OEM Hebrew
			case 863:	return 'CP863';				break;	//	OEM Canadian (French)
			case 864:	return 'CP864';				break;	//	OEM Arabic
			case 865:	return 'CP865';				break;	//	OEM Nordic
			case 866:	return 'CP866';				break;	//	OEM Cyrillic (Russian)
			case 869:	return 'CP869';				break;	//	OEM Greek (Modern)
			case 874:	return 'CP874';				break;	//	ANSI Thai
			case 932:	return 'CP932';				break;	//	ANSI Japanese Shift-JIS
			case 936:	return 'CP936';				break;	//	ANSI Chinese Simplified GBK
			case 949:	return 'CP949';				break;	//	ANSI Korean (Wansung)
			case 950:	return 'CP950';				break;	//	ANSI Chinese Traditional BIG5
			case 1200:	return 'UTF-16LE';			break;	//	UTF-16 (BIFF8)
			case 1250:	return 'CP1250';			break;	//	ANSI Latin II (Central European)
			case 1251:	return 'CP1251';			break;	//	ANSI Cyrillic
			case 0:		//	CodePage is not always correctly set when the xls file was saved by Apple's Numbers program
			case 1252:	return 'CP1252';			break;	//	ANSI Latin I (BIFF4-BIFF7)
			case 1253:	return 'CP1253';			break;	//	ANSI Greek
			case 1254:	return 'CP1254';			break;	//	ANSI Turkish
			case 1255:	return 'CP1255';			break;	//	ANSI Hebrew
			case 1256:	return 'CP1256';			break;	//	ANSI Arabic
			case 1257:	return 'CP1257';			break;	//	ANSI Baltic
			case 1258:	return 'CP1258';			break;	//	ANSI Vietnamese
			case 1361:	return 'CP1361';			break;	//	ANSI Korean (Johab)
			case 10000:	return 'MAC';				break;	//	Apple Roman
			case 10006:	return 'MACGREEK';			break;	//	Macintosh Greek
			case 10007:	return 'MACCYRILLIC';		break;	//	Macintosh Cyrillic
            case 10008: return 'CP936';             break;  //  Macintosh - Simplified Chinese (GB 2312)
			case 10029:	return 'MACCENTRALEUROPE';	break;	//	Macintosh Central Europe
			case 10079: return 'MACICELAND';		break;	//	Macintosh Icelandic
			case 10081: return 'MACTURKISH';		break;	//	Macintosh Turkish
			case 32768:	return 'MAC';				break;	//	Apple Roman
			case 32769:	throw new PHPExcel_Exception('Code page 32769 not supported.');
													break;	//	ANSI Latin I (BIFF2-BIFF3)
			case 65000:	return 'UTF-7';				break;	//	Unicode (UTF-7)
			case 65001:	return 'UTF-8';				break;	//	Unicode (UTF-8)
		}

		throw new PHPExcel_Exception('Unknown codepage: ' . $codePage);
	}

}
