<?php
/**
 * PHPExcel
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
 * @package    PHPExcel_RichText
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */


/**
 * PHPExcel_RichText_ITextElement
 *
 * @category   PHPExcel
 * @package    PHPExcel_RichText
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
interface PHPExcel_RichText_ITextElement
{
	/**
	 * Get text
	 *
	 * @return string	Text
	 */
	public function getText();

	/**
	 * Set text
	 *
	 * @param 	$pText string	Text
	 * @return PHPExcel_RichText_ITextElement
	 */
	public function setText($pText = '');

	/**
	 * Get font
	 *
	 * @return PHPExcel_Style_Font
	 */
	public function getFont();

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode();
}
