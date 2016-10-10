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
 * @package    PHPExcel_Writer_Excel5
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */

// Original file header of PEAR::Spreadsheet_Excel_Writer_Format (used as the base for this class):
// -----------------------------------------------------------------------------------------
// /*
// *  Module written/ported by Xavier Noguer <xnoguer@rezebra.com>
// *
// *  The majority of this is _NOT_ my code.  I simply ported it from the
// *  PERL Spreadsheet::WriteExcel module.
// *
// *  The author of the Spreadsheet::WriteExcel module is John McNamara
// *  <jmcnamara@cpan.org>
// *
// *  I _DO_ maintain this code, and John McNamara has nothing to do with the
// *  porting of this code to PHP.  Any questions directly related to this
// *  class library should be directed to me.
// *
// *  License Information:
// *
// *    Spreadsheet_Excel_Writer:  A library for generating Excel Spreadsheets
// *    Copyright (c) 2002-2003 Xavier Noguer xnoguer@rezebra.com
// *
// *    This library is free software; you can redistribute it and/or
// *    modify it under the terms of the GNU Lesser General Public
// *    License as published by the Free Software Foundation; either
// *    version 2.1 of the License, or (at your option) any later version.
// *
// *    This library is distributed in the hope that it will be useful,
// *    but WITHOUT ANY WARRANTY; without even the implied warranty of
// *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// *    Lesser General Public License for more details.
// *
// *    You should have received a copy of the GNU Lesser General Public
// *    License along with this library; if not, write to the Free Software
// *    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
// */


/**
 * PHPExcel_Writer_Excel5_Xf
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel5
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel5_Xf
{
    /**
	 * Style XF or a cell XF ?
	 *
	 * @var boolean
	 */
	private $_isStyleXf;

	/**
	 * Index to the FONT record. Index 4 does not exist
	 * @var integer
	 */
	private $_fontIndex;

	/**
	 * An index (2 bytes) to a FORMAT record (number format).
	 * @var integer
	 */
	public $_numberFormatIndex;

	/**
	 * 1 bit, apparently not used.
	 * @var integer
	 */
	public $_text_justlast;

	/**
	 * The cell's foreground color.
	 * @var integer
	 */
	public $_fg_color;

	/**
	 * The cell's background color.
	 * @var integer
	 */
	public $_bg_color;

	/**
	 * Color of the bottom border of the cell.
	 * @var integer
	 */
	public $_bottom_color;

	/**
	 * Color of the top border of the cell.
	 * @var integer
	 */
	public $_top_color;

	/**
	* Color of the left border of the cell.
	* @var integer
	*/
	public $_left_color;

	/**
	 * Color of the right border of the cell.
	 * @var integer
	 */
	public $_right_color;

	/**
	 * Constructor
	 *
	 * @access public
	 * @param PHPExcel_Style	The XF format
	 */
	public function __construct(PHPExcel_Style $style = null)
	{
		$this->_isStyleXf =     false;
		$this->_fontIndex      = 0;

		$this->_numberFormatIndex     = 0;

		$this->_text_justlast  = 0;

		$this->_fg_color       = 0x40;
		$this->_bg_color       = 0x41;

		$this->_diag           = 0;

		$this->_bottom_color   = 0x40;
		$this->_top_color      = 0x40;
		$this->_left_color     = 0x40;
		$this->_right_color    = 0x40;
		$this->_diag_color     = 0x40;
		$this->_style = $style;

	}


	/**
	 * Generate an Excel BIFF XF record (style or cell).
	 *
	 * @return string The XF record
	 */
	function writeXf()
	{
		// Set the type of the XF record and some of the attributes.
		if ($this->_isStyleXf) {
			$style = 0xFFF5;
		} else {
			$style   = self::_mapLocked($this->_style->getProtection()->getLocked());
			$style  |= self::_mapHidden($this->_style->getProtection()->getHidden()) << 1;
		}

		// Flags to indicate if attributes have been set.
		$atr_num     = ($this->_numberFormatIndex != 0)?1:0;
		$atr_fnt     = ($this->_fontIndex != 0)?1:0;
		$atr_alc     = ((int) $this->_style->getAlignment()->getWrapText()) ? 1 : 0;
		$atr_bdr     = (self::_mapBorderStyle($this->_style->getBorders()->getBottom()->getBorderStyle())   ||
						self::_mapBorderStyle($this->_style->getBorders()->getTop()->getBorderStyle())      ||
						self::_mapBorderStyle($this->_style->getBorders()->getLeft()->getBorderStyle())     ||
						self::_mapBorderStyle($this->_style->getBorders()->getRight()->getBorderStyle()))?1:0;
		$atr_pat     = (($this->_fg_color != 0x40) ||
						($this->_bg_color != 0x41) ||
						self::_mapFillType($this->_style->getFill()->getFillType()))?1:0;
		$atr_prot    = self::_mapLocked($this->_style->getProtection()->getLocked())
						| self::_mapHidden($this->_style->getProtection()->getHidden());

		// Zero the default border colour if the border has not been set.
		if (self::_mapBorderStyle($this->_style->getBorders()->getBottom()->getBorderStyle()) == 0) {
			$this->_bottom_color = 0;
		}
		if (self::_mapBorderStyle($this->_style->getBorders()->getTop()->getBorderStyle())  == 0) {
			$this->_top_color = 0;
		}
		if (self::_mapBorderStyle($this->_style->getBorders()->getRight()->getBorderStyle()) == 0) {
			$this->_right_color = 0;
		}
		if (self::_mapBorderStyle($this->_style->getBorders()->getLeft()->getBorderStyle()) == 0) {
			$this->_left_color = 0;
		}
		if (self::_mapBorderStyle($this->_style->getBorders()->getDiagonal()->getBorderStyle()) == 0) {
			$this->_diag_color = 0;
		}

		$record         = 0x00E0;              // Record identifier
		$length         = 0x0014;              // Number of bytes to follow

		$ifnt           = $this->_fontIndex;   // Index to FONT record
		$ifmt           = $this->_numberFormatIndex;  // Index to FORMAT record

		$align          = $this->_mapHAlign($this->_style->getAlignment()->getHorizontal());       // Alignment
		$align         |= (int) $this->_style->getAlignment()->getWrapText()     << 3;
		$align         |= self::_mapVAlign($this->_style->getAlignment()->getVertical())  << 4;
		$align         |= $this->_text_justlast << 7;

		$used_attrib    = $atr_num              << 2;
		$used_attrib   |= $atr_fnt              << 3;
		$used_attrib   |= $atr_alc              << 4;
		$used_attrib   |= $atr_bdr              << 5;
		$used_attrib   |= $atr_pat              << 6;
		$used_attrib   |= $atr_prot             << 7;

		$icv            = $this->_fg_color;      // fg and bg pattern colors
		$icv           |= $this->_bg_color      << 7;

		$border1        = self::_mapBorderStyle($this->_style->getBorders()->getLeft()->getBorderStyle());          // Border line style and color
		$border1       |= self::_mapBorderStyle($this->_style->getBorders()->getRight()->getBorderStyle())         << 4;
		$border1       |= self::_mapBorderStyle($this->_style->getBorders()->getTop()->getBorderStyle())           << 8;
		$border1       |= self::_mapBorderStyle($this->_style->getBorders()->getBottom()->getBorderStyle())        << 12;
		$border1       |= $this->_left_color    << 16;
		$border1       |= $this->_right_color   << 23;

		$diagonalDirection = $this->_style->getBorders()->getDiagonalDirection();
		$diag_tl_to_rb = $diagonalDirection == PHPExcel_Style_Borders::DIAGONAL_BOTH
							|| $diagonalDirection == PHPExcel_Style_Borders::DIAGONAL_DOWN;
		$diag_tr_to_lb = $diagonalDirection == PHPExcel_Style_Borders::DIAGONAL_BOTH
							|| $diagonalDirection == PHPExcel_Style_Borders::DIAGONAL_UP;
		$border1       |= $diag_tl_to_rb        << 30;
		$border1       |= $diag_tr_to_lb        << 31;

		$border2        = $this->_top_color;    // Border color
		$border2       |= $this->_bottom_color   << 7;
		$border2       |= $this->_diag_color     << 14;
		$border2       |= self::_mapBorderStyle($this->_style->getBorders()->getDiagonal()->getBorderStyle())           << 21;
		$border2       |= self::_mapFillType($this->_style->getFill()->getFillType())        << 26;

		$header      = pack("vv",       $record, $length);

		//BIFF8 options: identation, shrinkToFit and  text direction
		$biff8_options  = $this->_style->getAlignment()->getIndent();
		$biff8_options |= (int) $this->_style->getAlignment()->getShrinkToFit() << 4;

		$data  = pack("vvvC", $ifnt, $ifmt, $style, $align);
		$data .= pack("CCC"
			, self::_mapTextRotation($this->_style->getAlignment()->getTextRotation())
			, $biff8_options
			, $used_attrib
			);
		$data .= pack("VVv", $border1, $border2, $icv);

		return($header . $data);
	}

	/**
	 * Is this a style XF ?
	 *
	 * @param boolean $value
	 */
	public function setIsStyleXf($value)
	{
		$this->_isStyleXf = $value;
	}

	/**
	 * Sets the cell's bottom border color
	 *
	 * @access public
	 * @param int $colorIndex Color index
	 */
	function setBottomColor($colorIndex)
	{
		$this->_bottom_color = $colorIndex;
	}

	/**
	 * Sets the cell's top border color
	 *
	 * @access public
	 * @param int $colorIndex Color index
	 */
	function setTopColor($colorIndex)
	{
		$this->_top_color = $colorIndex;
	}

	/**
	 * Sets the cell's left border color
	 *
	 * @access public
	 * @param int $colorIndex Color index
	 */
	function setLeftColor($colorIndex)
	{
		$this->_left_color = $colorIndex;
	}

	/**
	 * Sets the cell's right border color
	 *
	 * @access public
	 * @param int $colorIndex Color index
	 */
	function setRightColor($colorIndex)
	{
		$this->_right_color = $colorIndex;
	}

	/**
	 * Sets the cell's diagonal border color
	 *
	 * @access public
	 * @param int $colorIndex Color index
	 */
	function setDiagColor($colorIndex)
	{
		$this->_diag_color = $colorIndex;
	}


	/**
	 * Sets the cell's foreground color
	 *
	 * @access public
	 * @param int $colorIndex Color index
	 */
	function setFgColor($colorIndex)
	{
		$this->_fg_color = $colorIndex;
	}

	/**
	 * Sets the cell's background color
	 *
	 * @access public
	 * @param int $colorIndex Color index
	 */
	function setBgColor($colorIndex)
	{
		$this->_bg_color = $colorIndex;
	}

	/**
	 * Sets the index to the number format record
	 * It can be date, time, currency, etc...
	 *
	 * @access public
	 * @param integer $numberFormatIndex Index to format record
	 */
	function setNumberFormatIndex($numberFormatIndex)
	{
		$this->_numberFormatIndex = $numberFormatIndex;
	}

	/**
	 * Set the font index.
	 *
	 * @param int $value Font index, note that value 4 does not exist
	 */
	public function setFontIndex($value)
	{
		$this->_fontIndex = $value;
	}

	/**
	 * Map of BIFF2-BIFF8 codes for border styles
	 * @static	array of int
	 *
	 */
	private static $_mapBorderStyle = array	( PHPExcel_Style_Border::BORDER_NONE				=> 0x00,
											  PHPExcel_Style_Border::BORDER_THIN				=> 0x01,
											  PHPExcel_Style_Border::BORDER_MEDIUM				=> 0x02,
											  PHPExcel_Style_Border::BORDER_DASHED				=> 0x03,
											  PHPExcel_Style_Border::BORDER_DOTTED				=> 0x04,
											  PHPExcel_Style_Border::BORDER_THICK				=> 0x05,
											  PHPExcel_Style_Border::BORDER_DOUBLE				=> 0x06,
											  PHPExcel_Style_Border::BORDER_HAIR				=> 0x07,
											  PHPExcel_Style_Border::BORDER_MEDIUMDASHED		=> 0x08,
											  PHPExcel_Style_Border::BORDER_DASHDOT				=> 0x09,
											  PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT		=> 0x0A,
											  PHPExcel_Style_Border::BORDER_DASHDOTDOT			=> 0x0B,
											  PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT	=> 0x0C,
											  PHPExcel_Style_Border::BORDER_SLANTDASHDOT		=> 0x0D,
											);

	/**
	 * Map border style
	 *
	 * @param string $borderStyle
	 * @return int
	 */
	private static function _mapBorderStyle($borderStyle) {
		if (isset(self::$_mapBorderStyle[$borderStyle]))
			return self::$_mapBorderStyle[$borderStyle];
		return 0x00;
	}

	/**
	 * Map of BIFF2-BIFF8 codes for fill types
	 * @static	array of int
	 *
	 */
	private static $_mapFillType = array( PHPExcel_Style_Fill::FILL_NONE					=> 0x00,
										  PHPExcel_Style_Fill::FILL_SOLID					=> 0x01,
										  PHPExcel_Style_Fill::FILL_PATTERN_MEDIUMGRAY		=> 0x02,
										  PHPExcel_Style_Fill::FILL_PATTERN_DARKGRAY		=> 0x03,
										  PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRAY		=> 0x04,
										  PHPExcel_Style_Fill::FILL_PATTERN_DARKHORIZONTAL	=> 0x05,
										  PHPExcel_Style_Fill::FILL_PATTERN_DARKVERTICAL	=> 0x06,
										  PHPExcel_Style_Fill::FILL_PATTERN_DARKDOWN		=> 0x07,
										  PHPExcel_Style_Fill::FILL_PATTERN_DARKUP			=> 0x08,
										  PHPExcel_Style_Fill::FILL_PATTERN_DARKGRID		=> 0x09,
										  PHPExcel_Style_Fill::FILL_PATTERN_DARKTRELLIS		=> 0x0A,
										  PHPExcel_Style_Fill::FILL_PATTERN_LIGHTHORIZONTAL	=> 0x0B,
										  PHPExcel_Style_Fill::FILL_PATTERN_LIGHTVERTICAL	=> 0x0C,
										  PHPExcel_Style_Fill::FILL_PATTERN_LIGHTDOWN		=> 0x0D,
										  PHPExcel_Style_Fill::FILL_PATTERN_LIGHTUP			=> 0x0E,
										  PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRID		=> 0x0F,
										  PHPExcel_Style_Fill::FILL_PATTERN_LIGHTTRELLIS	=> 0x10,
										  PHPExcel_Style_Fill::FILL_PATTERN_GRAY125			=> 0x11,
										  PHPExcel_Style_Fill::FILL_PATTERN_GRAY0625		=> 0x12,
										  PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR			=> 0x00,	// does not exist in BIFF8
										  PHPExcel_Style_Fill::FILL_GRADIENT_PATH			=> 0x00,	// does not exist in BIFF8
										);
	/**
	 * Map fill type
	 *
	 * @param string $fillType
	 * @return int
	 */
	private static function _mapFillType($fillType) {
		if (isset(self::$_mapFillType[$fillType]))
			return self::$_mapFillType[$fillType];
		return 0x00;
	}

	/**
	 * Map of BIFF2-BIFF8 codes for horizontal alignment
	 * @static	array of int
	 *
	 */
	private static $_mapHAlign = array( PHPExcel_Style_Alignment::HORIZONTAL_GENERAL			=> 0,
										PHPExcel_Style_Alignment::HORIZONTAL_LEFT				=> 1,
										PHPExcel_Style_Alignment::HORIZONTAL_CENTER				=> 2,
										PHPExcel_Style_Alignment::HORIZONTAL_RIGHT				=> 3,
										PHPExcel_Style_Alignment::HORIZONTAL_FILL				=> 4,
										PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY			=> 5,
										PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS	=> 6,
									  );
	/**
	 * Map to BIFF2-BIFF8 codes for horizontal alignment
	 *
	 * @param string $hAlign
	 * @return int
	 */
	private function _mapHAlign($hAlign)
	{
		if (isset(self::$_mapHAlign[$hAlign]))
			return self::$_mapHAlign[$hAlign];
		return 0;
	}

	/**
	 * Map of BIFF2-BIFF8 codes for vertical alignment
	 * @static	array of int
	 *
	 */
	private static $_mapVAlign = array( PHPExcel_Style_Alignment::VERTICAL_TOP		=> 0,
										PHPExcel_Style_Alignment::VERTICAL_CENTER	=> 1,
										PHPExcel_Style_Alignment::VERTICAL_BOTTOM	=> 2,
										PHPExcel_Style_Alignment::VERTICAL_JUSTIFY	=> 3,
									  );
	/**
	 * Map to BIFF2-BIFF8 codes for vertical alignment
	 *
	 * @param string $vAlign
	 * @return int
	 */
	private static function _mapVAlign($vAlign) {
		if (isset(self::$_mapVAlign[$vAlign]))
			return self::$_mapVAlign[$vAlign];
		return 2;
	}

	/**
	 * Map to BIFF8 codes for text rotation angle
	 *
	 * @param int $textRotation
	 * @return int
	 */
	private static function _mapTextRotation($textRotation) {
		if ($textRotation >= 0) {
			return $textRotation;
		}
		if ($textRotation == -165) {
			return 255;
		}
		if ($textRotation < 0) {
			return 90 - $textRotation;
		}
	}

	/**
	 * Map locked
	 *
	 * @param string
	 * @return int
	 */
	private static function _mapLocked($locked) {
		switch ($locked) {
			case PHPExcel_Style_Protection::PROTECTION_INHERIT:		return 1;
			case PHPExcel_Style_Protection::PROTECTION_PROTECTED:	return 1;
			case PHPExcel_Style_Protection::PROTECTION_UNPROTECTED:	return 0;
			default:												return 1;
		}
	}

	/**
	 * Map hidden
	 *
	 * @param string
	 * @return int
	 */
	private static function _mapHidden($hidden) {
		switch ($hidden) {
			case PHPExcel_Style_Protection::PROTECTION_INHERIT:		return 0;
			case PHPExcel_Style_Protection::PROTECTION_PROTECTED:	return 1;
			case PHPExcel_Style_Protection::PROTECTION_UNPROTECTED:	return 0;
			default:												return 0;
		}
	}

}
