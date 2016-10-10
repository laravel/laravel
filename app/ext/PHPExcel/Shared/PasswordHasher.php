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
 * PHPExcel_Shared_PasswordHasher
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_PasswordHasher
{
	/**
	 * Create a password hash from a given string.
	 *
	 * This method is based on the algorithm provided by
	 * Daniel Rentz of OpenOffice and the PEAR package
	 * Spreadsheet_Excel_Writer by Xavier Noguer <xnoguer@rezebra.com>.
	 *
	 * @param 	string	$pPassword	Password to hash
	 * @return 	string				Hashed password
	 */
	public static function hashPassword($pPassword = '') {
        $password	= 0x0000;
        $charPos	= 1;       // char position

        // split the plain text password in its component characters
        $chars = preg_split('//', $pPassword, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($chars as $char) {
            $value			= ord($char) << $charPos++;	// shifted ASCII value
            $rotated_bits	= $value >> 15;				// rotated bits beyond bit 15
            $value			&= 0x7fff;					// first 15 bits
            $password		^= ($value | $rotated_bits);
        }

        $password ^= strlen($pPassword);
        $password ^= 0xCE4B;

        return(strtoupper(dechex($password)));
	}
}
